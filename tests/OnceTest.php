<?php
use \infrajs\once\Once;
require_once __DIR__ . '/../Once.php';
class OnceTest extends PHPUnit_Framework_TestCase
{
	public function testOnce()
	{
		$res = Once::exec('test', function($a, $b) {return $a + $b;}, [4, 5]);
		$this->assertTrue(9 === $res);
		$res = Once::exec('test', function($a, $b) {return $a + $b;}, [10, 5]);
		$this->assertTrue(9 !== $res);
		$this->assertTrue(15 === $res);
		Once::clear('test');
		$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;});
		$this->assertTrue(3 === $res);
		$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
		$this->assertTrue(3 === $res);
		Once::clear('test');
		$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
		$this->assertTrue(6 === $res);
		Once::clear('test');
		$res = Once::exec('test', function() {return true;});
		$this->assertTrue(true === $res);
		$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;}, array(), true);
		assert(3 === $res);
		$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;}, array(), true);
		assert(6 === $res);
	}
}