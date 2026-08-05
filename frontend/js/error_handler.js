// error_handler.js - Handles API errors and redirects to appropriate fallback pages

// ============================================================
// DYNAMIC API URL LOADER
// ============================================================
let API_BASE_URL = 'http://127.0.0.1:5000'; // Default fallback

async function loadConfig() {
    try {
        const response = await fetch('/config.json');
        if (response.ok) {
            const config = await response.json();
            if (config.apiUrl) {
                API_BASE_URL = config.apiUrl;
                console.log('[APP] API URL loaded from config:', API_BASE_URL);
            } else {
                console.warn('[APP] No apiUrl in config, using default:', API_BASE_URL);
            }
        } else {
            console.warn('[APP] config.json not found, using default:', API_BASE_URL);
        }
    } catch (error) {
        console.warn('[APP] Failed to load config, using default:', API_BASE_URL);
    }
}

// Load config immediately
loadConfig();

// ============================================================
// ERROR HANDLING FUNCTIONS
// ============================================================

function handleAPIError(response) {
    if (!response.ok) {
        response.json().then(data => {
            let errorPage = '../error_handling/error.php';
            let params = '';
            
            // Add error details to URL
            if (data.code) {
                params += '?code=' + encodeURIComponent(data.code);
            }
            if (data.message) {
                params += (params ? '&' : '?') + 'message=' + encodeURIComponent(data.message);
            }
            
            switch(data.error_type) {
                case 'QUOTA_EXCEEDED':
                case 'RATE_LIMIT':
                    errorPage = '../error_handling/quota_limit.php';
                    break;
                case 'NETWORK_ERROR':
                case 'TIMEOUT':
                    errorPage = '../error_handling/network_error.php';
                    break;
                case 'SERVER_ERROR':
                    errorPage = '../error_handling/server_error.php';
                    break;
                case 'FILE_TOO_LARGE':
                    errorPage = '../error_handling/file_too_large.php';
                    break;
                case 'AUTH_ERROR':
                    errorPage = '../login.php?error=auth';
                    break;
                case 'NOT_FOUND':
                    errorPage = '../error_handling/error.php';
                    break;
                case 'VALIDATION_ERROR':
                    errorPage = '../error_handling/error.php';
                    break;
                default:
                    errorPage = '../error_handling/error.php';
            }
            
            // Redirect to the appropriate error page
            window.location.href = errorPage + params;
        }).catch(() => {
            // If the response is not JSON, redirect to generic error
            window.location.href = '../error_handling/error.php?code=' + response.status;
        });
    }
    return response;
}

// Intercept all fetch requests
const originalFetch = window.fetch;
window.fetch = function(url, options) {
    // If the URL starts with a relative path or is a status endpoint,
    // prepend the API_BASE_URL if it's not already absolute
    let finalUrl = url;
    if (typeof url === 'string' && 
        !url.startsWith('http://') && 
        !url.startsWith('https://') &&
        !url.startsWith('config.json')) {
        // For status and API endpoints, use the dynamic base URL
        if (url.startsWith('/status/') || url.startsWith('/upload') || url.startsWith('/api/')) {
            finalUrl = API_BASE_URL + url;
        } else if (url.startsWith('status/') || url.startsWith('api/')) {
            finalUrl = API_BASE_URL + '/' + url;
        }
    }
    
    return originalFetch(finalUrl, options)
        .then(response => {
            if (!response.ok) {
                handleAPIError(response);
                return Promise.reject(response);
            }
            return response;
        })
        .catch(error => {
            if (error.message === 'Failed to fetch' || 
                error.message === 'NetworkError' ||
                error.name === 'AbortError' ||
                error.name === 'TimeoutError') {
                window.location.href = '../error_handling/network_error.php';
            } else {
                window.location.href = '../error_handling/error.php?message=' + encodeURIComponent(error.message);
            }
            return Promise.reject(error);
        });
};

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 10 * 1024 * 1024; // 10 MB
                if (file.size > maxSize) {
                    e.preventDefault();
                    const params = '?file=' + encodeURIComponent(file.name) + '&max_size=10%20Mo&code=413';
                    window.location.href = '../error_handling/file_too_large.php' + params;
                }
            }
        });
    });
    
    // Handle status polling errors
    const statusElements = document.querySelectorAll('[data-poll-status]');
    statusElements.forEach(el => {
        const jobId = el.dataset.jobId;
        if (jobId) {
            pollStatus(jobId);
        }
    });
});

function pollStatus(jobId) {
    const statusUrl = API_BASE_URL + '/status/' + jobId;
    const statusElement = document.getElementById('status-' + jobId);
    const progressElement = document.getElementById('progress-' + jobId);
    
    fetch(statusUrl)
        .then(response => {
            if (!response.ok) {
                handleAPIError(response);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data) {
                if (statusElement) {
                    statusElement.textContent = data.status;
                }
                if (progressElement) {
                    progressElement.style.width = data.progress + '%';
                    progressElement.textContent = data.progress + '%';
                }
                
                if (data.status === 'processing' || data.status === 'extracting') {
                    // Poll again after 2 seconds
                    setTimeout(() => pollStatus(jobId), 2000);
                } else if (data.status === 'failed') {
                    // Handle failure
                    handleJobFailure(data);
                } else if (data.status === 'completed') {
                    // Handle completion
                    handleJobCompletion(data);
                }
            }
        })
        .catch(error => {
            if (error.name === 'AbortError' || error.message === 'Failed to fetch') {
                // Network error - redirect to network error page
                window.location.href = '../error_handling/network_error.php';
            }
        });
}

function handleJobFailure(data) {
    let errorPage = '../error_handling/error.php';
    let params = '?code=' + encodeURIComponent(data.error_type || '500') + 
                 '&message=' + encodeURIComponent(data.error || 'Erreur inconnue');
    
    switch(data.error_type) {
        case 'QUOTA_EXCEEDED':
            errorPage = '../error_handling/quota_limit.php';
            break;
        case 'NETWORK_ERROR':
            errorPage = '../error_handling/network_error.php';
            break;
        case 'SERVER_ERROR':
            errorPage = '../error_handling/server_error.php';
            break;
        case 'FILE_TOO_LARGE':
            errorPage = '../error_handling/file_too_large.php';
            break;
        default:
            errorPage = '../error_handling/error.php';
    }
    
    window.location.href = errorPage + params;
}

function handleJobCompletion(data) {
    // Redirect to results page or show success
    if (data.invoices && data.invoices.length > 0) {
        window.location.href = '../results.php?job_id=' + data.job_id;
    } else {
        window.location.href = '../error_handling/error.php?message=Aucune%20facture%20valide%20trouvee';
    }
}

// Export for use in other scripts
window.handleAPIError = handleAPIError;
window.pollStatus = pollStatus;