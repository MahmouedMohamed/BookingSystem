<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimeHelper
{
    private function getWeekDay($date)
    {
        return Carbon::parse($date)->dayOfWeek;
    }

    private function calculateNextWeekday($dateStart, $targetWeekday)
    {
        // Calculate the first occurrence of the weekday from date_start
        $startDate = Carbon::parse($dateStart);
        $currentWeekday = $startDate->dayOfWeek;

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
        return $startDate->addDays($daysToAdd)->format('Y-m-d');
    }

    /**
     * Convert integer timezone offset to MySQL timezone format (e.g., "+03:00", "-05:00")
     */
    private function getMySQLTimezoneString(int $offset): string
    {
        $sign = $offset >= 0 ? '+' : '-';
        $hours = abs($offset);

        return sprintf('%s%02d:00', $sign, $hours);
    }
}
