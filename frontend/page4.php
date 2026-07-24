<?php
    // Get the invoice ID from the URL FIRST (before any output)
    $invoiceId = $_GET['id'] ?? null;
    
    // If no ID is provided, redirect to page1
    // This MUST happen before any HTML output
    if (!$invoiceId) {
        header("Location: page1.php");
        exit();
    }
    
    // NOW include the sidebar (after the redirect check)
    include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails facture · SupScan</title>
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
            letter-spacing: 0.3px;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(108, 117, 125, 0.35);
        }

        .btn-back:active {
            transform: scale(0.96);
        }

        .btn-back i {
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

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 0.3rem 0.8rem;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .btn-delete i {
            font-size: 0.7rem;
        }

        .btn-view {
            background: #003049;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 0.3rem 0.8rem;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-right: 0.3rem;
        }

        .btn-view:hover {
            background: #004a6e;
            transform: scale(1.05);
        }

        .btn-view i {
            font-size: 0.7rem;
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

        .single-row {
            background: #f8f9fa;
        }

        .single-row td {
            padding: 0.8rem 1.2rem;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.2rem;
            }
            .header h1 {
                font-size: 1.2rem;
            }
            .btn-excel, .btn-refresh, .btn-back {
                padding: 0.5rem 1.2rem;
                font-size: 0.85rem;
            }
            thead th,
            tbody td {
                padding: 0.5rem 0.8rem;
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
                Facture
                
            </h1>
            <div class="header-actions">
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

        <div class="table-wrapper">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i>ID</th>
                        <th><i class="fas fa-building"></i>Fournisseur</th>
                        <th><i class="fas fa-barcode"></i>Référence</th>
                        <th><i class="fas fa-calendar-alt"></i>Date facture</th>
                        <th><i class="fas fa-tag"></i>Code TVA</th>
                        <th><i class="fas fa-euro-sign"></i>Montant</th>
                        <th><i class="fas fa-coins"></i>Devise</th>
                        <th><i class="fas fa-align-left"></i>Description</th>
                        <th><i class="fas fa-clock"></i>Créé le</th>
                        <th><i class="fas fa-cog"></i>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="10" class="loading-spinner">
                            <i class="fas fa-spinner"></i>
                            <p>Chargement de la facture #<?= htmlspecialchars($invoiceId) ?>...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span class="count">
                <i class="fas fa-database"></i>
                Facture : <span id="rowCount">0</span>
            </span>
            <span>
                <i class="fas fa-info-circle"></i>
                Affichage de la facture #<?= htmlspecialchars($invoiceId) ?>
            </span>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        (function() {
            let data = [];
            const tbody = document.getElementById('tableBody');
            const rowCount = document.getElementById('rowCount');
            const toast = document.getElementById('toast');

            // Get the invoice ID from PHP
            const invoiceId = <?= json_encode($invoiceId) ?>;

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
                    return `${day}/${month}/${year} ${hours}:${minutes}`;
                } catch (e) {
                    return dateString;
                }
            }

            async function loadInvoice() {
                try {
                    showToast('Chargement de la facture...', 1500);
                    
                    // Fetch the specific invoice by ID
                    const response = await fetch(`http://127.0.0.1:8000/api/invoices/${invoiceId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        if (response.status === 404) {
                            throw new Error('Facture non trouvée');
                        }
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    // Get the single invoice object
                    const invoice = await response.json();
                    
                    // Check if we got a valid invoice
                    if (invoice && invoice.id) {
                        // Wrap in array so renderTable() still works
                        data = [invoice];
                        renderTable();
                        showToast(`✅ Facture #${invoice.id} chargée`, 2000);
                    } else {
                        // No invoice found
                        data = [];
                        renderTable();
                        showToast('ℹ️ Facture non trouvée', 2000);
                    }

                } catch (error) {
                    console.error('Error loading invoice:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: #dc3545;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                                Erreur lors du chargement
                                <br><small style="color: #6b7a8f;">${error.message}</small>
                                <br><br>
                                <button onclick="location.reload()" style="padding: 10px 20px; background: #780000; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                    <i class="fas fa-sync-alt"></i> Réessayer
                                </button>
                            </td>
                        </tr>
                    `;
                    rowCount.textContent = '0';
                }
            }

            function renderTable() {
                if (!data || data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 60px 20px; color: #6b7a8f;">
                                <i class="fas fa-inbox" style="font-size: 60px; display: block; margin-bottom: 15px; opacity: 0.3; color: #780000;"></i>
                                Aucune facture trouvée
                                <br><small style="display: block; margin-top: 10px;">La facture #${invoiceId} n'existe pas</small>
                            </td>
                        </tr>
                    `;
                    rowCount.textContent = '0';
                    return;
                }

                // Only display the first row (index 0)
                const item = data[0];
                
                let html = `
                    <tr class="single-row">
                        <td class="col-id">#${item.id}</td>
                        <td>
                            <strong>${item.vendor ?? "-"}</strong>
                        </td>
                        <td>
                            ${item.reference ?? "-"}
                        </td>
                        <td class="col-date">
                            ${item.invoice_date ?? "-"}
                        </td>
                        <td>
                            ${item.tax_code ?? "A0"}
                        </td>
                        <td class="col-amount">
                            ${item.amount ?? "-"}
                        </td>
                        <td>
                            ${item.currency ?? "-"}
                        </td>
                        <td>
                            ${item.text ?? "-"}
                        </td>
                        <td class="col-created">
                            <i class="far fa-calendar-alt"></i>
                            ${formatDateTime(item.created_at)}
                        </td>
                        <td>
                            <button class="btn-view" onclick="window.location.href='page3.php?id=${item.id}'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-delete" data-id="${item.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                
                tbody.innerHTML = html;
                rowCount.textContent = `#${item.id}`;

                // Delete button
                document.querySelector('.btn-delete')?.addEventListener('click', function() {
                    const id = parseInt(this.dataset.id);
                    deleteInvoice(id);
                });
            }

            async function deleteInvoice(id) {
                if (!confirm(`Êtes-vous sûr de vouloir supprimer la facture #${id} ?`)) {
                    return;
                }

                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/invoices/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete invoice');
                    }

                    showToast(`✅ Facture #${id} supprimée`, 2000);
                    // Redirect back to page1 after deletion
                    setTimeout(() => {
                        window.location.href = 'page1.php';
                    }, 1500);

                } catch (error) {
                    console.error('Error deleting invoice:', error);
                    showToast('❌ Erreur lors de la suppression', 2000);
                }
            }

            function exportToExcel() {
                if (!data || data.length === 0) {
                    showToast('❌ Aucune donnée à exporter', 2000);
                    return;
                }

                let csvContent = '\uFEFF';
                const headers = [
                    "ID",
                    "Fournisseur",
                    "Référence",
                    "Date facture",
                    "Code TVA",
                    "Montant",
                    "Devise",
                    "Description",
                    "Créé le"
                ];
                csvContent += headers.join(';') + '\n';

                // Only export the first row
                const item = data[0];
                const row = [
                    item.id,
                    item.vendor ?? '',
                    item.reference ?? '',
                    item.invoice_date ?? '',
                    item.tax_code ?? 'A0',
                    item.amount ?? '',
                    item.currency ?? '',
                    item.text ?? '',
                    formatDateTime(item.created_at)
                ];
                csvContent += row.join(';') + '\n';

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `facture_${data[0]?.id ?? 'latest'}_${new Date().toISOString().slice(0,10)}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('✅ Export Excel terminé', 2000);
            }

            // Load invoice on page load
            loadInvoice();

            // Event listeners
            document.getElementById('refreshBtn').addEventListener('click', loadInvoice);
            document.getElementById('exportExcelBtn').addEventListener('click', exportToExcel);

            console.log(`📊 Chargement de la facture #${invoiceId}`);
        })();
    </script>

</body>
</html>