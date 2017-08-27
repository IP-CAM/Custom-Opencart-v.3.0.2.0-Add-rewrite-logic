<?php
/**
 * Created by PhpStorm.
 * User: Daniel.luo
 * Date: 2017/8/27
 * Time: 下午12:23
 */

class ModelTenfCustomerAddress extends AbstractModel
{
    public function getPrimaryKey()
    {
        return 'address_id';
    }

    public function getTable()
    {
        return 'address';
    }
}