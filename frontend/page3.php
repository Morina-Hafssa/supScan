<?php
session_start();
include("Sidebar.php");
// No more invoice ID needed - we use localStorage
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupScan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        
        :root {
            --main-color: #780000;
            --seondary-color: #003049;
            --accent-color: #4ca380;
            --white: #ffffff;
            --sidebar-bg: #0a1628;
            --hover-bg: rgba(120, 0, 0, 0.15);
            --active-bg: rgba(120, 0, 0, 0.25);
            --text-muted: rgba(255, 255, 255, 0.6);
            --border-light: rgba(255, 255, 255, 0.06);
            --bg-light: #f8f9fa;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-light);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 70px;
            padding: 30px 35px;
            flex: 1;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header .header-left h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--seondary-color);
        }

        .page-header .header-left .document-badge {
            background: var(--accent-color);
            color: var(--white);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .page-header .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .navigation-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-nav {
            padding: 10px 18px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            background: var(--white);
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav:hover:not(:disabled) {
            border-color: var(--accent-color);
            background: var(--bg-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-nav:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .btn-nav-primary {
            background: var(--seondary-color);
            color: var(--white);
            border-color: var(--seondary-color);
        }

        .btn-nav-primary:hover:not(:disabled) {
            background: #002538;
            border-color: #002538;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 48, 73, 0.3);
        }

        .btn-edit {
            background: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-edit:hover {
            background: #3a8f6e;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 163, 128, 0.4);
        }

        .btn-edit.saving {
            background: var(--main-color);
        }

        .btn-edit.saving:hover {
            background: #660000;
            box-shadow: 0 4px 15px rgba(120, 0, 0, 0.4);
        }

        .document-container {
            display: flex;
            gap: 25px;
            margin-top: 10px;
            align-items: stretch;
        }

        .document-preview,
        .data-container {
            flex: 1;
            min-width: 0;
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }

        .document-preview .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--bg-light);
            flex-shrink: 0;
        }

        .document-preview .preview-header h3 {
            font-size: 16px;
            color: var(--seondary-color);
            font-weight: 600;
        }

        .document-preview .preview-header .page-info {
            font-size: 13px;
            color: #718096;
            background: var(--bg-light);
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
        }

        .document-preview .preview-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            background: var(--bg-light);
            border-radius: 12px;
            padding: 20px;
            border: 2px dashed var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .preview-content iframe {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border: none;
            border-radius: 8px;
        }

        .preview-content img {
            max-width: 100%;
            max-height: 450px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .doc-preview {
            text-align: center;
            padding: 20px;
        }

        .doc-preview i {
            font-size: 64px;
            color: #2b5797;
            margin-bottom: 15px;
        }

        .doc-preview p {
            margin: 10px 0;
            color: var(--text-color);
            font-weight: 500;
        }

        .doc-preview a {
            display: inline-block;
            padding: 10px 24px;
            background: var(--accent-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .doc-preview a:hover {
            background: #3a8f6e;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 163, 128, 0.4);
        }

        .data-container .data-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--bg-light);
            flex-wrap: wrap;
            gap: 10px;
        }

        .data-container .data-header h3 {
            font-size: 16px;
            color: var(--seondary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .data-container .data-header h3 i {
            color: var(--accent-color);
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 5px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 700px;
        }

        table thead {
            background: var(--bg-light);
        }

        table thead th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--seondary-color);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        table thead th i {
            margin-right: 6px;
            font-size: 12px;
            color: var(--accent-color);
        }

        table tbody tr {
            transition: all 0.2s ease;
        }

        table tbody tr:hover {
            background: rgba(76, 163, 128, 0.05);
        }

        table tbody tr:nth-child(even) {
            background: var(--bg-light);
        }

        table tbody tr:nth-child(even):hover {
            background: rgba(76, 163, 128, 0.08);
        }

        table tbody td {
            padding: 14px 16px;
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }

        table tbody td input {
            width: 100%;
            padding: 6px 10px;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: var(--white);
        }

        table tbody td input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(76, 163, 128, 0.1);
        }

        .value-display {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .value-display:hover {
            background: #ebf8ff;
            outline: 2px dashed var(--accent-color);
        }

        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .loading-spinner i {
            font-size: 40px;
            color: var(--accent-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .file-meta {
            margin-top: 15px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            font-size: 13px;
            color: #718096;
        }

        .file-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .preview-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-back {
            background: var(--bg-light);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-back:hover {
            background: #e2e8f0;
        }

        .btn-download {
            background: var(--accent-color);
            color: white;
        }

        .btn-download:hover {
            background: #3a8f6e;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 163, 128, 0.4);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 1200px) {
            .document-container {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 220px;
                padding: 20px;
            }

            .page-header .header-left h1 {
                font-size: 22px;
            }

            .data-container .data-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .navigation-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .btn-nav {
                padding: 8px 14px;
                font-size: 14px;
            }

            table {
                font-size: 12px;
                min-width: 600px;
            }

            table thead th,
            table tbody td {
                padding: 10px 12px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header .header-actions {
                width: 100%;
                flex-wrap: wrap;
            }

            .document-preview,
            .data-container {
                padding: 15px;
            }

            .btn-nav {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <h1><i class="fas fa-file-invoice" style="color: var(--accent-color); margin-right: 10px;"></i>Détails du Document</h1>
                <span class="document-badge"><i class="fas fa-check-circle"></i> En cours</span>
            </div>
            <div class="header-actions">
                <button class="btn-edit" id="editBtn" onclick="toggleEdit()">
                    <i class="fas fa-edit"></i>
                    <span id="editBtnText">Modifier</span>
                </button>
            </div>
        </div>

        <!-- Document Container -->
        <div class="document-container">
            <!-- Left: Document Preview -->
            <div class="document-preview">
                <div class="preview-header">
                    <h3><i class="fas fa-file" style="color: var(--main-color);"></i> Aperçu du document</h3>
                    <span class="page-info" id="pageCounter">1 / 1</span>
                </div>
                <div class="preview-content" id="documentPreview">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner"></i>
                        <p>Chargement du document...</p>
                    </div>
                </div>
            </div>

            <!-- Right: Data Table -->
            <div class="data-container">
                <div class="data-header">
                    <h3>
                        <i class="fas fa-table"></i>
                        Données extraites
                        <span style="font-size: 12px; font-weight: 400; color: #718096; margin-left: 5px;">
                            (7 champs)
                        </span>
                    </h3>
                    <div class="navigation-buttons">
                        <button class="btn-nav" id="prevBtn" onclick="navigatePrev()">
                            <i class="fas fa-chevron-left"></i> Précédent
                        </button>
                        <button class="btn-nav btn-nav-primary" id="nextBtn" onclick="navigateNext()">
                            Suivant <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn-nav btn-nav-terminer" id="terminerBtn" onclick="terminer()">
                            <i class="fas fa-check-circle"></i> Terminer
                        </button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i>Champ</th>
                                <th><i class="fas fa-info-circle"></i>Valeur</th>
                            </tr>
                        </thead>
                        <tbody id="dataBody">
                            <tr>
                                <td><strong>Nom du fournisseur</strong></td>
                                <td id="vendor">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Numéro de facture</strong></td>
                                <td id="reference">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Date de facture</strong></td>
                                <td id="invoice_date">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Code Tax</strong></td>
                                <td id="tax_code">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Devise</strong></td>
                                <td id="currency">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td id="amount">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Description</strong></td>
                                <td id="text">
                                    <span class="value-display">Loading...</span>
                                    <input type="text" class="value-input" value="" style="display: none;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    // ============================================================
    // CHECK IF RUNNING IN ELECTRON
    // ============================================================
    function isElectron() {
        return typeof window !== 'undefined' && window.electronAPI !== undefined;
    }

    // ============================================================
    // DATABASE FUNCTIONS - SQLITE VIA ELECTRON IPC
    // ============================================================
    
    async function getCurrentInvoices() {
        try {
            if (isElectron()) {
                const invoices = await window.electronAPI.db.getCurrentInvoices();
                // Convert DB rows to invoice objects
                return invoices.map(row => {
                    // If we have JSON data stored, use it
                    if (row.data) {
                        try {
                            return JSON.parse(row.data);
                        } catch (e) {
                            // Fall back to column data
                        }
                    }
                    // Otherwise construct from columns
                    return {
                        id: row.id,
                        invoiceNumber: row.invoice_number,
                        vendor: row.vendor,
                        reference: row.reference,
                        invoice_date: row.invoice_date,
                        tax_code: row.tax_code,
                        amount: row.amount,
                        currency: row.currency,
                        description: row.description,
                        text: row.description,
                        created_at: row.created_at,
                        preview: row.preview,
                        fileType: row.file_type,
                        fileName: row.file_name
                    };
                });
            } else {
                // Fallback to localStorage
                const data = localStorage.getItem("currentInvoices");
                return data ? JSON.parse(data) : [];
            }
        } catch (error) {
            console.error('Error getting current invoices:', error);
            return [];
        }
    }

    async function saveCurrentInvoices(invoices) {
        try {
            if (isElectron()) {
                await window.electronAPI.db.saveCurrentInvoices(invoices);
                return true;
            } else {
                localStorage.setItem('currentInvoices', JSON.stringify(invoices));
                return true;
            }
        } catch (error) {
            console.error('Error saving current invoices:', error);
            return false;
        }
    }

    // Get invoices from database
    let invoices = [];
    let currentInvoice = 0;

    async function loadInvoices() {
        invoices = await getCurrentInvoices();
        currentInvoice = Number(localStorage.getItem("currentInvoice")) || 0;

        if (invoices.length === 0) {
            window.location.href = "page1.php";
            return;
        }

        if (currentInvoice >= invoices.length) {
            currentInvoice = 0;
            localStorage.setItem("currentInvoice", "0");
        }

        console.log('Total invoices:', invoices.length);
        console.log('Current index:', currentInvoice);
        
        // Show first invoice
        showInvoice(currentInvoice);
    }

    // DOM references
    const pageCounter = document.getElementById('pageCounter');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const terminerBtn = document.getElementById('terminerBtn');
    const editBtn = document.getElementById('editBtn');

    let isEditing = false;

    // Main function to show invoice
    function showInvoice(index) {
        // Validate index
        if (index < 0) index = 0;
        if (index >= invoices.length) index = invoices.length - 1;
        
        currentInvoice = index;
        const invoice = invoices[currentInvoice];

        // Update page title
        if (invoice.vendor) {
            document.querySelector('.page-header .header-left h1').innerHTML = 
                `<i class="fas fa-file-invoice" style="color: var(--accent-color); margin-right: 10px;"></i>${invoice.vendor}`;
        }

        // Update table
        updateTable(invoice);

        // Update preview
        updatePreview(invoice);

        // Update buttons
        updateButtons();

        // Update counter
        pageCounter.innerText = `${currentInvoice + 1} / ${invoices.length}`;

        // Update localStorage
        localStorage.setItem("currentInvoice", String(currentInvoice));

        console.log(`Showing invoice ${currentInvoice + 1}/${invoices.length}`);
    }

    // Update table with invoice data
    function updateTable(invoice) {
        document.getElementById("vendor").querySelector('.value-display').innerText = invoice.vendor || "-";
        document.getElementById("vendor").querySelector('.value-input').value = invoice.vendor || "";

        document.getElementById("reference").querySelector('.value-display').innerText = invoice.reference || "-";
        document.getElementById("reference").querySelector('.value-input').value = invoice.reference || "";

        document.getElementById("invoice_date").querySelector('.value-display').innerText = invoice.invoice_date || "-";
        document.getElementById("invoice_date").querySelector('.value-input').value = invoice.invoice_date || "";

        document.getElementById("tax_code").querySelector('.value-display').innerText = invoice.tax_code || "V0";
        document.getElementById("tax_code").querySelector('.value-input').value = invoice.tax_code || "V0";

        document.getElementById("currency").querySelector('.value-display').innerText = invoice.currency || "MAD";
        document.getElementById("currency").querySelector('.value-input').value = invoice.currency || "MAD";

        document.getElementById("amount").querySelector('.value-display').innerText = invoice.amount || "-";
        document.getElementById("amount").querySelector('.value-input').value = invoice.amount || "";

        document.getElementById("text").querySelector('.value-display').innerText = invoice.description || invoice.text || "-";
        document.getElementById("text").querySelector('.value-input').value = invoice.description || invoice.text || "";
    }

    // Update preview based on file type - WITH BASE64 SUPPORT
    function updatePreview(invoice) {
        const previewContainer = document.getElementById('documentPreview');
        
        console.log('Invoice preview data:', {
            hasPreview: !!invoice?.preview,
            previewUrl: invoice?.preview ? invoice.preview.substring(0, 100) + '...' : null,
            fileType: invoice?.fileType,
            fullInvoice: invoice
        });

        if (!invoice || !invoice.preview) {
            previewContainer.innerHTML = `
                <i class="fas fa-file-invoice" style="font-size: 80px; color: var(--accent-color); margin-bottom: 20px;"></i>
                <div style="font-size: 16px; color: #4a5a72;">${invoice?.vendor || 'Facture'} #${currentInvoice + 1}</div>
                <div style="font-size: 13px; color: #718096; margin-top: 10px;">${invoice?.reference || 'Aucun aperçu disponible'}</div>
            `;
            return;
        }

        const previewUrl = invoice.preview;
        
        // Check if it's a base64 image (starts with data:image)
        if (previewUrl && previewUrl.startsWith('data:image')) {
            const html = `
                <img src="${previewUrl}" 
                     alt="Document preview" 
                     class="preview-image"
                     onerror="this.onerror=null; this.alt='Image non disponible'; this.style.display='none';">
                <div class="file-meta">
                    <span><i class="fas fa-file-image"></i> Image (base64)</span>
                    <span><i class="fas fa-tag"></i> Facture ${currentInvoice + 1}</span>
                    <span style="font-size: 11px; color: #a0aec0;">Encodée dans le document</span>
                </div>
            `;
            previewContainer.innerHTML = html;
            return;
        }

        // If not base64, handle other file types
        const fileType = invoice.fileType || 'unknown';
        let html = '';

        switch(fileType) {
            case 'png':
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'webp':
                html = `
                    <img src="${previewUrl}" 
                        alt="Document preview" 
                        class="preview-image"
                        onerror="this.onerror=null; this.alt='Image non disponible'; this.style.display='none';">
                    <div class="file-meta">
                        <span><i class="fas fa-file-image"></i> Image</span>
                        <span><i class="fas fa-tag"></i> Facture ${currentInvoice + 1}</span>
                    </div>
                `;
                break;

            case 'pdf':
                html = `
                    <div style="width: 100%; height: 100%; min-height: 500px; display: flex; flex-direction: column;">
                        <div style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; justify-content: center;">
                            <a href="${previewUrl}" target="_blank" class="btn-action btn-download" style="padding: 8px 20px; background: var(--accent-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-external-link-alt"></i> Ouvrir dans un nouvel onglet
                            </a>
                            <a href="${previewUrl}" download class="btn-action btn-download" style="padding: 8px 20px; background: #003049; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </div>
                        <div style="flex: 1; min-height: 400px; background: #f0f0f0; border-radius: 8px; overflow: hidden; position: relative;">
                            <iframe src="${previewUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                                    width="100%" 
                                    height="100%"
                                    style="min-height: 450px; border: none; border-radius: 8px;"
                                    allow="fullscreen">
                            </iframe>
                        </div>
                        <div class="file-meta" style="margin-top: 10px;">
                            <span><i class="fas fa-file-pdf" style="color: #dc3545;"></i> PDF</span>
                            <span><i class="fas fa-tag"></i> Facture ${currentInvoice + 1}</span>
                        </div>
                    </div>
                `;
                break;

            default:
                // Try to detect from URL
                if (previewUrl.match(/\.(jpg|jpeg|png|gif|webp)/i)) {
                    html = `
                        <img src="${previewUrl}" 
                            alt="Document preview" 
                            class="preview-image">
                        <div class="file-meta">
                            <span><i class="fas fa-file-image"></i> Image</span>
                            <span><i class="fas fa-tag"></i> Facture ${currentInvoice + 1}</span>
                        </div>
                    `;
                } else {
                    html = `
                        <i class="fas fa-file" style="font-size: 80px; color: var(--accent-color); margin-bottom: 20px;"></i>
                        <div style="font-size: 14px; color: #718096;">Type de fichier non supporté</div>
                        <div style="font-size: 12px; color: #a0aec0; margin-top: 5px;">${invoice.fileName || 'Document'}</div>
                        <a href="${previewUrl}" target="_blank" class="btn-action" style="margin-top: 15px; padding: 10px 24px; background: var(--accent-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 500;">
                            <i class="fas fa-external-link-alt"></i> Ouvrir le document
                        </a>
                        <div class="file-meta">
                            <span><i class="fas fa-tag"></i> Facture ${currentInvoice + 1}</span>
                        </div>
                    `;
                }
        }

        previewContainer.innerHTML = html;
    }

    // Update navigation buttons - ALL buttons always shown
    function updateButtons() {
        // Previous button - only disabled when on first invoice
        prevBtn.disabled = currentInvoice === 0;
        
        // Always show all buttons
        prevBtn.style.display = 'inline-flex';
        nextBtn.style.display = 'inline-flex';
        terminerBtn.style.display = 'inline-flex';
        
        // Update next button text based on current position
        if (currentInvoice === invoices.length - 1) {
            nextBtn.innerHTML = 'Suivant <i class="fas fa-chevron-right"></i>';
            nextBtn.className = 'btn-nav btn-nav-primary';
        } else {
            nextBtn.innerHTML = 'Suivant <i class="fas fa-chevron-right"></i>';
            nextBtn.className = 'btn-nav btn-nav-primary';
        }
        
        // Terminer button is always visible and enabled
        terminerBtn.disabled = false;
    }

    // Navigation functions
    function navigatePrev() {
        if (currentInvoice > 0) {
            // Save changes before navigating
            if (isEditing) {
                saveChanges();
                toggleEdit();
            }
            currentInvoice--;
            showInvoice(currentInvoice);
        }
    }

    function navigateNext() {
        // Save changes before navigating
        if (isEditing) {
            saveChanges();
            toggleEdit();
        }

        if (currentInvoice < invoices.length - 1) {
            currentInvoice++;
            showInvoice(currentInvoice);
        } else {
            // Last invoice - show notification
            showNotification(' Vous êtes sur la dernière facture', 'info');
        }
    }

    // Terminer function - Always available
    function terminer() {
        // Save changes if in edit mode
        if (isEditing) {
            saveChanges();
            toggleEdit();
        }
        
        // Show confirmation
        showNotification(' Toutes les factures ont été traitées!', 'success');
        
        // Redirect to page4 (statistics/export)
        setTimeout(() => {
            window.location.href = 'page4.php';
        }, 500);
    }

    // Edit functionality
    function toggleEdit() {
        isEditing = !isEditing;

        const editBtn = document.getElementById('editBtn');
        const editBtnText = document.getElementById('editBtnText');
        const displays = document.querySelectorAll('.value-display');
        const inputs = document.querySelectorAll('.value-input');

        if (isEditing) {
            editBtn.classList.add('saving');
            editBtnText.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
            editBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
            displays.forEach(el => el.style.display = 'none');
            inputs.forEach(el => el.style.display = 'block');
        } else {
            saveChanges();
            editBtn.classList.remove('saving');
            editBtnText.innerHTML = '<i class="fas fa-edit"></i> Modifier';
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Modifier';
            displays.forEach(el => el.style.display = 'inline');
            inputs.forEach(el => el.style.display = 'none');
            showNotification(' Données enregistrées avec succès!', 'success');
        }
    }

    // Save changes to localStorage - UPDATED to also update history
    async function saveChanges() {
        try {
            const invoice = invoices[currentInvoice];
            
            invoice.vendor = document.querySelector('#vendor .value-input').value;
            invoice.reference = document.querySelector('#reference .value-input').value;
            invoice.invoice_date = document.querySelector('#invoice_date .value-input').value;
            invoice.tax_code = document.querySelector('#tax_code .value-input').value || "V0";
            invoice.currency = document.querySelector('#currency .value-input').value;
            invoice.amount = document.querySelector('#amount .value-input').value;
            invoice.description = document.querySelector('#text .value-input').value;
            invoice.text = document.querySelector('#text .value-input').value;

            // Save to database
            await saveCurrentInvoices(invoices);
            
            // Also update history if this invoice exists there
            let history = JSON.parse(localStorage.getItem("invoiceHistory")) || [];
            
            const current = invoices[currentInvoice];
            
            // Try to find by reference and vendor
            let index = -1;
            if (current.reference && current.vendor) {
                index = history.findIndex(h => 
                    h.reference === current.reference && 
                    h.vendor === current.vendor
                );
            }
            
            // If not found by reference/vendor, try by created_at
            if (index === -1 && current.created_at) {
                index = history.findIndex(h => 
                    h.created_at === current.created_at
                );
            }
            
            // If found, update it
            if (index !== -1) {
                history[index] = { ...current };
                localStorage.setItem(
                    "invoiceHistory",
                    JSON.stringify(history)
                );
                console.log(' History updated for invoice:', current.reference || current.vendor);
            } else {
                console.log('ℹ️ Invoice not found in history (may be a new upload)');
            }
            
            // Update display
            updateTable(invoice);
            
            console.log(' Changes saved to currentInvoices and history');
            
        } catch (error) {
            console.error('Error saving changes:', error);
            showNotification(' Erreur lors de l\'enregistrement', 'error');
        }
    }

    // Show notification helper
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#003049' : '#780000'};
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInRight 0.4s ease;
            border-left: 4px solid ${type === 'success' ? '#4ca380' : '#fc8181'};
            max-width: 400px;
        `;
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        notification.innerHTML = `
            <i class="fas ${icon}" style="font-size: 20px;"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: rgba(255,255,255,0.6); cursor: pointer; font-size: 18px; margin-left: 10px;">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100px)';
                notification.style.transition = 'all 0.4s ease';
                setTimeout(() => notification.remove(), 400);
            }
        }, 4000);
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigatePrev();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigateNext();
        } else if (e.key === 'Enter' && e.ctrlKey) {
            e.preventDefault();
            toggleEdit();
        } else if (e.key === 'Enter' && e.shiftKey) {
            e.preventDefault();
            terminer();
        }
    });

    // Initialize - load invoices
    loadInvoices();
</script>
</body>
</html>