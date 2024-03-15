<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoteTag extends Model
{
    protected $table = 'notes_tags';
    protected $dates = ['deleted_at'];
    use HasFactory;
    use SoftDeletes;
}
