<?php

/**
 * All rights reserved.
 *
 * @authors daniel (luo3555@qq.com)
 * @date    17-8-25 下午5:22
 * @version 0.1.0
 */
abstract class AbstructModel extends BaseObject
{
    protected $_db;

    protected $_eventPrefix = 'core';

    /**
     * Get tableName
     * @return string
     */
    public abstract function getTable();

    public function __construct($register)
    {
        $this->_db = $register->db;
    }

    public function getInsertId()
    {
        return $this->_db->id();
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
            try {
                return $this->add($data);
            } catch (\Exception $e) {
                if (issset($data['id'])) {
                    return $this->update($data);
                }
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
                $this->_db->beginTransaction();
                $this->saveBefore($data);
                $result = $this->db-insert($this->getTable(),$data);
                $this->saveAfter($data);
                $this->_db->commit();
            } catch (Exception $e) {
                $this->_db->rollback();
                $this->exception($e);
            }
        }
        return $result;
    }

    protected function saveAfter()
    {
        // do you want to do
    }

    public function update($data=[])
    {
        if ($data === []) {
            $data = $this->_data;
        } else {
            $this->_data = $data;
        }
        $result = false;
        if (!empty($data)) {
            try {
                $this->_db->beginTransaction();
                $result = $this->_db->update($this->getTable(), $data, ['id' => $data['id']]);
                $this->_db->commit();
            } catch (Exception $e) {
                $this->_db->rollback();
                $this->exception($e);
            }
        }
        return $result;
    }

    public function delete($id=null)
    {
        $id = is_null($id) ? $this->getId() : $id ;
        $result = false;
        if ($id) {
            try {
                $this->_db->beginTransaction();
                $result = $this->_db->delete($this->getTable(), ['id' => $id]);
                $this->_db->commit();
            } catch (Exception $e) {
                $this->_db->rollback();
                $this->exception($e);
            }
        }
        return $result;
    }
}