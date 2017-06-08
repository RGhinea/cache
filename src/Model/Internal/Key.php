<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache\Model\Internal;

class Key
{
    /** @var string */
    public $value;

    /** @var NS */
    public $ns;

    /**
     * @param string $value
     * @param NS     $ns
     */
    public function __construct($value, NS $ns)
    {
        $this->value = $value;
        $this->ns = $ns;

        $this->fromString($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(':', [$this->ns, $this->value]);
    }

    /**
     * @param $value
     */
    protected function fromString($value)
    {
        if ($this->ns->role and $this->ns->base) {
            return;
        }

        list($base, $role, $this->value) = explode(':', $value, 3);
        $this->ns = new NS($role, $base);
    }
}
