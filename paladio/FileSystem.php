<?php namespace paladio;

	if (count(get_included_files()) === 1)
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}
	else
	{
		require_once('StringUtility.php');
	}

	/**
	 * self
	 * @package paladio
	 */
	final class self
	{
		//------------------------------------------------------------
		// Private (Class)
		//------------------------------------------------------------

		private static function _CreateRelativePath(/*array*/ $absolute, /*array*/ $target, /*string*/ $directory_separator)
		{
			$absoluteLength = count($absolute);
			$targetLength = count($target);
			$result = array();
			$commonLength = min($absoluteLength, $targetLength);
			for ($index = 0; $index < $commonLength; $index++)
			{
				if ($absolute[$index] !== $target[$index])
				{
					break;
				}
			}
			if ($index < $absoluteLength)
			{
				for ($index2 = $index; $index2 < $absoluteLength; $index2++)
				{
					$result[] = '..';
				}
			}
			if ($index < $targetLength)
			{
				for ($index2 = $index; $index2 < $targetLength; $index2++)
				{
					$result[] = $target[$index2];
				}
			}
			if (count($result) === 0)
			{
				$result[] = '.';
			}
			if ($directory_separator === null)
			{
				$directory_separator = DIRECTORY_SEPARATOR;
			}
			return implode($directory_separator, $result);
		}

		private static function _ProcessPath(/*array*/ $folders)
		{
			$result = array();
			$count = 0;
			while (($folder = array_shift($folders)) !== null)
			{
				if ($folder === '.')
				{
					continue;
				}
				if ($folder === '..')
				{
					if ($count > 0)
					{
						array_pop($result);
						$count--;
						continue;
					}
					else
					{
						array_push($result, '..');
						continue;
					}
				}
				array_push($result, $folder);
				$count++;
			}
			return $result;
		}

		//------------------------------------------------------------
		// Public (Class)
		//------------------------------------------------------------

		/**
		 * Creates the relative path thats needed to go from $absolutePath to $targetPath.
		 *
		 * Note 1: both $absolutePath and $targetPath are expected to be string, no check is performed.
		 * Note 2: On Windows, if $absolutePath and $targetPath are on diferent volumes the resulting path is invalid.
		 *
		 * @param $absolutepath: the path to be used as origin.
		 * @param $targetpath: the path to which the relative path will point to.
		 * @param $directory_separator: the directory separator to be used in the result, if null DIRECTORY_SEPARATOR will be used.
		 *
		 * Returns a path that can be used to go from $absolutePath to $targetPath, the path does not include the ending separator.
		 *
		 * @access public
		 * @return string
		 */
		public static function CreateRelativePath(/*string*/ $absolutePath, /*string*/ $targetPath, /*string*/ $directory_separator = null)
		{
			$absolute = self::ProcessAbsolutePath($absolutePath);
			$target = self::ProcessAbsolutePath($targetPath);
			return self::_CreateRelativePath($absolute, $target, $directory_separator);
		}

		/**
		 * Retrieves the path of the folder in which this file is stored.
		 *
		 * Returns the path of the folder in which this file is stored, the path uses DIRECTORY_SEPARATOR as separator and does include the ending DIRECTORY_SEPARATOR.
		 *
		 * @access public
		 * @return string
		 */
		public static function FolderCore()
		{
			return self::PreparePath(dirname(__FILE__));
		}

		/**
		 * Converts any "/" or "\" to DIRECTORY_SEPARATOR and appends DIRECTORY_SEPARATOR at the end if not present.
		 *
		 * Note: $path is expected to be string, no check is performed.
		 *
		 * @param $path: the path to be processed.
		 *
		 * Returns the string $path with both "/" and "\" converted to DIRECTORY_SEPARATOR and DIRECTORY_SEPARATOR and the end.
		 *
		 * @access public
		 * @return string
		 */
		public static function PreparePath(/*string*/ $path)
		{
			return StringUtility::EnsureEnd(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
		}

		/**
		 * Extracts the components of the absolute path $absolutePath.
		 *
		 * Note: $absolutePath is expected to be string, no check is performed.
		 *
		 * @param $absolutepath: the path to be processed.
		 *
		 * Returns an array that containts each component of the absolute path $absolutePath.
		 *
		 * @access public
		 * @return array of string
		 */
		public static function ProcessAbsolutePath(/*string*/ $absolutePath)
		{
			$absolutePath = StringUtility::NeglectEnd(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $absolutePath), DIRECTORY_SEPARATOR);
			$folders = explode(DIRECTORY_SEPARATOR, $absolutePath);
			return self::_ProcessPath($folders);
		}

		/**
		 * Extracts the components of the relative path $relativePath.
		 *
		 * Note: $relativePath is expected to be string, no check is performed.
		 *
		 * @param $relativePath: the path to be processed.
		 *
		 * Returns an array that containts each component of the relative path $relativePath.
		 *
		 * @access public
		 * @return array of string
		 */
		public static function ProcessRelativePath(/*string*/ $relativePath)
		{
			$relativePath = StringUtility::NeglectStart(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
			if ($relativePath === '')
			{
				return array('.');
			}
			else
			{
				$folders = explode(DIRECTORY_SEPARATOR, $relativePath);
				return self::_ProcessPath($folders);
			}
		}

		/**
		 * Creates the path resulting from following $relativePath starting in $absolutePath.
		 *
		 * Note: both $absolutePath and $relativePath are expected to be string, no check is performed.
		 *
		 * @param $absolutePath: the path to be used as origin.
		 * @param $relativePath: the path to be applied to the origin.
		 * @param $directory_separator: the directory separator to be used in the result, if null DIRECTORY_SEPARATOR will be used.
		 *
		 * @access public
		 * @return string
		 */
		public static function ResolveRelativePath(/*string*/ $absolutePath, /*string*/ $relativePath, /*string*/ $directory_separator = null)
		{
			$absolute = self::ProcessAbsolutePath($absolutePath);
			$relative = self::ProcessRelativePath($relativePath);
			if ($directory_separator === null)
			{
				$directory_separator = DIRECTORY_SEPARATOR;
			}
			$result = implode($directory_separator, self::_ProcessPath(array_merge($absolute, $relative)));
			return $result;
		}

		/**
		 * Creates the relative path thats needed to go from $newAbsolutePath to the location of following $relativePath starting in $oldAbsolutePath
		 *
		 * The result of this function is a relative path that if resolved using $newAbsolutePath is equivalent to resolving $relativePath using $oldAbsolutePath
		 *
		 * Note: $oldAbsolutePath, $newAbsolutePath and $relativePath are expected to be string, no check is performed.
		 *
		 * @param $newAbsolutePath: the path to be used as new origin.
		 * @param $oldAbsolutePath: the path that was used as origin for $relativePath.
		 * @param $relativePath: the old relative path that will be rebased.
		 * @param $directory_separator: the directory separator to be used in the result, if null DIRECTORY_SEPARATOR will be used.
		 *
		 * @access public
		 * @return string
		 */
		public static function RebaseRelativePath(/*string*/ $newAbsolutePath, /*string*/ $oldAbsolutePath, /*string*/ $relativePath, /*string*/ $directory_separator = null)
		{
			$newAbsolute = self::ProcessAbsolutePath($newAbsolutePath);
			$oldAbsolute = self::ProcessAbsolutePath($oldAbsolutePath);
			$relative = self::ProcessRelativePath($relativePath);
			$target = self::_ProcessPath(array_merge($oldAbsolute, $relative));
			$result = self::_CreateRelativePath($newAbsolute, $target, $directory_separator);
			return $result;
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