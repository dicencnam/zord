(function() {
	var div = document.createElement('div');
	if ('classList' in div) {
		return; // classList property exists
	}

	function changeList(element, itemName, add, remove) {
		var s = element.className || '';
		var list = s.split(/\s+/g);
		if (list[0] === '') {
			list.shift();
		}
		var index = list.indexOf(itemName);
		if (index < 0 && add) {
			list.push(itemName);
		}
		if (index >= 0 && remove) {
			list.splice(index, 1);
		}
		element.className = list.join(' ');
		return (index >= 0);
	}

	var classListPrototype = {
		add: function(name) {
			changeList(this.element, name, true, false);
		},
		contains: function(name) {
			return changeList(this.element, name, false, false);
		},
		remove: function(name) {
			changeList(this.element, name, false, true);
		},
		toggle: function(name) {
			changeList(this.element, name, true, true);
		}
	};

	Object.defineProperty(HTMLElement.prototype, 'classList', {
		get: function() {
			if (this._classList) {
				return this._classList;
			}

			var classList = Object.create(classListPrototype, {
				element: {
					value: this,
					writable: false,
					enumerable: true
				}
			});
			Object.defineProperty(this, '_classList', {
				value: classList,
				writable: false,
				enumerable: false
			});
			return classList;
		},
		enumerable: true
	});
})();
(function() {
	try {
		var a = new Uint8Array(1);
		return; //no need
	} catch(e) { }

	function subarray(start, end) {
		return this.slice(start, end);
	}

	function set_(array, offset) {
		if (arguments.length < 2) offset = 0;
		for (var i = 0, n = array.length; i < n; ++i, ++offset)
			this[offset] = array[i] & 0xFF;
	}

	// we need typed arrays
	function TypedArray(arg1) {
		var result;
		if (typeof arg1 === "number") {
			 result = new Array(arg1);
			 for (var i = 0; i < arg1; ++i)
				 result[i] = 0;
		} else
			 result = arg1.slice(0);
		result.subarray = subarray;
		result.buffer = result;
		result.byteLength = result.length;
		result.set = set_;
		if (typeof arg1 === "object" && arg1.buffer)
			result.buffer = arg1.buffer;

		return result;
	}

	window.Uint8Array = TypedArray;
	window.Uint32Array = TypedArray;
	window.Int32Array = TypedArray;
})();
