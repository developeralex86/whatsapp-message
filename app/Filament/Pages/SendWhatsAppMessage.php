<?php

namespace App\Filament\Pages;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\MessageTemplate;
use App\Services\WhatsAppService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SendWhatsAppMessage extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Send Messages';
    protected static ?string $title = 'WhatsApp Messaging';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('MessagingTabs')
                    ->tabs([
                        Tab::make('Single Message')
                            ->components([
                                TextInput::make('single_phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required()
                                    ->placeholder('+1234567890')
                                    ->helperText('Include country code with + symbol'),
                                Textarea::make('single_message')
                                    ->label('Message')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Bulk Messages')
                            ->components([
                                Select::make('bulk_contact_source')
                                    ->label('Contact Source')
                                    ->options([
                                        'individual' => 'Individual Contacts',
                                        'group' => 'Contact Group',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('individual'),
                                Select::make('bulk_contacts')
                                    ->label('Select Contacts')
                                    ->multiple()
                                    ->options(Contact::where('active', true)->pluck('name', 'id'))
                                    ->visible(fn ($get) => $get('bulk_contact_source') === 'individual')
                                    ->required(fn ($get) => $get('bulk_contact_source') === 'individual')
                                    ->searchable()
                                    ->preload(),
                                Select::make('bulk_group')
                                    ->label('Select Group')
                                    ->options(ContactGroup::where('active', true)->pluck('name', 'id'))
                                    ->visible(fn ($get) => $get('bulk_contact_source') === 'group')
                                    ->required(fn ($get) => $get('bulk_contact_source') === 'group')
                                    ->searchable()
                                    ->preload(),
                                Textarea::make('bulk_message')
                                    ->label('Message')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),
                                TextInput::make('batch_size')
                                    ->label('Batch Size')
                                    ->numeric()
                                    ->default(20)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->helperText('Number of messages to send in each batch'),
                                TextInput::make('batch_delay')
                                    ->label('Batch Delay (seconds)')
                                    ->numeric()
                                    ->default(60)
                                    ->minValue(1)
                                    ->helperText('Delay between batches in seconds'),
                            ]),
                        Tab::make('Schedule Messages')
                            ->components([
                                Select::make('schedule_contact_source')
                                    ->label('Contact Source')
                                    ->options([
                                        'individual' => 'Individual Contacts',
                                        'group' => 'Contact Group',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('individual'),
                                Select::make('schedule_contacts')
                                    ->label('Select Contacts')
                                    ->multiple()
                                    ->options(Contact::where('active', true)->pluck('name', 'id'))
                                    ->visible(fn ($get) => $get('schedule_contact_source') === 'individual')
                                    ->required(fn ($get) => $get('schedule_contact_source') === 'individual')
                                    ->searchable()
                                    ->preload(),
                                Select::make('schedule_group')
                                    ->label('Select Group')
                                    ->options(ContactGroup::where('active', true)->pluck('name', 'id'))
                                    ->visible(fn ($get) => $get('schedule_contact_source') === 'group')
                                    ->required(fn ($get) => $get('schedule_contact_source') === 'group')
                                    ->searchable()
                                    ->preload(),
                                Select::make('schedule_message_type')
                                    ->label('Message Type')
                                    ->options([
                                        'direct' => 'Direct Message',
                                        'template' => 'Template Message',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('direct'),
                                Textarea::make('schedule_direct_message')
                                    ->label('Message')
                                    ->visible(fn ($get) => $get('schedule_message_type') === 'direct')
                                    ->required(fn ($get) => $get('schedule_message_type') === 'direct')
                                    ->rows(5),
                                Select::make('schedule_template')
                                    ->label('Select Template')
                                    ->options(MessageTemplate::where('active', true)->pluck('name', 'id'))
                                    ->visible(fn ($get) => $get('schedule_message_type') === 'template')
                                    ->required(fn ($get) => $get('schedule_message_type') === 'template')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('scheduled_at')
                                    ->label('Schedule Date/Time')
                                    ->required()
                                    ->minDate(now())
                                    ->helperText('Select when to send the messages'),
                                TextInput::make('schedule_batch_size')
                                    ->label('Batch Size')
                                    ->numeric()
                                    ->default(20)
                                    ->minValue(1)
                                    ->maxValue(100),
                                TextInput::make('schedule_batch_delay')
                                    ->label('Batch Delay (seconds)')
                                    ->numeric()
                                    ->default(60)
                                    ->minValue(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function getFormContentComponent(): Form
    {
        return Form::make([
            EmbeddedSchema::make('form')
        ])
        ->id('form')
        ->footer([
            Actions::make($this->getFormActions())
                ->key('form-actions'),
        ]);
    }

    public function sendSingle(): void
    {
        $data = $this->form->getState();
        
        $whatsAppService = app(WhatsAppService::class);
        $result = $whatsAppService->sendMessage(
            $data['single_phone'],
            $data['single_message']
        );

        if ($result['success']) {
            Notification::make()
                ->success()
                ->title('Message Sent')
                ->body('Your WhatsApp message has been sent successfully.')
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Failed to Send')
                ->body('Failed to send message: ' . ($result['error'] ?? 'Unknown error'))
                ->send();
        }

        $this->form->fill();
    }

    public function sendBulk(): void
    {
        $data = $this->form->getState();
        
        // Get contacts based on source
        if ($data['bulk_contact_source'] === 'individual') {
            $contacts = Contact::whereIn('id', $data['bulk_contacts'])->where('active', true)->get();
        } else {
            $group = ContactGroup::findOrFail($data['bulk_group']);
            $contacts = $group->contacts()->where('active', true)->get();
        }

        $batchSize = $data['batch_size'] ?? 20;
        $batchDelay = $data['batch_delay'] ?? 60;
        
        $whatsAppService = app(WhatsAppService::class);
        $results = [];
        $contactBatches = $contacts->chunk($batchSize);
        
        foreach ($contactBatches as $index => $batch) {
            if ($index > 0) {
                sleep($batchDelay);
            }
            
            foreach ($batch as $contact) {
                $results[$contact->id] = $whatsAppService->sendMessage(
                    $contact->phone_number,
                    $data['bulk_message']
                );
            }
        }
        
        $successCount = count(array_filter($results, fn($result) => $result['success'] ?? false));
        
        Notification::make()
            ->success()
            ->title('Bulk Messages Sent')
            ->body("Sent {$successCount} out of " . count($results) . " messages successfully.")
            ->send();

        $this->form->fill();
    }

    public function schedule(): void
    {
        $data = $this->form->getState();
        
        // Get contacts based on source
        if ($data['schedule_contact_source'] === 'individual') {
            $contactIds = $data['schedule_contacts'];
        } else {
            $group = ContactGroup::findOrFail($data['schedule_group']);
            $contactIds = $group->contacts()->where('active', true)->pluck('contacts.id')->toArray();
        }

        // Prepare message content
        $messageContent = null;
        if ($data['schedule_message_type'] === 'direct') {
            $messageContent = $data['schedule_direct_message'];
        } else {
            $template = MessageTemplate::findOrFail($data['schedule_template']);
            $messageContent = $template->content;
        }

        // Create scheduled message
        $scheduledMessage = \App\Models\ScheduledMessage::create([
            'message_type' => $data['schedule_message_type'],
            'direct_message' => $data['schedule_message_type'] === 'direct' ? $messageContent : null,
            'template_id' => $data['schedule_message_type'] === 'template' ? $data['schedule_template'] : null,
            'variables' => [],
            'scheduled_at' => $data['scheduled_at'],
            'status' => 'pending',
            'batch_size' => $data['schedule_batch_size'] ?? 20,
            'batch_delay' => $data['schedule_batch_delay'] ?? 60,
            'created_by' => Auth::id(),
        ]);

        $scheduledMessage->contacts()->attach($contactIds);

        Notification::make()
            ->success()
            ->title('Messages Scheduled')
            ->body('Messages have been scheduled successfully. They will be sent at the scheduled time.')
            ->send();

        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('sendSingle')
                ->label('Send Single Message')
                ->submit('sendSingle')
                ->color('success')
                ->icon('heroicon-o-paper-airplane'),
            Action::make('sendBulk')
                ->label('Send Bulk Messages')
                ->submit('sendBulk')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),
            Action::make('schedule')
                ->label('Schedule Messages')
                ->submit('schedule')
                ->color('warning')
                ->icon('heroicon-o-clock'),
        ];
    }
}
