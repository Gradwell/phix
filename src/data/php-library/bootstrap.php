<?php

// =========================================================================
//
// tests/bootstrap.php
//		A helping hand for running our unit tests
//
// Author	Stuart Herbert
//		(stuart.herbert@gradwell.com)
//
// Copyright	(c) 2010 Gradwell dot com Ltd
//		All rights reserved
//
// =========================================================================

// step 1: create the APP_TOPDIR constant that all MF components require
define('APP_TOPDIR', realpath(__DIR__ . '/../../php'));
define('APP_LIBDIR', realpath(__DIR__ . '/../../../vendor/lib'));

// step 2: add the tests folder to the include path
set_include_path(realpath(dirname(__FILE__)) . PATH_SEPARATOR . 'php' . PATH_SEPARATOR . get_include_path());

// step 3: add the lib-vendor code to the include path
set_include_path(APP_LIBDIR . PATH_SEPARATOR . get_include_path());

// step 4: add our code to the include path
set_include_path(APP_TOPDIR . PATH_SEPARATOR . get_include_path());

// step 5: find the autoloader, and install it
require_once(APP_LIBDIR . '/gwc.autoloader.php');
