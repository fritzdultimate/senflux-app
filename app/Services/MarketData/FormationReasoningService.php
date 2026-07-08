<?php

// app/Services/MarketData/FormationReasoningService.php
namespace App\Services\MarketData;

use App\Models\Formation;

class FormationReasoningService {
    public function score(Formation $f): string {
        $metrics = [
            'New Capital Inflow' => $f->capital_concentration,
            'Liquidity Flow'        => $f->liquidity_migration, //
            'Participation Growth'  => $f->participation_growth,
            'Wallet Quality'        => $f->wallet_quality,
        ];

        $drivers  = array_keys(array_filter($metrics, fn ($v) => $v >= 70));
        $laggards = array_keys(array_filter($metrics, fn ($v) => $v < 40));

        $summary = "Formation Score of {$f->score}/100";

        $summary .= $drivers
            ? ' is being pulled up by strong ' . $this->join($drivers) . '.'
            : ' reflects a market still forming — no single signal is dominant yet.';

        if ($laggards) {
            $summary .= ' ' . ucfirst($this->join($laggards)) . ' '
                . (count($laggards) > 1 ? 'are' : 'is') . ' holding it back.';
        }

        if ($f->previous_score !== null && $f->previous_score != $f->score) {
            $delta = $f->score - $f->previous_score;
            $summary .= $delta > 0
                ? " Up {$delta} points since the last sync."
                : ' Down ' . abs($delta) . ' points since the last sync.';
        }

        return $summary;
    }

    public function confidence(Formation $f): string {
        return match ($f->confidence) {
            'High'     => 'High confidence — the formation score has cleared the 60-point threshold, meaning capital, liquidity, participation, and wallet quality are aligned rather than conflicting.',
            'Moderate' => "Moderate confidence — the score sits in the 35–59 range. Some signals are strong but others haven't confirmed the move yet, so this is being watched rather than fully trusted.",
            default    => 'Low confidence — the score is below 35. Signals are weak or contradictory; treat this as early / speculative.',
        };
    }

    public function metric(string $key, float $value, Formation $f): string {
        return match ($key) {
            'capital_concentration' => $this->capitalInflow($value, $f),
            'liquidity_migration' => $this->liquidityFlow($value, $f),
            'participation_growth' => $this->participation($value, $f),
            'wallet_quality' => $this->walletQuality($value),
            default => '',
        };
    }

    private function band(float $v): string {
        return match (true) {
            $v >= 80 => 'Very strong',
            $v >= 60 => 'Strong',
            $v >= 40 => 'Moderate',
            $v >= 20 => 'Early',
            default  => 'Weak',
        };
    }

    private function capitalInflow(float $v, Formation $f): string {
        $total = ($f->buys_24h ?? 0) + ($f->sells_24h ?? 0);
        $txnNote = $total > 0
            ? " Buys currently make up " . round($f->buys_24h / $total * 100) . "% of 24h transactions ({$f->buys_24h} buys vs {$f->sells_24h} sells)."
            : '';

        return "{$this->band($v)} at {$v}% — capital is entering without being dominated by a small number of large wallets.{$txnNote}";
    }

    private function liquidityFlow(float $v, Formation $f): string {
        return "{$this->band($v)} at {$v}% — measures whether liquidity is deepening in this pool or draining out. Current pool liquidity: $" . number_format($f->liquidity_usd) . ".";
    }

    // private function participation(float $v): string {
    //     return "{$this->band($v)} at {$v}% — tracks growth in the number of distinct wallets trading, not just volume.";
    // }

    private function participation(float $v, Formation $f): string {
        $walletNote = $f->unique_wallets_24h !== null
            ? " {$f->unique_wallets_24h} unique wallets traded in the last 24h" .
            ($f->unique_wallets_24h_change_pct !== null
                ? ', ' . ($f->unique_wallets_24h_change_pct >= 0 ? 'up ' : 'down ') . abs(round($f->unique_wallets_24h_change_pct)) . '% from the prior period.'
                : '.')
            : '';

        return "{$this->band($v)} at {$v}% — tracks growth in the number of distinct wallets trading, not just volume.{$walletNote}";
    }

    private function walletQuality(float $v): string {
        return "{$this->band($v)} at {$v}% — reflects the track record of wallets currently active here (age, prior behavior), filtering out fresh or bot-like wallets.";
    }

    private function join(array $items): string {
        if (count($items) === 1) return $items[0];
        $last = array_pop($items);
        return implode(', ', $items) . ' and ' . $last;
    }
}