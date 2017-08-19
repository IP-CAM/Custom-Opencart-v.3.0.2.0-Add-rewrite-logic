<?php
$rewrite = [
    'controller' => [
        'product/product' => [
            'rewrite' => true,
            'class' => 'ControllerTenfProductProduct'
        ]
    ],
    'model' => [
        'catalog/product' => [
            'rewrite' => true,
            'class' => 'ModelTenfCatalogProduct',
        ]
    ],
];