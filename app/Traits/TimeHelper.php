<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimeHelper
{
    private function calculateNextWeekday($dateStart, $currentWeekDay)
    {
        // Calculate the first occurrence of the weekday from date_start
        $dateStart = Carbon::parse($dateStart);
        $targetWeekday = $currentWeekDay;
        $currentWeekday = $dateStart->dayOfWeek;

        // Calculate days to add to reach the target weekday
        /**
         * Ex. Target is Friday (5), Current is Thursday (4) So Days to Add is 1
         * Else
         * Target is Friday (5), Current is Saturday (6) So Days would be (7 - 6 + 5) => Add 6 Days to start
         * Means If start is 06/09/2025 => Start will be next Friday 12/09/2025
         */
        if ($targetWeekday >= $currentWeekday) {
            $daysToAdd = $targetWeekday - $currentWeekday;
        } else {
            $daysToAdd = 7 - $currentWeekday + $targetWeekday;
        }

        // Store the actual first date of the weekday in the date field
        return $dateStart->addDays($daysToAdd)->format('Y-m-d');
    }
}
