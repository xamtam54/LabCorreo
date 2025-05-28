<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BusinessDaysCalculator
{
    // Sumar días hábiles
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

    // Verifica si un día es hábil (solo lunes a viernes)
    protected function isBusinessDay(Carbon $date): bool
    {
        return !in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
    }
}
