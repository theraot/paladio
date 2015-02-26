<?php namespace paladio;

	if (count(get_included_files()) == 1)
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}

	/**
	 * Configuration
	 * @package Paladio
	 */
	final class Configuration
	{
		//------------------------------------------------------------
		// Private (Class)
		//------------------------------------------------------------

		private static $data;

		//------------------------------------------------------------
		// Public (Class)
		//------------------------------------------------------------

		public static function Add(/*array*/ $configuration)
		{
			if (self::$data === null)
			{
				self::$data = array();
			}
			if (is_array($configuration))
			{
				foreach ($configuration as $key => $value)
				{
					if
					(
						array_key_exists($key, self::$data)
						&& is_array(self::$data[$key])
						&& is_array($configuration[$key])
					)
					{
						self::$data[$key] = array_replace_recursive(self::$data[$key], $configuration[$key]);
					}
					else
					{
						self::$data[$key] = $configuration[$key];
					}
				}
			}
		}

		/**
		 * Verifies if the category with the name $categoryName is available.
		 *
		 * If the category with the name $categoryName exists returns true, false otherwise.
		 *
		 * @param $categoryName: The name of the requested category.
		 *
		 * @access public
		 * @return bool
		 */
		public static function CategoryExists(/*mixed*/ $categoryName)
		{
			if (self::$data  === null)
			{
				return false;
			}
			else
			{
				if (is_string($categoryName))
				{
					return array_key_exists($categoryName, self::$data);
				}
				else if (is_array($categoryName))
				{
					$categoryNames = $categoryName;
					foreach ($categoryName as $categoryName)
					{
						if (!array_key_exists($categoryName, self::$data))
						{
							return false;
						}
					}
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		/**
		 * Reads the value of the configuration field identified by $fieldName in the category with the name $categoryName.
		 *
		 * Returns the value of the configuration field if it is available, $default otherwise.
		 *
		 * @param $categoryName: The name of the requested category.
		 * @param $fieldName: The name of the requested field.
		 * @param $default: The value to fallback when the field is not available.
		 *
		 * @access public
		 * @return mixed
		 */
		public static function Get(/*string*/ $categoryName, /*string*/ $fieldName, /*mixed*/ $default = null)
		{
			if (self::$data !== null && array_key_exists($categoryName, self::$data) && array_key_exists($fieldName, self::$data[$categoryName]))
			{
				return self::$data[$categoryName][$fieldName];
			}
			else
			{
				return $default;
			}
		}

		/**
		 * Attempts to reads the value of the configuration field identified by $fieldName in the category with the name $categoryName.
		 *
		 * Sets $result to the value of the configuration field if it is available, it is left untouched otherwise.
		 *
		 * Returns true if the configuration field is available, false otherwise.
		 *
		 * @param $categoryName: The name of the requested category.
		 * @param $fieldName: The name of the requested field.
		 * @param &$result: Set to the readed value, left untouched if the field is not available.
		 *
		 * @access public
		 * @return bool
		 */
		public static function TryGet(/*string*/ $categoryName, /*string*/ $fieldName, /*mixed*/ &$result)
		{
			if (self::$data !== null && array_key_exists($categoryName, self::$data) && array_key_exists($fieldName, self::$data[$categoryName]))
			{
				$result = self::$data[$categoryName][$fieldName];
				return true;
			}
			else
			{
				return false;
			}
		}

		//------------------------------------------------------------
		// Public (Constructors)
		//------------------------------------------------------------

		/**
		 * Creating instances of this class is not allowed.
		 */
		public function __construct()
		{
			throw new \Exception('Creating instances of '.__CLASS__.' is forbidden');
		}
	}