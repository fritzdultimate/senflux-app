{{-- resources/views/components/senflux/footer.blade.php --}}

<footer class="foot">

    <div style="height:1px;background:linear-gradient(90deg,transparent,rgba(155,125,255,.18),rgba(99,102,241,.12),transparent);"></div>

    <div class="foot-inner" style="padding-top:3rem;padding-bottom:0;flex-direction:column;gap:0;">

        {{-- ── Main footer grid ── --}}
        <div class="sf-foot-grid" style="padding-bottom:3rem;width:100%;">

            {{-- Brand column --}}
            <div class="sf-foot-brand" style="display:flex;flex-direction:column;gap:1rem;">
                <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
                    <x-senflux.logo width="22" height="22" gradient-id="sfFootLogo" />
                    <span class="font-syne font-bold text-[13px] text-white tracking-[.14em]">SENFLUX</span>
                </a>
                <p style="font-size:12.5px;color:#4a4a6a;line-height:1.75;max-width:240px;">
                    Real-time participation intelligence for smarter market deployment.
                </p>
                <div style="display:flex;gap:.75rem;margin-top:.25rem;">
                    {{-- replace the existing @foreach social icons block --}}
                    @foreach([
                        ['X / Twitter', 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L2.25 2.25h6.988l4.26 5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z', false, '#'],
                        ['Telegram', 'M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z', true, 'https://t.me/senfluxai'],
                        ['Discord', 'M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057c.002.022.015.043.034.055a19.9 19.9 0 0 0 5.993 3.03.077.077 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z', false, ''],
                        ['GitHub', 'M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12', false, ''],
                        ['YouTube', 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z', true, 'https://www.youtube.com/@SenFluxAI'],
                        ['Facebook', 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z', false, ''],
                        ['TikTok', 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z', false, ''],
                        ['Instagram', 'M7.75 2C4.574 2 2 4.574 2 7.75v8.5C2 19.426 4.574 22 7.75 22h8.5C19.426 22 22 19.426 22 16.25v-8.5C22 4.574 19.426 2 16.25 2h-8.5zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm6-1.25a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0zM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z', true, 'https://instagram.com/senflux_ai'],
                    ] as [$label, $path, $include, $url])
                        @if($include)
                            <a href="{{ $url }}" target="_blank" aria-label="{{ $label }}" class="sf-social-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="{{ $path }}"/></svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Product column --}}
            <div style="display:flex;flex-direction:column;gap:.65rem;">
                <p class="sf-foot-col-head">Product</p>
                @foreach([['#','Terminal'],['#','Market Insights'],['#','How It Works'],['#','Pricing'],['#','Changelog']] as [$h,$l])
                <a href="{{ $h }}" class="sf-foot-link">{{ $l }}</a>
                @endforeach
            </div>

            {{-- Company column --}}
            <div style="display:flex;flex-direction:column;gap:.65rem;">
                <p class="sf-foot-col-head">Company</p>
                @foreach([['#','About'],['#','Blog'],['#','Careers'],['#','Press Kit'],['#','Contact']] as [$h,$l])
                <a href="{{ $h }}" class="sf-foot-link">{{ $l }}</a>
                @endforeach
            </div>

            {{-- Legal column --}}
            <div style="display:flex;flex-direction:column;gap:.65rem;">
                <p class="sf-foot-col-head">Legal</p>
                @foreach([['#','Terms of Service'],['#','Privacy Policy'],['#','Cookie Policy'],['#','Security'],['#','Disclosures']] as [$h,$l])
                <a href="{{ $h }}" class="sf-foot-link">{{ $l }}</a>
                @endforeach
            </div>

        </div>

        {{-- ── Bottom bar ── --}}
        <div style="border-top:1px solid rgba(255,255,255,.05);padding:1.25rem 0;display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:.75rem;">
            <p style="font-size:11.5px;color:#4a4a6a;">© {{ date('Y') }} Senflux Capital Deployment System. All rights reserved.</p>
            <div style="display:flex;align-items:center;gap:.4rem;">
                <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;box-shadow:0 0 6px rgba(34,197,94,.6);"></span>
                <span style="font-size:11px;color:#4a4a6a;">All systems operational</span>
            </div>
        </div>

    </div>

</footer>

<style>
    /* ── Desktop: 4-col ── */
    .sf-foot-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr 1fr 1fr;
        gap: 2.5rem;
    }

    /* ── Tablet: brand full-width, nav 3-col ── */
    @media (max-width: 900px) {
        .sf-foot-grid {
            grid-template-columns: 1fr 1fr 1fr;
        }
        .sf-foot-brand {
            grid-column: 1 / -1;
            flex-direction: row !important;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
    }

    /* ── Mobile: brand full-width, nav in tight 2-col ── */
    @media (max-width: 600px) {
        .sf-foot-grid {
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem 1.25rem;
        }
        .sf-foot-brand {
            grid-column: 1 / -1;
            flex-direction: column !important;
        }
    }

    .sf-foot-col-head {
        font-size: 11px;
        color: #4a4a6a;
        text-transform: uppercase;
        letter-spacing: .1em;
        font-weight: 600;
        margin-bottom: .25rem;
    }

    .sf-foot-link {
        font-size: 12.5px;
        color: #4a4a6a;
        text-decoration: none;
        transition: color .2s;
        line-height: 1.4;
    }
    .sf-foot-link:hover { color: #c8c8e0; }

    .sf-social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 8px;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.07);
        color: #4a4a6a;
        transition: border-color .2s, color .2s;
    }
    .sf-social-icon:hover {
        border-color: rgba(155,125,255,.35);
        color: #c8c8e0;
    }
</style>