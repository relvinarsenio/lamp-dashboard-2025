<?php
$phpVersion = phpversion();
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown';
$serverName = $_SERVER['SERVER_NAME'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
$normalizedClientIp = is_string($clientIp) ? $clientIp : '';
if (strpos($normalizedClientIp, '::ffff:') === 0) {
    $normalizedClientIp = substr($normalizedClientIp, 7);
}

$phpinfoAllowed = false;
if ($normalizedClientIp !== '' && $normalizedClientIp !== 'N/A' && filter_var($normalizedClientIp, FILTER_VALIDATE_IP)) {
    $isPublicIp = filter_var($normalizedClientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    if (!$isPublicIp) {
        $phpinfoAllowed = true;
    } elseif (isset($_SERVER['SERVER_ADDR'])) {
        $serverAddr = $_SERVER['SERVER_ADDR'];
        if (strpos($serverAddr, '::ffff:') === 0) {
            $serverAddr = substr($serverAddr, 7);
        }
        if ($normalizedClientIp === $serverAddr) {
            $phpinfoAllowed = true;
        }
    }
}

$loadedExtensions = get_loaded_extensions();
$extensionCount = count($loadedExtensions);
$databaseHost = ini_get('mysqli.default_host') ?: 'localhost';
$databaseEngine = 'Unknown';
$databaseVersion = '';

if (function_exists('mysqli_get_client_info')) {
    $clientInfo = mysqli_get_client_info();
    if ($clientInfo) {
        if (stripos($clientInfo, 'mariadb') !== false) {
            $databaseEngine = 'MariaDB';
        } elseif (stripos($clientInfo, 'mysql') !== false || stripos($clientInfo, 'mysqlnd') !== false) {
            $databaseEngine = 'MySQL';
        }

        if (preg_match('/(\d+\.\d+\.\d+)/', $clientInfo, $matches)) {
            $databaseVersion = $matches[1];
        } elseif (preg_match('/(\d+\.\d+)/', $clientInfo, $matches)) {
            $databaseVersion = $matches[1];
        }
    }
}

if ($databaseEngine === 'Unknown') {
    if (extension_loaded('pdo_mysql')) {
        $databaseEngine = 'MySQL';
    } elseif (extension_loaded('mysqli')) {
        $databaseEngine = 'MySQL';
    }
}

$databaseLabel = $databaseEngine;
if ($databaseVersion !== '') {
    $databaseLabel .= ' ' . $databaseVersion;
}

if (trim($databaseLabel) === '') {
    $databaseLabel = 'Unknown';
}

$osName = trim(php_uname('s') . ' ' . php_uname('r'));

$osReleasePath = '/etc/os-release';
if (is_readable($osReleasePath)) {
    $osRelease = file_get_contents($osReleasePath);
    if ($osRelease !== false && preg_match('/^PRETTY_NAME="?([^"\n]+)"?/m', $osRelease, $matches)) {
        $osName = $matches[1];
    }
}

$phpinfo = '';
if ($phpinfoAllowed) {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();
    $phpinfo = preg_replace('%<style[^>]*?>.*?</style>%si', '', $phpinfo);
    $phpinfo = preg_replace('%^.*<body[^>]*>(.*)</body>.*$%msi', '$1', $phpinfo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome to Your LAMP Playground</title>
    <style>
        :root {
            --font-family: 'Poppins', 'Segoe UI', system-ui, -apple-system, sans-serif;
            --transition-base: 0.3s ease;

            --light-bg: linear-gradient(135deg, #eef2ff, #f8fafc);
            --light-surface: rgba(255, 255, 255, 0.75);
            --light-card: rgba(248, 250, 252, 0.85);
            --light-primary: #6366f1;
            --light-secondary: #14b8a6;
            --light-text: #0f172a;
            --light-muted: rgba(15, 23, 42, 0.65);
            --light-shadow: 12px 12px 36px rgba(57, 66, 99, 0.2), -12px -12px 36px rgba(255, 255, 255, 0.9);

            --dark-bg: linear-gradient(135deg, #0f172a, #1e293b);
            --dark-surface: rgba(15, 23, 42, 0.65);
            --dark-card: rgba(30, 41, 59, 0.7);
            --dark-primary: #8b5cf6;
            --dark-secondary: #22d3ee;
            --dark-text: #f8fafc;
            --dark-muted: rgba(226, 232, 240, 0.72);
            --dark-shadow: 12px 12px 36px rgba(2, 6, 23, 0.55), -12px -12px 36px rgba(148, 163, 184, 0.12);
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-family);
            display: flex;
            align-items: stretch;
            justify-content: center;
            background: var(--light-bg);
            color: var(--light-text);
            transition: background var(--transition-base), color var(--transition-base);
        }

        body[data-theme="dark"] {
            background: var(--dark-bg);
            color: var(--dark-text);
        }

        .page-shell {
            width: min(1100px, 94vw);
            margin: 3rem auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .glass-panel {
            background: var(--light-surface);
            border-radius: 32px;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            box-shadow: var(--light-shadow);
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(24px);
            transition: background var(--transition-base), box-shadow var(--transition-base), border var(--transition-base);
        }

        body[data-theme="dark"] .glass-panel {
            background: var(--dark-surface);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: var(--dark-shadow);
        }

        header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .top-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.75), rgba(20, 184, 166, 0.8));
            box-shadow: 8px 8px 24px rgba(79, 70, 229, 0.25), -6px -6px 18px rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        body[data-theme="dark"] .brand-logo {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.55), rgba(14, 165, 233, 0.55));
            box-shadow: 8px 8px 24px rgba(2, 6, 23, 0.65), -6px -6px 18px rgba(148, 163, 184, 0.15);
            border: 1px solid rgba(148, 163, 184, 0.22);
        }

        .brand-logo svg {
            width: 28px;
            height: 28px;
            fill: white;
            object-fit: contain;
            filter: drop-shadow(0 12px 18px rgba(79, 70, 229, 0.35));
            transition: transform var(--transition-base);
        }

        body[data-theme="dark"] .brand-logo svg {
            filter: drop-shadow(0 10px 16px rgba(34, 211, 238, 0.3));
        }

        .brand-logo:hover svg {
            transform: scale(1.06) translateY(-1px);
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toggle-switch {
            position: relative;
            width: 64px;
            height: 32px;
            border-radius: 24px;
            background: rgba(99, 102, 241, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.65);
            cursor: pointer;
            transition: background var(--transition-base), border var(--transition-base);
            box-shadow: inset 6px 6px 12px rgba(15, 23, 42, 0.08), inset -6px -6px 12px rgba(255, 255, 255, 0.45);
        }

        .toggle-thumb {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(145deg, #ffffff, #dfe6ff);
            box-shadow: 4px 4px 12px rgba(15, 23, 42, 0.25), -4px -4px 12px rgba(255, 255, 255, 0.9);
            transition: transform var(--transition-base), background var(--transition-base), box-shadow var(--transition-base);
        }

        body[data-theme="dark"] .toggle-switch {
            background: rgba(139, 92, 246, 0.25);
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: inset 6px 6px 14px rgba(2, 6, 23, 0.6), inset -4px -4px 12px rgba(148, 163, 184, 0.12);
        }

        body[data-theme="dark"] .toggle-thumb {
            transform: translateX(32px);
            background: linear-gradient(145deg, #1e293b, #111827);
            box-shadow: 4px 4px 12px rgba(2, 6, 23, 0.8), -4px -4px 12px rgba(148, 163, 184, 0.2);
        }

        h1 {
            margin: 0;
            font-size: clamp(2.1rem, 4vw, 2.9rem);
            font-weight: 600;
        }

        p.subtitle {
            margin: 0;
            color: var(--light-muted);
            max-width: 620px;
        }

        body[data-theme="dark"] p.subtitle {
            color: var(--dark-muted);
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .quick-card {
            position: relative;
            padding: 1.4rem;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            background: var(--light-card);
            box-shadow: 12px 12px 34px rgba(148, 163, 184, 0.2), -10px -10px 30px rgba(255, 255, 255, 0.7);
            text-decoration: none;
            color: inherit;
            overflow: hidden;
            transition: transform var(--transition-base), box-shadow var(--transition-base), border var(--transition-base);
        }

        .quick-card:hover {
            transform: translateY(-6px);
            box-shadow: 16px 16px 36px rgba(99, 102, 241, 0.18), -12px -12px 34px rgba(255, 255, 255, 0.75);
        }

        .quick-card .quick-icon {
            font-size: 2.1rem;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 18px;
            background: rgba(99, 102, 241, 0.12);
            box-shadow: inset 4px 4px 10px rgba(148, 163, 184, 0.25), inset -4px -4px 10px rgba(255, 255, 255, 0.25);
            margin-bottom: 0.8rem;
        }

        body[data-theme="dark"] .quick-card .quick-icon {
            background: rgba(139, 92, 246, 0.2);
            box-shadow: inset 4px 4px 10px rgba(2, 6, 23, 0.55), inset -4px -4px 10px rgba(148, 163, 184, 0.18);
        }

        .quick-card h3 {
            margin: 1rem 0 0.5rem;
            font-size: 1.1rem;
        }

        .quick-card span {
            font-size: 0.95rem;
            color: inherit;
            opacity: 0.75;
        }

        .quick-card.disabled {
            cursor: not-allowed;
            opacity: 0.55;
        }

        body[data-theme="dark"] .quick-card {
            background: var(--dark-card);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 12px 12px 34px rgba(2, 6, 23, 0.6), -10px -10px 28px rgba(148, 163, 184, 0.08);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .info-card {
            padding: 1.5rem;
            border-radius: 24px;
            background: var(--light-card);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 10px 10px 24px rgba(148, 163, 184, 0.22), -10px -10px 24px rgba(255, 255, 255, 0.8);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: background var(--transition-base), border var(--transition-base), box-shadow var(--transition-base);
        }

        .info-card h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.05rem;
        }

        .info-card span {
            font-size: clamp(1.2rem, 2vw + 0.9rem, 2.15rem);
            font-weight: 600;
            color: var(--light-primary);
            line-height: 1.25;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        body[data-theme="dark"] .info-card {
            background: var(--dark-card);
            border: 1px solid rgba(148, 163, 184, 0.22);
            box-shadow: 10px 10px 24px rgba(2, 6, 23, 0.7), -10px -10px 24px rgba(148, 163, 184, 0.08);
        }

        body[data-theme="dark"] .info-card span {
            color: var(--dark-secondary);
        }

        .stack-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.4rem;
        }

        .detail-item {
            border-radius: 24px;
            padding: 1.4rem;
            background: var(--light-card);
            border: 1px solid rgba(255, 255, 255, 0.45);
            box-shadow: 10px 10px 24px rgba(148, 163, 184, 0.18), -8px -8px 24px rgba(255, 255, 255, 0.82);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: background var(--transition-base), border var(--transition-base), box-shadow var(--transition-base);
        }

        .detail-item strong {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--light-muted);
        }

        .detail-item span {
            font-size: clamp(0.95rem, 1.1vw + 0.6rem, 1.05rem);
            font-weight: 500;
            line-height: 1.4;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        body[data-theme="dark"] .detail-item {
            background: var(--dark-card);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 10px 10px 24px rgba(2, 6, 23, 0.68), -8px -8px 24px rgba(148, 163, 184, 0.08);
        }

        body[data-theme="dark"] .detail-item strong {
            color: var(--dark-muted);
        }

        .cta-area {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            justify-content: space-between;
        }

        .cta-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.85rem 1.4rem;
            border-radius: 18px;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform var(--transition-base), box-shadow var(--transition-base), background var(--transition-base);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.85), rgba(20, 184, 166, 0.9));
            color: white;
            box-shadow: 10px 10px 24px rgba(79, 70, 229, 0.28), -10px -10px 24px rgba(255, 255, 255, 0.6);
        }

        .btn:hover {
            transform: translateY(-4px);
            box-shadow: 12px 12px 28px rgba(79, 70, 229, 0.33), -12px -12px 28px rgba(255, 255, 255, 0.72);
        }

        .btn.secondary {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.85), rgba(13, 148, 136, 0.9));
        }

        .btn.outline {
            background: transparent;
            color: inherit;
            border: 1px solid rgba(99, 102, 241, 0.32);
            box-shadow: 10px 10px 24px rgba(148, 163, 184, 0.18), -10px -10px 24px rgba(255, 255, 255, 0.7);
        }

        .btn svg {
            width: 20px;
            height: 20px;
        }

        .card-muted {
            font-size: 0.9rem;
            color: var(--light-muted);
            margin: 0;
        }

        body[data-theme="dark"] .card-muted {
            color: var(--dark-muted);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .module-chip {
            padding: 0.7rem 1rem;
            border-radius: 16px;
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.18);
            font-size: 0.92rem;
            text-transform: capitalize;
            letter-spacing: 0.02em;
        }

        body[data-theme="dark"] .module-chip {
            background: rgba(139, 92, 246, 0.12);
            border: 1px solid rgba(139, 92, 246, 0.24);
        }

        footer {
            text-align: center;
            font-size: 0.9rem;
            color: var(--light-muted);
        }

        body[data-theme="dark"] footer {
            color: var(--dark-muted);
        }

        /* Modal Styles */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(12px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 40;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-shell {
            width: min(960px, 90vw);
            max-height: min(85vh, 720px);
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 20px 20px 48px rgba(15, 23, 42, 0.28);
            border: 1px solid rgba(255, 255, 255, 0.65);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        body[data-theme="dark"] .modal-shell {
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(148, 163, 184, 0.24);
            box-shadow: 20px 20px 48px rgba(2, 6, 23, 0.75);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 1.6rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .modal-body {
            padding: 1.2rem 1.6rem;
            overflow: auto;
            background: rgba(255, 255, 255, 0.55);
        }

        body[data-theme="dark"] .modal-body {
            background: rgba(15, 23, 42, 0.55);
        }

        .modal-body table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .modal-body table td,
        .modal-body table th {
            padding: 0.4rem 0.6rem;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .modal-body table th {
            background: rgba(99, 102, 241, 0.1);
            text-align: left;
        }

        body[data-theme="dark"] .modal-body table th {
            background: rgba(139, 92, 246, 0.18);
        }

        /* phpinfo override */
        .modal-body .phpinfo-table {
            margin-bottom: 1rem;
        }

        .modal-body .phpinfo-row {
            background: transparent !important;
        }

        .modal-body .phpinfo-cell {
            font-size: 0.9rem;
            background: transparent !important;
        }

        @media (max-width: 768px) {
            .page-shell {
                margin: 2rem auto;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .cta-area {
                flex-direction: column;
                align-items: flex-start;
            }

            .modal-shell {
                border-radius: 20px;
            }
        }

        @media (max-width: 520px) {
            .brand-logo {
                width: 48px;
                height: 48px;
            }

            .brand-logo svg {
                width: 24px;
                height: 24px;
            }

            .toggle-switch {
                transform: scale(0.9);
            }
        }
    </style>
</head>
<body data-theme="light">
    <div class="page-shell">
        <div class="glass-panel">
            <header>
                <div class="top-bar">
                    <div class="brand">
                        <div class="brand-logo" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img" aria-label="LAMP Icon">
                                <path d="M3 9a9 9 0 1118 0c0 3.53-2.03 6.58-5 8.01V21a1 1 0 01-1.52.85L12 20l-2.48 1.85A1 1 0 018 21v-3.99A9.01 9.01 0 013 9zm9-7a7 7 0 00-7 7c0 2.79 1.64 5.31 4.19 6.41.47.2.81.65.81 1.17V19l2-1.49a1 1 0 011.18 0L15 19v-2.42c0-.52.34-.97.81-1.17A7 7 0 0012 2zm0 5a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                        </div>
                        <div>
                            <h1>LAMP Dashboard</h1>
                            <p class="subtitle">One gateway to manage your <?php echo htmlspecialchars($osName, ENT_QUOTES, 'UTF-8'); ?> server, complete with shortcuts and stylish stack insights.</p>
                        </div>
                    </div>
                    <div class="theme-toggle" role="presentation">
                        <span>Theme</span>
                        <div class="toggle-switch" id="themeToggle" aria-label="Toggle theme" role="switch" aria-checked="false">
                            <div class="toggle-thumb"></div>
                        </div>
                    </div>
                </div>

                <div class="quick-links" aria-label="Quick access shortcuts">
                    <a class="quick-card" href="/phpmyadmin" target="_blank" rel="noopener">
                        <span class="quick-icon" aria-hidden="true">🗃️</span>
                        <h3>phpMyAdmin</h3>
                        <span>Manage your MySQL/MariaDB databases visually.</span>
                    </a>
                    <button class="quick-card<?php echo $phpinfoAllowed ? '' : ' disabled'; ?>" id="openPhpinfo" type="button">
                        <span class="quick-icon" aria-hidden="true">ℹ️</span>
                        <h3>PHP Info</h3>
                        <span><?php echo $phpinfoAllowed ? 'Explore complete PHP configuration in an interactive pop-up.' : 'Available only from localhost for security.'; ?></span>
                    </button>
                    <a class="quick-card" href="https://www.php.net/docs.php" target="_blank" rel="noopener">
                        <span class="quick-icon" aria-hidden="true">📚</span>
                        <h3>PHP Documentation</h3>
                        <span>Jump straight into the official docs to dive deeper.</span>
                    </a>
                    <a class="quick-card" href="https://httpd.apache.org/docs/" target="_blank" rel="noopener">
                        <span class="quick-icon" aria-hidden="true">🔥</span>
                        <h3>Apache Docs</h3>
                        <span>Reference guides and modules for tuning your web server.</span>
                    </a>
                </div>
            </header>
        </div>

        <div class="glass-panel">
            <div class="info-grid">
                <div class="info-card">
                    <h4>PHP Version</h4>
                    <span><?php echo htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8'); ?></span>
                    <p class="card-muted">Stay updated to keep your stack secure and compatible.</p>
                </div>
                <div class="info-card">
                    <h4>Server</h4>
                    <span><?php echo htmlspecialchars($serverSoftware, ENT_QUOTES, 'UTF-8'); ?></span>
                    <p class="card-muted">Powered by <?php echo htmlspecialchars($osName, ENT_QUOTES, 'UTF-8'); ?> + Apache.</p>
                </div>
                <div class="info-card">
                    <h4>PHP Extensions</h4>
                    <span><?php echo $extensionCount; ?></span>
                    <p class="card-muted">Ready to support a wide range of application needs.</p>
                </div>
                <div class="info-card">
                    <h4>Database Engine</h4>
                    <span><?php echo htmlspecialchars($databaseLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                    <p class="card-muted">Current database server identified from available drivers.</p>
                </div>
            </div>
        </div>

        <div class="glass-panel">
            <h2>Stack Details</h2>
            <div class="stack-details">
                <div class="detail-item">
                    <strong>Server Name</strong>
                    <span><?php echo htmlspecialchars($serverName, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="detail-item">
                    <strong>Document Root</strong>
                    <span><?php echo htmlspecialchars($documentRoot, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="detail-item">
                    <strong>Client IP</strong>
                    <span><?php echo htmlspecialchars($clientIp, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="detail-item">
                    <strong>Default DB Host</strong>
                    <span><?php echo htmlspecialchars($databaseHost, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>
            <p class="card-muted" style="margin-top: 1.2rem;">Popular active extensions:</p>
            <div class="modules-grid">
                <?php
                $topExtensions = array_slice($loadedExtensions, 0, 12);
                foreach ($topExtensions as $extension): ?>
                    <div class="module-chip"><?php echo htmlspecialchars($extension, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
                <?php if ($extensionCount > 12): ?>
                    <div class="module-chip">+<?php echo $extensionCount - 12; ?> more</div>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            &copy; <?php echo date('Y'); ?> LAMP Dashboard — Crafted with neumorphism & glassmorphism for a smooth dev experience.
        </footer>
    </div>

    <?php if ($phpinfoAllowed): ?>
    <div class="modal-backdrop" id="phpinfoModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal-shell">
            <div class="modal-header">
                <h3>PHP Info</h3>
                <button class="modal-close" type="button" id="closeModal" aria-label="Close PHP info">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7a1 1 0 00-1.41 1.41L10.59 12l-4.9 4.89a1 1 0 101.41 1.41L12 13.41l4.89 4.9a1 1 0 001.41-1.41L13.41 12l4.9-4.89a1 1 0 000-1.4z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="phpinfoContent">
                <?php echo $phpinfo; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const phpinfoAllowed = <?php echo $phpinfoAllowed ? 'true' : 'false'; ?>;
        const openPhpinfoButtons = [
            document.getElementById('openPhpinfo'),
            document.getElementById('ctaPhpinfo')
        ];
        const phpinfoModal = phpinfoAllowed ? document.getElementById('phpinfoModal') : null;
        const closeModalBtn = phpinfoAllowed ? document.getElementById('closeModal') : null;
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        const storedTheme = localStorage.getItem('lamp-theme');

        function setTheme(theme) {
            document.body.setAttribute('data-theme', theme);
            const isDark = theme === 'dark';
            themeToggle.setAttribute('aria-checked', isDark);
            localStorage.setItem('lamp-theme', theme);
        }

        if (storedTheme) {
            setTheme(storedTheme);
        } else if (prefersDarkScheme.matches) {
            setTheme('dark');
        }

        themeToggle.addEventListener('click', () => {
            const current = document.body.getAttribute('data-theme');
            setTheme(current === 'dark' ? 'light' : 'dark');
        });

        prefersDarkScheme.addEventListener('change', (event) => {
            if (!storedTheme) {
                setTheme(event.matches ? 'dark' : 'light');
            }
        });

        function openModal() {
            if (!phpinfoAllowed || !phpinfoModal) {
                alert('PHP info is restricted to local access for security.');
                return;
            }
            phpinfoModal.classList.add('active');
            phpinfoModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!phpinfoAllowed || !phpinfoModal) {
                return;
            }
            phpinfoModal.classList.remove('active');
            phpinfoModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        openPhpinfoButtons.forEach(btn => {
            if (btn && phpinfoAllowed) {
                btn.addEventListener('click', openModal);
            } else if (btn && !phpinfoAllowed) {
                btn.addEventListener('click', openModal);
            }
        });

        if (phpinfoAllowed && closeModalBtn && phpinfoModal) {
            closeModalBtn.addEventListener('click', closeModal);

            phpinfoModal.addEventListener('click', (event) => {
                if (event.target === phpinfoModal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && phpinfoModal.classList.contains('active')) {
                    closeModal();
                }
            });
        }
    </script>
</body>
</html>
