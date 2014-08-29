<?php namespace paladio;

	if (count(get_included_files()) == 1)
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