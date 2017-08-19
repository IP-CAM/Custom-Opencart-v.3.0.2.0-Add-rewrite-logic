<?php
/**
 * Created by PhpStorm.
 * User: Daniel.luo
 * Date: 2017/8/18
 * Time: 下午3:23
 */
$rewrite = [
    'controller' => [
        'catalog/product' => [
            'rewrite' => true,
            'class' => 'ControllerTenfCatalogProduct'
        ]
    ],
    'model' => [
        'catalog/product' => [
            'rewrite' => true,
            'class' => 'ModelTenfCatalogProduct',
        ]
    ],
];