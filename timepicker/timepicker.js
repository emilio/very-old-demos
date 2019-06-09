/*!
 * Timepikr
 * @author Emilio Cobos (http://emiliocobos.net)
 */

(function(window, document, undefined) {
	'use strict';
	var
	/** Time for the arrow */
	ARROW_CLICK_TIMER = 100,
	/** The pattern that a valid time must use: HH:mm */
	validTimeRegex = /^([0-9]{1,2})\:([0-9]{1,2})$/
	/**
	 * Merge two objects into one
	 * @param {Object} obj1
	 * @param {Object} obj2
	 * @return {Object}
	 */
	, merge = function(obj1, obj2) {
		var ret = obj1,
			i;
		for(i in obj2) {
			if( obj2.hasOwnProperty(i) ) {
				ret[i] = obj2[i];
			}
		}
		return ret;
	}
	/** Yay! no options. here for consistency (later versions may include options) */
	, defaults = {
		element: null
	}
	/**
	 * Basic addEvent function. No need for something more complicated, since we'll save references to the target
	 * @param {HTMLElement} el
	 * @param {String} type the event type (eg: click)
	 * @param {Function} fn the callback
	 * @param {Boolean} capture (not used)
	 * @return void
	 */
	, addEvent = function(el, type, fn, capture) {
		if( el.addEventListener ) {
			el.addEventListener(type, fn, !! capture);
		} else {
			el.attachEvent('on' + type, fn);
		}
	}
	/**
	 * Trim both sides of a string, using native String.prototype.trim if supported
	 * @param {String} str
	 * @return {String}
	 */
	, trim = function(str) {
		return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g,'');
	}
	/**
	 * String | Number to int conversion
	 * @param {String|Number} num
	 * @return {Number}
	 */
	, toInt = function(num) {
		return parseInt(num, 10);
	}
	/**
	 * Check if a time string is valid
	 * @param {String} time
	 * @return {Boolean}
	 */
	, isValidTime = function(time) {
		var tokens = (validTimeRegex).exec(time),
			hour,
			minutes;
		if( ! tokens ) {
			return false;
		}

		hour = toInt(tokens[1]);
		minutes = toInt(tokens[2]);

		
		return tokens && (hour < 24 && minutes < 60);
	}
	/** Selectionstart support (Basically IE9+) */
	, hasSelectionStart = 'selectionStart' in document.createElement('textarea')
	/**
	 * Get the selection start and end of an HTML element
	 * @param {HTMLElement} el
	 * @return {Object}
	 */
	, getCaretPos = function(el) {
		var start,
			end,
			len,
			range,
			inputRange;

		if( hasSelectionStart ) {
			start = el.selectionStart;
			end = el.selectionEnd;
		} else if (document.selection && (range = document.selection.createRange()) && range.parentElement() === el) {
			el.focus();
			len = el.value.length;
			inputRange = el.createTextRange();
			inputRange.moveToBookmark(range.getBookmark());
			start = -inputRange.moveStart("character", -len);
			end = -inputRange.moveEnd("character", -len);
		}

		return {
			start: start,
			end: end
		}
	}
	/**
	 * Set the cursor position of an element
	 * @param {HTMLElement} el
	 * @param {Number} start
	 * @param {Number} end
	 * @return {Boolean} always true
	 */
	, setCaretPos = function(el, start, end) {
		var range;
		if( ! end ) {
			end = start;
		}
		if( el.setSelectionRange ) {
			el.setSelectionRange(start, end);
		} else if( hasSelectionStart ) {
			el.selectionStart = start;
			el.selectionEnd = end;
		} else if( el.createTextRange ) {
			el.focus();
			range = el.createTextRange();
			range.collapse(true);
			range.moveStart(start);
			range.moveEnd(end);
			range.select();
		}
		return true;
	}
	/** setTimeout reference */
	, setTimeout = window.setTimeout;

	/**
	 * @constructor
	 */
	window.TimePikr = function(opts) {
		var self = this,
			el,
			wrapper,
			timepicker,
			arrowUp,
			arrowDown;
		opts = merge(defaults, opts);

		self._oninput = function() {
			if( isValidTime(timepicker.value) ) {
				self._value = timepicker.value;
			}
		};
		
		self._downArrowClicked = false;
		self._downArrowClick = function() {
			var method = self._lastSelected === 'hours' ? 'decreaseHours' : 'decreaseMinutes';
			self[method](1)
		}
		self._downArrowTimeout = null;
		self._downArrowMouseup = function() {
			self._downArrowClicked = false;
			if( self._downArrowTimeout ) {
				clearTimeout(self._downArrowTimeout);
			}
		}
		self._downArrowMousedown = function() {
			self._downArrowClicked = true;
			self._downArrowTimeout = setTimeout(function mantainClick() {
				if( self._downArrowClicked ) {
					self._downArrowClick();
					self._downArrowTimeout = setTimeout(mantainClick, ARROW_CLICK_TIMER)
				}
			}, ARROW_CLICK_TIMER);
		}

		self._upArrowClicked = false;
		self._upArrowClick = function() {
			var method = self._lastSelected === 'hours' ? 'increaseHours' : 'increaseMinutes';
			self[method](1)
		}
		
		self._upArrowTimeout = null;
		self._upArrowMouseup = function() {
			self._upArrowClicked = false;
			if( self._upArrowTimeout ) {
				clearTimeout(self._upArrowTimeout)
			}
		}
		self._upArrowMousedown = function() {
			self._upArrowClicked = true;
			self._upArrowTimeout = setTimeout(function mantainClick() {
				if( self._upArrowClicked ) {
					self._upArrowClick();
					self._upArrowTimeout = setTimeout(mantainClick, ARROW_CLICK_TIMER)
				}
			}, ARROW_CLICK_TIMER);
		}

		/* There's no element or time is supported */
		if( ! opts.element || opts.element.type === 'time' ) {
			return;
		}

		el = self._el = opts.element;

		el.style.display = "none";

		// IE da problemas al cambiar type, así que ocultarlo debería de bastar.
		try {
			el.type = 'hidden';
		} catch( e ) {}

		wrapper = self._wrapper = el.parentNode.insertBefore(document.createElement('div'), el);
		wrapper.className = 'timepikr';
		
		timepicker = self._timepicker = wrapper.appendChild(document.createElement('input'));
		
		timepicker.type = 'text';
		timepicker.value = el.value || '00:00';
		timepicker.className = 'timepikr-input';

		arrowUp = wrapper.appendChild(document.createElement('div'));
		arrowUp.className = 'timepikr-arrow timepikr-arrow-up';

		arrowDown = wrapper.appendChild(document.createElement('div'));
		arrowDown.className = 'timepikr-arrow timepikr-arrow-down';


		addEvent(arrowDown, 'click', self._downArrowClick);
		addEvent(arrowDown, 'mousedown', self._downArrowMousedown);
		addEvent(arrowDown, 'mouseup', self._downArrowMouseup);
		addEvent(arrowDown, 'mouseout', self._downArrowMouseup);

		addEvent(arrowUp, 'click', self._upArrowClick);
		addEvent(arrowUp, 'mousedown', self._upArrowMousedown);
		addEvent(arrowUp, 'mouseup', self._upArrowMouseup);
		addEvent(arrowUp, 'mouseout', self._upArrowMouseup);


		if( 'oninput' in timepicker ) {
			addEvent(timepicker, 'input', self._oninput);
		} else {
			addEvent(timepicker, 'keyup', self._oninput);
		}

		addEvent(timepicker, 'blur', function() {
			self._oninput();
			self.updateToCurrentValue();
		})

		if( 'selectionStart' in timepicker ) {
			addEvent(timepicker, 'click', function() {
				var selection = getCaretPos(timepicker);
				if( selection.start === selection.end ) {
					switch(selection.start) {
						case 0:
						case 1:
						case 2:
							self._lastSelected = 'hours';
							setCaretPos(timepicker, 0, 2)
						break;
						case 3:
						case 4:
						case 5:
							self._lastSelected = 'minutes';
							setCaretPos(timepicker, 3,5)
						break;
					}
				}
			})
		}
	}

	/**
	 * The public API
	 */
	window.TimePikr.prototype = merge(window.TimePikr.prototype, {
		/** the current value */
		_value: '00:00'

		/** The last selected part of the time */
		, _lastSelected: 'minutes'

		/** 
		 * Get the current hour
		 * @return {Number}
		 */
		, getHour: function() {
			return toInt(this._value.match(/^[0-9]{2}/)[0]);
		}

		/** 
		 * Get the current minutes
		 * @return {Number}
		 */
		, getMinutes: function() {
			return toInt(this._value.match(/[0-9]{2}$/)[0]);
		}

		/** 
		 * Set the current hour
		 * @param {Number} hour
		 * @return void
		 */
		, setHour: function(hour) {
			this._value = hour + ':' + this.getMinutes();
			this.updateToCurrentValue();
		}

		/** 
		 * Set the current minutes
		 * @param {Number} hour
		 * @return void
		 */
		, setMinutes: function(minutes) {
			this._value = this.getHour() + ':' + minutes;
			this.updateToCurrentValue();
		}

		/** 
		 * Increase the current minutes
		 * @param {Number} amount
		 * @return void
		 */
		, increaseMinutes: function(amount) {
			var minutes = this.getMinutes() + amount,
				hour = this.getHour();
			if( minutes >= 60 ) {
				minutes -= 60;
				hour++;
			} else if( minutes < 0 ) {
				minutes += 60;
				hour--;
			}
			if( hour < 0  ) {
				hour += 24;
			} else if( hour >= 24 ) {
				hour -= 24;
			}
			this._value = hour + ':' + minutes;
			this.updateToCurrentValue();
		}

		/** 
		 * Decrease the current minutes
		 * @param {Number} amount
		 * @return void
		 */
		, decreaseMinutes: function(amount) {
			// ejem... genius! :P
			return this.increaseMinutes(-amount)
		}
		/** 
		 * Increase the current hour
		 * @param {Number} amount
		 * @return void
		 */
		, increaseHours: function(amount) {
			var minutes = this.getMinutes(),
				hour = this.getHour() + amount;
			if( hour < 0 ) {
				hour += 24;
			} else if( hour >= 24 ) {
				hour -= 24;
			}
			this._value = hour + ':' + minutes;
			this.updateToCurrentValue();
		}
		/** 
		 * Increase the current hours
		 * @param {Number} amount
		 * @return void
		 */
		, decreaseHours: function(amount) {
			return this.increaseHours(-amount);
		}
		/** 
		 * Update the value to an standard time
		 * @return void
		 */
		, updateToCurrentValue: function() {
			var 
				self = this
				, current = self._value.match(validTimeRegex)
				, hour = toInt(current[1])
				, minutes = toInt(current[2]);
			if(hour < 10) {
				hour = '0' + hour;
			}
			if( minutes < 10 ) {
				minutes = '0' + minutes;
			}
			self._timepicker.value = self._el.value = self._value = (hour + ':' + minutes);
		}
	})

})(window, window.document);