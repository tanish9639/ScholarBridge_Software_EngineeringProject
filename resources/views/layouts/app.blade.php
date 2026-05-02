<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Thapar LMS' }}</title>
    <style>
        :root {
            --bg: #e9dfd2;
            --panel: #ffffff;
            --ink: #501419;
            --muted: #7e685f;
            --line: #dfd1c4;
            --brand: #b11226;
            --brand-dark: #7e0e1b;
            --brand-soft: #f4dfdc;
            --accent: #dc2626;
            --success: #2f855a;
            --warning: #d97706;
            --danger: #991b1b;
            --glow: rgba(177, 18, 38, 0.14);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Trebuchet MS", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(177, 18, 38, 0.1), transparent 22rem),
                radial-gradient(circle at bottom right, rgba(228, 210, 197, 0.65), transparent 24rem),
                linear-gradient(180deg, #f4ede5 0%, var(--bg) 100%);
        }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1180px, calc(100% - 2rem)); margin: 0 auto; }
        .hero, .shell { padding: 2rem 0 3rem; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center; gap: 1rem;
            padding: 1rem 0 0.5rem;
        }
        .brand {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--brand-dark);
        }
        .nav { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
        .btn, button {
            background: linear-gradient(135deg, var(--brand), #d11c33);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 0.8rem 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 12px 24px var(--glow);
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }
        .btn:hover, button:hover {
            transform: translateY(-1px);
            filter: saturate(1.05);
            box-shadow: 0 16px 28px rgba(177, 18, 38, 0.24);
        }
        .btn.secondary, button.secondary {
            background: white;
            color: var(--brand-dark);
            border: 1px solid #efc0c7;
            box-shadow: none;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 1.3rem;
            box-shadow: 0 18px 40px rgba(129, 18, 32, 0.08);
            position: relative;
            overflow: hidden;
        }
        .panel::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--brand), transparent 70%);
        }
        .panel h2, .panel h3 { margin-top: 0; }
        .metric { font-size: 2rem; font-weight: 700; margin: 0.2rem 0; }
        .muted { color: var(--muted); }
        .badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            border-radius: 999px; padding: 0.35rem 0.7rem;
            background: var(--brand-soft); color: var(--brand-dark); font-size: 0.85rem; font-weight: 700;
            border: 1px solid #f3c5cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.75rem;
            font-size: 0.95rem;
        }
        th, td { padding: 0.8rem 0.65rem; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        th { color: var(--muted); font-weight: 700; }
        input, select, textarea {
            width: 100%;
            padding: 0.82rem 0.95rem;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: white;
            font: inherit;
            color: var(--ink);
        }
        input:focus, select:focus, textarea:focus {
            outline: 3px solid rgba(220, 38, 38, 0.14);
            border-color: #d45767;
        }
        textarea { min-height: 120px; resize: vertical; }
        label { display: block; font-size: 0.92rem; font-weight: 600; margin-bottom: 0.35rem; }
        .field + .field { margin-top: 0.85rem; }
        .section-title { display: flex; justify-content: space-between; gap: 1rem; align-items: center; }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .actions-inline {
            display: flex;
            gap: 0.6rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .btn.small, button.small {
            padding: 0.55rem 0.9rem;
            font-size: 0.92rem;
        }
        .menu {
            position: relative;
        }
        .menu summary {
            list-style: none;
            cursor: pointer;
        }
        .menu summary::-webkit-details-marker { display: none; }
        .menu-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 0.6rem);
            min-width: 250px;
            background: white;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 18px 34px rgba(80, 20, 25, 0.12);
            padding: 0.9rem;
            z-index: 20;
        }
        .menu-panel a, .menu-panel button {
            display: block;
            width: 100%;
            text-align: left;
            margin-top: 0.4rem;
        }
        .menu-panel form { margin-top: 0.4rem; }
        .menu-title { font-weight: 700; margin-bottom: 0.25rem; }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 0.9rem;
        }
        .detail-card {
            background: linear-gradient(180deg, #fffefd, #f9f1ea);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 1rem;
        }
        .detail-card strong {
            display: block;
            margin-bottom: 0.25rem;
            color: var(--brand-dark);
        }
        .flash {
            margin-bottom: 1rem;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: #fff2f4;
            border: 1px solid #f5ccd2;
            color: var(--brand-dark);
            font-weight: 600;
        }
        .error-list {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 18px;
            background: #fff3f3;
            border: 1px solid #f2b2b8;
            color: #8d2020;
        }
        .alert-card { border-left: 6px solid var(--brand); }
        .alert-warning { border-left-color: var(--warning); }
        .alert-critical { border-left-color: var(--danger); }
        .alert-info { border-left-color: var(--brand); }
        .cards-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1rem;
        }
        dialog {
            max-width: 28rem;
            border: none;
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 26px 60px rgba(0, 0, 0, 0.25);
        }
        dialog::backdrop { background: rgba(9, 21, 34, 0.45); }
        .dialog-body { padding: 1.4rem; }
        .hero-card {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 16rem),
                linear-gradient(135deg, #8f1020, #b11226 55%, #d11c33 100%);
            color: white;
            border-radius: 32px;
            padding: 2.25rem;
            box-shadow: 0 28px 60px rgba(129, 18, 32, 0.28);
        }
        .hero-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.2rem; }
        .pillars { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1.2rem; }
        .pillar {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 20px;
            padding: 1rem;
            backdrop-filter: blur(6px);
        }
        code {
            background: #fff1f3;
            border: 1px solid #f0c3c9;
            color: var(--brand-dark);
            padding: 0.15rem 0.4rem;
            border-radius: 8px;
        }
        .surface-accent {
            background: linear-gradient(180deg, #fffefe, #fff3f4);
            border: 1px solid #f4cfd5;
        }
        @media (max-width: 640px) {
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <a class="brand" href="{{ route('landing') }}">Thapar LMS</a>
            <div class="nav">
                @auth
                    <a class="btn small secondary" href="{{ route(auth()->user()->dashboardRouteName()) }}">Dashboard</a>
                    <details class="menu">
                        <summary class="badge">
                            {{ auth()->user()->name }}
                            @if (auth()->user()->departmentName())
                                · {{ auth()->user()->departmentName() }}
                            @endif
                        </summary>
                        <div class="menu-panel">
                            <div class="menu-title">{{ ucfirst(auth()->user()->role) }} Profile</div>
                            @if (auth()->user()->departmentName())
                                <p class="muted" style="margin: 0 0 0.75rem;">Department: {{ auth()->user()->departmentName() }}</p>
                            @endif
                            @if (auth()->user()->role !== 'admin')
                                <a class="btn small secondary" href="{{ route(auth()->user()->role.'.profile.show') }}">Personal Details</a>
                                <a class="btn small secondary" href="{{ route(auth()->user()->role.'.profile.edit') }}">Edit Details</a>
                                <a class="btn small secondary" href="{{ route(auth()->user()->role.'.profile.password.edit') }}">Change Password</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="secondary small">Logout</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a class="btn secondary" href="{{ route('login.form', 'student') }}">Student Login</a>
                    <a class="btn" href="{{ route('login.form', 'staff') }}">Staff Login</a>
                    <a class="btn secondary" href="{{ route('login.form', 'admin') }}">Admin Login</a>
                @endauth
            </div>
        </div>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-list">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
