const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const fs = require('fs');
const { app } = require('electron');

let db = null;

function getDatabasePath() {
    const userDataPath = app.getPath('userData');
    const dbDir = path.join(userDataPath, 'data');
    
    if (!fs.existsSync(dbDir)) {
        fs.mkdirSync(dbDir, { recursive: true });
    }
    
    return path.join(dbDir, 'supscan.db');
}

function initDatabase() {
    const dbPath = getDatabasePath();
    
    return new Promise((resolve, reject) => {
        db = new sqlite3.Database(dbPath, (err) => {
            if (err) {
                console.error('[Database] Error opening database:', err);
                reject(err);
                return;
            }
            
            console.log('[Database] Connected to:', dbPath);
            
            // Use db.exec() to run all CREATE TABLE statements in one transaction
            // This ensures all tables are created before the callback fires
            const schema = `
                CREATE TABLE IF NOT EXISTS sessions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    session_id TEXT UNIQUE NOT NULL,
                    created_at TEXT NOT NULL,
                    data TEXT
                );

                CREATE TABLE IF NOT EXISTS invoices (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    session_id TEXT NOT NULL,
                    invoice_number TEXT,
                    vendor TEXT,
                    reference TEXT,
                    invoice_date TEXT,
                    tax_code TEXT,
                    amount REAL,
                    currency TEXT,
                    description TEXT,
                    created_at TEXT,
                    data TEXT,
                    FOREIGN KEY(session_id) REFERENCES sessions(session_id)
                );

                CREATE TABLE IF NOT EXISTS current_invoices (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    invoice_number TEXT,
                    vendor TEXT,
                    reference TEXT,
                    invoice_date TEXT,
                    tax_code TEXT,
                    amount REAL,
                    currency TEXT,
                    description TEXT,
                    created_at TEXT,
                    data TEXT,
                    position INTEGER
                );

                CREATE TABLE IF NOT EXISTS settings (
                    key TEXT PRIMARY KEY,
                    value TEXT
                );
            `;

            // exec() runs all SQL statements and only calls callback when complete
            db.exec(schema, (err) => {
                if (err) {
                    console.error('[Database] Error creating tables:', err);
                    reject(err);
                    return;
                }

                console.log('[Database] Tables created/verified successfully');
                resolve(db);
            });
        });
    });
}

function getDB() {
    if (!db) {
        throw new Error('Database not initialized. Call initDatabase() first.');
    }
    return db;
}

// ============================================================
// DATABASE OPERATIONS
// ============================================================

// Sessions
function getAllSessions() {
    return new Promise((resolve, reject) => {
        db.all('SELECT * FROM sessions ORDER BY created_at DESC', (err, rows) => {
            if (err) {
                reject(err);
                return;
            }
            resolve(rows);
        });
    });
}

function getSession(sessionId) {
    return new Promise((resolve, reject) => {
        db.get('SELECT * FROM sessions WHERE session_id = ?', [sessionId], (err, row) => {
            if (err) {
                reject(err);
                return;
            }
            resolve(row);
        });
    });
}

function saveSession(sessionId, data) {
    return new Promise((resolve, reject) => {
        const now = new Date().toISOString();
        db.run(
            'INSERT OR REPLACE INTO sessions (session_id, created_at, data) VALUES (?, ?, ?)',
            [sessionId, now, JSON.stringify(data)],
            function(err) {
                if (err) {
                    reject(err);
                    return;
                }
                resolve(this.lastID);
            }
        );
    });
}

function deleteSession(sessionId) {
    return new Promise((resolve, reject) => {
        db.run('DELETE FROM sessions WHERE session_id = ?', [sessionId], function(err) {
            if (err) {
                reject(err);
                return;
            }
            resolve(this.changes);
        });
    });
}

// Invoices for a session
function getInvoicesForSession(sessionId) {
    return new Promise((resolve, reject) => {
        db.all('SELECT * FROM invoices WHERE session_id = ?', [sessionId], (err, rows) => {
            if (err) {
                reject(err);
                return;
            }
            resolve(rows);
        });
    });
}

