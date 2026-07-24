<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [

    'vendor',
    'invoice_date',
    'reference',
    'tax_code',
    'amount',
    'currency',
    'text',
    'file_path',
    'status'
    ];
}
