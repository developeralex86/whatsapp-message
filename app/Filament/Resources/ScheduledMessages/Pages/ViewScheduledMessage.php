<?php

namespace App\Filament\Resources\ScheduledMessages\Pages;

use App\Filament\Resources\ScheduledMessages\ScheduledMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScheduledMessage extends ViewRecord
{
    protected static string $resource = ScheduledMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
