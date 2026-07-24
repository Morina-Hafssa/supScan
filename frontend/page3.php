<?php
session_start();
include("Sidebar.php");

$invoiceId = $_GET['id'] ?? null;

if (!$invoiceId) {
    header("Location: page1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Scanner - SupScan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ... your existing styles ... */
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

        /* ... rest of your styles ... */
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

        .document-preview {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            min-height: 500px;
            position: relative;
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

        .data-container {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            width: 100%;
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

        .data-container .data-header .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-next {
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
        }

        .btn-edit {
            background: var(--accent-color);
            color: var(--white);
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

        .btn-next {
            background: var(--seondary-color);
            color: var(--white);
        }

        .btn-next:hover {
            background: #002538;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 48, 73, 0.4);
        }

        .btn-next:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
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

        table tbody td .empty-cell {
            color: #a0aec0;
            font-style: italic;
            font-size: 12px;
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

        table tbody td input:hover {
            border-color: var(--accent-color);
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

        .loading-spinner p {
            margin-top: 15px;
            color: #718096;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .preview-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            object-fit: contain;
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

        .btn-pdf {
            background: #dc3545;
            color: white;
        }

        .btn-pdf:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        .pdf-icon-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .pdf-icon-container i {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 10px;
        }

        .file-name {
            font-size: 14px;
            color: #4a5a72;
            margin-bottom: 10px;
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

            .data-container .data-header .action-buttons {
                width: 100%;
            }

            .btn-edit, .btn-next {
                flex: 1;
                justify-content: center;
                padding: 10px 16px;
                font-size: 13px;
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
            }

            .document-preview,
            .data-container {
                padding: 15px;
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
                <button class="btn-next" id="nextBtn" onclick="handleNext()">
                    <i class="fas fa-arrow-right"></i>
                    Suivant
                </button>
            </div>
        </div>

        <!-- Document Container -->
        <div class="document-container">
            <!-- Left: Document Preview -->
            <div class="document-preview">
                <div class="preview-header">
                    <h3><i class="fas fa-file" style="color: var(--main-color);"></i> Aperçu du document</h3>
                    <span class="page-info"><i class="fas fa-file"></i> Document</span>
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
        // Edit functionality
        let isEditing = false;

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
                // Save changes to backend
                saveChanges();

                editBtn.classList.remove('saving');
                editBtnText.innerHTML = '<i class="fas fa-edit"></i> Modifier';
                editBtn.innerHTML = '<i class="fas fa-edit"></i> Modifier';
                inputs.forEach((input, index) => {
                    const display = displays[index];
                    if (display && input.value.trim() !== '') {
                        display.textContent = input.value;
                    }
                    display.style.display = 'inline';
                    input.style.display = 'none';
                });
                showNotification('Données enregistrées avec succès!', 'success');
            }
        }

        async function saveChanges() {
            try {
                const invoiceId = <?= $invoiceId ?>;
                
                const vendor = document.querySelector('#vendor .value-input').value;
                const reference = document.querySelector('#reference .value-input').value;
                const invoice_date = document.querySelector('#invoice_date .value-input').value;
                const currency = document.querySelector('#currency .value-input').value;
                const amount = document.querySelector('#amount .value-input').value;
                const text = document.querySelector('#text .value-input').value;

                const response = await fetch(
                    `http://127.0.0.1:8000/api/invoices/${invoiceId}`,
                    {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            vendor: vendor,
                            reference: reference,
                            invoice_date: invoice_date,
                            currency: currency,
                            amount: amount,
                            text: text
                        })
                    }
                );

                if (!response.ok) {
                    throw new Error('Failed to save changes');
                }

                const data = await response.json();
                console.log('Saved successfully:', data);
                
            } catch (error) {
                console.error('Error saving changes:', error);
                showNotification('Erreur lors de l\'enregistrement', 'error');
            }
        }

        function handleNext() {
            const nextBtn = document.getElementById('nextBtn');
            nextBtn.disabled = true;
            nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';

            setTimeout(() => {
                showNotification('Document traité avec succès!', 'success');
                window.location.href = 'page4.php?id=<?= $invoiceId ?>';
            }, 1500);
        }

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

        // Add animation keyframes
        const style = document.createElement('style');
        style.textContent = `
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
        `;
        document.head.appendChild(style);

        // Load invoice data from Laravel API
        const invoiceId = <?= $invoiceId ?>;

        async function loadInvoice() {
            try {
                const response = await fetch(
                    `http://127.0.0.1:8000/api/invoices/${invoiceId}`
                );

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const invoice = await response.json();

                console.log('Invoice data:', invoice);

                // Update all fields with data from API
                document.getElementById("vendor").querySelector('.value-display').innerText =
                    invoice.vendor ?? "-";
                document.getElementById("vendor").querySelector('.value-input').value =
                    invoice.vendor ?? "";

                document.getElementById("reference").querySelector('.value-display').innerText =
                    invoice.reference ?? "-";
                document.getElementById("reference").querySelector('.value-input').value =
                    invoice.reference ?? "";

                document.getElementById("invoice_date").querySelector('.value-display').innerText =
                    invoice.invoice_date ?? "-";
                document.getElementById("invoice_date").querySelector('.value-input').value =
                    invoice.invoice_date ?? "";

                document.getElementById("tax_code").querySelector('.value-display').innerText = "A0";
                document.getElementById("tax_code").querySelector('.value-input').value = "A0";

                document.getElementById("currency").querySelector('.value-display').innerText =
                    invoice.currency ?? "-";
                document.getElementById("currency").querySelector('.value-input').value =
                    invoice.currency ?? "";

                document.getElementById("amount").querySelector('.value-display').innerText =
                    invoice.amount ?? "-";
                document.getElementById("amount").querySelector('.value-input').value =
                    invoice.amount ?? "";

                document.getElementById("text").querySelector('.value-display').innerText =
                    invoice.text ?? "-";
                document.getElementById("text").querySelector('.value-input').value =
                    invoice.text ?? "";

                // Load document preview
                loadDocumentPreview(invoice);

            } catch (error) {
                console.error('Error loading invoice:', error);
                alert('Cannot load invoice. Please make sure the Laravel backend is running.');
            }
        }

        // Load document preview from API
        async function loadDocumentPreview(invoice) {
            const previewContainer = document.getElementById('documentPreview');
            
            try {
                if (invoice && invoice.file_path) {
                    const fileExtension = invoice.file_path.split('.').pop().toLowerCase();
                    
                    // Use the new file serving route
                    const fileUrl = `http://127.0.0.1:8000/api/files/${invoice.file_path}`;
                    
                    console.log('File path from DB:', invoice.file_path);
                    console.log('Full URL:', fileUrl);
                    
                    // Test if the file is accessible
                    try {
                        const testResponse = await fetch(fileUrl, { method: 'HEAD' });
                        console.log('File access test:', testResponse.ok ? '✅ Accessible' : '❌ Not accessible');
                    } catch (e) {
                        console.warn('Error testing file URL:', e);
                    }
                    
                    let html = '';
                    
                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                        html = `
                            <img src="${fileUrl}" 
                                alt="Document preview" 
                                class="preview-image"
                                onerror="this.onerror=null; this.alt='Image non disponible'; this.style.display='none'; document.getElementById('imageError').style.display='block';"
                                style="max-width: 100%; max-height: 400px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div id="imageError" style="display: none; color: #dc3545; margin-top: 10px;">
                                <i class="fas fa-exclamation-circle"></i> Image non disponible
                            </div>
                            <div class="file-meta">
                                <span><i class="fas fa-file-image"></i> Image</span>
                                <span><i class="fas fa-tag"></i> ID: ${invoice.id}</span>
                                <span><i class="fas fa-info-circle"></i> ${invoice.file_path.split('/').pop()}</span>
                            </div>
                        `;
                    } else if (fileExtension === 'pdf') {
                        html = `
                            <div class="pdf-icon-container">
                                <i class="fas fa-file-pdf" style="font-size: 80px; color: #dc3545; margin-bottom: 10px;"></i>
                                <div class="file-name" style="font-size: 14px; color: #4a5a72; margin-bottom: 10px;">
                                    <i class="fas fa-file-pdf"></i> 
                                    ${invoice.file_path.split('/').pop()}
                                </div>
                                <a href="${fileUrl}" 
                                target="_blank" 
                                class="btn-action btn-pdf"
                                style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; background: #dc3545; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                                    <i class="fas fa-eye"></i> Ouvrir PDF
                                </a>
                            </div>
                            <div class="file-meta">
                                <span><i class="fas fa-file-pdf"></i> PDF</span>
                                <span><i class="fas fa-tag"></i> ID: ${invoice.id}</span>
                            </div>
                        `;
                    } else {
                        html = `
                            <i class="fas fa-file" style="font-size: 80px; color: var(--accent-color); margin-bottom: 20px;"></i>
                            <div class="file-name" style="font-size: 14px; color: #4a5a72;">
                                ${invoice.file_path.split('/').pop()}
                            </div>
                            <div class="file-meta">
                                <span><i class="fas fa-file"></i> Document</span>
                                <span><i class="fas fa-tag"></i> ID: ${invoice.id}</span>
                            </div>
                        `;
                    }
                    
                    // Add download button
                    html += `
                        <div class="preview-actions" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <a href="page1.php" class="btn-action btn-back" style="padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; background: var(--bg-light); color: var(--text-color); border: 1px solid var(--border-color);">
                                <i class="fas fa-arrow-left"></i> Nouveau document
                            </a>
                            <a href="${fileUrl}" 
                            download 
                            class="btn-action btn-download" 
                            style="padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; background: var(--accent-color); color: white;">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </div>
                    `;
                    
                    previewContainer.innerHTML = html;
                    
                } else {
                    previewContainer.innerHTML = `
                        <i class="fas fa-file-invoice" style="font-size: 80px; color: var(--accent-color); margin-bottom: 20px;"></i>
                        <div style="font-size: 16px; color: #4a5a72;">Document #${invoiceId}</div>
                        <div style="font-size: 13px; color: #718096; margin-top: 10px;">En cours de traitement...</div>
                        <div class="preview-actions" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <a href="page1.php" class="btn-action btn-back" style="padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; background: var(--bg-light); color: var(--text-color); border: 1px solid var(--border-color);">
                                <i class="fas fa-arrow-left"></i> Nouveau document
                            </a>
                        </div>
                    `;
                }
                
            } catch (error) {
                console.error('Error loading document preview:', error);
                previewContainer.innerHTML = `
                    <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: #dc3545; margin-bottom: 15px;"></i>
                    <div style="font-size: 14px; color: #718096;">Impossible de charger l'aperçu</div>
                    <div style="font-size: 12px; color: #a0aec0; margin-top: 5px;">${error.message}</div>
                    <div class="preview-actions" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                        <a href="page1.php" class="btn-action btn-back" style="padding: 10px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; background: var(--bg-light); color: var(--text-color); border: 1px solid var(--border-color);">
                            <i class="fas fa-arrow-left"></i> Nouveau document
                        </a>
                    </div>
                `;
            }
        }

        // Load invoice data when page loads
        loadInvoice();
    </script>
</body>
</html>