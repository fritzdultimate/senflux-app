@push('styles')
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html,
        body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: #05050c;
            color: #c8c8e0;
            overflow: hidden
        }

        :root {
            --p: #7B5CF5;
            --pl: #9B7DFF;
            --pd: #4F46E5;
            --green: #10B981;
            --cyan: #06B6D4;
            --yellow: #F59E0B;
            --red: #F43F5E;
            --bg: #05050c;
            --bg2: #080811;
            --bg3: #0d0d1a;
            --b: rgba(255, 255, 255, .07);
            --bp: rgba(123, 92, 245, .22);
            --t1: #fff;
            --t2: #c8c8e0;
            --t3: #7a7a9a;
            --t4: #4a4a6a;
        }

        /* grid bg */
        body {
            background-image: linear-gradient(rgba(123, 92, 245, .025) 1px, transparent 1px), linear-gradient(90deg, rgba(123, 92, 245, .025) 1px, transparent 1px);
            background-size: 44px 44px
        }

        ::-webkit-scrollbar {
            width: 3px
        }

        ::-webkit-scrollbar-thumb {
            background: var(--p);
            border-radius: 2px
        }

        /* ── Layout ── */
        .auth-shell {
            min-height: 100vh;
            display: flex;
            overflow: hidden
        }

        /* ── Left panel ── */
        .auth-left {
            width: 52%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            background: var(--bg2);
            border-right: 1px solid var(--b);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 36px 48px;
        }

        .auth-left-bg {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(ellipse 80% 60% at 30% 40%, rgba(123, 92, 245, .18), transparent 65%),
                radial-gradient(ellipse 50% 40% at 80% 80%, rgba(16, 185, 129, .1), transparent 60%);
        }

        /* Floating orb */
        .orb {
            position: absolute;
            right: -60px;
            top: 50%;
            transform: translateY(-50%);
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle at 38% 32%, rgba(123, 92, 245, .4), rgba(79, 70, 229, .2) 50%, transparent);
            box-shadow: 0 0 120px rgba(123, 92, 245, .25), 0 0 240px rgba(123, 92, 245, .1);
            animation: float 7s ease-in-out infinite;
        }

        .orb-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 1px solid rgba(123, 92, 245, .18);
            animation: spinSlow 18s linear infinite
        }

        .orb-ring2 {
            position: absolute;
            inset: 20px;
            border-radius: 50%;
            border: 1px solid rgba(123, 92, 245, .1)
        }

        /* Stats floating cards */
        .float-card {
            position: absolute;
            background: rgba(8, 8, 17, .88);
            border: 1px solid rgba(123, 92, 245, .2);
            border-radius: 12px;
            padding: 12px 16px;
            backdrop-filter: blur(12px);
            animation: float 6s ease-in-out infinite;
        }

        /* Ticker strip */
        .ticker-strip {
            display: flex;
            gap: 20px;
            overflow: hidden;
            padding: 8px 0;
            border-top: 1px solid var(--b);
            margin-top: auto;
        }

        .tk-item {
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            flex-shrink: 0;
            font-size: 12px
        }

        .tk-sym {
            color: var(--t4);
            font-weight: 500
        }

        .tk-p {
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 500
        }

        .up {
            color: var(--green)
        }

        .dn {
            color: var(--red)
        }

        /* ── Right panel (form) ── */
        .auth-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            overflow-y: auto;
        }

        .auth-card {
            width: 100%;
            max-width: 420px
        }

        /* Form elements */
        .form-group {
            margin-bottom: 16px
        }

        .lbl {
            display: block;
            font-size: 12.5px;
            color: var(--t3);
            margin-bottom: 7px;
            font-weight: 500
        }

        .inp {
            width: 100%;
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--b);
            border-radius: 9px;
            padding: 11px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: #fff;
            outline: none;
            transition: border-color .2s, background .2s;
        }

        .inp:focus {
            border-color: var(--p);
            background: rgba(123, 92, 245, .07)
        }

        .inp::placeholder {
            color: var(--t4)
        }

        .inp-wrap {
            position: relative
        }

        .inp-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none
        }

        .inp-wrap .inp {
            padding-left: 38px
        }

        .inp-suffix {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--t4);
            transition: color .2s
        }

        .inp-suffix:hover {
            color: var(--t2)
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--p), var(--pd));
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 13px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(123, 92, 245, .45);
            transition: all .25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            box-shadow: 0 6px 28px rgba(123, 92, 245, .65);
            transform: translateY(-1px)
        }

        .btn-submit:active {
            transform: translateY(0)
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0
        }

        .divider span {
            font-size: 12px;
            color: var(--t4);
            white-space: nowrap
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--b)
        }

        .btn-social {
            width: 100%;
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--b);
            border-radius: 9px;
            padding: 11px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--t2);
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-social:hover {
            background: rgba(255, 255, 255, .09);
            border-color: rgba(255, 255, 255, .14)
        }

        .checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            cursor: pointer
        }

        .checkbox-box {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid var(--b);
            background: rgba(255, 255, 255, .04);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
            margin-top: 1px;
        }

        .checkbox-wrap input:checked~.checkbox-box,
        .checkbox-wrap.checked .checkbox-box {
            background: var(--p);
            border-color: var(--p)
        }

        .checkbox-wrap input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10.5px;
            font-weight: 600;
            padding: 2.5px 7px;
            border-radius: 5px
        }

        .b-g {
            background: rgba(16, 185, 129, .12);
            color: var(--green);
            border: 1px solid rgba(16, 185, 129, .22)
        }

        .b-p {
            background: rgba(123, 92, 245, .12);
            color: var(--pl);
            border: 1px solid rgba(123, 92, 245, .22)
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-10px)
            }
        }

        @keyframes spinSlow {
            to {
                transform: rotate(360deg)
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .4;
                transform: scale(.65)
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .ap {
            animation: pulse 2.5s ease-in-out infinite
        }

        .fade-up {
            animation: fadeUp .5s ease forwards
        }

        .fade-up-d1 {
            animation: fadeUp .5s ease .08s forwards;
            opacity: 0
        }

        .fade-up-d2 {
            animation: fadeUp .5s ease .16s forwards;
            opacity: 0
        }

        .fade-up-d3 {
            animation: fadeUp .5s ease .24s forwards;
            opacity: 0
        }

        .fade-up-d4 {
            animation: fadeUp .5s ease .32s forwards;
            opacity: 0
        }

        .fade-up-d5 {
            animation: fadeUp .5s ease .4s forwards;
            opacity: 0
        }

        @media(max-width:768px) {
            .auth-left {
                display: none
            }

            .auth-right {
                padding: 24px 20px
            }
        }
    </style>
@endpush

<div class="auth-shell">

    {{--  ═══ LEFT PANEL ═══ --}}
    <div class="auth-left">
        <div class="auth-left-bg"></div>

        <!-- Logo -->
        <a href="index.html"
            style="display:flex;align-items:center;gap:10px;text-decoration:none;position:relative;z-index:2">
            <svg width="30" height="30" viewBox="0 0 100 100" fill="none">
                <defs>
                    <linearGradient id="slg" x1="20" y1="10" x2="80" y2="90" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#9B7DFF" />
                        <stop offset="1" stop-color="#4F46E5" />
                    </linearGradient>
                </defs>
                <path
                    d="M65 18C65 18 80 22 80 38C80 52 66 58 52 55C38 52 28 44 30 33C32 22 46 20 52 28C58 36 52 46 40 44C34 43 30 38 30 38M30 38C30 38 18 48 22 62C26 76 42 82 56 76C70 70 72 56 64 48C58 42 48 42 44 50C40 58 46 66 56 64"
                    stroke="url(#slg)" stroke-width="9" stroke-linecap="round" fill="none" />
            </svg>
            <span
                style="font-family:'Syne',sans-serif;font-weight:700;font-size:16px;color:#fff;letter-spacing:.07em">Sen<span
                    style="color:#9B7DFF">flux</span></span>
        </a>

        <!-- Hero copy -->
        <div
            style="position:relative;z-index:2;flex:1;display:flex;flex-direction:column;justify-content:center;padding:40px 0">
            <div
                style="display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(123,92,245,.26);border-radius:999px;padding:4px 12px;margin-bottom:20px;width:fit-content">
                <span class="ap"
                    style="width:6px;height:6px;border-radius:50%;background:var(--green);display:block;flex-shrink:0"></span>
                <span
                    style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:rgba(155,125,255,.85)">Live
                    Market Intelligence</span>
            </div>
            <h1
                style="font-family:'Syne',sans-serif;font-weight:800;font-size:clamp(1.8rem,3vw,2.8rem);color:#fff;line-height:1.12;margin-bottom:16px">
                See Formation<br />Before the<br /><span
                    style="background:linear-gradient(135deg,#9B7DFF,#7B5CF5,#4F46E5);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Market
                    Does.</span>
            </h1>
            <p style="font-size:14px;color:var(--t3);max-width:340px;line-height:1.7;margin-bottom:32px">Real-time
                on-chain participation intelligence. Track wallet formation, whale clusters, and get pro signals before
                expansion begins.</p>

            <!-- Feature list -->
            <div style="display:flex;flex-direction:column;gap:11px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div
                        style="width:28px;height:28px;border-radius:7px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.22);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7.5L5.5 11L12 4" />
                        </svg>
                    </div>
                    <span style="font-size:13.5px;color:var(--t2)">Live formation feed from BirdEye &amp;
                        DexScreener</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <div
                        style="width:28px;height:28px;border-radius:7px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.22);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7.5L5.5 11L12 4" />
                        </svg>
                    </div>
                    <span style="font-size:13.5px;color:var(--t2)">Whale cluster &amp; wallet cohesion
                        intelligence</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <div
                        style="width:28px;height:28px;border-radius:7px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.22);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7.5L5.5 11L12 4" />
                        </svg>
                    </div>
                    <span style="font-size:13.5px;color:var(--t2)">Automated bots + 73% win rate signals</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <div
                        style="width:28px;height:28px;border-radius:7px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.22);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7.5L5.5 11L12 4" />
                        </svg>
                    </div>
                    <span style="font-size:13.5px;color:var(--t2)">18% APY staking &amp; affiliate rewards</span>
                </div>
            </div>
        </div>

        <!-- Floating stat cards -->
        <div class="float-card" style="right:20px;top:28%;animation-delay:.5s;animation-duration:7s">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px">
                <span class="ap"
                    style="width:6px;height:6px;border-radius:50%;background:var(--green);display:block"></span>
                <span
                    style="font-size:10.5px;color:var(--t3);font-weight:600;text-transform:uppercase;letter-spacing:.08em">Active
                    Wallets</span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#fff">14,682</div>
            <div style="font-size:11px;color:var(--green);margin-top:2px">+18.3% today</div>
        </div>
        <div class="float-card" style="right:20px;top:52%;animation-delay:1.5s;animation-duration:8s">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px">
                <span
                    style="font-size:10.5px;color:var(--t3);font-weight:600;text-transform:uppercase;letter-spacing:.08em">Bot
                    P&L Today</span>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:var(--green)">+$48.14</div>
            <div style="display:flex;gap:5px;margin-top:4px"><span class="badge b-g"
                    style="font-size:9.5px">Running</span></div>
        </div>

        <!-- Bottom ticker -->
        <div style="position:relative;z-index:2">
            <div class="ticker-strip" id="ticker">
                <div class="tk-item"><span class="tk-sym">BTC</span><span class="tk-p">$69,174</span><span class="up"
                        style="font-size:10.5px">+2.4%</span></div>
                <div class="tk-item"><span class="tk-sym">ETH</span><span class="tk-p">$3,482</span><span class="up"
                        style="font-size:10.5px">+1.8%</span></div>
                <div class="tk-item"><span class="tk-sym">SOL</span><span class="tk-p">$187.32</span><span class="dn"
                        style="font-size:10.5px">-0.6%</span></div>
                <div class="tk-item"><span class="tk-sym">WIF</span><span class="tk-p">$2.84</span><span class="up"
                        style="font-size:10.5px">+14.2%</span></div>
                <div class="tk-item"><span class="tk-sym">BONK</span><span class="tk-p">$0.0000281</span><span
                        class="up" style="font-size:10.5px">+8.6%</span></div>
                <div class="tk-item"><span class="tk-sym">ADA</span><span class="tk-p">$0.4871</span><span class="up"
                        style="font-size:10.5px">+3.1%</span></div>
                <div class="tk-item"><span class="tk-sym">XRP</span><span class="tk-p">$0.6423</span><span class="dn"
                        style="font-size:10.5px">-1.5%</span></div>
                <div class="tk-item"><span class="tk-sym">BNB</span><span class="tk-p">$608.68</span><span class="up"
                        style="font-size:10.5px">+1.0%</span></div>
            </div>
        </div>
    </div>

    <!-- ═══ RIGHT PANEL — SIGN IN FORM ═══ -->
    <div class="auth-right">
        <div class="auth-card">

            <!-- Header -->
            <div class="fade-up" style="margin-bottom:28px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                    <svg width="22" height="22" viewBox="0 0 100 100" fill="none">
                        <defs>
                            <linearGradient id="slg2" x1="20" y1="10" x2="80" y2="90" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#9B7DFF" />
                                <stop offset="1" stop-color="#4F46E5" />
                            </linearGradient>
                        </defs>
                        <path
                            d="M65 18C65 18 80 22 80 38C80 52 66 58 52 55C38 52 28 44 30 33C32 22 46 20 52 28C58 36 52 46 40 44C34 43 30 38 30 38M30 38C30 38 18 48 22 62C26 76 42 82 56 76C70 70 72 56 64 48C58 42 48 42 44 50C40 58 46 66 56 64"
                            stroke="url(#slg2)" stroke-width="9" stroke-linecap="round" fill="none" />
                    </svg>
                    <span
                        style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:#fff;letter-spacing:.07em">SENFLUX</span>
                </div>
                <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#fff;margin-bottom:6px">
                    Welcome back</h2>
                <p style="font-size:13.5px;color:var(--t3)">Sign in to your account to continue</p>
            </div>

            <!-- Social sign in -->
            <div class="fade-up-d1" style="display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-bottom:4px">
                <button class="btn-social">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path
                            d="M15.5 8.18c0-.57-.05-1.11-.14-1.64H8v3.1h4.2a3.6 3.6 0 0 1-1.56 2.36v1.96h2.53c1.48-1.36 2.33-3.37 2.33-5.78z"
                            fill="#4285F4" />
                        <path
                            d="M8 16c2.1 0 3.87-.7 5.16-1.88l-2.53-1.96c-.7.47-1.6.75-2.63.75-2.02 0-3.74-1.37-4.35-3.2H1.05v2.02C2.34 14.18 4.98 16 8 16z"
                            fill="#34A853" />
                        <path
                            d="M3.65 9.71A4.87 4.87 0 0 1 3.4 8c0-.59.1-1.17.25-1.71V4.27H1.05A8.02 8.02 0 0 0 0 8c0 1.29.31 2.51.85 3.59l2.8-1.88z"
                            fill="#FBBC05" />
                        <path
                            d="M8 3.18c1.14 0 2.16.39 2.97 1.16l2.22-2.22C11.86.79 10.1 0 8 0 4.98 0 2.34 1.82 1.05 4.27l2.6 2.02C4.26 4.55 5.98 3.18 8 3.18z"
                            fill="#EA4335" />
                    </svg>
                    Google
                </button>
                <button class="btn-social">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="white">
                        <path
                            d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z" />
                    </svg>
                    GitHub
                </button>
            </div>

            <!-- Divider -->
            <div class="divider fade-up-d1"><span>or sign in with email</span></div>

            <!-- Form -->
            <form onsubmit="handleSignIn(event)">
                <div class="form-group fade-up-d2">
                    <label class="lbl">Email address</label>
                    <div class="inp-wrap">
                        <div class="inp-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="#4a4a6a"
                                stroke-width="1.3">
                                <rect x="1" y="3.5" width="14" height="10" rx="2" />
                                <path d="M1 5.5L8 9.5L15 5.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <input class="inp" type="email" placeholder="you@example.com" id="email" autocomplete="email" />
                    </div>
                </div>

                <div class="form-group fade-up-d3">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px">
                        <label class="lbl" style="margin:0">Password</label>
                        <a href="#" style="font-size:12px;color:var(--pl);text-decoration:none;transition:color .2s"
                            onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--pl)'">Forgot
                            password?</a>
                    </div>
                    <div class="inp-wrap">
                        <div class="inp-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="#4a4a6a"
                                stroke-width="1.3">
                                <rect x="3" y="7" width="10" height="8" rx="1.5" />
                                <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round" />
                                <circle cx="8" cy="11" r="1.2" fill="#4a4a6a" />
                            </svg>
                        </div>
                        <input class="inp" type="password" placeholder="Enter your password" id="password"
                            autocomplete="current-password" />
                        <span class="inp-suffix" onclick="togglePwd('password','eye1')" title="Show/hide">
                            <svg id="eye1" width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor"
                                stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                                <circle cx="8" cy="8" r="2.2" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="fade-up-d3" style="margin-bottom:22px">
                    <label class="checkbox-wrap" id="rememberWrap" onclick="toggleCheck('rememberWrap')">
                        <input type="checkbox" id="remember" />
                        <div class="checkbox-box">
                            <svg width="10" height="10" fill="none" viewBox="0 0 10 10" stroke="white"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" id="check-icon"
                                style="opacity:0;transition:opacity .15s">
                                <path d="M1.5 5L4 7.5L8.5 2.5" />
                            </svg>
                        </div>
                        <span style="font-size:13px;color:var(--t3)">Remember me for 30 days</span>
                    </label>
                </div>

                <!-- Submit -->
                <div class="fade-up-d4">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="white" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2H3C2.4 2 2 2.4 2 3V13C2 13.6 2.4 14 3 14H13C13.6 14 14 13.6 14 13V10" />
                            <path d="M9 2H14V7M14 2L8 8" />
                        </svg>
                        Sign In to Senflux
                    </button>
                </div>
            </form>

            <!-- Sign up link -->
            <div class="fade-up-d5" style="text-align:center;margin-top:22px">
                <span style="font-size:13.5px;color:var(--t3)">Don't have an account? </span>
                <a 
                    href="{{ route('register') }}"
                    style="font-size:13.5px;color:var(--pl);text-decoration:none;font-weight:600;transition:color .2s"
                    onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--pl)'"
                >
                    Create one free
                    →
                </a>
            </div>

            <!-- Error / success message placeholder -->
            <div id="msg"
                style="display:none;margin-top:14px;padding:11px 14px;border-radius:9px;font-size:13px;text-align:center">
            </div>

            <!-- Terms footer -->
            <div class="fade-up-d5"
                style="margin-top:28px;padding-top:20px;border-top:1px solid var(--b);text-align:center">
                <p style="font-size:11.5px;color:var(--t4);line-height:1.6">
                    By signing in you agree to our
                    <a href="#" style="color:var(--t3);text-decoration:none;transition:color .2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--t3)'">Terms of
                        Service</a>
                    and
                    <a href="#" style="color:var(--t3);text-decoration:none;transition:color .2s"
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--t3)'">Privacy
                        Policy</a>.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
            /* Ticker scroll */
            (function () {
                const s = document.getElementById('ticker');
                if (!s) return;
                let d = 1;
                setInterval(() => { s.scrollLeft += d; if (s.scrollLeft >= s.scrollWidth - s.clientWidth) d = -1; if (s.scrollLeft <= 0) d = 1; }, 30);
            })();

        /* Toggle password visibility */
        function togglePwd(inputId, eyeId) {
            const inp = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (inp.type === 'password') {
                inp.type = 'text';
                eye.innerHTML = '<path d="M2 2L14 14M6.5 6.7A2.2 2.2 0 0 0 9.3 9.5M4 4.5C2.7 5.6 1.8 6.8 1 8c1.8 2.8 4.2 5 7 5 1.1 0 2.2-.3 3.2-.8M10.5 5.8C11.5 6.5 12.3 7.2 13 8c-.5.8-1.2 1.6-2 2.3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="8" cy="8" r="2.2" stroke="currentColor" stroke-width="1.3"/>';
            } else {
                inp.type = 'password';
                eye.innerHTML = '<path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/><circle cx="8" cy="8" r="2.2"/>';
            }
            eye.setAttribute('fill', 'none');
            eye.setAttribute('viewBox', '0 0 16 16');
            eye.setAttribute('stroke', 'currentColor');
            eye.setAttribute('stroke-width', '1.3');
        }

        /* Checkbox toggle */
        function toggleCheck(wrapId) {
            const wrap = document.getElementById(wrapId);
            const inp = wrap.querySelector('input');
            const icon = document.getElementById('check-icon');
            wrap.classList.toggle('checked');
            inp.checked = !inp.checked;
            if (icon) icon.style.opacity = inp.checked ? '1' : '0';
        }

        /* Form submit */
        function handleSignIn(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const msg = document.getElementById('msg');
            const email = document.getElementById('email').value;
            const pass = document.getElementById('password').value;

            if (!email || !pass) {
                msg.style.display = 'block';
                msg.style.background = 'rgba(244,63,94,.1)';
                msg.style.border = '1px solid rgba(244,63,94,.22)';
                msg.style.color = '#F43F5E';
                msg.textContent = 'Please fill in all fields.';
                return;
            }

            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="rgba(255,255,255,.3)" stroke-width="2"/><path d="M8 2A6 6 0 0 1 14 8" stroke="white" stroke-width="2" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 8 8" to="360 8 8" dur=".7s" repeatCount="indefinite"/></path></svg> Signing in…';
            btn.disabled = true;

            setTimeout(() => {
                msg.style.display = 'block';
                msg.style.background = 'rgba(16,185,129,.1)';
                msg.style.border = '1px solid rgba(16,185,129,.22)';
                msg.style.color = '#10B981';
                msg.textContent = '✓ Signed in successfully! Redirecting to dashboard…';
                setTimeout(() => { window.location.href = 'index.html'; }, 1200);
            }, 1600);
        }
    </script>
@endpush