<?php
namespace infrajs\once;
if (!is_file('vendor/autoload.php')) {
    chdir('../../../'); //Согласно фактическому расположению файла
    require_once('vendor/autoload.php');
}

$res = Once::exec('test', function($a, $b) {return $a + $b;}, [4, 5]);
assert(9 === $res);
$res = Once::exec('test', function($a, $b) {return $a + $b;}, [10, 5]);
assert(9 !== $res);
assert(15 === $res);
Once::clear('test');
$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;});
assert(3 === $res);
$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
assert(3 === $res);
Once::clear('test');
$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
assert(6 === $res);
Once::clear('test');
$res = Once::exec('test', function() {return true;});
assert(true === $res);
$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;}, array(), true);
assert(3 === $res);
$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;}, array(), true);
assert(6 === $res);
if ($res) {
    echo '{"result":1}';
	return;
}
echo '{result:0, msg:"В работе методов класса Once произошел сбой."}';

