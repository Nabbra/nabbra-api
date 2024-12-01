<?php

namespace App\Models\Helpers;

trait AudiogramHelpers
{
    /**
     * Is audiogram for left ear.
     */
    public function isLeftEar()
    {
        return $this->type == self::LEFT;
    }

    /**
     * Is audiogram for right ear.
     */
    public function isRightEar()
    {
        return $this->type == self::RIGHT;
    }
}
