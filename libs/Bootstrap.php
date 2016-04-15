<?php
/*
 *   $url comes in as a $GET thanks to the .htaccess file which directs it to the root index.php page
 *   Index.php requires files within libs
 *   Index.php instantiantes Bootstrap() as $app;
 *   Bootstrap() returns $_GET as $url
 *   $url is then checked for view = $url[0], methods = $url[1], properties(ies) = $url[2]+
 *
 *   $url[0] fires a controller loads Models with $cont->$model($prop)
 *   Controller loads View with $cont->display_view()
 * */

class Bootstrap
{

    function __construct()
    {
        $url = explode('/', rtrim(filter_input(INPUT_GET, 'url'), '/'));
        /* -------------------------------------------------------------------------------------------- TEST */

        /* prepare a function that will load a model of the same name as the controller */
        function model($cont,$name)
        {
            if (method_exists($cont, 'loadModel')) {
                $cont->loadModel($name);
            }
        }


        /* check $url for controller, now called $cont
         *  - if there is no $url[0], instantiate controller index() and fire method display_view()
         *  - this gets index/index_view etc.
         *  - if the $url does match a controller in the controller directory list
         *  - ...then require that file and instantiate that controller as $cont
         */
        if ($url[0] != null) {
            $directory = 'controllers';
            $controller_list = array_diff(scandir($directory), array('..', '.'));
            if (in_array(strtolower($url[0].'.php'), $controller_list)) {
                require 'controllers/'.$url[0].'.php';
                $cont = new $url[0];
                model($cont,$url[0]);
            } else {
                $cont = 'error';
            }
        } else {
            require 'controllers/index.php';
            $cont = new index();
            $cont->display_view();
            return false;
        }


        /* - the url used for a controler can also find a model...so $url[0] = $model."_model".php
         * - if the model does exist, then it is launched with or without methods
         * - Methods are found in the url after $url[1] as $url[2],$url[3],$url[4] etc.
         * - if a method does exist, it is fired
         * - in either case of no method, then there will be a launch of the model and a launch of the view with display_view()
         * - in any case if there is no model or method or properties, we will check if the controller will launch from $url[0]->display_view()
         * - if that can't be found...find we go to the error page - that URL is messed up.
         */

        $model = $url[0]."_model";
        if (isset($url[1])) {
            $meth = $url[1];
        if (method_exists($cont->{$model}, $meth)) {
                /* check $url for properties, if they exist, place in an array called $prop */
                if (count($url) > 2) {
                    $prop = array();
                    for ($i = 2; $i < count($url); $i++) {
                        array_push($prop, $url[$i]);
                        }
                     /* if there is a method, and properties ... then let's launch that method from the model */
                    if (isset($meth) && isset($prop) && is_object($cont)) {
                        $cont->$model->{$meth}($prop);
                        $cont->display_view();
                    } else {
                        /* $meth & $prop should be set at this point, or the page would have launched - launch the error page... */
                        $cont = 'error';
                    }
                } else {
                    $prop = null;
                     /* if there is a method, and NO properties ... then let's launch that method from the model */
                    if (isset($meth) && is_object($cont)) {
                        $cont->$model->{$meth}($prop);
                        $cont->display_view();
                    } else {
                        /* $meth should be set at this point, or the page would have launched - launch the error page... */
                        $cont = 'error';
                    }
                }
            } else {
                /* $url contained a method, but method_exists() returned false, flag the error page */
                $meth = null;
                $cont = 'error';
            }
        } elseif (method_exists($cont, 'display_view')) {
            /* there was no methods - try and load the controller itself */
            $cont->display_view();
            
            return false;
        } else {
            /* dosen't actually match a file - $cont is set to error, which will later launch the error page... */
            $cont = 'error';
        }

        /* we should have everything by this point to launch a method with or without properties */


        if ($cont == 'error') {
            require 'controllers/error.php';
            $cont = new Error;
            $cont->display_view();
            /* debug */
            echo '<p><pre>';
            echo'1';
            var_dump($controller_list);
            echo'<br>';
            echo'2: $cont: ';
            var_dump($cont);
            echo'<br>';
            echo'3';
            var_dump($meth);
            echo'<br>';
            echo'4';
            var_dump($prop);
            echo '</p></pre>';

            return false;
        }


        /* -------------------------------------------------------------------------------------------- TEST */


        /* an error has occured above */
        if ($cont == 'error') {
            
        }
    }
}