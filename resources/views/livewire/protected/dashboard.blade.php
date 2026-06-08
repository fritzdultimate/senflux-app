{{-- resources/views/livewire/protected/dashboard.blade.php --}}
<div>

    {{-- ══════════════════════════════════
         ROW 1 — Stat Cards
         Mobile: 2-col | Tablet: 3-col | Desktop: 5-col
    ═══════════════════════════════════ --}}
    
    @include('dashboard.stats-grid')


    {{-- ══════════════════════════════════
         ROW 2 — Chart · Quick Actions · Formation Feed
         Mobile: stacked | Desktop: 3-col grid
    ═══════════════════════════════════ --}}
    <div class="dash-row-2" style="margin-bottom:12px; display:flex; flex-direction:column; gap:10px;">

        {{-- Portfolio Chart --}}
        <div class="card card-p" style="padding:16px 18px">
            <div class="sh">
                <div>
                    <div class="sh-title">Portfolio Performance</div>
                    <div class="sh-sub">Weekly summary — active positions</div>
                </div>
                <span class="badge b-g">+12.4% this week</span>
            </div>
            <div style="display:flex;align-items:flex-end;gap:6px;height:100px">
                @php
                    $bars = [
                        ['val'=>'$5,840','h'=>47,'day'=>'M','green'=>false],
                        ['val'=>'$8,960','h'=>61,'day'=>'T','green'=>false],
                        ['val'=>'$5,120','h'=>40,'day'=>'W','green'=>false],
                        ['val'=>'$8,760','h'=>59,'day'=>'T','green'=>false],
                        ['val'=>'$8,180','h'=>55,'day'=>'F','green'=>false],
                        ['val'=>'$10,280','h'=>75,'day'=>'S','green'=>false],
                        ['val'=>'$11,640','h'=>100,'day'=>'S','green'=>true],
                    ];
                @endphp
                @foreach($bars as $bar)
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%">
                    <div style="font-size:9px;color:{{ $bar['green'] ? 'var(--green)' : 'var(--t4)' }};margin-bottom:2px;font-family:'Syne',sans-serif;font-weight:{{ $bar['green'] ? '700' : '400' }}">{{ $bar['val'] }}</div>
                    <div class="cbar" style="width:100%;height:{{ $bar['h'] }}%;background:{{ $bar['green'] ? 'linear-gradient(180deg,#10B981,rgba(16,185,129,.3))' : 'linear-gradient(180deg,rgba(123,92,245,.5),rgba(79,70,229,.2))' }};border:1px solid {{ $bar['green'] ? 'rgba(16,185,129,.48)' : 'rgba(123,92,245,.28)' }};{{ $bar['green'] ? 'box-shadow:0 0 12px rgba(16,185,129,.25)' : '' }}"></div>
                    <div style="font-size:10px;color:{{ $bar['green'] ? 'var(--green)' : 'var(--t4)' }};margin-top:4px;font-weight:{{ $bar['green'] ? '600' : '400' }}">{{ $bar['day'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions — 2×3 grid on mobile, list on desktop --}}
        <div>
            <div class="sh-title" style="margin-bottom:10px">Quick Actions</div>
            <div class="qa-grid">
                <a href="#" class="qa qa-main">
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#9B7DFF" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.5 1.5v7M3.5 5.5l3 3 3-3" /><path d="M1.5 11h10" />
                        </svg>
                    </div>
                    <span class="qa-lbl">Deposit</span>
                </a>
                <a href="#" class="qa">
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#9B7DFF" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.5 8.5v-7M3.5 4.5l3-3 3 3" /><path d="M1.5 11h10" />
                        </svg>
                    </div>
                    <span class="qa-lbl">Withdraw</span>
                </a>
                <a href="#" class="qa">
                    <div class="qa-icon g">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#10B981" stroke-width="1.3" stroke-linejoin="round">
                            <path d="M6.5 1L12 3.5V7C12 10 9.5 12.5 6.5 13.5C3.5 12.5 1 10 1 7V3.5z" />
                        </svg>
                    </div>
                    <span class="qa-lbl">Stake</span>
                </a>
                <a href="#" class="qa" style="border-color:var(--bp);background:rgba(123,92,245,.07)">
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#9B7DFF" stroke-width="1.3" stroke-linecap="round">
                            <rect x="1" y="1" width="11" height="9" rx="2" /><path d="M3 5l2 2-2 2M6.5 9h3" />
                        </svg>
                    </div>
                    <span class="qa-lbl">Terminal</span>
                </a>
                <a href="#" class="qa" style="border-color:rgba(245,158,11,.22);background:rgba(245,158,11,.05)">
                    <div class="qa-icon y">
                        <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#F59E0B" stroke-width="1.3" stroke-linecap="round">
                            <path d="M1.5 8L4 5.5l2.5 2L9 4l2.5 2" />
                        </svg>
                    </div>
                    <span class="qa-lbl">Signals</span>
                </a>
                <div class="notif notif-y" style="border-radius:10px;padding:10px 12px">
                    <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#F59E0B" stroke-width="1.3" style="flex-shrink:0;margin-top:1px">
                        <circle cx="6.5" cy="6.5" r="5.5" />
                        <path d="M6.5 4V7M6.5 9h.01" stroke-linecap="round" />
                    </svg>
                    <div>
                        <div style="font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#fff">Pending</div>
                        <div style="font-size:10.5px;color:var(--t3)">1 deposit awaiting</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formation Feed --}}
        <div class="card" style="padding:0;overflow:hidden;border-color:var(--bp);position:relative">
            <div style="padding:10px 13px;border-bottom:1px solid var(--b);display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:6px">
                    <span class="ap" style="width:6px;height:6px;border-radius:50%;background:var(--green);display:block"></span>
                    <span style="font-family:'Syne',sans-serif;font-size:11.5px;font-weight:700;color:#fff">Formation Feed</span>
                </div>
                <a href="#" class="sh-link" style="font-size:11px">Full Terminal →</a>
            </div>
            <div class="term-scan" style="top:0"></div>
            <div style="padding:10px 13px;display:flex;flex-direction:column;gap:7px">
                @php
                    $feed = [
                        ['sym'=>'WIF',    'badge'=>'ACTIVE',   'bc'=>'b-g', 'val'=>'+214%', 'score'=>'86/100', 'cls'=>'up'],
                        ['sym'=>'BONK',   'badge'=>'BUILDING', 'bc'=>'b-p', 'val'=>'+143%', 'score'=>'72/100', 'cls'=>'up'],
                        ['sym'=>'POPCAT', 'badge'=>'EARLY',    'bc'=>'b-c', 'val'=>'+67%',  'score'=>'58/100', 'cls'=>'up'],
                        ['sym'=>'JTO',    'badge'=>'WEAK',     'bc'=>'b-r', 'val'=>'-23%',  'score'=>'34/100', 'cls'=>'dn'],
                        ['sym'=>'PYTH',   'badge'=>'IDLE',     'bc'=>'',    'val'=>'+5%',   'score'=>'21/100', 'cls'=>'nt'],
                    ];
                @endphp
                @foreach($feed as $row)
                <div class="feed-row" style="display:flex;align-items:center;justify-content:space-between;padding:6px 8px;border-radius:7px;border:1px solid rgba(255,255,255,.05)">
                    <div style="display:flex;align-items:center;gap:7px">
                        <span style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff">{{ $row['sym'] }}</span>
                        <span class="badge {{ $row['bc'] }}" style="font-size:9.5px{{ $row['bc'] ? '' : ';background:rgba(255,255,255,.06);color:var(--t3);border:1px solid var(--b)' }}">{{ $row['badge'] }}</span>
                    </div>
                    <div style="text-align:right">
                        <span class="{{ $row['cls'] }}" style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700">{{ $row['val'] }}</span>
                        <div style="font-size:10px;color:var(--t4)">{{ $row['score'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /dash-row-2 --}}


    {{-- ══════════════════════════════════
         ROW 3 — Live Trades · Bots · Activity
         Mobile: stacked | Desktop: 3-col grid
    ═══════════════════════════════════ --}}
    <div class="dash-row-3" style="display:flex; flex-direction:column; gap:10px;">

        {{-- Live Trades table --}}
        <div class="card" style="overflow:hidden">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 14px;border-bottom:1px solid var(--b)">
                <div>
                    <div class="sh-title" style="margin:0">Live Trades</div>
                    <div class="sh-sub">Active bot positions</div>
                </div>
                <div style="display:flex;align-items:center;gap:5px">
                    <span class="ap" style="width:5px;height:5px;border-radius:50%;background:var(--green);display:block"></span>
                    <span style="font-size:11px;color:var(--green)">Live</span>
                </div>
            </div>
            <div class="tbl-scroll">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Pair</th>
                            <th>Type</th>
                            <th>Entry</th>
                            <th>Current</th>
                            <th>P&L</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="feed-row">
                            <td><b style="font-family:'Syne',sans-serif;color:#fff">BTC/USDT</b></td>
                            <td><span class="badge b-g">Long</span></td>
                            <td style="color:var(--t2);font-family:var(--mono);font-size:12px">$67,820</td>
                            <td style="font-weight:600;color:#fff;font-family:var(--mono);font-size:12px">$69,174</td>
                            <td class="up" style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px">+$1,354 <span style="font-size:10px;opacity:.6">+2.0%</span></td>
                            <td><span class="badge b-p">Active</span></td>
                        </tr>
                        <tr class="feed-row">
                            <td><b style="font-family:'Syne',sans-serif;color:#fff">ETH/USDT</b></td>
                            <td><span class="badge b-g">Long</span></td>
                            <td style="color:var(--t2);font-family:var(--mono);font-size:12px">$3,390</td>
                            <td style="font-weight:600;color:#fff;font-family:var(--mono);font-size:12px">$3,482</td>
                            <td class="up" style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px">+$92 <span style="font-size:10px;opacity:.6">+2.7%</span></td>
                            <td><span class="badge b-p">Active</span></td>
                        </tr>
                        <tr class="feed-row">
                            <td><b style="font-family:'Syne',sans-serif;color:#fff">SOL/USDT</b></td>
                            <td><span class="badge b-g">Long</span></td>
                            <td style="color:var(--t2);font-family:var(--mono);font-size:12px">$189.40</td>
                            <td style="font-weight:600;color:#fff;font-family:var(--mono);font-size:12px">$187.32</td>
                            <td class="dn" style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px">-$10.4 <span style="font-size:10px;opacity:.6">-1.1%</span></td>
                            <td><span class="badge b-y">Watch</span></td>
                        </tr>
                        <tr class="feed-row">
                            <td><b style="font-family:'Syne',sans-serif;color:#fff">ADA/USDT</b></td>
                            <td><span class="badge b-g">Long</span></td>
                            <td style="color:var(--t2);font-family:var(--mono);font-size:12px">$0.471</td>
                            <td style="font-weight:600;color:#fff;font-family:var(--mono);font-size:12px">$0.487</td>
                            <td class="up" style="font-family:'Syne',sans-serif;font-weight:700;font-size:12px">+$16 <span style="font-size:10px;opacity:.6">+3.4%</span></td>
                            <td><span class="badge b-p">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Active Bots + Formation mini --}}
        <div class="card" style="padding:14px 15px">
            <div class="sh-title" style="margin-bottom:11px">Active Bots</div>
            <div style="display:flex;flex-direction:column;gap:7px">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.18)">
                    <div style="display:flex;align-items:center;gap:7px">
                        <span class="ap" style="width:6px;height:6px;border-radius:50%;background:var(--green);display:block"></span>
                        <b style="font-family:'Syne',sans-serif;font-size:12.5px;color:#fff">Grid Bot #1</b>
                    </div>
                    <span class="up" style="font-size:11.5px;font-weight:700">+$24.60</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.18)">
                    <div style="display:flex;align-items:center;gap:7px">
                        <span class="ap2" style="width:6px;height:6px;border-radius:50%;background:var(--green);display:block"></span>
                        <b style="font-family:'Syne',sans-serif;font-size:12.5px;color:#fff">DCA Bot #2</b>
                    </div>
                    <span class="up" style="font-size:11.5px;font-weight:700">+$18.40</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.18)">
                    <div style="display:flex;align-items:center;gap:7px">
                        <span style="width:6px;height:6px;border-radius:50%;background:var(--yellow);display:block"></span>
                        <b style="font-family:'Syne',sans-serif;font-size:12.5px;color:#fff">Scalp #3</b>
                    </div>
                    <span style="font-size:11.5px;font-weight:700;color:var(--yellow)">Paused</span>
                </div>
            </div>
            <div class="dv"></div>
            <div style="font-family:'Syne',sans-serif;font-size:11.5px;font-weight:600;color:var(--t3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">Formation</div>
            <div style="display:flex;flex-direction:column;gap:7px">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <b style="font-family:'Syne',sans-serif;font-size:12px;color:#fff">BTC</b>
                    <div style="display:flex;align-items:center;gap:6px">
                        <div style="width:72px;height:3.5px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden">
                            <div style="width:86%;height:100%;background:linear-gradient(90deg,var(--p),var(--pl));border-radius:2px"></div>
                        </div>
                        <span style="font-size:11px;color:var(--pl);font-weight:600;min-width:38px;text-align:right">Strong</span>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <b style="font-family:'Syne',sans-serif;font-size:12px;color:#fff">SOL</b>
                    <div style="display:flex;align-items:center;gap:6px">
                        <div style="width:72px;height:3.5px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden">
                            <div style="width:48%;height:100%;background:linear-gradient(90deg,var(--yellow),rgba(245,158,11,.4));border-radius:2px"></div>
                        </div>
                        <span style="font-size:11px;color:var(--yellow);font-weight:600;min-width:38px;text-align:right">Mod.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity + Top Movers --}}
        <div style="display:flex;flex-direction:column;gap:8px">
            <div class="card" style="padding:13px 14px">
                <div class="sh-title" style="margin-bottom:10px;font-size:13px">Recent Activity</div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <div style="display:flex;gap:8px;align-items:center">
                        <div style="width:26px;height:26px;border-radius:7px;flex-shrink:0;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.22);display:flex;align-items:center;justify-content:center">
                            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="#10B981" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5.5 8V1M3 4.5l2.5 3 2.5-3" /></svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;font-weight:600;color:#fff">Deposit confirmed</div>
                            <div style="font-size:10.5px;color:var(--t4)">$500 · 2m ago</div>
                        </div>
                        <span class="up" style="font-family:'Syne',sans-serif;font-size:11.5px;font-weight:700">+$500</span>
                    </div>
                    <div class="dv" style="margin:0"></div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <div style="width:26px;height:26px;border-radius:7px;flex-shrink:0;background:var(--bg-p);border:1px solid var(--bp);display:flex;align-items:center;justify-content:center">
                            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="#9B7DFF" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9L4.5 6.5l2 2L9 4" /></svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;font-weight:600;color:#fff">Bot trade closed</div>
                            <div style="font-size:10.5px;color:var(--t4)">BTC/USDT · 18m ago</div>
                        </div>
                        <span class="up" style="font-family:'Syne',sans-serif;font-size:11.5px;font-weight:700">+$24.6</span>
                    </div>
                    <div class="dv" style="margin:0"></div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <div style="width:26px;height:26px;border-radius:7px;flex-shrink:0;background:var(--bg-y);border:1px solid rgba(245,158,11,.2);display:flex;align-items:center;justify-content:center">
                            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="#F59E0B" stroke-width="1.3"><circle cx="5.5" cy="5.5" r="4.5" /><path d="M5.5 3.5v2L7 7" stroke-linecap="round" /></svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:12px;font-weight:600;color:#fff">Staking reward</div>
                            <div style="font-size:10.5px;color:var(--t4)">Pro plan · 1h ago</div>
                        </div>
                        <span style="font-family:'Syne',sans-serif;font-size:11.5px;font-weight:700;color:var(--yellow)">+$4.14</span>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:12px 14px">
                <div class="sh-title" style="margin-bottom:8px;font-size:13px">Top Movers</div>
                <div style="display:flex;flex-direction:column;gap:6px">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff">WIF</span>
                        <span class="up" style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700">+14.2%</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff">BONK</span>
                        <span class="up" style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700">+8.6%</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff">ADA</span>
                        <span class="up" style="font-family:'Syne',sans-serif;font-size:12px;font-weight:700">+3.1%</span>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /dash-row-3 --}}

</div>