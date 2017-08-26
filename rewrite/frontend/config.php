<?php
$rewrite = [
    'controller' => [
        'product/product' => [
            'rewrite' => true,
            'class' => 'ControllerTenfProductProduct'
        ],
        'checkout/checkout' => [
            'rewrite' => true,
            'class' => 'ControllerTenfCheckoutCheckout'
        ]
    ],
    'model' => [
        'catalog/product' => [
            'rewrite' => true,
            'class' => 'ModelTenfCatalogProduct',
        ],
        'sales/order' => [
            'rewrite' => true,
            'class' => 'ModelTenfCheckoutOrder'
        ]
    ],
    'library' => [
        'DB' => [
            'rewrite' => true,
            'class' => 'DB'
        ]
    ],
    'view' => [
        // file path => file path
        'default/template/checkout/checkout' => 'default/template/checkout/checkout'
    ]
];