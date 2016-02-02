<?php
use \infrajs\once\Once;
require_once __DIR__ . '/../Once.php';
class OnceTest extends PHPUnit_Framework_TestCase
{
	public function testOnce()
	{
		$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;});
		$this->assertTrue(3 === $res);
		$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
		$this->assertTrue(3 === $res);
		$res = Once::clear('test');
		$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
		$this->assertTrue(6 === $res);
		$res = Once::clear('test');
		$res = Once::exec('test', function() {return true;});
		$this->assertTrue(true === $res);
	}
}