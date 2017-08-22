<?php
/**
 * Created by PhpStorm.
 * User: Daniel.luo
 * Date: 2017/8/22
 * Time: 下午9:22
 */

trait baseModel
{
    protected $_data = [];

    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function getData($key=null)
    {
        if ($key === null) {
            return $this->_data;
        } else {
            if (isset($this->_data[$key])) {
                return $this->_data[$key];
            }
        }

        return null;
    }

    public function __get($key) {
        //
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }
}