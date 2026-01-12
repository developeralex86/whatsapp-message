<?php

namespace App\Filament\Resources\Contacts\Schemas;

use App\Models\ContactGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->placeholder('+1234567890'),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('groups')
                    ->relationship('groups', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Toggle::make('active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
