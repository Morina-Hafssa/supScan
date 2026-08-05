<?php
include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des factures</title>
    <!-- Add this in the head section -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f0f2f5;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 2rem;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.15);
            padding: 2rem 2rem 2.5rem;
            margin-left: 60px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .header h1 i {
            color: #780000;
        }

        .header h1 .badge {
            font-size: 0.7rem;
            background: #780000;
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .header-actions {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .storage-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: #f0f2f8;
            border-radius: 30px;
            font-size: 0.8rem;
            color: #4a5a72;
            border: 1px solid #e2e6ef;
        }

        .storage-info i {
            color: #780000;
        }

        .storage-info .storage-bar {
            width: 80px;
            height: 4px;
            background: #e2e6ef;
            border-radius: 2px;
            overflow: hidden;
        }

        .storage-info .storage-bar .storage-fill {
            height: 100%;
            background: #4ca380;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .storage-info .storage-bar .storage-fill.warning {
            background: #f59e0b;
        }

        .storage-info .storage-bar .storage-fill.danger {
            background: #dc3545;
        }

        .btn-excel {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.8rem;
            background: #1e7e34;
            color: white;
            border: none;
            border-radius: 60px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(30, 126, 52, 0.25);
            letter-spacing: 0.3px;
        }

        .btn-excel:hover {
            background: #166b2a;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(30, 126, 52, 0.35);
        }

        .btn-excel:active {
            transform: scale(0.96);
        }

        .btn-excel i {
            font-size: 1.1rem;
        }

        .btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.8rem;
            background: #003049;
            color: white;
            border: none;
            border-radius: 60px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(0, 48, 73, 0.25);
            letter-spacing: 0.3px;
        }

        .btn-refresh:hover {
            background: #004a6e;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 48, 73, 0.35);
        }

        .btn-refresh:active {
            transform: scale(0.96);
        }

        .btn-refresh i {
            font-size: 1.1rem;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 1.2rem;
            border: 1px solid #eef0f5;
            background: #fafbfc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 1100px;
        }

        thead {
            background: #f0f2f8;
            border-bottom: 2px solid #e2e6ef;
        }

        thead th {
            padding: 1rem 1.2rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4a5a72;
            white-space: nowrap;
            position: sticky;
            top: 0;
            background: #f0f2f8;
            z-index: 2;
        }

        thead th i {
            margin-right: 0.4rem;
            color: #6b7a8f;
            font-size: 0.75rem;
        }

        thead th:first-child {
            border-radius: 1.2rem 0 0 0;
        }
        thead th:last-child {
            border-radius: 0 1.2rem 0 0;
        }

        tbody tr {
            border-bottom: 1px solid #eef0f5;
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f5f7fb;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 0.5rem 1.2rem;
            color: #1e2a36;
            vertical-align: middle;
        }

        .col-id {
            font-weight: 600;
            color: #780000;
            font-size: 0.85rem;
        }

        .col-amount {
            font-weight: 600;
            color: #1a1a1a;
        }

        .col-date {
            color: #4a5a72;
            font-size: 0.85rem;
        }

        .col-created {
            color: #6b7a8f;
            font-size: 0.8rem;
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: #5e6f85;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .table-footer .count {
            background: #f0f2f8;
            padding: 0.3rem 1.2rem;
            border-radius: 40px;
            font-weight: 500;
        }

        .table-footer .count span {
            font-weight: 700;
            color: #1a1a1a;
        }

        .table-footer i {
            color: #780000;
            margin-right: 0.3rem;
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #1a1a1a;
            color: white;
            padding: 0.8rem 1.8rem;
            border-radius: 60px;
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.4s ease;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
        }

        .loading-spinner i {
            font-size: 40px;
            color: #780000;
            animation: spin 1s linear infinite;
        }

        .loading-spinner p {
            margin-top: 15px;
            color: #5e6f85;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7a8f;
        }

        .empty-state i {
            font-size: 60px;
            display: block;
            margin-bottom: 15px;
            opacity: 0.3;
            color: #780000;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #718096;
            margin-bottom: 20px;
        }

        .tax-note {
            background: #fff8ec;
            border-left: 4px solid #780000;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.9rem;
            color: #5e4a2e;
        }

        .tax-note i {
            color: #780000;
            font-size: 1.2rem;
        }

        .tax-note strong {
            color: #780000;
        }

        .tax-note .highlight {
            background: #780000;
            color: white;
            padding: 0.1rem 0.6rem;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.2rem;
            }
            .header h1 {
                font-size: 1.2rem;
            }
            .btn-excel, .btn-refresh {
                padding: 0.5rem 1.2rem;
                font-size: 0.85rem;
            }
            .storage-info {
                font-size: 0.7rem;
                padding: 0.3rem 0.8rem;
            }
            .storage-info .storage-bar {
                width: 50px;
            }
            thead th,
            tbody td {
                padding: 0.5rem 0.8rem;
                font-size: 0.8rem;
            }
            .tax-note {
                flex-wrap: wrap;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-file-invoice"></i>
                Liste des factures
                <span class="badge"><i class="fas fa-check-circle"></i> Local</span>
            </h1>
            <div class="header-actions">
                <div class="storage-info" id="storageInfo">
                    <i class="fas fa-database"></i>
                    <span id="storageSize">0 KB</span>
                    <div class="storage-bar">
                        <div class="storage-fill" id="storageFill" style="width: 0%;"></div>
                    </div>
                    <span id="storagePercent">0%</span>
                </div>
                <button class="btn-refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i>
                    Actualiser
                </button>
                <button class="btn-excel" id="exportExcelBtn">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </button>
            </div>
        </div>

        <div class="tax-note">
            <i class="fas fa-info-circle"></i>
            <span>
                <strong>Code TVA :</strong> 
                Par defaut, le code TVA est defini sur <span class="highlight">V0</span> 
                (TVA non applicable). 
            </span>
        </div>

        <div class="table-wrapper">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i>#</th>
                        <th><i class="fas fa-building"></i>Fournisseur</th>
                        <th><i class="fas fa-barcode"></i>Reference</th>
                        <th><i class="fas fa-calendar-alt"></i>Date facture</th>
                        <th><i class="fas fa-tag"></i>Code TVA</th>
                        <th><i class="fas fa-euro-sign"></i>Montant</th>
                        <th><i class="fas fa-coins"></i>Devise</th>
                        <th><i class="fas fa-align-left"></i>Description</th>
                        <th><i class="fas fa-clock"></i>Cree le</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="9" class="loading-spinner">
                            <i class="fas fa-spinner"></i>
                            <p>Chargement...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span class="count">
                <i class="fas fa-database"></i>
                Total : <span id="rowCount">0</span> facture(s)
            </span>
            <span>
                <i class="fas fa-info-circle"></i>
                Donnees stockees localement
            </span>
        </div>
    </div>

    <div class="toast" id="toast"></div>

<script>
        (function() {
            
            let data = [];
            let currentSessionId = null;
            const tbody = document.getElementById('tableBody');
            const rowCount = document.getElementById('rowCount');
            const toast = document.getElementById('toast');

            // ============================================================
            // CHECK IF RUNNING IN ELECTRON
            // ============================================================
            function isElectron() {
                return typeof window !== 'undefined' && window.electronAPI !== undefined;
            }

            // ============================================================
            // DATABASE FUNCTIONS - SQLITE ONLY
            // ============================================================

            async function getCurrentInvoices() {
                try {
                    if (!isElectron()) {
                        console.error('Not running in Electron');
                        return [];
                    }
                    
                    const invoices = await window.electronAPI.db.getCurrentInvoices();
                    console.log('[Database] Retrieved ' + (invoices ? invoices.length : 0) + ' invoices from SQLite');
                    
                    currentSessionId = await window.electronAPI.db.getSetting('currentSessionId', null);
                    console.log('[Database] Current session ID:', currentSessionId);
                    
                    return invoices.map(row => ({
                        id: row.id,
                        invoiceNumber: row.invoice_number,
                        vendor: row.vendor,
                        reference: row.reference,
                        invoice_date: row.invoice_date,
                        tax_code: row.tax_code,
                        amount: row.amount,
                        currency: row.currency,
                        description: row.description,
                        created_at: row.created_at,
                        ...(row.data ? JSON.parse(row.data) : {})
                    }));
                } catch (error) {
                    console.error('[Database] Error getting current invoices:', error);
                    return [];
                }
            }

            async function setCurrentInvoices(invoices) {
                try {
                    if (!isElectron()) {
                        console.error('Not running in Electron');
                        return false;
                    }
                    
                    await window.electronAPI.db.saveCurrentInvoices(invoices);
                    console.log('[Database] Saved ' + (invoices ? invoices.length : 0) + ' invoices to SQLite');
                    return true;
                } catch (error) {
                    console.error('[Database] Error saving current invoices:', error);
                    return false;
                }
            }

            async function updateSessionInvoices(sessionId, invoices) {
                try {
                    if (!isElectron()) {
                        console.error('Not running in Electron');
                        return false;
                    }
                    
                    const session = await window.electronAPI.db.getSession(sessionId);
                    if (!session) {
                        console.error('[Database] Session not found:', sessionId);
                        return false;
                    }
                    
                    const sessionData = JSON.parse(session.data);
                    sessionData.invoices = invoices;
                    
                    await window.electronAPI.db.saveSession(sessionId, sessionData);
                    console.log('[Database] Updated session:', sessionId, 'with', invoices.length, 'invoices');
                    return true;
                } catch (error) {
                    console.error('[Database] Error updating session:', error);
                    return false;
                }
            }

            async function getLastSavedHash() {
                try {
                    if (!isElectron()) {
                        console.error('Not running in Electron');
                        return null;
                    }
                    return await window.electronAPI.db.getSetting('lastSavedInvoiceHash', null);
                } catch (error) {
                    console.error('[Database] Error getting last saved hash:', error);
                    return null;
                }
            }

            async function setLastSavedHash(hash) {
                try {
                    if (!isElectron()) {
                        console.error('Not running in Electron');
                        return;
                    }
                    await window.electronAPI.db.setSetting('lastSavedInvoiceHash', hash);
                } catch (error) {
                    console.error('[Database] Error setting last saved hash:', error);
                }
            }

            // ============================================================
            // SYNC FUNCTIONS
            // ============================================================

            function hashInvoices(invoices) {
                return JSON.stringify(invoices);
            }

            // ============================================================
            // STORAGE INFO
            // ============================================================

            async function getStorageInfo() {
                try {
                    if (!isElectron()) {
                        return { size: 0, session_count: 0, invoice_count: 0 };
                    }
                    
                    const stats = await window.electronAPI.db.getStats();
                    return { 
                        size: stats.session_count * 1024 + stats.invoice_count * 512,
                        session_count: stats.session_count,
                        invoice_count: stats.invoice_count
                    };
                } catch (error) {
                    console.error('[Database] Error getting storage info:', error);
                    return { size: 0, session_count: 0, invoice_count: 0 };
                }
            }

            async function updateStorageInfo() {
                try {
                    const info = await getStorageInfo();
                    const sizeDisplay = document.getElementById('storageSize');
                    const fillDisplay = document.getElementById('storageFill');
                    const percentDisplay = document.getElementById('storagePercent');
                    
                    if (sizeDisplay) {
                        const size = info.size || 0;
                        if (size > 1024 * 1024) {
                            sizeDisplay.textContent = (size / 1024 / 1024).toFixed(2) + ' MB';
                        } else if (size > 1024) {
                            sizeDisplay.textContent = (size / 1024).toFixed(1) + ' KB';
                        } else {
                            sizeDisplay.textContent = size + ' B';
                        }
                    }
                    
                    if (fillDisplay) {
                        const maxSize = 50 * 1024 * 1024;
                        const percent = Math.min((info.size || 0) / maxSize * 100, 100);
                        fillDisplay.style.width = percent + '%';
                        fillDisplay.classList.remove('warning', 'danger');
                        if (percent >= 90) {
                            fillDisplay.classList.add('danger');
                        } else if (percent >= 70) {
                            fillDisplay.classList.add('warning');
                        }
                    }
                    
                    if (percentDisplay) {
                        const maxSize = 50 * 1024 * 1024;
                        const percent = Math.min((info.size || 0) / maxSize * 100, 100);
                        percentDisplay.textContent = Math.round(percent) + '%';
                    }
                } catch (error) {
                    console.error('[Database] Error updating storage info:', error);
                }
            }

            // ============================================================
            // UI FUNCTIONS
            // ============================================================

            function showToast(message, duration = 2500) {
                toast.textContent = message;
                toast.classList.add('show');
                clearTimeout(toast._timeout);
                toast._timeout = setTimeout(() => {
                    toast.classList.remove('show');
                }, duration);
            }

            function formatDateTime(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                } catch (e) {
                    return dateString;
                }
            }

            // ============================================================
            // LOAD INVOICES
            // ============================================================

            async function loadInvoices() {
                if (!isElectron()) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h3>Erreur de connexion</h3>
                                    <p>L'application n'est pas en cours d'exécution dans Electron</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                const invoices = await getCurrentInvoices();

                if (invoices.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3>Aucune facture dans le lot actuel</h3>
                                    <p>Importez des factures depuis la page d'accueil</p>
                                    <button onclick="window.location.href='page1.php'" style="padding: 10px 20px; background: #780000; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                        <i class="fas fa-upload"></i> Importer une facture
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    rowCount.textContent = '0';
                    data = [];
                    updateStorageInfo();
                    return;
                }

                data = invoices;
                renderTable();
                updateStorageInfo();

                if (currentSessionId) {
                    const currentHash = hashInvoices(data);
                    const lastSavedHash = await getLastSavedHash();
                    
                    if (currentHash !== lastSavedHash) {
                        console.log('[Database] Updating existing session with latest data');
                        await updateSessionInvoices(currentSessionId, data);
                        await setLastSavedHash(currentHash);
                    }
                } else {
                    console.warn('[Database] No session ID found.');
                }

                showToast(data.length + ' facture(s) du lot actuel', 2000);
            }

     

            function renderTable() {
                if (!data || data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3>Aucune facture trouvée</h3>
                                </div>
                            </td>
                        </tr>
                    `;
                    rowCount.textContent = '0';
                    return;
                }

                let html = '';
                data.forEach((item, index) => {
                    const displayId = item.invoiceNumber || (item.id ? item.id : index + 1);
                    const description = item.description || item.text || '';
                    const taxCode = item.tax_code || 'V0';

                    html += `
                        <tr>
                            <td class="col-id">#${displayId}</td>
                            <td><strong>${escapeHtml(item.vendor ?? "-")}</strong></td>
                            <td>${escapeHtml(item.reference ?? "-")}</td>
                            <td class="col-date">${escapeHtml(item.invoice_date ?? "-")}</td>
                            <td>${escapeHtml(taxCode)}</td>
                            <td class="col-amount">${escapeHtml(item.amount ?? "-")}</td>
                            <td>${escapeHtml(item.currency ?? "-")}</td>
                            <td>${escapeHtml(description || "-")}</td>
                            <td class="col-created">
                                <i class="far fa-calendar-alt"></i>
                                ${formatDateTime(item.created_at)}
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
                rowCount.textContent = data.length;
            }

            function escapeHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            // ============================================================
            // EXPORT TO EXCEL
            // ============================================================

            function exportToExcel() {
    if (!data || data.length === 0) {
        showToast('Aucune donnée à exporter', 2000);
        return;
    }

    const worksheetData = [];

    // Headers
    worksheetData.push([
        '#',
        'Fournisseur',
        'Reference',
        'Date facture',
        'Code TVA',
        'Montant',
        'Devise',
        'Description',
        'Créé le'
    ]);

    // Data
    data.forEach((item, index) => {
        const displayId = item.invoiceNumber || item.id || index + 1;

        worksheetData.push([
            displayId,
            item.vendor || '',
            item.reference || '',
            item.invoice_date || '',
            item.tax_code || 'V0',
            item.amount || '',
            item.currency || 'MAD',
            item.description || item.text || '',
            formatDateTime(item.created_at)
        ]);
    });

    // Create workbook
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(worksheetData);

    // Column widths
    ws['!cols'] = [
        { wch: 12 },
        { wch: 30 },
        { wch: 20 },
        { wch: 15 },
        { wch: 10 },
        { wch: 15 },
        { wch: 10 },
        { wch: 50 },
        { wch: 22 }
    ];

    XLSX.utils.book_append_sheet(wb, ws, "Factures");

    const today = new Date().toISOString().slice(0, 10);

    XLSX.writeFile(wb, `factures_lot_${today}.xlsx`);

    showToast('Export Excel terminé', 2000);
}

            // ============================================================
            // INITIALIZATION
            // ============================================================

            (async function init() {
                if (!isElectron()) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h3>Erreur de connexion</h3>
                                    <p>L'application n'est pas en cours d'exécution dans Electron</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                await loadInvoices();
                
                document.getElementById('refreshBtn').addEventListener('click', loadInvoices);
                document.getElementById('exportExcelBtn').addEventListener('click', exportToExcel);
                
                setInterval(updateStorageInfo, 30000);
                
                console.log('Using SQLite database only - Read-only mode');
            })();

        })();
</script>

</body>
</html>