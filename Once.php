<?php
namespace infrajs\once;

use infrajs\hash\Hash;

class Once {
	public static $store=array();
	public static function &exec($name, $call, $args = array(), $re = false) {
		$strargs = Hash::make($args);
		$hash = $name.$strargs;

		if (!is_callable($call)) {
			$re = false;
			self::$store[$hash] = array('result' => $call);
		}
		if (isset(self::$store[$hash]) && !$re) {
			return self::$store[$hash]['result'];
		}
		self::$store[$hash] = array('exec' => true);

		$v = array_merge($args, array($re, $hash));
		$v = call_user_func_array($call, $v);

		self::$store[$hash]['result'] = $v;

		return self::$store[$hash]['result'];
	}
	public static function clear($name, $args){
		$strargs = Hash::make($args);
		$hash = $name.$strargs;
		unset(self::$store[$hash]);
		return $hash;
	}
}
