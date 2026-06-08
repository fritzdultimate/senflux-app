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
            color: #c8c8e0
        }

        body {
            overflow-y: auto;
            background-image: linear-gradient(rgba(123, 92, 245, .025) 1px, transparent 1px), linear-gradient(90deg, rgba(123, 92, 245, .025) 1px, transparent 1px);
            background-size: 44px 44px
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
            --b: rgba(255, 255, 255, .07);
            --bp: rgba(123, 92, 245, .22);
            --t1: #fff;
            --t2: #c8c8e0;
            --t3: #7a7a9a;
            --t4: #4a4a6a;
        }

        ::-webkit-scrollbar {
            width: 3px
        }

        ::-webkit-scrollbar-thumb {
            background: var(--p);
            border-radius: 2px
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
            overflow: hidden
        }

        /* Left panel */
        .auth-left {
            width: 48%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            background: var(--bg2);
            border-right: 1px solid var(--b);
            display: flex;
            flex-direction: column;
            padding: 36px 44px
        }

        .auth-left-bg {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(ellipse 70% 55% at 20% 35%, rgba(123, 92, 245, .18), transparent 60%), radial-gradient(ellipse 50% 40% at 90% 75%, rgba(16, 185, 129, .1), transparent 55%)
        }

        /* Plan cards */
        .plan-card {
            border-radius: 14px;
            padding: 18px 20px;
            cursor: pointer;
            transition: all .22s;
            border: 1.5px solid var(--b);
            background: rgba(255, 255, 255, .03);
            position: relative;
            overflow: hidden
        }

        .plan-card:hover {
            border-color: rgba(123, 92, 245, .38);
            background: rgba(123, 92, 245, .06)
        }

        .plan-card.selected {
            border-color: var(--p);
            background: rgba(123, 92, 245, .1)
        }

        .plan-card.selected::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(123, 92, 245, .12), transparent);
            pointer-events: none
        }

        .plan-card.pro-card.selected {
            border-color: #F59E0B;
            background: rgba(245, 158, 11, .08)
        }

        .plan-card.pro-card.selected::before {
            background: linear-gradient(135deg, rgba(245, 158, 11, .1), transparent)
        }

        .plan-radio {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid var(--b);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s
        }

        .plan-card.selected .plan-radio {
            border-color: var(--p);
            background: var(--p)
        }

        .plan-card.pro-card.selected .plan-radio {
            border-color: #F59E0B;
            background: #F59E0B
        }

        /* Form elements (same as signin) */
        .form-group {
            margin-bottom: 15px
        }

        .lbl {
            display: block;
            font-size: 12.5px;
            color: var(--t3);
            margin-bottom: 6px;
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
            transition: border-color .2s, background .2s
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
            gap: 8px
        }

        .btn-submit:hover {
            box-shadow: 0 6px 28px rgba(123, 92, 245, .65);
            transform: translateY(-1px)
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0
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
            gap: 10px
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
            width: 17px;
            height: 17px;
            border-radius: 5px;
            border: 1.5px solid var(--b);
            background: rgba(255, 255, 255, .04);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
            margin-top: 1.5px
        }

        .checkbox-wrap.checked .checkbox-box {
            background: var(--p);
            border-color: var(--p)
        }

        /* Strength bar */
        .strength-bar {
            height: 3px;
            border-radius: 2px;
            transition: width .4s ease, background .4s ease
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

        .b-y {
            background: rgba(245, 158, 11, .12);
            color: var(--yellow);
            border: 1px solid rgba(245, 158, 11, .22)
        }

        .b-free {
            background: rgba(16, 185, 129, .12);
            color: var(--green);
            border: 1px solid rgba(16, 185, 129, .22)
        }

        .b-pro {
            background: rgba(245, 158, 11, .12);
            color: var(--yellow);
            border: 1px solid rgba(245, 158, 11, .22)
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-8px)
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

        .fade-up-d6 {
            animation: fadeUp .5s ease .48s forwards;
            opacity: 0
        }

        /* Ticker */
        .ticker-strip {
            display: flex;
            gap: 20px;
            overflow: hidden;
            padding: 8px 0;
            border-top: 1px solid var(--b);
            margin-top: auto
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

        /* Right panel */
        .auth-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 24px;
            overflow-y: auto
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            padding-bottom: 40px
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

    <!-- ═══ LEFT PANEL ═══ -->
    <div class="auth-left">
        <div class="auth-left-bg"></div>

        <!-- Logo -->
        <a 
            href="{{ route('home') }}"
            style="display:flex;align-items:center;gap:10px;text-decoration:none;position:relative;z-index:2;flex-shrink:0"
        >
            <x-senflux.logo width="28" height="28" gradient-id="lgnav" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.14em]">SENFLUX</span>
        </a>

        <!-- Plan chooser -->
        <div 
            style="position:relative;z-index:2;flex:1;display:flex;flex-direction:column;justify-content:center;padding:32px 0" class="">
            <p style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--t4);margin-bottom:14px;font-weight:600">
                Choose your plan
            </p>

            <!-- Free plan -->
            <div class="plan-card selected" id="planFree" onclick="selectPlan('free')" style="margin-bottom:10px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="plan-radio" id="radioFree"><svg width="8" height="8" fill="white" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg></div>
                        <div>
                            <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#fff">Free
                                Plan</div>
                            <div style="font-size:11.5px;color:var(--t4);margin-top:2px">Get started immediately</div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:#fff">$0<span
                                style="font-size:12px;color:var(--t3)">/mo</span></div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px">
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>8 free signals per day</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>Basic formation feed</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>1 trading bot</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t4)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.4"
                            stroke-linecap="round">
                            <path d="M2 2L11 11M11 2L2 11" />
                        </svg>Pro terminal access</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t4)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.4"
                            stroke-linecap="round">
                            <path d="M2 2L11 11M11 2L2 11" />
                        </svg>Whale cluster alerts</div>
                </div>
            </div>

            <!-- Pro plan -->
            <div class="plan-card pro-card" id="planPro" onclick="selectPlan('pro')">
                <div
                    style="position:absolute;top:-1px;right:18px;background:linear-gradient(135deg,#F59E0B,#F97316);color:#fff;font-family:'Syne',sans-serif;font-size:10px;font-weight:700;padding:3px 10px;border-radius:0 0 7px 7px;letter-spacing:.06em">
                    MOST POPULAR</div>
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="plan-radio" id="radioPro"></div>
                        <div>
                            <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#fff">Pro
                                Plan</div>
                            <div style="font-size:11.5px;color:var(--t4);margin-top:2px">Full intelligence access</div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:var(--yellow)">
                            $49<span style="font-size:12px;color:var(--t3)">/mo</span></div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px">
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>16 pro signals per day</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>Full Terminal + BirdEye/DexScreener</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>5 bots · Whale cluster alerts</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>18% APY staking + Telegram alerts</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)"><svg
                            width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 6.5L5 10L11.5 3" />
                        </svg>73% win rate · Priority support</div>
                </div>
            </div>
        </div>

        <!-- Bottom ticker -->
        <div style="position:relative;z-index:2;flex-shrink:0">
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
                <div class="tk-item"><span class="tk-sym">XRP</span><span class="tk-p">$0.6423</span><span class="dn"
                        style="font-size:10.5px">-1.5%</span></div>
            </div>
        </div>
    </div>

    <!-- ═══ RIGHT PANEL — SIGN UP FORM ═══ -->
    <div class="auth-right">
        <div class="auth-card">

            <!-- Header -->
            <div class="fade-up" style="margin-bottom:26px">
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
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                    <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:#fff">Create your
                        account</h2>
                    <span class="badge b-free" id="planBadge" style="font-size:9.5px">FREE</span>
                </div>
                <p style="font-size:13.5px;color:var(--t3)">Join 50,000+ participants tracking market formation</p>
            </div>

            <!-- Social sign up -->
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

            <div class="divider fade-up-d1"><span>or sign up with email</span></div>

            <!-- Form -->
            <form onsubmit="handleSignUp(event)">

                <!-- Name row -->
                <div class="fade-up-d2" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:15px">
                    <div>
                        <label class="lbl">First name</label>
                        <input class="inp" type="text" placeholder="John" id="fname" autocomplete="given-name" />
                    </div>
                    <div>
                        <label class="lbl">Last name</label>
                        <input class="inp" type="text" placeholder="Doe" id="lname" autocomplete="family-name" />
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group fade-up-d2">
                    <label class="lbl">Email address</label>
                    <div class="inp-wrap">
                        <div class="inp-icon"><svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                                stroke="#4a4a6a" stroke-width="1.3">
                                <rect x="1" y="3.5" width="14" height="10" rx="2" />
                                <path d="M1 5.5L8 9.5L15 5.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></div>
                        <input class="inp" type="email" placeholder="you@example.com" id="email" autocomplete="email" />
                    </div>
                </div>

                <!-- Referral code (optional) -->
                <div class="form-group fade-up-d3">
                    <label class="lbl">Referral code <span
                            style="color:var(--t4);font-weight:400">(optional)</span></label>
                    <div class="inp-wrap">
                        <div class="inp-icon"><svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                                stroke="#4a4a6a" stroke-width="1.3">
                                <circle cx="5" cy="5" r="2.5" />
                                <circle cx="11" cy="5" r="2.5" />
                                <circle cx="8" cy="11" r="2.5" />
                                <path d="M7 5H9M6.5 7.5L7.5 9.5M9.5 7.5L8.5 9.5" stroke-linecap="round" />
                            </svg></div>
                        <input class="inp" type="text" placeholder="Enter referral code" id="refcode" autocomplete="off"
                            style="text-transform:uppercase" />
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group fade-up-d3">
                    <label class="lbl">Password</label>
                    <div class="inp-wrap">
                        <div class="inp-icon"><svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                                stroke="#4a4a6a" stroke-width="1.3">
                                <rect x="3" y="7" width="10" height="8" rx="1.5" />
                                <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round" />
                                <circle cx="8" cy="11" r="1.2" fill="#4a4a6a" />
                            </svg></div>
                        <input class="inp" type="password" placeholder="Create a strong password" id="password"
                            oninput="checkStrength()" autocomplete="new-password" />
                        <span class="inp-suffix" onclick="togglePwd('password','eye1')">
                            <svg id="eye1" width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor"
                                stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                                <circle cx="8" cy="8" r="2.2" />
                            </svg>
                        </span>
                    </div>
                    <!-- Strength meter -->
                    <div style="margin-top:8px">
                        <div style="display:flex;gap:4px;margin-bottom:5px">
                            <div
                                style="flex:1;height:3px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden">
                                <div id="s1" class="strength-bar" style="width:0;background:var(--red)"></div>
                            </div>
                            <div
                                style="flex:1;height:3px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden">
                                <div id="s2" class="strength-bar" style="width:0;background:var(--yellow)"></div>
                            </div>
                            <div
                                style="flex:1;height:3px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden">
                                <div id="s3" class="strength-bar" style="width:0;background:var(--green)"></div>
                            </div>
                            <div
                                style="flex:1;height:3px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden">
                                <div id="s4" class="strength-bar" style="width:0;background:var(--green)"></div>
                            </div>
                        </div>
                        <p id="strengthLabel" style="font-size:11.5px;color:var(--t4)">At least 8 characters, one
                            number, one special character</p>
                    </div>
                </div>

                <!-- Confirm password -->
                <div class="form-group fade-up-d4">
                    <label class="lbl">Confirm password</label>
                    <div class="inp-wrap">
                        <div class="inp-icon"><svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                                stroke="#4a4a6a" stroke-width="1.3">
                                <rect x="3" y="7" width="10" height="8" rx="1.5" />
                                <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round" />
                                <path d="M6 11.5L7.5 13L10 10.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></div>
                        <input class="inp" type="password" placeholder="Repeat your password" id="password2"
                            autocomplete="new-password" />
                        <span class="inp-suffix" onclick="togglePwd('password2','eye2')">
                            <svg id="eye2" width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor"
                                stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                                <circle cx="8" cy="8" r="2.2" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="fade-up-d4" style="display:flex;flex-direction:column;gap:11px;margin-bottom:20px">
                    <label class="checkbox-wrap" id="termsWrap" onclick="toggleCheck('termsWrap','termsIcon')">
                        <input type="checkbox" id="terms" />
                        <div class="checkbox-box"><svg id="termsIcon" width="9" height="9" fill="none" viewBox="0 0 9 9"
                                stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                style="opacity:0;transition:opacity .15s">
                                <path d="M1 4.5L3.5 7L8 2" />
                            </svg></div>
                        <span style="font-size:12.5px;color:var(--t3)">I agree to the <a href="#"
                                style="color:var(--pl);text-decoration:none" onclick="return false">Terms of Service</a>
                            and <a href="#" style="color:var(--pl);text-decoration:none" onclick="return false">Privacy
                                Policy</a></span>
                    </label>
                    <label class="checkbox-wrap" id="mktWrap" onclick="toggleCheck('mktWrap','mktIcon')">
                        <input type="checkbox" id="marketing" checked />
                        <div class="checkbox-box" style="background:var(--p);border-color:var(--p)"><svg id="mktIcon"
                                width="9" height="9" fill="none" viewBox="0 0 9 9" stroke="white" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="opacity:1;transition:opacity .15s">
                                <path d="M1 4.5L3.5 7L8 2" />
                            </svg></div>
                        <span style="font-size:12.5px;color:var(--t3)">Send me signal alerts and market updates</span>
                    </label>
                </div>

                <!-- Submit -->
                <div class="fade-up-d5">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="white" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 4v4l3 1.5" />
                        </svg>
                        <span id="btnLabel">Create Free Account</span>
                    </button>
                </div>
            </form>

            <!-- Sign in link -->
            <div class="fade-up-d6" style="text-align:center;margin-top:20px">
                <span style="font-size:13.5px;color:var(--t3)">Already have an account? </span>
                <a href="signin.html"
                    style="font-size:13.5px;color:var(--pl);text-decoration:none;font-weight:600;transition:color .2s"
                    onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--pl)'">Sign in →</a>
            </div>

            <!-- Error/success -->
            <div id="msg"
                style="display:none;margin-top:14px;padding:11px 14px;border-radius:9px;font-size:13px;text-align:center">
            </div>

            <!-- Trust badges -->
            <div class="fade-up-d6" style="margin-top:28px;padding-top:20px;border-top:1px solid var(--b)">
                <div style="display:flex;align-items:center;justify-content:center;gap:18px;flex-wrap:wrap">
                    <div style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--t4)">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.5 1L11.5 3.5V7C11.5 10 9.5 12.4 6.5 13.5C3.5 12.4 1.5 10 1.5 7V3.5z" />
                        </svg>
                        SSL Secured
                    </div>
                    <div style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--t4)">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6.5" cy="6.5" r="5.5" />
                            <path d="M4.5 6.5L6 8L9 5" />
                        </svg>
                        No credit card required
                    </div>
                    <div style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--t4)">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.5 1v4.5l2.5 2M11.5 6.5a5 5 0 1 1-10 0 5 5 0 0 1 10 0z" />
                        </svg>
                        Cancel anytime
                    </div>
                </div>
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

        /* Plan selection */
        let selectedPlan = 'free';
        function selectPlan(plan) {
            selectedPlan = plan;
            const freeCard = document.getElementById('planFree');
            const proCard = document.getElementById('planPro');
            const radioFree = document.getElementById('radioFree');
            const radioPro = document.getElementById('radioPro');
            const badge = document.getElementById('planBadge');
            const btnLabel = document.getElementById('btnLabel');
            const submitBtn = document.getElementById('submitBtn');

            if (plan === 'free') {
                freeCard.classList.add('selected');
                proCard.classList.remove('selected');
                radioFree.innerHTML = '<svg width="8" height="8" fill="white" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>';
                radioPro.innerHTML = '';
                badge.className = 'badge b-free'; badge.textContent = 'FREE';
                btnLabel.textContent = 'Create Free Account';
                submitBtn.style.background = 'linear-gradient(135deg,#7B5CF5,#4F46E5)';
                submitBtn.style.boxShadow = '0 4px 18px rgba(123,92,245,.45)';
            } else {
                proCard.classList.add('selected');
                freeCard.classList.remove('selected');
                radioPro.innerHTML = '<svg width="8" height="8" fill="white" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>';
                radioFree.innerHTML = '';
                badge.className = 'badge b-y'; badge.textContent = 'PRO';
                btnLabel.textContent = 'Start Pro Trial — $49/mo';
                submitBtn.style.background = 'linear-gradient(135deg,#F59E0B,#F97316)';
                submitBtn.style.boxShadow = '0 4px 18px rgba(245,158,11,.45)';
            }
        }

        /* Password strength */
        function checkStrength() {
            const v = document.getElementById('password').value;
            const s1 = document.getElementById('s1'), s2 = document.getElementById('s2'), s3 = document.getElementById('s3'), s4 = document.getElementById('s4');
            const lbl = document.getElementById('strengthLabel');
            let score = 0;
            if (v.length >= 8) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;
            const configs = [
                { w: '0%', c: 'var(--red)' }, { w: '0%', c: 'var(--red)' },
                { w: '0%', c: 'var(--yellow)' }, { w: '0%', c: 'var(--green)' }
            ];
            const fill = {
                0: { label: 'Too short', color: 'var(--t4)' },
                1: { label: 'Weak', color: 'var(--red)' },
                2: { label: 'Fair', color: 'var(--yellow)' },
                3: { label: 'Good', color: 'var(--cyan)' },
                4: { label: 'Strong', color: 'var(--green)' }
            };
            [s1, s2, s3, s4].forEach((bar, i) => { bar.style.width = i < score ? '100%' : '0'; bar.style.background = fill[score]?.color || 'var(--green)' });
            lbl.textContent = fill[score]?.label || ''; lbl.style.color = fill[score]?.color || 'var(--t4)';
        }

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
            eye.setAttribute('fill', 'none'); eye.setAttribute('viewBox', '0 0 16 16'); eye.setAttribute('stroke', 'currentColor'); eye.setAttribute('stroke-width', '1.3');
        }

        /* Checkbox toggle */
        function toggleCheck(wrapId, iconId) {
            const wrap = document.getElementById(wrapId);
            const icon = document.getElementById(iconId);
            wrap.classList.toggle('checked');
            if (icon) icon.style.opacity = wrap.classList.contains('checked') ? '1' : '0';
        }
        /* Init marketing checkbox as checked */
        document.getElementById('mktWrap').classList.add('checked');

        /* Form submit */
        function handleSignUp(e) {
            e.preventDefault();
            const msg = document.getElementById('msg');
            const btn = document.getElementById('submitBtn');
            const fname = document.getElementById('fname').value;
            const email = document.getElementById('email').value;
            const pass = document.getElementById('password').value;
            const pass2 = document.getElementById('password2').value;
            const terms = document.getElementById('terms').checked;

            msg.style.display = 'block';

            if (!fname || !email || !pass || !pass2) {
                msg.style.background = 'rgba(244,63,94,.1)'; msg.style.border = '1px solid rgba(244,63,94,.22)'; msg.style.color = '#F43F5E';
                msg.textContent = 'Please fill in all required fields.'; return;
            }
            if (pass !== pass2) {
                msg.style.background = 'rgba(244,63,94,.1)'; msg.style.border = '1px solid rgba(244,63,94,.22)'; msg.style.color = '#F43F5E';
                msg.textContent = 'Passwords do not match.'; return;
            }
            if (!terms) {
                msg.style.background = 'rgba(245,158,11,.1)'; msg.style.border = '1px solid rgba(245,158,11,.22)'; msg.style.color = '#F59E0B';
                msg.textContent = 'Please accept the Terms of Service to continue.'; return;
            }

            msg.style.display = 'none';
            btn.disabled = true;
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="rgba(255,255,255,.3)" stroke-width="2"/><path d="M8 2A6 6 0 0 1 14 8" stroke="white" stroke-width="2" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 8 8" to="360 8 8" dur=".7s" repeatCount="indefinite"/></path></svg> Creating account…';

            setTimeout(() => {
                msg.style.display = 'block';
                msg.style.background = 'rgba(16,185,129,.1)'; msg.style.border = '1px solid rgba(16,185,129,.22)'; msg.style.color = '#10B981';
                msg.textContent = '✓ Account created! Redirecting to dashboard…';
                setTimeout(() => { window.location.href = 'index.html'; }, 1400);
            }, 1800);
        }
    </script>
@endpush