<?php
/**
 * Class Singleton
 *
 * This is used for Classes that only need one instance,
 * meaning that they won't be created more than once.
 *
 * @package Theme_Toolbox
 * @since 0.1.0
 * @author John Regan <john@johnregan3.com>
 */

namespace Theme_Toolbox;

/**
 * Class Singleton
 *
 * In a basic sense, Abstract classes are used as "templates" for other
 * classes to extend; they prevent repeating functionality in several
 * different places, allowing you to keep things DRY.
 *
 * @package Theme_Toolbox
 * @since   0.1.0
 */
abstract class Singleton {

	/**
	 * The class instance.
	 *
	 * @var array
	 */
	protected static $_instance = array();

	/**
	 * Class constructor.
	 *
	 * Prevents direct object creation.
	 *
	 * This means that only Theme_Toolbox::get_instance() can be used to
	 * "get" this class.  This prevents multiple instances of the same
	 * class running at the same time.
	 *
	 * @see self::get_instance()
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {}

	/**
	 * Clone.
	 *
	 * Prevent object cloning.
	 *
	 * @since 0.1.0
	 */
	final private function __clone() {}

	/**
	 * Get the Class instance.
	 *
	 * @since 0.1.0
	 *
	 * @return Singleton
	 */
	final public static function get_instance() {
		$class = get_called_class();

		// If no instance exists, create one.
		if ( ! isset( static::$_instance[ $class ] ) ) {
			self::$_instance[ $class ] = new $class();

			// Run the Initialization of the class.
			self::$_instance[ $class ]->_init();
		}

		return self::$_instance[ $class ];
	}

	/**
	 * Initialize the class.
	 *
	 * This is where you load your assets, actions, etc.
	 *
	 * @since 0.1.0
	 */
	protected function _init() {}
}