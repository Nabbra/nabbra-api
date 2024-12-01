<?php

namespace App\Models;

use App\Models\Helpers\AudiogramHelpers;
use Illuminate\Database\Eloquent\Model;

class Audiogram extends Model
{
    use AudiogramHelpers;

    const LEFT = 'left';
    const RIGHT = 'right';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'freqs',
        'user_id',
    ];

    /**
     * Get the user associated with the audiogram.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'freqs' => 'json',
        ];
    }
}
