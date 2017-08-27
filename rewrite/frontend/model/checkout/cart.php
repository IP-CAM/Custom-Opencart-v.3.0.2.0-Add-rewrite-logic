<?php
/**
 * Created by PhpStorm.
 * User: Daniel.luo
 * Date: 2017/8/27
 * Time: 上午9:26
 */

class ModelTenfCheckoutCart extends AbstractModel
{
    public function getTable()
    {
        return 'cart';
    }

    public function getPrimaryKey()
    {
        return 'cart_id';
    }

    public function addProduct($productId, $options = [])
    {

    }

    /**
     * Shopping cart validate
     *
     * min qty
     * min total amount
     *
     *
     */
    public function validate()
    {
        // @TODO shopping cart validate
        return true;
    }

    public function isEmpty()
    {

    }
}