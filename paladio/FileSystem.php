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
	final class FileSystem
	{
		public function __construct()
		{
			throw new Exception('Creating instances of '.__CLASS__.' is forbidden');
		}
	}