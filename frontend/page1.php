<?php
    session_start();
    
    // ============================================================
    // FIXED PORTS - NO CONFIG FILE NEEDED
    // ============================================================
    $API_BASE_URL = 'http://127.0.0.1:5000';
    $PHP_PORT = 8000;
    $API_PORT = 5000;
    
    include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ... (all your existing styles remain the same) ... */
        :root {
            --main-color: #780000;
            --seondary-color: #003049;
            --accent-color: #4ca380;
            --white: white;
            --light-bg: #f8f9fa;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
            --warning-color: #f59e0b;
            --danger-color: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 70px;
            padding: 40px;
            flex: 1;
            min-height: 100vh;
            position: relative;
        }

        .page-header {
            margin-bottom: 40px;
            animation: fadeInDown 0.6s ease;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-header .title {
            font-size: 36px;
            font-weight: 700;
            color: var(--seondary-color);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header .title i {
            color: var(--accent-color);
            font-size: 32px;
        }

        .upload-counter {
            background: white;
            padding: 12px 24px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 16px;
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
            min-width: 160px;
            justify-content: center;
        }

        .upload-counter .counter-icon {
            font-size: 20px;
            color: var(--accent-color);
        }

        .upload-counter .counter-numbers {
            font-weight: 700;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .upload-counter .counter-current {
            color: var(--seondary-color);
        }

        .upload-counter .counter-separator {
            color: #a0aec0;
        }

        .upload-counter .counter-max {
            color: #a0aec0;
        }

        .upload-counter .counter-progress {
            width: 80px;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }

        .upload-counter .counter-progress-bar {
            height: 100%;
            background: var(--accent-color);
            border-radius: 3px;
            transition: all 0.5s ease;
            width: 0%;
        }

        .upload-counter .counter-progress-bar.warning {
            background: var(--warning-color);
        }

        .upload-counter .counter-progress-bar.danger {
            background: var(--danger-color);
            animation: pulse 1s ease-in-out infinite;
        }

        .upload-counter.limited {
            border-color: var(--danger-color);
            background: #fff5f5;
        }

        .upload-counter .counter-limit-reached {
            color: var(--danger-color);
            font-weight: 600;
            font-size: 13px;
            display: none;
        }

        .upload-counter.limited .counter-limit-reached {
            display: inline;
        }

        .upload-counter.limited .counter-numbers {
            display: none;
        }

        .upload-counter.limited .counter-progress {
            display: none;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .upload-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            max-width: 800px;
            margin: 0 auto;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease;
        }

        .upload-container.disabled {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        .upload-container.disabled::after {
            content: 'Service indisponible - Limite atteinte';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 20px 40px;
            border-radius: 12px;
            font-size: 20px;
            font-weight: 700;
            z-index: 10;
            text-align: center;
            box-shadow: 0 10px 40px rgba(220, 53, 69, 0.3);
        }

        .upload-container:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        }

        .mode-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--light-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
        }

        .mode-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 10px;
            background: white;
            border-radius: 12px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
        }

        .mode-label:hover {
            border-color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 163, 128, 0.15);
        }

        .mode-label input[type="radio"] {
            display: none;
        }

        .mode-label:has(input:checked) {
            border-color: var(--main-color);
            background: #fff5f5;
            box-shadow: 0 4px 15px rgba(120, 0, 0, 0.1);
        }

        .mode-label .mode-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .mode-label .mode-content i {
            font-size: 28px;
            color: var(--seondary-color);
            transition: color 0.3s ease;
        }

        .mode-label:has(input:checked) .mode-content i {
            color: var(--main-color);
        }

        .mode-label .mode-content .mode-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-color);
        }

        .mode-label .mode-content .mode-desc {
            font-size: 11px;
            color: #718096;
            line-height: 1.3;
        }

        .mode-label .mode-content .mode-badge {
            font-size: 10px;
            background: var(--accent-color);
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            margin-top: 4px;
            font-weight: 600;
        }

        .drop-zone {
            border: 3px dashed var(--border-color);
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: var(--light-bg);
            position: relative;
            cursor: pointer;
        }

        .drop-zone:hover {
            border-color: var(--accent-color);
            background: #f0fdf4;
            transform: scale(1.01);
        }

        .drop-zone.dragover {
            border-color: var(--main-color);
            background: #fef2f2;
            transform: scale(1.02);
        }

        .drop-zone i {
            font-size: 64px;
            color: var(--accent-color);
            margin-bottom: 20px;
            display: block;
            transition: all 0.3s ease;
        }

        .drop-zone:hover i {
            transform: translateY(-5px) scale(1.05);
        }

        .drop-zone h3 {
            font-size: 22px;
            color: var(--seondary-color);
            margin-bottom: 10px;
        }

        .drop-zone p {
            color: #718096;
            font-size: 15px;
            margin-bottom: 20px;
        }

        .drop-zone .or-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            color: #a0aec0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .drop-zone .or-divider::before,
        .drop-zone .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .btn-select {
            background: var(--main-color);
            color: var(--white);
            border: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
            line-height: 1;
            vertical-align: middle;
        }

        .btn-select:hover {
            background: #660000;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(120, 0, 0, 0.3);
        }

        .btn-select:active {
            transform: scale(0.95);
        }

        .btn-select i {
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            height: 18px;
            width: 18px;
            flex-shrink: 0;
        }

        .file-info {
            margin-top: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.4s ease;
            border: 1px solid var(--border-color);
        }

        .file-info.show {
            display: flex;
        }

        .file-info .file-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-info .file-details i {
            font-size: 28px;
            color: var(--accent-color);
        }

        .file-info .file-details .file-name {
            font-weight: 600;
            color: var(--seondary-color);
        }

        .file-info .file-details .file-size {
            font-size: 13px;
            color: #718096;
        }

        .btn-remove {
            background: none;
            border: none;
            color: #fc8181;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .btn-remove:hover {
            background: #fff5f5;
            transform: scale(1.1);
        }

        .btn-next-wrapper {
            margin-top: 25px;
            text-align: center;
            display: flex;
            justify-content: center;
        }
        .btn-next-wrapper button{
            display: none;
        }
        .btn-next {
            background: var(--seondary-color);
            color: var(--white);
            border: none;
            padding: 14px 50px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            opacity: 0.5;
            pointer-events: none;
        }

        .btn-next.active {
            opacity: 1;
            pointer-events: auto;
        }

        .btn-next.active:hover {
            background: #002538;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 48, 73, 0.3);
        }

        .btn-next:active {
            transform: scale(0.95);
        }

        .btn-next i {
            font-size: 18px;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 220px;
                padding: 25px;
            }

            .page-header .title {
                font-size: 28px;
            }

            .upload-counter {
                padding: 10px 18px;
                font-size: 14px;
                min-width: 140px;
            }

            .upload-counter .counter-progress {
                width: 60px;
            }

            .upload-container {
                padding: 30px 20px;
            }

            .mode-selector {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 15px;
            }

            .mode-label {
                flex-direction: row;
                padding: 12px 15px;
                gap: 15px;
                align-items: center;
            }

            .mode-label .mode-content {
                flex-direction: row;
                flex: 1;
                justify-content: space-between;
                align-items: center;
            }

            .mode-label .mode-content i {
                font-size: 20px;
            }

            .drop-zone {
                padding: 40px 20px;
            }

            .drop-zone i {
                font-size: 48px;
            }

            .drop-zone h3 {
                font-size: 18px;
            }

            .btn-select, .btn-next {
                padding: 12px 30px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .page-header .title {
                font-size: 24px;
                flex-wrap: wrap;
            }

            .upload-counter {
                width: 100%;
                justify-content: center;
            }

            .upload-container {
                padding: 20px 15px;
            }

            .mode-selector {
                grid-template-columns: 1fr;
                padding: 10px;
            }

            .drop-zone {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <div class="title">
                <i class="fas fa-file-upload"></i>
                Remplissage automatique
            </div>
            
            <!-- Upload Counter -->
            <div class="upload-counter" id="uploadCounter">
                <i class="fas fa-file-invoice counter-icon"></i>
                <div class="counter-numbers">
                    <span class="counter-current" id="counterCurrent">0</span>
                    <span class="counter-separator">/</span>
                    <span class="counter-max">200</span>
                </div>
                <div class="counter-progress">
                    <div class="counter-progress-bar" id="counterProgressBar"></div>
                </div>
                <span class="counter-limit-reached">
                    <i class="fas fa-exclamation-circle"></i> Limite atteinte
                </span>
            </div>
        </div>

        <!-- Upload Container -->
        <form id="uploadForm">
            <div class="upload-container" id="uploadContainer">
                <!-- Mode Selection -->
                <div class="mode-selector">
                    <label class="mode-label">
                        <input type="radio" name="mode" value="first_page" >
                        <div class="mode-content">
                            <i class="fas fa-file"></i>
                            <div>
                                <div class="mode-title">Premiere page</div>
                                <div class="mode-desc">Factures d'une page</div>
                            </div>
                            <span class="mode-badge">Rapide</span>
                        </div>
                    </label>

                    <label class="mode-label">
                        <input type="radio" name="mode" value="full_document">
                        <div class="mode-content">
                            <i class="fas fa-file-pdf"></i>
                            <div>
                                <div class="mode-title">Document complet</div>
                                <div class="mode-desc">Factures multi-pages</div>
                            </div>
                            <span class="mode-badge">Complet</span>
                        </div>
                    </label>

                    <label class="mode-label">
                        <input type="radio" name="mode" value="each_page" checked>
                        <div class="mode-content">
                            <i class="fa-solid fa-copy"></i>
                            <div>
                                <div class="mode-title">Chaque page</div>
                                <div class="mode-desc">Scan de plusieurs factures</div>
                            </div>
                            <span class="mode-badge">Multiple</span>
                        </div>
                    </label>
                </div>

                <div class="drop-zone" id="dropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>De posez votre document ici</h3>
                    <p>Glissez-de posez votre fichier ou cliquez sur le bouton ci-dessous</p>

                    <div class="or-divider">ou</div>

                    <div class="file-input-wrapper">
                        <button type="button" class="btn-select" onclick="document.getElementById('fileInput').click()">
                            Selectionner un fichier
                        </button>
                        <input type="file" id="fileInput" name="invoices[]" multiple accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.jpg,.png,.jpeg">
                    </div>

                    <!-- File Info -->
                    <div class="file-info" id="fileInfo">
                        <div class="file-details">
                            <i class="fas fa-file-pdf"></i>
                            <div>
                                <div class="file-name" id="fileName">document.pdf</div>
                                <div class="file-size" id="fileSize">2.4 MB</div>
                            </div>
                        </div>
                        <button type="button" class="btn-remove" onclick="removeFile()">
                            <i class="fas fa-times" style="font-size: 20px;"></i>
                        </button>
                    </div>

                    <!-- Next Button -->
                    <div class="btn-next-wrapper">
                        <button type="submit" class="btn-next" id="nextBtn" disabled>
                            Suivant
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // ============================================================
        // FIXED API URL - NO CONFIG FILE NEEDED
        // ============================================================
        const API_BASE_URL = 'http://127.0.0.1:5000';
        const PHP_PORT = 8000;
        const API_PORT = 5000;
        
        console.log('[APP] Using fixed API URL:', API_BASE_URL);

        // ============================================================
        // ERROR HANDLING REDIRECT HELPER
        // ============================================================
        function redirectToErrorPage(type, params = {}) {
            const pages = {
                network: "error_handling/network_error.php",
                quota: "error_handling/quota_limit.php",
                storage: "error_handling/storage_limit.php",
                file: "error_handling/file_too_large.php",
                server: "error_handling/server_error.php",
                error: "error_handling/error.php"
            };

            const url = new URL(pages[type], window.location.origin + window.location.pathname);

            Object.entries(params).forEach(([key, value]) => {
                url.searchParams.set(key, value);
            });

            window.location.href = url.toString();
        }

        // ============================================================
        // CHECK IF RUNNING IN ELECTRON
        // ============================================================
        function isElectron() {
            return typeof window !== 'undefined' && window.electronAPI !== undefined;
        }

        // ============================================================
        // DATABASE FUNCTIONS - SQLITE VIA ELECTRON IPC
        // ============================================================
        const MAX_UPLOADS = 200;
        const MAX_FILE_SIZE = 150 * 1024 * 1024; // 150 MB

        // Get total invoices from today only using SQLite
        async function getTotalInvoicesToday() {
            try {
                if (isElectron()) {
                    const sessions = await window.electronAPI.db.getAllSessions();
                    let total = 0;
                    const today = new Date().toISOString().split('T')[0];
                    
                    for (const session of sessions) {
                        if (session.data) {
                            try {
                                const sessionData = typeof session.data === 'string' ? JSON.parse(session.data) : session.data;
                                if (sessionData.invoices && Array.isArray(sessionData.invoices)) {
                                    const todayInvoices = sessionData.invoices.filter(invoice => {
                                        if (!invoice.created_at) return false;
                                        const invoiceDate = invoice.created_at.split('T')[0];
                                        return invoiceDate === today;
                                    });
                                    total += todayInvoices.length;
                                }
                            } catch (e) {
                                // Skip invalid session data
                            }
                        }
                    }
                    return total;
                } else {
                    // Fallback to localStorage
                    const data = localStorage.getItem("invoiceSessions");
                    if (!data) return 0;
                    const sessions = JSON.parse(data);
                    let total = 0;
                    const today = new Date().toISOString().split('T')[0];
                    
                    sessions.forEach(session => {
                        if (session.invoices && Array.isArray(session.invoices)) {
                            const todayInvoices = session.invoices.filter(invoice => {
                                if (!invoice.created_at) return false;
                                const invoiceDate = invoice.created_at.split('T')[0];
                                return invoiceDate === today;
                            });
                            total += todayInvoices.length;
                        }
                    });
                    return total;
                }
            } catch (error) {
                console.error('Error counting today\'s invoices:', error);
                return 0;
            }
        }

        // Check if limit is reached for today
        async function isLimitReached() {
            const count = await getTotalInvoicesToday();
            return count >= MAX_UPLOADS;
        }

        // Update counter display
        async function updateCounter() {
            const current = await getTotalInvoicesToday();
            const counterElement = document.getElementById('uploadCounter');
            const currentSpan = document.getElementById('counterCurrent');
            const progressBar = document.getElementById('counterProgressBar');
            const container = document.getElementById('uploadContainer');
            
            if (!currentSpan || !progressBar || !container) return;
            
            currentSpan.textContent = current;
            
            const percentage = Math.min((current / MAX_UPLOADS) * 100, 100);
            progressBar.style.width = percentage + '%';
            
            progressBar.classList.remove('warning', 'danger');
            if (percentage >= 90) {
                progressBar.classList.add('danger');
            } else if (percentage >= 70) {
                progressBar.classList.add('warning');
            }
            
            if (current >= MAX_UPLOADS) {
                counterElement.classList.add('limited');
                container.classList.add('disabled');
                
                document.getElementById('fileInput').disabled = true;
                document.getElementById('nextBtn').disabled = true;
                document.getElementById('nextBtn').classList.remove('active');
                document.getElementById('nextBtn').style.display = 'none';
                const selectBtn = document.querySelector('.btn-select');
                if (selectBtn) {
                    selectBtn.style.opacity = '0.5';
                    selectBtn.style.cursor = 'not-allowed';
                }
            } else {
                counterElement.classList.remove('limited');
                container.classList.remove('disabled');
                document.getElementById('fileInput').disabled = false;
                const selectBtn = document.querySelector('.btn-select');
                if (selectBtn) {
                    selectBtn.style.opacity = '1';
                    selectBtn.style.cursor = 'pointer';
                }
            }
        }

        // Reset counter at midnight
        async function checkAndResetCounter() {
            const today = new Date().toISOString().split('T')[0];
            
            if (isElectron()) {
                const lastReset = await window.electronAPI.db.getSetting('resetTime', '');
                if (lastReset !== today) {
                    await window.electronAPI.db.setSetting('resetTime', today);
                    await updateCounter();
                    showToast('Compteur reinitialise pour aujourd\'hui');
                }
            } else {
                const lastReset = localStorage.getItem('resetTime');
                if (lastReset !== today) {
                    localStorage.setItem('resetTime', today);
                    await updateCounter();
                    showToast('Compteur reinitialise pour aujourd\'hui');
                }
            }
        }

        // Toast notification
        function showToast(message, duration = 3000) {
            let toast = document.getElementById('toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast';
                toast.style.cssText = `
                    position: fixed;
                    bottom: 30px;
                    right: 30px;
                    background: #2d3748;
                    color: white;
                    padding: 15px 25px;
                    border-radius: 12px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    z-index: 9999;
                    transform: translateY(100px);
                    opacity: 0;
                    transition: all 0.4s ease;
                    font-family: 'Segoe UI', sans-serif;
                    font-size: 14px;
                    max-width: 400px;
                `;
                document.body.appendChild(toast);
            }
            
            toast.textContent = message;
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
            
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => {
                toast.style.transform = 'translateY(100px)';
                toast.style.opacity = '0';
            }, duration);
        }

        // Initialize counter
        async function initCounter() {
            await checkAndResetCounter();
            await updateCounter();
            
            setInterval(async () => {
                await checkAndResetCounter();
                await updateCounter();
            }, 30000);
        }

        // ============================================================
        // FILE UPLOAD HANDLING WITH ERROR REDIRECTS
        // ============================================================
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const nextBtn = document.getElementById('nextBtn');
        const uploadForm = document.getElementById('uploadForm');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('dragover');
            });
        });

        dropZone.addEventListener('drop', handleDrop);
        fileInput.addEventListener('change', handleFileSelect);

        async function handleDrop(e) {
            if (await isLimitReached()) {
                showToast('Limite de 200 factures atteinte pour aujourd\'hui. Service indisponible jusqu\'a minuit.', 4000);
                return;
            }
            const dt = e.dataTransfer;
            const files = dt.files;
            
            for (const file of files) {
                if (file.size > MAX_FILE_SIZE) {
                    redirectToErrorPage("file", {
                        file: file.name,
                        max_size: "150 MB"
                    });
                    return;
                }
            }
            
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        }

        async function handleFileSelect(e) {
            if (await isLimitReached()) {
                showToast('Limite de 200 factures atteinte pour aujourd\'hui. Service indisponible jusqu\'a minuit.', 4000);
                fileInput.value = '';
                return;
            }

            const files = e ? e.target.files : fileInput.files;
            
            for (const file of files) {
                if (file.size > MAX_FILE_SIZE) {
                    redirectToErrorPage("file", {
                        file: file.name,
                        max_size: "150 MB"
                    });
                    fileInput.value = '';
                    return;
                }
            }
            
            if (files.length > 0) {
                const file = files[0];
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                
                if (files.length > 1) {
                    fileName.textContent = files.length + ' fichiers selectionnes';
                    fileSize.textContent = 'Total: ' + (Array.from(files).reduce((sum, f) => sum + f.size, 0) / 1024 / 1024).toFixed(2) + ' MB';
                }
                
                fileInfo.classList.add('show');

                const fileIcon = fileInfo.querySelector('.file-details i');
                const extension = file.name.split('.').pop().toLowerCase();
                const iconMap = {
                    'pdf': 'fa-file-pdf',
                    'doc': 'fa-file-word',
                    'docx': 'fa-file-word',
                    'txt': 'fa-file-alt',
                    'xls': 'fa-file-excel',
                    'xlsx': 'fa-file-excel',
                    'jpg': 'fa-file-image',
                    'jpeg': 'fa-file-image',
                    'png': 'fa-file-image'
                };
                fileIcon.className = 'fas ' + (iconMap[extension] || 'fa-file');

                nextBtn.disabled = false;
                nextBtn.classList.add('active');
                nextBtn.style.display = "inline-flex";
                
                document.querySelector('.btn-select').innerHTML = (files.length > 1 ? files.length + ' fichiers selectionnes' : 'Fichier selectionne');
            }
        }

        function removeFile() {
            fileInfo.classList.remove('show');
            fileInput.value = '';
            nextBtn.disabled = true;
            nextBtn.classList.remove('active');
            nextBtn.style.display = "none";
            document.querySelector('.btn-select').innerHTML = '<i class="fas fa-folder-open"></i> Selectionner un fichier';
        }

        // Save invoices to SQLite
        async function saveInvoicesToDatabase(invoices) {
            try {
                if (isElectron()) {
                    // Save to SQLite via Electron IPC
                    const sessionId = 'SESSION_' + new Date().toISOString().replace(/[-:.TZ]/g, "").slice(0, 14);
                    const sessionData = {
                        sessionID: sessionId,
                        created_at: new Date().toISOString(),
                        invoices: invoices
                    };
                    
                    await window.electronAPI.db.saveSession(sessionId, sessionData);
                    await window.electronAPI.db.saveCurrentInvoices(invoices);
                    localStorage.setItem("currentInvoice", "0");
                    
                    console.log('Invoices saved to SQLite:', invoices.length);
                    return true;
                } else {
                    // Fallback to localStorage
                    localStorage.setItem("invoices", JSON.stringify(invoices));
                    localStorage.setItem("currentInvoice", "0");
                    
                    let history = JSON.parse(localStorage.getItem("invoiceHistory")) || [];
                    history.push(...invoices);
                    localStorage.setItem("invoiceHistory", JSON.stringify(history));
                    
                    return true;
                }
            } catch (error) {
                console.error('Error saving invoices:', error);
                return false;
            }
        }

        uploadForm.addEventListener("submit", async function(e) {
            console.log("SUBMIT EVENT");
            e.preventDefault();

            if (await isLimitReached()) {
                showToast('Limite de 200 factures atteinte pour aujourd\'hui. Service indisponible jusqu\'a minuit.', 4000);
                return;
            }

            if (fileInput.files.length === 0) {
                redirectToErrorPage("error", {
                    code: "NO_FILE",
                    message: "Aucun fichier selectionne"
                });
                return;
            }

            for (const file of fileInput.files) {
                if (file.size > MAX_FILE_SIZE) {
                    redirectToErrorPage("file", {
                        file: file.name,
                        max_size: "150 MB"
                    });
                    return;
                }
            }

            const selectedMode = document.querySelector('input[name="mode"]:checked').value;
            console.log('Selected mode:', selectedMode);

            const formData = new FormData();
            
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append("invoices[]", fileInput.files[i]);
            }
            
            formData.append("mode", selectedMode);

            try {
                nextBtn.disabled = true;
                nextBtn.innerHTML = ' Traitement...';

                // Use fixed API URL
                const response = await fetch(API_BASE_URL + "/upload", {
                    method: "POST",
                    body: formData
                });

                if (response.status === 429) {
                    redirectToErrorPage("quota", {
                        code: 429,
                        message: "Gemini API quota exceeded"
                    });
                    return;
                }

                if (response.status >= 500) {
                    redirectToErrorPage("server");
                    return;
                }

                const data = await response.json();

                console.log('Upload response:', data);

                if (data.error === "quota_exceeded") {
                    redirectToErrorPage("quota", {
                        code: 429,
                        message: data.message || "Gemini API quota exceeded"
                    });
                    return;
                }

                if (data.error) {
                    redirectToErrorPage("error", {
                        code: response.status || 500,
                        message: data.message || data.error
                    });
                    return;
                }

                if (data.success && data.job_id) {
                    console.log("Redirecting to:", "process_animation.php?id=" + data.job_id);
                    window.location.href = "process_animation.php?id=" + encodeURIComponent(data.job_id);
                }
                else if (data.success && data.invoices) {
                    const invoicesWithDate = (data.invoices || []).map(invoice => ({
                        ...invoice,
                        created_at: new Date().toISOString()
                    }));
                    
                    try {
                        await saveInvoicesToDatabase(invoicesWithDate);
                    } catch (storageError) {
                        redirectToErrorPage("storage", {
                            code: "DB001",
                            message: "Espace de stockage insuffisant"
                        });
                        return;
                    }
                    
                    await updateCounter();
                    window.location.href = "process_animation.php";
                } else {
                    redirectToErrorPage("error", {
                        code: response.status || 500,
                        message: data.message || "Erreur inconnue lors de l'extraction"
                    });
                }

            } catch (error) {
                console.error("Error:", error);
                
                if (error.name === "TypeError" || error.message.includes("fetch")) {
                    redirectToErrorPage("network", {
                        message: error.message || "Impossible de contacter le serveur"
                    });
                } else {
                    redirectToErrorPage("error", {
                        code: 500,
                        message: error.message || "Erreur inattendue"
                    });
                }
                
                nextBtn.disabled = false;
                nextBtn.innerHTML = ' Suivant';
            }
        });

        // ============================================================
        // INITIALIZATION
        // ============================================================
        (async function init() {
            await initCounter();
            console.log('[APP] API URL:', API_BASE_URL);
            console.log('[APP] Storage: SQLite via Electron IPC');
        })();
    </script>
</body>
</html>