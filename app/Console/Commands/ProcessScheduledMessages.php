<?php

namespace App\Console\Commands;

use App\Models\ScheduledMessage;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled WhatsApp messages';

    /**
     * The WhatsApp service.
     *
     * @var WhatsAppService
     */
    protected $whatsAppService;

    /**
     * Create a new command instance.
     */
    public function __construct(WhatsAppService $whatsAppService)
    {
        parent::__construct();
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find scheduled messages that are due
        $scheduledMessages = ScheduledMessage::where('status', 'pending')
            ->where('scheduled_at', '<=', $now)
            ->get();
            
        if ($scheduledMessages->isEmpty()) {
            $this->info('No scheduled messages to process.');
            return 0;
        }
        
        $this->info('Found ' . $scheduledMessages->count() . ' scheduled messages to process.');
        
        foreach ($scheduledMessages as $scheduledMessage) {
            $this->processScheduledMessage($scheduledMessage);
        }
        
        return 0;
    }
    
    /**
     * Process a single scheduled message
     */
    protected function processScheduledMessage(ScheduledMessage $scheduledMessage)
    {
        $this->info('Processing scheduled message #' . $scheduledMessage->id);
        
        try {
            // Mark as processing
            $scheduledMessage->update(['status' => 'processing']);
            
            // Get pending contacts for this message
            $pendingContacts = $scheduledMessage->contacts()
                ->wherePivot('status', 'pending')
                ->get();
                
            if ($pendingContacts->isEmpty()) {
                $this->info('No pending contacts for message #' . $scheduledMessage->id);
                $scheduledMessage->update(['status' => 'completed']);
                return;
            }
            
            $this->info('Sending to ' . $pendingContacts->count() . ' contacts');
            
            // Process in batches for cost optimization
            $contactBatches = $pendingContacts->chunk($scheduledMessage->batch_size);
            
            foreach ($contactBatches as $index => $batch) {
                // Add delay between batches except for the first one
                if ($index > 0) {
                    $this->info('Waiting ' . $scheduledMessage->batch_delay . ' seconds before next batch');
                    sleep($scheduledMessage->batch_delay);
                }
                
                $this->info('Processing batch ' . ($index + 1) . ' of ' . $contactBatches->count());
                
                if ($scheduledMessage->message_type === 'direct') {
                    foreach ($batch as $contact) {
                        $this->sendDirectMessage($scheduledMessage, $contact);
                    }
                } else {
                    $this->sendTemplateMessages($scheduledMessage, $batch);
                }
            }
            
            // Check if all contacts have been processed
            $pendingCount = $scheduledMessage->contacts()
                ->wherePivot('status', 'pending')
                ->count();
                
            if ($pendingCount === 0) {
                $scheduledMessage->update(['status' => 'completed']);
                $this->info('Scheduled message #' . $scheduledMessage->id . ' completed');
            } else {
                $this->warn('Scheduled message #' . $scheduledMessage->id . ' partially completed. ' . $pendingCount . ' contacts remaining.');
            }
            
        } catch (\Exception $e) {
            $this->error('Error processing scheduled message #' . $scheduledMessage->id . ': ' . $e->getMessage());
            Log::error('Error processing scheduled message: ' . $e->getMessage(), [
                'scheduled_message_id' => $scheduledMessage->id,
                'exception' => $e,
            ]);
            
            $scheduledMessage->update(['status' => 'failed']);
        }
    }
    
    /**
     * Send a direct message to a contact
     */
    protected function sendDirectMessage(ScheduledMessage $scheduledMessage, $contact)
    {
        try {
            $result = $this->whatsAppService->sendMessage(
                $contact->phone_number,
                $scheduledMessage->direct_message
            );
            
            $status = $result['success'] ? 'sent' : 'failed';
            $error = $result['success'] ? null : ($result['error'] ?? 'Unknown error');
            
            // Update pivot with result
            $scheduledMessage->contacts()->updateExistingPivot($contact->id, [
                'status' => $status,
                'sent_at' => $status === 'sent' ? Carbon::now() : null,
                'error' => $error,
            ]);
            
            $this->info('Message to ' . $contact->name . ' (' . $contact->phone_number . '): ' . $status);
            
        } catch (\Exception $e) {
            $this->error('Error sending to ' . $contact->name . ': ' . $e->getMessage());
            
            // Update pivot with error
            $scheduledMessage->contacts()->updateExistingPivot($contact->id, [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Send template messages to a batch of contacts
     */
    protected function sendTemplateMessages(ScheduledMessage $scheduledMessage, $contacts)
    {
        try {
            $results = $this->whatsAppService->sendTemplateToContacts(
                $contacts,
                $scheduledMessage->template,
                $scheduledMessage->variables ?? []
            );
            
            foreach ($results as $contactId => $result) {
                $status = $result['result']['success'] ?? false ? 'sent' : 'failed';
                $error = $status === 'sent' ? null : ($result['result']['error'] ?? 'Unknown error');
                
                // Update pivot with result
                $scheduledMessage->contacts()->updateExistingPivot($contactId, [
                    'status' => $status,
                    'sent_at' => $status === 'sent' ? Carbon::now() : null,
                    'error' => $error,
                ]);
                
                $contactInfo = $result['contact'] ?? "Contact #{$contactId}";
                $this->info('Template message to ' . $contactInfo . ': ' . $status);
            }
            
        } catch (\Exception $e) {
            $this->error('Error sending template messages: ' . $e->getMessage());
            
            // Mark all contacts in this batch as failed
            foreach ($contacts as $contact) {
                $scheduledMessage->contacts()->updateExistingPivot($contact->id, [
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
