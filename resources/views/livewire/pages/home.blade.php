{{-- resources/views/livewire/pages/home.blade.php --}}

@push('styles')
	<style>
		/* page extras */
		.orb-ring {
			position: absolute;
			inset: 0;
			border-radius: 50%;
			border: 1px solid rgba(123, 92, 245, .2)
		}

		.orb-ring2 {
			position: absolute;
			inset: 18px;
			border-radius: 50%;
			border: 1px solid rgba(123, 92, 245, .1)
		}

		.pipeline-step {
			display: flex;
			gap: 14px;
			padding: 14px 16px;
			border-radius: 10px;
			cursor: pointer;
			transition: background .2s;
			border: 1px solid transparent
		}

		.pipeline-step.active,
		.pipeline-step:hover {
			background: rgba(123, 92, 245, .08);
			border-color: rgba(123, 92, 245, .16)
		}

		.insight-row {
			display: flex;
			gap: 12px;
			align-items: flex-start;
			padding: 14px;
			border-radius: 12px;
			cursor: pointer;
			transition: all .2s;
			text-decoration: none;
			background: rgba(8, 8, 18, .9);
			border: 1px solid rgba(255, 255, 255, .07)
		}

		.insight-row:hover {
			border-color: rgba(123, 92, 245, .3);
			background: rgba(123, 92, 245, .04)
		}

		.hx {
			border-radius: 10px;
			padding: 11px 9px;
			text-align: center;
			cursor: pointer
		}

		.hx-vs {
			background: rgba(16, 185, 129, .2);
			border: 1px solid rgba(16, 185, 129, .32)
		}

		.hx-s {
			background: rgba(123, 92, 245, .2);
			border: 1px solid rgba(123, 92, 245, .32)
		}

		.hx-m {
			background: rgba(245, 158, 11, .15);
			border: 1px solid rgba(245, 158, 11, .26)
		}

		.hx-e {
			background: rgba(6, 182, 212, .15);
			border: 1px solid rgba(6, 182, 212, .26)
		}

		.hx-w {
			background: rgba(244, 63, 94, .14);
			border: 1px solid rgba(244, 63, 94, .22)
		}

		.hx-i {
			background: rgba(255, 255, 255, .04);
			border: 1px solid rgba(255, 255, 255, .07)
		}

		.fs {
			border-radius: 8px;
			padding: 10px 8px;
			text-align: center
		}

		.fs-i {
			background: rgba(255, 255, 255, .04);
			border: 1px solid rgba(255, 255, 255, .07)
		}

		.fs-e {
			background: rgba(6, 182, 212, .08);
			border: 1px solid rgba(6, 182, 212, .2)
		}

		.fs-b {
			background: rgba(123, 92, 245, .1);
			border: 1px solid rgba(123, 92, 245, .24)
		}

		.fs-a {
			background: rgba(16, 185, 129, .1);
			border: 1px solid rgba(16, 185, 129, .24)
		}

		.fs-w {
			background: rgba(244, 63, 94, .08);
			border: 1px solid rgba(244, 63, 94, .2)
		}
	</style>
@endpush

