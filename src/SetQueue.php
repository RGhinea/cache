<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache;

use MS\Cache\Model\Store;
use MS\ContainerType\Interfaces\Queue;

class SetQueue implements Queue
{
    /** @var \Redis */
    protected $client;

    /** @var array */
    protected $config;

    /** @var string */
    protected $source;

    /**
     * @param \Redis $client
     * @param array  $config
     */
    public function __construct(\Redis $client, $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $source
     */
    public function fromSource($source)
    {
        $this->source = $source;
    }

    /**
     * @param mixed $items,...
     *
     * @return int
     */
    public function enqueue($items)
    {
        $args = func_get_args();
        $args = new Store($args);
        array_unshift($args, $this->source);
        $args = array_map('strval', $args);

        return call_user_func_array([$this->client, 'sAdd'], $args);
    }

    /**
     * @param int $count
     *
     * @return mixed|mixed[]
     */
    public function peek($count = 1)
    {
        $items = $this->client->sRandMember($this->source, $count);

        if (func_num_args() === 0) {
            return reset($items);
        }

        return $items;
    }

    /**
     * @param int $count
     *
     * @return mixed|mixed[]
     */
    public function dequeue($count = 1)
    {
        $items = $this->client->sPop($this->source, $count);

        if (func_num_args() === 0) {
            return reset($items);
        }

        return $items;
    }
}
