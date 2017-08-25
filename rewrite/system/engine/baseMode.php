<?php

/**
 * All rights reserved.
 *
 * @authors daniel (luo3555@qq.com)
 * @date    17-8-25 下午5:29
 * @version 0.1.0
 */
class BaseObject
{
    protected $_data = [];

    protected $_fieldMap = [];

    public function __construct($data=null)
    {
        // default set data
    }

    public function __call($name, $arguments)
    {
        $prefix = strtolower(substr($name, 0, 3));
        $key = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", substr($name, 3)));

        switch ($prefix) {
            case 'set':
                return $this->setData($key, (isset($arguments[0]) ? $arguments[0] : null));
                break;
            case 'get':
                return $this->getData($key, (isset($arguments[0]) ? $arguments[0] : null));
                break;
            default:
//                throw new Exception(sprintf('function %s not exist!', $key));
                break;
        }
    }

    public function getData($key=null, $defaultValue=null)
    {
        if (is_null($key)) {
            $defaultValue = $this->_data;
        } else {
            if ($this->hasData($key)) {
                $defaultValue = $this->_data[$key];
            }
        }
        return $defaultValue;
    }

    public function hasData($key)
    {
        return isset($this->_data[$key]);
    }

    public function setData($key, $value=null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_data[$k] = $v;
            }
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    protected function _getMappingKey($key)
    {
        return isset($this->_fieldMap[$key]) ? $this->_fieldMap[$key] : $key ;
    }

    public function exception(Exception $exception)
    {
        $this->log($exception->getMessage(), 1, 2);
    }

    public function log($data, $classstep = 6, $function = 6, $file='system') {
        $backtrace = debug_backtrace();
        $log = new Log($file . '.log');
        $log->write('(' . $backtrace[$classstep]['class'] . '::' . $backtrace[$function]['function'] . ') - ' . print_r($data, true));
    }
}