<?php
/**
 * Created by PhpStorm.
 * User: Daniel.luo
 * Date: 2017/8/23
 * Time: 下午10:26
 */

class ModelTenfSalesOrder extends AbstractModel
{
    public function getTable()
    {
        return 'order';
    }

    public function getPrimaryKey()
    {
        return 'order_id';
    }

    public function addProduct($product)
    {

    }
}