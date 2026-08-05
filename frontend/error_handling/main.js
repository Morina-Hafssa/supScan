const { app, BrowserWindow, ipcMain, dialog, Menu } = require("electron");
const { spawn } = require("child_process");
const path = require("path");
const fs = require("fs");
const http = require("http");

// ============================================================
// IMPORT DATABASE MODULE
// ============================================================
const db = require('../../electron/database');

let phpProcess;
let apiProcess;
let mainWindow;

// ============================================================
// FIXED PORTS - NO CONFIG FILE NEEDED
// ============================================================
const PORTS = {
    PHP: 8000,
    API: 5000
};

// ============================================================
// DETECT DEVELOPMENT VS PRODUCTION
// ============================================================
const isDev = !app.isPackaged;
const ROOT = isDev
    ? path.join(__dirname, "..")
    : process.resourcesPath;

console.log("[APP] Mode: " + (isDev ? 'Development' : 'Production'));
console.log("[APP] Root directory: " + ROOT);

// ============================================================
// PATH CONFIGURATION
// ============================================================
const PATHS = {
    phpExe: path.join(ROOT, "runtime", "php", "php.exe"),
    frontend: path.join(ROOT, "frontend"),
    apiExe: path.join(ROOT, "ai", "dist", "SupScanAPI", "SupScanAPI.exe"),
    icon: path.join(ROOT, "assets", "SupScan.ico"),
    loading: path.join(ROOT, "assets", "loading.html"),
    about: path.join(ROOT, "assets", "about.html"),
    error: path.join(ROOT, "assets", "error.html")
};

console.log("[APP] PHP executable: " + PATHS.phpExe);
console.log("[APP] Frontend folder: " + PATHS.frontend);
console.log("[APP] API executable: " + PATHS.apiExe);

// ============================================================
// IPC HANDLERS FOR DATABASE
// ============================================================

// Get all sessions
ipcMain.handle('db:getAllSessions', async () => {
    try {
        return await db.getAllSessions();
    } catch (error) {
        console.error('[IPC] Error getting sessions:', error);
        return [];
    }
});

// Get a single session
ipcMain.handle('db:getSession', async (event, sessionId) => {
    try {
        return await db.getSession(sessionId);
    } catch (error) {
        console.error('[IPC] Error getting session:', error);
        return null;
    }
});

// Save a session
ipcMain.handle('db:saveSession', async (event, sessionId, data) => {
    try {
        return await db.saveSession(sessionId, data);
    } catch (error) {
        console.error('[IPC] Error saving session:', error);
        return null;
    }
});

// Delete a session
ipcMain.handle('db:deleteSession', async (event, sessionId) => {
    try {
        return await db.deleteSession(sessionId);
    } catch (error) {
        console.error('[IPC] Error deleting session:', error);
        return 0;
    }
});

// Get invoices for a session
ipcMain.handle('db:getInvoicesForSession', async (event, sessionId) => {
    try {
        return await db.getInvoicesForSession(sessionId);
    } catch (error) {
        console.error('[IPC] Error getting invoices:', error);
        return [];
    }
});

// Save an invoice
ipcMain.handle('db:saveInvoice', async (event, sessionId, invoice) => {
    try {
        return await db.saveInvoice(sessionId, invoice);
    } catch (error) {
        console.error('[IPC] Error saving invoice:', error);
        return null;
    }
});

// Delete an invoice
ipcMain.handle('db:deleteInvoice', async (event, invoiceId) => {
    try {
        return await db.deleteInvoice(invoiceId);
    } catch (error) {
        console.error('[IPC] Error deleting invoice:', error);
        return 0;
    }
});

// Get current invoices
ipcMain.handle('db:getCurrentInvoices', async () => {
    try {
        return await db.getCurrentInvoices();
    } catch (error) {
        console.error('[IPC] Error getting current invoices:', error);
        return [];
    }
});

// Save current invoices
ipcMain.handle('db:saveCurrentInvoices', async (event, invoices) => {
    try {
        return await db.saveCurrentInvoices(invoices);
    } catch (error) {
        console.error('[IPC] Error saving current invoices:', error);
        return false;
    }
});

