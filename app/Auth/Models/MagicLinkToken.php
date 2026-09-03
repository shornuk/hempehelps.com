<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property Carbon $expires_at
 * @property Carbon|null $used_at
 */
#[Fillable(['email', 'token', 'expires_at', 'used_at', 'ip_address'])]
class MagicLinkToken extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public static function issue(string $email, ?string $ip = null): self
    {
        return self::query()->create([
            'email' => strtolower($email),
            'token' => Str::password(64, symbols: false),
            'expires_at' => Carbon::now()->addMinutes(10),
            'ip_address' => $ip,
        ]);
    }

    public function consume(): void
    {
        $this->forceFill(['used_at' => now()])->save();
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