function saveInvoice(sessionId, invoice) {
    return new Promise((resolve, reject) => {
        const now = new Date().toISOString();
        db.run(`
            INSERT OR REPLACE INTO invoices 
            (session_id, invoice_number, vendor, reference, invoice_date, tax_code, amount, currency, description, created_at, data)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        `, [
            sessionId,
            invoice.invoiceNumber || invoice.id || null,
            invoice.vendor || null,
            invoice.reference || null,
            invoice.invoice_date || null,
            invoice.tax_code || 'V0',
            invoice.amount || null,
            invoice.currency || "MAD",
            invoice.description || null,
            invoice.created_at || now,
            JSON.stringify(invoice)
        ], function(err) {
            if (err) {
                reject(err);
                return;
            }
            resolve(this.lastID);
        });
    });
}

function deleteInvoice(invoiceId) {
    return new Promise((resolve, reject) => {
        db.run('DELETE FROM invoices WHERE id = ?', [invoiceId], function(err) {
            if (err) {
                reject(err);
                return;
            }
            resolve(this.changes);
        });
    });
}

// Current invoices
function getCurrentInvoices() {
    return new Promise((resolve, reject) => {
        db.all('SELECT * FROM current_invoices ORDER BY position ASC', (err, rows) => {
            if (err) {
                reject(err);
                return;
            }
            resolve(rows);
        });
    });
}

function saveCurrentInvoices(invoices) {
    return new Promise((resolve, reject) => {
        db.run('DELETE FROM current_invoices', (err) => {
            if (err) {
                reject(err);
                return;
            }
            
            if (!invoices || invoices.length === 0) {
                resolve();
                return;
            }
            
            const stmt = db.prepare(`
                INSERT INTO current_invoices 
                (invoice_number, vendor, reference, invoice_date, tax_code, amount, currency, description, created_at, data, position)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            `);
            
            let inserted = 0;
            invoices.forEach((invoice, index) => {
                const now = new Date().toISOString();
                stmt.run(
                    invoice.invoiceNumber || invoice.id || null,
                    invoice.vendor || null,
                    invoice.reference || null,
                    invoice.invoice_date || null,
                    invoice.tax_code || 'V0',
                    invoice.amount || null,
                    invoice.currency || "MAD",
                    invoice.description || null,
                    invoice.created_at || now,
                    JSON.stringify(invoice),
                    index,
                    (err) => {
                        if (err) console.error('[Database] Error inserting current invoice:', err);
                        inserted++;
                        if (inserted === invoices.length) {
                            stmt.finalize();
                            resolve();
                        }
                    }
                );
            });
            
            if (invoices.length === 0) {
                stmt.finalize();
                resolve();
            }
        });
    });
}

function clearCurrentInvoices() {
    return new Promise((resolve, reject) => {
        db.run('DELETE FROM current_invoices', function(err) {
            if (err) {
                reject(err);
                return;
            }
            resolve(this.changes);
        });
    });
}

// Settings
function getSetting(key, defaultValue = null) {
    return new Promise((resolve, reject) => {
        db.get('SELECT value FROM settings WHERE key = ?', [key], (err, row) => {
            if (err) {
                reject(err);
                return;
            }
            if (!row) {
                resolve(defaultValue);
                return;
            }
            try {
                resolve(JSON.parse(row.value));
            } catch {
                resolve(row.value);
            }
        });
    });
}

function setSetting(key, value) {
    return new Promise((resolve, reject) => {
        const valueStr = typeof value === 'string' ? value : JSON.stringify(value);
        db.run(
            'INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)',
            [key, valueStr],
            function(err) {
                if (err) {
                    reject(err);
                    return;
                }
                resolve(this.changes);
            }
        );
    });
}

// Database stats
function getDatabaseStats() {
    return new Promise((resolve, reject) => {
        db.all(`
            SELECT 
                (SELECT COUNT(*) FROM sessions) as session_count,
                (SELECT COUNT(*) FROM invoices) as invoice_count,
                (SELECT COUNT(*) FROM current_invoices) as current_count
        `, (err, rows) => {
            if (err) {
                reject(err);
                return;
            }
            resolve(rows[0] || { session_count: 0, invoice_count: 0, current_count: 0 });
        });
    });
}

module.exports = {
    initDatabase,
    getDB,
    getAllSessions,
    getSession,
    saveSession,
    deleteSession,
    getInvoicesForSession,
    saveInvoice,
    deleteInvoice,
    getCurrentInvoices,
    saveCurrentInvoices,
    clearCurrentInvoices,
    getSetting,
    setSetting,
    getDatabaseStats
};