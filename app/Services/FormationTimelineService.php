<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Carbon\Carbon;

class FormationTimelineService {
    public function group(Collection $events): array {
        $buckets = [];

        foreach ($events->groupBy(fn ($e) => $e->created_at->toDateString()) as $date => $dayEvents) {
            $label = match (true) {
                Carbon::parse($date)->isToday() => 'Today',
                Carbon::parse($date)->isYesterday() => 'Yesterday',
                default => Carbon::parse($date)->format('M j, Y'),
            };

            $buckets[] = [
                'label' => $label,
                'items' => $this->cluster($dayEvents->values()),
            ];
        }

        return $buckets;
    }

    protected function cluster(Collection $events): array {
        $clusters = [];

        foreach ($events as $event) {
            $type = $event->type->category();
            $lastKey = array_key_last($clusters);
            $last = $lastKey !== null ? $clusters[$lastKey] : null;

            $withinWindow = $last
                && $last['type'] === $type
                && $last['events']->last()->created_at->diffInMinutes($event->created_at) <= 5;

            if ($withinWindow) {
                $clusters[$lastKey]['events']->push($event);
            } else {
                $clusters[] = ['type' => $type, 'events' => collect([$event])];
            }
        }

        return array_map(function ($cluster) {
            $count = $cluster['events']->count();

            return [
                'type' => $cluster['type'],
                'primary' => $cluster['events']->first(),
                'count' => $count,
                'others' => $count > 1 ? $cluster['events']->slice(1)->values() : collect(),
            ];
        }, $clusters);
    }
}