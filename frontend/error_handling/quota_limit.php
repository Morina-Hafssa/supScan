<?php
// Get error code and message from URL if provided
$error_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '429';
$error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Le service a atteint sa limite de traitement';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limite de service atteinte</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 16px;
            padding: 50px 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 6px solid #f39c12;
        }
        .error-icon { font-size: 72px; margin-bottom: 20px; color: #f39c12; }
        h1 { color: #2c3e50; font-size: 28px; margin-bottom: 16px; font-weight: 700; }
        .error-subtitle { color: #7f8c8d; font-size: 18px; margin-bottom: 20px; }
        .error-message {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            padding: 0 10px;
        }
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52,152,219,0.3);
        }
        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
        }
        .btn-secondary:hover {
            background: #dde1e3;
            transform: translateY(-2px);
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231,76,60,0.3);
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #229954;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39,174,96,0.3);
        }
        .error-details {
            margin-top: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            color: #7f8c8d;
            border: 1px solid #e9ecef;
        }
        .error-code {
            font-family: monospace;
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 4px;
            color: #2c3e50;
        }
        @media (max-width: 480px) {
            .error-container { padding: 30px 20px; }
            h1 { font-size: 22px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon"><i class="fa-solid fa-gauge-high" style="color: #e74c3c;"></i></div>
        <h1>Limite de service atteinte</h1>
        <p class="error-subtitle">L'IA est temporairement indisponible</p>
        <p class="error-message">
            La facture n'a pas pu être traitée car le service d'intelligence artificielle a atteint sa limite de traitement actuelle.<br><br>
            <strong>Que faire ?</strong>
        </p>
        <div class="error-actions">
            <button onclick="retryRequest()" class="btn btn-primary"> Réessayer</button>
            <button onclick="saveForLater()" class="btn btn-success"> Sauvegarder pour plus tard</button>
            <a href="mailto:h.mahrir@scs-group.net" class="btn btn-danger"> Contacter l'admin</a>
        </div>
        <div class="error-details">
            <strong>Code d'erreur :</strong> <span class="error-code">QUOTA_<?php echo $error_code; ?></span><br>
            <small><?php echo $error_message; ?></small>
        </div>
    </div>

    <script>
        function retryRequest() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '../page1.php';
            }
        }

        function saveForLater() {
            const formData = new FormData(document.querySelector('form'));
            if (formData) {
                localStorage.setItem('pending_invoice', JSON.stringify({
                    data: 'pending',
                    timestamp: new Date().toISOString()
                }));
                alert(' Vos fichiers ont été sauvegardés. Vous pourrez les traiter plus tard.');
                window.location.href = '../page1.php';
            }
        }
    </script>
</body>
</html>