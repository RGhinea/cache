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
use MS\ContainerType\Interfaces\SortedSet as SortedSetInterface;
use MS\Normalizer\ItemTrait;

class SortedSet implements SortedSetInterface
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
        return $this->redis->zRank(
            $this->key,
            (string) (new Value($element))
        ) !== null;
    }

    /**
     * @param Value $element
     * @param float $score
     *
     * @return bool
     */
    public function add($element, $score = 0.0)
    {
        return (bool) $this->redis->zAdd(
            $this->key,
            $score,
            (string) new Value($element)
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
            (string) new Value($element)
        );
    }

    /**
     * @param Value $element
     *
     * @return mixed|mixed[]
     */
    public function head($element = null)
    {
        if (func_num_args() === 0) {
            return $this->redis->zRange(
                $this->key,
                0,
                1,
                false
            );
        }

        return $this->redis->zRangeByLex(
            $this->key,
            null,
            '('.(new Value($element))
        );
    }

    /**
     * @param Value $from
     * @param Value $to
     *
     * @return mixed[]
     */
    public function subset($from, $to)
    {
        $this->redis->zRangeByLex($this->key, '('.(new Value($from)), '('.(new Value($to)));
    }

    /**
     * @param Value $element
     *
     * @return mixed|mixed[]
     */
    public function tail($element = null)
    {
        if (func_num_args() === 0) {
            return $this->redis->zRange($this->key, -2,-1, false);
        }

        return $this->redis->zRangeByLex($this->key, '('.(new Value($element)),null);
    }
}
