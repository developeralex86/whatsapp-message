<?php

namespace App\Filament\Resources\ContactGroups;

use App\Filament\Resources\ContactGroups\Pages\CreateContactGroup;
use App\Filament\Resources\ContactGroups\Pages\EditContactGroup;
use App\Filament\Resources\ContactGroups\Pages\ListContactGroups;
use App\Filament\Resources\ContactGroups\Pages\ViewContactGroup;
use App\Filament\Resources\ContactGroups\Schemas\ContactGroupForm;
use App\Filament\Resources\ContactGroups\Schemas\ContactGroupInfolist;
use App\Filament\Resources\ContactGroups\Tables\ContactGroupsTable;
use App\Models\ContactGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactGroupResource extends Resource
{
    protected static ?string $model = ContactGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ContactGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactGroups::route('/'),
            'create' => CreateContactGroup::route('/create'),
            'view' => ViewContactGroup::route('/{record}'),
            'edit' => EditContactGroup::route('/{record}/edit'),
        ];
    }
}
