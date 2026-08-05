<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur</title>
    <style>
        /* Même styles adaptés */
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
        @media (max-width: 480px) {
            .error-container { padding: 30px 20px; }
            h1 { font-size: 22px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon"></div>
        <h1>Erreur interne du serveur</h1>
        <p class="error-subtitle">Un problème technique est survenu</p>
        <p class="error-message">
            La facture n'a pas pu être traitée en raison d'une erreur interne.<br><br>
            <strong>Notre équipe technique a été informée.</strong><br>
            Veuillez réessayer dans quelques minutes.
        </p>
        <div class="error-actions">
            <button onclick="window.location.reload()" class="btn btn-primary"> Réessayer</button>
            <button onclick="window.location.href='page1.php'" class="btn btn-secondary"> Retour à l'accueil</button>
            <a href="mailto:h.mahrir@scs-group.net" class="btn btn-danger"> Contacter l'admin</a>
        </div>
        <div class="error-details">
            <strong>Code d'erreur :</strong> <span class="error-code">SERVER_500</span><br>
            <small>L'équipe technique a été notifiée automatiquement</small>
        </div>
    </div>
</body>
</html>