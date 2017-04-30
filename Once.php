<?php
namespace infrajs\once;
use infrajs\hash\Hash;

class Once {
	public static $store = array();
	public static function omit($name, $args = array())
	{	
		if (sizeof($args)) $hash = Hash::make($args);
		else $hash = '';
		if (empty(Once::$store[$name][$hash])) {
			if (!isset(Once::$store[$name])) Once::$store[$name] = array();
			Once::$store[$name][$hash] = array('result' => 'is');
			return false; //будет означать пропустить в условии
		}
		return true;
	}
	public static function &exec($name, $call, $args = array(), $re = false) {

		if (sizeof($args)) {
			$hash = Hash::make($args);
		} else {
			$hash = '';
		}
		
		if (!isset(Once::$store[$name])) Once::$store[$name] = array();
		if (!isset(Once::$store[$name][$hash])) Once::$store[$name][$hash] = array();
		$store=&Once::$store[$name][$hash];

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
		if (empty(self::$store[$name])) return;
		if ($args===true) {
			unset(self::$store[$name]);
			return;
		}

		if (sizeof($args)) {
			$hash = Hash::make($args);
		} else {
			$hash = '';
		}
		
		unset(self::$store[$name][$hash]);
		return;
	}
}
