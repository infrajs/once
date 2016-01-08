(function (){
	var store={};
	var key = function (name, args) {
		return name + '-' + infrajs.hash(args);
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
		var hash=infrajs.hash(name, args);
		delete store[hash];
		return hash;
	}
	once.clear=clear;
	once.key=key;
	infra.once=once;
})();