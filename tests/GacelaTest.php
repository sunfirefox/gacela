<?php

class GacelaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Gacela
     */
    protected $object;

	/**
	 * @var \Memcache
	 */
	protected $memcache = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = Gacela::instance();

		$this->object->registerNamespace('Test', __DIR__);

		$this->memcache = new Memcache;

		$this->memcache->addServer('127.0.0.1', 11211);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
		Gacela::reset();

		if(is_object($this->memcache)) {
			$this->memcache->flush();
		}
    }

	public function providerAutoload()
	{
		return array(
			array('Mapper\User', 'Test\Mapper\User'),
			array('Model\User', 'Test\Model\User'),
			array('Test\Mapper\Customer', 'Test\Mapper\Customer'),
			array('Test\Model\Customer', 'Test\Model\Customer'),
			array('Gacela\DataSource\DataSource', 'Gacela\DataSource\DataSource'),
			array('Field\Bool', 'Gacela\Field\Bool'),
			array('Field\Field', 'Test\Field\Field')
		);
	}

	public function providerSources()
	{
		return array(
			array('mysql', 'mysql', array(), "Gacela\\DataSource\\Database"),
		//	array('mssql', 'mssql', array(), "Gacela\\DataSource\\Database"),
			array('salesforce', 'salesforce', array(), "Gacela\\DataSource\\Salesforce")
		);
	}

	public function providerGetField()
	{
		return array(
			array('Binary'),
			array('Bool'),
			array('Date'),
			array('Decimal'),
			array('Enum'),
			array('Float'),
			array('Int'),
			array('Set'),
			array('String'),
			array('Time')
		);
	}

	public function providerMapper()
	{
		return array(
			array('User', 'Test\Mapper\User'),
			array('Mapper\Order', 'Test\Mapper\Order'),
			array('Test\Mapper\Customer', 'Test\Mapper\Customer')
		);
	}

    /**
     * @covers Gacela::instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Gacela', Gacela::instance());
    }

    /**
     * @covers Gacela::autoload
	 * @dataProvider providerAutoload
     */
    public function testAutoload($class, $qualified)
    {
		$this->assertSame($qualified, $this->object->autoload($class));
    }

	/**
	 * @covers Gacela::enableCache
	 */
	public function testEnableCache()
	{
		$this->object->enableCache($this->memcache);

		$this->assertAttributeInstanceOf('\Memcache','_cache', $this->object);
	}

    /**
     * @covers Gacela::cacheMetaData
     */
    public function testCacheMetaDataWithoutMemcache()
    {
		$array = array(
			array('var1' => 1, 'var2' => 2, 'var3' => 3),
			array('var1' => 999, 'var2' => 'something else', 'var3' => 'more')
		);

        $this->object->cacheMetaData('test', $array);

		$this->assertEquals($array, $this->object->cacheMetaData('test'));
    }

	/**
	 * @covers Gacela::cacheMetaData
	 */
	public function testCacheMetaDataWithMemcache()
	{
		$array = array(
			array('var1' => 1, 'var2' => 2, 'var3' => 3),
			array('var1' => 999, 'var2' => 'something else', 'var3' => 'more')
		);

		$this->assertFalse($this->memcache->get('test'));

		$this->object->enableCache($this->memcache);

		$this->object->cacheMetaData('test', $array);

		$this->assertSame($array, $this->memcache->get('test'));
	}

    /**
     * @covers Gacela::configPath
     * @todo   Implement testConfigPath().
     */
    public function testConfigPath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

	/**
	 * @covers Gacela::registerDataSource
	 * @dataProvider providerSources
	 */
	public function testRegisterDataSource($name, $type, $config, $class)
	{
		$this->object->registerDataSource($name, $type, $config);

		$this->assertInstanceOf($class, $this->object->getDataSource($name));
	}

    /**
     * @covers Gacela::getDataSource
	 * @dataProvider providerSources
	 * @depends testRegisterDataSource
     */
    public function testGetDataSource($name, $type, $config, $class)
    {
		$this->object->registerDataSource($name, $type, $config);

		$this->assertInstanceOf($class, $this->object->getDataSource($name));
    }

	/**
	 * @expectedException \Gacela\Exception
	 */
	public function testGetDataSourceThrowsException()
	{
		$this->object->getDataSource('not_here');
	}

	/**
	 * @param $type
	 * @dataProvider providerGetField
	 */
	public function testGetField($type)
	{
		$this->assertInstanceOf("\\Gacela\\Field\\".$type, $this->object->getField($type));
	}

    /**
     * @covers Gacela::loadConfig
     * @todo   Implement testLoadConfig().
     */
    public function testLoadConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Gacela::loadMapper
     * @dataProvider providerMapper
     */
    public function testLoadMapper($name, $expected)
    {
		$this->assertInstanceOf($expected, $this->object->loadMapper($name));
    }

    /**
     * @covers Gacela::makeCollection
     * @todo   Implement testMakeCollection().
     */
    public function testMakeCollection()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Gacela::registerNamespace
     */
    public function testRegisterNamespace()
    {
		$array = array(
			array('Test1', __DIR__, __DIR__.'/'),
			array('App', '/var/www/app/', '/var/www/app/')

		);

		$expected = array('Gacela' => '/var/www/gacela/library/Gacela/', 'Test' => __DIR__.'/');

		foreach($array as $ns) {
			$this->object->registerNamespace($ns[0], $ns[1]);

			$expected[$ns[0]] = $ns[2];
		}

		$this->assertAttributeSame($expected, '_namespaces', $this->object);
    }
}
