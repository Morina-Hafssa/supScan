<?php
include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail de la session</title>
    <!-- Add this in the head section -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ... (all your existing styles remain the same) ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            background: #f0f2f5;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 2rem;
            margin-left: 60px;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.15);
            padding: 2rem 2rem 2.5rem;
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

        .session-info {
            background: #f0f2f8;
            padding: 0.3rem 1.2rem;
            border-radius: 40px;
            font-size: 0.85rem;
            color: #5e6f85;
            font-family: 'Courier New', monospace;
        }

        .header-actions {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.8rem;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 60px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            box-shadow: 0 6px 16px rgba(108, 117, 125, 0.25);
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
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
        }

        .btn-excel:hover {
            background: #166b2a;
            transform: translateY(-2px);
        }

        .btn-export-selected {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.8rem;
            background: #e67e22;
            color: white;
            border: none;
            border-radius: 60px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(230, 126, 34, 0.25);
        }

        .btn-export-selected:hover {
            background: #d35400;
            transform: translateY(-2px);
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
            min-width: 800px;
        }

        thead {
            background: #f0f2f8;
            border-bottom: 2px solid #e2e6ef;
        }

        thead th {
            padding: 1rem 1.2rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
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
        }

        tbody tr {
            border-bottom: 1px solid #eef0f5;
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f5f7fb;
        }

        tbody td {
            padding: 0.6rem 1.2rem;
            color: #1e2a36;
            vertical-align: middle;
        }

        .checkbox-column {
            width: 40px;
            text-align: center;
        }

        .checkbox-column input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #780000;
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

        .no-data {
            text-align: center;
            padding: 3rem 1rem;
            color: #8a9bb0;
        }

        .no-data i {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
            color: #d0d7e0;
        }

        @media (max-width: 640px) {
            .container { padding: 1.2rem; }
            .header h1 { font-size: 1.2rem; }
            .btn-excel, .btn-back, .btn-export-selected { padding: 0.5rem 1.2rem; font-size: 0.85rem; }
            thead th, tbody td { padding: 0.5rem 0.8rem; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

    <div class="container">

        <div class="header">
            <h1>
                <i class="fas fa-folder-open"></i>
                Detail de la session
                <span class="session-info" id="sessionIdDisplay">-</span>
            </h1>
            <div class="header-actions">
                <button class="btn-back" onclick="window.location.href='page5.php'">
                    <i class="fas fa-arrow-left"></i> Retour
                </button>
                <button class="btn-export-selected" id="exportSelectedBtn">
                    <i class="fas fa-file-excel"></i> Exporter selection
                </button>
                <button class="btn-excel" id="exportAllBtn">
                    <i class="fas fa-file-excel"></i> Tout exporter
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table id="invoiceTable">
                <thead>
                    <tr>
                        <th class="checkbox-column">
                            <input type="checkbox" id="selectAllInvoices">
                        </th>
                        <th><i class="fas fa-hashtag"></i>#</th>
                        <th><i class="fas fa-building"></i>Fournisseur</th>
                        <th><i class="fas fa-barcode"></i>Reference</th>
                        <th><i class="fas fa-calendar-alt"></i>Date facture</th>
                        <th><i class="fas fa-tag"></i>Code TVA</th>
                        <th><i class="fas fa-euro-sign"></i>Montant</th>
                        <th><i class="fas fa-coins"></i>Devise</th>
                        <th><i class="fas fa-align-left"></i>Description</th>
                    </tr>
                </thead>
                <tbody id="invoiceBody">
                    <tr>
                        <td colspan="9">
                            <div class="no-data">
                                <i class="fas fa-spinner fa-spin"></i>
                                Chargement...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span class="count">
                <i class="fas fa-database"></i>
                Total : <span id="invoiceCount">0</span> facture(s)
            </span>
            
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        (function() {
            
            let sessionData = null;
            let invoices = [];
            const invoiceBody = document.getElementById('invoiceBody');
            const invoiceCount = document.getElementById('invoiceCount');
            const sessionIdDisplay = document.getElementById('sessionIdDisplay');
            const toast = document.getElementById('toast');

            // ============================================================
            // CHECK IF RUNNING IN ELECTRON
            // ============================================================
            function isElectron() {
                return typeof window !== 'undefined' && window.electronAPI !== undefined;
            }

            // ============================================================
            // DATABASE FUNCTIONS USING SQLITE VIA ELECTRON IPC
            // ============================================================

            async function getSelectedSession() {
                try {
                    if (isElectron()) {
                        const selected = await window.electronAPI.db.getSetting('selectedSession', null);
                        console.log('[Session] Raw selected session from DB:', selected);
                        
                        if (!selected) {
                            console.log('[Session] No selected session found');
                            return null;
                        }
                        
                        let parsed = typeof selected === 'string' ? JSON.parse(selected) : selected;
                        console.log('[Session] Parsed session data:', parsed);
                        
                        // Ensure invoices is an array
                        if (!parsed.invoices) {
                            parsed.invoices = [];
                        }
                        if (!Array.isArray(parsed.invoices)) {
                            console.warn('[Session] Invoices is not an array, converting...');
                            parsed.invoices = Object.values(parsed.invoices);
                        }
                        
                        return parsed;
                    } else {
                        const data = localStorage.getItem('selectedSession');
                        return data ? JSON.parse(data) : null;
                    }
                } catch (error) {
                    console.error('[Session] Error getting selected session:', error);
                    return null;
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

function exportInvoices(invoiceData, filename = 'factures') {
    if (!invoiceData || invoiceData.length === 0) {
        showToast('Aucune donnée à exporter', 2000);
        return;
    }

    const rows = invoiceData.map((item, index) => ({
        "#": item.invoiceNumber || item.id || index + 1,
        "Fournisseur": item.vendor || "",
        "Reference": item.reference || "",
        "Date facture": item.invoice_date || "",
        "Code TVA": item.tax_code || "V0",
        "Montant": item.amount || "",
        "Devise": item.currency || "",
        "Description": item.description || "",
        "Date de creation": formatDateTime(item.created_at)
    }));

    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.json_to_sheet(rows);

    XLSX.utils.book_append_sheet(workbook, worksheet, "Factures");

    XLSX.writeFile(workbook, `${filename}_${new Date().toISOString().slice(0,10)}.xlsx`);

    showToast(`Export de ${invoiceData.length} factures terminé`, 2000);
}

            async function loadSession() {
                try {
                    console.log('[Session] Loading session...');
                    const data = await getSelectedSession();
                    console.log('[Session] Retrieved data:', data);
                    
                    if (!data) {
                        invoiceBody.innerHTML = `
                            <tr>
                                <td colspan="9">
                                    <div class="no-data">
                                        <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>
                                        Aucune session selectionnee
                                        <br><small style="color: #6b7a8f;">Retournez a l'historique</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    sessionData = data;
                    sessionIdDisplay.textContent = sessionData.sessionID || 'Session inconnue';
                    
                    // Make sure invoices is an array
                    if (sessionData.invoices) {
                        invoices = Array.isArray(sessionData.invoices) ? sessionData.invoices : Object.values(sessionData.invoices);
                    } else {
                        invoices = [];
                    }
                    
                    console.log('[Session] Invoices found:', invoices.length);
                    
                    if (invoices.length === 0) {
                        invoiceBody.innerHTML = `
                            <tr>
                                <td colspan="9">
                                    <div class="no-data">
                                        <i class="fas fa-inbox"></i>
                                        Aucune facture dans cette session
                                        <br><small style="color: #6b7a8f;">La session contient ${invoices.length} facture(s)</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                        invoiceCount.textContent = '0';
                        return;
                    }

                    renderInvoices();
                    showToast(invoices.length + ' facture(s) chargee(s)', 2000);

                } catch (error) {
                    console.error('[Session] Error loading session:', error);
                    invoiceBody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="no-data">
                                    <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>
                                    Erreur de chargement<br>
                                    <small style="color: #6b7a8f;">${error.message}</small>
                                </div>
                            </td>
                        </tr>
                    `;
                    showToast('Erreur de chargement', 3000);
                }
            }

            function renderInvoices() {
                if (!invoices || invoices.length === 0) {
                    invoiceBody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    Aucune facture dans cette session
                                </div>
                            </td>
                        </tr>
                    `;
                    invoiceCount.textContent = '0';
                    return;
                }

                let html = '';
                invoices.forEach((item, index) => {
                    const displayId = item.invoiceNumber || (item.id ? item.id : index + 1);
                    
                    let amountDisplay = '-';
                    if (item.amount !== null && item.amount !== undefined) {
                        const numAmount = typeof item.amount === 'number' ? item.amount : parseFloat(item.amount);
                        if (!isNaN(numAmount)) {
                            amountDisplay = numAmount.toFixed(2);
                            if (item.currency) {
                                amountDisplay += ' ' + item.currency;
                            }
                        }
                    }
                    
                    html += `
                        <tr>
                            <td class="checkbox-column">
                                <input type="checkbox" class="invoiceCheck" data-index="${index}">
                            </td>
                            <td class="col-id">#${displayId}</td>
                            <td><strong>${item.vendor ?? "-"}</strong></td>
                            <td>${item.reference ?? "-"}</td>
                            <td class="col-date">${item.invoice_date ?? "-"}</td>
                            <td>${item.tax_code ?? "V0"}</td>
                            <td class="col-amount">${amountDisplay}</td>
                            <td>${item.currency ?? "-"}</td>
                            <td>${item.description || item.text || "-"}</td>
                        </tr>
                    `;
                });

                invoiceBody.innerHTML = html;
                invoiceCount.textContent = invoices.length;

                const selectAll = document.getElementById('selectAllInvoices');
                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.invoiceCheck').forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                }
            }

            function exportSelectedInvoices() {
                const selectedCheckboxes = document.querySelectorAll('.invoiceCheck:checked');
                
                if (selectedCheckboxes.length === 0) {
                    showToast('Veuillez selectionner au moins une facture', 2000);
                    return;
                }

                let selectedInvoices = [];
                selectedCheckboxes.forEach(checkbox => {
                    const index = parseInt(checkbox.dataset.index);
                    if (invoices[index]) {
                        selectedInvoices.push(invoices[index]);
                    }
                });

                if (selectedInvoices.length === 0) {
                    showToast('Aucune facture a exporter', 2000);
                    return;
                }

                const sessionId = sessionData ? sessionData.sessionID : 'session';
                exportInvoices(selectedInvoices, 'session_' + sessionId + '_selection');
            }

            function exportAllInvoices() {
                if (!invoices || invoices.length === 0) {
                    showToast('Aucune facture a exporter', 2000);
                    return;
                }

                const sessionId = sessionData ? sessionData.sessionID : 'session';
                exportInvoices(invoices, 'session_' + sessionId);
            }

            // ============================================================
            // INITIALIZATION
            // ============================================================

            loadSession();

            document.getElementById('exportSelectedBtn').addEventListener('click', exportSelectedInvoices);
            document.getElementById('exportAllBtn').addEventListener('click', exportAllInvoices);

            console.log('[Session] Session detaillee chargee');
            console.log('[Session] Architecture: session avec ses factures');
            console.log('[Session] Using SQLite via Electron IPC');

        })();
    </script>

</body>
</html>