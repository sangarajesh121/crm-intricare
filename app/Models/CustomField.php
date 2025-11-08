<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    const FIELD_TYPES = ['text', 'number', 'date', 'file'];

    protected $fillable = [
        'field_name',
        'field_key',
        'field_type',
    ];
}