<div>

	{{-- ═══ HERO ═══ --}}
	<section style="padding-top: 384pxs" class="pt-20 pb-16 relative overflow-hidden text-center">
		<div class="absolute inset-0 pointer-events-none"
			style="background:radial-gradient(ellipse 72% 55% at 50% 0%,rgba(123,92,245,.22),transparent 65%)"></div>
		<div class="max-w-[1180px] mx-auto px-6 relative z-10">

			<div class="inline-flex items-center gap-2 border rounded-full px-4 py-1 mb-6 text-[11px] tracking-[.12em] uppercase"
				style="border-color:rgba(123,92,245,.26);color:rgba(155,125,255,.85)">
				<span class="w-[7px] h-[7px] rounded-full bg-[#10B981] ap block flex-shrink-0"></span>
				DETECT • VALIDATE • DEPLOY
			</div>

			<h1 class="font-syne font-extrabold text-[clamp(1.5rem,3.5vw,2.6rem)] text-white leading-[1.1] mb-4">
				Capital Moves <span class="tg">Before</span> <br> Markets Do.
			</h1>
			<p class="text-[15px] text-[#7a7a9a] max-w-[540px] mx-auto mb-8 leading-[1.75]">
				{{ env('APP_NAME') }} identifies strengthening market formations across the Solana network through AI-powered capital intelligence, validating qualifying conditions and automatically deploying when opportunities emerge.
			</p>
			<div class="flex gap-3 justify-center flex-wrap">
				<a href="{{ route('register') }}" class="btn-p" style="padding:11px 26px;font-size:14px">
					Explore the Platform →
				</a>
				<a href="https://youtu.be/nvbMhzTe3L8?si=FpAkApTXDouitRCJ" target="_blank" class="btn-o" style="padding:11px 26px;font-size:14px">
					Watch Overview
				</a>
			</div>

			{{-- Glowing orb --}}
			<div class="relative w-[260px] h-[260px] mx-auto mt-14">
				<div class="orb-ring as"></div>
				<div class="orb-ring2"></div>
				<div class="absolute inset-10 rounded-full af flex items-center justify-center"
					style="background:radial-gradient(circle at 38% 32%,rgba(123,92,245,.58),rgba(79,70,229,.3) 58%,transparent);box-shadow:0 0 60px rgba(123,92,245,.42),0 0 120px rgba(123,92,245,.14)">
					<div class="w-[48px] h-[48px] rounded-full flex items-center justify-center"
						style="background:linear-gradient(135deg,#9B7DFF,#4F46E5);box-shadow:0 0 32px rgba(123,92,245,.8)">
						<x-senflux.logo width="22" height="22" color="white" gradient-id="heroOrb" />
					</div>
				</div>
				<span class="absolute w-2 h-2 rounded-full ap block"
					style="background:rgba(155,125,255,.9);box-shadow:0 0 10px rgba(123,92,245,.8);top:5px;left:50%;transform:translateX(-50%)"></span>
				<span class="absolute w-[7px] h-[7px] rounded-full ap2 block"
					style="background:rgba(123,92,245,.7);bottom:7px;left:50%;transform:translateX(-50%)"></span>
				<span class="absolute w-[7px] h-[7px] rounded-full ap3 block"
					style="background:rgba(123,92,245,.6);left:7px;top:50%;transform:translateY(-50%)"></span>
				<span class="absolute w-[7px] h-[7px] rounded-full ap4 block"
					style="background:rgba(123,92,245,.6);right:7px;top:50%;transform:translateY(-50%)"></span>
			</div>

			{{-- Stat strip --}}
			<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-10 rv">
				<div class="card card-brand rounded-2xl p-5 text-center">
					<p class="font-syne font-semibold text-sm tg mb-1">We Detect</p>
					<p class="text-[12.5px] text-[#7a7a9a]">Monitor wallet activity, liquidity flow and participation behavior across the market.</p>
				</div>
				<div class="card card-brand rounded-2xl p-5 text-center">
					<p class="font-syne font-semibold text-sm tg mb-1">We Validate</p>
					<p class="text-[12.5px] text-[#7a7a9a]">Confirm strengthening participation through persistence, concentration and formation quality.</p>
				</div>
				<div class="card card-brand rounded-2xl p-5 text-center">
					<p class="font-syne font-semibold text-sm tg mb-1">We Deploy</p>
					<p class="text-[12.5px] text-[#7a7a9a]">Position capital only when deployment conditions meet defined thresholds.</p>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ MARKET MOVE ═══ --}}
	<section class="py-20 text-center">
		<div class="max-w-[1180px] mx-auto px-6">
			<p class="text-[11px] text-[#4a4a6a] uppercase tracking-widest mb-2">How Markets Behave</p>
			<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)] mb-3">Markets Move When <span
					class="tg">Capital Moves</span></h2>
			<p class="text-[14px] text-[#7a7a9a] max-w-[400px] mx-auto mb-10">
				Price is often the result. Capital movement is the cause.
			</p>
			<div class="relative w-[168px] h-[168px] mx-auto mb-12">
				<div class="absolute inset-0 rounded-full border-2 as" style="border-color:rgba(123,92,245,.2)"></div>
				<div class="absolute inset-5 rounded-full border" style="border-color:rgba(123,92,245,.1)"></div>
				<div class="absolute inset-10 rounded-full af flex items-center justify-center"
					style="background:radial-gradient(circle,rgba(123,92,245,.42),rgba(79,70,229,.2));box-shadow:0 0 40px rgba(123,92,245,.36)">
					<div class="w-8 h-8 rounded-full"
						style="background:linear-gradient(135deg,rgba(123,92,245,.6),rgba(79,70,229,.4));box-shadow:0 0 20px rgba(123,92,245,.5)">
					</div>
				</div>
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-3xl mx-auto rv">
				<div class="card card-brand p-6 text-left">
					<div class="ib mb-3.5"><svg width="18" height="18" fill="none">
							<path d="M2 14L6 10L9 13L13 7L16 9" stroke="#9B7DFF" stroke-width="1.5"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg></div>
					<h3 class="font-syne text-[14px] mb-2">Capital Concentrates</h3>
					<p class="text-[13px] text-[#7a7a9a]">
						Capital begins flowing into specific assets or ecosystems.
					</p>
				</div>
				<div class="card card-brand p-6 text-left">
					<div class="ib mb-3.5"><svg width="18" height="18" fill="none">
							<rect x="2" y="10" width="3" height="6" rx="1" fill="#9B7DFF" opacity=".7" />
							<rect x="7" y="7" width="3" height="9" rx="1" fill="#9B7DFF" />
							<rect x="12" y="4" width="3" height="12" rx="1" fill="#9B7DFF" opacity=".7" />
						</svg></div>
					<h3 class="font-syne text-[14px] mb-2">Participation Strengthens</h3>
					<p class="text-[13px] text-[#7a7a9a]">
						Sustained participation validates that the movement is gaining strength.
					</p>
				</div>
				<div class="card card-green p-6 text-left">
					<div class="ib ib-g mb-3.5"><svg width="18" height="18" fill="none">
							<path d="M9 3V9L13 13" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" />
							<circle cx="9" cy="9" r="7" stroke="#10B981" stroke-width="1.5" />
						</svg></div>
					<h3 class="font-syne text-[14px] mb-2">Opportunity Emerges</h3>
					<p class="text-[13px] text-[#7a7a9a]">
						When capital concentration and participation align, favorable conditions form.
					</p>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ DETECTION → DEPLOYMENT ═══ --}}
	<section class="py-20" style="background:rgba(8,8,18,.72)">
		<div class="max-w-[1180px] mx-auto px-6">
			<div class="text-center mb-12">
				<span class="pill mb-3.5 inline-block">Our Three-Step Process</span>
				<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">From Detection to <span
						class="tg">Deployment</span></h2>
				<p class="text-[14px] text-[#7a7a9a] mt-2.5">Every position starts with a signal — tracked all the way
					to confirmed scaling.</p>
			</div>
			<div class="flex items-start gap-0 rv">
				<div class="flex-1 text-center px-3">
					<div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
						style="border-color:rgba(123,92,245,.32);background:rgba(123,92,245,.08)"><svg width="20"
							height="20" fill="none">
							<circle cx="10" cy="10" r="4" stroke="#9B7DFF" stroke-width="1.5" />
							<path d="M10 2V4M10 16V18M2 10H4M16 10H18" stroke="#9B7DFF" stroke-width="1.5"
								stroke-linecap="round" />
						</svg></div>
					<p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">1. Detect</p>
					<p class="text-[12px] text-[#7a7a9a]">System identifies unusual participation across monitored
						assets.</p>
				</div>
				<div class="sline mt-6"></div>
				<div class="flex-1 text-center px-3">
					<div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
						style="border-color:rgba(123,92,245,.32);background:rgba(123,92,245,.08)"><svg width="20"
							height="20" fill="none">
							<path d="M5 10L9 14L15 6" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"
								stroke-linejoin="round" />
							<circle cx="10" cy="10" r="8" stroke="#9B7DFF" stroke-width="1.5" />
						</svg></div>
					<p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">2. Validate</p>
					<p class="text-[12px] text-[#7a7a9a]">Cross-referencing multiple indicators to confirm signal
						strength.</p>
				</div>
				<div class="sline mt-6"
					style="background:linear-gradient(90deg,rgba(123,92,245,.3),rgba(16,185,129,.3))"></div>
				<div class="flex-1 text-center px-3">
					<div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
						style="border-color:rgba(16,185,129,.32);background:rgba(16,185,129,.08)"><svg width="20"
							height="20" fill="none">
							<path d="M10 3L17 10L10 17M3 10H17" stroke="#10B981" stroke-width="1.5"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg></div>
					<p class="text-[11px] text-[#10B981] font-semibold mb-1.5">3. Deploy</p>
					<p class="text-[12px] text-[#7a7a9a]">Capital is deployed in staged portions as signal validates.
					</p>
				</div>
			</div>
			<div class="rounded-2xl p-6 mt-8 rv"
				style="background:rgba(8,8,18,.92);border:1px solid rgba(123,92,245,.14)">
				<p class="text-[11px] text-[#9B7DFF] uppercase tracking-widest mb-3">Staged Development</p>
				<div class="grid grid-cols-1 sm:grid-cols-3 rounded-lg overflow-hidden"
					style="border:1px solid rgba(255,255,255,.06)">
					<div class="p-4 text-center"
						style="background:#08080f;border-right:1px solid rgba(255,255,255,.06)">
						<p class="font-syne text-[#9B7DFF] font-semibold text-sm">→ Live Probe</p>
						<p class="text-[12px] text-[#7a7a9a] mt-1">Initial position to gauge market</p>
					</div>
					<div class="p-4 text-center"
						style="background:#08080f;border-right:1px solid rgba(255,255,255,.06)">
						<p class="font-syne text-[#9B7DFF] font-semibold text-sm">→ Confirmation Add</p>
						<p class="text-[12px] text-[#7a7a9a] mt-1">Expand on confirmed direction</p>
					</div>
					<div class="p-4 text-center" style="background:#08080f">
						<p class="font-syne text-[#10B981] font-semibold text-sm">→ Confident Scaling</p>
						<p class="text-[12px] text-[#7a7a9a] mt-1">Full deployment at peak signal</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ DISCIPLINE ═══ --}}
	<section class="py-20 relative overflow-hidden">
		<div class="absolute pointer-events-none rounded-full -right-48 top-1/2 -translate-y-1/2"
			style="width:500px;height:500px;background:radial-gradient(circle,rgba(123,92,245,.07),transparent 70%)">
		</div>
		<div class="max-w-[1180px] mx-auto px-6 relative z-10">
			<div class="text-center mb-12">
				<span class="pill mb-3.5 inline-block">DEPLOYMENT FRAMEWORK</span>
				<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Capital Is Deployed With <span
						class="tg">Conviction</span></h2>
				<p class="text-[14px] text-[#7a7a9a] mt-2.5">
					Only validated capital formations qualify for deployment.
				</p>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-[1fr_2fr] gap-3 rv">
				<div class="card card-brand p-7">
					<div class="ib mb-4"><svg width="18" height="18" fill="none">
							<rect x="2" y="2" width="14" height="14" rx="3" stroke="#9B7DFF" stroke-width="1.5" />
							<path d="M6 9H12M9 6V12" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" />
						</svg></div>
					<h3 class="font-syne text-[15px] mb-2">Formation-Based Deployment</h3>
					<p class="text-[13px] text-[#7a7a9a] mb-6">
						Position sizing is determined by formation quality, participation strength, and market conditions.
					</p>
					<p class="text-[11px] text-[#4a4a6a] uppercase tracking-wider mb-3.5">Exposure Overview</p>
					<div class="mb-4">
						<div class="flex justify-between text-[12px] mb-1.5"><span class="text-[#c8c8e0]">Max
								Exposure</span><span class="text-[#9B7DFF]">98%</span></div>
						<div class="prog-t">
							<div class="prog-f" style="width:98%"></div>
						</div>
					</div>
					<div>
						<div class="flex justify-between text-[12px] mb-1.5"><span class="text-[#c8c8e0]">Capital
								Safety</span><span class="text-[#10B981]">70%</span></div>
						<div class="prog-t">
							<div class="prog-f" style="width:70%;background:linear-gradient(90deg,#10B981,#06B6D4)">
							</div>
						</div>
					</div>
				</div>
				<div class="grid grid-cols-2 gap-3">
					<div class="card card-brand p-5">
						<div class="ib mb-3.5"><svg width="18" height="18" fill="none">
								<path d="M3 9C3 9 5 4 9 4C13 4 15 9 15 9C15 9 13 14 9 14C5 14 3 9 3 9Z" stroke="#9B7DFF"
									stroke-width="1.5" />
								<circle cx="9" cy="9" r="2" fill="#9B7DFF" />
							</svg></div>
						<h3 class="font-syne text-[14px] mb-2">Dynamic Allocation</h3>
						<p class="text-[12.5px] text-[#7a7a9a]">
							Exposure expands as capital concentration strengthens and contracts as conditions weaken.
						</p>
					</div>
					<div class="card card-green p-5">
						<div class="ib ib-g mb-3.5"><svg width="18" height="18" fill="none">
								<path d="M9 2L14 5V9C14 12.3 11.8 15.2 9 16C6.2 15.2 4 12.3 4 9V5L9 2Z" stroke="#10B981"
									stroke-width="1.5" stroke-linejoin="round" />
							</svg></div>
						<h3 class="font-syne text-[14px] mb-2">Capital Protection</h3>
						<p class="text-[12.5px] text-[#7a7a9a]">
							Deployment remains controlled when formation quality deteriorates or participation fades.
						</p>
					</div>
					<div class="card col-span-2 p-5">
						<p class="text-[11px] text-[#4a4a6a] uppercase tracking-wider mb-3">Exposure Overview</p>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<div class="flex justify-between text-[12px] mb-1.5"><span
										class="text-[#c8c8e0]">98%</span><span
										class="text-[#4a4a6a] text-[11px]">0900–22:07</span></div>
								<div class="prog-t">
									<div class="prog-f" style="width:98%"></div>
								</div><span class="badge badge-purple mt-2 text-[10px]">0900–22:07</span>
							</div>
							<div>
								<div class="flex justify-between text-[12px] mb-1.5"><span
										class="text-[#10B981]">70%</span><span
										class="text-[#4a4a6a] text-[11px]">0900–22:07</span></div>
								<div class="prog-t">
									<div class="prog-f"
										style="width:70%;background:linear-gradient(90deg,#10B981,#06B6D4)"></div>
								</div><span class="badge badge-green mt-2 text-[10px]">0900–22:07</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ SYSTEM IN MOTION ═══ --}}
	<section class="py-20 hidden" style="background:rgba(8,8,18,.72)">
		<div class="max-w-[1180px] mx-auto px-6">
			<div class="text-center mb-10">
				<p class="text-[11px] text-[#4a4a6a] uppercase tracking-widest mb-2">Live Dashboard</p>
				<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">The System <span class="tg">In
						Motion</span></h2>
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 rv">
				<div class="card rounded-2xl p-5">
					<p class="text-[11px] text-[#4a4a6a] mb-3">Active Deployments</p>
					<div class="flex items-end gap-1 h-14">
						<div style="width:10px;height:30%;border-radius:3px 3px 0 0;background:#7B5CF5;opacity:.6">
						</div>
						<div style="width:10px;height:50%;border-radius:3px 3px 0 0;background:#7B5CF5;opacity:.7">
						</div>
						<div style="width:10px;height:75%;border-radius:3px 3px 0 0;background:#7B5CF5;opacity:.8">
						</div>
						<div style="width:10px;height:60%;border-radius:3px 3px 0 0;background:#7B5CF5;opacity:.7">
						</div>
						<div style="width:10px;height:85%;border-radius:3px 3px 0 0;background:#9B7DFF"></div>
					</div>
				</div>
				<div class="card rounded-2xl p-5">
					<p class="text-[11px] text-[#4a4a6a] mb-2">Flow Rate</p><span
						class="badge badge-green mb-2">Active</span>
					<p class="font-syne font-extrabold text-[30px] text-white">+24%</p>
					<p class="text-[11px] text-[#4a4a6a] mt-1">7-day trend</p>
				</div>
				<div class="card rounded-2xl p-5 flex flex-col items-center justify-center">
					<p class="text-[11px] text-[#4a4a6a] mb-3">Leader Allotment</p>
					<div class="relative w-[72px] h-[72px]"><svg viewBox="0 0 72 72" width="72" height="72"
							style="transform:rotate(-90deg)">
							<circle cx="36" cy="36" r="28" fill="none" stroke="rgba(123,92,245,.14)" stroke-width="7" />
							<circle cx="36" cy="36" r="28" fill="none" stroke="#7B5CF5" stroke-width="7"
								stroke-dasharray="176" stroke-dashoffset="44" stroke-linecap="round" />
						</svg>
						<div
							class="absolute inset-0 flex items-center justify-center font-syne font-bold text-[15px] text-white">
							55%</div>
					</div>
				</div>
				<div class="card rounded-2xl p-5">
					<p class="text-[11px] text-[#4a4a6a] mb-2">Signals</p>
					<p class="font-syne font-extrabold text-[34px] text-white mb-2">128</p>
					<p class="text-[11px] text-[#4a4a6a] mb-1">Confidence</p><span class="badge badge-green">High</span>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ SIMPLE FOR YOU ═══ --}}
	<section class="py-20 text-center">
		<div class="max-w-[1180px] mx-auto px-6">
			<span class="pill mb-3.5 inline-block">Getting Started</span>
			<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Simple for You.<br /><span
					class="tg">Powerful in Action</span></h2>
			<p class="text-[14px] text-[#7a7a9a] mt-2.5 mb-12">You set the capital — the system takes it from there.</p>
			<div class="grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-[720px] mx-auto rv">
				<div>
					<div class="w-[52px] h-[52px] rounded-full border flex items-center justify-center mx-auto mb-4"
						style="border-color:rgba(123,92,245,.3);background:rgba(123,92,245,.08)"><svg width="22"
							height="22" fill="none">
							<path d="M11 3V11L15 15" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" />
							<circle cx="11" cy="11" r="8" stroke="#9B7DFF" stroke-width="1.5" />
						</svg></div>
					<h3 class="font-syne text-[14px] mb-2">1. Allocate Capital</h3>
					<p class="text-[13px] text-[#7a7a9a]">Define how much you want the system to manage.</p>
				</div>
				<div>
					<div class="w-[52px] h-[52px] rounded-full border flex items-center justify-center mx-auto mb-4"
						style="border-color:rgba(123,92,245,.3);background:rgba(123,92,245,.08)"><svg width="22"
							height="22" fill="none">
							<polygon points="9,6 18,11 9,16" fill="#9B7DFF" />
							<path d="M4 6V16" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" />
						</svg></div>
					<h3 class="font-syne text-[14px] mb-2">2. Activate System</h3>
					<p class="text-[13px] text-[#7a7a9a]">Enable real-time monitoring and automated deployment.</p>
				</div>
				<div>
					<div class="w-[52px] h-[52px] rounded-full border flex items-center justify-center mx-auto mb-4"
						style="border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.08)"><svg width="22"
							height="22" fill="none">
							<path d="M4 12L8 16L18 7" stroke="#10B981" stroke-width="1.5" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg></div>
					<h3 class="font-syne text-[14px] mb-2">3. Observe Execution</h3>
					<p class="text-[13px] text-[#7a7a9a]">Watch deployments execute with transparent analytics.</p>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ LIVE FEED ═══ --}}
	<section class="py-20 hidden" style="background:rgba(8,8,18,.78)">
		<div class="max-w-[1280px] mx-auto px-6">
			<div class="text-center mb-10">
				<span class="pill mb-3.5 inline-block">Unlimited Transparency</span>
				<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Observe Participation Formation <span
						style="-webkit-text-fill-color:#10B981;color:#10B981">In Real Time</span></h2>
				<p class="text-[14px] text-[#7a7a9a] mt-2.5 max-w-lg mx-auto">Watch as participation forms across any
					asset — confirmed in seconds.</p>
			</div>
			<div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-3.5 rv">
				<div class="rounded-2xl overflow-hidden"
					style="background:rgba(8,8,18,.92);border:1px solid rgba(255,255,255,.07)">
					<div class="flex items-center justify-between px-5 py-3.5 border-b"
						style="border-color:rgba(255,255,255,.07)">
						<div class="flex items-center gap-2"><span
								class="w-2 h-2 rounded-full bg-[#10B981] ap block"></span><span
								class="text-[12px] font-semibold text-[#c8c8e0]">LIVE FORMATION FEED</span></div>
						<span class="text-[11px] text-[#4a4a6a]">Last updated: just now</span>
					</div>
					<div class="overflow-x-auto">
						<table class="ftbl">
							<thead>
								<tr>
									<th>Asset</th>
									<th>Formation State</th>
									<th>Participation</th>
									<th>Persistence Score</th>
									<th>Velocity 7D</th>
									<th>Trend</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span class="font-syne font-bold text-white text-[13px]">WIF</span><br /><span
											class="text-[11px] text-[#4a4a6a]">dogwifhat</span></td>
									<td><span class="badge badge-green">ACTIVE ▲</span></td>
									<td><span class="text-[#9B7DFF] font-semibold">+214%</span><br /><span
											class="text-[11px] text-[#4a4a6a]">High</span></td>
									<td><span class="text-white font-bold">86</span><span
											class="text-[#4a4a6a] text-[12px]">/100</span><br /><span
											class="text-[11px] text-[#10B981] font-semibold">STRONG</span></td>
									<td>
										<div class="spark"><span style="height:8px"></span><span
												style="height:13px"></span><span style="height:17px"></span><span
												style="height:21px;opacity:1"></span><span style="height:19px"></span>
										</div>
									</td>
									<td class="text-[#9B7DFF] text-base">↗</td>
								</tr>
								<tr>
									<td><span class="font-syne font-bold text-white text-[13px]">BONK</span><br /><span
											class="text-[11px] text-[#4a4a6a]">bonk</span></td>
									<td><span class="badge badge-purple">BUILDING ↑</span></td>
									<td><span class="text-[#9B7DFF] font-semibold">+143%</span><br /><span
											class="text-[11px] text-[#4a4a6a]">High</span></td>
									<td><span class="text-white font-bold">72</span><span
											class="text-[#4a4a6a] text-[12px]">/100</span><br /><span
											class="text-[11px] text-[#9B7DFF] font-semibold">STRONG</span></td>
									<td>
										<div class="spark"><span style="height:6px"></span><span
												style="height:10px"></span><span style="height:15px"></span><span
												style="height:19px;opacity:1;background:#9B7DFF"></span><span
												style="height:22px;opacity:1;background:#9B7DFF"></span></div>
									</td>
									<td class="text-[#9B7DFF] text-base">↗</td>
								</tr>
								<tr>
									<td><span
											class="font-syne font-bold text-white text-[13px]">POPCAT</span><br /><span
											class="text-[11px] text-[#4a4a6a]">popcat</span></td>
									<td><span class="badge"
											style="background:rgba(6,182,212,.12);color:#06B6D4;border:1px solid rgba(6,182,212,.22)">EARLY
											◉</span></td>
									<td><span class="text-[#06B6D4] font-semibold">+67%</span><br /><span
											class="text-[11px] text-[#4a4a6a]">Moderate</span></td>
									<td><span class="text-white font-bold">58</span><span
											class="text-[#4a4a6a] text-[12px]">/100</span><br /><span
											class="text-[11px] text-[#F59E0B] font-semibold">MODERATE</span></td>
									<td>
										<div class="spark"><span style="height:11px;background:#06B6D4"></span><span
												style="height:14px;background:#06B6D4"></span><span
												style="height:12px;background:#06B6D4"></span><span
												style="height:16px;background:#06B6D4;opacity:.8"></span><span
												style="height:13px;background:#06B6D4"></span></div>
									</td>
									<td class="text-[#06B6D4]">→</td>
								</tr>
								<tr>
									<td><span class="font-syne font-bold text-white text-[13px]">JTO</span><br /><span
											class="text-[11px] text-[#4a4a6a]">jito</span></td>
									<td><span class="badge badge-red">WEAKENING ↓</span></td>
									<td><span class="text-[#F43F5E] font-semibold">-23%</span><br /><span
											class="text-[11px] text-[#4a4a6a]">Low</span></td>
									<td><span class="text-white font-bold">34</span><span
											class="text-[#4a4a6a] text-[12px]">/100</span><br /><span
											class="text-[11px] text-[#F43F5E] font-semibold">WEAK</span></td>
									<td>
										<div class="spark"><span
												style="height:21px;background:#F43F5E;opacity:.7"></span><span
												style="height:17px;background:#F43F5E;opacity:.6"></span><span
												style="height:12px;background:#F43F5E"></span><span
												style="height:7px;background:#F43F5E"></span><span
												style="height:4px;background:#F43F5E"></span></div>
									</td>
									<td class="text-[#F43F5E]">↓</td>
								</tr>
								<tr>
									<td><span class="font-syne font-bold text-white text-[13px]">PYTH</span><br /><span
											class="text-[11px] text-[#4a4a6a]">Pyth Network</span></td>
									<td><span class="badge badge-gray">IDLE ↔</span></td>
									<td><span class="text-[#7a7a9a] font-semibold">+5%</span><br /><span
											class="text-[11px] text-[#4a4a6a]">Low</span></td>
									<td><span class="text-white font-bold">21</span><span
											class="text-[#4a4a6a] text-[12px]">/100</span><br /><span
											class="text-[11px] text-[#4a4a6a]">WEAK</span></td>
									<td>
										<div class="spark"><span style="height:11px;background:#4a4a6a"></span><span
												style="height:9px;background:#4a4a6a"></span><span
												style="height:11px;background:#4a4a6a"></span><span
												style="height:10px;background:#4a4a6a"></span><span
												style="height:9px;background:#4a4a6a"></span></div>
									</td>
									<td class="text-[#4a4a6a]">→</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="flex flex-col gap-3">
					<div class="rounded-2xl p-4"
						style="background:rgba(8,8,18,.92);border:1px solid rgba(255,255,255,.07)">
						<p class="text-[11px] text-[#4a4a6a] uppercase tracking-wider mb-3">Participation Map</p>
						<div class="relative h-[108px] rounded-xl overflow-hidden"
							style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
							<span class="absolute w-3 h-3 rounded-full ap block"
								style="background:rgba(123,92,245,.85);box-shadow:0 0 12px rgba(123,92,245,.7);top:18px;left:50px"></span>
							<span class="absolute w-[10px] h-[10px] rounded-full ap2 block"
								style="background:rgba(16,185,129,.85);box-shadow:0 0 10px rgba(16,185,129,.6);top:36px;right:60px"></span>
							<span class="absolute w-[13px] h-[13px] rounded-full ap3 block"
								style="background:rgba(123,92,245,.55);box-shadow:0 0 14px rgba(123,92,245,.45);bottom:22px;left:45%"></span>
						</div>
						<div class="grid grid-cols-3 gap-2 mt-3.5 text-center">
							<div>
								<p class="font-syne text-[16px] font-bold text-white">14,881</p>
								<p class="text-[11px] text-[#4a4a6a]">Assets</p>
							</div>
							<div>
								<p class="font-syne text-[16px] font-bold text-[#10B981]">$19.7K</p>
								<p class="text-[11px] text-[#4a4a6a]">Avg Size</p>
							</div>
							<div>
								<p class="font-syne text-[16px] font-bold text-[#9B7DFF]">2.4K</p>
								<p class="text-[11px] text-[#4a4a6a]">Active</p>
							</div>
						</div>
					</div>
					<div class="rounded-2xl p-4 flex-1"
						style="background:rgba(8,8,18,.92);border:1px solid rgba(255,255,255,.07)">
						<p class="text-[11px] text-[#4a4a6a] uppercase tracking-wider mb-3">Recent Events</p>
						<div class="flex flex-col gap-3">
							<div class="flex gap-2.5"><span
									class="w-1.5 h-1.5 rounded-full bg-[#10B981] mt-1.5 flex-shrink-0 block"></span>
								<div>
									<p class="text-[13px] text-white font-medium">BONK – Formation Detected</p>
									<p class="text-[11px] text-[#4a4a6a]">Building Confirmed · 2m ago</p>
								</div>
							</div>
							<div class="flex gap-2.5"><span
									class="w-1.5 h-1.5 rounded-full bg-[#9B7DFF] mt-1.5 flex-shrink-0 block"></span>
								<div>
									<p class="text-[13px] text-white font-medium">POPCAT – Expansion Stage</p>
									<p class="text-[11px] text-[#4a4a6a]">Expansion Began · 8m ago</p>
								</div>
							</div>
							<div class="flex gap-2.5"><span
									class="w-1.5 h-1.5 rounded-full bg-[#10B981] mt-1.5 flex-shrink-0 block"></span>
								<div>
									<p class="text-[13px] text-white font-medium">WIF – Full Deployment</p>
									<p class="text-[11px] text-[#4a4a6a]">Expansion Began · 15m ago</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ NETWORK ═══ --}}
	<section class="py-20 text-center">
		<div class="max-w-[1180px] mx-auto px-6">
			<span class="pill mb-3.5 inline-block">Grow Together</span>
			<h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Scale With the <span
					class="tg">Network</span></h2>
			<p class="text-[14px] text-[#7a7a9a] mt-2.5 mb-12">Invite others and grow your allocation.</p>
			<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 rv">
				<div class="card p-6 text-center">
					<div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none">
							<circle cx="9" cy="6" r="3" stroke="#9B7DFF" stroke-width="1.5" />
							<path d="M3 16C3 13 5.7 11 9 11C12.3 11 15 13 15 16" stroke="#9B7DFF" stroke-width="1.5"
								stroke-linecap="round" />
						</svg></div>
					<h3 class="font-syne text-[13px]">Invite Users</h3>
				</div>
				<div class="card p-6 text-center">
					<div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none">
							<path d="M3 9L7 13L15 5" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg></div>
					<h3 class="font-syne text-[13px]">System Benefits</h3>
				</div>
				<div class="card p-6 text-center">
					<div class="ib ib-g mx-auto mb-3.5"><svg width="18" height="18" fill="none">
							<path d="M9 3V9L12 12" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" />
							<circle cx="9" cy="9" r="7" stroke="#10B981" stroke-width="1.5" />
						</svg></div>
					<h3 class="font-syne text-[13px]">You Earn</h3>
				</div>
				<div class="card p-6 text-center">
					<div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none">
							<circle cx="4" cy="9" r="2" stroke="#9B7DFF" stroke-width="1.5" />
							<circle cx="14" cy="5" r="2" stroke="#9B7DFF" stroke-width="1.5" />
							<circle cx="14" cy="13" r="2" stroke="#9B7DFF" stroke-width="1.5" />
							<path d="M6 9L12 6M6 9L12 12" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" />
						</svg></div>
					<h3 class="font-syne text-[13px]">Network Grows</h3>
				</div>
			</div>
		</div>
	</section>

	{{-- ═══ CTA ═══ --}}
	<section class="py-24 text-center relative overflow-hidden">
		<div class="absolute inset-0 pointer-events-none"
			style="background:radial-gradient(ellipse 70% 60% at 50% 50%,rgba(123,92,245,.15),transparent 72%)"></div>
		<div class="max-w-[1180px] mx-auto px-6 relative z-10">
			<h2 class="font-syne font-extrabold text-[clamp(1.8rem,4vw,3rem)]">Capital Doesn't Wait. <span class="tg">It
					Moves.</span></h2>
			<p class="text-[15px] text-[#7a7a9a] mt-4 mb-8 max-w-[480px] mx-auto leading-[1.75]">
				The strongest opportunities rarely announce themselves. Senflux continuously evaluates market conditions, qualifies high-conviction formations, and deploys automatically when opportunity emerges.
			</p>
			<a href="{{ route('register') }}" class="btn-p mx-auto" style="padding:13px 34px;font-size:14px">Start
				Deploying Now →</a>
		</div>
	</section>

</div>