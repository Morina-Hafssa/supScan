<?php
function getBaseUrl() {
    $path = dirname($_SERVER['SCRIPT_NAME']);
    if ($path == '/' || $path == '\\') {
        return '/';
    }
    return rtrim($path, '/') . '/';
}

$base_url = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Icons</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --main-color: #780000;
            --seondary-color: #003049;
            --accent-color: #4ca380;
            --white: #ffffff;
            --sidebar-bg: #0a1628;
            --hover-bg: rgba(120, 0, 0, 0.15);
            --active-bg: rgba(120, 0, 0, 0.25);
            --text-muted: rgba(255, 255, 255, 0.6);
            --border-light: rgba(255, 255, 255, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: #f0f2f5;
        }

        /* MENU STYLE - Collapsed by default */
        .menu {
            width: 80px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            border-right: 1px solid var(--border-light);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            z-index: 1000;
        }

        /* Expand on hover */
        .menu:hover {
            width: 250px;
        }

        /* Brand/Logo - Always visible */
        .menu .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 25px 20px;
            padding-bottom: 30px;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 20px;
            text-decoration: none;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu .sidebar-brand .logo-container {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;
            background: var(--main-color);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(120, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .menu .sidebar-brand .logo-container:hover {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 6px 25px rgba(120, 0, 0, 0.4);
        }

        .menu .sidebar-brand .logo-container img {
            width: 70%;
            height: 70%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        /* Brand text - Hidden when collapsed, shows on hover */
        .menu .sidebar-brand .brand-text {
            display: flex;
            flex-direction: column;
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            overflow: hidden;
        }

        .menu:hover .sidebar-brand .brand-text {
            opacity: 1;
            transform: translateX(0);
        }

        .menu .sidebar-brand .brand-name {
            color: var(--white);
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .menu .sidebar-brand .brand-name span {
            color: var(--accent-color);
            position: relative;
        }

        .menu .sidebar-brand .brand-name span::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .menu .sidebar-brand .brand-sub {
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Navigation */
        .menu ul {
            list-style: none;
            margin-top: 10px;
            padding: 0 12px;
        }

        .menu ul li {
            margin-bottom: 4px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .menu ul li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 13px 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            white-space: nowrap;
        }

        /* Icon styling - Always visible */
        .menu ul li a i {
            width: 24px;
            min-width: 24px;
            font-size: 18px;
            color: var(--text-muted);
            transition: all 0.3s ease;
            text-align: center;
        }

        /* Link text - Hidden when collapsed, shows on hover */
        .menu ul li a .link-text {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            white-space: nowrap;
        }

        .menu:hover ul li a .link-text {
            opacity: 1;
            transform: translateX(0);
        }

        /* Badge - Hidden when collapsed, shows on hover */
        .menu ul li a .badge {
            margin-left: auto;
            background: var(--main-color);
            color: var(--white);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 24px;
            text-align: center;
            opacity: 0;
            transform: scale(0.8);
        }

        .menu:hover ul li a .badge {
            opacity: 1;
            transform: scale(1);
        }

        /* Hover effect */
        .menu ul li a:hover {
            background: var(--hover-bg);
            color: var(--white);
            transform: translateX(6px);
        }

        .menu ul li a:hover i {
            color: var(--white);
            transform: scale(1.1);
        }

        .menu ul li a:hover .badge {
            background: var(--accent-color);
            color: var(--sidebar-bg);
            transform: scale(1.05);
        }

        /* Active state */
        .menu ul li a.active {
            background: var(--active-bg);
            color: var(--white);
            box-shadow: inset 3px 0 0 var(--main-color);
        }

        .menu ul li a.active i {
            color: var(--white);
        }

        /* Glow effect on active */
        .menu ul li a.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--accent-color);
            border-radius: 4px 0 0 4px;
            box-shadow: 0 0 20px rgba(201, 168, 76, 0.3);
        }

        /* Custom Scrollbar */
        .menu::-webkit-scrollbar {
            width: 4px;
        }

        .menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .menu::-webkit-scrollbar-thumb {
            background: var(--main-color);
            border-radius: 10px;
        }

        /* Main content */
        .main-content {
            margin-left: 80px;
            padding: 40px;
            flex: 1;
            min-height: 100vh;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu:hover + .main-content,
        .menu:hover ~ .main-content {
            margin-left: 250px;
        }

        .main-content h1 {
            color: var(--seondary-color);
            margin-bottom: 15px;
            font-size: 32px;
        }

        .main-content p {
            color: #555;
            line-height: 1.8;
            font-size: 16px;
        }

        .content-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .content-card .color-demo {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .content-card .color-demo div {
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu {
                width: 70px;
            }

            .menu:hover {
                width: 220px;
            }

            .menu ul li a {
                font-size: 14px;
                padding: 11px 10px;
            }

            .menu ul li a i {
                font-size: 16px;
                width: 20px;
                min-width: 20px;
            }

            .menu .sidebar-brand .brand-name {
                font-size: 22px;
            }

            .menu .sidebar-brand .logo-container {
                width: 40px;
                height: 40px;
                min-width: 40px;
            }

            .main-content {
                margin-left: 70px;
                padding: 25px;
            }

            .menu:hover + .main-content,
            .menu:hover ~ .main-content {
                margin-left: 220px;
            }
        }

        @media (max-width: 480px) {
            .menu {
                width: 100%;
                height: auto;
                position: relative;
                padding: 15px;
                border-right: none;
                border-bottom: 1px solid var(--border-light);
                overflow: visible;
            }

            .menu:hover {
                width: 100%;
            }

            .menu .sidebar-brand {
                padding: 15px 10px;
            }

            .menu .sidebar-brand .brand-text {
                opacity: 1;
                transform: translateX(0);
            }

            .menu ul {
                display: flex;
                flex-direction: column;
                gap: 3px;
                padding: 0;
            }

            .menu ul li a .link-text {
                opacity: 1;
                transform: translateX(0);
            }

            .menu ul li a .badge {
                opacity: 1;
                transform: scale(1);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .menu:hover + .main-content,
            .menu:hover ~ .main-content {
                margin-left: 0;
            }

            .menu ul li a:hover {
                transform: translateX(5px);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Menu -->
    <nav class="menu">
        <a href="#" class="sidebar-brand">
            <div class="logo-container">
                <img src=".\imgs\suprajitLogo.png" alt="Suprajit Logo">
            </div>
            <div class="brand-text">
                <div class="brand-name">SupScan</div>
            </div>
        </a>
        <ul>
            <li>
                <a href="<?= $base_url ?>page1.php" class="active">
                    <i class="fas fa-home"></i>
                    <span class="link-text">Accueil</span>
                </a>
            </li>
            <li>
                <a href="<?= $base_url ?>page5.php">
                    <i class="fas fa-history"></i>
                    <span class="link-text">Historique</span>
                    <span class="badge">12</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-plus-circle"></i>
                    <span class="link-text">Ajouter un Thème</span>
                </a>
            </li>
        </ul>
    </nav>


</body>
</html>
