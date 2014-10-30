<?php namespace BrightMachine;

/**
 * Allows a class to wrap a pre-existing object & proxy method calls to the target object.
 *
 * @package BrightMachine
 * @author Kelvin Jones kelvin@brightmachine.co.uk
 */
trait ObjectProxy
{
    /**
     * @var object Object to be used to proxy requests to.
     */
    private $objectWrapperTargetObject;
    /**
     * @var array a map of functions to redirect calls to
     */
    private $objectWrapperProxyFunctionMap;

    /**
     * @param $targetObject
     * @return mixed the object that you've assigned
     * @throws \InvalidArgumentException
     */
    private function setTargetObject($targetObject)
    {
        if (!is_object($targetObject)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '$target must be an object, %s given',
                    gettype($targetObject)
                )
            );
        }

        $this->objectWrapperTargetObject = $targetObject;
        return $this;
    }

    /**
     * Give the ObjectWrapper an array of methods to map from -> to, e.g:
     *  ['log' => 'logInfo']
     *
     * @param array $proxyFunctionMap
     * @return $this
     */
    private function setProxyFunctionMap(array $proxyFunctionMap)
    {
        $this->objectWrapperProxyFunctionMap = $proxyFunctionMap;
        return $this;
    }

    /**
     * @param $func
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($func, $args = array())
    {
        $this->assertValidTarget();
        $func = $this->resolveFunctionCall($func);
        if (method_exists($this->objectWrapperTargetObject, $func) || method_exists($this->objectWrapperTargetObject, '__call')) {
            return call_user_func_array([$this->objectWrapperTargetObject, $func], $args);
        } else {
            throw new \BadMethodCallException(sprintf("Attempted to call %s::%s", get_class($this->objectWrapperTargetObject), $func));
        }
    }

    /**
     * Check the function map for a different function to use.
     * @param $func
     * @return mixed
     */
    private function resolveFunctionCall($func)
    {
        if (!$this->objectWrapperProxyFunctionMap) {
            return $func;
        }

        if (array_key_exists($func, $this->objectWrapperProxyFunctionMap)) {
            return $this->objectWrapperProxyFunctionMap[$func];
        } else {
            return $func;
        }
    }

    /**
     * @param $k
     * @param $v
     */
    public function __set($k, $v)
    {
        $this->assertValidTarget();
        $this->objectWrapperTargetObject->$k = $v;
    }

    /**
     * @param $k
     * @return mixed
     */
    public function __get($k)
    {
        $this->assertValidTarget();
        return $this->objectWrapperTargetObject->$k;
    }

    /**
     * @param $k
     * @return bool
     */
    public function __isset($k)
    {
        $this->assertValidTarget();
        return isset($this->objectWrapperTargetObject->$k);
    }

    /**
     * @param $k
     * @return null
     */
    public function __unset($k)
    {
        $this->assertValidTarget();
        return $this->objectWrapperTargetObject->$k = null;
    }

    /**
     * @throws \UnderflowException
     */
    private function assertValidTarget ()
    {
        if (is_null($this->objectWrapperTargetObject)) {
            throw new \UnderflowException('BrightMachine\\ObjectProxy target object is null');
        }
    }
}
