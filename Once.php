<?php
namespace infrajs\once;

use infrajs\hash\Hash;
require_once __DIR__ . '/../hash/Hash.php';

class Once {
	public static $store=array();
	public static function &exec($name, $call, $args = array(), $re = false) {

		if (sizeof($args)) {
			$hash = $name.Hash::make($args);
		} else {
			$hash = $name;
		}

		if (!isset(self::$store[$hash])) {
			self::$store[$hash] = array();
		}
		$store=&self::$store[$hash];

		if (!is_callable($call)) {
			$store['result'] = $call;
			$r=true;
			return $r;
		}
		if (!empty($store) && !$re) {
			return $store['result'];
		}

		$store['exec'] = true;

		$v = array_merge($args, array($re, $hash));
		$v = call_user_func_array($call, $v);

		$store['result'] = $v;

		return $store['result'];
	}
	public static function clear($name, $args = array()){
		if (sizeof($args)) {
			$hash = $name.Hash::make($args);
		} else {
			$hash = $name;
		}
		unset(self::$store[$hash]);
		return $hash;
	}
}
