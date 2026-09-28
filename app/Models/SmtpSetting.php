<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'host', 'port', 'encryption', 'authentication_required',
        'username', 'password_encrypted', 'from_address', 'from_name',
        'is_active', 'last_tested_at', 'last_test_status',
    ];

    protected $hidden = ['password_encrypted'];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'authentication_required' => 'boolean',
            'is_active' => 'boolean',
            'last_tested_at' => 'datetime',
        ];
    }
}
