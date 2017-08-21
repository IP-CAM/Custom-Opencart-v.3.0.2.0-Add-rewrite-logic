##Custome OpenCart
####Importent
The follwoing cron file have been rewrite.
1. `/system/engine/loader.php` for rewrite mode Add custome rewrite logic.
2. `/system/library/template/ctwig.php` clone `/system/library/template/twig.php` for rewrite template file.
3. `/system/startup.php` use custome `action.php` replace `/system/engine/action.php` for rewrite controller.

The following params need add
1. File `/install/controller/install/step_3.php` **Line 34** add 

```php
$output .= 'define(\'DIR_REWRITE\', \'' . addslashes(DIR_OPENCART) . 'rewrite/frontend/\');' . "\n\n";
```
**Line 87** add

```php
$output .= 'define(\'DIR_REWRITE\', \'' . addslashes(DIR_OPENCART) . 'rewrite/adminhtml/\');' . "\n";
```

2. Modify `/system/startup.php` use the following script replace origin

```php
// Engine
// require_once(modification(DIR_SYSTEM . 'engine/action.php'));
// use rewrite router
if (defined('DIR_REWRITE')) {
    require_once(modification(dirname(DIR_SYSTEM) . DIRECTORY_SEPARATOR . 'rewrite' . DIRECTORY_SEPARATOR . 'system/engine/action.php'));
    require_once(modification(dirname(DIR_SYSTEM) . DIRECTORY_SEPARATOR . 'rewrite' . DIRECTORY_SEPARATOR . 'system/engine/loader.php'));
} else {
    require_once(modification(DIR_SYSTEM . 'engine/action.php'));
    require_once(modification(DIR_SYSTEM . 'engine/loader.php'));
}
```

3. In website root dir add `rewrite` dir.

- - -

####Rewrite dir struct

```text
root_dir
|--rewrite
|   |--system                  save our custom action.php
|   |   |--action.php
|   |   |--loader.php
|   |
|   |
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


















