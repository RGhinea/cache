<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache\Model;

use MS\Cache\Model\Internal\Key;
use MS\Cache\Model\Internal\NS;
use MS\Cache\Model\Internal\Value;
use MS\ContainerType\Interfaces\Set as SetInterface;
use MS\Normalizer\ItemTrait;

class Set implements SetInterface
{
    use ItemTrait;

    /** @var \Redis */
    protected $redis;

    /** @var  NS */
    protected $ns;

    /** @var string */
    protected $key;

    /**
     * @param \Redis $redis
     * @param NS     $ns
     * @param string $key
     */
    public function __construct(\Redis $redis, NS $ns, $key)
    {
        $this->redis = $redis;
        $this->ns = $ns;
        $this->key = new Key($key, $ns);
    }

    /**
     * @param Value $element
     *
     * @return bool
     */
    public function has($element)
    {
        return (bool) $this->redis->sContains(
            $this->key,
            (string) (new Value($element))
        );
    }

    /**
     * @param Value $element
     *
     * @return bool
     */
    public function add($element)
    {
        return (bool) $this->redis->sAdd(
            $this->key,
            (string) (new Value($element))
        );
    }

    /**
     * @param Value $element
     *
     * @return int
     */
    public function remove($element)
    {
        return (bool) $this->redis->sRem(
            $this->key,
            (string) (new Value($element))
        );
    }
}
