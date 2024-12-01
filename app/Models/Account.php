<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'provider',
        'provider_account_id',
        'refresh_token',
        'access_token',
        'token_type',
        'scope',
        'id_token',
        'session_state',
        'oauth_token_secret',
        'oauth_token',
        'refresh_token_expires_in',
        'expires_at',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
        'oauth_token',
        'oauth_token_secret',
        'provider_account_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'integer',
            'refresh_token_expires_in' => 'integer',
        ];
    }

    /**
     * Get the user associated with the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Find account.
     *
     * @return self
     */
    public static function findByProviderAccountId(string $provider, $providerAccountId)
    {
        return self::query()
            ->where('provider', $provider)
            ->where('provider_account_id', $providerAccountId)
            ->first();
    }
}
