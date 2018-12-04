<?php

namespace Handscube\Kernel;

use Handscube\Foundations\BaseCollection;

/**
 * Class Collection [c]Handscube
 * @author J.W.
 *
 */
class Collection extends BaseCollection
{
    protected $count;
    protected $container;

    public function __construct(array $container = [])
    {
        $this->container = $container;
    }

    /**
     * Add element to collection,
     * Replaceing existing key when the param $forceReplaced is true,
     * and do noting with existing key when the param $foreceReplaced is false.
     *
     * @param array $newContainer
     * @param boolean $forceRepaced
     * @return bool
     */
    public function replace(array $newContainer, bool $forceRepaced = true)
    {
        if ($forceRepaced === true) {
            foreach ($newContainer as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            foreach ($newContainer as $key => $value) {
                $this->setnx($key, $value);
            }
        }
        return true;

    }

    /**
     * Replace element in colleciton that does not exists.
     *
     * @param array $newContainer
     * @return void
     */
    public function replacenx(array $newContainer)
    {
        return $this->replace($newContainer, false);
    }

    /**
     * Set element to Collection.
     *
     * @param [type] $key
     * @param [type] $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * Set element and do not replace existing element.
     *
     * @param [type] $key
     * @param [type] $value
     * @return void
     */
    public function setnx($key, $value)
    {
        if (!$this->container[$key]) {
            $this->container[$key] = $value;
            return true;
        }
        return false;
    }

    /**
     * Get element from container.
     *
     * @param [type] $key
     * @return void
     */
    public function get($key)
    {
        return isset($this->container[$key]) ? $this->container[$key] : null;
    }

    /**
     * Remove element from container.
     *
     * @param [type] $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->container[$key]);
    }

    /**
     * Clear container.
     *
     * @return void
     */
    public function clear()
    {
        $this->container = [];
    }

    /**
     * Return container keys.
     *
     * @return void
     */
    public function keys()
    {
        return array_keys($this->container);
    }

    /**
     * Check an element whether exists or not.
     *
     * @param [type] $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->container);
    }

    /** ArrayAccess interface imp */

    public function offsetSet($key, $value)
    {
        return $this->set($key, $value);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetExists($key)
    {
        return $this->has($key);
    }

    public function offsetUnset($key)
    {
        return $this->remove($key);
    }

    /** IteratorAggregate imp */

    public function getIterator()
    {
        return new \ArrayIterator($this->container);
    }

    /** CountAble imp */

    public function count()
    {
        return count($this->container);
    }

}
