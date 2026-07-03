<?php
// app/Http/Controllers/FormationShareController.php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
// use Intervention\Image\Laravel\Facades\Image;

class FormationShareController extends Controller {
    public function show(Formation $formation) {
        abort_unless($formation->is_active, 404);

        return view('formations.share', ['formation' => $formation]);
    }

    /**
     * Cached per (formation, state) — regenerated only when state_changed_at
     * is newer than the cached file, so a formation sitting in ACTIVE for
     * days doesn't get re-rendered on every request/crawler hit.
     */
    public function ogImage(Formation $formation) {
        abort_unless($formation->is_active, 404);

        $path = "og/formations/{$formation->id}.png";
        $isStale = !Storage::disk('public')->exists($path)
            || Storage::disk('public')->lastModified($path) < $formation->state_changed_at->timestamp;

        if ($isStale) {
            Storage::disk('public')->put($path, $this->render($formation)->toPng());
        }

        return response(Storage::disk('public')->get($path))->header('Content-Type', 'image/png');
    }

    private function render(Formation $formation) {
        $canvas = Image::create(1200, 630)->fill('#05050c');

        $canvas->text($formation->token_symbol, 60, 140, function ($font) {
            $font->filename(public_path('fonts/Inter-Bold.ttf'));
            $font->size(72);
            $font->color('#ffffff');
        });

        $canvas->text($formation->token_name, 60, 190, function ($font) {
            $font->filename(public_path('fonts/Inter-Regular.ttf'));
            $font->size(28);
            $font->color('#9ca3af');
        });

        $canvas->text(strtoupper($formation->state->label()), 60, 260, function ($font) use ($formation) {
            $font->filename(public_path('fonts/Inter-Bold.ttf'));
            $font->size(32);
            $font->color($formation->state->color());
        });

        $canvas->text("{$formation->score}/100", 60, 340, function ($font) {
            $font->filename(public_path('fonts/Inter-Bold.ttf'));
            $font->size(56);
            $font->color('#ffffff');
        });

        $metrics = [
            'Capital Concentration' => $formation->capital_concentration,
            'Liquidity Migration'   => $formation->liquidity_migration,
            'Participation Growth'  => $formation->participation_growth,
            'Wallet Quality'        => $formation->wallet_quality,
        ];

        $y = 420;
        foreach ($metrics as $label => $value) {
            $canvas->text("{$label}  {$value}%", 60, $y, function ($font) {
                $font->filename(public_path('fonts/Inter-Regular.ttf'));
                $font->size(22);
                $font->color('#d1d5db');
            });
            $canvas->drawRectangle(60, $y + 10, function ($rect) use ($value) {
                $rect->size((int) (400 * ($value / 100)), 10);
                $rect->background('#9B7DFF');
            });
            $y += 50;
        }

        $canvas->text('SENFLUX', 1010, 590, function ($font) {
            $font->filename(public_path('fonts/Inter-Bold.ttf'));
            $font->size(28);
            $font->color('#9B7DFF');
        });

        return $canvas;
    }
}