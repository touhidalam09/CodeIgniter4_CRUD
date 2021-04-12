<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();


//Global Variable Domain Name Change here


// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('MySchoolController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'MySchoolController::index');

// Role Mangament APPLICATIN 
$routes->group('rolemanagment', function($routes){
	$routes->get('/', 'RoleMgtController::index');
	$routes->get('add', 'RoleMgtController::add');
	$routes->post('add_validation', 'RoleMgtController::add_validation');
	$routes->get('fetch_single_data/(:num)', 'RoleMgtController::fetch_single_data/$1');
	$routes->post('edit_validation', 'RoleMgtController::edit_validation');
	$routes->get('delete/(:num)', 'RoleMgtController::delete/$1');
	$routes->get('pdfDownload', 'RoleMgtController::pdfDownload');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
