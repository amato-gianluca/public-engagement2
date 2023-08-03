<?php
/*
Pleast copy this file to config.php and set the correct values for constants, or provide them
trough environment variables.
*/
define('ESSE3_DSN', 'mysql:host=esse3.labeconomia.unich.it;dbname=esse3;charset=utf8mb4');
define('ESSE3_USERNAME', '');
define('ESSE3_PASSWORD', '');

define('IRIS_HOST', 'iris.labeconomia.unich.it');
define('IRIS_USERNAME', '');
define('IRIS_PASSWORD', '');

define('PE_DNS', 'mysql:host=db;dbname=pe;charset=utf8mb4');
define('PE_USERNAME','pe');
define('PE_PASSWORD','');

define('ADMIN_USERNAME', '');
define('ERROR_MODE', 'debug');

define('DEFAULT_SEARCH_LIMIT',20);

define('DEVELOPER_NAME', 'Gianluca Amato');
define('DEVELOPER_ADDRESS', 'gianluca.amato@unich.it');

/**
 * Deprtment code of the personell which is entitled to use this web site. If it is 'all',
 * the site is open to the personell of the entire university.
 */
define('DEPARTMENT_CODE', 'all');

/**
 * Determines which authors are shown as results of queries.
 * If 'all', all relevant authors are shown.
 * If 'department', only shows authors of the department whose code is in the DEPARTMENT_CODE config variabel.
 * If 'registered', only shows authors registered in this web site.
 */
define('SEARCH_RESULTS_MODE', 'all');

/**
 * DSN for Sentry exception reporting
 */
define('DSN_SENTRY', '');
?>
