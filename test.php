<?php
namespace infrajs\once;
if (!is_file('vendor/autoload.php')) {
    chdir('../../../'); //Согласно фактическому расположению файла
    require_once('vendor/autoload.php');
}

//$func = function() {$a = 1; $b = 2; return $a + $b;}
$res = Once::exec('test', function() {$a = 1; $b = 2; return $a + $b;});
assert(3 === $res);
$res = Once::clear('test', array());
$res = Once::exec('test', function() {$a = 3; $b = 2; return $a * $b;});
var_dump($res);
/*$a = [];
$res = assert(Hash::make($a) === '40cd750bba9870f18aada2478b24840a');
$a[] = 'Передали текст';
$res = assert(Hash::make($a) === 'c6a45e965497496002680ac385b17b05');
$a[] = function(){};
$res = assert(Hash::make($a) === 'c6008bd78fcdc1a923c65ecddc8f67ba');
$a[] = new Hash;
$res = assert(Hash::make($a) === '58843b5ad3da812f794515abe816c979');
$b = new Hash;
$res = assert(Hash::make($b) === '639cb6369416f4178c26b9fabba9a38f');
$b = true;
$res = assert(Hash::make($b) === '431014e4a761ea216e9a35f20aaec61c');
$b = false;
$res = assert(Hash::make($b) === 'a2072c8a50f1127f73a55a6b5f574da7');
$b = 15;
$res = assert(Hash::make($b) === '3ae4e7e87b9038a299ee40119700914a');
$b = 'текст';
$res = assert(Hash::make($b) === 'c899e3abc57a1426a4404a74e0a5a0cf');
$b = function(){};
$res = assert(Hash::make($b) === 'b35bcc328f12fccc6d8c7f7ed98cd0f1');
if ($res) {
	echo '{result:1}';
	return true; 	
}
echo '{result:0, msg:"значение поменялось"}';
return false;*/

