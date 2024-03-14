<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * This class can be used to operate table tag.
 */
class Tag extends Model
{
    protected $table = 'tags';
    use HasFactory;

    /**
     * This method use to find many notes for target note.
     *
     * @return BelongsToMany A lot of note classes or only one, maybe none.
     */
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class, 'notes_tags');
    }
}