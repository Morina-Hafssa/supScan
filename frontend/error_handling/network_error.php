<?php
$error_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '503';
$error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Probleme de connexion reseau';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Erreur reseau</title>
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
            border-top: 6px solid #e74c3c;
        }
        .error-icon { font-size: 72px; margin-bottom: 20px; color: #e74c3c; }
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
        .connection-status {
            display: inline-block;
            padding: 6px 14px;
            background: #fee;
            color: #c0392b;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 15px;
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
        <div class="error-icon"><i class="fa-solid fa-triangle-exclamation" style="color: #e74c3c;"></i></div>
        <h1>Erreur de connexion</h1>
        <p class="error-subtitle">Probleme de communication avec le serveur</p>
        <div class="connection-status" id="connectionStatus"> Connexion instable</div>
        <p class="error-message">
            La facture n'a pas pu etre traitee en raison d'un probleme reseau.<br><br>
            <strong>Verifiez :</strong>
        </p>
        <ul style="text-align: left; margin: 15px 30px; color: #555; line-height: 1.8;">
            <li> Votre connexion Internet</li>
            <li> Le statut du serveur (peut-etre en maintenance)</li>
            <li> Votre pare-feu ou VPN</li>
        </ul>
        <div class="error-actions">
            <button onclick="checkNetwork()" class="btn btn-primary"> Tester la connexion</button>
            <button onclick="window.location.reload()" class="btn btn-secondary"> Reessayer</button>
            <a href="mailto:h.mahrir@scs-group.net" class="btn btn-danger"> Contacter l'admin</a>
            <button onclick="window.location.href='../page1.php'" class="btn btn-secondary">
                <i class="fas fa-home"></i> Accueil
            </button>
        </div>
        <div class="error-details">
            <strong>Code d'erreur :</strong> <span class="error-code">NETWORK_<?php echo $error_code; ?></span><br>
            <small><?php echo $error_message; ?></small>
        </div>
    </div>

    <script>
        // ============================================================
        // DYNAMIC API URL LOADER
        // ============================================================
        let API_BASE_URL = 'http://127.0.0.1:5000'; // Default fallback

        async function loadConfig() {
            try {
                const response = await fetch('/config.json');
                if (response.ok) {
                    const config = await response.json();
                    if (config.apiUrl) {
                        API_BASE_URL = config.apiUrl;
                        console.log('[APP] API URL loaded from config:', API_BASE_URL);
                    } else {
                        console.warn('[APP] No apiUrl in config, using default:', API_BASE_URL);
                    }
                } else {
                    console.warn('[APP] config.json not found, using default:', API_BASE_URL);
                }
            } catch (error) {
                console.warn('[APP] Failed to load config, using default:', API_BASE_URL);
            }
        }

        function checkNetwork() {
            const statusDiv = document.getElementById('connectionStatus');
            statusDiv.textContent = 'Verification...';
            statusDiv.style.background = '#fff3cd';
            statusDiv.style.color = '#856404';

            fetch(API_BASE_URL + '/api/health', {
                method: 'GET',
                signal: AbortSignal.timeout(5000)
            })
            .then(response => {
                if (response.ok) {
                    statusDiv.textContent = 'Connexion retablie ! Redirection...';
                    statusDiv.style.background = '#d4edda';
                    statusDiv.style.color = '#155724';
                    setTimeout(() => window.location.href = '../page1.php', 1500);
                } else {
                    throw new Error('Serveur indisponible');
                }
            })
            .catch(error => {
                statusDiv.textContent = 'Toujours hors ligne - Verifiez votre reseau';
                statusDiv.style.background = '#f8d7da';
                statusDiv.style.color = '#721c24';
            });
        }

        // ============================================================
        // INITIALIZATION
        // ============================================================
        (async function init() {
            await loadConfig();
            console.log('[APP] API URL:', API_BASE_URL);
        })();
    </script>
</body>
</html>