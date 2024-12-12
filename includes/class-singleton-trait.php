<?php
namespace WebSpark\MyProduct;

trait SingletonTrait {
	/**
	 * Singleton instance
	 *
	 * @var mixed
	 */
	protected static $instance = array();

	/**
	 * Get singleton instance
	 *
	 * @return mixed
	 */
	public static function get_instance() {
		$called_class = get_called_class();

		if ( ! isset( self::$instance[ $called_class ] ) ) {
			self::$instance[ $called_class ] = new $called_class();
		}

		return self::$instance[ $called_class ];
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of the instance
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of the instance
	 */
	public function __wakeup() {}
}
