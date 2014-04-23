<?php namespace BrightMachine;

/**
* Allows a class to wrap a pre-existing object, passing all unknown calls to the object passed in
* and returns the returned value.
*
* Use case: you have an object returned from Mongo (a document), and you want to wrap that object in
* a class that implements the domain logic for that object.
*/

trait ObjectWrapper
{
    private $targetObject;

    private function setTargetObject ($target)
    {
        if (!is_object($target)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '$target must be an object, %s given',
                    gettype($target)
                )
            );
        }

        $this->targetObject = $target;
    }

    public function __call ($func, $args = array())
    {
        $this->assertValidTarget();
        return call_user_func_array([$this->targetObject, $func], $args);
    }

    public static function __callStatic($name, $arguments)
    {
        $this->assertValidTarget();
        return forward_static_call_array([$this->targetObject, $func], $args);
    }

    public function __set($k, $v)
    {
        $this->assertValidTarget();
        $this->targetObject->$k = $v;
    }

    public function __get($k)
    {
        $this->assertValidTarget();
        return $this->targetObject->$k;
    }

    public function __isset($k)
    {
        $this->assertValidTarget();
        return isset($this->targetObject->$k);
    }

    public function __unset($k)
    {
        $this->assertValidTarget();
        return $this->targetObject->$k = null;
    }

    private function assertValidTarget ()
    {
        if (is_null($this->targetObject)) {
            throw new \UnderflowException('ObjectWrapper target object is null');
        }
    }
}
