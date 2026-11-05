<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LinkStats
{
    public function __construct(private Link $link)
    {
    }

    public function total(): int
    {
        return $this->link->clicks()->count();
    }

    /**
     * Clics por día de los últimos $days días, con ceros en los días sin visitas.
     */
    public function clicksPerDay(int $days = 30): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = $this->link->clicks()
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $from->copy()->addDays($i)->toDateString();
            $labels[] = Carbon::parse($day)->format('d/m');
            $values[] = (int) ($rows[$day] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function topBrowsers(): Collection
    {
        return $this->topBy('browser');
    }

    public function topOs(): Collection
    {
        return $this->topBy('os');
    }

    public function topDevices(): Collection
    {
        return $this->topBy('device');
    }

    public function topReferers(): Collection
    {
        return $this->topBy('referer', 'Directo / desconocido');
    }

    private function topBy(string $column, string $fallback = 'Desconocido', int $limit = 5): Collection
    {
        return $this->link->clicks()
            ->selectRaw("COALESCE($column, ?) as label, COUNT(*) as total", [$fallback])
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}