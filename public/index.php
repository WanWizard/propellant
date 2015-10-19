<?php
/**
 * @package    Fuel
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2015 - 2016 Fuel Development Team
 * @link       http://fuelphp.org
 */

use Fuel\Foundation\Fuel;

/**
 * Set error reporting and display errors settings.
 * You may want to change these when in production.
 */
error_reporting(-1);
ini_set('display_errors', 1);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

/**
 * Framework document root
 */
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);

/**
 * Path to the composer vendor root directory
 */
define('VENDORPATH', realpath(__DIR__.'/../vendor/').DIRECTORY_SEPARATOR);

/**
 * Fire up the composer autoloader
 */
$autoloader = require VENDORPATH.'autoload.php';

// **************************** [TEST CODE] ********************************

// Manually add paths for test and development code to the autoloader
$autoloader->addPsr4('Fuel\\Foundation\\', VENDORPATH.'fuelv2/foundation/src');
$autoloader->addPsr4('Fuel\\Demo\\', VENDORPATH.'fuelv2/demo/src');
$autoloader->addPsr4('Fuel\\Oil\\', VENDORPATH.'fuelv2/oil/src');

// **************************** [TEST CODE] ********************************

/**
 * Initialize the framework
 */
Fuel::initialize($autoloader);

/**
 * Forge the "demo" application
 */
Fuel::forge('Fuel\\Demo');

/**
 * Forge the "oil" application
 */
Fuel::forge('Fuel\\Oil', Fuel::PRODUCTION);

/**
 * Let's go girls (and boys)!
 */
Fuel::run();
