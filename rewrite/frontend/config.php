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
        'checkout/cart' => [
            'rewrite' => true,
            'class' => 'ModelTenfCheckoutCart'
        ],
        'sales/order' => [
            'rewrite' => true,
            'class' => 'ModelTenfSalesOrder'
        ],
        'customer/address' => [
            'rewrite' => true,
            'class' => 'ModelTenfCustomerAddress'
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