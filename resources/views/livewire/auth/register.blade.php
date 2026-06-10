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
            /* min-height: 100vh; */
            /* display: flex; */
            /* overflow: hidden */
        }

        /* Left panel */
        .auth-left {
            width: 88%;
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

<div class="auth-shell grid min-h-screen grid-cols-1 lg:grid-cols-[420px_1fr] xl:grid-cols-[720px_1fr]">

    {{-- Left: plan chooser --}}
    <div class="hidden lg:block sticky top-0 h-screen overflow-hidden">
        <x-auth.register.auth-left />
    </div>

    {{-- Right: sign up form --}}
    <x-auth.register.auth-right />
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
    </script>
@endpush