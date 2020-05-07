let Once = {
	store: {},
	omit: function (name, args) {
		var hash = Once.key(name, args);
		if (!Once.store[hash]) {
			Once.store[hash] = { 'result': 'is' };
			return false; //будет означать пропустить в условии
		}
		return true;
	},
	key: function (name, args) {
		if (!name) name = '';
		return name + '-' + JSON.stringify(args);
	},
	exec: function (name, call, args, re) {
		var hash = Once.key(name, args);
		if (!Once.store[hash] || re) {
			if (typeof (args) == 'object' && args.constructor == Array) {
				var r = call.apply(this, args.concat([re]));
			} else {
				var r = call.apply(this, [re]);
			}
			Once.store[hash] = {
				result: r
			};
		}
		return Once.store[hash].result;
	},
	clear: function (name, args) {
		var hash = Once.key(name, args);
		delete Once.store[hash];
		return hash;
	}
}
window.Once = Once;
export { Once };