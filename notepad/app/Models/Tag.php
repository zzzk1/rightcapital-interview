<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * This class can be used to operate table tag.
 */
class Tag extends Model
{
    protected $table = 'tags';
    protected $dates = ['deleted_at'];
    use HasFactory;
    use SoftDeletes;

    /**
     * This method use to find many notes for target tag.
     *
     * @return BelongsToMany A lot of note classes or only one, maybe none.
     */
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class, 'notes_tags');
    }
}
