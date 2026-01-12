<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScheduledMessage extends Model
{
    protected $fillable = [
        'message_type',
        'direct_message',
        'template_id',
        'scheduled_at',
        'status',
        'variables',
        'batch_size',
        'batch_delay',
        'created_by'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'variables' => 'array',
        'batch_size' => 'integer',
        'batch_delay' => 'integer',
    ];

    /**
     * Get the message template if using a template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    /**
     * Get the contacts this message is scheduled to be sent to
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'scheduled_message_contacts')
            ->withPivot(['status', 'sent_at', 'error'])
            ->withTimestamps();
    }
}
