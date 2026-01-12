<?php

namespace App\Services;

use Twilio\Rest\Client;
use App\Models\Contact;
use App\Models\MessageTemplate;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->fromNumber = config('services.twilio.whatsapp_from');
    }

    /**
     * Send a WhatsApp message to a single recipient
     *
     * @param string $to Recipient phone number with country code (e.g., +1234567890)
     * @param string $message Message content
     * @return array Response from Twilio API
     */
    public function sendMessage(string $to, string $message): array
    {
        try {
            // Format number for WhatsApp
            $to = $this->formatWhatsAppNumber($to);
            
            $message = $this->client->messages->create(
                $to,
                [
                    'from' => $this->formatWhatsAppNumber($this->fromNumber),
                    'body' => $message
                ]
            );

            return [
                'success' => true,
                'message_id' => $message->sid,
                'status' => $message->status,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp message sending failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send a message to multiple recipients
     *
     * @param array $recipients Array of phone numbers
     * @param string $message Message content
     * @return array Results for each recipient
     */
    public function sendBulkMessages(array $recipients, string $message): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->sendMessage($recipient, $message);
        }

        return $results;
    }

    /**
     * Send a template message to multiple recipients with personalized variables
     *
     * @param array $contacts Array of Contact models or IDs
     * @param MessageTemplate|int $template Template model or ID
     * @param array $defaultVariables Default variables to use if not specified per contact
     * @return array Results for each recipient
     */
    public function sendTemplateToContacts($contacts, $template, array $defaultVariables = []): array
    {
        $results = [];
        
        // Get template if ID was provided
        if (!$template instanceof MessageTemplate) {
            $template = MessageTemplate::findOrFail($template);
        }
        
        // Get contacts if IDs were provided
        if (is_array($contacts) && !empty($contacts) && !$contacts[0] instanceof Contact) {
            $contacts = Contact::whereIn('id', $contacts)->where('active', true)->get();
        }
        
        foreach ($contacts as $contact) {
            // Skip inactive contacts
            if (!$contact->active) {
                continue;
            }
            
            // Prepare message with variables
            $messageContent = $this->prepareTemplateMessage($template, $contact, $defaultVariables);
            
            // Send the message
            $results[$contact->id] = [
                'contact' => $contact->name . ' (' . $contact->phone_number . ')',
                'result' => $this->sendMessage($contact->phone_number, $messageContent)
            ];
        }
        
        return $results;
    }

    /**
     * Prepare a template message with personalized variables
     *
     * @param MessageTemplate $template
     * @param Contact $contact
     * @param array $defaultVariables
     * @return string
     */
    protected function prepareTemplateMessage(MessageTemplate $template, Contact $contact, array $defaultVariables = []): string
    {
        $content = $template->content;
        $variables = array_merge(
            $defaultVariables,
            [
                'name' => $contact->name,
                'phone' => $contact->phone_number,
            ]
        );
        
        // Replace variables in the template
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Format a phone number for WhatsApp
     *
     * @param string $number
     * @return string
     */
    protected function formatWhatsAppNumber(string $number): string
    {
        // Remove any non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Ensure number has country code
        if (substr($number, 0, 1) !== '+') {
            $number = '+' . $number;
        }
        
        return 'whatsapp:' . $number;
    }
}
