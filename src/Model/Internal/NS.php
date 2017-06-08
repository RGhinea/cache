<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache\Model\Internal;

class NS
{
    /** @var string */
    public $role;

    /** @var string */
    public $base;

    /**
     * @param string $role
     * @param string $base
     */
    public function __construct($role, $base)
    {
        $this->role = $role;
        $this->base = $base;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->base and $this->role;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(':', [$this->base, $this->role]);
    }
}
