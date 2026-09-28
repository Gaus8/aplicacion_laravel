<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordRecoveryOtp extends Model
{
    protected $table = 'password_recovery_otps';

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['email', 'code_hash', 'attempts', 'sent_at', 'expires_at', 'verified_at'];

    protected $hidden = ['code_hash'];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }
}
