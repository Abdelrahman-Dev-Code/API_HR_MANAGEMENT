<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="HR Management dashboard">
        <title>@yield('title', 'HR Management')</title>
        <style>
            :root {
                --bg: #f4f7fb;
                --panel: rgba(255, 255, 255, 0.88);
                --panel-strong: #ffffff;
                --muted: #7a8699;
                --heading: #172033;
                --line: rgba(135, 147, 167, 0.18);
                --primary: #4f46e5;
                --primary-soft: rgba(79, 70, 229, 0.12);
                --blue: #3b82f6;
                --purple: #8b5cf6;
                --green: #22c55e;
                --orange: #f59e0b;
                --red: #ef4444;
                --shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
            }

            * { box-sizing: border-box; }
            html { scroll-behavior: smooth; }
            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: radial-gradient(circle at top left, rgba(79, 70, 229, 0.16), transparent 20%), radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.12), transparent 20%), var(--bg);
                color: var(--heading);
            }
            a { text-decoration: none; }
            button { font: inherit; cursor: pointer; }
            .app-shell { padding: 28px; }
            .dashboard-page {
                max-width: 1500px;
                margin: 0 auto;
                min-height: calc(100vh - 56px);
                display: grid;
                grid-template-columns: 280px minmax(0, 1fr);
                background: rgba(255,255,255,0.5);
                border: 1px solid rgba(148,163,184,0.22);
                border-radius: 32px;
                box-shadow: var(--shadow);
                overflow: hidden;
                backdrop-filter: blur(14px);
            }
            .sidebar {
                background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(30, 41, 59, 0.96));
                color: white;
                padding: 24px 18px;
                display: flex;
                flex-direction: column;
                gap: 24px;
            }
            .brand-box {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 8px 8px 18px;
                border-bottom: 1px solid rgba(255,255,255,0.12);
            }
            .brand-mark {
                width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center;
                background: linear-gradient(145deg, #7c3aed, #4f46e5); font-weight: 800; box-shadow: 0 12px 22px rgba(79,70,229,0.5);
            }
            .eyebrow {
                margin: 0 0 4px; text-transform: uppercase; letter-spacing: 0.12em; font-size: 11px; color: rgba(255,255,255,0.7);
            }
            .brand-box h2 { margin: 0; font-size: 1.05rem; }
            .nav-menu { display: flex; flex-direction: column; gap: 8px; }
            .nav-item {
                display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 14px; color: rgba(255,255,255,0.8);
                transition: all 0.2s ease;
            }
            .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.08); color: white; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.06); }
            .icon {
                width: 28px; height: 28px; display: grid; place-items: center; border-radius: 10px;
                background: rgba(255,255,255,0.06); font-size: 1rem;
            }
            .sidebar-card {
                margin-top: auto; padding: 18px 16px; border-radius: 18px; background: linear-gradient(180deg, rgba(79,70,229,0.28), rgba(30,41,59,0.75));
                border: 1px solid rgba(255,255,255,0.08);
            }
            .sidebar-card h3 { margin: 0 0 10px; font-size: 2rem; }
            .sidebar-card span { color: rgba(255,255,255,0.7); font-size: 0.82rem; }
            .main-panel { padding: 24px 26px 28px; background: rgba(248,250,253,0.9); }
            .topbar, .panel-head, .stat-head, .stat-foot, .topbar-actions { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
            .topbar { margin-bottom: 24px; }
            .topbar h1 { margin: 0; font-size: clamp(1.8rem, 2vw, 2.5rem); }
            .topbar-actions { display: flex; }
            .primary-btn, .ghost-btn, .chip-btn {
                border-radius: 12px; border: 1px solid transparent; padding: 10px 16px; font-weight: 600; transition: 0.2s ease;
            }
            .primary-btn {
                background: linear-gradient(135deg, #4f46e5, #3b82f6); color: white; box-shadow: 0 12px 22px rgba(79,70,229,0.28);
            }
            .primary-btn:hover { transform: translateY(-1px); }
            .ghost-btn, .chip-btn { background: rgba(255,255,255,0.65); color: var(--heading); border-color: var(--line); }
            .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(180px, 1fr)); gap: 18px; margin-bottom: 22px; }
            .stat-card {
                position: relative; overflow: hidden; padding: 18px 18px 16px; border: 1px solid rgba(148,163,184,0.18); border-radius: 20px; background: var(--panel); box-shadow: 0 12px 24px rgba(15,23,42,0.04);
            }
            .accent-blue::before, .accent-purple::before, .accent-green::before, .accent-orange::before {
                content: ''; position: absolute; inset: 0 auto auto 0; width: 100%; height: 4px;
            }
            .accent-blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
            .accent-purple::before { background: linear-gradient(90deg, #8b5cf6, #c084fc); }
            .accent-green::before { background: linear-gradient(90deg, #22c55e, #4ade80); }
            .accent-orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
            .stat-head { margin-bottom: 14px; }
            .stat-label { color: var(--muted); font-size: 0.78rem; }
            .trend { display: inline-flex; padding: 5px 8px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }
            .trend.up { background: rgba(34,197,94,0.12); color: #15803d; }
            .trend.down { background: rgba(239,68,68,0.1); color: #b91c1c; }
            .stat-card h3 { margin: 0; font-size: clamp(1.8rem, 2vw, 2.3rem); }
            .stat-foot { margin-top: 16px; font-size: 0.8rem; color: var(--muted); }
            .stat-foot strong { color: var(--heading); }
            .content-grid, .bottom-grid { display: grid; grid-template-columns: 1.7fr 0.95fr; gap: 20px; }
            .panel-card {
                background: rgba(255,255,255,0.88); border: 1px solid rgba(148,163,184,0.18); border-radius: 22px; box-shadow: 0 10px 24px rgba(15,23,42,0.03); padding: 20px;
            }
            .large-panel { min-height: 320px; }
            .panel-head { margin-bottom: 16px; }
            .panel-head h3 { margin: 0; font-size: 1.15rem; }
            .chart-area { padding-top: 10px; }
            .chart-bars {
                height: 220px; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 12px; align-items: end; padding: 20px 10px 8px; border-radius: 18px; background: linear-gradient(180deg, rgba(79,70,229,0.04), rgba(255,255,255,0));
            }
            .chart-bars span { display: block; height: var(--h); border-radius: 12px 12px 8px 8px; background: linear-gradient(180deg, #8b5cf6, #4f46e5); box-shadow: inset 0 -8px 15px rgba(255,255,255,0.22); }
            .chart-labels {
                display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 12px; margin-top: 10px; color: var(--muted); font-size: 0.75rem; text-align: center;
            }
            .ring-wrap { display: grid; place-items: center; padding: 14px 0 8px; }
            .progress-ring {
                width: 170px; height: 170px; border-radius: 50%; background: conic-gradient(#4f46e5 0 73%, #e2e8f0 73% 100%); display: grid; place-items: center; position: relative;
            }
            .progress-ring::before { content: ''; width: 118px; height: 118px; background: white; border-radius: 50%; position: absolute; }
            .ring-center { position: relative; z-index: 1; display: grid; place-items: center; }
            .ring-center strong { font-size: 1.9rem; }
            .ring-center span { color: var(--muted); font-size: 0.78rem; }
            .list-stats { list-style: none; margin: 0; padding: 10px 0 0; display: grid; gap: 10px; }
            .list-stats li { display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border-radius: 12px; background: rgba(148,163,184,0.06); color: var(--muted); }
            .list-stats strong { color: var(--heading); }
            .bottom-grid { margin-top: 20px; }
            .table-panel { overflow: hidden; }
            table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
            th, td { text-align: left; padding: 14px 12px; border-bottom: 1px solid rgba(148,163,184,0.12); }
            th { font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); }
            .user-cell { display: flex; align-items: center; gap: 10px; }
            .avatar { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 50%; color: white; font-size: 0.8rem; font-weight: 800; }
            .avatar-one { background: linear-gradient(135deg, #f59e0b, #ef4444); }
            .avatar-two { background: linear-gradient(135deg, #8b5cf6, #4f46e5); }
            .avatar-three { background: linear-gradient(135deg, #22c55e, #16a34a); }
            .avatar-four { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
            .badge { display: inline-flex; padding: 6px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }
            .badge.success { background: rgba(34,197,94,0.12); color: #166534; }
            .badge.warning { background: rgba(245,158,11,0.12); color: #92400e; }
            .badge.danger { background: rgba(239,68,68,0.1); color: #991b1b; }
            .task-list { display: grid; gap: 14px; padding-top: 4px; }
            .task-item { display: flex; gap: 12px; padding: 12px 12px; border-radius: 14px; background: rgba(148,163,184,0.06); }
            .task-item h4 { margin: 0 0 3px; font-size: 0.98rem; }
            .task-item p { margin: 0; color: var(--muted); font-size: 0.8rem; }
            .task-dot { display: block; width: 12px; height: 12px; border-radius: 50%; margin-top: 8px; }
            .task-dot.blue { background: #3b82f6; }
            .task-dot.purple { background: #8b5cf6; }
            .task-dot.green { background: #22c55e; }
            .page-shell { padding: 24px; }
            .login-card {
                max-width: 430px; margin: 80px auto; background: rgba(255,255,255,0.88); border-radius: 28px; padding: 32px; border: 1px solid rgba(148,163,184,0.2); box-shadow: var(--shadow);
            }
            .login-header { text-align: center; margin-bottom: 24px; }
            .login-header h1 { margin: 0; font-size: 2rem; }
            .login-form { display: grid; gap: 16px; }
            .field { display: grid; gap: 8px; }
            .field label { font-weight: 600; color: var(--heading); }
            .field input { width: 100%; border: 1px solid rgba(148,163,184,0.4); border-radius: 12px; padding: 12px 14px; font-size: 1rem; background: rgba(248,250,253,0.8); }
            .submit-btn { margin-top: 8px; width: 100%; border: none; padding: 14px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5, #3b82f6); color: white; font-weight: 700; }
            .subtitle { text-align: center; color: var(--muted); font-size: 0.9rem; }
            .table-toolbar { display: flex; justify-content: space-between; margin-bottom: 16px; }
            .search-box { padding: 10px 14px; border-radius: 12px; border: 1px solid var(--line); background: rgba(248,250,253,0.8); }
            @media (max-width: 1100px) { .dashboard-page { grid-template-columns: 1fr; } .sidebar { padding-bottom: 18px; } .nav-menu { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); } .content-grid, .bottom-grid, .stats-grid { grid-template-columns: 1fr; } }
            @media (max-width: 640px) { .app-shell { padding: 16px; } .main-panel { padding: 18px 16px 22px; } .topbar { align-items: flex-start; flex-direction: column; } .topbar-actions { width: 100%; justify-content: stretch; } .topbar-actions button { flex: 1; } .nav-menu { grid-template-columns: 1fr; } }
        </style>
    </head>
    <body class="app-shell">
        @yield('content')
    </body>
</html>
