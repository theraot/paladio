<?php namespace paladio;
	// This is Paladio for PHP 5.3.0 or superior

	if (count(get_included_files()) === 1)
	{
		header('HTTP/1.0 404 Not Found', true, 404);
		exit();
	}

	//Disabling magic quotes at runtime taken from http://php.net/manual/en/security.magicquotes.disabling.php
	if (get_magic_quotes_gpc())
	{
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process))
		{
			foreach ($val as $k => $v) 
			{
				unset($process[$key][$k]);
				if (is_array($v))
				{
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				}
				else
				{
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
	
	final class Core
	{
		private static $core;
		private static $main;
		private static $root;
		private static $guid;
		
		private static function Prepare(/*string*/ $path)
		{
			if (substr($path, strlen($path) - strlen(DIRECTORY_SEPARATOR)) !== DIRECTORY_SEPARATOR)
			{
				$path .= DIRECTORY_SEPARATOR;
			}
			return $path;
		}
		
		public static function FolderCore()
		{
			return self::$core;
		}
		
		public static function FolderMain()
		{
			return self::$main;
		}
		
		public static function FolderRoot()
		{
			return self::$root;
		}
		
		public static function GUID()
		{
			return self::$guid;
		}
		
		/**
		* Retrieves the file where the most recent include happened.
		*
		* Returns the absolute path of the file where "include", "include_once", "require" or "require_once" was called if any, false otherwise.
		*
		* @access public
		* @return string or false
		*/
		public static function GetIncludingFile()
		{
			$file = false;
			$backtrace = debug_backtrace();
			$include_functions = array('include', 'include_once', 'require', 'require_once');
			for ($index = 0; $index < count($backtrace); $index++)
			{
				$function = $backtrace[$index]['function'];
				if (in_array($function, $include_functions))
				{
					$file = $backtrace[$index]['file'];
					break;
				}
			}
			return $file;
		}
		
		/**
		* Retrieves the file that was included with the most recent include happened.
		*
		* Returns the absolute path of the file that was included where "include", "include_once", "require" or "require_once" was called if any, false otherwise.
		*
		* @access public
		* @return string or false
		*/
		public static function GetIncludedFile()
		{
			$file = false;
			$backtrace = debug_backtrace();
			$include_functions = array('include', 'include_once', 'require', 'require_once');
			for ($index = 0; $index < count($backtrace); $index++)
			{
				$function = $backtrace[$index]['function'];
				if (in_array($function, $include_functions))
				{
					$file = $backtrace[$index - 1]['file'];
					break;
				}
			}
			return $file;
		}
		
		public static function __initialize()
		{
			// We trust __DIR__ and __FILE__ to be using DIRECTORY_SEPARATOR
			self::$core = self::Prepare(__DIR__);
			$currentFolder = getcwd(); chdir(self::$core);
			{
				self::$main = self::Prepare(realpath('..'));
			}chdir($currentFolder);
			self::$root = self::Prepare(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT']));
			$salt = filemtime(self::$root);
			$uname = php_uname('n');
			$md5 = md5($uname.self::$root.$salt);
			self::$guid = '{'.substr($md5, 0, 8).'-'.substr($md5, 8, 4).'-'.substr($md5, 12, 4).'-'.substr($md5, 16, 4).'-'.substr($md5, 20).'}';
		}
		
		public function __construct()
		{
			throw new \Exception('Creating instances of '.__CLASS__.' is forbidden');
		}
	}
	
	Core::__initialize();
	
	spl_autoload_register
	(
		function($classname)
		{
			require_once(Core::FolderMain().$classname.'.php');
		}
	);
	
	foreach (glob(Core::FolderCore().'*.boot.php', GLOB_MARK | GLOB_NOSORT) as $item)
	{
		$isDir = substr($item,-strlen(DIRECTORY_SEPARATOR)) === DIRECTORY_SEPARATOR;
		if ($isDir === false)
		{
			require_once($item);
		}
	}