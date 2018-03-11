<?php
namespace infrajs\once;
use infrajs\config\Config;
if (!is_file('vendor/autoload.php')) {
    chdir('../../../'); //Согласно фактическому расположению файла
    require_once('vendor/autoload.php');
}


$res = Once::func(function($a, $b) {return $a + $b;}, [4, 5]);
assert(9 === $res);
$res = Once::func(function($a, $b) {return $a + $b;}, [10, 5]);
assert(9 !== $res);
assert(15 === $res);
Once::clear(Once::$lastid);
$res = Once::func(function() {$a = 1; $b = 2; return $a + $b;});
assert(3 === $res);
$res = Once::func(function() {$a = 3; $b = 2; return $a * $b;});
assert(6 === $res);

Once::clear(Once::$lastid);
$res = Once::func( function() {$a = 3; $b = 2; return $a * $b;});
assert(6 === $res);
Once::clear(Once::$lastid);
$res = Once::func( function() {return true;});
assert(true === $res);
$res = Once::func( function() {$a = 1; $b = 2; return $a + $b;}, array(), true);
assert(3 === $res);
$res = Once::func( function() {$a = 3; $b = 2; return $a * $b;}, array(), true);
assert(6 === $res);

echo '{result: 1}';