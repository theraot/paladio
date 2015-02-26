<?php namespace paladio;

	if (count(get_included_files()) === 1)
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}

	// This file assumes StringUtility is available

	/**
	 * FileSystem
	 * @package paladio
	 */
	final class FileSystem
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

		private static function _GetItems(/*mixed*/ $pattern, /*string*/ $path, /*bool*/ $folders)
		{
			if (is_array($pattern))
			{
				$result = array();
				foreach($pattern as $pattern)
				{
					$result = array_merge($result, self::_GetItems($pattern, $path, $folders));
				}
				return $result;
			}
			else
			{
				if (!is_string($pattern))
				{
					$pattern = '*';
				}
				//---
				if (is_string($path))
				{
					$pattern = self::CombinePath($path, $pattern, DIRECTORY_SEPARATOR);
				}
				//---
				$result = array();
				foreach (glob($pattern, GLOB_MARK | GLOB_NOSORT) as $item)
				{
					$isDir = substr($item,-strlen(DIRECTORY_SEPARATOR)) === DIRECTORY_SEPARATOR;
					if ($folders === null || ($folders === true && $isDir === true) || ($folders === false && $isDir === false))
					{
						$result[] = $item;
					}
				}
				return $result;
			}
		}

		private static function _GetItemsRecursive(/*mixed*/ $pattern, /*string*/ $path, /*bool*/ $folders)
		{
			if ($folders === true)
			{
				$result = array();
			}
			else
			{
				$result = self::_GetItems($pattern, $path, false);
			}
			$queue = array($path);
			$branches = null;
			$branches_index = -1;
			$branches_length = -1;
			while (true)
			{
				if ($branches === null)
				{
					if (count($queue) > 0)
					{
						$found = array_shift($queue);
						$branches = self::_GetItems('*', $found, true);
						$branches_index = -1;
						$branches_length = count($branches);
					}
					else
					{
						break;
					}
				}
				else
				{
					$advanced = false;
					$branches_index++;
					if ($branches_index < $branches_length)
					{
						$advanced = true;
					}
					if ($advanced)
					{
						$found = $branches[$branches_index];
						if ($folders !== false)
						{
							$result[] = $found;
						}
						if ($folders !== true)
						{
							$new = self::_GetItems($pattern, $found, false);
							$result = array_merge($result, $new);
						}
						$queue[] = $found;
					}
					else
					{
						$branches = null;
						$branches_index = -1;
						$branches_length = -1;
					}
				}
			}
			return $result;
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
		* Retrieves the files that match the pattern in $pattern and are in the folder $path.
		*
		* If $pattern is string: it will be interpreted as a relative path followed by a Windows file search pattern.
		* The Windows file search pattern uses:
		* "?" : any character
		* "*" : any character, zero or more times
		* Otherwise, it will be interpretated as the Windows file search pattern "*".
		*
		* Note: files which name starts with "." are ignored.
		*
		* Returns an array that contains the absolute path of the files.
		*
		* @access public
		* @return array of string
		*/
		public static function GetFiles(/*mixed*/ $pattern, /*string*/ $path)
		{
			return FileSystem::_GetItems($pattern, $path, false);
		}
		
		/**
		* Recursively retrieves the files that match the pattern in $pattern and are in the folder $path.
		*
		* If $pattern is string: it will be interpreted as a relative path followed by a Windows file search pattern.
		* The Windows file search pattern uses:
		* "?" : any character
		* "*" : any character, zero or more times
		* Otherwise, it will be interpretated as the Windows file search pattern "*".
		*
		* Note: files which name starts with "." are ignored.
		*
		* Returns an array that contains the absolute path of the files.
		*
		* @access public
		* @return array of string
		*/
		public static function GetFilesRecursive(/*mixed*/ $pattern, /*string*/ $path)
		{
			return FileSystem::_GetItemsRecursive($pattern, $path, false);
		}
		
		/**
		* Retrieves the files and folders that match the pattern in $pattern and are in the folder $path.
		*
		* If $pattern is string: it will be interpreted as a relative path followed by a Windows file search pattern.
		* The Windows file search pattern uses:
		* "?" : any character
		* "*" : any character, zero or more times
		* Otherwise, it will be interpretated as the Windows file search pattern "*".
		*
		* Note: files which name starts with "." are ignored.
		*
		* Returns an array that contains the absolute path of the files and folders.
		*
		* @access public
		* @return array of string
		*/
		public static function GetItems(/*mixed*/ $pattern, /*string*/ $path)
		{
			return FileSystem::_GetItems($pattern, $path, null);
		}
		
		/**
		* Recursively retrieves the files and folders that match the pattern in $pattern and are in the folder $path.
		*
		* If $pattern is string: it will be interpreted as a relative path followed by a Windows file search pattern.
		* The Windows file search pattern uses:
		* "?" : any character
		* "*" : any character, zero or more times
		* Otherwise, it will be interpretated as the Windows file search pattern "*".
		*
		* Note: files which name starts with "." are ignored.
		*
		* Returns an array that contains the absolute path of the files and folders.
		*
		* @access public
		* @return array of string
		*/
		public static function GetItemsRecursive(/*mixed*/ $pattern, /*string*/ $path)
		{
			return FileSystem::_GetItemsRecursive($pattern, $path, null);
		}
		
		/**
		* Retrieves the folders that match the pattern in $pattern and are in the folder $path.
		*
		* Returns an array that contains the absolute path of the folders.
		*
		* @access public
		* @return array of string
		*/
		public static function GetFolders(/*string*/ $path)
		{
			return FileSystem::_GetItems('*', $path, true);
		}
		
		/**
		* Recursively retrieves the folders that match the pattern in $pattern and are in the folder $path.
		*
		* Note: files which name starts with "." are ignored.
		*
		* Returns an array that contains the absolute path of the folders.
		*
		* @access public
		* @return array of string
		*/
		public static function GetFoldersRecursive(/*string*/ $path)
		{
			return FileSystem::_GetItemsRecursive('*', $path, true);
		}

		//------------------------------------------------------------

		/**
		 * Creates the path resulting from following $relativePath starting in $path.
		 *
		 * Note: both $path and $relativePath are expected to be string, no check is performed.
		 *
		 * @param $path: the path to be used as origin.
		 * @param $relativePath: the path to be applied to the origin.
		 * @param $directory_separator: the directory separator to be used in the result, if null DIRECTORY_SEPARATOR will be used.
		 *
		 * @access public
		 * @return string
		 */
		public static function CombinePath(/*string*/ $path, /*string*/ $relativePath, /*string*/ $directory_separator = null)
		{
			$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
			$leading = false;
			if (StringUtility::TryNeglectStart($path, DIRECTORY_SEPARATOR, $path))
			{
				$leading = true;
			}
			$path = StringUtility::NeglectEnd($path, DIRECTORY_SEPARATOR);
			$origin = explode(DIRECTORY_SEPARATOR, $path);
			$origin = self::_ProcessPath($origin);
			$count = count($origin);
			if ($count == 0)
			{
				$absolute = array('.');
			}
			$relative = self::ProcessPath($relativePath);
			if ($directory_separator === null)
			{
				$directory_separator = DIRECTORY_SEPARATOR;
			}
			$result = implode($directory_separator, self::_ProcessPath(array_merge($origin, $relative)));
			if ($leading)
			{
				$result = StringUtility::EnsureStart($result, $directory_separator);
			}
			return $result;
		}

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
			$absolute = self::ProcessPath($absolutePath);
			$target = self::ProcessPath($targetPath);
			return self::_CreateRelativePath($absolute, $target, $directory_separator);
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
		 * Extracts the components of the path $path.
		 *
		 * Note: $path is expected to be string, no check is performed.
		 *
		 * @param $path: the path to be processed.
		 *
		 * Returns an array that containts each component of the path $path.
		 *
		 * @access public
		 * @return array of string
		 */
		public static function ProcessPath(/*string*/ $path)
		{
			$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
			$path = StringUtility::NeglectStart($path, DIRECTORY_SEPARATOR);
			$path = StringUtility::NeglectEnd($path, DIRECTORY_SEPARATOR);
			$folders = explode(DIRECTORY_SEPARATOR, $path);
			$folders = self::_ProcessPath($folders);
			$count = count($folders);
			if ($count == 0)
			{
				return array('.');
			}
			else
			{
				return $folders;
			}
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
			$target = self::CombinePath($oldAbsolutePath, $relativePath);
			return self::CreateRelativePath($newAbsolutePath, $target, $directory_separator);
		}
		
		//------------------------------------------------------------
		
		/**
		* Retrieves the absolute path of the requested script.
		*
		* Returns the value of $_SERVER['SCRIPT_FILENAME'] with any "/" or "\" replaced to DIRECTORY_SEPARATOR.
		*
		* @access public
		* @return string
		*/
		public static function ScriptPath()
		{
			return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']);
		}
		
		/**
		* Creates the relative path needed to go from $absolutePath to the absolute path of the requested script as given by FileSystem::ScriptPath().
		*
		* If $absolutePath is null: Creates the relative path needed to go from FileSystem::DocumentRoot() to FileSystem::ScriptPath().
		* Otehrwise: Assumes $absolutePath is string and creates the relative path needed to go from $absolutePath to FileSystem::ScriptPath().
		*
		* Note: $absolutePath is expected to be null or string, no check is performed.
		*
		* @access public
		* @return string
		*/
		public static function ScriptPathRelative(/*string*/ $absolutePath = null, $directory_separator = '/')
		{
			if ($absolutePath === null)
			{
				$absolutePath = Core::FolderRoot();
			}
			return $directory_separator.FileSystem::CreateRelativePath($absolutePath, self::ScriptPath(), $directory_separator);
		}

		//------------------------------------------------------------
		// Public (Constructor)
		//------------------------------------------------------------

		/**
		 * Creating instances of this class is not allowed.
		 */
		public function __construct()
		{
			throw new \Exception('Creating instances of '.__CLASS__.' is forbidden');
		}
	}