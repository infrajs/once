<?php
namespace infrajs\once;
use infrajs\nostore\Nostore;

class Once
{
	public static $parents = [];
	public static $childs = array();
	public static $item = false;
	public static $items = array();
	public static $lastid = false;
	public static $nextgid = false;
	public static function &createItem($args = array(), $condfn = array(), $condargs = array(), $level = 0)
	{
		$level++;
		list($gid, $id, $hash, $fn, $file) = static::hash($args, $level);
		if (isset(Once::$items[$id])) return Once::$items[$id];
		$item = array();
		$item['gid'] = $gid;
		$item['file'] = $file;
		$item['id'] = $id;
		$item['fn'] = $fn;
		$item['args'] = $args;
		$item['hash'] = $hash;
		$item['conds'] = array();
		$item['nostore'] = false;
		$item['condfn'] = $condfn;
        $item['condargs'] = $condargs;  
		$item['timer'] = 0;
		$item['start'] = false;
		$item['title'] = $id;
		$item['gtitle'] = $gid;
		$item['childs'] = array();

		Once::$items[$id] = &$item;
		return $item;
	}
	public static function encode($str) {
		if (strlen($str)>100) return md5($str);
		$str = preg_replace('/[\\\\\\/\.,]/ui',"_",$str);//Принципиальные, но запрещённые символы
		$str = preg_replace("/[^a-zA-ZА-Яа-я0-9~\-!_]/ui","-",$str);
		$str = preg_replace('/\-+/', '-', $str);
		$str = trim($str,'-');
		return $str;
	}
	public static $rp = false;
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
		if (isset($callinfos[$level+1])) {
			$fn = Once::encode($callinfos[$level+1]['function']);
		} else {
			$fn = '';
		}
		$callinfo = $callinfos[$level];
		$path = $callinfo['file'];
		
		$path = str_replace(Once::$rp . DIRECTORY_SEPARATOR, '', $path); //Путь от корня
		if (Once::$nextgid) { //Определяем свой Id, что бы можно было сделать функцию clear
			$gid = Once::$nextgid;
			Once::$nextgid = false;
		} else {
			$gid = Once::encode($path) . '-' . $callinfo['line'] . '-' . $fn;	
		}
		$id = md5($gid . '-' . $hashargs);
		return [$gid, $id, $hashargs, $fn, $path];
	}

	public static function end(&$item)
	{		
		Once::$lastid = array_pop(Once::$parents);
		Once::$item = &Once::$items[Once::$parents[sizeof(Once::$parents) - 1]];
	}
	public static function &start(&$item)
    {
        Once::$item['childs'][$item['id']] = true;
        Once::$parents[] = $item['id'];
        Once::$item = &$item;
        return $item;
    }
	public static function clear($gid, $args = array()) {
		if (isset(Once::$items[$gid])) {
			unset(Once::$items[$gid]['start']);
		} else {
			$hashargs = Once::encode(json_encode($args, JSON_UNESCAPED_UNICODE));
			$id = md5($gid . '-' . $hashargs);
			if (isset(Once::$items[$id])) {
				unset(Once::$items[$id]['start']);
			}
		}

	}
	/*public static function lastPrint() {
		echo '<pre>';
		unset(Once::$items[Once::$lastid]['result'])
		print_r(Once::$items[Once::$lastid]);
	}*/
	public static function execfn(&$item, $fn)
	{
		$item['nostore'] = Nostore::check(function () use (&$item, $fn) { //Проверка был ли запрет кэша
			$item['result'] = call_user_func_array($fn, $item['args']);
		});
	}
	public static function &func($fn, $args = array(), $condfn = array(), $condargs = array(), $level = 0){

		$level++;

		$item = &static::createItem($args, $condfn, $condargs, $level);
		
		static::start($item);

        $execute = static::isChange($item);
        if ($execute) {
        	//Раз выполняем значит будут новые данные даже если это было загружено
            $item['start'] = true; //Метка что результат есть.
            $item['conds'] = array(); //Если загрузили надо удалить старые условия, сейчас появятся новые может быть... 
            $t = microtime(true);
			static::execfn($item, $fn);
			$t = microtime(true) - $t;
			$item['timer'] += $t;
        } else {
        	if ($item['nostore']) Nostore::on();
        }
		static::end($item);
		if ($execute) {
			Once::$item['timer'] -= $t;
		}
        return $item['result'];
	}
	public static function isChange(&$item) {
		return empty($item['start']);
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
		Once::$item['timer'] += $t;
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
    	Once::$rp = realpath('.');
		Once::$item = &Once::createItem();
		Once::$parents[] = Once::$item['id'];
    }
}

Once::init();
