<?php
/**
 * Created by PhpStorm.
 * User: Daniel.luo
 * Date: 2017/8/19
 * Time: 下午5:53
 */

namespace Template;


final class cTwig {
    private $twig;
    private $data = array();

    public function __construct() {
        // include and register Twig auto-loader
        include_once(DIR_SYSTEM . 'library/template/Twig/Autoloader.php');

        \Twig_Autoloader::register();
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    public function render($template, $cache = false) {
        // specify where to look for templates
        // if rewrite
        // if is rewrite, load $rewrite
        $rewrite = [];
        require(DIR_REWRITE . 'config.php');
        if (isset($rewrite['view'][$template])) {
            $path = DIR_REWRITE . 'view' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR;
        } else {
            $path = DIR_TEMPLATE;
        }

        $loader = new \Twig_Loader_Filesystem($path);

        // initialize Twig environment
        $config = array('autoescape' => false);

        if ($cache) {
            $config['cache'] = DIR_CACHE;
        }

        $this->twig = new \Twig_Environment($loader, $config);

        try {
            // load template
            $template = $this->twig->loadTemplate($template . '.twig');
//            echo '<pre>';
//            print_r($template);
//            echo '</pre>';
            return $template->render($this->data);
        } catch (Exception $e) {
            trigger_error('Error: Could not load template ' . $template . '!');
            exit();
        }
    }
}