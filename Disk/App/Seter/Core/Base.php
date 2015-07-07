<?php


namespace Seter\Core;




/**
 * Class Base
 * ArrayAccess -
interface ArrayAccess
boolean offsetExists($index)
mixed offsetGet($index)
void offsetSet($index, $newvalue)
void offsetUnset($index)
 *
 * 示例
$base = new F();
$base['s'] = 999;       //set
unset($base['s']);      //unset
echo isset($base['s']); //isset
echo $base['s'] ;       //get
 *  IteratorAggregate  ->foreach
其他操作
 * $base->clear     清除所有数据
 */

class Base implements \ArrayAccess, \Countable, \IteratorAggregate {

    /**
     * Key-value array of arbitrary data
     * @var array
     */
    public $data = array();


    public function __construct($items = array())
    {
    }


    /**
     * Normalize data key
     * @param  string $key The data key
     * @return mixed       The transformed/normalized data key
     */
    protected function normalizeKey($key)
    {
        return $key;
    }

    /**
     * Set data key to value
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        $this->data[$this->normalizeKey($key)] = $value;
    }

    /**
     * Get data value with key
     * @param  string $key     The data key
     * @param  mixed  $default The value to return if data key does not exist
     * @return mixed           The data value, or the default value
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            $isInvokable = is_object($this->data[$this->normalizeKey($key)]) && method_exists($this->data[$this->normalizeKey($key)], '__invoke');
            return $isInvokable ? $this->data[$this->normalizeKey($key)]($this) : $this->data[$this->normalizeKey($key)];
        }
        return $default;
    }

    /**
     * Add data to set
     * @param array $items Key-value array of data to append to this set
     */
    public function replace($items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value); // Ensure keys are normalized
        }
    }

    /**
     * Fetch set data
     * @return array This set's key-value data array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Fetch set data keys
     * @return array This set's key-value data array keys
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * Does this set contain a key?
     * @param  string  $key The data key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($this->normalizeKey($key), $this->data);
    }

    /**
     * Remove value with key from this set
     * @param  string $key The data key
     */
    public function remove($key)
    {
        unset($this->data[$this->normalizeKey($key)]);
    }

    /**
     * Property Overloading
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __isset($key)
    {
        return $this->has($key);
    }

    public function __unset($key)
    {
        return $this->remove($key);
    }

    /**
     * Clear all values
     */
    public function clear()
    {
        $this->data = array();
    }




    /**
     * Array Access
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    /**
     * end Array Access
     */

    /**
     * Countable ->Countable
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * IteratorAggregate
     * foreach
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    function __call($function_name, $args)
    {
        echo "function: $function_name (<br />";
        var_dump($args);
        echo ") not exist！";
    }

}

