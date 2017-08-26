<?php

/**
 * All rights reserved.
 *
 * @authors daniel (luo3555@qq.com)
 * @date    17-8-25 下午5:22
 * @version 0.1.0
 */
abstract class AbstractModel extends BaseObject
{
    protected $_adapt;

    protected $_table;

    protected $_eventPrefix = 'core';

    /**
     * Get tableName
     * @return string
     */
    public abstract function getTable();

    /**
     * get table primary key
     * @return string
     */
    public abstract function getPrimaryKey();

    public function __construct($register)
    {
        $this->_adapt = $register->get('db')->getAdapter();
        $this->_table = $this->getTable();
    }

    public function getInsertId()
    {
        return $this->_adapt->id();
    }

    public function load($id)
    {
        $rows = $this->_adapt->select($this->_table, '*', [$this->getPrimaryKey() => $id]);
        if (!empty($rows)) {
            $this->setData($rows[0]);
        }
        return $this;
    }




    /**
     * @param array $data
     * @return bool
     */
    public function save($data=[])
    {
        if ($data === []) {
            $data = $this->_data;
        } else {
            $this->_data = $data;
        }

        if (!empty($data)) {
            // update
            if (isset($data[$this->getPrimaryKey()])) {
                return $this->update($data);
            } else {
                // insert new
                return $this->add($data);
            }

        }
        return false;
    }

    protected function saveBefore()
    {
        // do you want to do
    }

    public function add($data=[])
    {
        if ($data === []) {
            $data = $this->_data;
        } else {
            $this->_data = $data;
        }
        $result = false;
        if (!empty($data)) {
            try {
                $this->_adapt->beginTransaction();
                $this->saveBefore($data);
                $result = $this->_adapt->insert($this->_table,$data);
                $this->saveAfter($data);
                $this->_adapt->commit();
            } catch (Exception $e) {
                $this->_adapt->rollBack();
                $this->exception($e);
            }
        }
        return $result;
    }

    protected function saveAfter()
    {
        // do you want to do
    }

    protected function update($data=[])
    {
        if ($data === []) {
            $data = $this->_data;
        } else {
            $this->_data = $data;
        }
        $result = false;
        if (!empty($data)) {
            try {
                $this->_adapt->beginTransaction();
                $result = $this->_adapt->update($this->_table, $data, [$this->getPrimaryKey() => $data[$this->getPrimaryKey()]]);
                $this->_adapt->commit();
            } catch (Exception $e) {
                $this->_adapt->rollBack();
                $this->exception($e);
            }
        }
        return $result;
    }

    public function delete($id=null)
    {
        $id = is_null($id) ? $this->getData($this->getPrimaryKey()) : $id ;
        $result = false;
        if ($id) {
            try {
                $this->_adapt->beginTransaction();
                $result = $this->_adapt->delete($this->_table, [$this->getPrimaryKey() => $id]);
                $this->_adapt->commit();
            } catch (Exception $e) {
                $this->_adapt->rollBack();
                $this->exception($e);
            }
        }
        return $result;
    }
}