(function (){
	
	var Once = {
		store: {},
		key: function (name, args) {
			return name + '-' + Hash.exec(args);
		},
		exec: function (name, call, args, re) {
			var hash=Once.key(name, args);
			if (!Once.store[hash]||re) {
				if (typeof(args)=='object'&&args.constructor==Array) {
					var r=call.apply(this, args.concat([re]));
				} else {
					var r=call.apply(this, [re]);
				}
				Once.store[hash] = {
					result:r
				};
			}
			return Once.store[hash].result;
		},
		clear: function (name, args) {
			var hash=Hash.exec(name, args);
			delete Once.store[hash];
			return hash;
		}	
	}
	window.Once=Once;
})();