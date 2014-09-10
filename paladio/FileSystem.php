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
	 * FileSystem
	 * @package paladio
	 */
	final class FileSystem
	{
		//------------------------------------------------------------
		// Public (Class)
		//------------------------------------------------------------

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
			return FileSystem::PreparePath(dirname(__FILE__));
		}

		/**
		 * Converts any "/" or "\" to DIRECTORY_SEPARATOR and appends DIRECTORY_SEPARATOR at the end if not present.
		 *
		 * Note: $path is expected to be string, no check is performed.
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