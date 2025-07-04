<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MaintenanceStatus: string implements HasColor, HasLabel
{
    case Scheduled = 'Scheduled';
    case InProgress = 'In Progress';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    public function getLabel(): ?string
    {
        return str_replace('_', ' ', $this->name);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Scheduled => 'primary',
            self::InProgress => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }
}