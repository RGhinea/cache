<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache\Model;

use MS\Cache\Model\Internal\Value;

class TaggedValue extends Value
{
    /** @var array|string[] */
    public $tags = [];

    /**
     * @param mixed          $value
     * @param array|string[] $tags
     */
    public function __construct($value, array $tags = [])
    {
        $this->tags = $tags;

        parent::__construct($value);
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
        list($this->value, $this->tags) = json_decode($serialized);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            !is_scalar($this->value) ? (array) $this->value : $this->value,
            (array) $this->tags,
        ];
    }
}
