<?php

namespace App\Models\Relations;

use App\Models\Account;
use App\Models\Audiogram;

trait UserRelations
{
    /**
     * Get social accounts belongs to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get audiograms belongs to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audiograms()
    {
        return $this->hasMany(Audiogram::class);
    }
}
