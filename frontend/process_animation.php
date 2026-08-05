<?php
  session_start();

  $jobId = $_GET['id'] ?? null;

  if (!$jobId) {
      header("Location: page1.php");
      exit();
  }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suivi automatique · SupScan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    /* ... (all styles remain the same) ... */
    :root {
      --ink-navy: #003049;
      --ink-navy-deep: #001c2b;
      --ink-maroon: #780000;
      --ink-red: #E63946;
      --ink-green: #2d8a4e;
      --paper: #fbfbfa;
      --paper-line: #e4e7ee;
      --muted: #6b7a8f;
      --dark: #12202c;
      --console-bg: #0b1b26;
      --console-text: #b9c9d4;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      display: flex;
      min-height: 100vh;
      background: #eef1f6;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: var(--dark);
    }

    .main-content {
      flex: 1;
      margin-left: 70px;
      min-height: 100vh;
      display: flex;
      align-items: stretch;
      justify-content: stretch;
    }

    .ticket {
      width: 100%;
      background: var(--paper);
      overflow: hidden;
      position: relative;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .timeline-header {
      text-align: center;
      margin-top: 3.5rem;
      position: relative;
      z-index: 1;
    }

    .timeline-header h1 {
      font-size: 2.2rem;
      font-weight: 700;
      color: #1a1a2e;
      letter-spacing: -0.5px;
      margin-bottom: 0.3rem;
    }

    .timeline-header h1 i {
      color: #780000;
      margin-right: 10px;
    }

    .timeline-header p {
      color: #6b7a8f;
      font-size: 1rem;
      font-weight: 400;
    }

    .job-code {
      display: inline-block;
      margin-top: 10px;
      background: #f0f2f8;
      padding: 0.3rem 1.2rem;
      border-radius: 30px;
      font-size: 0.8rem;
      font-weight: 600;
      color: #2d3a4a;
      font-family: 'Courier New', monospace;
      border: 1px solid #e8ecf1;
    }

    .job-code i {
      color: #780000;
      margin-right: 6px;
    }

    .ticket-body {
      padding: 48px 56px 48px 56px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      max-width: 720px;
      width: 100%;
      margin: 0 auto;
    }

    .stations {
      display: flex;
      align-items: flex-start;
      margin-bottom: 30px;
    }

    .station {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      width: 84px;
      flex-shrink: 0;
    }

    .station-node {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #fff;
      border: 2.5px solid var(--paper-line);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      color: #b3bdcb;
      transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                  border-color 0.35s ease, color 0.35s ease,
                  box-shadow 0.35s ease, background 0.35s ease;
      position: relative;
    }

    .station-label {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 0.68rem;
      letter-spacing: 0.4px;
      text-transform: uppercase;
      color: var(--muted);
      text-align: center;
      transition: color 0.35s ease;
    }

    .station-line {
      flex: 1;
      height: 2px;
      background-image: repeating-linear-gradient(90deg, var(--paper-line) 0 6px, transparent 6px 12px);
      margin-top: 23px;
      transition: background-image 0.4s ease;
    }

    .station-line.completed {
      background-image: repeating-linear-gradient(90deg, var(--ink-green) 0 6px, transparent 6px 12px);
    }

    .station.active .station-node {
      border-color: var(--ink-red);
      color: var(--ink-red);
      background: #fff5f5;
      box-shadow: 0 0 0 6px rgba(230, 57, 70, 0.12);
      animation: pulse-ring 1.8s ease-in-out infinite;
    }

    .station.active .station-label { color: var(--ink-red); font-weight: 600; }

    @keyframes pulse-ring {
      0%, 100% { box-shadow: 0 0 0 6px rgba(230, 57, 70, 0.12); }
      50% { box-shadow: 0 0 0 10px rgba(230, 57, 70, 0.06); }
    }

    .station.completed .station-node {
      border-color: var(--ink-green);
      background: var(--ink-green);
      color: #fff;
      animation: stamp-down 0.5s cubic-bezier(0.2, 1.4, 0.4, 1);
    }

    .station.completed .station-label { color: var(--ink-green); }

    @keyframes stamp-down {
      0% { transform: scale(1.7) rotate(-8deg); opacity: 0; }
      60% { transform: scale(0.92) rotate(2deg); opacity: 1; }
      100% { transform: scale(1) rotate(0deg); }
    }

    .status-container {
      text-align: center;
      margin: 10px 0 20px 0;
      padding: 10px;
      background: #f8f9fc;
      border-radius: 10px;
      border: 1px solid #e8ecf1;
    }

    .status-text {
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      font-size: 0.95rem;
      color: var(--dark);
    }

    .status-text i {
      color: #780000;
      font-size: 1.1rem;
    }

    .status-text i.fa-spinner {
      color: #E63946;
    }

    .status-text i.fa-check-circle {
      color: #2d8a4e;
    }

    .status-text i.fa-exclamation-triangle {
      color: #dc3545;
    }

    .progress-section {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
    }

    .ruler-track {
      flex: 1;
      position: relative;
      height: 10px;
    }

    .ruler-ticks {
      position: absolute;
      inset: 0;
      display: flex;
      justify-content: space-between;
      pointer-events: none;
    }

    .ruler-ticks span {
      width: 1px;
      height: 100%;
      background: rgba(0, 32, 51, 0.1);
    }

    .ruler-base {
      position: absolute;
      top: 3.5px;
      left: 0;
      right: 0;
      height: 3px;
      border-radius: 3px;
      background: var(--paper-line);
    }

    .ruler-fill {
      position: absolute;
      top: 3.5px;
      left: 0;
      height: 3px;
      width: 0%;
      border-radius: 3px;
      background: linear-gradient(90deg, var(--ink-maroon), var(--ink-red));
      transition: width 0.6s cubic-bezier(0.34, 1.2, 0.64, 1);
    }

    .progress-readout {
      font-family: 'IBM Plex Mono', monospace;
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--ink-navy);
      background: #eef1f6;
      padding: 5px 11px;
      border-radius: 8px;
      min-width: 56px;
      text-align: center;
      font-variant-numeric: tabular-nums;
    }

    .final-message {
      text-align: center;
      font-weight: 600;
      color: var(--ink-green);
      font-size: 0.95rem;
      opacity: 0;
      height: 0;
      overflow: hidden;
      transition: opacity 0.5s ease;
    }

    .final-message.show {
      opacity: 1;
      height: auto;
      margin-bottom: 18px;
    }

    .btn-tearoff {
      width: 100%;
      background: linear-gradient(135deg, var(--ink-navy), var(--ink-navy-deep));
      color: #fff;
      border: none;
      border-top: 2px dashed rgba(255, 255, 255, 0.3);
      padding: 16px 20px;
      border-radius: 14px;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      display: none;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      box-shadow: 0 10px 26px rgba(0, 48, 73, 0.3);
    }

    .btn-tearoff.show {
      display: inline-flex;
      animation: rise-in 0.5s ease;
    }

    .btn-tearoff:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 34px rgba(0, 48, 73, 0.4);
    }

    .btn-tearoff:active { transform: scale(0.97); }

    .btn-tearoff:focus-visible {
      outline: 3px solid var(--ink-red);
      outline-offset: 3px;
    }

    @keyframes rise-in {
      from { opacity: 0; transform: translateY(14px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (prefers-reduced-motion: reduce) {
      .station.active .station-node,
      .station.completed .station-node,
      .btn-tearoff.show {
        animation: none !important;
      }
    }

    @media (max-width: 768px) {
      .main-content { margin-left: 220px; }
      .ticket-body { padding: 32px 28px; }
      .station { width: 64px; }
      .station-node { width: 40px; height: 40px; font-size: 14px; }
      .station-label { font-size: 0.58rem; }
      .station-line { margin-top: 19px; }
    }

    @media (max-width: 480px) {
      .main-content { margin-left: 0; }
      .ticket-body { padding: 24px 20px; }
      .stations { margin-bottom: 22px; }
      .station { width: 52px; }
      .station-node { width: 34px; height: 34px; font-size: 12px; border-width: 2px; }
      .station-label { display: none; }
      .station-line { margin-top: 16px; }
      .progress-readout { font-size: 0.75rem; min-width: 44px; }
      .timeline-header h1 { font-size: 1.5rem; }
    }
  </style>
</head>
<body>

<?php include("Sidebar.php"); ?>

<div class="main-content">
  <div class="ticket">

    <div class="timeline-header">
      <h1> Traitement en cours</h1>
      <p>Votre document est en cours d'analyse, veuillez patienter...</p>
      <div class="job-code">
        <i class="fas fa-hashtag"></i> 
        <span id="jobCodeDisplay">—</span>
      </div>
    </div>

    <!-- Body -->
    <div class="ticket-body">

      <!-- Stations -->
      <div class="stations" id="stations">
        <div class="station" data-step="0">
          <div class="station-node"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="station-label">Envoi</div>
        </div>
        <div class="station-line" data-line="0"></div>

        <div class="station" data-step="1">
          <div class="station-node"><i class="fas fa-cog"></i></div>
          <div class="station-label">Preparation</div>
        </div>
        <div class="station-line" data-line="1"></div>

        <div class="station" data-step="2">
          <div class="station-node"><i class="fas fa-brain"></i></div>
          <div class="station-label">Extraction</div>
        </div>
        <div class="station-line" data-line="2"></div>

        <div class="station" data-step="3">
          <div class="station-node"><i class="fas fa-check"></i></div>
          <div class="station-label">Termine</div>
        </div>
      </div>

      <!-- Status Text -->
      <div class="status-container">
        <div class="status-text" id="statusText">
          <i class="fas fa-spinner fa-pulse"></i> Initialisation...
        </div>
      </div>

      <!-- Progress -->
      <div class="progress-section">
        <div class="ruler-track">
          <div class="ruler-base"></div>
          <div class="ruler-ticks">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span><span></span>
          </div>
          <div class="ruler-fill" id="progressFill"></div>
        </div>
        <div class="progress-readout" id="progressPercentage">0%</div>
      </div>

      <!-- CTA -->
      <div class="final-message" id="finalMessage"> Document valide - pret a consulter.</div>

      <button class="btn-tearoff" id="redirectBtn" onclick="redirectToPage3()">
        <i class="fas fa-arrow-right"></i>
        Voir le document
      </button>

    </div>
  </div>
</div>

<script>
  (function() {
    // ============================================================
    // CHECK IF RUNNING IN ELECTRON
    // ============================================================
    function isElectron() {
      return typeof window !== 'undefined' && window.electronAPI !== undefined;
    }

    // ============================================================
    // FIXED API URL - NO CONFIG FILE NEEDED
    // ============================================================
    const API_BASE_URL = 'http://127.0.0.1:5000';
    console.log('[APP] Using fixed API URL:', API_BASE_URL);

    const stations = document.querySelectorAll('.station');
    const stationLines = document.querySelectorAll('.station-line');
    const totalSteps = stations.length;
    
    const progressFill = document.getElementById('progressFill');
    const progressPercentage = document.getElementById('progressPercentage');
    const statusText = document.getElementById('statusText');
    const finalMessage = document.getElementById('finalMessage');
    const redirectBtn = document.getElementById('redirectBtn');
    const jobCodeDisplay = document.getElementById('jobCodeDisplay');

    const jobId = <?= json_encode($jobId) ?>;
    
    let activeIndex = 0;
    let statusInterval = null;
    let isCompleting = false; // FIX: Prevent duplicate completion saves

    if (jobCodeDisplay && jobId) {
      jobCodeDisplay.textContent = '#' + jobId.slice(0, 8).toUpperCase();
    }

    function updateSteps(newActiveIndex) {
      if (newActiveIndex < 0) newActiveIndex = 0;
      if (newActiveIndex >= totalSteps) newActiveIndex = totalSteps - 1;

      stations.forEach((station, index) => {
        station.classList.remove('completed', 'active');
        
        if (index < newActiveIndex) {
          station.classList.add('completed');
        } else if (index === newActiveIndex) {
          station.classList.add('active');
        }
      });

      stationLines.forEach((line, index) => {
        line.classList.toggle('completed', index < newActiveIndex);
      });

      activeIndex = newActiveIndex;

      const progressPercent = ((activeIndex + 1) / totalSteps) * 100;
      if (progressFill) {
        progressFill.style.width = progressPercent + '%';
      }
      if (progressPercentage) {
        progressPercentage.textContent = Math.round(progressPercent) + '%';
      }
    }

    function resetToFirst() {
      stations.forEach(station => {
        station.classList.remove('completed', 'active');
      });
      stationLines.forEach(line => {
        line.classList.remove('completed');
      });

      if (stations.length > 0) {
        stations[0].classList.add('active');
      }
      activeIndex = 0;

      if (progressFill) {
        progressFill.style.width = ((1 / totalSteps) * 100) + '%';
      }
      if (progressPercentage) {
        progressPercentage.textContent = Math.round((1 / totalSteps) * 100) + '%';
      }
      
      finalMessage.classList.remove('show');
      redirectBtn.classList.remove('show');
    }

    resetToFirst();

    if (!jobId) {
      console.error('No job ID provided');
      alert('Erreur: Aucun ID de job fourni. Redirection vers la page d\'accueil.');
      window.location.href = 'page1.php';
      return;
    }

    console.log('Job ID:', jobId);

    function updateStatusText(status, progress, currentInvoice, totalInvoices) {
      let icon = 'fa-spinner fa-pulse';
      let text = '';

      switch(status) {
        case 'uploaded':
          icon = 'fa-cloud-upload-alt';
          text = ' Document telecharge, en attente...';
          break;
        case 'processing':
          icon = 'fa-cog fa-spin';
          if (currentInvoice !== undefined && totalInvoices !== undefined && totalInvoices > 0) {
            text = ' Traitement page ' + currentInvoice + ' / ' + totalInvoices;
          } else {
            text = ' Preparation du document...';
          }
          break;
        case 'extracting':
          icon = 'fa-brain';
          if (currentInvoice !== undefined && totalInvoices !== undefined && totalInvoices > 0) {
            text = ' Extraction IA page ' + currentInvoice + ' / ' + totalInvoices;
          } else {
            text = ' Extraction des donnees par IA...';
          }
          break;
        case 'validating':
          icon = 'fa-check-circle';
          if (currentInvoice !== undefined && totalInvoices !== undefined && totalInvoices > 0) {
            text = ' Validation page ' + currentInvoice + ' / ' + totalInvoices;
          } else {
            text = ' Validation des donnees...';
          }
          break;
        case 'completed':
          icon = 'fa-check-circle';
          text = ' Termine avec succes !';
          break;
        case 'failed':
          icon = 'fa-exclamation-triangle';
          text = ' Erreur lors du traitement';
          break;
        default:
          icon = 'fa-spinner fa-pulse';
          text = ' En cours...';
      }

      
      if (statusText) {
        statusText.innerHTML = '<i class="fas ' + icon + '"></i> ' + text;
      }
    }

    // ============================================================
    // SAVE INVOICES TO SQLITE VIA ELECTRON IPC
    // FIX: Set currentSessionId so page4 knows this session exists
    // ============================================================
    async function saveInvoicesToDatabase(invoices) {
      try {
        if (isElectron()) {
          // Save to SQLite via Electron IPC
          const sessionId = 'SESSION_' + new Date().toISOString().replace(/[-:.TZ]/g, "").slice(0, 14);
          const sessionData = {
            sessionID: sessionId,
            created_at: new Date().toISOString(),
            invoices: invoices
          };
          
          await window.electronAPI.db.saveSession(sessionId, sessionData);
          await window.electronAPI.db.saveCurrentInvoices(invoices);
          
          // FIX: Tell page4 this session already exists — prevents duplicate session creation
          await window.electronAPI.db.setSetting('currentSessionId', sessionId);
          await window.electronAPI.db.setSetting('lastSavedInvoiceHash', JSON.stringify(invoices));
          
          localStorage.setItem("currentInvoice", "0");
          
          console.log('Invoices saved to SQLite, sessionId:', sessionId);
          return true;
        } else {
          // Fallback to localStorage
          localStorage.setItem("currentInvoices", JSON.stringify(invoices));
          localStorage.setItem("currentInvoice", "0");
          
          let history = JSON.parse(localStorage.getItem("invoiceHistory")) || [];
          history.push(...invoices);
          localStorage.setItem("invoiceHistory", JSON.stringify(history));
          
          console.log('Invoices saved to localStorage:', invoices.length);
          return true;
        }
      } catch (error) {
        console.error('Error saving invoices:', error);
        return false;
      }
    }

    async function checkStatus() {
      try {
        const response = await fetch(API_BASE_URL + '/status/' + jobId);

        if (!response.ok) {
          console.log('Status check failed:', await response.text());
          return;
        }

        const data = await response.json();
        console.log('Status:', data.status, 'Progress:', data.progress);
        
        // Update progress bar from server progress
        if (data.progress !== undefined && progressFill) {
          const progressPercent = Math.min(data.progress, 100);
          progressFill.style.width = progressPercent + '%';
          if (progressPercentage) {
            progressPercentage.textContent = Math.round(progressPercent) + '%';
          }
        }

        // Update status text
        updateStatusText(
          data.status, 
          data.progress, 
          data.current_invoice, 
          data.total_invoices
        );

        // Update station steps based on status
        switch (data.status) {
          case "uploaded":
            updateSteps(0);
            break;
          
          case "processing":
            updateSteps(1);
            break;
          
          case "extracting":
          case "validating":
            updateSteps(2);
            break;
          
          case "completed":
            // FIX: Guard against duplicate completion saves
            if (isCompleting) {
              console.log('Completion already in progress, skipping duplicate...');
              break;
            }
            isCompleting = true;
            
            updateSteps(3);
            
            if (statusInterval) {
              clearInterval(statusInterval);
              statusInterval = null;
            }
            
            // Save invoices to database
            if (data.invoices) {
              const invoices = Array.isArray(data.invoices) ? data.invoices : [data.invoices];
              
              const invoicesWithDate = invoices.map(invoice => ({
                ...invoice,
                created_at: invoice.created_at || new Date().toISOString()
              }));
              
              await saveInvoicesToDatabase(invoicesWithDate);
            }
            
            redirectBtn.classList.add('show');
            finalMessage.classList.add('show');
            
            // Redirect after a delay
            setTimeout(() => {
              redirectToPage3();
            }, 1500);
            
            break;
          
          case "failed":
            if (statusInterval) {
              clearInterval(statusInterval);
              statusInterval = null;
            }
            alert(data.error || "Une erreur est survenue lors du traitement.");
            break;
          
          default:
            console.log('Unknown status:', data.status);
        }

      } catch (error) {
        console.error('Error checking status:', error);
      }
    }

    function redirectToPage3() {
      window.location.href = "page3.php";
    }

    window.redirectToPage3 = redirectToPage3;

    // ============================================================
    // START MONITORING
    // ============================================================
    console.log('Page loaded, starting job monitoring...');
    (async function init() {
      console.log('[APP] API URL:', API_BASE_URL);
      console.log('[APP] Storage: SQLite via Electron IPC');
      
      await checkStatus();
      statusInterval = setInterval(checkStatus, 2000);
    })();

  })();
</script>

</body>
</html>