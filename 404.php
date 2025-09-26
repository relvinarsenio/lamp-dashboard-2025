<?php
http_response_code(404);
$requestedUri = $_SERVER['REQUEST_URI'] ?? '';
$requestedUri = is_string($requestedUri) ? $requestedUri : '';
$requestedUri = htmlspecialchars($requestedUri, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Page Not Found — LAMP Dashboard</title>
    <style>
        :root {
            --font-family: 'Poppins', 'Segoe UI', system-ui, -apple-system, sans-serif;
            --transition-base: 0.3s ease;

            --light-bg: linear-gradient(135deg, #eef2ff, #f8fafc);
            --light-surface: rgba(255, 255, 255, 0.74);
            --light-card: rgba(248, 250, 252, 0.86);
            --light-text: #0f172a;
            --light-muted: rgba(15, 23, 42, 0.65);
            --primary: #6366f1;
            --secondary: #14b8a6;

            --dark-bg: linear-gradient(135deg, #0f172a, #1e293b);
            --dark-surface: rgba(15, 23, 42, 0.7);
            --dark-card: rgba(30, 41, 59, 0.72);
            --dark-text: #f8fafc;
            --dark-muted: rgba(226, 232, 240, 0.72);
            --dark-border: rgba(148, 163, 184, 0.28);
            --dark-primary: #8b5cf6;
            --dark-secondary: #22d3ee;

            --shadow-lg: 16px 16px 40px rgba(148, 163, 184, 0.25), -16px -16px 40px rgba(255, 255, 255, 0.85);
            --dark-shadow-lg: 18px 18px 44px rgba(2, 6, 23, 0.65), -14px -14px 40px rgba(148, 163, 184, 0.14);
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-family);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light-bg);
            color: var(--light-text);
            padding: clamp(1.5rem, 4vw, 3rem);
            transition: background var(--transition-base), color var(--transition-base);
        }

        body[data-theme="dark"] {
            background: var(--dark-bg);
            color: var(--dark-text);
        }

        .card-shell {
            width: min(900px, 96vw);
            background: var(--light-surface);
            border-radius: 32px;
            padding: clamp(2rem, 5vw, 3.4rem);
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(24px);
            display: grid;
            gap: clamp(1.5rem, 3vw, 2.5rem);
        }

        body[data-theme="dark"] .card-shell {
            background: var(--dark-surface);
            border: 1px solid var(--dark-border);
            box-shadow: var(--dark-shadow-lg);
        }

        .status-tag {
            align-self: flex-start;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.24);
            color: var(--primary);
        }

        body[data-theme="dark"] .status-tag {
            background: rgba(139, 92, 246, 0.18);
            border: 1px solid var(--dark-border);
            color: var(--dark-primary);
        }

        h1 {
            margin: 0;
            font-size: clamp(2.4rem, 6vw, 3.6rem);
            line-height: 1.1;
            font-weight: 600;
        }

        p.lead {
            margin: 0;
            font-size: clamp(1rem, 2.6vw, 1.25rem);
            color: var(--light-muted);
            max-width: 60ch;
        }

        body[data-theme="dark"] p.lead {
            color: var(--dark-muted);
        }

        .uri-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 1rem;
            padding: 0.65rem 1.1rem;
            border-radius: 999px;
            background: rgba(20, 184, 166, 0.12);
            border: 1px solid rgba(20, 184, 166, 0.2);
            color: var(--secondary);
            font-size: 0.95rem;
        }

        body[data-theme="dark"] .uri-chip {
            background: rgba(34, 211, 238, 0.18);
            border: 1px solid rgba(34, 211, 238, 0.22);
            color: var(--dark-secondary);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.85rem 1.45rem;
            border-radius: 18px;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform var(--transition-base), box-shadow var(--transition-base);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.9), rgba(20, 184, 166, 0.92));
            color: white;
            box-shadow: 18px 18px 40px rgba(79, 70, 229, 0.28), -16px -16px 36px rgba(255, 255, 255, 0.7);
        }

        .btn:hover {
            transform: translateY(-4px);
        }

        body[data-theme="dark"] .btn {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.88), rgba(34, 211, 238, 0.88));
            color: var(--dark-text);
            box-shadow: 18px 18px 40px rgba(2, 6, 23, 0.6), -16px -16px 36px rgba(148, 163, 184, 0.2);
        }

        .btn.secondary {
            background: transparent;
            color: var(--primary);
            border: 1px solid rgba(99, 102, 241, 0.28);
            box-shadow: 14px 14px 32px rgba(148, 163, 184, 0.18), -12px -12px 32px rgba(255, 255, 255, 0.72);
        }

        body[data-theme="dark"] .btn.secondary {
            background: rgba(30, 41, 59, 0.72);
            color: var(--dark-primary);
            border: 1px solid rgba(139, 92, 246, 0.32);
            box-shadow: 14px 14px 32px rgba(2, 6, 23, 0.55), -12px -12px 32px rgba(148, 163, 184, 0.14);
        }

        .suggestions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.2rem;
        }

        .suggestion-card {
            padding: 1.3rem;
            border-radius: 22px;
            background: var(--light-card);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 10px 10px 28px rgba(148, 163, 184, 0.2), -10px -10px 26px rgba(255, 255, 255, 0.8);
            display: grid;
            gap: 0.5rem;
        }

        .suggestion-card strong {
            font-size: 1rem;
        }

        .suggestion-card span {
            font-size: 0.92rem;
            color: var(--light-muted);
        }

        body[data-theme="dark"] .suggestion-card {
            background: var(--dark-card);
            border: 1px solid var(--dark-border);
            box-shadow: 10px 10px 28px rgba(2, 6, 23, 0.6), -10px -10px 26px rgba(148, 163, 184, 0.12);
        }

        body[data-theme="dark"] .suggestion-card span {
            color: var(--dark-muted);
        }

        footer {
            font-size: 0.85rem;
            color: var(--light-muted);
        }

        body[data-theme="dark"] footer {
            color: var(--dark-muted);
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--light-muted);
        }

        body[data-theme="dark"] .theme-toggle {
            color: var(--dark-muted);
        }

        .toggle-switch {
            position: relative;
            width: 56px;
            height: 28px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.65);
            background: rgba(99, 102, 241, 0.25);
            display: inline-flex;
            align-items: center;
            padding: 0 4px;
            cursor: pointer;
            transition: background var(--transition-base), border var(--transition-base), box-shadow var(--transition-base);
            box-shadow: inset 4px 4px 10px rgba(15, 23, 42, 0.08), inset -4px -4px 10px rgba(255, 255, 255, 0.4);
        }

        .toggle-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(145deg, #ffffff, #dfe6ff);
            box-shadow: 3px 3px 8px rgba(15, 23, 42, 0.2), -3px -3px 8px rgba(255, 255, 255, 0.6);
            transition: transform var(--transition-base), background var(--transition-base), box-shadow var(--transition-base);
        }

        body[data-theme="dark"] .toggle-switch {
            background: rgba(139, 92, 246, 0.28);
            border: 1px solid var(--dark-border);
            box-shadow: inset 4px 4px 12px rgba(2, 6, 23, 0.6), inset -4px -4px 10px rgba(148, 163, 184, 0.12);
        }

        body[data-theme="dark"] .toggle-thumb {
            transform: translateX(26px);
            background: linear-gradient(145deg, #1e293b, #111827);
            box-shadow: 3px 3px 8px rgba(2, 6, 23, 0.6), -3px -3px 8px rgba(148, 163, 184, 0.14);
        }

        @media (max-width: 540px) {
            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body data-theme="light">
    <main class="card-shell" role="presentation">
        <div class="top-bar">
            <span class="status-tag">Error 404</span>
            <div class="theme-toggle" role="presentation">
                <span>Theme</span>
                <button class="toggle-switch" id="themeToggle" type="button" role="switch" aria-checked="false">
                    <span class="toggle-thumb"></span>
                </button>
            </div>
        </div>
        <header>
            <h1>Oops! That page drifted away.</h1>
            <p class="lead">We couldn't find the resource you were looking for. Let's get you back on track with one of these options.</p>
            <?php if ($requestedUri !== ''): ?>
                <div class="uri-chip" aria-label="Requested path">
                    <span>Requested:</span>
                    <code><?php echo $requestedUri; ?></code>
                </div>
            <?php endif; ?>
        </header>

        <div class="actions">
            <a class="btn" href="/"><span aria-hidden="true">🏠</span>Return to Dashboard</a>
            <a class="btn secondary" href="/phpmyadmin" target="_blank" rel="noopener"><span aria-hidden="true">🗃️</span>Open phpMyAdmin</a>
        </div>

        <section class="suggestions" aria-label="Helpful next steps">
            <div class="suggestion-card">
                <strong>Check the URL</strong>
                <span>Typos happen! Make sure the address is spelled correctly.</span>
            </div>
            <div class="suggestion-card">
                <strong>Review server configs</strong>
                <span>Verify your Apache vhost and document root are pointed to the right place.</span>
            </div>
            <div class="suggestion-card">
                <strong>Inspect recent deploys</strong>
                <span>Did a new change move or rename this file? Roll back or update links.</span>
            </div>
        </section>

        <footer>&copy; <?php echo date('Y'); ?> LAMP Dashboard — Crafted with care for smooth sessions.</footer>
    </main>
    <script>
        (function () {
            const body = document.body;
            const toggle = document.getElementById('themeToggle');
            if (!toggle) {
                return;
            }

            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

            function setTheme(theme, persist = true) {
                const normalized = theme === 'dark' ? 'dark' : 'light';
                body.setAttribute('data-theme', normalized);
                toggle.setAttribute('aria-checked', normalized === 'dark');
                if (persist) {
                    try {
                        localStorage.setItem('lamp-theme', normalized);
                    } catch (error) {
                        console.warn('Unable to persist theme preference', error);
                    }
                }
            }

            const storedTheme = (() => {
                try {
                    return localStorage.getItem('lamp-theme');
                } catch (error) {
                    console.warn('Unable to read theme preference', error);
                    return null;
                }
            })();

            if (storedTheme) {
                setTheme(storedTheme, false);
            } else if (prefersDark.matches) {
                setTheme('dark', false);
            }

            toggle.addEventListener('click', () => {
                const current = body.getAttribute('data-theme');
                setTheme(current === 'dark' ? 'light' : 'dark');
            });

            prefersDark.addEventListener('change', (event) => {
                const hasStored = (() => {
                    try {
                        return localStorage.getItem('lamp-theme') !== null;
                    } catch (error) {
                        console.warn('Unable to access theme storage', error);
                        return false;
                    }
                })();

                if (!hasStored) {
                    setTheme(event.matches ? 'dark' : 'light', false);
                }
            });
        })();
    </script>
</body>
</html>
