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
use MS\ContainerType\Interfaces\Collection;
use MS\Normalizer\ItemTrait;

class Hash implements Collection
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
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return (bool) $this->redis->hExists(
            $this->key,
            (string) new Key($key, $this->ns)
        );
    }

    /**
     * @param string $key
     *
     * @return Value
     */
    public function get($key)
    {
        $string = $this->redis->hGet(
            $this->key,
            (string) new Key($key, $this->ns)
        );
        $value = unserialize($string);

        return $value->value;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function add($key, $value)
    {
        $string = $this->redis->hSetNx(
            $this->key,
            (string) new Key($key, $this->ns),
            (string) new Value($value)
        );
        $value = unserialize($string);

        return $value->value;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->redis->hSet(
            $this->key,
            (string) new Key($key, $this->ns),
            (string) new Value($value)
        );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function replace($key, $value)
    {
        $this->redis->multi(\Redis::PIPELINE);
        $this->redis->multi(\Redis::MULTI);

        $this->redis->watch($this->key);
        $string = $this->redis->hGet(
            $this->key,
            (string) new Key($key, $this->ns)
        );
        $value = unserialize($string);

        return $value->value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return $this->redis->hDel(
            $this->key,
            (string) new Key($key, $this->ns)
        );
    }
}
