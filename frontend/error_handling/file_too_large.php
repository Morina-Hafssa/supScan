<?php
$error_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '413';
$max_size = isset($_GET['max_size']) ? htmlspecialchars($_GET['max_size']) : '10 Mo';
$file_name = isset($_GET['file']) ? htmlspecialchars($_GET['file']) : 'fichier';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Fichier trop volumineux</title>
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
        .file-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0 25px;
            border: 2px dashed #dee2e6;
        }
        .file-info strong { display: block; color: #2c3e50; margin-bottom: 5px; }
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
        <div class="error-icon"><i class="fa-solid fa-file-circle-exclamation" style="color: #e74c3c;"></i></div>
        <h1>Fichier trop volumineux</h1>
        <p class="error-subtitle">Le fichier dépasse la limite autorisée</p>
        <div class="file-info">
            <strong>📋 Fichier :</strong>
            <span style="color: #e74c3c; font-weight: bold;"><?php echo $file_name; ?></span><br>
            <strong>📋 Limite actuelle :</strong>
            <span style="color: #e74c3c; font-weight: bold;"><?php echo $max_size; ?></span><br>
            <small style="color: #7f8c8d;">Veuillez réduire la taille de votre fichier</small>
        </div>
        <p class="error-message">
            Le fichier que vous essayez d'uploader est trop volumineux.<br><br>
            <strong>Suggestions :</strong>
        </p>
        <ul style="text-align: left; margin: 15px 30px; color: #555; line-height: 1.8;">
            <li> Compresser le fichier (PDF, JPG, PNG)</li>
            <li> Diviser en plusieurs fichiers</li>
            <li> Utiliser un format moins lourd</li>
        </ul>
        <div class="error-actions">
            <button onclick="window.history.back()" class="btn btn-primary"> Choisir un autre fichier</button>
            <a href="mailto:h.mahrir@scs-group.net" class="btn btn-danger"> Contacter l'admin</a>
        </div>
        <div class="error-details">
            <strong>Code d'erreur :</strong> <span class="error-code">FILE_<?php echo $error_code; ?></span><br>
            <small>Taille maximale : <?php echo $max_size; ?></small>
        </div>
    </div>
</body>
</html>