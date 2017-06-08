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

class Lock
{
    /** @var \Redis */
    protected $redis;

    /** @var NS */
    protected $ns;

    /** @var Key */
    protected $name;

    /** @var string */
    protected $secret;

    /**
     * @param \Redis $redis
     * @param NS     $ns
     * @param string $name
     * @param string $secret
     */
    public function __construct(\Redis $redis, NS $ns, $name, $secret = null)
    {
        if ($secret === null) {
            $secret = openssl_random_pseudo_bytes(32);
            $secret = base64_encode($secret);
            $secret = rtrim($secret, '=');
        }

        $this->redis = $redis;
        $this->ns = $ns;
        $this->name = new Key($name, $ns);
        $this->secret = $secret;
    }

    /**
     * @return bool
     */
    public function check()
    {
        return $this->redis->get((string) $this->name) === $this->secret;
    }

    /**
     * @param int  $ttl
     * @param bool $blocking
     *
     * @return bool
     */
    public function acquire($ttl = null, $blocking = false)
    {
        $ttl = is_numeric($ttl) ? $ttl : 1;
        $options = ['nx', 'px' => $ttl ? (int) ($ttl * 1000) : null];
        $options = array_filter($options);

        while (!($result = $this->redis->set((string) $this->name, $this->secret, $options)) and $blocking) {
            usleep(5 * 1000);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function release()
    {
        if (!$this->check()) {
            return false;
        }

        if (!$this->redis->del((string) $this->name)) {
            return false;
        }

        return true;
    }
}
