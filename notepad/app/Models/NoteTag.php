<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteTag extends Model
{
    protected $table = 'notes_tags';
    use HasFactory;
}