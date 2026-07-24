<?php
include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- ============================================ -->
    <!-- MÉTADONNÉES ET TITRE DE LA PAGE              -->
    <!-- ============================================ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des factures</title>

    <!-- ============================================ -->
    <!-- CHARGEMENT DE FONT AWESOME (ICÔNES)          -->
    <!-- ============================================ -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ============================================ */
        /* STYLES GÉNÉRAUX : RESET ET MISE EN PAGE      */
        /* ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Centrage de la page avec fond gris clair */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f0f2f5;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 2rem;
            margin-left: 60px;
        }

        /* ============================================ */
        /* CONTENEUR PRINCIPAL : CARTE BLANCHE          */
        /* ============================================ */
        .container {
            width: 100%;
            max-width: 1500px;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 60px -12px rgba(0, 0, 0, 0.15);
            padding: 2rem 2rem 2.5rem;
        }

        /* ============================================ */
        /* EN-TÊTE : TITRE + BOUTONS D'ACTION           */
        /* ============================================ */
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

        /* Badge "Lecture seule" */
        .header h1 span {
            background: #f0f2f8;
            padding: 0.2rem 0.8rem;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #5e6f85;
            margin-left: 0.5rem;
        }

        /* Groupe des boutons d'action */
        .header-actions {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ============================================ */
        /* BOUTON RAFRAÎCHIR                            */
        /* ============================================ */
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
            background: #004a6b;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 48, 73, 0.35);
        }

        .btn-refresh:active {
            transform: scale(0.96);
        }

        .btn-refresh i {
            font-size: 1.1rem;
        }

        /* ============================================ */
        /* BOUTON EXPORT EXCEL                          */
        /* ============================================ */
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

        /* ============================================ */
        /* BOUTON IMPRIMER                              */
        /* ============================================ */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1.8rem;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 60px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(44, 62, 80, 0.25);
            letter-spacing: 0.3px;
        }

        .btn-print:hover {
            background: #1a2a3a;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(44, 62, 80, 0.35);
        }

        .btn-print:active {
            transform: scale(0.96);
        }

        .btn-print i {
            font-size: 1.1rem;
        }

        /* ============================================ */
        /* ZONE DU TABLEAU AVEC SCROLL HORIZONTAL       */
        /* ============================================ */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 1.2rem;
            border: 1px solid #eef0f5;
            background: #fafbfc;
        }

        /* ============================================ */
        /* STYLES DU TABLEAU                            */
        /* ============================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 1100px;
        }

        /* ============================================ */
        /* EN-TÊTE DU TABLEAU (THEAD)                   */
        /* ============================================ */
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

        /* Icônes dans les en-têtes */
        thead th i {
            margin-right: 0.4rem;
            color: #6b7a8f;
            font-size: 0.7rem;
        }

        /* Coins arrondis pour l'en-tête */
        thead th:first-child {
            border-radius: 1.2rem 0 0 0;
        }
        thead th:last-child {
            border-radius: 0 1.2rem 0 0;
        }

        /* ============================================ */
        /* CORPS DU TABLEAU (TBODY)                     */
        /* ============================================ */
        tbody tr {
            border-bottom: 1px solid #eef0f5;
            transition: background 0.2s ease;
        }

        /* Effet au survol d'une ligne */
        tbody tr:hover {
            background: #f5f7fb;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 0.8rem 1.2rem;
            color: #1e2a36;
            vertical-align: middle;
        }

        /* ============================================ */
        /* CLASSES SPÉCIFIQUES POUR CERTAINES COLONNES  */
        /* ============================================ */
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
            font-size: 0.8rem;
            color: #5e6f85;
            white-space: nowrap;
        }

        .col-created i {
            margin-right: 0.3rem;
            color: #8a9bb0;
        }

        /* ============================================ */
        /* PIED DU TABLEAU                              */
        /* ============================================ */
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

        /* ============================================ */
        /* TOAST : NOTIFICATION TEMPORAIRE              */
        /* ============================================ */
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

        /* ============================================ */
        /* MESSAGE QUAND AUCUNE DONNÉE                  */
        /* ============================================ */
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

        /* ============================================ */
        /* LOADING SPINNER                              */
        /* ============================================ */
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

        /* ============================================ */
        /* RESPONSIVE : ADAPTATION AUX PETITS ÉCRANS    */
        /* ============================================ */
        @media (max-width: 640px) {
            .container {
                padding: 1.2rem;
            }
            .header h1 {
                font-size: 1.2rem;
            }
            .btn-excel, .btn-print, .btn-refresh {
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

    <!-- ============================================ -->
    <!-- CONTENEUR PRINCIPAL                          -->
    <!-- ============================================ -->
    <div class="container">

        <!-- ============================================ -->
        <!-- EN-TÊTE : TITRE + BOUTONS                    -->
        <!-- ============================================ -->
        <div class="header">
            <h1>
                <i class="fas fa-history"></i>
                Historique des factures
                <span><i class="far fa-clock"></i> Lecture seule</span>
            </h1>
            <div class="header-actions">
                <button class="btn-refresh" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i>
                    Rafraîchir
                </button>
                <button class="btn-print" id="printBtn">
                    <i class="fas fa-print"></i>
                    Imprimer
                </button>
                <button class="btn-excel" id="exportExcelBtn">
                    <i class="fas fa-file-excel"></i>
                    Excel
                </button>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ZONE DU TABLEAU                              -->
        <!-- ============================================ -->
        <div class="table-wrapper">
            <table id="dataTable">
                <thead>
                    <tr>
                        <!-- 9 colonnes avec icônes Font Awesome -->
                        <th><i class="fas fa-hashtag"></i>ID</th>
                        <th><i class="fas fa-building"></i>Fournisseur</th>
                        <th><i class="fas fa-barcode"></i>Référence</th>
                        <th><i class="fas fa-calendar-alt"></i>Date facture</th>
                        <th><i class="fas fa-tag"></i>Code TVA</th>
                        <th><i class="fas fa-euro-sign"></i>Montant</th>
                        <th><i class="fas fa-coins"></i>Devise</th>
                        <th><i class="fas fa-align-left"></i>Description</th>
                        <th><i class="fas fa-clock"></i>Date création</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="9">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner"></i>
                                Chargement des factures...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ============================================ -->
        <!-- PIED DU TABLEAU                              -->
        <!-- ============================================ -->
        <div class="table-footer">
            <span class="count">
                <i class="fas fa-database"></i>
                Total : <span id="rowCount">0</span> facture(s)
            </span>
            <span>
                <i class="fas fa-eye"></i>
                Consultation uniquement
            </span>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- TOAST : NOTIFICATION TEMPORAIRE              -->
    <!-- ============================================ -->
    <div class="toast" id="toast"></div>

    <!-- ============================================ -->
    <!-- JAVASCRIPT : LOGIQUE DE L'APPLICATION        -->
    <!-- ============================================ -->
    <script>
        (function() {
            // ============================================ //
            // DONNÉES : Liste vide - chargée depuis Laravel //
            // ============================================ //
            let data = [];

            // ============================================ //
            // RÉFÉRENCES VERS LES ÉLÉMENTS DOM            //
            // ============================================ //
            const tbody = document.getElementById('tableBody');
            const rowCount = document.getElementById('rowCount');
            const toast = document.getElementById('toast');

            // ============================================ //
            // FONCTION : FORMATER UNE DATE COMPLÈTE       //
            // ============================================ //
            function formatDateTime(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const seconds = String(date.getSeconds()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
            }

            // ============================================ //
            // FONCTION : AFFICHER UNE NOTIFICATION (TOAST)//
            // ============================================ //
            function showToast(message, duration = 2500) {
                toast.textContent = message;
                toast.classList.add('show');
                clearTimeout(toast._timeout);
                toast._timeout = setTimeout(() => {
                    toast.classList.remove('show');
                }, duration);
            }

            // ============================================ //
            // FONCTION : CHARGER LES FACTURES DEPUIS API  //
            // ============================================ //
            async function loadInvoices() {
                try {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="loading-spinner">
                                    <i class="fas fa-spinner"></i>
                                    Chargement des factures...
                                </div>
                            </td>
                        </tr>
                    `;

                    const response = await fetch("http://127.0.0.1:8000/api/invoices");

                    if (!response.ok) {
                        throw new Error("Erreur lors du chargement");
                    }

                    data = await response.json();
                    renderTable();
                    showToast(`✅ ${data.length} factures chargées`, 2000);

                } catch (error) {
                    console.error('Error loading invoices:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="no-data">
                                    <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>
                                    Impossible de charger les factures<br>
                                    <small style="color: #6b7a8f;">Vérifiez que le backend Laravel est en cours d'exécution.</small>
                                </div>
                            </td>
                        </tr>
                    `;
                    showToast('❌ Erreur de chargement', 3000);
                }
            }

            // ============================================ //
            // FONCTION : GENERER LE TABLEAU               //
            // ============================================ //
            function renderTable() {
                // Si aucune donnée, afficher un message
                if (data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    Aucune facture dans l'historique
                                </div>
                            </td>
                        </tr>
                    `;
                    rowCount.textContent = '0';
                    return;
                }

                // Construction du HTML des lignes
                let html = '';
                data.forEach((item) => {
                    html += `
                        <tr>
                            <td class="col-id">#${item.id}</td>
                            <td><strong>${item.vendor ?? "-"}</strong></td>
                            <td>${item.reference ?? "-"}</td>
                            <td class="col-date">${item.invoice_date ?? "-"}</td>
                            <td>${item.tax_code ?? "A0"}</td>
                            <td class="col-amount">${item.amount ?? "-"}</td>
                            <td>${item.currency ?? "-"}</td>
                            <td>${item.text ?? "-"}</td>
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

            // ============================================ //
            // FONCTION : EXPORTER VERS EXCEL (CSV)        //
            // ============================================ //
            function exportToExcel() {
                if (data.length === 0) {
                    showToast('Aucune donnée à exporter', 2000);
                    return;
                }

                // Ajout du BOM UTF-8 pour les accents
                let csvContent = '\uFEFF';
                const headers = [
                    'ID',
                    'Fournisseur',
                    'Référence',
                    'Date facture',
                    'Code TVA',
                    'Montant',
                    'Devise',
                    'Description',
                    'Date de création'
                ];
                csvContent += headers.join(';') + '\n';

                // Parcours des données pour construire le CSV
                data.forEach(item => {
                    const row = [
                        item.id,
                        item.vendor || '',
                        item.reference || '',
                        item.invoice_date || '',
                        item.tax_code || 'A0',
                        item.amount || '',
                        item.currency || '',
                        item.text || '',
                        formatDateTime(item.created_at)
                    ];
                    csvContent += row.join(';') + '\n';
                });

                // Création et téléchargement du fichier
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'historique_factures.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                showToast('✅ Export Excel terminé', 2000);
            }

            // ============================================ //
            // FONCTION : IMPRIMER LA PAGE                 //
            // ============================================ //
            function printTable() {
                window.print();
            }

            // ============================================ //
            // INITIALISATION : CHARGER LES DONNÉES        //
            // ============================================ //
            loadInvoices();

            // ============================================ //
            // ASSOCIATION DES ÉVÉNEMENTS AUX BOUTONS      //
            // ============================================ //
            document.getElementById('exportExcelBtn').addEventListener('click', exportToExcel);
            document.getElementById('printBtn').addEventListener('click', printTable);
            
            document.getElementById('refreshBtn').addEventListener('click', function() {
                const icon = this.querySelector('i');
                icon.classList.add('fa-spin');
                loadInvoices().finally(() => {
                    icon.classList.remove('fa-spin');
                });
            });

            // Auto-refresh toutes les 30 secondes
            setInterval(() => {
                loadInvoices();
            }, 30000);

            // Message dans la console pour le développeur
            console.log(' Historique chargé depuis Laravel API');
            console.log(' Mode lecture seule - aucune modification possible');

        })();
    </script>

</body>
</html>