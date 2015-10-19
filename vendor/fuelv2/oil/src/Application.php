<?php
/**
 * @package    Fuel
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2015 - 2016 Fuel Development Team
 * @link       http://fuelphp.org
 */

declare (strict_types=1);

namespace Fuel\Oil;

use Fuel\Foundation\Application as Base;

/**
 * Application class
 *
 * Defines a FuelPHP Application.
 *
 * @package  Fuel\Foundation
 *
 * @since  2.0.0
 */
class Application extends Base
{
	/**
	 * @var  string  Name of this application
	 *
	 * @since  2.0.0
	 */
	protected $name = 'oil';
}
