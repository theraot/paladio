<?php namespace paladio;

	if (count(get_included_files()) === 1)
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}

	/**
	 * Iteration
	 * @package paladio
	 */
	final class Iteration
	{
	}