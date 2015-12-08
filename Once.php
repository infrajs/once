<?php
namespace infrajs\once;

use infrajs\hash\Hash;

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
			return $store['result'] = $call;
		}

		if (isset($store) && !$re) {
			return $store['result'];
		}

		$store['exec'] = true;

		$v = array_merge($args, array($re, $hash));
		$v = call_user_func_array($call, $v);

		$store['result'] = $v;

		return $store['result'];
	}
	public static function clear($name, $args){
		$strargs = Hash::make($args);
		$hash = $name.$strargs;
		unset(self::$store[$hash]);
		return $hash;
	}
}
