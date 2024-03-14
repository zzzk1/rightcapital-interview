<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * This class can be used to operate table note.
 */
class Note extends Model
{
    protected $table = 'notes';
    use HasFactory;

    /**
     * This method use to find many tags for target note.
     *
     * @return BelongsToMany A lot of tag classes or only one, maybe none.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'notes_tags');
    }
}
