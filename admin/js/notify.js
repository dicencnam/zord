/**
* Notification sub/pub
* @class $notify
* @static
* @module JSElem
* @author David Dauvergne
* @copyright 2013 David Dauvergne
* @licence GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/
(function() {

	window.$notify = {
		/**
		* Object des canaux
		*
		* @type object
		*/
		_channel : {},

		/**
		* Subscription
		* @method sub
		* @param {string} target Target Subscription
		* @param {function} handler
		* @param {string} position Position for publication ('before' or 'after' default: undefined)
		*/
		sub : function (target, handler, position) {
			if (position!==undefined)
				target = target+'/'+position;

			if (!this._channel[target])
				this._channel[target] = [];

			this._channel[target].push(handler);
			return [target, handler];
		},

		/**
		*
		* Publication
		*
		* @method pub
		* @param {string} target Target publication
		* @param {mixed} data
		* @returns {mixed}
		*/
		pub : function (target, data) {
			this._channel[target+'/before'] && this._channel[target+'/before'].forEach( function (item) {
				data = item.apply($notify, data || []);
			});
			this._channel[target] && this._channel[target].forEach( function (item) {
				data = item.apply($notify, data || []);
			});
			this._channel[target+'/after'] && this._channel[target+'/after'].forEach( function (item) {
				data = item.apply($notify, data || []);
			});
			return data;
		},

		/**
		* Unsubscribe
		*
		* if 'handler' is 'undefined' it's all channel subscriptions that are deleted
		*
		* @method unSub
		* @param {string} target Target unsubscribe
		* @param {mixed} handler Callback function for a channel type 'Object',
		* otherwise array returned for a subscription type 'string'
		*/
		unSub : function (target, handler) {
			if(handler!==undefined){
				var t = handler[0];
				var that = this;
				this._channel[t] && that._channel[t].forEach( function(item,index){
						if( item == handler[1]) {
							that._channel[t].splice(index, 1);
							if (that._channel[t].length==0)
								delete(that._channel[t]);
						}
				});
			} else {
				if (this._channel[target])
					delete(this._channel[target]);
			}
		},

		/**
		* Connection between two channels
		*
		* @method connect
		* @param {string} fromTarget Target Subscription
		* @param {string} toChannel Target publication
		* @param {string} position Position for publication ('before' or 'after' default: undefined)
		*/
		connect : function (fromTarget, toTarget, position) {
			var that = this;
			that.sub(fromTarget, function () {
				var args = Array.prototype.slice.call(arguments, 0);
				that.pub(toTarget,arguments);
				return arguments;
			},position);
		}
	};
}());
