<?php

namespace App\Filament\Resources\ContactGroups\Pages;

use App\Filament\Resources\ContactGroups\ContactGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactGroup extends ViewRecord
{
    protected static string $resource = ContactGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
