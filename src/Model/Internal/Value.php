<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache\Model\Internal;

class Value implements \Serializable, \JsonSerializable
{
    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->value = json_decode($serialized);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        if (!is_scalar($this->value)) {
            return (array) $this->value;
        }

        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }
}
