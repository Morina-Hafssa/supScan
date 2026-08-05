<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Statistiques - SupScan</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
      /* ... (all your existing styles remain the same) ... */
      :root {
        --main-color: #780000;
        --secondary-color: #003049;
        --accent-color: #4ca380;
        --white: #ffffff;
        --bg: #f0f2f5;
        --card-shadow: 0 10px 30px -8px rgba(0, 0, 0, 0.1);

        --c-pink-1: #d6336c;
        --c-pink-2: #ad1457;
        --c-purple-1: #8e5fd9;
        --c-purple-2: #5b3a99;
        --c-blue-1: #2b6fd1;
        --c-blue-2: #003049;
        --c-orange-1: #f6a24a;
        --c-orange-2: #e2711d;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        background: var(--bg);
      }

      .main-content {
        padding: 30px 35px 60px;
        max-width: 1500px;
        margin: 0 auto;
      }

      .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.8rem;
        flex-wrap: wrap;
        gap: 1rem;
      }

      .header-titles h1 {
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--secondary-color);
      }

      .header-titles p {
        color: #7c8a9c;
        font-size: 0.92rem;
        margin-top: 0.3rem;
      }

      .header-actions {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        flex-wrap: wrap;
      }

      .btn-refresh {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.65rem 1.4rem;
        background: var(--secondary-color);
        color: white;
        border: none;
        border-radius: 60px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: var(--card-shadow);
      }
      .btn-refresh:hover {
        background: #004a6e;
        transform: translateY(-2px);
      }
      .btn-refresh:active {
        transform: scale(0.96);
      }
      .btn-refresh i.spinning {
        animation: spin 0.6s linear;
      }
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }

      .date-filter {
        background: var(--white);
        border-radius: 1.2rem;
        box-shadow: var(--card-shadow);
        padding: 1.1rem 1.6rem;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        flex-wrap: wrap;
      }

      .date-filter label {
        font-weight: 600;
        color: var(--secondary-color);
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .date-filter select {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        background: #f8f9fa;
        cursor: pointer;
      }

      .date-filter select:focus {
        border-color: var(--accent-color);
        outline: none;
      }

      .date-filter input[type="date"] {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        background: #f8f9fa;
        cursor: pointer;
      }

      .date-filter input[type="date"]:focus {
        border-color: var(--accent-color);
        outline: none;
      }

      .date-filter .filter-actions {
        display: flex;
        gap: 0.6rem;
        margin-left: auto;
      }

      .date-filter .filter-actions button {
        padding: 0.5rem 1.2rem;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
      }

      .btn-apply {
        background: var(--secondary-color);
        color: white;
      }
      .btn-apply:hover {
        background: #004a6e;
      }

      .btn-reset {
        background: #e2e8f0;
        color: #5e6f85;
      }
      .btn-reset:hover {
        background: #d0d7e0;
      }

      .date-filter .preset-buttons {
        display: flex;
        gap: 0.4rem;
      }

      .date-filter .preset-buttons button {
        padding: 0.4rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: transparent;
        font-size: 0.8rem;
        font-weight: 600;
        color: #5e6f85;
        cursor: pointer;
        transition: all 0.15s ease;
      }

      .date-filter .preset-buttons button:hover {
        border-color: var(--secondary-color);
        color: var(--secondary-color);
      }

      .date-filter .preset-buttons button.active {
        background: var(--secondary-color);
        color: white;
        border-color: var(--secondary-color);
      }

      .selector-box {
        background: var(--white);
        border-radius: 1.2rem;
        box-shadow: var(--card-shadow);
        padding: 1.1rem 1.6rem;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        flex-wrap: wrap;
      }

      .selector-box label {
        font-weight: 600;
        color: var(--secondary-color);
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .selector-box select {
        padding: 0.6rem 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #2d3748;
        background: #f8f9fa;
        cursor: pointer;
        min-width: 210px;
      }
      .selector-box select:focus {
        border-color: var(--accent-color);
        outline: none;
      }

      .selector-box .info-badge {
        margin-left: auto;
        background: #f0f2f8;
        padding: 0.4rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        color: #5e6f85;
      }

      .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.4rem;
        margin-bottom: 1.8rem;
      }

      .stat-card {
        border-radius: 1.3rem;
        padding: 1.5rem 1.6rem;
        color: var(--white);
        position: relative;
        overflow: hidden;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }

      .stat-card .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        opacity: 0.9;
      }

      .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-top: 0.6rem;
      }

      .stat-card i.bg-icon {
        position: absolute;
        right: 1rem;
        bottom: 1rem;
        font-size: 2.6rem;
        opacity: 0.25;
      }

      .stat-card.pink { background: linear-gradient(135deg, var(--c-pink-1), var(--c-pink-2)); }
      .stat-card.purple { background: linear-gradient(135deg, var(--c-purple-1), var(--c-purple-2)); }
      .stat-card.blue { background: linear-gradient(135deg, var(--c-blue-1), var(--c-blue-2)); }
      .stat-card.orange { background: linear-gradient(135deg, var(--c-orange-1), var(--c-orange-2)); }

      .charts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.6rem;
        margin-bottom: 1.8rem;
      }

      .chart-card {
        background: var(--white);
        border-radius: 1.3rem;
        box-shadow: var(--card-shadow);
        padding: 1.6rem;
        min-height: 380px;
        display: flex;
        flex-direction: column;
      }

      .chart-card h3 {
        font-size: 0.98rem;
        color: var(--secondary-color);
        margin-bottom: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .chart-card h3 i {
        color: var(--accent-color);
      }

      .chart-canvas-wrapper {
        flex: 1;
        position: relative;
      }

      .chart-card.full-width {
        grid-column: 1 / -1;
        min-height: 320px;
      }

      .chart-card-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-bottom: 1.1rem;
      }

      .chart-card-header-row h3 {
        margin-bottom: 0;
      }

      .no-data-msg {
        text-align: center;
        padding: 3rem 1rem;
        color: #8a9bb0;
        margin: auto;
      }

      .no-data-msg i {
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
        color: #d0d7e0;
      }

      .quarter-dropdown {
        position: relative;
      }

      .quarter-dropdown-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: #f8f9fa;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4a5a72;
        cursor: pointer;
        transition: all 0.15s ease;
      }

      .quarter-dropdown-btn:hover {
        border-color: var(--accent-color);
      }

      .quarter-dropdown-panel {
        display: none;
        position: absolute;
        right: 0;
        top: 115%;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        padding: 0.8rem;
        min-width: 220px;
        max-height: 280px;
        overflow-y: auto;
        z-index: 20;
      }

      .quarter-dropdown-panel.open {
        display: block;
      }

      .quarter-dropdown-actions {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.6rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid #eef0f5;
      }

      .quarter-dropdown-actions button {
        flex: 1;
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        border: none;
        border-radius: 6px;
        background: #f0f2f8;
        color: #5e6f85;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.15s ease;
      }

      .quarter-dropdown-actions button:hover {
        background: #e2e8f0;
      }

      .quarter-checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.2rem;
        font-size: 0.85rem;
        color: #2d3748;
        cursor: pointer;
      }

      .quarter-checkbox-item input {
        accent-color: var(--accent-color);
        cursor: pointer;
      }

      .quarter-empty-msg {
        font-size: 0.8rem;
        color: #a0aec0;
        padding: 0.4rem 0.2rem;
      }

      @media (max-width: 1100px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid { grid-template-columns: 1fr 1fr; }
        .chart-card.full-width { grid-column: 1 / -1; }
        .date-filter .filter-actions {
          margin-left: 0;
          width: 100%;
        }
      }

      @media (max-width: 700px) {
        .stats-grid { grid-template-columns: 1fr; }
        .charts-grid { grid-template-columns: 1fr; }
        .main-content { padding: 20px; }
        .date-filter {
          flex-direction: column;
          align-items: stretch;
        }
        .date-filter .preset-buttons {
          flex-wrap: wrap;
        }
      }
    </style>
  </head>
  <body>
    <?php
    include("Sidebar.php");
    ?>
    <div class="main-content">
      <div class="header">
        <div class="header-titles">
          <h1><i class="fas fa-chart-pie"></i> Statistiques des factures</h1>
        </div>
        <div class="header-actions">
          <button class="btn-refresh" id="refreshBtn">
            <i class="fas fa-sync-alt"></i>
            Actualiser
          </button>
        </div>
      </div>

      <!-- Date Filter -->
      <div class="date-filter" id="dateFilter">
        <label><i class="fas fa-calendar-alt"></i> Base sur :</label>
        <select id="dateTypeSelect">
          <option value="invoice_date">Date de facture</option>
          <option value="created_at">Date de creation</option>
        </select>

        <label>Periode :</label>
        <div class="preset-buttons" id="presetButtons">
          <button data-preset="today">Aujourd'hui</button>
          <button data-preset="7">7 jours</button>
          <button data-preset="30">30 jours</button>
          <button data-preset="90">90 jours</button>
          <button data-preset="all" class="active">Tout</button>
        </div>
        <label>Du :</label>
        <input type="date" id="dateFrom" />
        <label>Au :</label>
        <input type="date" id="dateTo" />
        <div class="filter-actions">
          <button class="btn-apply" id="applyDateFilter"><i class="fas fa-check"></i> Appliquer</button>
          <button class="btn-reset" id="resetDateFilter"><i class="fas fa-undo"></i> Reinitialiser</button>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="stats-grid" id="statsGrid">
        <div class="stat-card pink">
          <span class="stat-label">Total factures</span>
          <span class="stat-value" id="statTotal">0</span>
          <i class="fas fa-file-invoice bg-icon"></i>
        </div>
        <div class="stat-card purple">
          <span class="stat-label">Montant total</span>
          <span class="stat-value" id="statAmountTotal">0</span>
          <i class="fas fa-sack-dollar bg-icon"></i>
        </div>
        <div class="stat-card blue">
          <span class="stat-label">Montant moyen</span>
          <span class="stat-value" id="statAmountAvg">0</span>
          <i class="fas fa-chart-line bg-icon"></i>
        </div>
        <div class="stat-card orange">
          <span class="stat-label">Fournisseurs uniques</span>
          <span class="stat-value" id="statVendors">0</span>
          <i class="fas fa-building bg-icon"></i>
        </div>
      </div>

      <!-- Field filter for bar/pie -->
      <div class="selector-box">
        <label for="fieldSelect"><i class="fas fa-filter"></i> Analyser par :</label>
        <select id="fieldSelect">
          <option value="vendor">Fournisseur</option>
          <option value="currency">Devise</option>
          <option value="tax_code">Code TVA</option>
          <option value="month">Mois de facturation</option>
        </select>
        <span class="info-badge" id="totalBadge"><i class="fas fa-database"></i> 0 facture(s)</span>
      </div>

      <div class="charts-grid" id="chartsGrid">
        <div class="chart-card">
          <h3><i class="fas fa-chart-pie"></i> Repartition par devise</h3>
          <div class="chart-canvas-wrapper"><canvas id="donutChart"></canvas></div>
        </div>
        <div class="chart-card">
          <h3><i class="fas fa-chart-bar"></i> Top 5 fournisseurs (EUR)</h3>
          <div class="chart-canvas-wrapper"><canvas id="barChart"></canvas></div>
        </div>
        <div class="chart-card">
          <h3><i class="fas fa-chart-pie"></i> Repartition dynamique</h3>
          <div class="chart-canvas-wrapper"><canvas id="pieChart"></canvas></div>
        </div>
      </div>

      <div class="chart-card full-width">
        <div class="chart-card-header-row">
          <h3><i class="fas fa-chart-line"></i> Evolution trimestrielle</h3>
          <div class="quarter-dropdown" id="quarterDropdown">
            <button type="button" class="quarter-dropdown-btn" id="quarterDropdownBtn">
              <i class="fas fa-calendar-check"></i> Trimestres <i class="fas fa-chevron-down"></i>
            </button>
            <div class="quarter-dropdown-panel" id="quarterDropdownPanel">
              <div class="quarter-dropdown-actions">
                <button type="button" id="selectAllQuarters">Tout selectionner</button>
                <button type="button" id="selectNoneQuarters">Tout deselectionner</button>
              </div>
              <div id="quarterCheckboxList"></div>
            </div>
          </div>
        </div>
        <div class="chart-canvas-wrapper"><canvas id="lineChart"></canvas></div>
      </div>
    </div>

    <script>
  (function () {
    
    // ============================================================
    // CHECK IF RUNNING IN ELECTRON
    // ============================================================
    function isElectron() {
      return typeof window !== 'undefined' && window.electronAPI !== undefined;
    }

    // ============================================================
    // DATABASE FUNCTIONS USING SQLITE VIA ELECTRON IPC
    // ============================================================

    async function getInvoiceData() {
      try {
        if (isElectron()) {
          // Get sessions from SQLite
          const sessions = await window.electronAPI.db.getAllSessions();
          let combined = [];

          for (const session of sessions) {
            if (session.data) {
              try {
                const sessionData = typeof session.data === 'string' ? JSON.parse(session.data) : session.data;
                if (sessionData.invoices && Array.isArray(sessionData.invoices)) {
                  sessionData.invoices.forEach((inv) => {
                    combined.push({ 
                      ...inv, 
                      _sessionCreatedAt: session.created_at || null 
                    });
                  });
                }
              } catch (e) {
                // Skip invalid session data
              }
            }
          }

          // Get current invoices
          try {
            const currentInvoices = await window.electronAPI.db.getCurrentInvoices();
            if (currentInvoices && currentInvoices.length > 0) {
              currentInvoices.forEach((row) => {
                // Try to parse JSON data if available
                let invoice;
                if (row.data) {
                  try {
                    invoice = JSON.parse(row.data);
                  } catch (e) {
                    // Fall back to column data
                    invoice = {
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
                      preview: row.preview,
                      fileType: row.file_type,
                      fileName: row.file_name
                    };
                  }
                } else {
                  invoice = {
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
                    preview: row.preview,
                    fileType: row.file_type,
                    fileName: row.file_name
                  };
                }
                combined.push({ 
                  ...invoice, 
                  _sessionCreatedAt: null 
                });
              });
            }
          } catch (e) {
            console.warn('Impossible de lire currentInvoices:', e);
          }

          // Deduplicate
          const seen = new Map();
          combined.forEach((item) => {
            const key = [
              item.reference || '',
              item.vendor || '',
              item.invoice_date || '',
              item.amount || ''
            ].join('|');

            const existing = seen.get(key);
            if (!existing) {
              seen.set(key, item);
              return;
            }

            const existingDate = item.created_at || item._sessionCreatedAt || null;
            const candidateDate = item.created_at || item._sessionCreatedAt || null;
            // Keep the one with the latest date
            if (existingDate && candidateDate && new Date(candidateDate) > new Date(existingDate)) {
              seen.set(key, item);
            }
          });

          return Array.from(seen.values()).map((item) => {
            const fixed = { ...item };
            if (!fixed.created_at && fixed._sessionCreatedAt) {
              fixed.created_at = fixed._sessionCreatedAt;
            }
            delete fixed._sessionCreatedAt;
            return fixed;
          });
        } else {
          // Fallback to localStorage
          return getInvoicesFromLocalStorage();
        }
      } catch (error) {
        console.error('Error getting invoice data:', error);
        return [];
      }
    }

    function getInvoicesFromLocalStorage() {
      let sessions = [];
      try {
        sessions = JSON.parse(localStorage.getItem('invoiceSessions')) || [];
      } catch (e) {
        console.warn('Impossible de lire invoiceSessions:', e);
      }

      let combined = [];

      sessions.forEach((session) => {
        if (Array.isArray(session.invoices)) {
          session.invoices.forEach((inv) => {
            combined.push({ ...inv, _sessionCreatedAt: session.created_at || null });
          });
        }
      });

      try {
        const currentBatch = JSON.parse(localStorage.getItem('currentInvoices')) || [];
        currentBatch.forEach((inv) => combined.push({ ...inv, _sessionCreatedAt: null }));
      } catch (e) {
        console.warn('Impossible de lire currentInvoices:', e);
      }

      try {
        const legacy = JSON.parse(localStorage.getItem('invoices')) || [];
        legacy.forEach((inv) => combined.push({ ...inv, _sessionCreatedAt: null }));
      } catch (e) {
        // rien
      }

      function effectiveDate(item) {
        return item.created_at || item._sessionCreatedAt || null;
      }

      const seen = new Map();
      combined.forEach((item) => {
        const key = [
          item.reference || '',
          item.vendor || '',
          item.invoice_date || '',
          item.amount || ''
        ].join('|');

        const existing = seen.get(key);
        if (!existing) {
          seen.set(key, item);
          return;
        }

        const existingDate = effectiveDate(existing);
        const candidateDate = effectiveDate(item);

        if (!existingDate && candidateDate) {
          seen.set(key, item);
        } else if (existingDate && candidateDate && new Date(candidateDate) > new Date(existingDate)) {
          seen.set(key, item);
        }
      });

      return Array.from(seen.values()).map((item) => {
        const fixed = { ...item };
        if (!fixed.created_at && fixed._sessionCreatedAt) {
          fixed.created_at = fixed._sessionCreatedAt;
        }
        delete fixed._sessionCreatedAt;
        return fixed;
      });
    }

    const fieldSelect = document.getElementById('fieldSelect');
    const totalBadge = document.getElementById('totalBadge');
    const chartsGrid = document.getElementById('chartsGrid');
    const refreshBtn = document.getElementById('refreshBtn');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const dateTypeSelect = document.getElementById('dateTypeSelect');
    const presetButtons = document.getElementById('presetButtons');
    const applyBtn = document.getElementById('applyDateFilter');
    const resetBtn = document.getElementById('resetDateFilter');

    const quarterDropdownBtn = document.getElementById('quarterDropdownBtn');
    const quarterDropdownPanel = document.getElementById('quarterDropdownPanel');
    const quarterCheckboxList = document.getElementById('quarterCheckboxList');
    const selectAllQuartersBtn = document.getElementById('selectAllQuarters');
    const selectNoneQuartersBtn = document.getElementById('selectNoneQuarters');

    let donutChartInstance = null;
    let barChartInstance = null;
    let pieChartInstance = null;
    let lineChartInstance = null;

    let dateFilter = { from: null, to: null, type: 'invoice_date' };

    let selectedQuarters = null;
    let previousQuarterKeys = new Set();

    const palette = [
      '#780000', '#003049', '#4ca380', '#e63946', '#f4a261',
      '#2a9d8f', '#264653', '#e9c46a', '#8ecae6', '#a26769'
    ];

    // CURRENCY CONVERSION RATES
    let EXCHANGE_RATES = {
      'EUR': 1.0, 'USD': 0.92, 'GBP': 1.17, 'MAD': 0.092, 'DZD': 0.0069,
      'TND': 0.30, 'EGP': 0.019, 'SAR': 0.245, 'AED': 0.25, 'QAR': 0.253,
      'KWD': 3.00, 'BHD': 2.44, 'OMR': 2.39, 'JOD': 1.30, 'LBP': 0.00061,
      'XOF': 0.00152, 'XAF': 0.00152, 'CHF': 1.05, 'CAD': 0.68, 'AUD': 0.60,
      'JPY': 0.0062, 'CNY': 0.127, 'INR': 0.011, 'BRL': 0.17, 'RUB': 0.0095,
      'TRY': 0.028
    };
    const DEFAULT_RATE = 1.0;

    function toBAMDateStr(date) {
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, '0');
      const d = String(date.getDate()).padStart(2, '0');
      return y + '-' + m + '-' + d + 'T00:00:00.000Z';
    }

    async function loadExchangeRates() {
      const today = new Date();
      const todayStr = today.toISOString().slice(0, 10);

      try {
        const cached = JSON.parse(localStorage.getItem('exchangeRatesCache'));
        if (cached && cached.date === todayStr && cached.rates) {
          EXCHANGE_RATES = cached.rates;
          return;
        }
      } catch (e) {}

      try {
        const dateParam = toBAMDateStr(today);
        const url = 'https://api.centralbankofmorocco.ma/cours/Version1/api/CoursBBE?date=' + encodeURIComponent(dateParam);
        
        console.log('Fetching exchange rates from Bank Al-Maghrib:', url);
        
        const res = await fetch(url);
        
        if (!res.ok) {
          throw new Error('HTTP error! status: ' + res.status);
        }
        
        const data = await res.json();
        console.log('Bank Al-Maghrib API response:', data);

        if (data && Array.isArray(data)) {
          const rates = {};
          let madToEur = 1.0;
          
          data.forEach(item => {
            if (item.devise === 'EUR') {
              madToEur = 1 / parseFloat(item.cours);
            }
          });
          
          data.forEach(item => {
            const currencyCode = item.devise;
            const cours = parseFloat(item.cours);
            if (currencyCode && cours > 0) {
              const rate = cours * madToEur;
              rates[currencyCode] = rate;
            }
          });
          rates['EUR'] = 1.0;
          rates['MAD'] = madToEur;
          console.log('Converted rates:', rates);
          
          if (Object.keys(rates).length > 1) {
            EXCHANGE_RATES = rates;
            localStorage.setItem('exchangeRatesCache', JSON.stringify({ date: todayStr, rates: EXCHANGE_RATES }));
          }
        }
      } catch (e) {
        console.warn('Impossible de recuperer les taux de change:', e);
      }
    }

    function convertToEUR(amount, currency) {
      const numAmount = toNumber(amount);
      if (!numAmount) return 0;
      const rate = EXCHANGE_RATES[currency] || DEFAULT_RATE;
      return numAmount * rate;
    }

    function getInvoices() {
      return getInvoicesFromLocalStorage();
    }

    async function getInvoicesAsync() {
      return await getInvoiceData();
    }

    function parseDate(str, type) {
      if (!str) return null;

      if (type === 'invoice_date') {
        const isSlash = str.includes('/');
        const parts = isSlash ? str.split('/') : str.split('-');
        if (parts.length !== 3) return null;
        if (isSlash) {
          return new Date(+parts[2], +parts[1] - 1, +parts[0]);
        }
        return new Date(+parts[0], +parts[1] - 1, +parts[2]);
      }

      if (type === 'created_at') {
        const d = new Date(str);
        return isNaN(d.getTime()) ? null : d;
      }

      return null;
    }

    function getDateFromInvoice(item, type) {
      if (type === 'invoice_date') {
        return parseDate(item.invoice_date, type);
      } else if (type === 'created_at') {
        return parseDate(item.created_at, type);
      }
      return null;
    }

    function filterByDateRange(invoices, fromDate, toDate, dateType) {
      if (!fromDate && !toDate) return invoices;

      const from = fromDate ? new Date(fromDate + 'T00:00:00') : null;
      const to = toDate ? new Date(toDate + 'T23:59:59.999') : null;

      return invoices.filter((item) => {
        const d = getDateFromInvoice(item, dateType);
        if (!d) return false;
        if (from && d < from) return false;
        if (to && d > to) return false;
        return true;
      });
    }

    function extractValue(item, field) {
      if (field === 'month') {
        const d = parseDate(item.invoice_date, 'invoice_date');
        if (!d) return 'Inconnu';
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        return mm + '/' + d.getFullYear();
      }
      return item[field] || 'Non renseigne';
    }

    function computeFrequency(invoices, field) {
      const counts = {};
      invoices.forEach((item) => {
        const value = extractValue(item, field);
        counts[value] = (counts[value] || 0) + 1;
      });
      return counts;
    }

    function toNumber(montant) {
      if (typeof montant === 'number') return montant;
      if (!montant) return 0;
      const n = parseFloat(String(montant).replace(',', '.'));
      return isNaN(n) ? 0 : n;
    }

    function formatNumber(n, decimals = 2) {
      return n.toLocaleString('fr-FR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      });
    }

    function renderStats(invoices) {
      const total = invoices.length;

      let totalEUR = 0;
      invoices.forEach((item) => {
        totalEUR += convertToEUR(item.amount, item.currency);
      });

      const avgEUR = total ? totalEUR / total : 0;
      const vendors = new Set(invoices.map((i) => i.vendor || 'Non renseigne')).size;

      document.getElementById('statTotal').textContent = total;
      document.getElementById('statAmountTotal').textContent = formatNumber(totalEUR, 2) + ' €';
      document.getElementById('statAmountAvg').textContent = formatNumber(avgEUR, 2) + ' €';
      document.getElementById('statVendors').textContent = vendors;
    }

    function ensureChartsGridExists() {
      if (
        !document.getElementById('donutChart') ||
        !document.getElementById('barChart') ||
        !document.getElementById('pieChart')
      ) {
        chartsGrid.innerHTML = `
          <div class="chart-card">
            <h3><i class="fas fa-chart-pie"></i> Repartition par devise</h3>
            <div class="chart-canvas-wrapper"><canvas id="donutChart"></canvas></div>
          </div>
          <div class="chart-card">
            <h3><i class="fas fa-chart-bar"></i> Top 5 fournisseurs (EUR)</h3>
            <div class="chart-canvas-wrapper"><canvas id="barChart"></canvas></div>
          </div>
          <div class="chart-card">
            <h3><i class="fas fa-chart-pie"></i> Repartition dynamique</h3>
            <div class="chart-canvas-wrapper"><canvas id="pieChart"></canvas></div>
          </div>
        `;
        donutChartInstance = null;
        barChartInstance = null;
        pieChartInstance = null;
      }
    }

    function renderDonutCurrency(invoices) {
      const counts = computeFrequency(invoices, 'currency');
      const labels = Object.keys(counts);
      const values = Object.values(counts);
      const colors = labels.map((_, i) => palette[i % palette.length]);

      if (donutChartInstance) donutChartInstance.destroy();
      const ctx = document.getElementById('donutChart').getContext('2d');
      donutChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } },
            tooltip: {
              callbacks: {
                label: function (context) {
                  const label = context.label || '';
                  const value = context.parsed || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                  return label + ': ' + value + ' facture(s) (' + percentage + '%)';
                }
              }
            }
          }
        }
      });
    }

    function renderTopVendors(invoices) {
      const vendorTotals = {};
      invoices.forEach((item) => {
        const vendor = item.vendor || 'Non renseigne';
        vendorTotals[vendor] = (vendorTotals[vendor] || 0) + convertToEUR(item.amount, item.currency);
      });

      const sorted = Object.entries(vendorTotals).sort((a, b) => b[1] - a[1]).slice(0, 5);
      const labels = sorted.map((e) => e[0]);
      const values = sorted.map((e) => e[1]);
      const colors = labels.map((_, i) => palette[i % palette.length]);

      if (barChartInstance) barChartInstance.destroy();
      const ctx = document.getElementById('barChart').getContext('2d');
      barChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Montant total (EUR)',
            data: values,
            backgroundColor: colors,
            borderRadius: 8,
            maxBarThickness: 50
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return formatNumber(context.parsed.y, 2) + ' €';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) { return formatNumber(value, 0) + ' €'; }
              }
            }
          }
        }
      });
    }

    function renderDynamicPie(invoices, field) {
      const counts = computeFrequency(invoices, field);
      const labels = Object.keys(counts);
      const values = Object.values(counts);
      const colors = labels.map((_, i) => palette[i % palette.length]);

      if (pieChartInstance) pieChartInstance.destroy();
      const ctx = document.getElementById('pieChart').getContext('2d');
      pieChartInstance = new Chart(ctx, {
        type: 'pie',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } },
            tooltip: {
              callbacks: {
                label: function (context) {
                  const label = context.label || '';
                  const value = context.parsed || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                  return label + ': ' + value + ' (' + percentage + '%)';
                }
              }
            }
          }
        }
      });
    }

    function getAllQuarterKeys(allInvoices) {
      const keys = new Set();
      allInvoices.forEach((item) => {
        const d = getDateFromInvoice(item, dateFilter.type);
        if (!d) return;
        const year = d.getFullYear();
        const quarter = Math.floor(d.getMonth() / 3) + 1;
        keys.add(year + '-Q' + quarter);
      });
      return Array.from(keys).sort();
    }

    function populateQuarterCheckboxes(allInvoices) {
      const keys = getAllQuarterKeys(allInvoices);

      if (selectedQuarters === null) {
        selectedQuarters = new Set(keys);
      } else {
        keys.forEach((k) => {
          if (!previousQuarterKeys.has(k)) {
            selectedQuarters.add(k);
          }
        });
      }
      previousQuarterKeys = new Set(keys);

      if (keys.length === 0) {
        quarterCheckboxList.innerHTML = '<div class="quarter-empty-msg">Aucune donnee</div>';
        return;
      }

      quarterCheckboxList.innerHTML = keys.map((k) => {
        const parts = k.split('-Q');
        const year = parts[0];
        const q = parts[1];
        const checked = selectedQuarters.has(k) ? 'checked' : '';
        return `
          <label class="quarter-checkbox-item">
            <input type="checkbox" data-quarter="${k}" ${checked}>
            Q${q} ${year}
          </label>
        `;
      }).join('');

      quarterCheckboxList.querySelectorAll('input[type="checkbox"]').forEach((cb) => {
        cb.addEventListener('change', () => {
          const key = cb.dataset.quarter;
          if (cb.checked) selectedQuarters.add(key);
          else selectedQuarters.delete(key);
          renderQuarterlyEvolution(getInvoices());
        });
      });
    }

    quarterDropdownBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      quarterDropdownPanel.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
      if (!quarterDropdownPanel.contains(e.target) && e.target !== quarterDropdownBtn && !quarterDropdownBtn.contains(e.target)) {
        quarterDropdownPanel.classList.remove('open');
      }
    });

    selectAllQuartersBtn.addEventListener('click', () => {
      quarterCheckboxList.querySelectorAll('input[type="checkbox"]').forEach((cb) => {
        cb.checked = true;
        selectedQuarters.add(cb.dataset.quarter);
      });
      renderQuarterlyEvolution(getInvoices());
    });

    selectNoneQuartersBtn.addEventListener('click', () => {
      quarterCheckboxList.querySelectorAll('input[type="checkbox"]').forEach((cb) => {
        cb.checked = false;
      });
      if (selectedQuarters) selectedQuarters.clear();
      renderQuarterlyEvolution(getInvoices());
    });

    function renderQuarterlyEvolution(allInvoices) {
      populateQuarterCheckboxes(allInvoices);

      const quarterData = {};

      allInvoices.forEach((item) => {
        const d = getDateFromInvoice(item, dateFilter.type);
        if (!d) return;

        const year = d.getFullYear();
        const quarter = Math.floor(d.getMonth() / 3) + 1;
        const key = year + '-Q' + quarter;

        if (selectedQuarters && !selectedQuarters.has(key)) return;

        const label = 'Q' + quarter + ' ' + year;

        if (!quarterData[key]) {
          quarterData[key] = { label: label, total: 0, count: 0 };
        }

        quarterData[key].total += convertToEUR(item.amount, item.currency);
        quarterData[key].count += 1;
      });

      const sortedKeys = Object.keys(quarterData).sort();
      const labels = sortedKeys.map(k => quarterData[k].label);
      const amounts = sortedKeys.map(k => quarterData[k].total);
      const countVals = sortedKeys.map(k => quarterData[k].count);

      if (lineChartInstance) lineChartInstance.destroy();
      const ctx = document.getElementById('lineChart').getContext('2d');

      if (labels.length === 0) {
        lineChartInstance = new Chart(ctx, {
          type: 'line',
          data: { labels: ['Aucune donnee'], datasets: [{ data: [0], borderColor: '#a0aec0' }] },
          options: { responsive: true, maintainAspectRatio: false }
        });
        return;
      }

      lineChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Montant total (EUR)',
              data: amounts,
              borderColor: '#780000',
              backgroundColor: 'rgba(120,0,0,0.08)',
              fill: true,
              tension: 0.35,
              pointRadius: 4,
              yAxisID: 'y'
            },
            {
              label: 'Nombre de factures',
              data: countVals,
              borderColor: '#4ca380',
              backgroundColor: 'rgba(76,163,128,0.08)',
              fill: false,
              tension: 0.35,
              pointRadius: 4,
              yAxisID: 'y1'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom' },
            tooltip: {
              callbacks: {
                label: function (context) {
                  if (context.datasetIndex === 0) {
                    return formatNumber(context.parsed.y, 2) + ' €';
                  }
                  return context.parsed.y + ' facture(s)';
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              position: 'left',
              title: { display: true, text: 'Montant (EUR)' },
              ticks: { callback: function (value) { return formatNumber(value, 0) + ' €'; } }
            },
            y1: {
              beginAtZero: true,
              position: 'right',
              grid: { drawOnChartArea: false },
              title: { display: true, text: 'Factures' },
              ticks: { stepSize: 1 }
            }
          }
        }
      });
    }

    async function renderAll() {
      const all = await getInvoicesAsync();
      const filtered = filterByDateRange(all, dateFilter.from, dateFilter.to, dateFilter.type);
      const field = fieldSelect.value;

      totalBadge.innerHTML = '<i class="fas fa-database"></i> ' + filtered.length + ' facture(s)';
      renderStats(filtered);

      if (filtered.length === 0) {
        [donutChartInstance, barChartInstance, pieChartInstance].forEach((c) => c && c.destroy());
        donutChartInstance = barChartInstance = pieChartInstance = null;
        chartsGrid.innerHTML = `
          <div class="chart-card full-width" style="grid-column: 1 / -1;">
            <div class="no-data-msg">
              <i class="fas fa-inbox"></i>
              Aucune facture disponible pour generer des statistiques.
            </div>
          </div>
        `;
      } else {
        ensureChartsGridExists();
        renderDonutCurrency(filtered);
        renderTopVendors(filtered);
        renderDynamicPie(filtered, field);
      }

      renderQuarterlyEvolution(all);
    }

    function setDateFilter(from, to) {
      dateFilter.from = from;
      dateFilter.to = to;

      if (from) dateFrom.value = from;
      else dateFrom.value = '';

      if (to) dateTo.value = to;
      else dateTo.value = '';

      presetButtons.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));

      renderAll();
    }

    function toLocalDateStr(date) {
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, '0');
      const d = String(date.getDate()).padStart(2, '0');
      return y + '-' + m + '-' + d;
    }

    function applyPreset(days) {
      const now = new Date();

      if (days === 'today') {
        const todayStr = toLocalDateStr(now);
        setDateFilter(todayStr, todayStr);
        return;
      }

      if (days === 'all') {
        setDateFilter(null, null);
        return;
      }

      const to = new Date(now);
      to.setHours(23, 59, 59, 999);

      const from = new Date(now);
      from.setDate(from.getDate() - parseInt(days, 10));
      from.setHours(0, 0, 0, 0);

      setDateFilter(from.toISOString().slice(0, 10), to.toISOString().slice(0, 10));
    }

    function resetDateFilter() {
      setDateFilter(null, null);
      presetButtons.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
      const allBtn = presetButtons.querySelector('button[data-preset="all"]');
      if (allBtn) allBtn.classList.add('active');
    }

    fieldSelect.addEventListener('change', renderAll);
    dateTypeSelect.addEventListener('change', () => {
      dateFilter.type = dateTypeSelect.value;
      renderAll();
    });

    presetButtons.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-preset]');
      if (!btn) return;

      presetButtons.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');

      applyPreset(btn.dataset.preset);
    });

    applyBtn.addEventListener('click', () => {
      const from = dateFrom.value || null;
      const to = dateTo.value || null;

      if (from && to && from > to) {
        alert('La date de debut doit etre anterieure a la date de fin.');
        return;
      }

      presetButtons.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
      setDateFilter(from, to);
    });

    resetBtn.addEventListener('click', resetDateFilter);

    refreshBtn.addEventListener('click', () => {
      const icon = refreshBtn.querySelector('i');
      icon.classList.add('spinning');
      loadExchangeRates().then(renderAll);
      setTimeout(() => icon.classList.remove('spinning'), 600);
    });

    window.addEventListener('storage', (e) => {
      if (['invoiceSessions', 'currentInvoices', 'invoices', 'exchangeRatesCache'].includes(e.key)) {
        if (e.key === 'exchangeRatesCache') {
          try {
            const cached = JSON.parse(e.newValue);
            if (cached && cached.rates) {
              EXCHANGE_RATES = cached.rates;
            }
          } catch (err) {}
        }
        renderAll();
      }
    });

    window.addEventListener('pageshow', (e) => {
      if (e.persisted) {
        loadExchangeRates().then(renderAll);
      }
    });

    // INITIALIZE
    loadExchangeRates().then(async () => {
      const now = new Date();
      const from = new Date(now);
      from.setDate(from.getDate() - 90);
      from.setHours(0, 0, 0, 0);
      const to = new Date(now);
      to.setHours(23, 59, 59, 999);

      const invoices = await getInvoicesAsync();
      if (invoices.length > 0) {
        setDateFilter(from.toISOString().slice(0, 10), to.toISOString().slice(0, 10));
        const preset90 = presetButtons.querySelector('button[data-preset="90"]');
        if (preset90) preset90.classList.add('active');
      } else {
        renderAll();
      }
    });
  })();
</script>
  </body>
</html>