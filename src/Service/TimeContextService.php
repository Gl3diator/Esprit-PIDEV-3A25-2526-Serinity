<?php

declare(strict_types=1);

namespace App\Service;

final class TimeContextService
{
    public function getTimeOfDay(): string
    {
        $hour = (int) (new \DateTime())->format('H');

        return match (true) {
            $hour >= 6 && $hour < 12 => 'matin',
            $hour >= 12 && $hour < 18 => 'après-midi',
            $hour >= 18 && $hour < 22 => 'soir',
            default => 'soir',
        };
    }
}