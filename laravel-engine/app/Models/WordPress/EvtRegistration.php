<?php declare(strict_types=1);

namespace App\Models\WordPress;

use App\Services\WordPress\RegistrationCrypto;
use Illuminate\Database\Eloquent\Model;

class EvtRegistration extends WordPressModel
{
    protected $table = 'evt_registrations';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['registration_uuid', 'payment_uuid', 'gateway_reference', 'event_id', 'ticket_type_id', 'user_id', 'user_access_key', 'full_name', 'email', 'phone', 'ticket_type', 'payment_method', 'amount', 'notes', 'status', 'created_at', 'updated_at'];

    protected static function booted(): void
    {
        static::creating(function (self $registration): void {
            $registration->user_access_key ??= bin2hex(random_bytes(32));
            $registration->encryptPii();
        });

        static::updating(function (self $registration): void {
            $registration->encryptPii();
        });
    }

    public function decryptPii(): self
    {
        foreach (['full_name', 'email', 'phone', 'notes'] as $field) {
            if (isset($this->{$field})) {
                $this->{$field} = RegistrationCrypto::decrypt((string) $this->{$field});
            }
        }

        return $this;
    }

    private function encryptPii(): void
    {
        foreach (['full_name', 'email', 'phone', 'notes'] as $field) {
            if (isset($this->{$field})) {
                $this->{$field} = RegistrationCrypto::encrypt((string) $this->{$field});
            }
        }
    }
}
