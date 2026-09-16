<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'payment_uuid',
        'registration_uuid',
        'amount',
        'currency',
        'gateway',
        'status',
        'reference',
        'callback_url',
        'response_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'response_payload' => 'array',
    ];
}
