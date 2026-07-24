<?php

    $invoiceId = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suivi automatique</title>
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
      padding: 1.5rem;
    }

    .timeline {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      background: white;
      padding: 2.5rem 2rem 2rem 2rem;
      border-radius: 2.5rem;
      box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);
      min-width: 320px;
      transition: box-shadow 0.3s;
    }

    .item {
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
      transition: all 0.2s;
    }

    .left {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex-shrink: 0;
      width: 32px;
    }

    .circle {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: #d3d3d3;
      transition: background 0.7s cubic-bezier(0.34, 1.56, 0.64, 1),
                  box-shadow 0.5s ease,
                  transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      box-shadow: 0 0 0 0 rgba(120, 0, 0, 0);
      position: relative;
      z-index: 2;
    }

    .line {
      width: 4px;
      height: 68px;
      background: #d3d3d3;
      transition: background 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      border-radius: 4px;
      margin: 4px 0 2px 0;
      flex-shrink: 0;
      position: relative;
      z-index: 1;
    }

    .completed .circle {
      background: #780000;
      box-shadow: 0 6px 14px rgba(120, 0, 0, 0.35);
      transform: scale(1);
    }
    .completed .line {
      background: #780000;
    }

    .active .circle {
      background: #E63946;
      box-shadow: 0 0 0 6px rgba(230, 57, 70, 0.2), 0 8px 20px rgba(230, 57, 70, 0.3);
      transform: scale(1.12);
    }
    .active .line {
      background: #E63946;
    }

    @keyframes pulse-glow {
      0% { box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.15), 0 8px 20px rgba(230, 57, 70, 0.25); }
      50% { box-shadow: 0 0 0 10px rgba(230, 57, 70, 0.08), 0 8px 24px rgba(230, 57, 70, 0.35); }
      100% { box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.15), 0 8px 20px rgba(230, 57, 70, 0.25); }
    }
    .active .circle {
      animation: pulse-glow 2.4s infinite ease-in-out;
    }

    .text {
      margin-left: 6px;
      padding-top: 2px;
      transition: opacity 0.3s;
    }

    .text h3 {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      color: #1a1a1a;
      letter-spacing: -0.01em;
      transition: color 0.4s;
    }

    .text p {
      margin: 4px 0 0 0;
      color: #5e5e5e;
      font-size: 0.9rem;
      font-weight: 450;
      transition: color 0.4s;
    }

    .active .text h3 {
      color: #c1121f;
    }
    .active .text p {
      color: #1e1e1e;
      font-weight: 500;
    }

    .item:last-child .line {
      display: none;
    }

    .progress-bar {
      margin-top: 2rem;
      padding-top: 1.2rem;
      border-top: 1px solid #e9e9ed;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
    }

    .progress-track {
      flex: 1;
      height: 5px;
      background: #e6e9f0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }

    .progress-fill {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #780000, #E63946);
      border-radius: 8px;
      transition: width 0.9s cubic-bezier(0.34, 1.2, 0.64, 1);
    }

    .step-indicator {
      font-size: 0.85rem;
      font-weight: 500;
      color: #2d3a4a;
      background: #f0f2f8;
      padding: 0.2rem 1rem;
      border-radius: 40px;
      white-space: nowrap;
      border: 1px solid #dce1eb;
    }

    .step-indicator span {
      font-weight: 700;
      color: #780000;
    }

    .final-message {
      text-align: center;
      margin-top: 1.2rem;
      font-weight: 500;
      color: #780000;
      opacity: 0;
      transition: opacity 0.8s ease;
      font-size: 0.95rem;
      letter-spacing: 0.3px;
    }
    .final-message.show {
      opacity: 1;
    }

    /* Redirect Button - Hidden by default */
    .btn-redirect {
      background: var(--seondary-color, #003049);
      color: var(--white, #ffffff);
      border: none;
      padding: 14px 40px;
      border-radius: 50px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: none; /* Hidden by default */
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin: 20px auto 0;
      width: fit-content;
      background: #003049;
      color: white;
    }

    .btn-redirect.show {
      display: inline-flex; /* Show when class 'show' is added */
      animation: fadeInUp 0.6s ease;
    }

    .btn-redirect:hover {
      background: #002538;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 48, 73, 0.3);
    }

    .btn-redirect i {
      font-size: 18px;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 480px) {
      .timeline {
        padding: 1.8rem 1.2rem;
        min-width: unset;
        width: 100%;
      }
      .circle {
        width: 22px;
        height: 22px;
      }
      .line {
        height: 56px;
      }
      .text h3 {
        font-size: 1rem;
      }
      .btn-redirect {
        padding: 12px 30px;
        font-size: 14px;
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="timeline" id="timelineContainer">
 <!-- Étape 1 -->
<div class="item" data-step="0">

    <div class="text">
        <h3>Étape 1</h3>
        <p>Téléchargement de la facture</p>
    </div>
</div>

<!-- Étape 2 -->
<div class="item" data-step="1">

    <div class="text">
        <h3>Étape 2</h3>
        <p>Lecture et reconnaissance du document</p>
    </div>
</div>

<!-- Étape 3 -->
<div class="item" data-step="2">

    <div class="text">
        <h3>Étape 3</h3>
        <p>Extraction des informations de la facture</p>
    </div>
</div>

<!-- Étape 4 -->
<div class="item" data-step="3">

    <div class="text">
        <h3>Étape 4</h3>
        <p>Validation des données</p>
    </div>
</div>

  <div class="progress-bar">
    <div class="progress-track">
      <div class="progress-fill" id="progressFill"></div>
    </div>
    <div class="step-indicator">
      <span id="currentStepDisplay">1</span> / <span id="totalStepsDisplay">4</span>
    </div>
  </div>

  <div class="final-message" id="finalMessage">✅ Processus terminé avec succès !</div>

  <!-- Redirect Button - Hidden by default, shown when autoplay finishes -->
  <button class="btn-redirect" id="redirectBtn" onclick="redirectToPage3()">
    <i class="fas fa-arrow-right"></i>
    Voir le document
  </button>
</div>

<script>
  (function() {
    const items = document.querySelectorAll('.item');
    const totalSteps = items.length;
    const progressFill = document.getElementById('progressFill');
    const currentStepDisplay = document.getElementById('currentStepDisplay');
    const totalStepsDisplay = document.getElementById('totalStepsDisplay');
    const finalMessage = document.getElementById('finalMessage');
    const redirectBtn = document.getElementById('redirectBtn');
    const invoiceId = <?= $invoiceId ?>;
    if (totalStepsDisplay) {
      totalStepsDisplay.textContent = totalSteps;
    }

    let activeIndex = 0;

    function updateSteps(newActiveIndex) {
      if (newActiveIndex < 0) newActiveIndex = 0;
      if (newActiveIndex >= totalSteps) newActiveIndex = totalSteps - 1;

      items.forEach((item, index) => {
        item.classList.remove('completed', 'active');

        if (index < newActiveIndex) {
          item.classList.add('completed');
        } else if (index === newActiveIndex) {
          item.classList.add('active');
        }
      });

      activeIndex = newActiveIndex;

      const progressPercent = ((activeIndex + 1) / totalSteps) * 100;
      if (progressFill) {
        progressFill.style.width = progressPercent + '%';
      }

      if (currentStepDisplay) {
        currentStepDisplay.textContent = activeIndex + 1;
      }

      // Check if we reached the last step
      if (activeIndex === totalSteps - 1) {
        finalMessage.classList.add('show');
        // Show the redirect button when autoplay finishes
        redirectBtn.classList.add('show');
        clearInterval(statusInterval);
        stopAutoPlay();
      } else {
        finalMessage.classList.remove('show');
        redirectBtn.classList.remove('show');
      }
    }

    function resetToFirst() {
      items.forEach(item => {
        item.classList.remove('completed', 'active');
      });

      if (items.length > 0) {
        items[0].classList.add('active');
      }
      activeIndex = 0;

      if (progressFill) {
        progressFill.style.width = ((1 / totalSteps) * 100) + '%';
      }
      if (currentStepDisplay) {
        currentStepDisplay.textContent = '1';
      }
      finalMessage.classList.remove('show');
      redirectBtn.classList.remove('show');
    }

    resetToFirst();

    async function checkStatus() {

    try {

        const response = await fetch(
            `http://127.0.0.1:8000/api/invoices/${invoiceId}/status`
        );

        const data = await response.json();

        switch (data.status) {

            case "uploaded":
                updateSteps(0);
                break;

            case "template_detected":
                updateSteps(1);
                break;

                case "extracting":
                    updateSteps(2);
                    break;

                case "completed":
                    updateSteps(3);
                    break;

                case "failed":
                    alert("Une erreur est survenue.");
                    break;
            }

        } catch (error) {
            console.error(error);
        }

    }
    // Function to redirect to page3.php with file parameters
    function redirectToPage3() {
      // Get file info from PHP
          window.location.href =
        "page3.php?id=<?= $invoiceId ?>";

    }
    checkStatus();

    const statusInterval = setInterval(checkStatus,1000);
    // Make redirect function globally accessible
    window.redirectToPage3 = redirectToPage3;

  })();
</script>

</body>
</html>
