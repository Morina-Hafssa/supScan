<?php
include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique </title>
    
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

        .header h1 span {
            background: #f0f2f8;
            padding: 0.2rem 0.8rem;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #5e6f85;
            margin-left: 0.5rem;
        }

        .header-actions {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
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
        }

        .btn-refresh:hover {
            background: #004a6b;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 48, 73, 0.35);
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
            box-shadow: 0 10px 24px rgba(30, 126, 52, 0.35);
        }

        .btn-excel i, .btn-refresh i {
            font-size: 1.1rem;
        }

        .filter-section {
            background: #f8f9fc;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
            border: 1px solid #eef0f5;
        }

        .filter-section label {
            font-weight: 600;
            color: #4a5a72;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .filter-section select {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid #d5d9e0;
            font-size: 0.9rem;
            background: white;
            cursor: pointer;
            outline: none;
            min-width: 140px;
        }

        .filter-section select:focus {
            border-color: #780000;
            box-shadow: 0 0 0 2px rgba(120, 0, 0, 0.1);
        }

        .filter-section input[type="date"] {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid #d5d9e0;
            font-size: 0.9rem;
            background: white;
            cursor: pointer;
            outline: none;
        }

        .filter-section input[type="date"]:focus {
            border-color: #780000;
            box-shadow: 0 0 0 2px rgba(120, 0, 0, 0.1);
        }

        .filter-section .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-section .filter-group.hidden {
            display: none;
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
            min-width: 700px;
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

        .col-group-select {
            width: 50px;
            text-align: center;
            vertical-align: middle;
        }

        .col-group-select input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #780000;
        }

        .col-session-check {
            width: 40px;
            text-align: center;
        }

        .col-session-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #003049;
        }

        .col-session-id {
            font-weight: 600;
            color: #003049;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }

        .col-count {
            font-weight: 700;
            color: #780000;
            text-align: center;
        }

        .col-group {
            background: #f8f9fc;
            border-top: 2px solid #d5d9e0;
            border-bottom: 2px solid #d5d9e0;
        }

        .col-group td {
            padding: 0.6rem 1.2rem;
            background: #f8f9fc;
            vertical-align: middle;
        }

        .col-group .group-label {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 600;
            color: #003049;
        }

        .col-group .group-label i {
            color: #780000;
            font-size: 1rem;
        }

        .col-group .group-count {
            background: #780000;
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .group-rowspan-cell {
            background: #f8f9fc;
            border-right: 2px solid #d5d9e0;
            border-top: 2px solid #d5d9e0;
            border-bottom: 2px solid #d5d9e0;
            vertical-align: middle;
            text-align: center;
            min-width: 50px;
            padding: 0.8rem 0.5rem;
        }

        .group-rowspan-cell .group-check-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }

        .group-rowspan-cell .group-check-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #780000;
        }

        .group-rowspan-cell .group-check-wrapper .group-label-text {
            font-size: 0.6rem;
            font-weight: 600;
            color: #4a5a72;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .group-rowspan-cell .group-check-wrapper .group-badge {
            background: #780000;
            color: white;
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-size: 0.6rem;
            font-weight: 700;
        }

        .group-divider {
            border-top: 3px solid #d5d9e0;
        }

        .btn-view-session {
            background: #003049;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 0.4rem 1.2rem;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-view-session:hover {
            background: #004a6e;
            transform: scale(1.05);
        }

        .btn-view-session i {
            margin-right: 0.3rem;
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

        .loading-spinner {
            text-align: center;
            padding: 3rem 1rem;
            color: #8a9bb0;
        }

        .loading-spinner i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
            color: #780000;
            display: block;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            box-shadow: 0 10px 24px rgba(230, 126, 34, 0.35);
        }

        table tr {
            padding: 20px 0px;
        }

        @media (max-width: 640px) {
            .container { padding: 1.2rem; }
            .header h1 { font-size: 1.2rem; }
            .btn-excel, .btn-refresh, .btn-export-selected { padding: 0.5rem 1.2rem; font-size: 0.85rem; }
            thead th, tbody td { padding: 0.5rem 0.8rem; font-size: 0.8rem; }
            .filter-section { padding: 0.8rem; }
            .filter-section select, .filter-section input[type="date"] { font-size: 0.8rem; padding: 0.4rem 0.8rem; }
            .group-rowspan-cell .group-check-wrapper .group-label-text { font-size: 0.5rem; }
        }

        tbody tr {
            border-bottom: 1px solid #eef0f5;
            transition: background 0.2s ease;
            box-shadow: 0 4px 0 0 #f0f2f5;
        }

        tbody tr {
            position: relative;
        }

        tbody td, tbody th {
            padding: 16px 12px;
        }

        .group-rowspan-cell {
            padding: 12px 8px;
            vertical-align: middle;
            text-align: center;
            min-width: 50px;
        }
    </style>
