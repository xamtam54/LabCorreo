<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BusinessDaysCalculator
{
    protected array $holidays = [];

    public function __construct()
    {
        // Solo cargar si la tabla cache ya existe
        if (Schema::hasTable('cache')) {
            $this->loadHolidays();
        }
    }

    protected function loadHolidays(): void
    {
        $years = [
            now()->year - 1,
            now()->year,
            now()->year + 1,
            now()->year + 2,
            now()->year + 3,
        ];

        foreach ($years as $year) {
            // Guarda en caché 2 años
            $this->holidays[$year] = Cache::remember(
                "holidays_CO_$year",
                now()->addYear(2), // duración del caché
                function () use ($year) {
                    $response = Http::get("https://api.generadordni.es/v2/holidays/holidays", [
                        'country' => 'CO',
                        'year' => $year,
                    ]);

                    if ($response->successful()) {
                        return collect($response->json())
                            ->pluck('date')
                            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                            ->toArray();
                    }

                    return []; // En caso de error, evita crash
                }
            );
        }
    }

    public function addBusinessDays(Carbon $startDate, int $daysToAdd): Carbon
    {
        $date = $startDate->copy();
        $addedDays = 0;

        while ($addedDays < $daysToAdd) {
            $date->addDay();

            if ($this->isBusinessDay($date)) {
                $addedDays++;
            }
        }

        return $date;
    }

    protected function isBusinessDay(Carbon $date): bool
    {
        $year = $date->year;

        return $date->isWeekday() &&
               !in_array($date->format('Y-m-d'), $this->holidays[$year] ?? []);
    }

    public function countBusinessDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $date = $startDate->copy();

        while ($date->lessThanOrEqualTo($endDate)) {
            if ($this->isBusinessDay($date)) {
                $days++;
            }
            $date->addDay();
        }

        return $days;
    }
}
