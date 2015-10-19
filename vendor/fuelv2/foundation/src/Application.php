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

namespace Fuel\Foundation;

use UnexpectedValueException;

/**
 * Application class
 *
 * Defines a FuelPHP Application.
 *
 * @package  Fuel\Foundation
 *
 * @since  2.0.0
 */
class Application
{
	/**
	 * @var  string  Name of this application
	 *
	 * @since  2.0.0
	 */
	protected $name = null;

	/**
	 * Return the name of the application
	 *
	 * @since  2.0.0
	 *
	 * @return  string
	 */
	public function getName(): string
	{
		// make sure we have a name
		if ( ! $this->name)
		{
			// TODO: localize
			throw new UnexpectedValueException(sprintf('Required property "name" not set on class %s', get_called_class()));
		}

		// return it
		return $this->name;
	}



}
