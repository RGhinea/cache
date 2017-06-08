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
use MS\ContainerType\Interfaces\Collection;
use MS\Normalizer\ItemTrait;

class Store implements Collection
{
    use ItemTrait;

    /** @var \Redis */
    protected $redis;

    /** @var NS */
    protected $ns;

    /**
     * @param \Redis $redis
     * @param NS     $ns
     */
    public function __construct(\Redis $redis, NS $ns)
    {
        $this->redis = $redis;
        $this->ns = $ns;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->redis->exists((string) new Key($key, $this->ns));
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $value = $this->redis->get((string) new Key($key, $this->ns));

        return unserialize($value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     */
    public function add($key, $value, $options = [])
    {
        $this->redis->set((string) new Key($key, $this->ns), serialize($value), ['nx'] + $options);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     */
    public function set($key, $value, $options = [])
    {
        $this->redis->set((string) new Key($key, $this->ns), serialize($value), $options);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     */
    public function replace($key, $value, $options = [])
    {
        $this->redis->set((string) new Key($key, $this->ns), serialize($value), ['xx'] + $options);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return $this->redis->del((string) new Key($key, $this->ns));
    }
}
