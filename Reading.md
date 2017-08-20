##Custome OpenCart##
####Importent####
The follwoing cron file have been rewrite.
1. `/system/engine/loader.php` for rewrite mode Add custome rewrite logic.
2. `/system/library/template/cTwig.php` clone `/system/library/template/Twig.php` for rewrite template file.
3. `/system/startup.php` use custome `action.php` replace `/system/engine/action.php` for rewrite controller.

The following params need add
1. in your frontend `config.php` add `DIR_REWRITE` dir. `define('DIR_REWRITE', '/rewrite/frontend/');`
2. in your admin `config.php` add `DIR_REWRITE` dir. `define('DIR_REWRITE', '/rewrite/adminhtml/');`
3. In websit root dir add `rewrite` dir.

- - -

####Rewrite dir struct####

```text
root_dir
|--rewrite
|   |--system                  save our custom action.php
|   |--frontend                rewrite frontend class dir
|   |   |--model               rewrite model
|   |   |   |--catalog         keep rewrite dir name
|   |   |   |   |-product.php  keep rewrite file name, rewrite ModelCatalogProduct
|   |   |
|   |   |--view
|   |   |   |--template        keep dir struct and file name
|   |   |
|   |   |--controller
|   |   |   |--catalog
|   |   |   |   |-product.php  rewrite ControllerProductProduct
|   |   |
|   |   |--config.php          define which file need rewite
|   |   |
|   |
|   |
|   |--adminhtml               rewrite frontend class dir
|   |   |--model               rewrite model
|   |   |   |--catalog         keep rewrite dir name
|   |   |   |   |-product.php  keep rewrite file name, rewrite ModelCatalogProduct
|   |   |
|   |   |--view
|   |   |   |--template        keep dir struct and file name
|   |   |   |   |--catalog
|   |   |   |   |   |--product_from.twig
|   |   |
|   |   |--controller
|   |   |   |--catalog
|   |   |   |   |-product.php  rewrite ControllerProductProduct
|   |   |
|   |   |--config.php          define which file need rewite
|   |   |
```

**config.php struct**

```php
$rewrite = [
    'controller' => [  // rewrite controler
        'product/product' => [ // router
            'rewrite' => true,
            'class' => 'ControllerTenfProductProduct' // this rewrite new class
        ]
    ],
    'model' => [  // rewrite model
        'catalog/product' => [ // load uri
            'rewrite' => true,
            'class' => 'ModelTenfCatalogProduct',
        ]
    ],
    'view' => [
        // file path => file path
        'catalog/product_form' => 'catalog/product_form'
    ]
];
```


















