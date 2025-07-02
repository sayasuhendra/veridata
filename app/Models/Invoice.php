<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'line_items' => 'array',
        'raw_ai_response' => 'array',
    ];
}