// Clear current invoices
ipcMain.handle('db:clearCurrentInvoices', async () => {
    try {
        return await db.clearCurrentInvoices();
    } catch (error) {
        console.error('[IPC] Error clearing current invoices:', error);
        return 0;
    }
});

// Get setting
ipcMain.handle('db:getSetting', async (event, key, defaultValue) => {
    try {
        return await db.getSetting(key, defaultValue);
    } catch (error) {
        console.error('[IPC] Error getting setting:', error);
        return defaultValue;
    }
});

// Set setting
ipcMain.handle('db:setSetting', async (event, key, value) => {
    try {
        return await db.setSetting(key, value);
    } catch (error) {
        console.error('[IPC] Error setting setting:', error);
        return false;
    }
});

// Get database stats
ipcMain.handle('db:getStats', async () => {
    try {
        return await db.getDatabaseStats();
    } catch (error) {
        console.error('[IPC] Error getting stats:', error);
        return { session_count: 0, invoice_count: 0, current_count: 0 };
    }
});

// ============================================================
// ABOUT WINDOW
// ============================================================
function openAboutWindow() {
    const about = new BrowserWindow({
        width: 600,
        height: 500,
        resizable: false,
        minimizable: false,
        maximizable: false,
        parent: mainWindow,
        modal: true,
        autoHideMenuBar: false,
        icon: fs.existsSync(PATHS.icon) ? PATHS.icon : undefined,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, "preload.js")
        }
    });

    about.loadFile(PATHS.about);
}

// ============================================================
// IPC HANDLER FOR ABOUT
// ============================================================
ipcMain.on("show-about", () => {
    openAboutWindow();
});

// ============================================================
// UTILITY: Wait for a service to be ready
// ============================================================
function waitForService(url, timeout = 30000, interval = 500) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();
        const checkService = () => {
            if (Date.now() - startTime > timeout) {
                reject(new Error("Service " + url + " not ready after " + timeout + "ms"));
                return;
            }

            const parsedUrl = new URL(url);
            const options = {
                hostname: parsedUrl.hostname,
                port: parsedUrl.port,
                path: parsedUrl.pathname || '/',
                method: 'GET',
                timeout: 2000
            };

            const req = http.request(options, (res) => {
                resolve();
            });

            req.on('error', () => {
                setTimeout(checkService, interval);
            });

            req.on('timeout', () => {
                req.destroy();
                setTimeout(checkService, interval);
            });

            req.end();
        };

        checkService();
    });
}

// ============================================================
// START PHP SERVER - WITH cwd FIX
// ============================================================
function startPHP() {
    const phpExe = PATHS.phpExe;
    const frontend = PATHS.frontend;
    const phpPort = PORTS.PHP;

    console.log("[PHP] Starting PHP server on port " + phpPort);

    if (!fs.existsSync(phpExe)) {
        console.error("[PHP] ERROR: PHP executable not found at: " + phpExe);
        if (mainWindow) {
            dialog.showErrorBox("PHP Not Found", "PHP executable not found.\n\n" + phpExe);
        }
        return;
    }

    if (!fs.existsSync(frontend)) {
        console.error("[PHP] ERROR: Frontend folder not found at: " + frontend);
        return;
    }

    // CRITICAL FIX: Set cwd to the PHP executable's directory
    // This ensures any relative paths in PHP work correctly in packaged app
    phpProcess = spawn(phpExe, ["-S", "127.0.0.1:" + phpPort, "-t", frontend], {
        stdio: ['ignore', 'pipe', 'pipe'],
        windowsHide: true,
        cwd: path.dirname(phpExe)  // <-- ADD THIS
    });

    phpProcess.stdout.on("data", (data) => {
        const output = data.toString().trim();
        if (output) console.log("[PHP]", output);
    });

    phpProcess.stderr.on("data", (data) => {
        const output = data.toString().trim();
        if (output) console.error("[PHP]", output);
    });

    phpProcess.on("error", (error) => {
        console.error("[PHP] ERROR: Failed to start: " + error.message);
    });

    phpProcess.on("close", (code) => {
        console.log("[PHP] Process exited with code " + code);
    });

    console.log("[PHP] Server started on http://127.0.0.1:" + phpPort);
}