</head>
<body>

    <div class="container">

        <div class="header">
            <h1>
                <i class="fas fa-history"></i>
                Historique des sessions
                <span><i class="far fa-clock"></i> Par lot</span>
            </h1>
            <div class="header-actions">
                <button class="btn-refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i>
                    Rafraichir
                </button>
                <button class="btn-export-selected" id="exportSelectedBtn">
                    <i class="fas fa-file-excel"></i>
                    Exporter selection
                </button>
                <button class="btn-excel" id="exportAllBtn">
                    <i class="fas fa-file-excel"></i>
                    Tout exporter
                </button>
            </div>
        </div>

        <!-- FILTER / GROUP SECTION -->
        <div class="filter-section">
            <label>
                <i class="fas fa-layer-group"></i> Grouper par :
            </label>
            <select id="groupBySelect">
                <option value="none">Aucun groupe</option>
                <option value="day">Par jour</option>
                <option value="week">Par semaine</option>
                <option value="month">Par mois</option>
                <option value="custom">Personnalise</option>
            </select>

            <div class="filter-group hidden" id="customRangeGroup">
                <label>
                    <i class="fas fa-calendar-alt"></i> Du :
                </label>
                <input type="date" id="startDate">
                <label>
                    <i class="fas fa-calendar-alt"></i> Au :
                </label>
                <input type="date" id="endDate">
                <button class="btn-refresh" id="applyCustomRange" style="padding: 0.4rem 1.2rem; font-size: 0.85rem;">
                    <i class="fas fa-check"></i> Appliquer
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table id="sessionTable">
                <thead>
                    <tr>
                        <th class="col-group-select"><i class="fas fa-layer-group"></i></th>
                        <th class="col-session-check">
                            <input type="checkbox" id="selectAllSessions">
                        </th>
                        <th><i class="fas fa-hashtag"></i>Session ID</th>
                        <th><i class="fas fa-calendar-alt"></i>Date</th>
                        <th><i class="fas fa-file-invoice"></i>Factures</th>
                        <th><i class="fas fa-cog"></i>Action</th>
                    </tr>
                </thead>
                <tbody id="sessionBody">
                    <tr>
                        <td colspan="6">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner"></i>
                                Chargement des sessions...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span class="count">
                <i class="fas fa-database"></i>
                Total : <span id="sessionCount">0</span> session(s)
            </span>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        (function() {
            
            let allSessions = [];
            let filteredSessions = [];
            let groupedSessions = [];
            const sessionBody = document.getElementById('sessionBody');
            const sessionCount = document.getElementById('sessionCount');
            const toast = document.getElementById('toast');
            const groupBySelect = document.getElementById('groupBySelect');
            const customRangeGroup = document.getElementById('customRangeGroup');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');

            // ============================================================
            // CHECK IF RUNNING IN ELECTRON
            // ============================================================
            function isElectron() {
                return typeof window !== 'undefined' && window.electronAPI !== undefined;
            }

            // ============================================================
            // DATABASE FUNCTIONS USING SQLITE VIA ELECTRON IPC
            // ============================================================

            async function getInvoiceSessions() {
                try {
                    if (isElectron()) {
                        const sessions = await window.electronAPI.db.getAllSessions();
                        console.log('[Database] Raw sessions from SQLite:', sessions);
                        
                        const parsedSessions = sessions.map(session => {
                            let sessionData = {};
                            let invoices = [];
                            
                            try {
                                if (session.data) {
                                    sessionData = typeof session.data === 'string' ? JSON.parse(session.data) : session.data;
                                    invoices = sessionData.invoices || [];
                                }
                            } catch (e) {
                                console.error('[Database] Error parsing session data:', e);
                            }
                            
                            return {
                                sessionID: session.session_id,
                                created_at: session.created_at,
                                invoices: invoices,
                                _raw: session
                            };
                        });
                        
                        console.log('[Database] Parsed sessions:', parsedSessions);
                        return parsedSessions;
                    } else {
                        // Fallback to localStorage
                        const data = localStorage.getItem('invoiceSessions');
                        return data ? JSON.parse(data) : [];
                    }
                } catch (error) {
                    console.error('[Database] Error getting invoice sessions:', error);
                    return [];
                }
            }

            // Debug function to check what's in the database
            async function debugDatabase() {
                try {
                    if (isElectron()) {
                        const stats = await window.electronAPI.db.getStats();
                        console.log('[Database] Stats:', stats);
                        
                        const sessions = await window.electronAPI.db.getAllSessions();
                        console.log('[Database] All sessions:', sessions.map(s => ({
                            id: s.session_id,
                            created: s.created_at,
                            dataLength: s.data ? s.data.length : 0,
                            dataPreview: s.data ? s.data.substring(0, 200) : 'null'
                        })));
                    }
                } catch (e) {
                    console.error('[Database] Debug error:', e);
                }
            }

            async function getSelectedSession() {
                try {
                    if (isElectron()) {
                        const selected = await window.electronAPI.db.getSetting('selectedSession', null);
                        return selected ? JSON.parse(selected) : null;
                    } else {
                        const data = localStorage.getItem('selectedSession');
                        return data ? JSON.parse(data) : null;
                    }
                } catch (error) {
                    console.error('Error getting selected session:', error);
                    return null;
                }
            }

            async function setSelectedSession(session) {
                try {
                    if (isElectron()) {
                        await window.electronAPI.db.setSetting('selectedSession', JSON.stringify(session));
                    } else {
                        localStorage.setItem('selectedSession', JSON.stringify(session));
                    }
                } catch (error) {
                    console.error('Error setting selected session:', error);
                }
            }

            // ============================================================
            // END STORAGE FUNCTIONS
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

            function formatDateShort(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                } catch (e) {
                    return dateString;
                }
            }

            function getWeekNumber(date) {
                const d = new Date(date);
                d.setHours(0, 0, 0, 0);
                d.setDate(d.getDate() + 3 - (d.getDay() + 6) % 7);
                const week1 = new Date(d.getFullYear(), 0, 4);
                return 1 + Math.round(((d - week1) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
            }

            function getMonthLabel(dateString) {
                if (!dateString) return 'Inconnu';
                try {
                    const date = new Date(dateString);
                    const months = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
                    return months[date.getMonth()] + ' ' + date.getFullYear();
                } catch (e) {
                    return dateString;
                }
            }

function exportInvoices(invoiceData, filename = 'factures') {
    if (!invoiceData || invoiceData.length === 0) {
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
        'Date de création'
    ]);

    // Data
    invoiceData.forEach((item, index) => {
        const displayId = item.invoiceNumber || item.id || index + 1;

        worksheetData.push([
            displayId,
            item.vendor || '',
            item.reference || '',
            item.invoice_date || '',
            item.tax_code || 'V0',
            item.amount || '',
            item.currency || 'MAD',
            item.description || '',
            formatDateTime(item.created_at)
        ]);
    });

    // Create workbook
    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);

    // Column widths
    worksheet['!cols'] = [
        { wch: 12 }, // #
        { wch: 30 }, // Fournisseur
        { wch: 20 }, // Reference
        { wch: 15 }, // Date facture
        { wch: 10 }, // TVA
        { wch: 15 }, // Montant
        { wch: 10 }, // Devise
        { wch: 50 }, // Description
        { wch: 22 }  // Date création
    ];

    XLSX.utils.book_append_sheet(workbook, worksheet, 'Factures');

    const today = new Date().toISOString().slice(0, 10);

    XLSX.writeFile(workbook, `${filename}_${today}.xlsx`);

    showToast(`Export de ${invoiceData.length} factures terminé`, 2000);
}

            async function loadSessions() {
                try {
                    console.log('[History] Loading sessions...');
                    await debugDatabase(); // Add this to debug
                    
                    const data = await getInvoiceSessions();
                    console.log('[History] Loaded sessions:', data);
                    
                    if (!data || data.length === 0) {
                        sessionBody.innerHTML = `
                            <tr>
                                <td colspan="6">
                                    <div class="no-data">
                                        <i class="fas fa-inbox"></i>
                                        Aucune session d'historique
                                        <br><small style="color: #6b7a8f;">Importez des factures depuis la page d'accueil</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                        sessionCount.textContent = '0';
                        allSessions = [];
                        filteredSessions = [];
                        groupedSessions = [];
                        return;
                    }

                    // Filter out sessions with no invoices
                    allSessions = data.filter(session => session.invoices && session.invoices.length > 0);
                    console.log('[History] Sessions with invoices:', allSessions.length);
                    
                    // Sort sessions: newest first
                    allSessions.sort((a, b) => {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });
                    
                    filteredSessions = [...allSessions];
                    applyGrouping();
                    showToast(allSessions.length + ' session(s) chargee(s)', 2000);

                } catch (error) {
                    console.error('[History] Error loading sessions:', error);
                    sessionBody.innerHTML = `
                        <tr>
                            <td colspan="6">
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

            function applyGrouping() {
                const groupBy = groupBySelect.value;
                
                if (groupBy === 'none') {
                    renderSessionsFlat(filteredSessions);
                    return;
                }

                let sessionsToGroup = [...filteredSessions];
                
                if (groupBy === 'custom') {
                    const start = startDate.value;
                    const end = endDate.value;
                    
                    if (start && end) {
                        const startDateObj = new Date(start);
                        const endDateObj = new Date(end);
                        endDateObj.setHours(23, 59, 59, 999);
                        
                        sessionsToGroup = sessionsToGroup.filter(session => {
                            const sessionDate = new Date(session.created_at);
                            return sessionDate >= startDateObj && sessionDate <= endDateObj;
                        });
                    }
                }

                const groups = {};
                
                sessionsToGroup.forEach(session => {
                    let key;
                    const date = new Date(session.created_at);
                    
                    switch(groupBy) {
                        case 'day':
                            key = formatDateShort(session.created_at);
                            break;
                        case 'week':
                            const weekNum = getWeekNumber(date);
                            const year = date.getFullYear();
                            key = 'Semaine ' + weekNum + ' - ' + year;
                            break;
                        case 'month':
                            key = getMonthLabel(session.created_at);
                            break;
                        case 'custom':
                            key = 'Periode selectionnee';
                            break;
                        default:
                            key = 'Autre';
                    }
                    
                    if (!groups[key]) {
                        groups[key] = [];
                    }
                    groups[key].push(session);
                });

                // Convert to array for rendering
                groupedSessions = Object.keys(groups).map(key => ({
                    groupName: key,
                    sessions: groups[key],
                    totalInvoices: groups[key].reduce((sum, s) => sum + (s.invoices ? s.invoices.length : 0), 0)
                }));

                // Sort groups by date 
                groupedSessions.sort((a, b) => {
                    const dateA = extractDateFromGroup(a.groupName);
                    const dateB = extractDateFromGroup(b.groupName);
                    if (dateA && dateB) {
                        return dateB - dateA;
                    }
                    return a.groupName.localeCompare(b.groupName);
                });

                renderGroupedSessions();
            }

            function extractDateFromGroup(groupName) {
                const weekMatch = groupName.match(/Semaine (\d+) - (\d{4})/);
                if (weekMatch) {
                    const week = parseInt(weekMatch[1]);
                    const year = parseInt(weekMatch[2]);
                    const date = new Date(year, 0, 1 + (week - 1) * 7);
                    return date;
                }
                
                const monthMatch = groupName.match(/(\d{2})\/(\d{4})/);
                if (monthMatch) {
                    return new Date(parseInt(monthMatch[2]), parseInt(monthMatch[1]) - 1, 1);
                }
                
                const monthNames = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 
                                    'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
                for (let i = 0; i < monthNames.length; i++) {
                    if (groupName.includes(monthNames[i])) {
                        const yearMatch = groupName.match(/\d{4}/);
                        if (yearMatch) {
                            return new Date(parseInt(yearMatch[0]), i, 1);
                        }
                    }
                }
                
                const dateMatch = groupName.match(/(\d{2})\/(\d{2})\/(\d{4})/);
                if (dateMatch) {
                    return new Date(parseInt(dateMatch[3]), parseInt(dateMatch[2]) - 1, parseInt(dateMatch[1]));
                }
                
                return null;
            }

            function renderGroupedSessions() {
                if (!groupedSessions || groupedSessions.length === 0) {
                    sessionBody.innerHTML = `
                        <tr>
                            <td colspan="6">
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    Aucune session dans ce groupe
                                </div>
                            </td>
                        </tr>
                    `;
                    sessionCount.textContent = '0';
                    return;
                }

                let html = '';
                let totalSessions = 0;
                let globalIndex = 0;

                groupedSessions.forEach((group, groupIndex) => {
                    const groupSize = group.sessions.length;
                    const groupInvoiceCount = group.totalInvoices || 0;
                    totalSessions += groupSize;

                    group.sessions.forEach((session, idx) => {
                        // Make sure invoices is an array
                        const invoices = session.invoices || [];
                        const invoiceCount = invoices.length;
                        
                        console.log(`[History] Session ${session.sessionID} has ${invoiceCount} invoices`);
                        
                        html += `
                            <tr ${idx === 0 ? 'class="group-start"' : ''}>
                                ${idx === 0 ? `
                                    <td class="group-rowspan-cell" rowspan="${groupSize}">
                                        <div class="group-check-wrapper">
                                            <input type="checkbox" class="groupCheck" data-group-index="${groupIndex}">
                                            <span class="group-badge">${groupInvoiceCount}</span>
                                            <span class="group-label-text">${group.groupName}</span>
                                        </div>
                                    </td>
                                ` : ''}
                                <td class="col-session-check">
                                    <input type="checkbox" class="sessionCheck" data-index="${globalIndex}">
                                </td>
                                <td class="col-session-id">
                                    <i class="fas fa-folder"></i> ${session.sessionID}
                                </td>
                                <td>
                                    <i class="far fa-calendar-alt"></i>
                                    ${formatDateTime(session.created_at)}
                                </td>
                                <td class="col-count">
                                    <span class="badge-invoice">${invoiceCount}</span>
                                    ${invoiceCount > 1 ? 'factures' : 'facture'}
                                </td>
                                <td>
                                    <button class="btn-view-session" onclick="openSession(${globalIndex})">
                                        <i class="fas fa-eye"></i> Voir
                                    </button>
                                </td>
                            </tr>
                        `;
                        globalIndex++;
                    });
                });

                sessionBody.innerHTML = html;
                sessionCount.textContent = totalSessions;

                // Update allSessions for the openSession function
                const flatSessions = [];
                groupedSessions.forEach(group => {
                    group.sessions.forEach(session => {
                        flatSessions.push(session);
                    });
                });
                allSessions = flatSessions;

                document.querySelectorAll('.groupCheck').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const groupIndex = parseInt(this.dataset.groupIndex);
                        const group = groupedSessions[groupIndex];
                        const startRow = this.closest('tr');
                        const allRowsInGroup = [];
                        
                        let currentRow = startRow;
                        let rowIndex = 0;
                        while (currentRow && rowIndex < group.sessions.length) {
                            allRowsInGroup.push(currentRow);
                            currentRow = currentRow.nextElementSibling;
                            rowIndex++;
                        }
                        
                        allRowsInGroup.forEach(row => {
                            const sessionCheck = row.querySelector('.sessionCheck');
                            if (sessionCheck) {
                                sessionCheck.checked = this.checked;
                            }
                        });
                    });
                });

                document.querySelectorAll('.sessionCheck').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const row = this.closest('tr');
                        const groupCell = row.previousElementSibling?.querySelector('.groupCheck') || 
                                         row.querySelector('.groupCheck');
                        if (groupCell) {
                            const groupCheck = groupCell;
                            const groupIndex = parseInt(groupCheck.dataset.groupIndex);
                            const group = groupedSessions[groupIndex];
                            
                            let allChecked = true;
                            let currentRow = groupCheck.closest('tr');
                            let rowCount = 0;
                            while (currentRow && rowCount < group.sessions.length) {
                                const check = currentRow.querySelector('.sessionCheck');
                                if (check && !check.checked) {
                                    allChecked = false;
                                    break;
                                }
                                currentRow = currentRow.nextElementSibling;
                                rowCount++;
                            }
                            
                            groupCheck.checked = allChecked;
                        }
                    });
                });

                const selectAll = document.getElementById('selectAllSessions');
                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.sessionCheck').forEach(cb => {
                            cb.checked = this.checked;
                        });
                        
                        document.querySelectorAll('.groupCheck').forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                }
            }

            function renderSessionsFlat(sessions) {
                if (!sessions || sessions.length === 0) {
                    sessionBody.innerHTML = `
                        <tr>
                            <td colspan="6">
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    Aucune session disponible
                                </div>
                            </td>
                        </tr>
                    `;
                    sessionCount.textContent = '0';
                    return;
                }

                let html = '';
                sessions.forEach((session, index) => {
                    const invoices = session.invoices || [];
                    const invoiceCount = invoices.length;
                    
                    html += `
                        <tr>
                            <td class="col-group-select"></td>
                            <td class="col-session-check">
                                <input type="checkbox" class="sessionCheck" data-index="${index}">
                            </td>
                            <td class="col-session-id">
                                <i class="fas fa-folder"></i> ${session.sessionID}
                            </td>
                            <td>
                                <i class="far fa-calendar-alt"></i>
                                ${formatDateTime(session.created_at)}
                            </td>
                            <td class="col-count">
                                <span class="badge-invoice">${invoiceCount}</span>
                                ${invoiceCount > 1 ? 'factures' : 'facture'}
                            </td>
                            <td>
                                <button class="btn-view-session" onclick="openSession(${index})">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                            </td>
                        </tr>
                    `;
                });

                sessionBody.innerHTML = html;
                sessionCount.textContent = sessions.length;
                allSessions = sessions;

                const selectAll = document.getElementById('selectAllSessions');
                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        document.querySelectorAll('.sessionCheck').forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                }
            }

            window.openSession = async function(index) {
                if (!allSessions[index]) {
                    showToast('Session introuvable', 2000);
                    return;
                }

                const session = allSessions[index];
                console.log('[History] Opening session:', session.sessionID, 'with', session.invoices.length, 'invoices');
                await setSelectedSession(session);
                window.location.href = "session.php";
            };

            function exportSelectedSessions() {
                const selectedCheckboxes = document.querySelectorAll('.sessionCheck:checked');
                
                if (selectedCheckboxes.length === 0) {
                    showToast('Veuillez selectionner au moins une session', 2000);
                    return;
                }

                let allInvoices = [];
                selectedCheckboxes.forEach(checkbox => {
                    const index = parseInt(checkbox.dataset.index);
                    if (allSessions[index] && allSessions[index].invoices) {
                        allInvoices = allInvoices.concat(allSessions[index].invoices);
                    }
                });

                if (allInvoices.length === 0) {
                    showToast('Aucune facture a exporter', 2000);
                    return;
                }

                exportInvoices(allInvoices, 'factures_' + selectedCheckboxes.length + '_sessions');
            }

            function exportAllSessions() {
                if (!allSessions || allSessions.length === 0) {
                    showToast('Aucune session a exporter', 2000);
                    return;
                }

                let allInvoices = [];
                allSessions.forEach(session => {
                    if (session.invoices) {
                        allInvoices = allInvoices.concat(session.invoices);
                    }
                });

                if (allInvoices.length === 0) {
                    showToast('Aucune facture a exporter', 2000);
                    return;
                }

                exportInvoices(allInvoices, 'toutes_les_factures');
            }

            groupBySelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customRangeGroup.classList.remove('hidden');
                } else {
                    customRangeGroup.classList.add('hidden');
                    applyGrouping();
                }
            });

            document.getElementById('applyCustomRange').addEventListener('click', function() {
                if (!startDate.value || !endDate.value) {
                    showToast('Veuillez selectionner une date de debut et de fin', 2000);
                    return;
                }
                if (new Date(startDate.value) > new Date(endDate.value)) {
                    showToast('La date de debut doit etre anterieure a la date de fin', 2000);
                    return;
                }
                applyGrouping();
            });

            // Set default dates for custom range (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];

            // ============================================================
            // INITIALIZATION
            // ============================================================
            console.log('[History] Application started');
            console.log('[History] Using SQLite database');

            // Add a manual refresh for debugging (Ctrl+R)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'r' && e.ctrlKey) {
                    e.preventDefault();
                    console.log('[History] Manual refresh triggered');
                    loadSessions();
                }
            });

            loadSessions();

            document.getElementById('refreshBtn').addEventListener('click', function() {
                const icon = this.querySelector('i');
                icon.classList.add('fa-spin');
                loadSessions();
                setTimeout(() => {
                    icon.classList.remove('fa-spin');
                }, 500);
            });

            document.getElementById('exportSelectedBtn').addEventListener('click', exportSelectedSessions);
            document.getElementById('exportAllBtn').addEventListener('click', exportAllSessions);

            console.log('Historique des sessions avec groupement charge');
            console.log('Groupes disponibles: jour, semaine, mois, personnalise');
            console.log('Using SQLite via Electron IPC');

        })();
    </script>

</body>
</html>