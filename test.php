<?php
namespace infrajs\once;
if (!is_file('vendor/autoload.php')) {
    chdir('../../../'); //Согласно фактическому расположению файла
    require_once('vendor/autoload.php');
}

//$func = function() {$a = 1; $b = 2; return $a + $b;}
$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;});
assert(3 === $res);
$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
assert(3 === $res);
$res = Once::clear('test');
$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
assert(6 === $res);
$res = Once::clear('test');
$res = Once::exec('test', function() {return true;});
assert(true === $res);
if ($res) {
	echo '{result:1}';
	return true; 	
}
echo '{result:0, msg:"В работе методов класса Once произошел сбой."}';
return false;

