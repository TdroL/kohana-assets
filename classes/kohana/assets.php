<?php defined('SYSPATH') or die('No direct script access.');

// TODO: implement Assets_Node
class Kohana_Assets
{
	protected $_group;

	protected $_item;

	public static function factory()
	{
		return new Assets;
	}

	public function __construct()
	{
		$this->reset();
	}

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

		$this->_item[$name] = $this->_parse_url($url);

		if ( ! empty($parts))
		{
			$current = & $this->_group;

			foreach ($parts as $part)
			{
				if ( ! Arr::is_array($current))
				{
					$current = array();
				}

				if ( ! array_key_exists($part, $current) OR ! Arr::is_array($current[$part]))
				{
					$current[$part] = array();
				}

				$current = & $current[$part];
			}

			$current[] = $name;
		}

		return $this;
	}

	public function reset()
	{
		$this->_group = array(
			'body' => array(
				'js' => array(),
			),
			'head' => array(
				'js' => array(),
				'css' => array(),
			),
			'image' => NULL,
		);

		$this->_item = array();

		return $this;
	}

	public function remove($name)
	{
		if ( ! isset($this->_item[$name]))
		{
			return $this;
		}

		unset($this->_item[$name]);

		$this->_group = $this->_remove_recursive($this->_group, $name);

		return $this;
	}

	protected function _remove_recursive($value, $name)
	{
		if (is_array($value))
		{
			if (Arr::is_assoc($value))
			{
				foreach ($value as $k => $v)
				{
					$value[$k] = $this->_remove_recursive($v, $name);

					if ($value[$k] === FALSE)
					{
						unset($value[$k]);
					}
				}
			}
			else
			{
				$tmp = array();

				foreach ($value as $v)
				{
					$val = $this->_remove_recursive($v, $name);

					if ($val !== FALSE)
					{
						$tmp[] = $val;
					}
				}

				$value = $tmp;
			}

			return $value;
		}

		return ( ! empty($value) AND $value != $name) ? $value : FALSE;
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

	protected function _parse_url($url)
	{
		/* "//", "http://", "https://", "ftp://", "ftps://", "$base_url/" */
		if (preg_match('/^(((ht|f)tps?:)?\/\/)|(^'.preg_quote(Url::base(), '/').')/iD', $url))
		{
			return $url;
		}

		return Url::site($url);
	}

	protected function _node($node)
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
