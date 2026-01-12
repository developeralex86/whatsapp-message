<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the contacts that belong to this group
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_group_members')
            ->withTimestamps();
    }
}
