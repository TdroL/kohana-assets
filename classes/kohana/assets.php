<?php defined('SYSPATH') or die('No direct script access.');

// TODO: implement Assets_Node
class Kohana_Assets
{
	protected $_group = array(
		'body' => array(
			'js' => array(),
		),
		'head' => array(
			'js' => array(),
			'css' => array(),
		),
		'image' => NULL,
	);

	protected $_item = array();

	public function __get($path)
	{
		return $this->get($path);
	}

	public function get($path)
	{
		if (stripos($path, '.') === FALSE)
		{
			if ($group = Arr::get($this->_group, $path))
			{
				return $this->_node($group);
			}

			return Arr::get($this->_item, $path);
		}

		return $this->_node(Arr::path($this->_group, $path));
	}

	public function set($path, $url)
	{
		if (empty($path))
		{
			return;
		}

		$parts = explode('.', $path);

		$name = array_pop($parts);

		$this->_item[$name] = $url;

		if ( ! empty($parts))
		{
			$current = &$this->_group;

			foreach($parts as $part)
			{
				if ( ! Arr::is_array($current))
				{
					$current = array();
				}

				if ( ! array_key_exists($part, $current) OR ! Arr::is_array($current[$part]))
				{
					$current[$part] = array();
				}

				$current = &$current[$part];
			}

			$current[] = $name;
		}
	}

	public function remove($name)
	{
		if ( ! isset($this->_item[$name]))
		{
			return;
		}

		unset($this->_item[$name]);

		$this->_remove_name = $name;
		$this->_group = array_map(array($this, '_remove_callback'), $this->_group);
	}

	public function _remove_callback($value)
	{
		if (is_array($value))
		{
			return array_map(array($this, '_remove_callback'), $value);
		}

		return !empty($value) AND $value != $this->_remove_name;
	}

	public function head()
	{
		return $this->get('head');
	}

	public function body()
	{
		return $this->get('body');
	}

	public function image()
	{
		return $this->get('image');
	}

	public function id()
	{
		return $this->_item;
	}

	public function _node($node)
	{
		if (Arr::is_array($node) AND ! Arr::is_assoc($node))
		{
			$return = array();

			foreach ($node as $v)
			{
				$val = $this->_node($v);
				if ( ! is_null($val))
				{
					$return[] = $val;
				}
			}

			return $return;
		}

		if (Arr::is_array($node) AND Arr::is_assoc($node))
		{
			$return = array();

			foreach ($node as $k => $v)
			{
				$val = $this->_node($v);
				if ( ! is_null($val))
				{
					$return[$k] = $val;
				}
			}

			return $return;
		}

		if (is_string($node))
		{
			return Arr::get($this->_item, $node);
		}

		return NULL;
	}

}
