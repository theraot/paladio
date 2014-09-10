<?php namespace paladio;

	if (count(get_included_files()) === 1)
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}

	/**
	 * FileSystem
	 * @package paladio
	 */
	final class StringUtility
	{
		//------------------------------------------------------------
		// Public (Class)
		//------------------------------------------------------------

		/**
		 * Verifies if a string ends with another string.
		 *
		 * Returns true of the string $string ends with $with.
		 *
		 * @param $string: the string to verify.
		 * @param $with: the ending to verify.
		 *
		 * @access public
		 * @return bool
		 */
		public static function EndsWith(/*string*/ $string, /*string*/ $with)
		{
			if (substr($string, strlen($string) - strlen($with)) === $with)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Returns a new string equal to $string that ends with $ending.
		 *
		 * If $string ends with $ending, the returned string is $string, otherwise $string.$ending.
		 *
		 * @param $string: the string to verify.
		 * @param $ending: the ending to ensure.
		 *
		 * @access public
		 * @return string
		 */
		public static function EnsureEnd(/*string*/ $string, /*string*/ $ending)
		{
			if (self::EndsWith($string, $ending))
			{
				return $string;
			}
			else
			{
				return $string.$ending;
			}
		}

		//------------------------------------------------------------
		// Public (Constructor)
		//------------------------------------------------------------

		/**
		 * Creating instances of this class is not allowed.
		 */
		public function __construct()
		{
			throw new Exception('Creating instances of '.__CLASS__.' is forbidden');
		}
	}