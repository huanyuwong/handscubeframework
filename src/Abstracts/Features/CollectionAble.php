<?php

namespace Handscube\Abstracts\Features;

/**
 * Collection interface. [c]Handscube
 */
interface CollectionAble extends \ArrayAccess, \IteratorAggregate, \Countable
{
    /************ ArrayAccess interface ********** */

    public function offsetExists($key);
    public function offsetGet($key);
    public function offsetSet($key, $value);
    public function offsetUnset($key);

    /************ IteratorAggregate interface ********** */

    public function getIterator();

    /************ Coutable interface ********** */

    public function count();
}
