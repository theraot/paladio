<?php namespace paladio;
	require('../paladio/core.php');
	// core should disalbe magic quotes and set autoload.
	// consider this file a test for FileSystem
	
	final class Assert
	{
		private static function Read($caller)
		{
			$file = $caller['file'];
			$lineNumber = $caller['line'];
			$handle = fopen($caller['file'], 'r');
			if ($handle)
			{
				$result = '';
				while (($line = fgets($handle)) !== false)
				{
					$lineNumber--;
					if ($lineNumber == 0)
					{
						$result = $line;
						break;
					}
				}
				fclose($handle);
				return $result;
			}
			else
			{
				return 'UNABLE TO READ SOURCE FILE';
			} 
		}
		
		public static function AreEqual($expected, $found)
		{
			if ($expected !== $found)
			{
				$bt = debug_backtrace();
				throw new \Exception('Expected: '.$expected.' but found: '.$found.' at '.self::Read(array_shift($bt)));
			}
		}
		
		public static function AreNotEqual($unexpected, $found)
		{
			if ($unexpected === $found)
			{
				$bt = debug_backtrace();
				throw new \Exception('Unexpected: '.$unexpected.' and found: '.$found.' at '.self::Read(array_shift($bt)));
			}
		}
		
		public static function IsFalse($value)
		{
			if ($value)
			{
				$bt = debug_backtrace();
				throw new \Exception('IsFalse assertion failed'.' at '.self::Read(array_shift($bt)));
			}
		} 
		
		public static function IsTrue($value)
		{
			if (!$value)
			{
				$bt = debug_backtrace();
				throw new \Exception('IsTrue assertion failed'.' at '.self::Read(array_shift($bt)));
			}
		}
		
		public static function SequenceEquals($expected, $found)
		{
			if (!is_array($expected))
			{
				$expected = iterator_to_array($expected);
			}
			if (!is_array($found))
			{
				$found = iterator_to_array($found);
			}
			$fail = count($expected) !== count($found);
			if (!$fail)
			{
				$count = count($expected);
				for (;$count > 0; $count--)
				{
					$left = array_shift($expected);
					$right = array_shift($found);
					if ($left !== $right)
					{
						$fail = true;
						break;
					}
				}
			}
			if ($fail)
			{
				$bt = debug_backtrace();
				throw new \Exception('Expected '.@var_export($expected, true).' and found: '.@var_export($found, true).' at '.self::Read(array_shift($bt)));
			}
		}
		
		public function __construct()
		{
			throw new \Exception('Creating instances of '.__CLASS__.' is forbidden');
		}
	}
	
	function StringUtility_EndsWith()
	{
		Assert::IsFalse(StringUtility::EndsWith('supispworn', '_supispworn'));
		Assert::IsTrue(StringUtility::EndsWith('supispworn', 'supispworn'));
		Assert::IsTrue(StringUtility::EndsWith('supispworn', 'worn'));
		Assert::IsTrue(StringUtility::EndsWith('greepsyly', 'ly'));
		Assert::IsTrue(StringUtility::EndsWith('greepsyly', ''));
		Assert::IsFalse(StringUtility::EndsWith('greepsyly', 'worn'));
		Assert::IsFalse(StringUtility::EndsWith('greepsyly', null));
		Assert::IsFalse(StringUtility::EndsWith(null, 'aa'));
		Assert::IsFalse(StringUtility::EndsWith(null, null));
	}
	
	function StringUtility_EnsureEnd()
	{
		Assert::AreEqual('greepsylyworn', StringUtility::EnsureEnd('greepsyly', 'worn'));
		Assert::AreEqual('greepsyly', StringUtility::EnsureEnd('greepsyly', ''));
		Assert::AreEqual('greepsyly_', StringUtility::EnsureEnd('greepsyly', '_'));
		Assert::AreEqual('greepsyly', StringUtility::EnsureEnd('greepsyly', null));
	}
	
	function StringUtility_EnsureStart()
	{
		Assert::AreEqual('worngreepsyly', StringUtility::EnsureStart('greepsyly', 'worn'));
		Assert::AreEqual('greepsyly', StringUtility::EnsureStart('greepsyly', ''));
		Assert::AreEqual('_greepsyly', StringUtility::EnsureStart('greepsyly', '_'));
		Assert::AreEqual('greepsyly', StringUtility::EnsureStart('greepsyly', null));
	}
	
	function StringUtility_NeglectEnd()
	{
		Assert::AreEqual('greeps', StringUtility::NeglectEnd('greepsyly', 'yly'));
		Assert::AreEqual('greepsyly', StringUtility::NeglectEnd('greepsyly', 'worn'));
		Assert::AreEqual('greepsyly', StringUtility::NeglectEnd('greepsyly', ''));
		Assert::AreEqual('greepsyly', StringUtility::NeglectEnd('greepsyly', null));
	}
	
	function StringUtility_NeglectStart()
	{
		Assert::AreEqual('psyly', StringUtility::NeglectStart('greepsyly', 'gree'));
		Assert::AreEqual('greepsyly', StringUtility::NeglectStart('greepsyly', 'worn'));
		Assert::AreEqual('greepsyly', StringUtility::NeglectStart('greepsyly', ''));
		Assert::AreEqual('greepsyly', StringUtility::NeglectStart('greepsyly', null));
	}
	
	function StringUtility_StartsWith()
	{
		Assert::IsTrue(StringUtility::StartsWith('greepsyly', 'gree'));
		Assert::IsFalse(StringUtility::StartsWith('greepsyly', 'worn'));
		Assert::IsTrue(StringUtility::StartsWith('greepsyly', ''));
		Assert::IsFalse(StringUtility::StartsWith('greepsyly', null));
	}
	
	function StringUtility_TryNeglectEnd()
	{
		Assert::IsTrue(StringUtility::TryNeglectEnd('greepsyly', 'yly', $result));
		Assert::AreEqual('greeps', $result);
		$result = 0;
		Assert::IsFalse(StringUtility::TryNeglectEnd('greepsyly', 'worn', $result));
		Assert::AreEqual('greepsyly', $result);
		$result = 0;
		Assert::IsTrue(StringUtility::TryNeglectEnd('greepsyly', '', $result));
		Assert::AreEqual('greepsyly', $result);
		$result = 0;
		Assert::IsFalse(StringUtility::TryNeglectEnd('greepsyly', null, $result));
		Assert::AreEqual('greepsyly', $result);
	}
	
	function StringUtility_TryNeglectStart()
	{
		Assert::IsTrue(StringUtility::TryNeglectStart('greepsyly', 'gree', $result));
		Assert::AreEqual('psyly', $result);
		$result = 0;
		Assert::IsFalse(StringUtility::TryNeglectStart('greepsyly', 'worn', $result));
		Assert::AreEqual('greepsyly', $result);
		$result = 0;
		Assert::IsTrue(StringUtility::TryNeglectStart('greepsyly', '', $result));
		Assert::AreEqual('greepsyly', $result);
		$result = 0;
		Assert::IsFalse(StringUtility::TryNeglectStart('greepsyly', null, $result));
		Assert::AreEqual('greepsyly', $result);
	}
	
	function Test_dirname()
	{
		// dirname ignores final separator
		Assert::AreEqual('/root/folder', dirname('/root/folder/test'));
		Assert::AreEqual('/root/folder', dirname('/root/folder/test/'));
		// dirname preserves the separator
		Assert::AreEqual('/root\\folder', dirname('/root\\folder/test'));
		Assert::AreEqual('/root\\folder', dirname('/root\\folder/test/'));
	}
	
	function FileSystem_CreateRelativePath()
	{
		// even if it doesn't have a final separator, we consider it a folder
		// output doesn't include final separator
		// this means that adding the final separator is up to the caller
		// output uses DIRECTORY_SEPARATOR
		$expected = str_replace('/', DIRECTORY_SEPARATOR, '../../another/test');
		Assert::AreEqual($expected, FileSystem::CreateRelativePath('\\root\\folder\\test', '\\root\\another\\test'));
		Assert::AreEqual($expected, FileSystem::CreateRelativePath('\\root\\folder\\test\\', '\\root\\another\\test'));
		Assert::AreEqual($expected, FileSystem::CreateRelativePath('\\root\\folder\\test\\', '\\root\\another\\test\\'));
	}
	
	function FileSystem_PreparePath()
	{
		$expected = str_replace('/', DIRECTORY_SEPARATOR, '/root/folder/test/');
		Assert::AreEqual($expected, FileSystem::PreparePath('\\root\\folder\\test'));
		$expected = str_replace('/', DIRECTORY_SEPARATOR, 'C:\\');
		Assert::AreEqual($expected, FileSystem::PreparePath('C:\\'));
		Assert::AreEqual(DIRECTORY_SEPARATOR, FileSystem::PreparePath('\\'));
	}
	
	function FileSystem_ProcessPath()
	{
		Assert::SequenceEquals(array('root', 'folder', 'test'), FileSystem::ProcessPath('\\root\\folder\\test'));
		Assert::SequenceEquals(array('root', 'folder', 'test'), FileSystem::ProcessPath('\\root\\folder\\test\\'));
		
		Assert::SequenceEquals(array('root', 'folder'), FileSystem::ProcessPath('\\root\\folder\\test\\..'));
		Assert::SequenceEquals(array('root', 'folder'), FileSystem::ProcessPath('\\root\\folder\\test\\..\\'));
		
		Assert::SequenceEquals(array('root', 'test'), FileSystem::ProcessPath('\\root\\folder\\..\\test\\'));
		Assert::SequenceEquals(array('folder', 'test'), FileSystem::ProcessPath('\\root\\..\\folder\\test\\'));
		Assert::SequenceEquals(array('..', 'root', 'folder', 'test'), FileSystem::ProcessPath('\\..\\root\\folder\\test\\'));
		
		Assert::SequenceEquals(array('..', '..', 'root', 'folder', 'test'), FileSystem::ProcessPath('..\\..\\root\\folder\\test\\'));
		Assert::SequenceEquals(array('..', '..', 'root', 'folder', 'test'), FileSystem::ProcessPath('\\..\\..\\root\\folder\\test\\'));
		
		Assert::SequenceEquals(array('root'), FileSystem::ProcessPath('\\root\\folder\\test\\..\\..\\'));
		Assert::SequenceEquals(array('root'), FileSystem::ProcessPath('\\root\\folder\\test\\..\\..'));
		
		Assert::SequenceEquals(array('.'), FileSystem::ProcessPath('\\root\\folder\\test\\..\\..\\..'));
		Assert::SequenceEquals(array('.'), FileSystem::ProcessPath('\\root\\folder\\test\\..\\..\\..\\'));
		
		Assert::SequenceEquals(array('root', 'test'), FileSystem::ProcessPath('.\\root\\folder\\..\\test'));
		Assert::SequenceEquals(array('root', 'test'), FileSystem::ProcessPath('.\\root\\folder\\..\\test\\'));
		
		Assert::SequenceEquals(array('.'), FileSystem::ProcessPath('.\\root\\folder\\..\\..\\'));
		Assert::SequenceEquals(array('..'), FileSystem::ProcessPath('.\\root\\folder\\..\\..\\..'));
		Assert::SequenceEquals(array('..'), FileSystem::ProcessPath('.\\root\\folder\\..\\..\\..\\'));
	}
	
	function FileSystem_CombinePath()
	{
		$expected = str_replace('/', DIRECTORY_SEPARATOR, '/root/folder/test');
		Assert::AreEqual($expected, FileSystem::CombinePath('/root/folder/test/', '.'));
		Assert::AreEqual($expected, FileSystem::CombinePath('/root/folder/test/algo', '..'));
		Assert::AreEqual($expected, FileSystem::CombinePath('/root/folder/test/algo/otro', '../..'));
		Assert::AreEqual($expected, FileSystem::CombinePath('/root/folder/test/algo/otro', '../../../test'));
		Assert::AreEqual($expected, FileSystem::CombinePath('/root/folder/', 'test'));
		Assert::AreEqual($expected, FileSystem::CombinePath('/root/', 'folder/test'));
		Assert::AreEqual($expected, FileSystem::CombinePath('/', 'root/folder/test'));
		$expected = str_replace('/', DIRECTORY_SEPARATOR, 'C:\\path');
		Assert::AreEqual($expected, FileSystem::CombinePath('C:\\path\\in\\windows', '../..'));
		Assert::AreEqual($expected, FileSystem::CombinePath('C:\\', 'path'));
	}
	
	function FileSystem_RebaseRelativePath()
	{
		$expected = str_replace('/', DIRECTORY_SEPARATOR, '../../folder/test');
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/root/folder/test/', '.'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/root/folder/test/algo', '..'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/root/folder/test/algo/otro', '../..'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/root/folder/test/algo/otro', '../../../test'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/root/folder/', 'test'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/root/', 'folder/test'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('/root/new/path/', '/', 'root/folder/test'));
		$expected = str_replace('/', DIRECTORY_SEPARATOR, '..');
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('C:\path\windows', 'C:\\path\\in\\windows', '../..'));
		Assert::AreEqual($expected, FileSystem::RebaseRelativePath('C:\path\windows', 'C:\\', 'path'));
	}
	
	function Iteration_ArrayTest()
	{
		$input = array('0', '1', '2', '3');
		Assert::AreEqual(4, Iteration::From($input)->Count());
		$r = '';
		Iteration::From($input)->OnEach(function($item) use (&$r){$r.=$item;});
		Assert::AreEqual('0123', $r);
		// --
		$input = array
			(
				array('input' => 'a', 'output' => 'b'),
				array('input' => 'a', 'output' => 'c'),
				array('input' => 'b', 'output' => 'c'),
				array('input' => 'c', 'output' => 'a'),
			);
		// --
		$graph = Iteration::From($input)->ToGraph('input', 'output');
		
		Assert::SequenceEquals(array($graph['c']), $graph['a']->incoming);
		Assert::SequenceEquals(array($graph['b'], $graph['c']), $graph['a']->outgoing);
		
		Assert::SequenceEquals(array($graph['a']), $graph['b']->incoming);
		Assert::SequenceEquals(array($graph['c']), $graph['b']->outgoing);
		
		Assert::SequenceEquals(array($graph['a'], $graph['b']), $graph['c']->incoming);
		Assert::SequenceEquals(array($graph['a']), $graph['c']->outgoing);
		// --
		$dict = Iteration::From($input)->ToDictionary('input', 'output');
		
		Assert::AreEqual('c', $dict['a']);
		Assert::AreEqual('c', $dict['b']);
		Assert::AreEqual('a', $dict['c']);
		// --
		$select = Iteration::From($input)->Select('input');
		Assert::SequenceEquals(array('a', 'a', 'b', 'c'), $select);
	}
	
	function Iteration_IteratorTest()
	{
		$input = new \ArrayIterator(array('0', '1', '2', '3'));
		Assert::AreEqual(4, Iteration::From($input)->Count());
		$r = '';
		Iteration::From($input)->OnEach(function($item) use (&$r){$r.=$item;});
		Assert::AreEqual('0123', $r);
		// --
		$input = new \ArrayIterator
			(
				array
				(
					array('input' => 'a', 'output' => 'b'),
					array('input' => 'a', 'output' => 'c'),
					array('input' => 'b', 'output' => 'c'),
					array('input' => 'c', 'output' => 'a'),
				)
			);
		// --
		$graph = Iteration::From($input)->ToGraph('input', 'output');
		
		Assert::SequenceEquals(array($graph['c']), $graph['a']->incoming);
		Assert::SequenceEquals(array($graph['b'], $graph['c']), $graph['a']->outgoing);
		
		Assert::SequenceEquals(array($graph['a']), $graph['b']->incoming);
		Assert::SequenceEquals(array($graph['c']), $graph['b']->outgoing);
		
		Assert::SequenceEquals(array($graph['a'], $graph['b']), $graph['c']->incoming);
		Assert::SequenceEquals(array($graph['a']), $graph['c']->outgoing);
		// --
		$dict = Iteration::From($input)->ToDictionary('input', 'output');
		
		Assert::AreEqual('c', $dict['a']);
		Assert::AreEqual('c', $dict['b']);
		Assert::AreEqual('a', $dict['c']);
		// --
		$select = Iteration::From($input)->Select('input');
		Assert::SequenceEquals(array('a', 'a', 'b', 'c'), $select);
	}

	echo '<table>';
	$all = get_defined_functions();
	foreach ($all['user'] as $func)
	{
		echo '<tr>';
		echo '<td>'.$func.'</td>';
		echo '<td>';
		try
		{
			call_user_func($func);
			echo 'OK';
		}
		catch (\Exception $exception)
		{
			echo $exception->getMessage();
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
	
	echo '<hr>';
	echo Core::GUID();
	echo '<hr>';
	echo FileSystem::ScriptPath();
	echo '<hr>';
	echo FileSystem::ScriptPathRelative();
	echo '<hr>';
	
	echo 'All';
	echo '<pre>';
	var_dump(FileSystem::GetItems('*', '..'));
	echo '</pre>';
	echo 'Files';
	echo '<pre>';
	var_dump(FileSystem::GetFiles('*', '..'));
	echo '</pre>';
	echo 'Folders';
	echo '<pre>';
	var_dump(FileSystem::GetFolders('..'));
	echo '</pre>';
	
	echo 'All Recursive';
	echo '<pre>';
	var_dump(FileSystem::GetItemsRecursive('*', '..'));
	echo '</pre>';
	echo 'Files Recursive';
	echo '<pre>';
	var_dump(FileSystem::GetFilesRecursive('*', '..'));
	echo '</pre>';
	echo 'Folders Recursive';
	echo '<pre>';
	var_dump(FileSystem::GetFoldersRecursive('..'));
	echo '</pre>';
?>