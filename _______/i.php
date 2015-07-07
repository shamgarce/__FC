<?php
//name

define('IN_FAST_APP', TRUE);
define('WDS', DIRECTORY_SEPARATOR);

/**
 * 环境，生产环境？开发环境？
// */
define('ENVIRONMENT', 'testing');
//
if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
            error_reporting(E_ALL && ~E_NOTICE);
        break;

        case 'testing':
            error_reporting(E_ALL);
            break;

        case 'production':
            error_reporting(0);
        break;

        default:
            exit('The application environment is not set correctly.');
    }
}


echo 'port   70';

include 'Core/Common.php';
