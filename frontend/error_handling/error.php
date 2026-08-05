<?php
$error_code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '500';
$error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Une erreur inattendue s\'est produite';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Erreur inattendue</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            width: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .error-container {
            background: white;
            border-radius: 24px;
            padding: 60px 50px;
            max-width: 650px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.15);
            text-align: center;
            border-top: 8px solid #e74c3c;
            animation: slideUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative background element */
        .error-container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(231, 76, 60, 0.05);
            border-radius: 50%;
            z-index: 0;
        }
        
        .error-container::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(52, 152, 219, 0.05);
            border-radius: 50%;
            z-index: 0;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .error-icon {
            font-size: 90px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 12px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .error-subtitle {
            color: #7f8c8d;
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .error-message {
            color: #555;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            padding: 0 10px;
            position: relative;
            z-index: 1;
        }
        
        .error-message strong {
            color: #2c3e50;
            display: block;
            margin-top: 15px;
            font-size: 17px;
        }
        
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 160px;
            justify-content: center;
        }
        
        .btn i {
            font-size: 16px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }
        
        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .btn-secondary:hover {
            background: #dde1e3;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        }
        
        .btn:active {
            transform: scale(0.95);
        }
        
        .error-details {
            margin-top: 25px;
            padding: 18px 20px;
            background: #f8f9fa;
            border-radius: 12px;
            font-size: 14px;
            color: #7f8c8d;
            border: 1px solid #e9ecef;
            position: relative;
            z-index: 1;
        }
        
        .error-code {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 3px 12px;
            border-radius: 6px;
            color: #2c3e50;
            font-weight: 700;
            font-size: 15px;
        }
        
        /* Error type specific colors */
        .error-container.error-400 { border-top-color: #f39c12; }
        .error-container.error-401 { border-top-color: #f39c12; }
        .error-container.error-403 { border-top-color: #f39c12; }
        .error-container.error-404 { border-top-color: #f39c12; }
        .error-container.error-429 { border-top-color: #e67e22; }
        .error-container.error-500 { border-top-color: #e74c3c; }
        .error-container.error-503 { border-top-color: #e74c3c; }
        .error-container.error-504 { border-top-color: #e74c3c; }
        
        @media (max-width: 640px) {
            body {
                padding: 15px;
            }
            
            .error-container {
                padding: 40px 25px;
                border-radius: 20px;
            }
            
            .error-icon {
                font-size: 65px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .error-subtitle {
                font-size: 16px;
            }
            
            .error-message {
                font-size: 14px;
                padding: 0;
            }
            
            .btn {
                padding: 12px 24px;
                font-size: 14px;
                min-width: 140px;
                width: 100%;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .error-details {
                font-size: 13px;
                padding: 15px;
            }
        }
        
        @media (max-width: 400px) {
            .error-container {
                padding: 30px 18px;
            }
            
            h1 {
                font-size: 20px;
            }
            
            .error-icon {
                font-size: 50px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container error-<?php echo $error_code; ?>">
        <div class="error-icon">
            <?php
            // Different icons for different error types
            $icon = 'fa-circle-exclamation';
            $color = '#e74c3c';
            
            if ($error_code == '404') {
                $icon = 'fa-file-circle-exclamation';
                $color = '#f39c12';
            } elseif ($error_code == '429') {
                $icon = 'fa-clock';
                $color = '#e67e22';
            } elseif ($error_code == '503' || $error_code == '504') {
                $icon = 'fa-server';
                $color = '#e74c3c';
            } elseif ($error_code == '401' || $error_code == '403') {
                $icon = 'fa-lock';
                $color = '#f39c12';
            } elseif ($error_code == '413' || $error_code == 'file_too_large') {
                $icon = 'fa-file-circle-exclamation';
                $color = '#e67e22';
            } elseif ($error_code == 'network_error') {
                $icon = 'fa-wifi';
                $color = '#e74c3c';
            }
            ?>
            <i class="fa-solid <?php echo $icon; ?>" style="color: <?php echo $color; ?>"></i>
        </div>
        
        <h1>
            <?php
            $titles = [
                '400' => 'Requête invalide',
                '401' => 'Accès non autorisé',
                '403' => 'Accès interdit',
                '404' => 'Page non trouvée',
                '413' => 'Fichier trop volumineux',
                '429' => 'Limite de requêtes atteinte',
                '500' => 'Erreur interne du serveur',
                '503' => 'Service indisponible',
                '504' => 'Délai d\'attente dépassé',
                'file_too_large' => 'Fichier trop volumineux',
                'network_error' => 'Erreur réseau',
                'quota_exceeded' => 'Limite de l\'API atteinte'
            ];
            echo isset($titles[$error_code]) ? $titles[$error_code] : 'Une erreur inattendue s\'est produite';
            ?>
        </h1>
        
        <p class="error-subtitle">
            <?php
            $subtitles = [
                '400' => 'La requête n\'a pas pu être traitée correctement',
                '401' => 'Vous devez être authentifié pour accéder à cette ressource',
                '403' => 'Vous n\'avez pas les permissions nécessaires',
                '404' => 'La ressource demandée n\'existe pas',
                '413' => 'Le fichier dépasse la taille maximale autorisée',
                '429' => 'Trop de requêtes ont été envoyées',
                '500' => 'Le serveur a rencontré une erreur inattendue',
                '503' => 'Le service est temporairement indisponible',
                '504' => 'Le serveur a mis trop de temps à répondre',
                'file_too_large' => 'Le fichier dépasse la taille maximale autorisée',
                'network_error' => 'Impossible de se connecter au serveur',
                'quota_exceeded' => 'Le quota de l\'API a été dépassé'
            ];
            echo isset($subtitles[$error_code]) ? $subtitles[$error_code] : 'Nous rencontrons un problème technique';
            ?>
        </p>
        
        <p class="error-message">
            <?php echo $error_message; ?>
            <strong>Que faire ?</strong>
        </p>
        
        <div class="error-actions">
            <button onclick="window.location.reload()" class="btn btn-primary">
                <i class="fas fa-rotate"></i> Réessayer
            </button>
            <button onclick="window.location.href='../page1.php'" class="btn btn-secondary">
                <i class="fas fa-home"></i> Accueil
            </button>
            <a href="mailto:houda@scs.com?subject=Erreur%20<?php echo $error_code; ?>" class="btn btn-danger">
                <i class="fas fa-envelope"></i> Contacter
            </a>
        </div>
        
        <div class="error-details">
            <strong>Code d'erreur :</strong> 
            <span class="error-code">
                <?php 
                if (is_numeric($error_code)) {
                    echo 'ERR_' . $error_code;
                } else {
                    echo 'ERR_' . strtoupper($error_code);
                }
                ?>
            </span>
            <br>
            <small style="display: block; margin-top: 8px; color: #95a5a6;">
                <?php echo $error_message; ?>
            </small>
        </div>
    </div>
</body>
</html>