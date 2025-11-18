<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlloSSO • Dashboard</title>
    <style>
        :root {
            color-scheme: dark;
            --color-bg: linear-gradient(135deg, #1f284b 0%, #445c88 65%, #ff7a00 120%);
            --color-surface: rgba(9, 12, 24, 0.82);
            --color-border: rgba(255, 255, 255, 0.18);
            --color-tab: rgba(255, 255, 255, 0.08);
            --color-tab-active: rgba(255, 255, 255, 0.18);
            --color-text: #f5f7ff;
            --color-muted: rgba(230, 236, 255, 0.68);
            --accent: #ff7a00;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-bg);
            color: var(--color-text);
            padding: clamp(18px, 4vw, 32px);
        }

        .shell {
            width: min(520px, 100%);
            background: var(--color-surface);
            border-radius: 24px;
            border: 1px solid var(--color-border);
            backdrop-filter: blur(24px);
            box-shadow: 0 40px 70px rgba(6, 9, 20, 0.45);
            overflow: hidden;
            display: grid;
            grid-template-rows: auto 1fr auto;
            height: min(100vh - clamp(36px, 8vw, 64px), 680px);
        }

        .shell header {
            padding: 22px clamp(16px, 4vw, 24px) 16px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(1.6rem, 2.4vw, 2.1rem);
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .brand h1 span {
            color: var(--accent);
        }

        .brand p {
            margin: 10px 0 0;
            color: var(--color-muted);
            font-size: 0.92rem;
        }

        .logout form {
            display: inline-flex;
        }

        .logout button {
            border: none;
            border-radius: 14px;
            padding: 12px 22px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            background: #ff7a00;
            color: #11152c;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .logout button:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(255, 122, 0, 0.28);
        }

        nav {
            display: flex;
            gap: 12px;
            padding: 16px clamp(16px, 4vw, 24px);
            overflow-x: auto;
            scrollbar-width: thin;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        nav button {
            appearance: none;
            border: none;
            border-radius: 16px;
            padding: 14px 24px;
            background: var(--color-tab);
            color: var(--color-muted);
            font-size: 0.95rem;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
            transition: background 140ms ease, color 140ms ease, transform 140ms ease;
        }

        nav button[aria-selected="true"] {
            background: var(--color-tab-active);
            color: var(--color-text);
            transform: translateY(-2px);
        }

        nav button:hover {
            background: rgba(255, 255, 255, 0.12);
            color: var(--color-text);
        }

        .content {
            padding: clamp(16px, 4vw, 24px) clamp(16px, 4vw, 24px) 0;
            display: grid;
            gap: 0;
            overflow-y: auto;
            scrollbar-width: thin;
            align-content: start;
        }

        .tab-panel {
            display: none;
            gap: 24px;
            align-content: start;
        }

        .tab-panel.is-active {
            display: grid;
            align-content: start;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top-color: var(--color-accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .section-header {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        h2 {
            margin: 0;
            font-size: clamp(1.3rem, 2vw, 1.6rem);
            font-weight: 600;
        }

        .grid {
            display: grid;
            gap: 20px;
        }

        .grid.access {
            display: none;
        }

        .access-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 16px;
        }

        .access-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 20, 40, 0.65);
        }

        .access-label {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 1.02rem;
            font-weight: 600;
        }

        .access-icon {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
        }

        .access-meta {
            display: none;
        }

        .access-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.08);
            color: var(--color-text);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
        }

        .card {
            background: rgba(12, 16, 32, 0.72);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 22px;
            display: grid;
            gap: 12px;
        }

        .card header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card header span {
            font-weight: 600;
            font-size: 1.05rem;
        }

        .badge-exclusive {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 182, 73, 0.16);
            color: #ffd697;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid rgba(255, 182, 73, 0.42);
        }

        .card p {
            margin: 0;
            color: var(--color-muted);
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            color: rgba(209, 215, 238, 0.68);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .two-column {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            color: var(--color-muted);
            font-size: 0.9rem;
        }

        .table th,
        .table td {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            text-align: left;
        }

        .table th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(226, 232, 255, 0.52);
        }

        .status-online {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #8ce9b6;
            font-size: 0.85rem;
        }

        .status-online::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #8ce9b6;
        }

        .support-links {
            display: grid;
            gap: 12px;
        }

        .support-links a {
            color: #ffcf8f;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        @media (max-width: 720px) {
            nav {
                padding-bottom: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header>
            <div class="brand">
                <h1>Allo<span>SSO</span></h1>
                <p>Unified access fabric for every workspace.</p>
            </div>
            <div class="logout">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Sign Out</button>
                </form>
            </div>
        </header>

        <div class="content">
            <section class="tab-panel is-active" data-panel="access" aria-label="Access Overview">
                <div class="section-header">
                    <h2>Access Overview</h2>
                    @if (session('status'))
                        <div style="font-size: 0.9rem; color: rgba(220, 245, 255, 0.9);">{{ session('status') }}</div>
                    @endif
                </div>
                <ul class="access-list">
                    {{-- Public Access - Available to all users --}}
                    <li class="access-item">
                        <div class="access-label">
                            <span class="access-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6H4v12h16V6Z" stroke="#ffcf8f" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M4 10h16" stroke="#ffcf8f" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            allolancer.com
                        </div>
                        <a class="access-action" href="#" onclick="handleLogin('https://allolancer.com', event); return false;">Login</a>
                    </li>
                    <li class="access-item">
                        <div class="access-label">
                            <span class="access-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 3 4 9v12h6v-6h4v6h6V9l-8-6Z" stroke="#8ce9b6" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            allohubai.com
                        </div>
                        <a class="access-action" href="#" onclick="handleLogin('https://allohubai.com', event); return false;">Login</a>
                    </li>
                    <li class="access-item">
                        <div class="access-label">
                            <span class="access-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 6h14v4H5V6Zm0 6h14v6H5v-6Z" stroke="#9cc9ff" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M9 6v10" stroke="#9cc9ff" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            allo-learn.com
                        </div>
                        <a class="access-action" href="#" onclick="handleLogin('https://allo-learn.com', event); return false;">Login</a>
                    </li>

                    {{-- Exclusive Access - Only if granted in database --}}
                    @if($user->access_erp ?? false)
                    <li class="access-item">
                        <div class="access-label">
                            <span class="access-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 4h16v12H4V4Z" stroke="#ffd697" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M4 12h16" stroke="#ffd697" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            ERP
                            <span class="badge-exclusive">Exclusive Access</span>
                        </div>
                        <a class="access-action" href="#" onclick="handleLogin('https://allo-sso.com/erp/allosso.php', event); return false;">Login</a>
                    </li>
                    @endif

                    @if($user->access_admin_portal ?? false)
                    <li class="access-item">
                        <div class="access-label">
                            <span class="access-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 6h12v12H6V6Z" stroke="#ffd697" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path d="M9 9h6v6H9V9Z" stroke="#ffd697" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            Admin Portal
                            <span class="badge-exclusive">Exclusive Access</span>
                        </div>
                        <a class="access-action" href="#" onclick="handleLogin('https://admin.allosso.com'); return false;">Login</a>
                    </li>
                    @endif

                    @if($user->access_ai_developer ?? false)
                    <li class="access-item">
                        <div class="access-label">
                            <span class="access-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 4v16M4 12h16" stroke="#ffd697" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            AI Developer
                            <span class="badge-exclusive">Exclusive Access</span>
                        </div>
                        <a class="access-action" href="#" onclick="handleLogin('https://ai-dev.allosso.com'); return false;">Login</a>
                    </li>
                    @endif
                </ul>
            </section>

            <section class="tab-panel" data-panel="profile" aria-label="Profile">
                <div class="section-header">
                    <h2>User Profile</h2>
                </div>
                {{-- <article class="card">
                    <p>Name: {{ $user->name }}</p>
                    <p>Email: {{ $user->email }}</p>
                    @if($user->phone)
                    <p>Phone: {{ $user->phone }}</p>
                    @endif
                    <p>Security posture: <span class="status-online">Verified</span></p>
                </article> --}}
                <article class="card" style="text-align: center; padding: 40px 20px; display: flex; align-items: center; justify-content: center;">
                    <div class="loading-spinner"></div>
                </article>
            </section>

            <section class="tab-panel" data-panel="devices" aria-label="Active Devices">
                <div class="section-header">
                    <h2>Active Devices</h2>
                </div>
                {{-- <article class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>MacBook Pro · Chrome</td>
                                <td>Tehran, IR</td>
                                <td><span class="status-online">Online</span></td>
                            </tr>
                            <tr>
                                <td>iPhone 15 · Safari</td>
                                <td>Tehran, IR</td>
                                <td style="color:#ffd38f;">Idle</td>
                            </tr>
                        </tbody>
                    </table>
                </article> --}}
                <article class="card" style="text-align: center; padding: 40px 20px; display: flex; align-items: center; justify-content: center;">
                    <div class="loading-spinner"></div>
                </article>
            </section>

            <section class="tab-panel" data-panel="audit" aria-label="Audit Log">
                <div class="section-header">
                    <h2>Login & Sign-out Log</h2>
                </div>
                {{-- <article class="card">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Event</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-11-10 13:58</td>
                                <td>Signed in via OTP</td>
                                <td>127.0.0.1</td>
                            </tr>
                            <tr>
                                <td>2025-11-10 13:47</td>
                                <td>Signed out</td>
                                <td>127.0.0.1</td>
                            </tr>
                        </tbody>
                    </table>
                </article> --}}
                <article class="card" style="text-align: center; padding: 40px 20px; display: flex; align-items: center; justify-content: center;">
                    <div class="loading-spinner"></div>
                </article>
            </section>

            <section class="tab-panel" data-panel="support" aria-label="Support">
                <div class="section-header">
                    <h2>Support</h2>
                </div>
                {{-- <article class="card">
                    <p>Need help? Connect with the AlloSSO platform team.</p>
                    <div class="support-links">
                        <a href="mailto:support@allolancer.com">support@allolancer.com</a>
                        <a href="tel:+9821123456">+98 21 123 456</a>
                        <a href="#">Open support workspace</a>
                    </div>
                </article> --}}
                <article class="card" style="text-align: center; padding: 40px 20px; display: flex; align-items: center; justify-content: center;">
                    <div class="loading-spinner"></div>
                </article>
            </section>
        </div>

        <nav role="tablist" aria-label="Dashboard sections">
            <button type="button" aria-selected="true">Access Overview</button>
            <button type="button" aria-selected="false">Profile</button>
            <button type="button" aria-selected="false">Active Devices</button>
            <button type="button" aria-selected="false">Audit Log</button>
            <button type="button" aria-selected="false">Support</button>
        </nav>
    </div>

    <script>
        const tabButtons = Array.from(document.querySelectorAll('nav button'));
        const panels = Array.from(document.querySelectorAll('.tab-panel'));

        const panelMap = {
            'Access Overview': 'access',
            'Profile': 'profile',
            'Active Devices': 'devices',
            'Audit Log': 'audit',
            'Support': 'support',
        };

        function activatePanel(targetKey) {
            tabButtons.forEach((button) => {
                const isActive = panelMap[button.textContent.trim()] === targetKey;
                button.setAttribute('aria-selected', String(isActive));
            });

            panels.forEach((panel) => {
                panel.classList.toggle('is-active', panel.dataset.panel === targetKey);
            });
        }

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const key = panelMap[button.textContent.trim()];
                if (key) {
                    activatePanel(key);
                }
            });
        });

        activatePanel('access');

        // Handle login for all services - prevents URL copying
        function handleLogin(url, event) {
            const allohash = '{{ $user->allohash }}';
            
            // Prevent right-click and copy
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Build URL with allohash as GET parameter
            const separator = url.includes('?') ? '&' : '?';
            const finalUrl = url + separator + 'allohash=' + encodeURIComponent(allohash);
            
            // Redirect immediately - URL won't be visible in address bar before redirect
            window.location.href = finalUrl;
        }

        // Disable right-click context menu on access actions
        document.addEventListener('DOMContentLoaded', function() {
            const accessActions = document.querySelectorAll('.access-action');
            accessActions.forEach(function(action) {
                action.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });
                
                // Prevent text selection
                action.style.userSelect = 'none';
                action.style.webkitUserSelect = 'none';
                action.style.mozUserSelect = 'none';
                action.style.msUserSelect = 'none';
            });
        });
    </script>
</body>
</html>