// ============================================================
// START API SERVER - WITH cwd FIX
// ============================================================
function startAPI() {
    const apiExe = PATHS.apiExe;
    const apiPort = PORTS.API;
    
    console.log("[API] Starting API server on port " + apiPort);

    if (!fs.existsSync(apiExe)) {
        console.error("[API] ERROR: API executable not found at: " + apiExe);
        if (mainWindow) {
            dialog.showErrorBox("API Not Found", "AI service executable not found.\n\n" + apiExe);
        }
        return;
    }

    // CRITICAL FIX: Set cwd to the API executable's directory
    // This ensures the Python EXE can find its _internal folder with all resources
    // (model weights, config files, prompt templates, etc.)
    // Without this, the EXE looks in the current working directory (which is wherever Electron was launched from)
    // and fails to find its resources, causing silent extraction failures.
    const apiDir = path.dirname(apiExe);
    console.log("[API] Working directory set to: " + apiDir);

    apiProcess = spawn(apiExe, ["--port", apiPort.toString()], {
        stdio: ['ignore', 'pipe', 'pipe'],
        windowsHide: true,
        cwd: apiDir,  // <-- ADD THIS - CRITICAL FIX
        env: {
            ...process.env,
            PYTHONIOENCODING: 'utf-8',
            PYTHONUTF8: '1'
        }
    });

    apiProcess.stdout.on("data", (data) => {
        const output = data.toString().trim();
        if (output) console.log("[API]", output);
    });

    apiProcess.stderr.on("data", (data) => {
        const output = data.toString().trim();
        if (output) console.error("[API]", output);
    });

    apiProcess.on("error", (error) => {
        console.error("[API] ERROR: Failed to start: " + error.message);
    });

    apiProcess.on("close", (code) => {
        console.log("[API] Process exited with code " + code);
    });

    console.log("[API] Server started on http://127.0.0.1:" + apiPort);
}

// ============================================================
// CREATE MAIN WINDOW
// ============================================================
function createWindow() {
    const iconPath = PATHS.icon;
    console.log("[APP] Icon path: " + iconPath);
    console.log("[APP] Icon exists: " + fs.existsSync(iconPath));

    mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,
        autoHideMenuBar: false,
        icon: fs.existsSync(iconPath) ? iconPath : undefined,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, "preload.js")
        }
    });

    // Set up application menu
    const menuTemplate = [
        {
            label: 'File',
            submenu: [
                {
                    label: 'About SupScan',
                    click: () => openAboutWindow()
                },
                { type: 'separator' },
                {
                    label: 'Exit',
                    click: () => app.quit()
                }
            ]
        },
        {
            label: 'View',
            submenu: [
                { role: 'reload' },
                { role: 'forceReload' },
                { role: 'toggleDevTools' }
            ]
        },
        {
            label: 'Help',
            submenu: [
                {
                    label: 'About SupScan',
                    click: () => openAboutWindow()
                }
            ]
        }
    ];
    Menu.setApplicationMenu(Menu.buildFromTemplate(menuTemplate));

    // Show loading screen immediately
    if (fs.existsSync(PATHS.loading)) {
        mainWindow.loadFile(PATHS.loading);
    } else {
        // Fallback if loading.html doesn't exist
        mainWindow.loadURL(`data:text/html,
            <html>
                <head>
                    <style>
                        body { 
                            font-family: 'Segoe UI', sans-serif;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            background: #ffffff;
                            margin: 0;
                        }
                        .loader { text-align: center; }
                        .spinner {
                            width: 50px;
                            height: 50px;
                            border: 4px solid #e0e0e0;
                            border-top-color: #780000;
                            border-radius: 50%;
                            animation: spin 1s linear infinite;
                        }
                        @keyframes spin { 100% { transform: rotate(360deg); } }
                        h2 { color: #2c3e50; margin-top: 20px; }
                        p { color: #7f8c8d; }
                    </style>
                </head>
                <body>
                    <div class="loader">
                        <div class="spinner"></div>
                        <h2>SupScan</h2>
                        <p>Loading services...</p>
                    </div>
                </body>
            </html>
        `);
    }

    // Once loading is finished, start services and load app
    mainWindow.webContents.once("did-finish-load", () => {
        console.log("[APP] Loading screen displayed");
        loadApp();
    });
}

