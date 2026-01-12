<?php

namespace App\Filament\Resources\ScheduledMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ScheduledMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('message_type')
                    ->options(['direct' => 'Direct', 'template' => 'Template'])
                    ->required(),
                Textarea::make('direct_message')
                    ->columnSpanFull(),
                Select::make('template_id')
                    ->relationship('template', 'name'),
                DateTimePicker::make('scheduled_at')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('variables'),
                TextInput::make('batch_size')
                    ->required()
                    ->numeric()
                    ->default(20),
                TextInput::make('batch_delay')
                    ->required()
                    ->numeric()
                    ->default(60),
                TextInput::make('created_by')
                    ->numeric(),
            ]);
    }
}
