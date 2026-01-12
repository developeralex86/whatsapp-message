<?php

namespace App\Filament\Resources\ScheduledMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScheduledMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('message_type')
                    ->badge(),
                TextEntry::make('direct_message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('template.name')
                    ->label('Template')
                    ->placeholder('-'),
                TextEntry::make('scheduled_at')
                    ->dateTime(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('batch_size')
                    ->numeric(),
                TextEntry::make('batch_delay')
                    ->numeric(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