// ============================================================
// LOAD APP WITH SERVICE CHECKS
// ============================================================
async function loadApp() {
    const phpPort = PORTS.PHP;
    const apiPort = PORTS.API;
    
    try {
        console.log("[APP] Waiting for PHP server...");
        await waitForService("http://127.0.0.1:" + phpPort + "/page1.php", 30000, 500);
        console.log("[APP] PHP server is ready");

        console.log("[APP] Waiting for API server...");
        await waitForService("http://127.0.0.1:" + apiPort + "/api/health", 30000, 500);
        console.log("[APP] API server is ready");

        console.log("[APP] Loading application...");
        mainWindow.loadURL("http://127.0.0.1:" + phpPort + "/page1.php");

    } catch (error) {
        console.error("[APP] ERROR: Failed to start services: " + error.message);
        
        // Load error page
        if (fs.existsSync(PATHS.error)) {
            mainWindow.loadFile(PATHS.error);
            
            // Send error details to the error page
            mainWindow.webContents.once("did-finish-load", () => {
                mainWindow.webContents.send("startup-error", {
                    message: error.message,
                    phpPort: phpPort,
                    apiPort: apiPort
                });
            });
        } else {
            // Fallback error display
            mainWindow.loadURL(`data:text/html,
                <html>
                    <head>
                        <style>
                            body { 
                                font-family: 'Segoe UI', sans-serif;
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                height: 100vh;
                                background: #f8f9fa;
                                margin: 0;
                                padding: 20px;
                            }
                            .error-box {
                                background: white;
                                border-radius: 16px;
                                padding: 50px;
                                max-width: 600px;
                                text-align: center;
                                border-top: 6px solid #e74c3c;
                                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                            }
                            .error-box h1 { color: #2c3e50; font-size: 28px; }
                            .error-box p { color: #7f8c8d; font-size: 16px; }
                            .error-box .error-details {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-top: 20px;
                                font-size: 14px;
                                text-align: left;
                            }
                            .error-box .btn {
                                display: inline-block;
                                padding: 12px 28px;
                                background: #3498db;
                                color: white;
                                border: none;
                                border-radius: 8px;
                                font-size: 16px;
                                font-weight: 600;
                                cursor: pointer;
                                margin-top: 20px;
                            }
                            .error-box .btn:hover { background: #2980b9; }
                        </style>
                    </head>
                    <body>
                        <div class="error-box">
                            <h1>Failed to Start Services</h1>
                            <p>PHP or API services could not be started.</p>
                            <div class="error-details">
                                <strong>Error:</strong><br>
                                ${error.message.replace(/'/g, "\\'")}
                                <br><br>
                                <strong>PHP Port:</strong> ${phpPort}<br>
                                <strong>API Port:</strong> ${apiPort}
                            </div>
                            <button class="btn" onclick="location.reload()">Retry</button>
                        </div>
                    </body>
                </html>
            `);
        }
    }
}

// ============================================================
// APP LIFECYCLE
// ============================================================
app.whenReady().then(async () => {
    console.log("[APP] Electron app starting...");
    console.log("[APP] App directory: " + __dirname);
    
    // Initialize database FIRST
    try {
        await db.initDatabase();
        console.log("[APP] Database initialized successfully");
        
        // Test database connection
        const test = await db.getSetting('test_connection', 'ok');
        console.log("[APP] Database connection test: OK");
    } catch (error) {
        console.error("[APP] Failed to initialize database:", error);
        dialog.showErrorBox("Database Error", "Failed to initialize database.\n\n" + error.message);
        app.quit();
        return;
    }
    
    console.log("[APP] PHP Port: " + PORTS.PHP);
    console.log("[APP] API Port: " + PORTS.API);
    
    createWindow();
    startPHP();
    startAPI();
});

app.on("window-all-closed", () => {
    console.log("[APP] Shutting down...");
    if (phpProcess) phpProcess.kill();
    if (apiProcess) apiProcess.kill();
    app.quit();
});

app.on("activate", () => {
    if (BrowserWindow.getAllWindows().length === 0) {
        createWindow();
    }
});

process.on('uncaughtException', (error) => {
    console.error('[APP] Uncaught Exception:', error);
});

process.on('unhandledRejection', (reason) => {
    console.error('[APP] Unhandled Rejection:', reason);
});

console.log("[APP] main.js loaded successfully");