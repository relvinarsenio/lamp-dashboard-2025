# LAMP Dashboard

A sleek, neumorphic & glassmorphic landing page for CentOS-based LAMP stacks. It replaces the stock Apache test page with a responsive dashboard featuring quick shortcuts, live stack insights, dark mode support, and security-minded defaults.

## ✨ Features
- **Stack snapshot** cards for PHP version, server software, loaded extensions, and detected database engine.
- **Dynamic environment details** including hostname, document root, client IP, and database host.
- **Quick actions** to phpMyAdmin, PHP documentation, and Apache docs.
- **Modal PHP info viewer** gated to localhost/private networks for safety.
- **Theme toggle** with persistence and system preference awareness.
- **Custom 404 page** styled to match, complete with guidance and shortcuts.

## 🚀 Getting Started
1. Copy the PHP files into your Apache document root (e.g. `/var/www/html`).
2. Ensure PHP extensions `mysqli` or `pdo_mysql` are installed for database detection.
3. Visit your server in a browser to see the dashboard.

```bash
sudo cp *.php /var/www/html/
```

## 🌓 Dark Mode
The dashboard and 404 page both support manual and automatic dark mode. Preferences are saved per-browser using `localStorage` and fall back to the OS-level theme when unset.

## 🔒 Security Notes
- The PHP info modal only renders for requests from loopback and private IP ranges to avoid leaking configuration data publicly.
- All dynamic output is escaped with `htmlspecialchars` to prevent XSS when reflecting server environment values.

## 🛠 Development
- The project is a single PHP entry point (`index.php`) plus a matching `404.php`.
- Update styling directly in each file's `<style>` block.
- Run a quick syntax check before deploying:

```bash
php -l index.php
php -l 404.php
```

## 📄 License
MIT License. See [LICENSE](LICENSE) if provided, or adapt to your needs.
