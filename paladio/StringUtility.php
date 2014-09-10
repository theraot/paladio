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

		/**
		 * Returns a new string equal to $string that starts with $start.
		 *
		 * If $string starts with $start, the returned string is $string, otherwise $start.$string.
		 *
		 * @param $string: the string to verify.
		 * @param $start: the start to ensure.
		 *
		 * @access public
		 * @return string
		 */
		public static function EnsureStart(/*string*/ $string, /*string*/ $start)
		{
			if (self::StartsWith($string, $start))
			{
				return $string;
			}
			else
			{
				return $start.$string;
			}
		}

		/**
		 * Returns a new string equal to $string that doesn't end with $ending.
		 *
		 * If $string ends with $ending, the returned string is $string without $ending, $string otherwise.
		 *
		 * @param $string: the string to verify.
		 * @param $ending: the ending to neglect.
		 *
		 * @access public
		 * @return string
		 */
		public static function NeglectEnd(/*string*/ $string, /*string*/ $ending)
		{
			self::TryNeglectEnd($string, $ending, $result);
			return $result;
		}

		/**
		 * Returns a new string equal to $string that doesn't start with $start.
		 *
		 * If $string starts with $start, the returned string is $string without $start, $string otherwise.
		 *
		 * @param $string: the string to verify.
		 * @param $start: the start to neglect.
		 *
		 * @access public
		 * @return string
		 */
		public static function NeglectStart(/*string*/ $string, /*string*/ $start)
		{
			self::TryNeglectStart($string, $start, $result);
			return $result;
		}

		/**
		 * Verifies if a string starts with another string.
		 *
		 * Returns true of the string $string starts with $with.
		 *
		 * @param $string: the string to verify.
		 * @param $with: the ending to verify.
		 *
		 * @access public
		 * @return bool
		 */
		public static function StartsWith(/*string*/ $string, /*string*/ $with)
		{
			if (substr($string, 0, strlen($with)) === $with)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Attempts to create a a new string equal to $string that doesn't end with $ending.
		 *
		 * If $string ends with $ending, the returns true, false otherwise.
		 *
		 * @param $string: the string to verify.
		 * @param $ending: the ending to neglect.
		 * @param $result: the new string that was created.
		 *
		 * @access public
		 * @return bool
		 */
		public static function TryNeglectEnd(/*string*/ $string, /*string*/ $ending, /*string*/ &$result)
		{
			$length = strlen($string);
			$endLength = strlen($ending);
			if (substr($string, $length - $endLength) === $ending)
			{
				if ($length < $endLength)
				{
					$result = '';
					return true;
				}
				else
				{
					$result = substr($string, 0, $length - $endLength);
				}
				return true;
			}
			else
			{
				$result = $string;
				return false;
			}
		}

		/**
		 * Attempts to create a a new string equal to $string that doesn't start with $start.
		 *
		 * If $string starts with $start, the returns true, false otherwise.
		 *
		 * @param $string: the string to verify.
		 * @param $ending: the start to neglect.
		 * @param $result: the new string that was created.
		 *
		 * @access public
		 * @return bool
		 */
		public static function TryNeglectStart(/*string*/ $string, /*string*/ $start, /*string*/ &$result)
		{
			$startLength = strlen($start);
			if (substr($string, 0, $startLength) === $start)
			{
				$length = strlen($string);
				if ($length < $startLength)
				{
					$result = '';
					return true;
				}
				else
				{
					$result = substr($string, $startLength);
				}
				return true;
			}
			else
			{
				$result = $string;
				return false;
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