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
		//------------------------------------------------------------
		// Public (Class)
		//------------------------------------------------------------

		public static function From(/*mixed*/ $iterable)
		{
			return new self($iterable);
		}

		//------------------------------------------------------------
		// Private (Instance)
		//------------------------------------------------------------

		private $iterable;
		private $countable;
		private $array;

		//------------------------------------------------------------
		// Public (Instance)
		//------------------------------------------------------------

		public function Count()
		{
			if ($this->countable)
			{
				return count($this->iterable);
			}
			else
			{
				return iterator_count($this->iterable);
			}
		}

		public function OnEach(/*function*/ $callback, /*mixed*/ ...$args)
		{
			if (is_callable($callback))
			{
				foreach ($this->iterable as $record)
				{
					call_user_func_array($callback, array_merge(array($record), $args));
				}
			}
			else
			{
				throw new \Exception('Invalid $callback');
			}
		}

		public function Select(/*mixed*/ $key)
		{
			$result = array();
			if (is_array($key))
			{
				foreach ($this->iterable as $record)
				{
					$newEntry = array();
					foreach ($key as $keyItem)
					{
						$newEntry[$keyItem] = $record[$keyItem];
					}
					$result[] = $newEntry;
				}
			}
			else
			{
				foreach ($this->iterable as $record)
				{
					$result[] = $record[$key];
				}
			}
			return $result;
		}

		public function ToArray()
		{
			if ($this->array)
			{
				return $this->iterable;
			}
			else
			{
				return iterator_to_array($this->iterable);
			}
		}

		public function ToGraph(/*string*/ $sourceKey, /*string*/ $targetKey)
		{
			$result = array();
			foreach ($this->iterable as $record)
			{
				$source = $record[$sourceKey];
				$target = $record[$targetKey];
				if (!array_key_exists($source, $result))
				{
					$node = new GraphNode();
					$node->id = $source;
					$result[$source] = $node;
				}
				if (!array_key_exists($target, $result))
				{
					$node = new GraphNode();
					$node->id = $target;
					$result[$target] = $node;
				}
				$sourceNode = $result[$source];
				$targetNode = $result[$target];
				$sourceNode->outgoing[] = $targetNode;
				$targetNode->incoming[] = $sourceNode;
			}
			return $result;
		}


		public function ToDictionary(/*string*/ $keyKey, /*mixed*/ $keyValue)
		{
			$result = array();
			if (is_array($keyValue))
			{
				foreach ($this->iterable as $record)
				{
					$newEntry = array();
					foreach ($keyValue as $keyItem)
					{
						$newEntry[$keyItem] = $record[$keyItem];
					}
					$result[$record[$keyKey]] = $newEntry;
				}
			}
			else
			{
				foreach ($this->iterable as $record)
				{
					$result[$record[$keyKey]] = $record[$keyValue];
				}
			}
			return $result;
		}

		//------------------------------------------------------------
		// Public (Constructor)
		//------------------------------------------------------------

		/**
		 * Creates a new instance of Iteration
		 */
		public function __construct(/*mixed*/ $iterable)
		{
			if ($iterable instanceof \Traversable || $iterable instanceof Iterator || $iterable instanceof SeekableIterator)
			{
				$this->iterable = $iterable;
				$this->countable = $iterable instanceof Countable;
				$this->array = false;
			}
			else if (is_array($iterable))
			{
				$this->iterable = $iterable;
				$this->countable = true;
				$this->array = true;
			}
			else
			{
				var_dump($iterable);
				throw new \Exception('Invalid $iterable');
			}
		}
	}

	/**
	 * GraphNode
	 * @package Paladio
	 */
	final class GraphNode
	{
		//------------------------------------------------------------
		// Public (Instance)
		//------------------------------------------------------------

		/**
		 * The id of the node.
		 */
		public $id;

		/**
		 * The incomming relationships, this node is the target.
		 */
		public $incoming;

		/**
		 * The outgoing relationships, this node is the source.
		 */
		public $outgoing;
	}