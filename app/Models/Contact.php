<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'notes',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the groups this contact belongs to
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class, 'contact_group_members')
            ->withTimestamps();
    }

    /**
     * Get the scheduled messages for this contact
     */
    public function scheduledMessages(): BelongsToMany
    {
        return $this->belongsToMany(ScheduledMessage::class, 'scheduled_message_contacts')
            ->withPivot(['status', 'sent_at', 'error'])
            ->withTimestamps();
    }
}
