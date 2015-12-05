define('?*once/once.js', ['?*hash/hash.js'], function(hash){
	var store={};
	var key = function (name, args) {
		return name + '-' + window.hash(args);
	}
	var once = function (name, call, args, re) {
		var hash=key(name, args);
		if (!store[hash]||re) {
			store[hash]={
				result:call.apply(this, args.concat([re]))
			};
		}
		return store[hash].result;
	}
	var clear = function(name, args) {
		var hash=hash(name, args);
		delete store[hash];
		return hash;
	}
	once.clear=clear;
	once.key=key;
	window.once=once;
	return once;
});