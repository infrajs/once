<?php
namespace infrajs\once;

class Once
{
	public static $parents = [];
	public static $childs = array();
	public static $item = false;
	public static $items = array();
	public static $lastid = false;

	public static function &createItem($args = array(), $condfn = array(), $condargs = array(), $level = 0)
	{
		$level++;
		list($gid, $id, $hash, $fn) = static::hash($args, $level);
		if (isset(Once::$items[$id])) return Once::$items[$id];
		$item = array();
		$item['gid'] = $gid;
		$item['id'] = $id;
		$item['fn'] = $fn;
		$item['args'] = $args;
		$item['hash'] = $hash;
		$item['condfn'] = $condfn;
        $item['condargs'] = $condargs;  
		$item['exec'] = array();
		$item['exec']['timer'] = 0;
		$item['exec']['childs'] = array();
		
		$item['exec']['conds'] = array();
        if ($condfn) {
            $item['exec']['conds'][] = array('fn' => $condfn, 'args' => $condargs);
        }

		Once::$items[$id] = &$item;
		return $item;
	}
	public static function encode($str) {
		$str = preg_replace ("/[^a-zA-ZА-Яа-я0-9~\-!]/ui","-",$str);
		$str = preg_replace('/\-+/', '-', $str);
		$str = trim($str,'-');
		return $str;
	}
	/**
	 * На заданное количество шагов назад определяем файл и сроку вызов, по которым формируем $gid
	 * На основе $args формируем $hash который вместе с $gid формирует $id индетифицирующий место вызова с такими аргуентами
	 * Простые данные в $args формируют заголовок $title
	 * Возвращается массив данных, ожидается конструкция с list
	 *
	 * @param array $args
	 * @param int $level
	 * @return array
	 */
	public static function hash($args = array(), $level = 0)
	{
		$hashargs = Once::encode(json_encode($args, JSON_UNESCAPED_UNICODE));
		$callinfos = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level + 2);
		$fn = Once::encode($callinfos[$level+1]['function']);
		$callinfo = $callinfos[$level];
		$path = $callinfo['file'];
		$src = realpath('.');
		$path = str_replace($src . DIRECTORY_SEPARATOR, '', $path); //Путь от корня
		$gid = Once::encode($path) . '-' . $callinfo['line'] . '-' . $fn;
		$id = md5($gid . '-' . $hashargs);
		return [$gid, $id, $hashargs, $fn];
	}

	public static function end(&$item)
	{
		$parents = array_reverse(Once::$parents); //От последнего вызова
        foreach ($parents as $pid) {
            if (Once::$items[$pid]['condfn']) break; //У родителя своя функция проверки
            foreach ($item['exec']['conds'] as $cond) {
                if (!in_array($cond, Once::$items[$pid]['exec']['conds'])) Once::$items[$pid]['exec']['conds'][] = $cond;
            }
            if (!isset(Once::$items[$pid]['exec']['end'])) break; //Дальше этот родитель передаст сам, когда завериштся
        }

		Once::$lastid = array_pop(Once::$parents);
		if (sizeof(Once::$parents)) {
			Once::$item = &Once::$items[Once::$parents[sizeof(Once::$parents) - 1]];
		} else {
			$r = null;
			Once::$item = &$r;
		}
	}
	public static function &start($item)
    {
        if (!in_array($item['id'], Once::$item['exec']['childs'])) Once::$item['exec']['childs'][] = $item['id'];
        Once::$parents[] = $item['id'];
        Once::$item = &$item;
        return $item;
    }
	public static function omit($args = array(), $condfn = array(), $condargs = array(), $level = 0)
	{
		$level++;
		$item = &static::createItem($args, $condfn, $condargs, $level);
		static::start($item);
		if (empty($item['exec']['start'])) {
			$item['exec']['start'] = true;
			return false;
		}
		static::end($item);
		return true;
	}
	public static function clear($id) {
		unset(Once::$items[$id]['exec']['start']);
	}
	public static function execfn(&$item, $fn) {
		$item['exec']['result'] = call_user_func_array($fn, $item['args']);
    }
	public static function &func($fn, $args = array(), $condfn = array(), $condargs = array(), $level = 0){
		$level++;
		$item = &static::createItem($args, $condfn, $condargs, $level);
		static::start($item);
        $execute = static::isChange($item);
        if ($execute) {
            $item['exec']['start'] = true; //Метка что кэш есть.
            $t = microtime(true);
			static::execfn($item, $fn);
			$t = microtime(true) - $t;
			$item['exec']['timer'] += $t;
			$item['exec']['end'] = true;
			Once::$item['exec']['timer'] -= $t;
        }
		static::end($item);
        return $item['exec']['result'];
	}
	public static function isChange(&$item) {
		return empty($item['exec']['start']);
	}
    /**
	* Имитирует выполнение $call в рамках указанного $item
	**/
	public static function resume(&$parents, $call) {
		$old = &Once::$item;
		$oldparents = Once::$parents;

		Once::$item = &Once::$items[$parents[sizeof($parents)-1]];
		Once::$parents = $parents;

		$t = microtime(true);
		//Мы не знаем какая часть его собственная, а какая относится к его детям
		//Надо что бы дети ничего не корректировали или считать детей
		//Дети могу вычитать, а тут всё прибавить
		$r = $call();
		$t = microtime(true) - $t;
		Once::$item['exec']['timer'] += $t;
		Once::$item = &$old;
		Once::$parents = $oldparents;

		return $r;
	}
	public static function &exec($gtitle, $fn, $args = array(), $condfn = array(), $condargs = array(), $level = 0)
    {   
        $level++;
        $res = &static::func($fn, $args, $condfn, $condargs, $level);
        static::setGtitle($gtitle, Once::$items[Once::$lastid]);
        return $res;
    }
    public static function setGtitle($gtitle, &$item = false){
        if (!$item) $item = &Once::$item;
        if ($gtitle) {
            $item['gtitle'] = $gtitle;
        } else if (!$gtitle) {
            $gtitle = $item['fn'];
            $item['gtitle'] = $gtitle;    
        }
        if (sizeof($item['args']) == 0) {
            $item['title'] = $gtitle;
        }
    }
    public static function init () {
		Once::$item = &Once::createItem();
		Once::$parents[] = Once::$item['id'];
    }
}

Once::init();
