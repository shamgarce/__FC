<?php

/**
 * --------------------系统配置-------------------------
 */
$system['application_folder'] = dirname(__FILE__);

$system['controller_folder'] = $system['application_folder'] . '/controllers';

$system['model_folder'] = $system['application_folder'] . '/models';

$system['view_folder'] = $system['application_folder'] . '/views';

$system['library_folder'] = $system['application_folder'] . '/library';

$system['helper_folder'] = $system['application_folder'] . '/helper';

//$system['error_page_404'] = 'application/error/error_404.php';

//$system['error_page_50x'] = 'application/error/error_50x.php';

//$system['error_page_db'] = 'application/error/error_db.php';

//$system['message_page_view'] = '';

$system['default_controller'] = 'home';

$system['default_controller_method'] = 'index';

$system['controller_method_prefix'] = 'do';

$system['controller_file_subfix'] = '.php';

$system['model_file_subfix'] = '.model.php';

$system['view_file_subfix'] = '.view.php';

$system['library_file_subfix'] = '.class.php';

$system['helper_file_subfix'] = '.php';

$system['helper_file_autoload'] = array();

$system['library_file_autoload'] = array();

$system['models_file_autoload'] = array();

//$system['controller_method_ucfirst'] = TRUE;

//$system['autoload_db'] = FALSE;

$system['debug'] = false;

//$system['error_manage'] = FALSE;

//$system['log_error'] = FALSE;

//$system['log_error_handle'] = array(
//    'error' => '',
//    'exception' => '',
//    'db_error' => '',
//);

//$system['default_timezone'] = 'PRC';


$system['route'] = array(
);
