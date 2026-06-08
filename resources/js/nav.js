// resources/js/nav.js

/* ─ Sidebar open / close (mobile drawer) ─ */
window.openSidebar = function () {
    document.getElementById('sidebar')?.classList.add('open');
    document.getElementById('sbBackdrop')?.classList.add('open');
    document.body.style.overflow = 'hidden';
};
window.closeSidebar = function () {
    document.getElementById('sidebar')?.classList.remove('open');
    document.getElementById('sbBackdrop')?.classList.remove('open');
    document.body.style.overflow = '';
};
// Close on Escape key
document.addEventListener('keydown', e => { if (e.key === 'Escape') window.closeSidebar(); });

/* ─ Ticker auto-scroll ─ */
(function () {
    const s = document.querySelector('.ticker');
    if (!s) return;
    let dir = 1;
    setInterval(() => {
        s.scrollLeft += dir;
        if (s.scrollLeft >= s.scrollWidth - s.clientWidth) dir = -1;
        if (s.scrollLeft <= 0) dir = 1;
    }, 26);
})();

/* ─ Stat card fade-up ─ */
document.querySelectorAll('.sc-val').forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(8px)';
    setTimeout(() => {
        el.style.transition = 'opacity .45s ease, transform .45s ease';
        el.style.opacity = '1';
        el.style.transform = 'none';
    }, 80 + i * 65);
});

/* ─ Simulated live price updates ─ */
const prices = {
    BTC:  { p: 69174  },
    ETH:  { p: 3482   },
    SOL:  { p: 187.32 },
    BNB:  { p: 608.68 },
    XRP:  { p: 0.6423 },
};
function fmt(v) {
    return v >= 1000 ? '$' + v.toLocaleString('en', { maximumFractionDigits: 2 })
         : v >= 1    ? '$' + v.toFixed(2)
         : '$' + v.toFixed(4);
}
setInterval(() => {
    Object.keys(prices).forEach(k => {
        const r = prices[k];
        r.p *= (1 + (Math.random() - .499) * .004);
        document.querySelectorAll(`[data-price="${k}"]`).forEach(el => {
            el.textContent = fmt(r.p);
        });
    });
}, 2800);

/* ─ Live formation feed row flash ─ */
setInterval(() => {
    const rows = document.querySelectorAll('.feed-row');
    if (!rows.length) return;
    const idx = Math.floor(Math.random() * rows.length);
    rows[idx].style.background = 'rgba(123,92,245,.08)';
    setTimeout(() => { rows[idx].style.background = ''; }, 600);
}, 3200);

/* ─ Live timestamps ─ */
setInterval(() => {
    document.querySelectorAll('.live-ts').forEach(el => {
        el.textContent = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    });
}, 1000);

/* ─ Tab switching ─ */
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const group = tab.closest('.tabs');
        group.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const target = tab.dataset.tab;
        if (target) {
            const container = group.closest('[data-tab-container]') || group.parentElement.parentElement;
            container.querySelectorAll('[data-tab-panel]').forEach(p => {
                p.style.display = p.dataset.tabPanel === target ? 'block' : 'none';
            });
        }
    });
});

/* ─ Highlight active sidebar nav (Livewire SPA-friendly) ─ */
function setActiveNav() {
    const page = location.pathname;
    document.querySelectorAll('.sb-item').forEach(a => {
        a.classList.remove('active');
        const href = a.getAttribute('href');
        if (href && page.includes(href) && href !== '/') a.classList.add('active');
    });
}
setActiveNav();
document.addEventListener('livewire:navigated', setActiveNav);