<?php
class Cache_test extends TestCase {

	private $ci_obj;

	public function setUp() {
        $this->resetInstance();
        $this->ci_obj = $this->CI;
    }

	public function test_file() {
		$key = 'phpunit_test_cache_file';
		$value = time();
		$this->ci_obj->cache->set_file($key,$value,60);
		$this->assertEquals($value,$this->ci_obj->cache->get_file($key));
	}

}
