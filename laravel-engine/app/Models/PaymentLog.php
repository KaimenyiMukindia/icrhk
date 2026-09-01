<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    protected $fillable = [
        'payment_uuid',
        'event',
        'payload',
        'status',
        'message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
