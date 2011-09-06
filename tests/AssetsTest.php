<?php defined('SYSPATH') or die('No direct script access.');

class AssetsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @return array
	 */
	public function provider_data()
	{
		return array(
			array('script1', 'script1.js'),
			array('head.js.script2', 'script2.js'),
			array('body.js.script3', 'script3.js'),
			array('style1', 'style1.css'),
			array('head.css.style2', 'style2.css'),
			array('image1', 'image1.img'),
			array('image.image2', 'image2.img')
		);
	}

	/**
	 * @return array
	 */
	public function provider_data_array()
	{
		return array(
			array('head.js.script2', 'script2.js'),
			array('body.js.script3', 'script3.js'),
			array('head.css.style2', 'style2.css'),
			array('image.image2', 'image2.img')
		);
	}

	/**
	 * Test "get()" access
	 *
	 * @test
	 * @dataProvider provider_data
	 * @param string $name     Asset name
	 * @param array  $url      Asset url
	 */
	public function test_getaccess($name, $url)
	{
		$assets = new Assets();

		$assets->set($name, $url);

		$parts = explode('.', $name);
		$name = array_pop($parts);

		$this->assertSame(Url::site($url), $assets->get($name));
	}

	/**
	 * Test "get()" access (array)
	 *
	 * @test
	 * @dataProvider provider_data_array
	 * @param string $name     Asset name
	 * @param array  $url      Asset url
	 */
	public function test_getaccess_array($name, $url)
	{
		$assets = new Assets();

		$assets->set($name, $url);

		$parts = explode('.', $name);
		array_pop($parts);
		$name = implode('.', $parts);

		$this->assertSame(array(Url::site($url)), $assets->get($name));
	}

	/**
	 * Test "id()" access
	 *
	 * @test
	 * @dataProvider provider_data
	 * @param string $name     Asset name
	 * @param array  $url      Asset url
	 */
	public function test_idaccess($name, $url)
	{
		$assets = new Assets();

		$assets->set($name, $url);

		$parts = explode('.', $name);
		$name = array_pop($parts);

		$id = $assets->id();

		$this->assertSame(Url::site($url), Arr::get($id, $name));
	}

	/**
	 * Test data removal
	 *
	 * @test
	 * @dataProvider provider_data
	 * @param string $name     Asset name
	 * @param array  $url      Asset url
	 */
	public function test_remove($name, $url)
	{
		$assets = new Assets();

		$assets->set($name, $url);

		$parts = explode('.', $name);
		$name = array_pop($parts);

		$assets->remove($name);

		$id = $assets->id();

		$this->assertSame(NULL, $assets->get($name));
		$this->assertSame(NULL, Arr::get($id, $name));
	}
}
