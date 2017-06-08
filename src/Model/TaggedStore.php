<?php

/*
 * Copyright (c) 2016 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\Cache\Model;

use MS\Cache\Model\Internal\Key;

class TaggedStore extends Store
{
    /**
     * @param array $tags
     * @param bool  $intersect
     *
     * @return array
     */
    public function find(array $tags = [], $intersect = true)
    {
        foreach ($tags as &$tag) {
            $ns = $this->ns;
            $ns->role = 'tag';
            $tag = new Key($tag, $ns);
        }
        unset($tag);

        $keys = call_user_func_array([$this->redis, $intersect ? 'sInter' : 'sUnion'], $tags);

        $values = [];
        foreach ($keys as $key) {
            $key = new Key($key, $this->ns);
            $values[(string) $key] = $this->get($key);
        }

        return (array) $values;
    }

    /**
     * @param string      $key
     * @param TaggedValue $value
     * @param array       $options
     */
    public function set($key, $value, $options = [])
    {
        $this->redis->multi(\Redis::PIPELINE);
        $this->redis->multi(\Redis::MULTI);

        foreach ($value->tags as $tag) {
            $ns = $this->ns;
            $ns->role = 'tag';

            $set = new Set($this->redis, new Key($tag, $ns));
            $set->add($tag);
        }

        $this->redis->set((string) new Key($key, $this->ns), serialize($value), $options);
        $this->redis->exec();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        $this->redis->multi(\Redis::PIPELINE);
        $this->redis->multi(\Redis::MULTI);

        /** @var TaggedValue $value */
        $value = $this->get($key);
        foreach ($value->tags as $tag) {
            $ns = $this->ns;
            $ns->role = 'tag';

            $set = new Set($this->redis, new Key($tag, $ns));
            $set->remove($tag);
        }

        $result = $this->redis->del((string) new Key($key, $this->ns));
        $this->redis->exec();

        return $result;
    }
}
