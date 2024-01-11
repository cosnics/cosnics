(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("vue"));
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["cosnics-presence"] = factory(require("vue"));
	else
		root["cosnics-presence"] = factory(root["Vue"]);
})((typeof self !== 'undefined' ? self : this), (__WEBPACK_EXTERNAL_MODULE__203__) => {
return /******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 983:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"new-presence-status":"New status","label":"Label","title":"Title","aliasses":"Corresponds to","color":"Color","checkout":"Checkout possible","no-checkout":"No checkout","verification-icon":"Verification icon for self registration"},"nl":{"new-presence-status":"Nieuwe status","label":"Label","title":"Titel","aliasses":"Komt overeen met","color":"Kleur","checkout":"Checkout mogelijk","no-checkout":"Geen checkout","verification-icon":"Verificatie-icoon voor zelfregistratie"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 296:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"total":"Total","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","export":"Export","legend":"Legend","students-not-in-course":"Students not in course","without-status":"Without status","checkout-mode":"Checkout mode","show-all-periods":"Show all periods","new-period":"New period","refresh":"Refresh","changes-filters":"You have made changes so that the shown results possibly no longer reflect the chosen filter criteria. Choose different criteria or click refresh to remedy.","remove-period":"Remove period","more":"More"},"nl":{"total":"Totaal","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","export":"Exporteer","legend":"Legende","students-not-in-course":"Studenten niet in cursus","without-status":"Zonder status","checkout-mode":"Uitcheckmodus","show-all-periods":"Toon alle perioden","new-period":"Nieuwe periode","refresh":"Vernieuwen","changes-filters":"Je hebt een wijziging gedaan waardoor de getoonde resultaten mogelijk niet meer overeenkomen met de gekozen filtercriteria. Kies andere criteria of klik op Vernieuwen om dit op te lossen.","remove-period":"Verwijder periode","more":"Meer"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 26:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","print-qr":"Display / Print QR code for general self registration","self-registration-off":"General self registration OFF","self-registration-on":"General self registration ON"},"nl":{"error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","print-qr":"Toon / Print QR code voor globale zelfregistratie","self-registration-off":"Globale zelfregistratie UIT","self-registration-on":"Globale zelfregistratie AAN"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 356:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"legend":"Legend","period":"Period","checked-in":"Checked in","checked-out":"Checked out"},"nl":{"legend":"Legende","period":"Periode","checked-in":"Ingechecked","checked-out":"Uitgechecked"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 991:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"error-NoTitleGiven":"You haven\u0027t given a title for the following status:","error-TitleUpdateForbidden":"The title of the status \u0027{title}\u0027 can no longer be changed.","error-InvalidType":"The status \u0027{title}\u0027 has a wrong type set.","error-PresenceStatusMissing":"The status \u0027{title}\u0027 can no longer be removed.","error-InvalidAlias":"The status with title \u0027{title}\u0027 has a wrong mapping set.","error-AliasUpdateForbidden":"The mapping of the status \u0027{title}\u0027 can no longer be changed.","error-NoCodeGiven":"You haven\u0027t given a label for the status \u0027{title}\u0027.","error-NoColorGiven":"You haven\u0027t given a color for the status \u0027{title}\u0027.","error-InvalidColor":"You have given the status \u0027{title}\u0027 an invalid color.","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","changes-not-saved":"Your changes have not been saved."},"nl":{"error-NoTitleGiven":"Je hebt geen titel opgegeven voor de volgende status:","error-TitleUpdateForbidden":"De titel van de status \u0027{title}\u0027 kan niet meer gewijzigd worden.","error-InvalidType":"De status \u0027{title}\u0027 status heeft een verkeerd type.","error-PresenceStatusMissing":"De status \u0027{title}\u0027 kan niet meer gewist worden.","error-InvalidAlias":"De status \u0027{title}\u0027 heeft een verkeerde mapping.","error-AliasUpdateForbidden":"De mapping van de status \u0027{title}\u0027 kan niet meer gewijzigd worden.","error-NoCodeGiven":"Je hebt geen label opgegeven voor de status \u0027{title}\u0027.","error-NoColorGiven":"Je hebt geen kleur opgegeven voor de status \u0027{title}\u0027.","error-InvalidColor":"Je hebt de status \u0027{title}\u0027 een verkeerde kleur toegewezen.","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","changes-not-saved":"Je wijzigingen werden niet opgeslagen."}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 898:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"save":"Save","cancel":"Cancel"},"nl":{"save":"Opslaan","cancel":"Annuleren"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 859:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"save":"Save presence"},"nl":{"save":"Aanwezigheid opslaan"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 120:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"move-up":"Move up","move-down":"Move down","remove":"Remove"},"nl":{"move-up":"Verplaats naar boven","move-down":"Verplaats naar beneden","remove":"Verwijder"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 822:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"display":"Display"},"nl":{"display":"Weergave"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 98:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"last-name":"Last name","first-name":"First name","official-code":"Official code","period":"Period","new-period":"New period","checked-in":"Checked in","checked-out":"Checked out","not-checked-out":"Not checked out","checkout-mode":"Checkout mode","no-results":"No results","not-applicable":"n/a"},"nl":{"last-name":"Familienaam","first-name":"Voornaam","official-code":"Officiële code","period":"Periode","new-period":"Nieuwe periode","checked-in":"Ingechecked","checked-out":"Uitgechecked","not-checked-out":"Niet uitgechecked","checkout-mode":"Uitcheckmodus","no-results":"Geen resultaten","not-applicable":"n.v.t."}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 269:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"apply":"Apply","cancel":"Cancel","self-registration-off":"Self registration for period OFF","self-registration-on":"Self registration for period ON","set-students-without-status":"Set all students without a status to"},"nl":{"apply":"Pas toe","cancel":"Annuleer","self-registration-off":"Zelfregistratie voor periode UIT","self-registration-on":"Zelfregistratie voor periode AAN","set-students-without-status":"Zet alle studenten zonder status op"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 744:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"period":"Period","without-status":"Without status"},"nl":{"period":"Periode","without-status":"Zonder status"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 752:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"checked-out":"Checked out","not-checked-out":"Not checked out"},"nl":{"checked-out":"Uitgechecked","not-checked-out":"Niet uitgechecked"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 57:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"search":"Find"},"nl":{"search":"Zoeken"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 496:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"not-checked-out":"Not checked out"},"nl":{"not-checked-out":"Niet uitgechecked"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 547:
/***/ ((module) => {

"use strict";


var has = Object.prototype.hasOwnProperty
  , prefix = '~';

/**
 * Constructor to create a storage for our `EE` objects.
 * An `Events` instance is a plain object whose properties are event names.
 *
 * @constructor
 * @private
 */
function Events() {}

//
// We try to not inherit from `Object.prototype`. In some engines creating an
// instance in this way is faster than calling `Object.create(null)` directly.
// If `Object.create(null)` is not supported we prefix the event names with a
// character to make sure that the built-in object properties are not
// overridden or used as an attack vector.
//
if (Object.create) {
  Events.prototype = Object.create(null);

  //
  // This hack is needed because the `__proto__` property is still inherited in
  // some old browsers like Android 4, iPhone 5.1, Opera 11 and Safari 5.
  //
  if (!new Events().__proto__) prefix = false;
}

/**
 * Representation of a single event listener.
 *
 * @param {Function} fn The listener function.
 * @param {*} context The context to invoke the listener with.
 * @param {Boolean} [once=false] Specify if the listener is a one-time listener.
 * @constructor
 * @private
 */
function EE(fn, context, once) {
  this.fn = fn;
  this.context = context;
  this.once = once || false;
}

/**
 * Add a listener for a given event.
 *
 * @param {EventEmitter} emitter Reference to the `EventEmitter` instance.
 * @param {(String|Symbol)} event The event name.
 * @param {Function} fn The listener function.
 * @param {*} context The context to invoke the listener with.
 * @param {Boolean} once Specify if the listener is a one-time listener.
 * @returns {EventEmitter}
 * @private
 */
function addListener(emitter, event, fn, context, once) {
  if (typeof fn !== 'function') {
    throw new TypeError('The listener must be a function');
  }

  var listener = new EE(fn, context || emitter, once)
    , evt = prefix ? prefix + event : event;

  if (!emitter._events[evt]) emitter._events[evt] = listener, emitter._eventsCount++;
  else if (!emitter._events[evt].fn) emitter._events[evt].push(listener);
  else emitter._events[evt] = [emitter._events[evt], listener];

  return emitter;
}

/**
 * Clear event by name.
 *
 * @param {EventEmitter} emitter Reference to the `EventEmitter` instance.
 * @param {(String|Symbol)} evt The Event name.
 * @private
 */
function clearEvent(emitter, evt) {
  if (--emitter._eventsCount === 0) emitter._events = new Events();
  else delete emitter._events[evt];
}

/**
 * Minimal `EventEmitter` interface that is molded against the Node.js
 * `EventEmitter` interface.
 *
 * @constructor
 * @public
 */
function EventEmitter() {
  this._events = new Events();
  this._eventsCount = 0;
}

/**
 * Return an array listing the events for which the emitter has registered
 * listeners.
 *
 * @returns {Array}
 * @public
 */
EventEmitter.prototype.eventNames = function eventNames() {
  var names = []
    , events
    , name;

  if (this._eventsCount === 0) return names;

  for (name in (events = this._events)) {
    if (has.call(events, name)) names.push(prefix ? name.slice(1) : name);
  }

  if (Object.getOwnPropertySymbols) {
    return names.concat(Object.getOwnPropertySymbols(events));
  }

  return names;
};

/**
 * Return the listeners registered for a given event.
 *
 * @param {(String|Symbol)} event The event name.
 * @returns {Array} The registered listeners.
 * @public
 */
EventEmitter.prototype.listeners = function listeners(event) {
  var evt = prefix ? prefix + event : event
    , handlers = this._events[evt];

  if (!handlers) return [];
  if (handlers.fn) return [handlers.fn];

  for (var i = 0, l = handlers.length, ee = new Array(l); i < l; i++) {
    ee[i] = handlers[i].fn;
  }

  return ee;
};

/**
 * Return the number of listeners listening to a given event.
 *
 * @param {(String|Symbol)} event The event name.
 * @returns {Number} The number of listeners.
 * @public
 */
EventEmitter.prototype.listenerCount = function listenerCount(event) {
  var evt = prefix ? prefix + event : event
    , listeners = this._events[evt];

  if (!listeners) return 0;
  if (listeners.fn) return 1;
  return listeners.length;
};

/**
 * Calls each of the listeners registered for a given event.
 *
 * @param {(String|Symbol)} event The event name.
 * @returns {Boolean} `true` if the event had listeners, else `false`.
 * @public
 */
EventEmitter.prototype.emit = function emit(event, a1, a2, a3, a4, a5) {
  var evt = prefix ? prefix + event : event;

  if (!this._events[evt]) return false;

  var listeners = this._events[evt]
    , len = arguments.length
    , args
    , i;

  if (listeners.fn) {
    if (listeners.once) this.removeListener(event, listeners.fn, undefined, true);

    switch (len) {
      case 1: return listeners.fn.call(listeners.context), true;
      case 2: return listeners.fn.call(listeners.context, a1), true;
      case 3: return listeners.fn.call(listeners.context, a1, a2), true;
      case 4: return listeners.fn.call(listeners.context, a1, a2, a3), true;
      case 5: return listeners.fn.call(listeners.context, a1, a2, a3, a4), true;
      case 6: return listeners.fn.call(listeners.context, a1, a2, a3, a4, a5), true;
    }

    for (i = 1, args = new Array(len -1); i < len; i++) {
      args[i - 1] = arguments[i];
    }

    listeners.fn.apply(listeners.context, args);
  } else {
    var length = listeners.length
      , j;

    for (i = 0; i < length; i++) {
      if (listeners[i].once) this.removeListener(event, listeners[i].fn, undefined, true);

      switch (len) {
        case 1: listeners[i].fn.call(listeners[i].context); break;
        case 2: listeners[i].fn.call(listeners[i].context, a1); break;
        case 3: listeners[i].fn.call(listeners[i].context, a1, a2); break;
        case 4: listeners[i].fn.call(listeners[i].context, a1, a2, a3); break;
        default:
          if (!args) for (j = 1, args = new Array(len -1); j < len; j++) {
            args[j - 1] = arguments[j];
          }

          listeners[i].fn.apply(listeners[i].context, args);
      }
    }
  }

  return true;
};

/**
 * Add a listener for a given event.
 *
 * @param {(String|Symbol)} event The event name.
 * @param {Function} fn The listener function.
 * @param {*} [context=this] The context to invoke the listener with.
 * @returns {EventEmitter} `this`.
 * @public
 */
EventEmitter.prototype.on = function on(event, fn, context) {
  return addListener(this, event, fn, context, false);
};

/**
 * Add a one-time listener for a given event.
 *
 * @param {(String|Symbol)} event The event name.
 * @param {Function} fn The listener function.
 * @param {*} [context=this] The context to invoke the listener with.
 * @returns {EventEmitter} `this`.
 * @public
 */
EventEmitter.prototype.once = function once(event, fn, context) {
  return addListener(this, event, fn, context, true);
};

/**
 * Remove the listeners of a given event.
 *
 * @param {(String|Symbol)} event The event name.
 * @param {Function} fn Only remove the listeners that match this function.
 * @param {*} context Only remove the listeners that have this context.
 * @param {Boolean} once Only remove one-time listeners.
 * @returns {EventEmitter} `this`.
 * @public
 */
EventEmitter.prototype.removeListener = function removeListener(event, fn, context, once) {
  var evt = prefix ? prefix + event : event;

  if (!this._events[evt]) return this;
  if (!fn) {
    clearEvent(this, evt);
    return this;
  }

  var listeners = this._events[evt];

  if (listeners.fn) {
    if (
      listeners.fn === fn &&
      (!once || listeners.once) &&
      (!context || listeners.context === context)
    ) {
      clearEvent(this, evt);
    }
  } else {
    for (var i = 0, events = [], length = listeners.length; i < length; i++) {
      if (
        listeners[i].fn !== fn ||
        (once && !listeners[i].once) ||
        (context && listeners[i].context !== context)
      ) {
        events.push(listeners[i]);
      }
    }

    //
    // Reset the array, or remove it completely if we have no more listeners.
    //
    if (events.length) this._events[evt] = events.length === 1 ? events[0] : events;
    else clearEvent(this, evt);
  }

  return this;
};

/**
 * Remove all listeners, or those of the specified event.
 *
 * @param {(String|Symbol)} [event] The event name.
 * @returns {EventEmitter} `this`.
 * @public
 */
EventEmitter.prototype.removeAllListeners = function removeAllListeners(event) {
  var evt;

  if (event) {
    evt = prefix ? prefix + event : event;
    if (this._events[evt]) clearEvent(this, evt);
  } else {
    this._events = new Events();
    this._eventsCount = 0;
  }

  return this;
};

//
// Alias methods names because people roll like that.
//
EventEmitter.prototype.off = EventEmitter.prototype.removeListener;
EventEmitter.prototype.addListener = EventEmitter.prototype.on;

//
// Expose the prefix.
//
EventEmitter.prefixed = prefix;

//
// Allow `EventEmitter` to be imported as module namespace.
//
EventEmitter.EventEmitter = EventEmitter;

//
// Expose the module.
//
if (true) {
  module.exports = EventEmitter;
}


/***/ }),

/***/ 306:
/***/ ((module) => {

"use strict";

module.exports = (promise, onFinally) => {
	onFinally = onFinally || (() => {});

	return promise.then(
		val => new Promise(resolve => {
			resolve(onFinally());
		}).then(() => val),
		err => new Promise(resolve => {
			resolve(onFinally());
		}).then(() => {
			throw err;
		})
	);
};


/***/ }),

/***/ 781:
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
var __webpack_unused_export__;

__webpack_unused_export__ = ({ value: true });
const EventEmitter = __webpack_require__(547);
const p_timeout_1 = __webpack_require__(499);
const priority_queue_1 = __webpack_require__(906);
// eslint-disable-next-line @typescript-eslint/no-empty-function
const empty = () => { };
const timeoutError = new p_timeout_1.TimeoutError();
/**
Promise queue with concurrency control.
*/
class PQueue extends EventEmitter {
    constructor(options) {
        var _a, _b, _c, _d;
        super();
        this._intervalCount = 0;
        this._intervalEnd = 0;
        this._pendingCount = 0;
        this._resolveEmpty = empty;
        this._resolveIdle = empty;
        // eslint-disable-next-line @typescript-eslint/consistent-type-assertions
        options = Object.assign({ carryoverConcurrencyCount: false, intervalCap: Infinity, interval: 0, concurrency: Infinity, autoStart: true, queueClass: priority_queue_1.default }, options);
        if (!(typeof options.intervalCap === 'number' && options.intervalCap >= 1)) {
            throw new TypeError(`Expected \`intervalCap\` to be a number from 1 and up, got \`${(_b = (_a = options.intervalCap) === null || _a === void 0 ? void 0 : _a.toString()) !== null && _b !== void 0 ? _b : ''}\` (${typeof options.intervalCap})`);
        }
        if (options.interval === undefined || !(Number.isFinite(options.interval) && options.interval >= 0)) {
            throw new TypeError(`Expected \`interval\` to be a finite number >= 0, got \`${(_d = (_c = options.interval) === null || _c === void 0 ? void 0 : _c.toString()) !== null && _d !== void 0 ? _d : ''}\` (${typeof options.interval})`);
        }
        this._carryoverConcurrencyCount = options.carryoverConcurrencyCount;
        this._isIntervalIgnored = options.intervalCap === Infinity || options.interval === 0;
        this._intervalCap = options.intervalCap;
        this._interval = options.interval;
        this._queue = new options.queueClass();
        this._queueClass = options.queueClass;
        this.concurrency = options.concurrency;
        this._timeout = options.timeout;
        this._throwOnTimeout = options.throwOnTimeout === true;
        this._isPaused = options.autoStart === false;
    }
    get _doesIntervalAllowAnother() {
        return this._isIntervalIgnored || this._intervalCount < this._intervalCap;
    }
    get _doesConcurrentAllowAnother() {
        return this._pendingCount < this._concurrency;
    }
    _next() {
        this._pendingCount--;
        this._tryToStartAnother();
        this.emit('next');
    }
    _resolvePromises() {
        this._resolveEmpty();
        this._resolveEmpty = empty;
        if (this._pendingCount === 0) {
            this._resolveIdle();
            this._resolveIdle = empty;
            this.emit('idle');
        }
    }
    _onResumeInterval() {
        this._onInterval();
        this._initializeIntervalIfNeeded();
        this._timeoutId = undefined;
    }
    _isIntervalPaused() {
        const now = Date.now();
        if (this._intervalId === undefined) {
            const delay = this._intervalEnd - now;
            if (delay < 0) {
                // Act as the interval was done
                // We don't need to resume it here because it will be resumed on line 160
                this._intervalCount = (this._carryoverConcurrencyCount) ? this._pendingCount : 0;
            }
            else {
                // Act as the interval is pending
                if (this._timeoutId === undefined) {
                    this._timeoutId = setTimeout(() => {
                        this._onResumeInterval();
                    }, delay);
                }
                return true;
            }
        }
        return false;
    }
    _tryToStartAnother() {
        if (this._queue.size === 0) {
            // We can clear the interval ("pause")
            // Because we can redo it later ("resume")
            if (this._intervalId) {
                clearInterval(this._intervalId);
            }
            this._intervalId = undefined;
            this._resolvePromises();
            return false;
        }
        if (!this._isPaused) {
            const canInitializeInterval = !this._isIntervalPaused();
            if (this._doesIntervalAllowAnother && this._doesConcurrentAllowAnother) {
                const job = this._queue.dequeue();
                if (!job) {
                    return false;
                }
                this.emit('active');
                job();
                if (canInitializeInterval) {
                    this._initializeIntervalIfNeeded();
                }
                return true;
            }
        }
        return false;
    }
    _initializeIntervalIfNeeded() {
        if (this._isIntervalIgnored || this._intervalId !== undefined) {
            return;
        }
        this._intervalId = setInterval(() => {
            this._onInterval();
        }, this._interval);
        this._intervalEnd = Date.now() + this._interval;
    }
    _onInterval() {
        if (this._intervalCount === 0 && this._pendingCount === 0 && this._intervalId) {
            clearInterval(this._intervalId);
            this._intervalId = undefined;
        }
        this._intervalCount = this._carryoverConcurrencyCount ? this._pendingCount : 0;
        this._processQueue();
    }
    /**
    Executes all queued functions until it reaches the limit.
    */
    _processQueue() {
        // eslint-disable-next-line no-empty
        while (this._tryToStartAnother()) { }
    }
    get concurrency() {
        return this._concurrency;
    }
    set concurrency(newConcurrency) {
        if (!(typeof newConcurrency === 'number' && newConcurrency >= 1)) {
            throw new TypeError(`Expected \`concurrency\` to be a number from 1 and up, got \`${newConcurrency}\` (${typeof newConcurrency})`);
        }
        this._concurrency = newConcurrency;
        this._processQueue();
    }
    /**
    Adds a sync or async task to the queue. Always returns a promise.
    */
    async add(fn, options = {}) {
        return new Promise((resolve, reject) => {
            const run = async () => {
                this._pendingCount++;
                this._intervalCount++;
                try {
                    const operation = (this._timeout === undefined && options.timeout === undefined) ? fn() : p_timeout_1.default(Promise.resolve(fn()), (options.timeout === undefined ? this._timeout : options.timeout), () => {
                        if (options.throwOnTimeout === undefined ? this._throwOnTimeout : options.throwOnTimeout) {
                            reject(timeoutError);
                        }
                        return undefined;
                    });
                    resolve(await operation);
                }
                catch (error) {
                    reject(error);
                }
                this._next();
            };
            this._queue.enqueue(run, options);
            this._tryToStartAnother();
            this.emit('add');
        });
    }
    /**
    Same as `.add()`, but accepts an array of sync or async functions.

    @returns A promise that resolves when all functions are resolved.
    */
    async addAll(functions, options) {
        return Promise.all(functions.map(async (function_) => this.add(function_, options)));
    }
    /**
    Start (or resume) executing enqueued tasks within concurrency limit. No need to call this if queue is not paused (via `options.autoStart = false` or by `.pause()` method.)
    */
    start() {
        if (!this._isPaused) {
            return this;
        }
        this._isPaused = false;
        this._processQueue();
        return this;
    }
    /**
    Put queue execution on hold.
    */
    pause() {
        this._isPaused = true;
    }
    /**
    Clear the queue.
    */
    clear() {
        this._queue = new this._queueClass();
    }
    /**
    Can be called multiple times. Useful if you for example add additional items at a later time.

    @returns A promise that settles when the queue becomes empty.
    */
    async onEmpty() {
        // Instantly resolve if the queue is empty
        if (this._queue.size === 0) {
            return;
        }
        return new Promise(resolve => {
            const existingResolve = this._resolveEmpty;
            this._resolveEmpty = () => {
                existingResolve();
                resolve();
            };
        });
    }
    /**
    The difference with `.onEmpty` is that `.onIdle` guarantees that all work from the queue has finished. `.onEmpty` merely signals that the queue is empty, but it could mean that some promises haven't completed yet.

    @returns A promise that settles when the queue becomes empty, and all promises have completed; `queue.size === 0 && queue.pending === 0`.
    */
    async onIdle() {
        // Instantly resolve if none pending and if nothing else is queued
        if (this._pendingCount === 0 && this._queue.size === 0) {
            return;
        }
        return new Promise(resolve => {
            const existingResolve = this._resolveIdle;
            this._resolveIdle = () => {
                existingResolve();
                resolve();
            };
        });
    }
    /**
    Size of the queue.
    */
    get size() {
        return this._queue.size;
    }
    /**
    Size of the queue, filtered by the given options.

    For example, this can be used to find the number of items remaining in the queue with a specific priority level.
    */
    sizeBy(options) {
        // eslint-disable-next-line unicorn/no-fn-reference-in-iterator
        return this._queue.filter(options).length;
    }
    /**
    Number of pending promises.
    */
    get pending() {
        return this._pendingCount;
    }
    /**
    Whether the queue is currently paused.
    */
    get isPaused() {
        return this._isPaused;
    }
    get timeout() {
        return this._timeout;
    }
    /**
    Set the timeout for future operations.
    */
    set timeout(milliseconds) {
        this._timeout = milliseconds;
    }
}
exports.Z = PQueue;


/***/ }),

/***/ 918:
/***/ ((__unused_webpack_module, exports) => {

"use strict";

Object.defineProperty(exports, "__esModule", ({ value: true }));
// Port of lower_bound from https://en.cppreference.com/w/cpp/algorithm/lower_bound
// Used to compute insertion index to keep queue sorted after insertion
function lowerBound(array, value, comparator) {
    let first = 0;
    let count = array.length;
    while (count > 0) {
        const step = (count / 2) | 0;
        let it = first + step;
        if (comparator(array[it], value) <= 0) {
            first = ++it;
            count -= step + 1;
        }
        else {
            count = step;
        }
    }
    return first;
}
exports["default"] = lowerBound;


/***/ }),

/***/ 906:
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

Object.defineProperty(exports, "__esModule", ({ value: true }));
const lower_bound_1 = __webpack_require__(918);
class PriorityQueue {
    constructor() {
        this._queue = [];
    }
    enqueue(run, options) {
        options = Object.assign({ priority: 0 }, options);
        const element = {
            priority: options.priority,
            run
        };
        if (this.size && this._queue[this.size - 1].priority >= options.priority) {
            this._queue.push(element);
            return;
        }
        const index = lower_bound_1.default(this._queue, element, (a, b) => b.priority - a.priority);
        this._queue.splice(index, 0, element);
    }
    dequeue() {
        const item = this._queue.shift();
        return item === null || item === void 0 ? void 0 : item.run;
    }
    filter(options) {
        return this._queue.filter((element) => element.priority === options.priority).map((element) => element.run);
    }
    get size() {
        return this._queue.length;
    }
}
exports["default"] = PriorityQueue;


/***/ }),

/***/ 499:
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


const pFinally = __webpack_require__(306);

class TimeoutError extends Error {
	constructor(message) {
		super(message);
		this.name = 'TimeoutError';
	}
}

const pTimeout = (promise, milliseconds, fallback) => new Promise((resolve, reject) => {
	if (typeof milliseconds !== 'number' || milliseconds < 0) {
		throw new TypeError('Expected `milliseconds` to be a positive number');
	}

	if (milliseconds === Infinity) {
		resolve(promise);
		return;
	}

	const timer = setTimeout(() => {
		if (typeof fallback === 'function') {
			try {
				resolve(fallback());
			} catch (error) {
				reject(error);
			}

			return;
		}

		const message = typeof fallback === 'string' ? fallback : `Promise timed out after ${milliseconds} milliseconds`;
		const timeoutError = fallback instanceof Error ? fallback : new TimeoutError(message);

		if (typeof promise.cancel === 'function') {
			promise.cancel();
		}

		reject(timeoutError);
	}, milliseconds);

	// TODO: Use native `finally` keyword when targeting Node.js 10
	pFinally(
		// eslint-disable-next-line promise/prefer-await-to-then
		promise.then(resolve, reject),
		() => {
			clearTimeout(timer);
		}
	);
});

module.exports = pTimeout;
// TODO: Remove this for the next major release
module.exports["default"] = pTimeout;

module.exports.TimeoutError = TimeoutError;


/***/ }),

/***/ 203:
/***/ ((module) => {

"use strict";
module.exports = __WEBPACK_EXTERNAL_MODULE__203__;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/publicPath */
/******/ 	(() => {
/******/ 		__webpack_require__.p = "";
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ entry_lib)
});

// NAMESPACE OBJECT: ../../../../../../../../node_modules/axios/lib/platform/common/utils.js
var common_utils_namespaceObject = {};
__webpack_require__.r(common_utils_namespaceObject);
__webpack_require__.d(common_utils_namespaceObject, {
  hasBrowserEnv: () => (hasBrowserEnv),
  hasStandardBrowserEnv: () => (hasStandardBrowserEnv),
  hasStandardBrowserWebWorkerEnv: () => (hasStandardBrowserWebWorkerEnv)
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
/* eslint-disable no-var */
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var currentScript = window.document.currentScript
  if (false) { var getCurrentScript; }

  var src = currentScript && currentScript.src.match(/(.+\/)[^/]+\.js(\?.*)?$/)
  if (src) {
    __webpack_require__.p = src[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ const setPublicPath = (null);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=template&id=79e0346f&scoped=true
var render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.presence)?_c('div',[_c('div',{staticClass:"u-flex u-flex-wrap u-gap-small-3x"},[_c('div',{staticClass:"presence-builder"},[_c('b-table',{ref:"builder",staticClass:"mod-presence mod-builder",class:{'is-enabled': _vm.isEditEnabled},attrs:{"bordered":"","items":_vm.presenceStatuses,"fields":_vm.fields,"tbody-tr-class":_vm.rowClass,"selectable":_vm.isEditEnabled,"select-mode":"single","selected-variant":""},on:{"row-selected":_vm.onRowSelected},scopedSlots:_vm._u([{key:"thead-top",fn:function(){return [_c('selection-preview',{staticClass:"presence-preview-row",attrs:{"presence-statuses":_vm.presenceStatuses}})]},proxy:true},{key:"head(label)",fn:function(){return [_vm._v(_vm._s(_vm.$t('label')))]},proxy:true},{key:"head(title)",fn:function(){return [_vm._v(_vm._s(_vm.$t('title')))]},proxy:true},{key:"head(aliasses)",fn:function(){return [_vm._v(_vm._s(_vm.$t('aliasses')))]},proxy:true},{key:"head(color)",fn:function(){return [_vm._v(_vm._s(_vm.$t('color')))]},proxy:true},{key:"cell(label)",fn:function({item, index}){return [_c('b-input',{staticClass:"mod-input mod-trans mod-pad mod-small",attrs:{"type":"text","required":"","autocomplete":"off","disabled":_vm.isEditDisabled},on:{"focus":function($event){return _vm.onSelectStatus(item, index)}},model:{value:(item.code),callback:function ($$v) {_vm.$set(item, "code", $$v)},expression:"item.code"}})]}},{key:"cell(title)",fn:function({item, index}){return [_c('title-control',{attrs:{"status":item,"status-title":_vm.getStatusTitle(item),"is-editable":_vm.isStatusEditable(item),"disabled":_vm.isEditDisabled},on:{"select":function($event){return _vm.onSelectStatus(item, index)}}})]}},{key:"cell(aliasses)",fn:function({item, index}){return [_c('alias-control',{attrs:{"status":item,"alias-title":_vm.getAliasedTitle(item),"fixed-status-defaults":_vm.fixedStatusDefaults,"is-editable":_vm.isStatusEditable(item),"is-select-disabled":_vm.isEditDisabled},on:{"select":function($event){return _vm.onSelectStatus(item, index)}}})]}},{key:"cell(color)",fn:function({item, index}){return [_c('color-control',{staticClass:"u-flex u-align-items-center",attrs:{"id":index,"disabled":_vm.isEditDisabled,"color":item.color,"selected":item === _vm.selectedStatus},on:{"select":function($event){return _vm.onSelectStatus(item, index)},"color-selected":function($event){return _vm.setStatusColor(item, $event)}}})]}},{key:"cell(actions)",fn:function({item, index}){return [_c('selection-controls',{staticClass:"u-flex u-gap-small presence-actions",attrs:{"id":item.id,"is-up-disabled":_vm.isEditDisabled || index === 0,"is-down-disabled":_vm.isEditDisabled || index >= _vm.presenceStatuses.length - 1,"is-remove-disabled":_vm.isEditDisabled || item.type === 'fixed' || _vm.savedEntryStatuses.includes(item.id)},on:{"move-down":function($event){return _vm.onMoveDown(item.id, index)},"move-up":function($event){return _vm.onMoveUp(item.id, index)},"remove":function($event){return _vm.onRemove(item)},"select":function($event){return _vm.onSelectStatus(item, index)}}})]}},(_vm.createNew)?{key:"bottom-row",fn:function(){return [_c('b-td',[_c('b-input',{staticClass:"mod-input mod-pad mod-small",attrs:{"required":"","type":"text","id":"new-presence-code"},model:{value:(_vm.codeNew),callback:function ($$v) {_vm.codeNew=$$v},expression:"codeNew"}})],1),_c('b-td',[_c('b-input',{staticClass:"mod-input mod-pad",attrs:{"required":"","type":"text"},model:{value:(_vm.titleNew),callback:function ($$v) {_vm.titleNew=$$v},expression:"titleNew"}})],1),_c('b-td',[_c('alias-control',{attrs:{"status":_vm.aliasNew,"fixed-status-defaults":_vm.fixedStatusDefaults}})],1),_c('b-td',[_c('color-control',{staticClass:"u-flex u-align-items-center",attrs:{"id":"999","color":_vm.colorNew},on:{"color-selected":function($event){_vm.colorNew = $event}}})],1),_c('b-td',{staticClass:"table-actions"},[_c('new-status-controls',{staticClass:"u-flex u-gap-small presence-actions",attrs:{"isSavingDisabled":!(_vm.codeNew && _vm.titleNew && _vm.aliasNew.aliasses > 0)},on:{"save":_vm.onSaveNew,"cancel":_vm.onCancelNew}})],1)]},proxy:true}:null],null,true)}),(!_vm.createNew)?_c('div',{staticClass:"m-new"},[_c('button',{staticClass:"btn-new-status u-text-no-underline",on:{"click":_vm.onCreateNew}},[_c('i',{staticClass:"fa fa-plus",attrs:{"aria-hidden":"true"}}),_vm._v(" "+_vm._s(_vm.$t('new-presence-status')))])]):_vm._e(),(_vm.errorData)?_c('error-display',{on:{"close":function($event){_vm.errorData = null}}},[_c('error-message',{attrs:{"error-data":_vm.errorData}})],1):_vm._e()],1),_c('div',{staticClass:"u-align-self-start"},[_c('div',{staticStyle:{"margin-bottom":"15px"}},[_c('on-off-switch',{staticStyle:{"width":"136px"},attrs:{"id":"allow-checkout","checked":_vm.presence.has_checkout,"on-text":_vm.$t('checkout'),"off-text":_vm.$t('no-checkout'),"switch-class":"mod-checkout-choice"},on:{"toggle":function($event){_vm.presence.has_checkout = !_vm.presence.has_checkout}}})],1),_c('div',{style:(_vm.useVerificationIcon ? 'margin-bottom: 10px' : '')},[_c('button',{staticClass:"btn-check",class:{ 'checked': _vm.useVerificationIcon },attrs:{"aria-pressed":_vm.useVerificationIcon ? 'true' : 'false',"aria-expanded":_vm.useVerificationIcon ? 'true' : 'false'},on:{"click":function($event){_vm.useVerificationIcon = !_vm.useVerificationIcon}}},[_c('span',{staticClass:"lbl-check",attrs:{"tabindex":"-1"}},[_c('i',{staticClass:"btn-icon-check fa",attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('verification-icon')))])])]),_c('verification-icon',{directives:[{name:"show",rawName:"v-show",value:(_vm.useVerificationIcon),expression:"useVerificationIcon"}],ref:"verification-icon",attrs:{"icon-data":_vm.presence.verification_icon_data || null,"use-builder":true}})],1)]),_c('div',{staticClass:"m-save"},[_c('save-control',{attrs:{"is-disabled":_vm.isEditDisabled,"is-saving":_vm.isSaving},on:{"save":function($event){return _vm.onSave()}}})],1)]):_vm._e()
}
var staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/tslib/tslib.es6.mjs
/******************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise, SuppressedError, Symbol */

var extendStatics = function(d, b) {
  extendStatics = Object.setPrototypeOf ||
      ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
      function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
  return extendStatics(d, b);
};

function __extends(d, b) {
  if (typeof b !== "function" && b !== null)
      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
  extendStatics(d, b);
  function __() { this.constructor = d; }
  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
  __assign = Object.assign || function __assign(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
  }
  return __assign.apply(this, arguments);
}

function __rest(s, e) {
  var t = {};
  for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
      t[p] = s[p];
  if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
          if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
              t[p[i]] = s[p[i]];
      }
  return t;
}

function __decorate(decorators, target, key, desc) {
  var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
  if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
  else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
  return function (target, key) { decorator(target, key, paramIndex); }
}

function __esDecorate(ctor, descriptorIn, decorators, contextIn, initializers, extraInitializers) {
  function accept(f) { if (f !== void 0 && typeof f !== "function") throw new TypeError("Function expected"); return f; }
  var kind = contextIn.kind, key = kind === "getter" ? "get" : kind === "setter" ? "set" : "value";
  var target = !descriptorIn && ctor ? contextIn["static"] ? ctor : ctor.prototype : null;
  var descriptor = descriptorIn || (target ? Object.getOwnPropertyDescriptor(target, contextIn.name) : {});
  var _, done = false;
  for (var i = decorators.length - 1; i >= 0; i--) {
      var context = {};
      for (var p in contextIn) context[p] = p === "access" ? {} : contextIn[p];
      for (var p in contextIn.access) context.access[p] = contextIn.access[p];
      context.addInitializer = function (f) { if (done) throw new TypeError("Cannot add initializers after decoration has completed"); extraInitializers.push(accept(f || null)); };
      var result = (0, decorators[i])(kind === "accessor" ? { get: descriptor.get, set: descriptor.set } : descriptor[key], context);
      if (kind === "accessor") {
          if (result === void 0) continue;
          if (result === null || typeof result !== "object") throw new TypeError("Object expected");
          if (_ = accept(result.get)) descriptor.get = _;
          if (_ = accept(result.set)) descriptor.set = _;
          if (_ = accept(result.init)) initializers.unshift(_);
      }
      else if (_ = accept(result)) {
          if (kind === "field") initializers.unshift(_);
          else descriptor[key] = _;
      }
  }
  if (target) Object.defineProperty(target, contextIn.name, descriptor);
  done = true;
};

function __runInitializers(thisArg, initializers, value) {
  var useValue = arguments.length > 2;
  for (var i = 0; i < initializers.length; i++) {
      value = useValue ? initializers[i].call(thisArg, value) : initializers[i].call(thisArg);
  }
  return useValue ? value : void 0;
};

function __propKey(x) {
  return typeof x === "symbol" ? x : "".concat(x);
};

function __setFunctionName(f, name, prefix) {
  if (typeof name === "symbol") name = name.description ? "[".concat(name.description, "]") : "";
  return Object.defineProperty(f, "name", { configurable: true, value: prefix ? "".concat(prefix, " ", name) : name });
};

function __metadata(metadataKey, metadataValue) {
  if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
  function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
  return new (P || (P = Promise))(function (resolve, reject) {
      function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
      function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
      function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
      step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
}

function __generator(thisArg, body) {
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
  return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
  function verb(n) { return function (v) { return step([n, v]); }; }
  function step(op) {
      if (f) throw new TypeError("Generator is already executing.");
      while (g && (g = 0, op[0] && (_ = 0)), _) try {
          if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
          if (y = 0, t) op = [op[0] & 2, t.value];
          switch (op[0]) {
              case 0: case 1: t = op; break;
              case 4: _.label++; return { value: op[1], done: false };
              case 5: _.label++; y = op[1]; op = [0]; continue;
              case 7: op = _.ops.pop(); _.trys.pop(); continue;
              default:
                  if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                  if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                  if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                  if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                  if (t[2]) _.ops.pop();
                  _.trys.pop(); continue;
          }
          op = body.call(thisArg, _);
      } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
      if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
  }
}

var __createBinding = Object.create ? (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  var desc = Object.getOwnPropertyDescriptor(m, k);
  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
  }
  Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  o[k2] = m[k];
});

function __exportStar(m, o) {
  for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(o, p)) __createBinding(o, m, p);
}

function __values(o) {
  var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
  if (m) return m.call(o);
  if (o && typeof o.length === "number") return {
      next: function () {
          if (o && i >= o.length) o = void 0;
          return { value: o && o[i++], done: !o };
      }
  };
  throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
  var m = typeof Symbol === "function" && o[Symbol.iterator];
  if (!m) return o;
  var i = m.call(o), r, ar = [], e;
  try {
      while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
  }
  catch (error) { e = { error: error }; }
  finally {
      try {
          if (r && !r.done && (m = i["return"])) m.call(i);
      }
      finally { if (e) throw e.error; }
  }
  return ar;
}

/** @deprecated */
function __spread() {
  for (var ar = [], i = 0; i < arguments.length; i++)
      ar = ar.concat(__read(arguments[i]));
  return ar;
}

/** @deprecated */
function __spreadArrays() {
  for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
  for (var r = Array(s), k = 0, i = 0; i < il; i++)
      for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
  return r;
}

function __spreadArray(to, from, pack) {
  if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
      }
  }
  return to.concat(ar || Array.prototype.slice.call(from));
}

function __await(v) {
  return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var g = generator.apply(thisArg, _arguments || []), i, q = [];
  return i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i;
  function verb(n) { if (g[n]) i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; }
  function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
  function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
  function fulfill(value) { resume("next", value); }
  function reject(value) { resume("throw", value); }
  function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
  var i, p;
  return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
  function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: false } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var m = o[Symbol.asyncIterator], i;
  return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
  function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
  function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
  if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
  return cooked;
};

var __setModuleDefault = Object.create ? (function(o, v) {
  Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
  o["default"] = v;
};

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
  __setModuleDefault(result, mod);
  return result;
}

function __importDefault(mod) {
  return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, state, kind, f) {
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
  return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
}

function __classPrivateFieldSet(receiver, state, value, kind, f) {
  if (kind === "m") throw new TypeError("Private method is not writable");
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
  return (kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value)), value;
}

function __classPrivateFieldIn(state, receiver) {
  if (receiver === null || (typeof receiver !== "object" && typeof receiver !== "function")) throw new TypeError("Cannot use 'in' operator on non-object");
  return typeof state === "function" ? receiver === state : state.has(receiver);
}

function __addDisposableResource(env, value, async) {
  if (value !== null && value !== void 0) {
    if (typeof value !== "object" && typeof value !== "function") throw new TypeError("Object expected.");
    var dispose;
    if (async) {
        if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
        dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
        if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
        dispose = value[Symbol.dispose];
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
    env.stack.push({ value: value, dispose: dispose, async: async });
  }
  else if (async) {
    env.stack.push({ async: true });
  }
  return value;
}

var _SuppressedError = typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
  var e = new Error(message);
  return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
};

function __disposeResources(env) {
  function fail(e) {
    env.error = env.hasError ? new _SuppressedError(e, env.error, "An error was suppressed during disposal.") : e;
    env.hasError = true;
  }
  function next() {
    while (env.stack.length) {
      var rec = env.stack.pop();
      try {
        var result = rec.dispose && rec.dispose.call(rec.value);
        if (rec.async) return Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
      }
      catch (e) {
          fail(e);
      }
    }
    if (env.hasError) throw env.error;
  }
  return next();
}

/* harmony default export */ const tslib_es6 = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
  __metadata,
  __awaiter,
  __generator,
  __createBinding,
  __exportStar,
  __values,
  __read,
  __spread,
  __spreadArrays,
  __spreadArray,
  __await,
  __asyncGenerator,
  __asyncDelegator,
  __asyncValues,
  __makeTemplateObject,
  __importStar,
  __importDefault,
  __classPrivateFieldGet,
  __classPrivateFieldSet,
  __classPrivateFieldIn,
  __addDisposableResource,
  __disposeResources,
});

// EXTERNAL MODULE: external {"commonjs":"vue","commonjs2":"vue","root":"Vue"}
var external_commonjs_vue_commonjs2_vue_root_Vue_ = __webpack_require__(203);
var external_commonjs_vue_commonjs2_vue_root_Vue_default = /*#__PURE__*/__webpack_require__.n(external_commonjs_vue_commonjs2_vue_root_Vue_);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-class-component/dist/vue-class-component.esm.js
/**
  * vue-class-component v7.2.6
  * (c) 2015-present Evan You
  * @license MIT
  */


function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) arr2[i] = arr[i];

    return arr2;
  }
}

function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}

// The rational behind the verbose Reflect-feature check below is the fact that there are polyfills
// which add an implementation for Reflect.defineMetadata but not for Reflect.getOwnMetadataKeys.
// Without this check consumers will encounter hard to track down runtime errors.
function reflectionIsSupported() {
  return typeof Reflect !== 'undefined' && Reflect.defineMetadata && Reflect.getOwnMetadataKeys;
}
function copyReflectionMetadata(to, from) {
  forwardMetadata(to, from);
  Object.getOwnPropertyNames(from.prototype).forEach(function (key) {
    forwardMetadata(to.prototype, from.prototype, key);
  });
  Object.getOwnPropertyNames(from).forEach(function (key) {
    forwardMetadata(to, from, key);
  });
}

function forwardMetadata(to, from, propertyKey) {
  var metaKeys = propertyKey ? Reflect.getOwnMetadataKeys(from, propertyKey) : Reflect.getOwnMetadataKeys(from);
  metaKeys.forEach(function (metaKey) {
    var metadata = propertyKey ? Reflect.getOwnMetadata(metaKey, from, propertyKey) : Reflect.getOwnMetadata(metaKey, from);

    if (propertyKey) {
      Reflect.defineMetadata(metaKey, metadata, to, propertyKey);
    } else {
      Reflect.defineMetadata(metaKey, metadata, to);
    }
  });
}

var fakeArray = {
  __proto__: []
};
var hasProto = fakeArray instanceof Array;
function vue_class_component_esm_createDecorator(factory) {
  return function (target, key, index) {
    var Ctor = typeof target === 'function' ? target : target.constructor;

    if (!Ctor.__decorators__) {
      Ctor.__decorators__ = [];
    }

    if (typeof index !== 'number') {
      index = undefined;
    }

    Ctor.__decorators__.push(function (options) {
      return factory(options, key, index);
    });
  };
}
function mixins() {
  for (var _len = arguments.length, Ctors = new Array(_len), _key = 0; _key < _len; _key++) {
    Ctors[_key] = arguments[_key];
  }

  return Vue.extend({
    mixins: Ctors
  });
}
function isPrimitive(value) {
  var type = _typeof(value);

  return value == null || type !== 'object' && type !== 'function';
}
function warn(message) {
  if (typeof console !== 'undefined') {
    console.warn('[vue-class-component] ' + message);
  }
}

function collectDataFromConstructor(vm, Component) {
  // override _init to prevent to init as Vue instance
  var originalInit = Component.prototype._init;

  Component.prototype._init = function () {
    var _this = this;

    // proxy to actual vm
    var keys = Object.getOwnPropertyNames(vm); // 2.2.0 compat (props are no longer exposed as self properties)

    if (vm.$options.props) {
      for (var key in vm.$options.props) {
        if (!vm.hasOwnProperty(key)) {
          keys.push(key);
        }
      }
    }

    keys.forEach(function (key) {
      Object.defineProperty(_this, key, {
        get: function get() {
          return vm[key];
        },
        set: function set(value) {
          vm[key] = value;
        },
        configurable: true
      });
    });
  }; // should be acquired class property values


  var data = new Component(); // restore original _init to avoid memory leak (#209)

  Component.prototype._init = originalInit; // create plain data object

  var plainData = {};
  Object.keys(data).forEach(function (key) {
    if (data[key] !== undefined) {
      plainData[key] = data[key];
    }
  });

  if (false) {}

  return plainData;
}

var $internalHooks = ['data', 'beforeCreate', 'created', 'beforeMount', 'mounted', 'beforeDestroy', 'destroyed', 'beforeUpdate', 'updated', 'activated', 'deactivated', 'render', 'errorCaptured', 'serverPrefetch' // 2.6
];
function componentFactory(Component) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  options.name = options.name || Component._componentTag || Component.name; // prototype props.

  var proto = Component.prototype;
  Object.getOwnPropertyNames(proto).forEach(function (key) {
    if (key === 'constructor') {
      return;
    } // hooks


    if ($internalHooks.indexOf(key) > -1) {
      options[key] = proto[key];
      return;
    }

    var descriptor = Object.getOwnPropertyDescriptor(proto, key);

    if (descriptor.value !== void 0) {
      // methods
      if (typeof descriptor.value === 'function') {
        (options.methods || (options.methods = {}))[key] = descriptor.value;
      } else {
        // typescript decorated data
        (options.mixins || (options.mixins = [])).push({
          data: function data() {
            return _defineProperty({}, key, descriptor.value);
          }
        });
      }
    } else if (descriptor.get || descriptor.set) {
      // computed properties
      (options.computed || (options.computed = {}))[key] = {
        get: descriptor.get,
        set: descriptor.set
      };
    }
  });
  (options.mixins || (options.mixins = [])).push({
    data: function data() {
      return collectDataFromConstructor(this, Component);
    }
  }); // decorate options

  var decorators = Component.__decorators__;

  if (decorators) {
    decorators.forEach(function (fn) {
      return fn(options);
    });
    delete Component.__decorators__;
  } // find super


  var superProto = Object.getPrototypeOf(Component.prototype);
  var Super = superProto instanceof (external_commonjs_vue_commonjs2_vue_root_Vue_default()) ? superProto.constructor : (external_commonjs_vue_commonjs2_vue_root_Vue_default());
  var Extended = Super.extend(options);
  forwardStaticMembers(Extended, Component, Super);

  if (reflectionIsSupported()) {
    copyReflectionMetadata(Extended, Component);
  }

  return Extended;
}
var reservedPropertyNames = (/* unused pure expression or super */ null && ([// Unique id
'cid', // Super Vue constructor
'super', // Component options that will be used by the component
'options', 'superOptions', 'extendOptions', 'sealedOptions', // Private assets
'component', 'directive', 'filter']));
var shouldIgnore = {
  prototype: true,
  arguments: true,
  callee: true,
  caller: true
};

function forwardStaticMembers(Extended, Original, Super) {
  // We have to use getOwnPropertyNames since Babel registers methods as non-enumerable
  Object.getOwnPropertyNames(Original).forEach(function (key) {
    // Skip the properties that should not be overwritten
    if (shouldIgnore[key]) {
      return;
    } // Some browsers does not allow reconfigure built-in properties


    var extendedDescriptor = Object.getOwnPropertyDescriptor(Extended, key);

    if (extendedDescriptor && !extendedDescriptor.configurable) {
      return;
    }

    var descriptor = Object.getOwnPropertyDescriptor(Original, key); // If the user agent does not support `__proto__` or its family (IE <= 10),
    // the sub class properties may be inherited properties from the super class in TypeScript.
    // We need to exclude such properties to prevent to overwrite
    // the component options object which stored on the extended constructor (See #192).
    // If the value is a referenced value (object or function),
    // we can check equality of them and exclude it if they have the same reference.
    // If it is a primitive value, it will be forwarded for safety.

    if (!hasProto) {
      // Only `cid` is explicitly exluded from property forwarding
      // because we cannot detect whether it is a inherited property or not
      // on the no `__proto__` environment even though the property is reserved.
      if (key === 'cid') {
        return;
      }

      var superDescriptor = Object.getOwnPropertyDescriptor(Super, key);

      if (!isPrimitive(descriptor.value) && superDescriptor && superDescriptor.value === descriptor.value) {
        return;
      }
    } // Warn if the users manually declare reserved properties


    if (false) {}

    Object.defineProperty(Extended, key, descriptor);
  });
}

function Component(options) {
  if (typeof options === 'function') {
    return componentFactory(options);
  }

  return function (Component) {
    return componentFactory(Component, options);
  };
}

Component.registerHooks = function registerHooks(keys) {
  $internalHooks.push.apply($internalHooks, _toConsumableArray(keys));
};

/* harmony default export */ const vue_class_component_esm = (Component);


;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/decorators/Emit.js
var Emit_spreadArrays = (undefined && undefined.__spreadArrays) || function () {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};
// Code copied from Vue/src/shared/util.js
var hyphenateRE = /\B([A-Z])/g;
var hyphenate = function (str) { return str.replace(hyphenateRE, '-$1').toLowerCase(); };
/**
 * decorator of an event-emitter function
 * @param  event The name of the event
 * @return MethodDecorator
 */
function Emit(event) {
    return function (_target, propertyKey, descriptor) {
        var key = hyphenate(propertyKey);
        var original = descriptor.value;
        descriptor.value = function emitter() {
            var _this = this;
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var emit = function (returnValue) {
                var emitName = event || key;
                if (returnValue === undefined) {
                    if (args.length === 0) {
                        _this.$emit(emitName);
                    }
                    else if (args.length === 1) {
                        _this.$emit(emitName, args[0]);
                    }
                    else {
                        _this.$emit.apply(_this, Emit_spreadArrays([emitName], args));
                    }
                }
                else {
                    args.unshift(returnValue);
                    _this.$emit.apply(_this, Emit_spreadArrays([emitName], args));
                }
            };
            var returnValue = original.apply(this, args);
            if (isPromise(returnValue)) {
                returnValue.then(emit);
            }
            else {
                emit(returnValue);
            }
            return returnValue;
        };
    };
}
function isPromise(obj) {
    return obj instanceof Promise || (obj && typeof obj.then === 'function');
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/helpers/metadata.js
/** @see {@link https://github.com/vuejs/vue-class-component/blob/master/src/reflect.ts} */
var reflectMetadataIsSupported = typeof Reflect !== 'undefined' && typeof Reflect.getMetadata !== 'undefined';
function metadata_applyMetadata(options, target, key) {
    if (reflectMetadataIsSupported) {
        if (!Array.isArray(options) &&
            typeof options !== 'function' &&
            !options.hasOwnProperty('type') &&
            typeof options.type === 'undefined') {
            var type = Reflect.getMetadata('design:type', target, key);
            if (type !== Object) {
                options.type = type;
            }
        }
    }
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/decorators/Model.js


/**
 * decorator of model
 * @param  event event name
 * @param options options
 * @return PropertyDecorator
 */
function Model(event, options) {
    if (options === void 0) { options = {}; }
    return function (target, key) {
        applyMetadata(options, target, key);
        createDecorator(function (componentOptions, k) {
            ;
            (componentOptions.props || (componentOptions.props = {}))[k] = options;
            componentOptions.model = { prop: k, event: event || k };
        })(target, key);
    };
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/decorators/ModelSync.js


/**
 * decorator of synced model and prop
 * @param propName the name to interface with from outside, must be different from decorated property
 * @param  event event name
 * @param options options
 * @return PropertyDecorator
 */
function ModelSync(propName, event, options) {
    if (options === void 0) { options = {}; }
    return function (target, key) {
        applyMetadata(options, target, key);
        createDecorator(function (componentOptions, k) {
            ;
            (componentOptions.props || (componentOptions.props = {}))[propName] = options;
            componentOptions.model = { prop: propName, event: event || k };
            (componentOptions.computed || (componentOptions.computed = {}))[k] = {
                get: function () {
                    return this[propName];
                },
                set: function (value) {
                    // @ts-ignore
                    this.$emit(event, value);
                },
            };
        })(target, key);
    };
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/decorators/Prop.js


/**
 * decorator of a prop
 * @param  options the options for the prop
 * @return PropertyDecorator | void
 */
function Prop(options) {
    if (options === void 0) { options = {}; }
    return function (target, key) {
        metadata_applyMetadata(options, target, key);
        vue_class_component_esm_createDecorator(function (componentOptions, k) {
            ;
            (componentOptions.props || (componentOptions.props = {}))[k] = options;
        })(target, key);
    };
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/decorators/PropSync.js


/**
 * decorator of a synced prop
 * @param propName the name to interface with from outside, must be different from decorated property
 * @param options the options for the synced prop
 * @return PropertyDecorator | void
 */
function PropSync(propName, options) {
    if (options === void 0) { options = {}; }
    return function (target, key) {
        applyMetadata(options, target, key);
        createDecorator(function (componentOptions, k) {
            ;
            (componentOptions.props || (componentOptions.props = {}))[propName] = options;
            (componentOptions.computed || (componentOptions.computed = {}))[k] = {
                get: function () {
                    return this[propName];
                },
                set: function (value) {
                    this.$emit("update:" + propName, value);
                },
            };
        })(target, key);
    };
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/decorators/Watch.js

/**
 * decorator of a watch function
 * @param  path the path or the expression to observe
 * @param  WatchOption
 * @return MethodDecorator
 */
function Watch(path, options) {
    if (options === void 0) { options = {}; }
    var _a = options.deep, deep = _a === void 0 ? false : _a, _b = options.immediate, immediate = _b === void 0 ? false : _b;
    return vue_class_component_esm_createDecorator(function (componentOptions, handler) {
        if (typeof componentOptions.watch !== 'object') {
            componentOptions.watch = Object.create(null);
        }
        var watch = componentOptions.watch;
        if (typeof watch[path] === 'object' && !Array.isArray(watch[path])) {
            watch[path] = [watch[path]];
        }
        else if (typeof watch[path] === 'undefined') {
            watch[path] = [];
        }
        watch[path].push({ handler: handler, deep: deep, immediate: immediate });
    });
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/vue-property-decorator/lib/index.js
/** vue-property-decorator verson 9.1.2 MIT LICENSE copyright 2020 kaorun343 */
/// <reference types='reflect-metadata'/>
















;// CONCATENATED MODULE: ./src/connect/APIConfig.ts
class APIConfig {
    loadPresenceEntriesURL = '';
    loadStatisticsURL = '';
    loadPresenceURL = '';
    updatePresenceURL = '';
    updatePresenceGlobalSelfRegistrationURL = '';
    savePresenceEntryURL = '';
    bulkSavePresenceEntriesURL = '';
    createPresencePeriodURL = '';
    updatePresencePeriodURL = '';
    deletePresencePeriodURL = '';
    loadRegisteredPresenceEntryStatusesURL = '';
    togglePresenceEntryCheckoutURL = '';
    printQrCodeURL = '';
    exportURL = '';
    csrfToken = '';
    constructor(config) {
        Object.assign(this, config);
    }
    static from(config) {
        return new APIConfig(config);
    }
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/bind.js


function bind(fn, thisArg) {
  return function wrap() {
    return fn.apply(thisArg, arguments);
  };
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/utils.js




// utils is a library of generic helper functions non-specific to axios

const {toString: utils_toString} = Object.prototype;
const {getPrototypeOf} = Object;

const kindOf = (cache => thing => {
    const str = utils_toString.call(thing);
    return cache[str] || (cache[str] = str.slice(8, -1).toLowerCase());
})(Object.create(null));

const kindOfTest = (type) => {
  type = type.toLowerCase();
  return (thing) => kindOf(thing) === type
}

const typeOfTest = type => thing => typeof thing === type;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 *
 * @returns {boolean} True if value is an Array, otherwise false
 */
const {isArray} = Array;

/**
 * Determine if a value is undefined
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if the value is undefined, otherwise false
 */
const isUndefined = typeOfTest('undefined');

/**
 * Determine if a value is a Buffer
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
  return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
    && isFunction(val.constructor.isBuffer) && val.constructor.isBuffer(val);
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
const isArrayBuffer = kindOfTest('ArrayBuffer');


/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  let result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (isArrayBuffer(val.buffer));
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a String, otherwise false
 */
const isString = typeOfTest('string');

/**
 * Determine if a value is a Function
 *
 * @param {*} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
const isFunction = typeOfTest('function');

/**
 * Determine if a value is a Number
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a Number, otherwise false
 */
const isNumber = typeOfTest('number');

/**
 * Determine if a value is an Object
 *
 * @param {*} thing The value to test
 *
 * @returns {boolean} True if value is an Object, otherwise false
 */
const isObject = (thing) => thing !== null && typeof thing === 'object';

/**
 * Determine if a value is a Boolean
 *
 * @param {*} thing The value to test
 * @returns {boolean} True if value is a Boolean, otherwise false
 */
const isBoolean = thing => thing === true || thing === false;

/**
 * Determine if a value is a plain Object
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a plain Object, otherwise false
 */
const isPlainObject = (val) => {
  if (kindOf(val) !== 'object') {
    return false;
  }

  const prototype = getPrototypeOf(val);
  return (prototype === null || prototype === Object.prototype || Object.getPrototypeOf(prototype) === null) && !(Symbol.toStringTag in val) && !(Symbol.iterator in val);
}

/**
 * Determine if a value is a Date
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a Date, otherwise false
 */
const isDate = kindOfTest('Date');

/**
 * Determine if a value is a File
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a File, otherwise false
 */
const isFile = kindOfTest('File');

/**
 * Determine if a value is a Blob
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a Blob, otherwise false
 */
const isBlob = kindOfTest('Blob');

/**
 * Determine if a value is a FileList
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a File, otherwise false
 */
const isFileList = kindOfTest('FileList');

/**
 * Determine if a value is a Stream
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a Stream, otherwise false
 */
const isStream = (val) => isObject(val) && isFunction(val.pipe);

/**
 * Determine if a value is a FormData
 *
 * @param {*} thing The value to test
 *
 * @returns {boolean} True if value is an FormData, otherwise false
 */
const isFormData = (thing) => {
  let kind;
  return thing && (
    (typeof FormData === 'function' && thing instanceof FormData) || (
      isFunction(thing.append) && (
        (kind = kindOf(thing)) === 'formdata' ||
        // detect form-data instance
        (kind === 'object' && isFunction(thing.toString) && thing.toString() === '[object FormData]')
      )
    )
  )
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
const isURLSearchParams = kindOfTest('URLSearchParams');

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 *
 * @returns {String} The String freed of excess whitespace
 */
const trim = (str) => str.trim ?
  str.trim() : str.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 *
 * @param {Boolean} [allOwnKeys = false]
 * @returns {any}
 */
function forEach(obj, fn, {allOwnKeys = false} = {}) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  let i;
  let l;

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    const keys = allOwnKeys ? Object.getOwnPropertyNames(obj) : Object.keys(obj);
    const len = keys.length;
    let key;

    for (i = 0; i < len; i++) {
      key = keys[i];
      fn.call(null, obj[key], key, obj);
    }
  }
}

function findKey(obj, key) {
  key = key.toLowerCase();
  const keys = Object.keys(obj);
  let i = keys.length;
  let _key;
  while (i-- > 0) {
    _key = keys[i];
    if (key === _key.toLowerCase()) {
      return _key;
    }
  }
  return null;
}

const _global = (() => {
  /*eslint no-undef:0*/
  if (typeof globalThis !== "undefined") return globalThis;
  return typeof self !== "undefined" ? self : (typeof window !== 'undefined' ? window : global)
})();

const isContextDefined = (context) => !isUndefined(context) && context !== _global;

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 *
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  const {caseless} = isContextDefined(this) && this || {};
  const result = {};
  const assignValue = (val, key) => {
    const targetKey = caseless && findKey(result, key) || key;
    if (isPlainObject(result[targetKey]) && isPlainObject(val)) {
      result[targetKey] = merge(result[targetKey], val);
    } else if (isPlainObject(val)) {
      result[targetKey] = merge({}, val);
    } else if (isArray(val)) {
      result[targetKey] = val.slice();
    } else {
      result[targetKey] = val;
    }
  }

  for (let i = 0, l = arguments.length; i < l; i++) {
    arguments[i] && forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 *
 * @param {Boolean} [allOwnKeys]
 * @returns {Object} The resulting value of object a
 */
const extend = (a, b, thisArg, {allOwnKeys}= {}) => {
  forEach(b, (val, key) => {
    if (thisArg && isFunction(val)) {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  }, {allOwnKeys});
  return a;
}

/**
 * Remove byte order marker. This catches EF BB BF (the UTF-8 BOM)
 *
 * @param {string} content with BOM
 *
 * @returns {string} content value without BOM
 */
const stripBOM = (content) => {
  if (content.charCodeAt(0) === 0xFEFF) {
    content = content.slice(1);
  }
  return content;
}

/**
 * Inherit the prototype methods from one constructor into another
 * @param {function} constructor
 * @param {function} superConstructor
 * @param {object} [props]
 * @param {object} [descriptors]
 *
 * @returns {void}
 */
const inherits = (constructor, superConstructor, props, descriptors) => {
  constructor.prototype = Object.create(superConstructor.prototype, descriptors);
  constructor.prototype.constructor = constructor;
  Object.defineProperty(constructor, 'super', {
    value: superConstructor.prototype
  });
  props && Object.assign(constructor.prototype, props);
}

/**
 * Resolve object with deep prototype chain to a flat object
 * @param {Object} sourceObj source object
 * @param {Object} [destObj]
 * @param {Function|Boolean} [filter]
 * @param {Function} [propFilter]
 *
 * @returns {Object}
 */
const toFlatObject = (sourceObj, destObj, filter, propFilter) => {
  let props;
  let i;
  let prop;
  const merged = {};

  destObj = destObj || {};
  // eslint-disable-next-line no-eq-null,eqeqeq
  if (sourceObj == null) return destObj;

  do {
    props = Object.getOwnPropertyNames(sourceObj);
    i = props.length;
    while (i-- > 0) {
      prop = props[i];
      if ((!propFilter || propFilter(prop, sourceObj, destObj)) && !merged[prop]) {
        destObj[prop] = sourceObj[prop];
        merged[prop] = true;
      }
    }
    sourceObj = filter !== false && getPrototypeOf(sourceObj);
  } while (sourceObj && (!filter || filter(sourceObj, destObj)) && sourceObj !== Object.prototype);

  return destObj;
}

/**
 * Determines whether a string ends with the characters of a specified string
 *
 * @param {String} str
 * @param {String} searchString
 * @param {Number} [position= 0]
 *
 * @returns {boolean}
 */
const endsWith = (str, searchString, position) => {
  str = String(str);
  if (position === undefined || position > str.length) {
    position = str.length;
  }
  position -= searchString.length;
  const lastIndex = str.indexOf(searchString, position);
  return lastIndex !== -1 && lastIndex === position;
}


/**
 * Returns new array from array like object or null if failed
 *
 * @param {*} [thing]
 *
 * @returns {?Array}
 */
const toArray = (thing) => {
  if (!thing) return null;
  if (isArray(thing)) return thing;
  let i = thing.length;
  if (!isNumber(i)) return null;
  const arr = new Array(i);
  while (i-- > 0) {
    arr[i] = thing[i];
  }
  return arr;
}

/**
 * Checking if the Uint8Array exists and if it does, it returns a function that checks if the
 * thing passed in is an instance of Uint8Array
 *
 * @param {TypedArray}
 *
 * @returns {Array}
 */
// eslint-disable-next-line func-names
const isTypedArray = (TypedArray => {
  // eslint-disable-next-line func-names
  return thing => {
    return TypedArray && thing instanceof TypedArray;
  };
})(typeof Uint8Array !== 'undefined' && getPrototypeOf(Uint8Array));

/**
 * For each entry in the object, call the function with the key and value.
 *
 * @param {Object<any, any>} obj - The object to iterate over.
 * @param {Function} fn - The function to call for each entry.
 *
 * @returns {void}
 */
const forEachEntry = (obj, fn) => {
  const generator = obj && obj[Symbol.iterator];

  const iterator = generator.call(obj);

  let result;

  while ((result = iterator.next()) && !result.done) {
    const pair = result.value;
    fn.call(obj, pair[0], pair[1]);
  }
}

/**
 * It takes a regular expression and a string, and returns an array of all the matches
 *
 * @param {string} regExp - The regular expression to match against.
 * @param {string} str - The string to search.
 *
 * @returns {Array<boolean>}
 */
const matchAll = (regExp, str) => {
  let matches;
  const arr = [];

  while ((matches = regExp.exec(str)) !== null) {
    arr.push(matches);
  }

  return arr;
}

/* Checking if the kindOfTest function returns true when passed an HTMLFormElement. */
const isHTMLForm = kindOfTest('HTMLFormElement');

const toCamelCase = str => {
  return str.toLowerCase().replace(/[-_\s]([a-z\d])(\w*)/g,
    function replacer(m, p1, p2) {
      return p1.toUpperCase() + p2;
    }
  );
};

/* Creating a function that will check if an object has a property. */
const utils_hasOwnProperty = (({hasOwnProperty}) => (obj, prop) => hasOwnProperty.call(obj, prop))(Object.prototype);

/**
 * Determine if a value is a RegExp object
 *
 * @param {*} val The value to test
 *
 * @returns {boolean} True if value is a RegExp object, otherwise false
 */
const isRegExp = kindOfTest('RegExp');

const reduceDescriptors = (obj, reducer) => {
  const descriptors = Object.getOwnPropertyDescriptors(obj);
  const reducedDescriptors = {};

  forEach(descriptors, (descriptor, name) => {
    let ret;
    if ((ret = reducer(descriptor, name, obj)) !== false) {
      reducedDescriptors[name] = ret || descriptor;
    }
  });

  Object.defineProperties(obj, reducedDescriptors);
}

/**
 * Makes all methods read-only
 * @param {Object} obj
 */

const freezeMethods = (obj) => {
  reduceDescriptors(obj, (descriptor, name) => {
    // skip restricted props in strict mode
    if (isFunction(obj) && ['arguments', 'caller', 'callee'].indexOf(name) !== -1) {
      return false;
    }

    const value = obj[name];

    if (!isFunction(value)) return;

    descriptor.enumerable = false;

    if ('writable' in descriptor) {
      descriptor.writable = false;
      return;
    }

    if (!descriptor.set) {
      descriptor.set = () => {
        throw Error('Can not rewrite read-only method \'' + name + '\'');
      };
    }
  });
}

const toObjectSet = (arrayOrString, delimiter) => {
  const obj = {};

  const define = (arr) => {
    arr.forEach(value => {
      obj[value] = true;
    });
  }

  isArray(arrayOrString) ? define(arrayOrString) : define(String(arrayOrString).split(delimiter));

  return obj;
}

const noop = () => {}

const toFiniteNumber = (value, defaultValue) => {
  value = +value;
  return Number.isFinite(value) ? value : defaultValue;
}

const ALPHA = 'abcdefghijklmnopqrstuvwxyz'

const DIGIT = '0123456789';

const ALPHABET = {
  DIGIT,
  ALPHA,
  ALPHA_DIGIT: ALPHA + ALPHA.toUpperCase() + DIGIT
}

const generateString = (size = 16, alphabet = ALPHABET.ALPHA_DIGIT) => {
  let str = '';
  const {length} = alphabet;
  while (size--) {
    str += alphabet[Math.random() * length|0]
  }

  return str;
}

/**
 * If the thing is a FormData object, return true, otherwise return false.
 *
 * @param {unknown} thing - The thing to check.
 *
 * @returns {boolean}
 */
function isSpecCompliantForm(thing) {
  return !!(thing && isFunction(thing.append) && thing[Symbol.toStringTag] === 'FormData' && thing[Symbol.iterator]);
}

const toJSONObject = (obj) => {
  const stack = new Array(10);

  const visit = (source, i) => {

    if (isObject(source)) {
      if (stack.indexOf(source) >= 0) {
        return;
      }

      if(!('toJSON' in source)) {
        stack[i] = source;
        const target = isArray(source) ? [] : {};

        forEach(source, (value, key) => {
          const reducedValue = visit(value, i + 1);
          !isUndefined(reducedValue) && (target[key] = reducedValue);
        });

        stack[i] = undefined;

        return target;
      }
    }

    return source;
  }

  return visit(obj, 0);
}

const isAsyncFn = kindOfTest('AsyncFunction');

const isThenable = (thing) =>
  thing && (isObject(thing) || isFunction(thing)) && isFunction(thing.then) && isFunction(thing.catch);

/* harmony default export */ const utils = ({
  isArray,
  isArrayBuffer,
  isBuffer,
  isFormData,
  isArrayBufferView,
  isString,
  isNumber,
  isBoolean,
  isObject,
  isPlainObject,
  isUndefined,
  isDate,
  isFile,
  isBlob,
  isRegExp,
  isFunction,
  isStream,
  isURLSearchParams,
  isTypedArray,
  isFileList,
  forEach,
  merge,
  extend,
  trim,
  stripBOM,
  inherits,
  toFlatObject,
  kindOf,
  kindOfTest,
  endsWith,
  toArray,
  forEachEntry,
  matchAll,
  isHTMLForm,
  hasOwnProperty: utils_hasOwnProperty,
  hasOwnProp: utils_hasOwnProperty, // an alias to avoid ESLint no-prototype-builtins detection
  reduceDescriptors,
  freezeMethods,
  toObjectSet,
  toCamelCase,
  noop,
  toFiniteNumber,
  findKey,
  global: _global,
  isContextDefined,
  ALPHABET,
  generateString,
  isSpecCompliantForm,
  toJSONObject,
  isAsyncFn,
  isThenable
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/AxiosError.js




/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [config] The config.
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 *
 * @returns {Error} The created error.
 */
function AxiosError(message, code, config, request, response) {
  Error.call(this);

  if (Error.captureStackTrace) {
    Error.captureStackTrace(this, this.constructor);
  } else {
    this.stack = (new Error()).stack;
  }

  this.message = message;
  this.name = 'AxiosError';
  code && (this.code = code);
  config && (this.config = config);
  request && (this.request = request);
  response && (this.response = response);
}

utils.inherits(AxiosError, Error, {
  toJSON: function toJSON() {
    return {
      // Standard
      message: this.message,
      name: this.name,
      // Microsoft
      description: this.description,
      number: this.number,
      // Mozilla
      fileName: this.fileName,
      lineNumber: this.lineNumber,
      columnNumber: this.columnNumber,
      stack: this.stack,
      // Axios
      config: utils.toJSONObject(this.config),
      code: this.code,
      status: this.response && this.response.status ? this.response.status : null
    };
  }
});

const AxiosError_prototype = AxiosError.prototype;
const descriptors = {};

[
  'ERR_BAD_OPTION_VALUE',
  'ERR_BAD_OPTION',
  'ECONNABORTED',
  'ETIMEDOUT',
  'ERR_NETWORK',
  'ERR_FR_TOO_MANY_REDIRECTS',
  'ERR_DEPRECATED',
  'ERR_BAD_RESPONSE',
  'ERR_BAD_REQUEST',
  'ERR_CANCELED',
  'ERR_NOT_SUPPORT',
  'ERR_INVALID_URL'
// eslint-disable-next-line func-names
].forEach(code => {
  descriptors[code] = {value: code};
});

Object.defineProperties(AxiosError, descriptors);
Object.defineProperty(AxiosError_prototype, 'isAxiosError', {value: true});

// eslint-disable-next-line func-names
AxiosError.from = (error, code, config, request, response, customProps) => {
  const axiosError = Object.create(AxiosError_prototype);

  utils.toFlatObject(error, axiosError, function filter(obj) {
    return obj !== Error.prototype;
  }, prop => {
    return prop !== 'isAxiosError';
  });

  AxiosError.call(axiosError, error.message, code, config, request, response);

  axiosError.cause = error;

  axiosError.name = error.name;

  customProps && Object.assign(axiosError, customProps);

  return axiosError;
};

/* harmony default export */ const core_AxiosError = (AxiosError);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/null.js
// eslint-disable-next-line strict
/* harmony default export */ const helpers_null = (null);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/toFormData.js




// temporary hotfix to avoid circular references until AxiosURLSearchParams is refactored


/**
 * Determines if the given thing is a array or js object.
 *
 * @param {string} thing - The object or array to be visited.
 *
 * @returns {boolean}
 */
function isVisitable(thing) {
  return utils.isPlainObject(thing) || utils.isArray(thing);
}

/**
 * It removes the brackets from the end of a string
 *
 * @param {string} key - The key of the parameter.
 *
 * @returns {string} the key without the brackets.
 */
function removeBrackets(key) {
  return utils.endsWith(key, '[]') ? key.slice(0, -2) : key;
}

/**
 * It takes a path, a key, and a boolean, and returns a string
 *
 * @param {string} path - The path to the current key.
 * @param {string} key - The key of the current object being iterated over.
 * @param {string} dots - If true, the key will be rendered with dots instead of brackets.
 *
 * @returns {string} The path to the current key.
 */
function renderKey(path, key, dots) {
  if (!path) return key;
  return path.concat(key).map(function each(token, i) {
    // eslint-disable-next-line no-param-reassign
    token = removeBrackets(token);
    return !dots && i ? '[' + token + ']' : token;
  }).join(dots ? '.' : '');
}

/**
 * If the array is an array and none of its elements are visitable, then it's a flat array.
 *
 * @param {Array<any>} arr - The array to check
 *
 * @returns {boolean}
 */
function isFlatArray(arr) {
  return utils.isArray(arr) && !arr.some(isVisitable);
}

const predicates = utils.toFlatObject(utils, {}, null, function filter(prop) {
  return /^is[A-Z]/.test(prop);
});

/**
 * Convert a data object to FormData
 *
 * @param {Object} obj
 * @param {?Object} [formData]
 * @param {?Object} [options]
 * @param {Function} [options.visitor]
 * @param {Boolean} [options.metaTokens = true]
 * @param {Boolean} [options.dots = false]
 * @param {?Boolean} [options.indexes = false]
 *
 * @returns {Object}
 **/

/**
 * It converts an object into a FormData object
 *
 * @param {Object<any, any>} obj - The object to convert to form data.
 * @param {string} formData - The FormData object to append to.
 * @param {Object<string, any>} options
 *
 * @returns
 */
function toFormData(obj, formData, options) {
  if (!utils.isObject(obj)) {
    throw new TypeError('target must be an object');
  }

  // eslint-disable-next-line no-param-reassign
  formData = formData || new (helpers_null || FormData)();

  // eslint-disable-next-line no-param-reassign
  options = utils.toFlatObject(options, {
    metaTokens: true,
    dots: false,
    indexes: false
  }, false, function defined(option, source) {
    // eslint-disable-next-line no-eq-null,eqeqeq
    return !utils.isUndefined(source[option]);
  });

  const metaTokens = options.metaTokens;
  // eslint-disable-next-line no-use-before-define
  const visitor = options.visitor || defaultVisitor;
  const dots = options.dots;
  const indexes = options.indexes;
  const _Blob = options.Blob || typeof Blob !== 'undefined' && Blob;
  const useBlob = _Blob && utils.isSpecCompliantForm(formData);

  if (!utils.isFunction(visitor)) {
    throw new TypeError('visitor must be a function');
  }

  function convertValue(value) {
    if (value === null) return '';

    if (utils.isDate(value)) {
      return value.toISOString();
    }

    if (!useBlob && utils.isBlob(value)) {
      throw new core_AxiosError('Blob is not supported. Use a Buffer instead.');
    }

    if (utils.isArrayBuffer(value) || utils.isTypedArray(value)) {
      return useBlob && typeof Blob === 'function' ? new Blob([value]) : Buffer.from(value);
    }

    return value;
  }

  /**
   * Default visitor.
   *
   * @param {*} value
   * @param {String|Number} key
   * @param {Array<String|Number>} path
   * @this {FormData}
   *
   * @returns {boolean} return true to visit the each prop of the value recursively
   */
  function defaultVisitor(value, key, path) {
    let arr = value;

    if (value && !path && typeof value === 'object') {
      if (utils.endsWith(key, '{}')) {
        // eslint-disable-next-line no-param-reassign
        key = metaTokens ? key : key.slice(0, -2);
        // eslint-disable-next-line no-param-reassign
        value = JSON.stringify(value);
      } else if (
        (utils.isArray(value) && isFlatArray(value)) ||
        ((utils.isFileList(value) || utils.endsWith(key, '[]')) && (arr = utils.toArray(value))
        )) {
        // eslint-disable-next-line no-param-reassign
        key = removeBrackets(key);

        arr.forEach(function each(el, index) {
          !(utils.isUndefined(el) || el === null) && formData.append(
            // eslint-disable-next-line no-nested-ternary
            indexes === true ? renderKey([key], index, dots) : (indexes === null ? key : key + '[]'),
            convertValue(el)
          );
        });
        return false;
      }
    }

    if (isVisitable(value)) {
      return true;
    }

    formData.append(renderKey(path, key, dots), convertValue(value));

    return false;
  }

  const stack = [];

  const exposedHelpers = Object.assign(predicates, {
    defaultVisitor,
    convertValue,
    isVisitable
  });

  function build(value, path) {
    if (utils.isUndefined(value)) return;

    if (stack.indexOf(value) !== -1) {
      throw Error('Circular reference detected in ' + path.join('.'));
    }

    stack.push(value);

    utils.forEach(value, function each(el, key) {
      const result = !(utils.isUndefined(el) || el === null) && visitor.call(
        formData, el, utils.isString(key) ? key.trim() : key, path, exposedHelpers
      );

      if (result === true) {
        build(el, path ? path.concat(key) : [key]);
      }
    });

    stack.pop();
  }

  if (!utils.isObject(obj)) {
    throw new TypeError('data must be an object');
  }

  build(obj);

  return formData;
}

/* harmony default export */ const helpers_toFormData = (toFormData);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/AxiosURLSearchParams.js




/**
 * It encodes a string by replacing all characters that are not in the unreserved set with
 * their percent-encoded equivalents
 *
 * @param {string} str - The string to encode.
 *
 * @returns {string} The encoded string.
 */
function encode(str) {
  const charMap = {
    '!': '%21',
    "'": '%27',
    '(': '%28',
    ')': '%29',
    '~': '%7E',
    '%20': '+',
    '%00': '\x00'
  };
  return encodeURIComponent(str).replace(/[!'()~]|%20|%00/g, function replacer(match) {
    return charMap[match];
  });
}

/**
 * It takes a params object and converts it to a FormData object
 *
 * @param {Object<string, any>} params - The parameters to be converted to a FormData object.
 * @param {Object<string, any>} options - The options object passed to the Axios constructor.
 *
 * @returns {void}
 */
function AxiosURLSearchParams(params, options) {
  this._pairs = [];

  params && helpers_toFormData(params, this, options);
}

const AxiosURLSearchParams_prototype = AxiosURLSearchParams.prototype;

AxiosURLSearchParams_prototype.append = function append(name, value) {
  this._pairs.push([name, value]);
};

AxiosURLSearchParams_prototype.toString = function toString(encoder) {
  const _encode = encoder ? function(value) {
    return encoder.call(this, value, encode);
  } : encode;

  return this._pairs.map(function each(pair) {
    return _encode(pair[0]) + '=' + _encode(pair[1]);
  }, '').join('&');
};

/* harmony default export */ const helpers_AxiosURLSearchParams = (AxiosURLSearchParams);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/buildURL.js





/**
 * It replaces all instances of the characters `:`, `$`, `,`, `+`, `[`, and `]` with their
 * URI encoded counterparts
 *
 * @param {string} val The value to be encoded.
 *
 * @returns {string} The encoded value.
 */
function buildURL_encode(val) {
  return encodeURIComponent(val).
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @param {?object} options
 *
 * @returns {string} The formatted url
 */
function buildURL(url, params, options) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }
  
  const _encode = options && options.encode || buildURL_encode;

  const serializeFn = options && options.serialize;

  let serializedParams;

  if (serializeFn) {
    serializedParams = serializeFn(params, options);
  } else {
    serializedParams = utils.isURLSearchParams(params) ?
      params.toString() :
      new helpers_AxiosURLSearchParams(params, options).toString(_encode);
  }

  if (serializedParams) {
    const hashmarkIndex = url.indexOf("#");

    if (hashmarkIndex !== -1) {
      url = url.slice(0, hashmarkIndex);
    }
    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/InterceptorManager.js




class InterceptorManager {
  constructor() {
    this.handlers = [];
  }

  /**
   * Add a new interceptor to the stack
   *
   * @param {Function} fulfilled The function to handle `then` for a `Promise`
   * @param {Function} rejected The function to handle `reject` for a `Promise`
   *
   * @return {Number} An ID used to remove interceptor later
   */
  use(fulfilled, rejected, options) {
    this.handlers.push({
      fulfilled,
      rejected,
      synchronous: options ? options.synchronous : false,
      runWhen: options ? options.runWhen : null
    });
    return this.handlers.length - 1;
  }

  /**
   * Remove an interceptor from the stack
   *
   * @param {Number} id The ID that was returned by `use`
   *
   * @returns {Boolean} `true` if the interceptor was removed, `false` otherwise
   */
  eject(id) {
    if (this.handlers[id]) {
      this.handlers[id] = null;
    }
  }

  /**
   * Clear all interceptors from the stack
   *
   * @returns {void}
   */
  clear() {
    if (this.handlers) {
      this.handlers = [];
    }
  }

  /**
   * Iterate over all the registered interceptors
   *
   * This method is particularly useful for skipping over any
   * interceptors that may have become `null` calling `eject`.
   *
   * @param {Function} fn The function to call for each interceptor
   *
   * @returns {void}
   */
  forEach(fn) {
    utils.forEach(this.handlers, function forEachHandler(h) {
      if (h !== null) {
        fn(h);
      }
    });
  }
}

/* harmony default export */ const core_InterceptorManager = (InterceptorManager);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/defaults/transitional.js


/* harmony default export */ const defaults_transitional = ({
  silentJSONParsing: true,
  forcedJSONParsing: true,
  clarifyTimeoutError: false
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/platform/browser/classes/URLSearchParams.js



/* harmony default export */ const classes_URLSearchParams = (typeof URLSearchParams !== 'undefined' ? URLSearchParams : helpers_AxiosURLSearchParams);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/platform/browser/classes/FormData.js


/* harmony default export */ const classes_FormData = (typeof FormData !== 'undefined' ? FormData : null);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/platform/browser/classes/Blob.js


/* harmony default export */ const classes_Blob = (typeof Blob !== 'undefined' ? Blob : null);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/platform/browser/index.js




/* harmony default export */ const browser = ({
  isBrowser: true,
  classes: {
    URLSearchParams: classes_URLSearchParams,
    FormData: classes_FormData,
    Blob: classes_Blob
  },
  protocols: ['http', 'https', 'file', 'blob', 'url', 'data']
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/platform/common/utils.js
const hasBrowserEnv = typeof window !== 'undefined' && typeof document !== 'undefined';

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 * nativescript
 *  navigator.product -> 'NativeScript' or 'NS'
 *
 * @returns {boolean}
 */
const hasStandardBrowserEnv = (
  (product) => {
    return hasBrowserEnv && ['ReactNative', 'NativeScript', 'NS'].indexOf(product) < 0
  })(typeof navigator !== 'undefined' && navigator.product);

/**
 * Determine if we're running in a standard browser webWorker environment
 *
 * Although the `isStandardBrowserEnv` method indicates that
 * `allows axios to run in a web worker`, the WebWorker will still be
 * filtered out due to its judgment standard
 * `typeof window !== 'undefined' && typeof document !== 'undefined'`.
 * This leads to a problem when axios post `FormData` in webWorker
 */
const hasStandardBrowserWebWorkerEnv = (() => {
  return (
    typeof WorkerGlobalScope !== 'undefined' &&
    // eslint-disable-next-line no-undef
    self instanceof WorkerGlobalScope &&
    typeof self.importScripts === 'function'
  );
})();



;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/platform/index.js



/* harmony default export */ const platform = ({
  ...common_utils_namespaceObject,
  ...browser
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/toURLEncodedForm.js






function toURLEncodedForm(data, options) {
  return helpers_toFormData(data, new platform.classes.URLSearchParams(), Object.assign({
    visitor: function(value, key, path, helpers) {
      if (platform.isNode && utils.isBuffer(value)) {
        this.append(key, value.toString('base64'));
        return false;
      }

      return helpers.defaultVisitor.apply(this, arguments);
    }
  }, options));
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/formDataToJSON.js




/**
 * It takes a string like `foo[x][y][z]` and returns an array like `['foo', 'x', 'y', 'z']
 *
 * @param {string} name - The name of the property to get.
 *
 * @returns An array of strings.
 */
function parsePropPath(name) {
  // foo[x][y][z]
  // foo.x.y.z
  // foo-x-y-z
  // foo x y z
  return utils.matchAll(/\w+|\[(\w*)]/g, name).map(match => {
    return match[0] === '[]' ? '' : match[1] || match[0];
  });
}

/**
 * Convert an array to an object.
 *
 * @param {Array<any>} arr - The array to convert to an object.
 *
 * @returns An object with the same keys and values as the array.
 */
function arrayToObject(arr) {
  const obj = {};
  const keys = Object.keys(arr);
  let i;
  const len = keys.length;
  let key;
  for (i = 0; i < len; i++) {
    key = keys[i];
    obj[key] = arr[key];
  }
  return obj;
}

/**
 * It takes a FormData object and returns a JavaScript object
 *
 * @param {string} formData The FormData object to convert to JSON.
 *
 * @returns {Object<string, any> | null} The converted object.
 */
function formDataToJSON(formData) {
  function buildPath(path, value, target, index) {
    let name = path[index++];
    const isNumericKey = Number.isFinite(+name);
    const isLast = index >= path.length;
    name = !name && utils.isArray(target) ? target.length : name;

    if (isLast) {
      if (utils.hasOwnProp(target, name)) {
        target[name] = [target[name], value];
      } else {
        target[name] = value;
      }

      return !isNumericKey;
    }

    if (!target[name] || !utils.isObject(target[name])) {
      target[name] = [];
    }

    const result = buildPath(path, value, target[name], index);

    if (result && utils.isArray(target[name])) {
      target[name] = arrayToObject(target[name]);
    }

    return !isNumericKey;
  }

  if (utils.isFormData(formData) && utils.isFunction(formData.entries)) {
    const obj = {};

    utils.forEachEntry(formData, (name, value) => {
      buildPath(parsePropPath(name), value, obj, 0);
    });

    return obj;
  }

  return null;
}

/* harmony default export */ const helpers_formDataToJSON = (formDataToJSON);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/defaults/index.js










/**
 * It takes a string, tries to parse it, and if it fails, it returns the stringified version
 * of the input
 *
 * @param {any} rawValue - The value to be stringified.
 * @param {Function} parser - A function that parses a string into a JavaScript object.
 * @param {Function} encoder - A function that takes a value and returns a string.
 *
 * @returns {string} A stringified version of the rawValue.
 */
function stringifySafely(rawValue, parser, encoder) {
  if (utils.isString(rawValue)) {
    try {
      (parser || JSON.parse)(rawValue);
      return utils.trim(rawValue);
    } catch (e) {
      if (e.name !== 'SyntaxError') {
        throw e;
      }
    }
  }

  return (encoder || JSON.stringify)(rawValue);
}

const defaults = {

  transitional: defaults_transitional,

  adapter: ['xhr', 'http'],

  transformRequest: [function transformRequest(data, headers) {
    const contentType = headers.getContentType() || '';
    const hasJSONContentType = contentType.indexOf('application/json') > -1;
    const isObjectPayload = utils.isObject(data);

    if (isObjectPayload && utils.isHTMLForm(data)) {
      data = new FormData(data);
    }

    const isFormData = utils.isFormData(data);

    if (isFormData) {
      if (!hasJSONContentType) {
        return data;
      }
      return hasJSONContentType ? JSON.stringify(helpers_formDataToJSON(data)) : data;
    }

    if (utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      headers.setContentType('application/x-www-form-urlencoded;charset=utf-8', false);
      return data.toString();
    }

    let isFileList;

    if (isObjectPayload) {
      if (contentType.indexOf('application/x-www-form-urlencoded') > -1) {
        return toURLEncodedForm(data, this.formSerializer).toString();
      }

      if ((isFileList = utils.isFileList(data)) || contentType.indexOf('multipart/form-data') > -1) {
        const _FormData = this.env && this.env.FormData;

        return helpers_toFormData(
          isFileList ? {'files[]': data} : data,
          _FormData && new _FormData(),
          this.formSerializer
        );
      }
    }

    if (isObjectPayload || hasJSONContentType ) {
      headers.setContentType('application/json', false);
      return stringifySafely(data);
    }

    return data;
  }],

  transformResponse: [function transformResponse(data) {
    const transitional = this.transitional || defaults.transitional;
    const forcedJSONParsing = transitional && transitional.forcedJSONParsing;
    const JSONRequested = this.responseType === 'json';

    if (data && utils.isString(data) && ((forcedJSONParsing && !this.responseType) || JSONRequested)) {
      const silentJSONParsing = transitional && transitional.silentJSONParsing;
      const strictJSONParsing = !silentJSONParsing && JSONRequested;

      try {
        return JSON.parse(data);
      } catch (e) {
        if (strictJSONParsing) {
          if (e.name === 'SyntaxError') {
            throw core_AxiosError.from(e, core_AxiosError.ERR_BAD_RESPONSE, this, null, this.response);
          }
          throw e;
        }
      }
    }

    return data;
  }],

  /**
   * A timeout in milliseconds to abort a request. If set to 0 (default) a
   * timeout is not created.
   */
  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,
  maxBodyLength: -1,

  env: {
    FormData: platform.classes.FormData,
    Blob: platform.classes.Blob
  },

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  },

  headers: {
    common: {
      'Accept': 'application/json, text/plain, */*',
      'Content-Type': undefined
    }
  }
};

utils.forEach(['delete', 'get', 'head', 'post', 'put', 'patch'], (method) => {
  defaults.headers[method] = {};
});

/* harmony default export */ const lib_defaults = (defaults);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/parseHeaders.js




// RawAxiosHeaders whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
const ignoreDuplicateOf = utils.toObjectSet([
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
]);

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} rawHeaders Headers needing to be parsed
 *
 * @returns {Object} Headers parsed into an object
 */
/* harmony default export */ const parseHeaders = (rawHeaders => {
  const parsed = {};
  let key;
  let val;
  let i;

  rawHeaders && rawHeaders.split('\n').forEach(function parser(line) {
    i = line.indexOf(':');
    key = line.substring(0, i).trim().toLowerCase();
    val = line.substring(i + 1).trim();

    if (!key || (parsed[key] && ignoreDuplicateOf[key])) {
      return;
    }

    if (key === 'set-cookie') {
      if (parsed[key]) {
        parsed[key].push(val);
      } else {
        parsed[key] = [val];
      }
    } else {
      parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
    }
  });

  return parsed;
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/AxiosHeaders.js





const $internals = Symbol('internals');

function normalizeHeader(header) {
  return header && String(header).trim().toLowerCase();
}

function normalizeValue(value) {
  if (value === false || value == null) {
    return value;
  }

  return utils.isArray(value) ? value.map(normalizeValue) : String(value);
}

function parseTokens(str) {
  const tokens = Object.create(null);
  const tokensRE = /([^\s,;=]+)\s*(?:=\s*([^,;]+))?/g;
  let match;

  while ((match = tokensRE.exec(str))) {
    tokens[match[1]] = match[2];
  }

  return tokens;
}

const isValidHeaderName = (str) => /^[-_a-zA-Z0-9^`|~,!#$%&'*+.]+$/.test(str.trim());

function matchHeaderValue(context, value, header, filter, isHeaderNameFilter) {
  if (utils.isFunction(filter)) {
    return filter.call(this, value, header);
  }

  if (isHeaderNameFilter) {
    value = header;
  }

  if (!utils.isString(value)) return;

  if (utils.isString(filter)) {
    return value.indexOf(filter) !== -1;
  }

  if (utils.isRegExp(filter)) {
    return filter.test(value);
  }
}

function formatHeader(header) {
  return header.trim()
    .toLowerCase().replace(/([a-z\d])(\w*)/g, (w, char, str) => {
      return char.toUpperCase() + str;
    });
}

function buildAccessors(obj, header) {
  const accessorName = utils.toCamelCase(' ' + header);

  ['get', 'set', 'has'].forEach(methodName => {
    Object.defineProperty(obj, methodName + accessorName, {
      value: function(arg1, arg2, arg3) {
        return this[methodName].call(this, header, arg1, arg2, arg3);
      },
      configurable: true
    });
  });
}

class AxiosHeaders {
  constructor(headers) {
    headers && this.set(headers);
  }

  set(header, valueOrRewrite, rewrite) {
    const self = this;

    function setHeader(_value, _header, _rewrite) {
      const lHeader = normalizeHeader(_header);

      if (!lHeader) {
        throw new Error('header name must be a non-empty string');
      }

      const key = utils.findKey(self, lHeader);

      if(!key || self[key] === undefined || _rewrite === true || (_rewrite === undefined && self[key] !== false)) {
        self[key || _header] = normalizeValue(_value);
      }
    }

    const setHeaders = (headers, _rewrite) =>
      utils.forEach(headers, (_value, _header) => setHeader(_value, _header, _rewrite));

    if (utils.isPlainObject(header) || header instanceof this.constructor) {
      setHeaders(header, valueOrRewrite)
    } else if(utils.isString(header) && (header = header.trim()) && !isValidHeaderName(header)) {
      setHeaders(parseHeaders(header), valueOrRewrite);
    } else {
      header != null && setHeader(valueOrRewrite, header, rewrite);
    }

    return this;
  }

  get(header, parser) {
    header = normalizeHeader(header);

    if (header) {
      const key = utils.findKey(this, header);

      if (key) {
        const value = this[key];

        if (!parser) {
          return value;
        }

        if (parser === true) {
          return parseTokens(value);
        }

        if (utils.isFunction(parser)) {
          return parser.call(this, value, key);
        }

        if (utils.isRegExp(parser)) {
          return parser.exec(value);
        }

        throw new TypeError('parser must be boolean|regexp|function');
      }
    }
  }

  has(header, matcher) {
    header = normalizeHeader(header);

    if (header) {
      const key = utils.findKey(this, header);

      return !!(key && this[key] !== undefined && (!matcher || matchHeaderValue(this, this[key], key, matcher)));
    }

    return false;
  }

  delete(header, matcher) {
    const self = this;
    let deleted = false;

    function deleteHeader(_header) {
      _header = normalizeHeader(_header);

      if (_header) {
        const key = utils.findKey(self, _header);

        if (key && (!matcher || matchHeaderValue(self, self[key], key, matcher))) {
          delete self[key];

          deleted = true;
        }
      }
    }

    if (utils.isArray(header)) {
      header.forEach(deleteHeader);
    } else {
      deleteHeader(header);
    }

    return deleted;
  }

  clear(matcher) {
    const keys = Object.keys(this);
    let i = keys.length;
    let deleted = false;

    while (i--) {
      const key = keys[i];
      if(!matcher || matchHeaderValue(this, this[key], key, matcher, true)) {
        delete this[key];
        deleted = true;
      }
    }

    return deleted;
  }

  normalize(format) {
    const self = this;
    const headers = {};

    utils.forEach(this, (value, header) => {
      const key = utils.findKey(headers, header);

      if (key) {
        self[key] = normalizeValue(value);
        delete self[header];
        return;
      }

      const normalized = format ? formatHeader(header) : String(header).trim();

      if (normalized !== header) {
        delete self[header];
      }

      self[normalized] = normalizeValue(value);

      headers[normalized] = true;
    });

    return this;
  }

  concat(...targets) {
    return this.constructor.concat(this, ...targets);
  }

  toJSON(asStrings) {
    const obj = Object.create(null);

    utils.forEach(this, (value, header) => {
      value != null && value !== false && (obj[header] = asStrings && utils.isArray(value) ? value.join(', ') : value);
    });

    return obj;
  }

  [Symbol.iterator]() {
    return Object.entries(this.toJSON())[Symbol.iterator]();
  }

  toString() {
    return Object.entries(this.toJSON()).map(([header, value]) => header + ': ' + value).join('\n');
  }

  get [Symbol.toStringTag]() {
    return 'AxiosHeaders';
  }

  static from(thing) {
    return thing instanceof this ? thing : new this(thing);
  }

  static concat(first, ...targets) {
    const computed = new this(first);

    targets.forEach((target) => computed.set(target));

    return computed;
  }

  static accessor(header) {
    const internals = this[$internals] = (this[$internals] = {
      accessors: {}
    });

    const accessors = internals.accessors;
    const prototype = this.prototype;

    function defineAccessor(_header) {
      const lHeader = normalizeHeader(_header);

      if (!accessors[lHeader]) {
        buildAccessors(prototype, _header);
        accessors[lHeader] = true;
      }
    }

    utils.isArray(header) ? header.forEach(defineAccessor) : defineAccessor(header);

    return this;
  }
}

AxiosHeaders.accessor(['Content-Type', 'Content-Length', 'Accept', 'Accept-Encoding', 'User-Agent', 'Authorization']);

// reserved names hotfix
utils.reduceDescriptors(AxiosHeaders.prototype, ({value}, key) => {
  let mapped = key[0].toUpperCase() + key.slice(1); // map `set` => `Set`
  return {
    get: () => value,
    set(headerValue) {
      this[mapped] = headerValue;
    }
  }
});

utils.freezeMethods(AxiosHeaders);

/* harmony default export */ const core_AxiosHeaders = (AxiosHeaders);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/transformData.js






/**
 * Transform the data for a request or a response
 *
 * @param {Array|Function} fns A single function or Array of functions
 * @param {?Object} response The response object
 *
 * @returns {*} The resulting transformed data
 */
function transformData(fns, response) {
  const config = this || lib_defaults;
  const context = response || config;
  const headers = core_AxiosHeaders.from(context.headers);
  let data = context.data;

  utils.forEach(fns, function transform(fn) {
    data = fn.call(config, data, headers.normalize(), response ? response.status : undefined);
  });

  headers.normalize();

  return data;
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/cancel/isCancel.js


function isCancel(value) {
  return !!(value && value.__CANCEL__);
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/cancel/CanceledError.js





/**
 * A `CanceledError` is an object that is thrown when an operation is canceled.
 *
 * @param {string=} message The message.
 * @param {Object=} config The config.
 * @param {Object=} request The request.
 *
 * @returns {CanceledError} The created error.
 */
function CanceledError(message, config, request) {
  // eslint-disable-next-line no-eq-null,eqeqeq
  core_AxiosError.call(this, message == null ? 'canceled' : message, core_AxiosError.ERR_CANCELED, config, request);
  this.name = 'CanceledError';
}

utils.inherits(CanceledError, core_AxiosError, {
  __CANCEL__: true
});

/* harmony default export */ const cancel_CanceledError = (CanceledError);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/settle.js




/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 *
 * @returns {object} The response.
 */
function settle(resolve, reject, response) {
  const validateStatus = response.config.validateStatus;
  if (!response.status || !validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(new core_AxiosError(
      'Request failed with status code ' + response.status,
      [core_AxiosError.ERR_BAD_REQUEST, core_AxiosError.ERR_BAD_RESPONSE][Math.floor(response.status / 100) - 4],
      response.config,
      response.request,
      response
    ));
  }
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/cookies.js



/* harmony default export */ const cookies = (platform.hasStandardBrowserEnv ?

  // Standard browser envs support document.cookie
  {
    write(name, value, expires, path, domain, secure) {
      const cookie = [name + '=' + encodeURIComponent(value)];

      utils.isNumber(expires) && cookie.push('expires=' + new Date(expires).toGMTString());

      utils.isString(path) && cookie.push('path=' + path);

      utils.isString(domain) && cookie.push('domain=' + domain);

      secure === true && cookie.push('secure');

      document.cookie = cookie.join('; ');
    },

    read(name) {
      const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
      return (match ? decodeURIComponent(match[3]) : null);
    },

    remove(name) {
      this.write(name, '', Date.now() - 86400000);
    }
  }

  :

  // Non-standard browser env (web workers, react-native) lack needed support.
  {
    write() {},
    read() {
      return null;
    },
    remove() {}
  });


;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/isAbsoluteURL.js


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 *
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d+\-.]*:)?\/\//i.test(url);
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/combineURLs.js


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 *
 * @returns {string} The combined URL
 */
function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/buildFullPath.js





/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 *
 * @returns {string} The combined full path
 */
function buildFullPath(baseURL, requestedURL) {
  if (baseURL && !isAbsoluteURL(requestedURL)) {
    return combineURLs(baseURL, requestedURL);
  }
  return requestedURL;
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/isURLSameOrigin.js





/* harmony default export */ const isURLSameOrigin = (platform.hasStandardBrowserEnv ?

// Standard browser envs have full support of the APIs needed to test
// whether the request URL is of the same origin as current location.
  (function standardBrowserEnv() {
    const msie = /(msie|trident)/i.test(navigator.userAgent);
    const urlParsingNode = document.createElement('a');
    let originURL;

    /**
    * Parse a URL to discover its components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
    function resolveURL(url) {
      let href = url;

      if (msie) {
        // IE needs attribute set twice to normalize properties
        urlParsingNode.setAttribute('href', href);
        href = urlParsingNode.href;
      }

      urlParsingNode.setAttribute('href', href);

      // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
      return {
        href: urlParsingNode.href,
        protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
        host: urlParsingNode.host,
        search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
        hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
        hostname: urlParsingNode.hostname,
        port: urlParsingNode.port,
        pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
          urlParsingNode.pathname :
          '/' + urlParsingNode.pathname
      };
    }

    originURL = resolveURL(window.location.href);

    /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
    return function isURLSameOrigin(requestURL) {
      const parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
      return (parsed.protocol === originURL.protocol &&
          parsed.host === originURL.host);
    };
  })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
  (function nonStandardBrowserEnv() {
    return function isURLSameOrigin() {
      return true;
    };
  })());

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/parseProtocol.js


function parseProtocol(url) {
  const match = /^([-+\w]{1,25})(:?\/\/|:)/.exec(url);
  return match && match[1] || '';
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/speedometer.js


/**
 * Calculate data maxRate
 * @param {Number} [samplesCount= 10]
 * @param {Number} [min= 1000]
 * @returns {Function}
 */
function speedometer(samplesCount, min) {
  samplesCount = samplesCount || 10;
  const bytes = new Array(samplesCount);
  const timestamps = new Array(samplesCount);
  let head = 0;
  let tail = 0;
  let firstSampleTS;

  min = min !== undefined ? min : 1000;

  return function push(chunkLength) {
    const now = Date.now();

    const startedAt = timestamps[tail];

    if (!firstSampleTS) {
      firstSampleTS = now;
    }

    bytes[head] = chunkLength;
    timestamps[head] = now;

    let i = tail;
    let bytesCount = 0;

    while (i !== head) {
      bytesCount += bytes[i++];
      i = i % samplesCount;
    }

    head = (head + 1) % samplesCount;

    if (head === tail) {
      tail = (tail + 1) % samplesCount;
    }

    if (now - firstSampleTS < min) {
      return;
    }

    const passed = startedAt && now - startedAt;

    return passed ? Math.round(bytesCount * 1000 / passed) : undefined;
  };
}

/* harmony default export */ const helpers_speedometer = (speedometer);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/adapters/xhr.js
















function progressEventReducer(listener, isDownloadStream) {
  let bytesNotified = 0;
  const _speedometer = helpers_speedometer(50, 250);

  return e => {
    const loaded = e.loaded;
    const total = e.lengthComputable ? e.total : undefined;
    const progressBytes = loaded - bytesNotified;
    const rate = _speedometer(progressBytes);
    const inRange = loaded <= total;

    bytesNotified = loaded;

    const data = {
      loaded,
      total,
      progress: total ? (loaded / total) : undefined,
      bytes: progressBytes,
      rate: rate ? rate : undefined,
      estimated: rate && total && inRange ? (total - loaded) / rate : undefined,
      event: e
    };

    data[isDownloadStream ? 'download' : 'upload'] = true;

    listener(data);
  };
}

const isXHRAdapterSupported = typeof XMLHttpRequest !== 'undefined';

/* harmony default export */ const xhr = (isXHRAdapterSupported && function (config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    let requestData = config.data;
    const requestHeaders = core_AxiosHeaders.from(config.headers).normalize();
    let {responseType, withXSRFToken} = config;
    let onCanceled;
    function done() {
      if (config.cancelToken) {
        config.cancelToken.unsubscribe(onCanceled);
      }

      if (config.signal) {
        config.signal.removeEventListener('abort', onCanceled);
      }
    }

    let contentType;

    if (utils.isFormData(requestData)) {
      if (platform.hasStandardBrowserEnv || platform.hasStandardBrowserWebWorkerEnv) {
        requestHeaders.setContentType(false); // Let the browser set it
      } else if ((contentType = requestHeaders.getContentType()) !== false) {
        // fix semicolon duplication issue for ReactNative FormData implementation
        const [type, ...tokens] = contentType ? contentType.split(';').map(token => token.trim()).filter(Boolean) : [];
        requestHeaders.setContentType([type || 'multipart/form-data', ...tokens].join('; '));
      }
    }

    let request = new XMLHttpRequest();

    // HTTP basic authentication
    if (config.auth) {
      const username = config.auth.username || '';
      const password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : '';
      requestHeaders.set('Authorization', 'Basic ' + btoa(username + ':' + password));
    }

    const fullPath = buildFullPath(config.baseURL, config.url);

    request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    function onloadend() {
      if (!request) {
        return;
      }
      // Prepare the response
      const responseHeaders = core_AxiosHeaders.from(
        'getAllResponseHeaders' in request && request.getAllResponseHeaders()
      );
      const responseData = !responseType || responseType === 'text' || responseType === 'json' ?
        request.responseText : request.response;
      const response = {
        data: responseData,
        status: request.status,
        statusText: request.statusText,
        headers: responseHeaders,
        config,
        request
      };

      settle(function _resolve(value) {
        resolve(value);
        done();
      }, function _reject(err) {
        reject(err);
        done();
      }, response);

      // Clean up request
      request = null;
    }

    if ('onloadend' in request) {
      // Use onloadend if available
      request.onloadend = onloadend;
    } else {
      // Listen for ready state to emulate onloadend
      request.onreadystatechange = function handleLoad() {
        if (!request || request.readyState !== 4) {
          return;
        }

        // The request errored out and we didn't get a response, this will be
        // handled by onerror instead
        // With one exception: request that using file: protocol, most browsers
        // will return status as 0 even though it's a successful request
        if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
          return;
        }
        // readystate handler is calling before onerror or ontimeout handlers,
        // so we should call onloadend on the next 'tick'
        setTimeout(onloadend);
      };
    }

    // Handle browser request cancellation (as opposed to a manual cancellation)
    request.onabort = function handleAbort() {
      if (!request) {
        return;
      }

      reject(new core_AxiosError('Request aborted', core_AxiosError.ECONNABORTED, config, request));

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(new core_AxiosError('Network Error', core_AxiosError.ERR_NETWORK, config, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      let timeoutErrorMessage = config.timeout ? 'timeout of ' + config.timeout + 'ms exceeded' : 'timeout exceeded';
      const transitional = config.transitional || defaults_transitional;
      if (config.timeoutErrorMessage) {
        timeoutErrorMessage = config.timeoutErrorMessage;
      }
      reject(new core_AxiosError(
        timeoutErrorMessage,
        transitional.clarifyTimeoutError ? core_AxiosError.ETIMEDOUT : core_AxiosError.ECONNABORTED,
        config,
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if(platform.hasStandardBrowserEnv) {
      withXSRFToken && utils.isFunction(withXSRFToken) && (withXSRFToken = withXSRFToken(config));

      if (withXSRFToken || (withXSRFToken !== false && isURLSameOrigin(fullPath))) {
        // Add xsrf header
        const xsrfValue = config.xsrfHeaderName && config.xsrfCookieName && cookies.read(config.xsrfCookieName);

        if (xsrfValue) {
          requestHeaders.set(config.xsrfHeaderName, xsrfValue);
        }
      }
    }

    // Remove Content-Type if data is undefined
    requestData === undefined && requestHeaders.setContentType(null);

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders.toJSON(), function setRequestHeader(val, key) {
        request.setRequestHeader(key, val);
      });
    }

    // Add withCredentials to request if needed
    if (!utils.isUndefined(config.withCredentials)) {
      request.withCredentials = !!config.withCredentials;
    }

    // Add responseType to request if needed
    if (responseType && responseType !== 'json') {
      request.responseType = config.responseType;
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', progressEventReducer(config.onDownloadProgress, true));
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', progressEventReducer(config.onUploadProgress));
    }

    if (config.cancelToken || config.signal) {
      // Handle cancellation
      // eslint-disable-next-line func-names
      onCanceled = cancel => {
        if (!request) {
          return;
        }
        reject(!cancel || cancel.type ? new cancel_CanceledError(null, config, request) : cancel);
        request.abort();
        request = null;
      };

      config.cancelToken && config.cancelToken.subscribe(onCanceled);
      if (config.signal) {
        config.signal.aborted ? onCanceled() : config.signal.addEventListener('abort', onCanceled);
      }
    }

    const protocol = parseProtocol(fullPath);

    if (protocol && platform.protocols.indexOf(protocol) === -1) {
      reject(new core_AxiosError('Unsupported protocol ' + protocol + ':', core_AxiosError.ERR_BAD_REQUEST, config));
      return;
    }


    // Send the request
    request.send(requestData || null);
  });
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/adapters/adapters.js





const knownAdapters = {
  http: helpers_null,
  xhr: xhr
}

utils.forEach(knownAdapters, (fn, value) => {
  if (fn) {
    try {
      Object.defineProperty(fn, 'name', {value});
    } catch (e) {
      // eslint-disable-next-line no-empty
    }
    Object.defineProperty(fn, 'adapterName', {value});
  }
});

const renderReason = (reason) => `- ${reason}`;

const isResolvedHandle = (adapter) => utils.isFunction(adapter) || adapter === null || adapter === false;

/* harmony default export */ const adapters = ({
  getAdapter: (adapters) => {
    adapters = utils.isArray(adapters) ? adapters : [adapters];

    const {length} = adapters;
    let nameOrAdapter;
    let adapter;

    const rejectedReasons = {};

    for (let i = 0; i < length; i++) {
      nameOrAdapter = adapters[i];
      let id;

      adapter = nameOrAdapter;

      if (!isResolvedHandle(nameOrAdapter)) {
        adapter = knownAdapters[(id = String(nameOrAdapter)).toLowerCase()];

        if (adapter === undefined) {
          throw new core_AxiosError(`Unknown adapter '${id}'`);
        }
      }

      if (adapter) {
        break;
      }

      rejectedReasons[id || '#' + i] = adapter;
    }

    if (!adapter) {

      const reasons = Object.entries(rejectedReasons)
        .map(([id, state]) => `adapter ${id} ` +
          (state === false ? 'is not supported by the environment' : 'is not available in the build')
        );

      let s = length ?
        (reasons.length > 1 ? 'since :\n' + reasons.map(renderReason).join('\n') : ' ' + renderReason(reasons[0])) :
        'as no adapter specified';

      throw new core_AxiosError(
        `There is no suitable adapter to dispatch the request ` + s,
        'ERR_NOT_SUPPORT'
      );
    }

    return adapter;
  },
  adapters: knownAdapters
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/dispatchRequest.js









/**
 * Throws a `CanceledError` if cancellation has been requested.
 *
 * @param {Object} config The config that is to be used for the request
 *
 * @returns {void}
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }

  if (config.signal && config.signal.aborted) {
    throw new cancel_CanceledError(null, config);
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 *
 * @returns {Promise} The Promise to be fulfilled
 */
function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  config.headers = core_AxiosHeaders.from(config.headers);

  // Transform request data
  config.data = transformData.call(
    config,
    config.transformRequest
  );

  if (['post', 'put', 'patch'].indexOf(config.method) !== -1) {
    config.headers.setContentType('application/x-www-form-urlencoded', false);
  }

  const adapter = adapters.getAdapter(config.adapter || lib_defaults.adapter);

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData.call(
      config,
      config.transformResponse,
      response
    );

    response.headers = core_AxiosHeaders.from(response.headers);

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData.call(
          config,
          config.transformResponse,
          reason.response
        );
        reason.response.headers = core_AxiosHeaders.from(reason.response.headers);
      }
    }

    return Promise.reject(reason);
  });
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/mergeConfig.js





const headersToObject = (thing) => thing instanceof core_AxiosHeaders ? thing.toJSON() : thing;

/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 *
 * @returns {Object} New object resulting from merging config2 to config1
 */
function mergeConfig(config1, config2) {
  // eslint-disable-next-line no-param-reassign
  config2 = config2 || {};
  const config = {};

  function getMergedValue(target, source, caseless) {
    if (utils.isPlainObject(target) && utils.isPlainObject(source)) {
      return utils.merge.call({caseless}, target, source);
    } else if (utils.isPlainObject(source)) {
      return utils.merge({}, source);
    } else if (utils.isArray(source)) {
      return source.slice();
    }
    return source;
  }

  // eslint-disable-next-line consistent-return
  function mergeDeepProperties(a, b, caseless) {
    if (!utils.isUndefined(b)) {
      return getMergedValue(a, b, caseless);
    } else if (!utils.isUndefined(a)) {
      return getMergedValue(undefined, a, caseless);
    }
  }

  // eslint-disable-next-line consistent-return
  function valueFromConfig2(a, b) {
    if (!utils.isUndefined(b)) {
      return getMergedValue(undefined, b);
    }
  }

  // eslint-disable-next-line consistent-return
  function defaultToConfig2(a, b) {
    if (!utils.isUndefined(b)) {
      return getMergedValue(undefined, b);
    } else if (!utils.isUndefined(a)) {
      return getMergedValue(undefined, a);
    }
  }

  // eslint-disable-next-line consistent-return
  function mergeDirectKeys(a, b, prop) {
    if (prop in config2) {
      return getMergedValue(a, b);
    } else if (prop in config1) {
      return getMergedValue(undefined, a);
    }
  }

  const mergeMap = {
    url: valueFromConfig2,
    method: valueFromConfig2,
    data: valueFromConfig2,
    baseURL: defaultToConfig2,
    transformRequest: defaultToConfig2,
    transformResponse: defaultToConfig2,
    paramsSerializer: defaultToConfig2,
    timeout: defaultToConfig2,
    timeoutMessage: defaultToConfig2,
    withCredentials: defaultToConfig2,
    withXSRFToken: defaultToConfig2,
    adapter: defaultToConfig2,
    responseType: defaultToConfig2,
    xsrfCookieName: defaultToConfig2,
    xsrfHeaderName: defaultToConfig2,
    onUploadProgress: defaultToConfig2,
    onDownloadProgress: defaultToConfig2,
    decompress: defaultToConfig2,
    maxContentLength: defaultToConfig2,
    maxBodyLength: defaultToConfig2,
    beforeRedirect: defaultToConfig2,
    transport: defaultToConfig2,
    httpAgent: defaultToConfig2,
    httpsAgent: defaultToConfig2,
    cancelToken: defaultToConfig2,
    socketPath: defaultToConfig2,
    responseEncoding: defaultToConfig2,
    validateStatus: mergeDirectKeys,
    headers: (a, b) => mergeDeepProperties(headersToObject(a), headersToObject(b), true)
  };

  utils.forEach(Object.keys(Object.assign({}, config1, config2)), function computeConfigValue(prop) {
    const merge = mergeMap[prop] || mergeDeepProperties;
    const configValue = merge(config1[prop], config2[prop], prop);
    (utils.isUndefined(configValue) && merge !== mergeDirectKeys) || (config[prop] = configValue);
  });

  return config;
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/env/data.js
const VERSION = "1.6.2";
;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/validator.js





const validators = {};

// eslint-disable-next-line func-names
['object', 'boolean', 'number', 'function', 'string', 'symbol'].forEach((type, i) => {
  validators[type] = function validator(thing) {
    return typeof thing === type || 'a' + (i < 1 ? 'n ' : ' ') + type;
  };
});

const deprecatedWarnings = {};

/**
 * Transitional option validator
 *
 * @param {function|boolean?} validator - set to false if the transitional option has been removed
 * @param {string?} version - deprecated version / removed since version
 * @param {string?} message - some message with additional info
 *
 * @returns {function}
 */
validators.transitional = function transitional(validator, version, message) {
  function formatMessage(opt, desc) {
    return '[Axios v' + VERSION + '] Transitional option \'' + opt + '\'' + desc + (message ? '. ' + message : '');
  }

  // eslint-disable-next-line func-names
  return (value, opt, opts) => {
    if (validator === false) {
      throw new core_AxiosError(
        formatMessage(opt, ' has been removed' + (version ? ' in ' + version : '')),
        core_AxiosError.ERR_DEPRECATED
      );
    }

    if (version && !deprecatedWarnings[opt]) {
      deprecatedWarnings[opt] = true;
      // eslint-disable-next-line no-console
      console.warn(
        formatMessage(
          opt,
          ' has been deprecated since v' + version + ' and will be removed in the near future'
        )
      );
    }

    return validator ? validator(value, opt, opts) : true;
  };
};

/**
 * Assert object's properties type
 *
 * @param {object} options
 * @param {object} schema
 * @param {boolean?} allowUnknown
 *
 * @returns {object}
 */

function assertOptions(options, schema, allowUnknown) {
  if (typeof options !== 'object') {
    throw new core_AxiosError('options must be an object', core_AxiosError.ERR_BAD_OPTION_VALUE);
  }
  const keys = Object.keys(options);
  let i = keys.length;
  while (i-- > 0) {
    const opt = keys[i];
    const validator = schema[opt];
    if (validator) {
      const value = options[opt];
      const result = value === undefined || validator(value, opt, options);
      if (result !== true) {
        throw new core_AxiosError('option ' + opt + ' must be ' + result, core_AxiosError.ERR_BAD_OPTION_VALUE);
      }
      continue;
    }
    if (allowUnknown !== true) {
      throw new core_AxiosError('Unknown option ' + opt, core_AxiosError.ERR_BAD_OPTION);
    }
  }
}

/* harmony default export */ const validator = ({
  assertOptions,
  validators
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/core/Axios.js











const Axios_validators = validator.validators;

/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 *
 * @return {Axios} A new instance of Axios
 */
class Axios {
  constructor(instanceConfig) {
    this.defaults = instanceConfig;
    this.interceptors = {
      request: new core_InterceptorManager(),
      response: new core_InterceptorManager()
    };
  }

  /**
   * Dispatch a request
   *
   * @param {String|Object} configOrUrl The config specific for this request (merged with this.defaults)
   * @param {?Object} config
   *
   * @returns {Promise} The Promise to be fulfilled
   */
  request(configOrUrl, config) {
    /*eslint no-param-reassign:0*/
    // Allow for axios('example/url'[, config]) a la fetch API
    if (typeof configOrUrl === 'string') {
      config = config || {};
      config.url = configOrUrl;
    } else {
      config = configOrUrl || {};
    }

    config = mergeConfig(this.defaults, config);

    const {transitional, paramsSerializer, headers} = config;

    if (transitional !== undefined) {
      validator.assertOptions(transitional, {
        silentJSONParsing: Axios_validators.transitional(Axios_validators.boolean),
        forcedJSONParsing: Axios_validators.transitional(Axios_validators.boolean),
        clarifyTimeoutError: Axios_validators.transitional(Axios_validators.boolean)
      }, false);
    }

    if (paramsSerializer != null) {
      if (utils.isFunction(paramsSerializer)) {
        config.paramsSerializer = {
          serialize: paramsSerializer
        }
      } else {
        validator.assertOptions(paramsSerializer, {
          encode: Axios_validators.function,
          serialize: Axios_validators.function
        }, true);
      }
    }

    // Set config.method
    config.method = (config.method || this.defaults.method || 'get').toLowerCase();

    // Flatten headers
    let contextHeaders = headers && utils.merge(
      headers.common,
      headers[config.method]
    );

    headers && utils.forEach(
      ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
      (method) => {
        delete headers[method];
      }
    );

    config.headers = core_AxiosHeaders.concat(contextHeaders, headers);

    // filter out skipped interceptors
    const requestInterceptorChain = [];
    let synchronousRequestInterceptors = true;
    this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
      if (typeof interceptor.runWhen === 'function' && interceptor.runWhen(config) === false) {
        return;
      }

      synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous;

      requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected);
    });

    const responseInterceptorChain = [];
    this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
      responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected);
    });

    let promise;
    let i = 0;
    let len;

    if (!synchronousRequestInterceptors) {
      const chain = [dispatchRequest.bind(this), undefined];
      chain.unshift.apply(chain, requestInterceptorChain);
      chain.push.apply(chain, responseInterceptorChain);
      len = chain.length;

      promise = Promise.resolve(config);

      while (i < len) {
        promise = promise.then(chain[i++], chain[i++]);
      }

      return promise;
    }

    len = requestInterceptorChain.length;

    let newConfig = config;

    i = 0;

    while (i < len) {
      const onFulfilled = requestInterceptorChain[i++];
      const onRejected = requestInterceptorChain[i++];
      try {
        newConfig = onFulfilled(newConfig);
      } catch (error) {
        onRejected.call(this, error);
        break;
      }
    }

    try {
      promise = dispatchRequest.call(this, newConfig);
    } catch (error) {
      return Promise.reject(error);
    }

    i = 0;
    len = responseInterceptorChain.length;

    while (i < len) {
      promise = promise.then(responseInterceptorChain[i++], responseInterceptorChain[i++]);
    }

    return promise;
  }

  getUri(config) {
    config = mergeConfig(this.defaults, config);
    const fullPath = buildFullPath(config.baseURL, config.url);
    return buildURL(fullPath, config.params, config.paramsSerializer);
  }
}

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(mergeConfig(config || {}, {
      method,
      url,
      data: (config || {}).data
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/

  function generateHTTPMethod(isForm) {
    return function httpMethod(url, data, config) {
      return this.request(mergeConfig(config || {}, {
        method,
        headers: isForm ? {
          'Content-Type': 'multipart/form-data'
        } : {},
        url,
        data
      }));
    };
  }

  Axios.prototype[method] = generateHTTPMethod();

  Axios.prototype[method + 'Form'] = generateHTTPMethod(true);
});

/* harmony default export */ const core_Axios = (Axios);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/cancel/CancelToken.js




/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @param {Function} executor The executor function.
 *
 * @returns {CancelToken}
 */
class CancelToken {
  constructor(executor) {
    if (typeof executor !== 'function') {
      throw new TypeError('executor must be a function.');
    }

    let resolvePromise;

    this.promise = new Promise(function promiseExecutor(resolve) {
      resolvePromise = resolve;
    });

    const token = this;

    // eslint-disable-next-line func-names
    this.promise.then(cancel => {
      if (!token._listeners) return;

      let i = token._listeners.length;

      while (i-- > 0) {
        token._listeners[i](cancel);
      }
      token._listeners = null;
    });

    // eslint-disable-next-line func-names
    this.promise.then = onfulfilled => {
      let _resolve;
      // eslint-disable-next-line func-names
      const promise = new Promise(resolve => {
        token.subscribe(resolve);
        _resolve = resolve;
      }).then(onfulfilled);

      promise.cancel = function reject() {
        token.unsubscribe(_resolve);
      };

      return promise;
    };

    executor(function cancel(message, config, request) {
      if (token.reason) {
        // Cancellation has already been requested
        return;
      }

      token.reason = new cancel_CanceledError(message, config, request);
      resolvePromise(token.reason);
    });
  }

  /**
   * Throws a `CanceledError` if cancellation has been requested.
   */
  throwIfRequested() {
    if (this.reason) {
      throw this.reason;
    }
  }

  /**
   * Subscribe to the cancel signal
   */

  subscribe(listener) {
    if (this.reason) {
      listener(this.reason);
      return;
    }

    if (this._listeners) {
      this._listeners.push(listener);
    } else {
      this._listeners = [listener];
    }
  }

  /**
   * Unsubscribe from the cancel signal
   */

  unsubscribe(listener) {
    if (!this._listeners) {
      return;
    }
    const index = this._listeners.indexOf(listener);
    if (index !== -1) {
      this._listeners.splice(index, 1);
    }
  }

  /**
   * Returns an object that contains a new `CancelToken` and a function that, when called,
   * cancels the `CancelToken`.
   */
  static source() {
    let cancel;
    const token = new CancelToken(function executor(c) {
      cancel = c;
    });
    return {
      token,
      cancel
    };
  }
}

/* harmony default export */ const cancel_CancelToken = (CancelToken);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/spread.js


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 *
 * @returns {Function}
 */
function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/isAxiosError.js




/**
 * Determines whether the payload is an error thrown by Axios
 *
 * @param {*} payload The value to test
 *
 * @returns {boolean} True if the payload is an error thrown by Axios, otherwise false
 */
function isAxiosError(payload) {
  return utils.isObject(payload) && (payload.isAxiosError === true);
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/helpers/HttpStatusCode.js
const HttpStatusCode = {
  Continue: 100,
  SwitchingProtocols: 101,
  Processing: 102,
  EarlyHints: 103,
  Ok: 200,
  Created: 201,
  Accepted: 202,
  NonAuthoritativeInformation: 203,
  NoContent: 204,
  ResetContent: 205,
  PartialContent: 206,
  MultiStatus: 207,
  AlreadyReported: 208,
  ImUsed: 226,
  MultipleChoices: 300,
  MovedPermanently: 301,
  Found: 302,
  SeeOther: 303,
  NotModified: 304,
  UseProxy: 305,
  Unused: 306,
  TemporaryRedirect: 307,
  PermanentRedirect: 308,
  BadRequest: 400,
  Unauthorized: 401,
  PaymentRequired: 402,
  Forbidden: 403,
  NotFound: 404,
  MethodNotAllowed: 405,
  NotAcceptable: 406,
  ProxyAuthenticationRequired: 407,
  RequestTimeout: 408,
  Conflict: 409,
  Gone: 410,
  LengthRequired: 411,
  PreconditionFailed: 412,
  PayloadTooLarge: 413,
  UriTooLong: 414,
  UnsupportedMediaType: 415,
  RangeNotSatisfiable: 416,
  ExpectationFailed: 417,
  ImATeapot: 418,
  MisdirectedRequest: 421,
  UnprocessableEntity: 422,
  Locked: 423,
  FailedDependency: 424,
  TooEarly: 425,
  UpgradeRequired: 426,
  PreconditionRequired: 428,
  TooManyRequests: 429,
  RequestHeaderFieldsTooLarge: 431,
  UnavailableForLegalReasons: 451,
  InternalServerError: 500,
  NotImplemented: 501,
  BadGateway: 502,
  ServiceUnavailable: 503,
  GatewayTimeout: 504,
  HttpVersionNotSupported: 505,
  VariantAlsoNegotiates: 506,
  InsufficientStorage: 507,
  LoopDetected: 508,
  NotExtended: 510,
  NetworkAuthenticationRequired: 511,
};

Object.entries(HttpStatusCode).forEach(([key, value]) => {
  HttpStatusCode[value] = key;
});

/* harmony default export */ const helpers_HttpStatusCode = (HttpStatusCode);

;// CONCATENATED MODULE: ../../../../../../../../node_modules/axios/lib/axios.js




















/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 *
 * @returns {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  const context = new core_Axios(defaultConfig);
  const instance = bind(core_Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, core_Axios.prototype, context, {allOwnKeys: true});

  // Copy context to instance
  utils.extend(instance, context, null, {allOwnKeys: true});

  // Factory for creating new instances
  instance.create = function create(instanceConfig) {
    return createInstance(mergeConfig(defaultConfig, instanceConfig));
  };

  return instance;
}

// Create the default instance to be exported
const axios = createInstance(lib_defaults);

// Expose Axios class to allow class inheritance
axios.Axios = core_Axios;

// Expose Cancel & CancelToken
axios.CanceledError = cancel_CanceledError;
axios.CancelToken = cancel_CancelToken;
axios.isCancel = isCancel;
axios.VERSION = VERSION;
axios.toFormData = helpers_toFormData;

// Expose AxiosError class
axios.AxiosError = core_AxiosError;

// alias for CanceledError for backward compatibility
axios.Cancel = axios.CanceledError;

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};

axios.spread = spread;

// Expose isAxiosError
axios.isAxiosError = isAxiosError;

// Expose mergeConfig
axios.mergeConfig = mergeConfig;

axios.AxiosHeaders = core_AxiosHeaders;

axios.formToJSON = thing => helpers_formDataToJSON(utils.isHTMLForm(thing) ? new FormData(thing) : thing);

axios.getAdapter = adapters.getAdapter;

axios.HttpStatusCode = helpers_HttpStatusCode;

axios.default = axios;

// this module should only have a default export
/* harmony default export */ const lib_axios = (axios);

// EXTERNAL MODULE: ../../../../../../../../node_modules/p-queue/dist/index.js
var dist = __webpack_require__(781);
;// CONCATENATED MODULE: ./src/connect/Connector.ts


const TIMEOUT_SEC = 30;
class Connector {
    apiConfig;
    queue = new dist/* default */.Z({ concurrency: 1 });
    _isSaving = false;
    errorListeners = [];
    constructor(apiConfig) {
        this.apiConfig = apiConfig;
        this.finishSaving = this.finishSaving.bind(this);
    }
    addErrorListener(errorListener) {
        this.errorListeners.push(errorListener);
    }
    removeErrorListener(errorListener) {
        const index = this.errorListeners.indexOf(errorListener);
        if (index >= 0) {
            this.errorListeners.splice(index, 1);
        }
    }
    get processingSize() {
        return this.queue.pending + this.queue.size;
    }
    get isSaving() {
        return this._isSaving;
    }
    beginSaving() {
        this._isSaving = true;
    }
    finishSaving() {
        this._isSaving = false;
    }
    async createResultPeriod(callback = undefined) {
        this.addToQueue(async () => {
            const data = await this.executeAPIRequest(this.apiConfig.createPresencePeriodURL);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    // eslint-disable-next-line
    async loadPresenceEntries(params) {
        const res = await lib_axios.get(this.apiConfig.loadPresenceEntriesURL, { params });
        return res.data;
    }
    async loadStatistics() {
        const res = await lib_axios.get(this.apiConfig.loadStatisticsURL);
        return res.data;
    }
    // eslint-disable-next-line
    async loadPresence() {
        const res = await lib_axios.get(this.apiConfig.loadPresenceURL);
        return res.data;
    }
    async updatePresence(id, statuses, has_checkout, verification_icon_data, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { data: JSON.stringify({ id, statuses, has_checkout, verification_icon_data }) };
            const data = await this.executeAPIRequest(this.apiConfig.updatePresenceURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    async updatePresenceGlobalSelfRegistration(id, self_registration_disabled, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { data: JSON.stringify({ id, self_registration_disabled }) };
            const data = await this.executeAPIRequest(this.apiConfig.updatePresenceGlobalSelfRegistrationURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    async loadRegisteredPresenceEntryStatuses() {
        const res = await lib_axios.get(this.apiConfig.loadRegisteredPresenceEntryStatusesURL);
        return res.data;
    }
    async updatePresencePeriod(periodId, label, selfRegistrationDisabled, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'period_label': label, 'period_self_registration_disabled': selfRegistrationDisabled };
            const data = await this.executeAPIRequest(this.apiConfig.updatePresencePeriodURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    async deletePresencePeriod(periodId, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId };
            const data = await this.executeAPIRequest(this.apiConfig.deletePresencePeriodURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    async savePresenceEntry(periodId, userId, statusId, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'user_id': userId, 'status_id': statusId };
            const data = await this.executeAPIRequest(this.apiConfig.savePresenceEntryURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    async bulkSavePresenceEntries(periodId, statusId, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'status_id': statusId };
            const data = await this.executeAPIRequest(this.apiConfig.bulkSavePresenceEntriesURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    async togglePresenceEntryCheckout(periodId, userId, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'user_id': userId };
            const data = await this.executeAPIRequest(this.apiConfig.togglePresenceEntryCheckoutURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }
    addToQueue(callback) {
        this.queue.add(async () => {
            await callback();
        });
        this.queue.onIdle().then(this.finishSaving);
    }
    async executeAPIRequest(apiURL, parameters = {}) {
        this.beginSaving();
        const formData = new FormData();
        if (this.apiConfig.csrfToken) {
            formData.set('_csrf_token', this.apiConfig.csrfToken);
        }
        for (const [key, value] of Object.entries(parameters)) {
            formData.set(key, value);
        }
        try {
            const res = await lib_axios.post(apiURL, formData, { timeout: TIMEOUT_SEC * 1000 });
            if (typeof res.data === 'object') {
                return res.data;
            }
            else if (typeof res.data === 'string' && res.data.indexOf('formLogin') !== -1) {
                throw { 'type': 'LoggedOut' };
            }
            else {
                throw { 'type': 'Unknown' };
            }
        }
        catch (err) {
            let error;
            if (err?.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
                error = { 'type': 'Timeout' };
            }
            else if (!!err?.response?.data?.error) {
                error = err.response.data.error;
            }
            else if (!!err?.type) {
                error = err;
            }
            else {
                error = { 'type': 'Unknown' };
            }
            this.errorListeners.forEach(errorListener => errorListener.setError(error));
        }
    }
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/TitleControl.vue?vue&type=template&id=22f05c55
var TitleControlvue_type_template_id_22f05c55_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.isEditable)?_c('b-input',{staticClass:"mod-input mod-trans mod-pad",attrs:{"type":"text","required":"","autocomplete":"off","disabled":_vm.disabled},on:{"focus":function($event){return _vm.$emit('select')}},model:{value:(_vm.status.title),callback:function ($$v) {_vm.$set(_vm.status, "title", $$v)},expression:"status.title"}}):_c('span',[_vm._v(_vm._s(_vm.statusTitle))])
}
var TitleControlvue_type_template_id_22f05c55_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/TitleControl.vue?vue&type=script&lang=ts


let TitleControl = class TitleControl extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    status;
    statusTitle;
    disabled;
    isEditable;
};
__decorate([
    Prop({ type: Object, required: true })
], TitleControl.prototype, "status", void 0);
__decorate([
    Prop({ type: String, default: '' })
], TitleControl.prototype, "statusTitle", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], TitleControl.prototype, "disabled", void 0);
__decorate([
    Prop({ type: Boolean, default: true })
], TitleControl.prototype, "isEditable", void 0);
TitleControl = __decorate([
    vue_class_component_esm({
        name: 'title-control'
    })
], TitleControl);
/* harmony default export */ const TitleControlvue_type_script_lang_ts = (TitleControl);

;// CONCATENATED MODULE: ./src/components/builder/TitleControl.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_TitleControlvue_type_script_lang_ts = (TitleControlvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/runtime/componentNormalizer.js
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent(
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */,
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options =
    typeof scriptExports === 'function' ? scriptExports.options : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) {
    // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () {
          injectStyles.call(
            this,
            (options.functional ? this.parent : this).$root.$options.shadowRoot
          )
        }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection(h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing ? [].concat(existing, hook) : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}

;// CONCATENATED MODULE: ./src/components/builder/TitleControl.vue





/* normalize component */
;
var component = normalizeComponent(
  builder_TitleControlvue_type_script_lang_ts,
  TitleControlvue_type_template_id_22f05c55_render,
  TitleControlvue_type_template_id_22f05c55_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const builder_TitleControl = (component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/AliasControl.vue?vue&type=template&id=fbe0cc68
var AliasControlvue_type_template_id_fbe0cc68_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.isEditable)?_c('select',{directives:[{name:"model",rawName:"v-model",value:(_vm.status.aliasses),expression:"status.aliasses"}],staticClass:"form-control mod-trans mod-select",attrs:{"disabled":_vm.isSelectDisabled},on:{"focus":function($event){return _vm.$emit('select')},"change":function($event){var $$selectedVal = Array.prototype.filter.call($event.target.options,function(o){return o.selected}).map(function(o){var val = "_value" in o ? o._value : o.value;return val}); _vm.$set(_vm.status, "aliasses", $event.target.multiple ? $$selectedVal : $$selectedVal[0])}}},_vm._l((_vm.fixedStatusDefaults),function(statusDefault,index){return _c('option',{key:`fs-${index}`,domProps:{"value":statusDefault.id}},[_vm._v(_vm._s(statusDefault.title))])}),0):_c('span',[_vm._v(_vm._s(_vm.aliasTitle))])
}
var AliasControlvue_type_template_id_fbe0cc68_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/AliasControl.vue?vue&type=script&lang=ts


let AliasControl = class AliasControl extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    fixedStatusDefaults;
    status;
    aliasTitle;
    isSelectDisabled;
    isEditable;
};
__decorate([
    Prop({ type: Array, default: () => [] })
], AliasControl.prototype, "fixedStatusDefaults", void 0);
__decorate([
    Prop({ type: Object, required: true })
], AliasControl.prototype, "status", void 0);
__decorate([
    Prop({ type: String, default: '' })
], AliasControl.prototype, "aliasTitle", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], AliasControl.prototype, "isSelectDisabled", void 0);
__decorate([
    Prop({ type: Boolean, default: true })
], AliasControl.prototype, "isEditable", void 0);
AliasControl = __decorate([
    vue_class_component_esm({
        name: 'alias-control'
    })
], AliasControl);
/* harmony default export */ const AliasControlvue_type_script_lang_ts = (AliasControl);

;// CONCATENATED MODULE: ./src/components/builder/AliasControl.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_AliasControlvue_type_script_lang_ts = (AliasControlvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ./src/components/builder/AliasControl.vue





/* normalize component */
;
var AliasControl_component = normalizeComponent(
  builder_AliasControlvue_type_script_lang_ts,
  AliasControlvue_type_template_id_fbe0cc68_render,
  AliasControlvue_type_template_id_fbe0cc68_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const builder_AliasControl = (AliasControl_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ColorControl.vue?vue&type=template&id=1a65bc8c
var ColorControlvue_type_template_id_1a65bc8c_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('button',{staticClass:"btn-color",class:[{'is-selected': _vm.selected}, _vm.color],attrs:{"id":`color-${_vm.id}`,"disabled":_vm.disabled},on:{"focus":function($event){return _vm.$emit('select')}}}),_c('color-picker',{attrs:{"target":`color-${_vm.id}`,"selected-color":_vm.color,"triggers":"click blur","placement":"right"},on:{"color-selected":function($event){return _vm.$emit('color-selected', $event)}}})],1)
}
var ColorControlvue_type_template_id_1a65bc8c_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ColorPicker.vue?vue&type=template&id=3c855671
var ColorPickervue_type_template_id_3c855671_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('b-popover',{attrs:{"target":_vm.target,"triggers":_vm.triggers,"placement":_vm.placement}},[_c('div',{staticClass:"presence-swatches"},[_vm._l((_vm.variants),function(variant){return [_vm._l((_vm.colors),function(color){return [_c('button',{key:`color-swatch-${color}-${variant}`,class:[`btn-color mod-swatch ${color}-${variant}`, {'is-selected': _vm.selectedColor === `${color}-${variant}`}],on:{"click":function($event){$event.stopPropagation();return _vm.$emit('color-selected', `${color}-${variant}`)}}})]})]})],2)])
}
var ColorPickervue_type_template_id_3c855671_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ColorPicker.vue?vue&type=script&lang=ts


let ColorPicker = class ColorPicker extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    variants = [100, 300, 500, 700, 900];
    colors = ['pink', 'blue', 'cyan', 'teal', 'green', 'light-green', 'lime', 'yellow', 'amber', 'deep-orange', 'grey'];
    target;
    triggers;
    placement;
    selectedColor;
};
__decorate([
    Prop({ type: String, required: true })
], ColorPicker.prototype, "target", void 0);
__decorate([
    Prop({ type: String, default: 'click' })
], ColorPicker.prototype, "triggers", void 0);
__decorate([
    Prop({ type: String, default: 'bottom' })
], ColorPicker.prototype, "placement", void 0);
__decorate([
    Prop({ type: String, default: '' })
], ColorPicker.prototype, "selectedColor", void 0);
ColorPicker = __decorate([
    vue_class_component_esm({
        name: 'color-picker'
    })
], ColorPicker);
/* harmony default export */ const ColorPickervue_type_script_lang_ts = (ColorPicker);

;// CONCATENATED MODULE: ./src/components/builder/ColorPicker.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_ColorPickervue_type_script_lang_ts = (ColorPickervue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ColorPicker.vue?vue&type=style&index=0&id=3c855671&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/builder/ColorPicker.vue?vue&type=style&index=0&id=3c855671&prod&lang=css

;// CONCATENATED MODULE: ./src/components/builder/ColorPicker.vue



;


/* normalize component */

var ColorPicker_component = normalizeComponent(
  builder_ColorPickervue_type_script_lang_ts,
  ColorPickervue_type_template_id_3c855671_render,
  ColorPickervue_type_template_id_3c855671_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const builder_ColorPicker = (ColorPicker_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ColorControl.vue?vue&type=script&lang=ts



let ColorControl = class ColorControl extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    id;
    disabled;
    selected;
    color;
};
__decorate([
    Prop({ type: Number, default: 0 })
], ColorControl.prototype, "id", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], ColorControl.prototype, "disabled", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], ColorControl.prototype, "selected", void 0);
__decorate([
    Prop({ type: String, default: '' })
], ColorControl.prototype, "color", void 0);
ColorControl = __decorate([
    vue_class_component_esm({
        name: 'color-control',
        components: { ColorPicker: builder_ColorPicker }
    })
], ColorControl);
/* harmony default export */ const ColorControlvue_type_script_lang_ts = (ColorControl);

;// CONCATENATED MODULE: ./src/components/builder/ColorControl.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_ColorControlvue_type_script_lang_ts = (ColorControlvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ColorControl.vue?vue&type=style&index=0&id=1a65bc8c&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/builder/ColorControl.vue?vue&type=style&index=0&id=1a65bc8c&prod&lang=css

;// CONCATENATED MODULE: ./src/components/builder/ColorControl.vue



;


/* normalize component */

var ColorControl_component = normalizeComponent(
  builder_ColorControlvue_type_script_lang_ts,
  ColorControlvue_type_template_id_1a65bc8c_render,
  ColorControlvue_type_template_id_1a65bc8c_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const builder_ColorControl = (ColorControl_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SelectionControls.vue?vue&type=template&id=1c8c95d9
var SelectionControlsvue_type_template_id_1c8c95d9_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('button',{staticClass:"btn btn-default btn-sm mod-status-action",attrs:{"id":`btn-up-${_vm.id}`,"title":_vm.$t('move-up'),"disabled":_vm.isUpDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('move-up')},"focus":function($event){return _vm.$emit('select')}}},[_c('i',{staticClass:"fa fa-arrow-up",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('move-up')))])]),_c('button',{staticClass:"btn btn-default btn-sm mod-status-action",attrs:{"id":`btn-down-${_vm.id}`,"title":_vm.$t('move-down'),"disabled":_vm.isDownDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('move-down')},"focus":function($event){return _vm.$emit('select')}}},[_c('i',{staticClass:"fa fa-arrow-down",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('move-down')))])]),_c('button',{staticClass:"btn btn-default btn-sm mod-status-action mod-remove",attrs:{"title":_vm.$t('remove'),"disabled":_vm.isRemoveDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('remove')},"focus":function($event){return _vm.$emit('select')}}},[_c('i',{staticClass:"fa fa-minus-circle",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('remove')))])])])
}
var SelectionControlsvue_type_template_id_1c8c95d9_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SelectionControls.vue?vue&type=script&lang=ts


let SelectionControls = class SelectionControls extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    id;
    isUpDisabled;
    isDownDisabled;
    isRemoveDisabled;
};
__decorate([
    Prop({ type: Number, default: 0 })
], SelectionControls.prototype, "id", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], SelectionControls.prototype, "isUpDisabled", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], SelectionControls.prototype, "isDownDisabled", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], SelectionControls.prototype, "isRemoveDisabled", void 0);
SelectionControls = __decorate([
    vue_class_component_esm({
        name: 'selection-controls'
    })
], SelectionControls);
/* harmony default export */ const SelectionControlsvue_type_script_lang_ts = (SelectionControls);

;// CONCATENATED MODULE: ./src/components/builder/SelectionControls.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_SelectionControlsvue_type_script_lang_ts = (SelectionControlsvue_type_script_lang_ts); 
// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SelectionControls.vue?vue&type=custom&index=0&blockType=i18n
var SelectionControlsvue_type_custom_index_0_blockType_i18n = __webpack_require__(120);
var SelectionControlsvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(SelectionControlsvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/builder/SelectionControls.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const builder_SelectionControlsvue_type_custom_index_0_blockType_i18n = ((SelectionControlsvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/builder/SelectionControls.vue





/* normalize component */
;
var SelectionControls_component = normalizeComponent(
  builder_SelectionControlsvue_type_script_lang_ts,
  SelectionControlsvue_type_template_id_1c8c95d9_render,
  SelectionControlsvue_type_template_id_1c8c95d9_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof builder_SelectionControlsvue_type_custom_index_0_blockType_i18n === 'function') builder_SelectionControlsvue_type_custom_index_0_blockType_i18n(SelectionControls_component)

/* harmony default export */ const builder_SelectionControls = (SelectionControls_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/NewStatusControls.vue?vue&type=template&id=4faf5460
var NewStatusControlsvue_type_template_id_4faf5460_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('button',{staticClass:"btn btn-default btn-sm mod-status-action",attrs:{"title":_vm.$t('save'),"disabled":_vm.isSavingDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('save')}}},[_c('i',{staticClass:"fa fa-check-circle",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('save')))])]),_c('button',{staticClass:"btn btn-default btn-sm mod-status-action mod-cancel",attrs:{"title":_vm.$t('cancel')},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('cancel')}}},[_c('i',{staticClass:"fa fa-minus-circle",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('cancel')))])])])
}
var NewStatusControlsvue_type_template_id_4faf5460_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/NewStatusControls.vue?vue&type=script&lang=ts


let NewStatusControls = class NewStatusControls extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isSavingDisabled;
};
__decorate([
    Prop({ type: Boolean, default: false })
], NewStatusControls.prototype, "isSavingDisabled", void 0);
NewStatusControls = __decorate([
    vue_class_component_esm({
        name: 'new-status-controls'
    })
], NewStatusControls);
/* harmony default export */ const NewStatusControlsvue_type_script_lang_ts = (NewStatusControls);

;// CONCATENATED MODULE: ./src/components/builder/NewStatusControls.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_NewStatusControlsvue_type_script_lang_ts = (NewStatusControlsvue_type_script_lang_ts); 
// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/NewStatusControls.vue?vue&type=custom&index=0&blockType=i18n
var NewStatusControlsvue_type_custom_index_0_blockType_i18n = __webpack_require__(898);
var NewStatusControlsvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(NewStatusControlsvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/builder/NewStatusControls.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const builder_NewStatusControlsvue_type_custom_index_0_blockType_i18n = ((NewStatusControlsvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/builder/NewStatusControls.vue





/* normalize component */
;
var NewStatusControls_component = normalizeComponent(
  builder_NewStatusControlsvue_type_script_lang_ts,
  NewStatusControlsvue_type_template_id_4faf5460_render,
  NewStatusControlsvue_type_template_id_4faf5460_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof builder_NewStatusControlsvue_type_custom_index_0_blockType_i18n === 'function') builder_NewStatusControlsvue_type_custom_index_0_blockType_i18n(NewStatusControls_component)

/* harmony default export */ const builder_NewStatusControls = (NewStatusControls_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SelectionPreview.vue?vue&type=template&id=d9b9df98
var SelectionPreviewvue_type_template_id_d9b9df98_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('b-tr',[_c('b-th',{staticClass:"u-font-normal"},[_vm._v(_vm._s(_vm.$t('display')))]),_c('b-th',{attrs:{"colspan":"3"}},[_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap",staticStyle:{"padding":"4px 0"}},_vm._l((_vm.presenceStatuses),function(status,index){return _c('div',{key:`status-${index}`,staticClass:"color-code",class:[status.color]},[_c('span',[_vm._v(_vm._s(status.code))])])}),0)])],1)
}
var SelectionPreviewvue_type_template_id_d9b9df98_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SelectionPreview.vue?vue&type=script&lang=ts


let SelectionPreview = class SelectionPreview extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    presenceStatuses;
};
__decorate([
    Prop({ type: Array, default: () => [] })
], SelectionPreview.prototype, "presenceStatuses", void 0);
SelectionPreview = __decorate([
    vue_class_component_esm({
        name: 'selection-preview'
    })
], SelectionPreview);
/* harmony default export */ const SelectionPreviewvue_type_script_lang_ts = (SelectionPreview);

;// CONCATENATED MODULE: ./src/components/builder/SelectionPreview.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_SelectionPreviewvue_type_script_lang_ts = (SelectionPreviewvue_type_script_lang_ts); 
// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SelectionPreview.vue?vue&type=custom&index=0&blockType=i18n
var SelectionPreviewvue_type_custom_index_0_blockType_i18n = __webpack_require__(822);
var SelectionPreviewvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(SelectionPreviewvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/builder/SelectionPreview.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const builder_SelectionPreviewvue_type_custom_index_0_blockType_i18n = ((SelectionPreviewvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/builder/SelectionPreview.vue





/* normalize component */
;
var SelectionPreview_component = normalizeComponent(
  builder_SelectionPreviewvue_type_script_lang_ts,
  SelectionPreviewvue_type_template_id_d9b9df98_render,
  SelectionPreviewvue_type_template_id_d9b9df98_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof builder_SelectionPreviewvue_type_custom_index_0_blockType_i18n === 'function') builder_SelectionPreviewvue_type_custom_index_0_blockType_i18n(SelectionPreview_component)

/* harmony default export */ const builder_SelectionPreview = (SelectionPreview_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SaveControl.vue?vue&type=template&id=e9994b3e
var SaveControlvue_type_template_id_e9994b3e_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('button',{staticClass:"btn btn-primary mod-presence-save",attrs:{"disabled":_vm.isSaving || _vm.isDisabled},on:{"click":function($event){return _vm.$emit('save')}}},[(_vm.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin"}):_vm._e(),_vm._v(" "+_vm._s(_vm.$t('save'))+" ")])
}
var SaveControlvue_type_template_id_e9994b3e_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SaveControl.vue?vue&type=script&lang=ts


let SaveControl = class SaveControl extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isSaving;
    isDisabled;
};
__decorate([
    Prop({ type: Boolean, default: false })
], SaveControl.prototype, "isSaving", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], SaveControl.prototype, "isDisabled", void 0);
SaveControl = __decorate([
    vue_class_component_esm({
        name: 'save-control'
    })
], SaveControl);
/* harmony default export */ const SaveControlvue_type_script_lang_ts = (SaveControl);

;// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_SaveControlvue_type_script_lang_ts = (SaveControlvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SaveControl.vue?vue&type=style&index=0&id=e9994b3e&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue?vue&type=style&index=0&id=e9994b3e&prod&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/SaveControl.vue?vue&type=custom&index=0&blockType=i18n
var SaveControlvue_type_custom_index_0_blockType_i18n = __webpack_require__(859);
var SaveControlvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(SaveControlvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const builder_SaveControlvue_type_custom_index_0_blockType_i18n = ((SaveControlvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue



;


/* normalize component */

var SaveControl_component = normalizeComponent(
  builder_SaveControlvue_type_script_lang_ts,
  SaveControlvue_type_template_id_e9994b3e_render,
  SaveControlvue_type_template_id_e9994b3e_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof builder_SaveControlvue_type_custom_index_0_blockType_i18n === 'function') builder_SaveControlvue_type_custom_index_0_blockType_i18n(SaveControl_component)

/* harmony default export */ const builder_SaveControl = (SaveControl_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ErrorMessage.vue?vue&type=template&id=13429834
var ErrorMessagevue_type_template_id_13429834_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.errorData.type === 'NoTitleGiven')?_c('span',[_vm._v(" "+_vm._s(_vm.$t('error-NoTitleGiven'))+" "),_c('span',{staticClass:"u-block"},[_vm._v(_vm._s(_vm.errorData.status))]),_vm._v(" "+_vm._s(_vm.$t('changes-not-saved'))+" ")]):(!!_vm.errorData.status)?_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type, {title: _vm.errorData.status.title}))+" "+_vm._s(_vm.$t('changes-not-saved')))]):_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type)))])
}
var ErrorMessagevue_type_template_id_13429834_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ErrorMessage.vue?vue&type=script&lang=ts


let ErrorMessage = class ErrorMessage extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    errorData;
};
__decorate([
    Prop({ type: Object, required: true })
], ErrorMessage.prototype, "errorData", void 0);
ErrorMessage = __decorate([
    vue_class_component_esm({
        name: 'error-message'
    })
], ErrorMessage);
/* harmony default export */ const ErrorMessagevue_type_script_lang_ts = (ErrorMessage);

;// CONCATENATED MODULE: ./src/components/builder/ErrorMessage.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_ErrorMessagevue_type_script_lang_ts = (ErrorMessagevue_type_script_lang_ts); 
// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/ErrorMessage.vue?vue&type=custom&index=0&blockType=i18n
var ErrorMessagevue_type_custom_index_0_blockType_i18n = __webpack_require__(991);
var ErrorMessagevue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(ErrorMessagevue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/builder/ErrorMessage.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const builder_ErrorMessagevue_type_custom_index_0_blockType_i18n = ((ErrorMessagevue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/builder/ErrorMessage.vue





/* normalize component */
;
var ErrorMessage_component = normalizeComponent(
  builder_ErrorMessagevue_type_script_lang_ts,
  ErrorMessagevue_type_template_id_13429834_render,
  ErrorMessagevue_type_template_id_13429834_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof builder_ErrorMessagevue_type_custom_index_0_blockType_i18n === 'function') builder_ErrorMessagevue_type_custom_index_0_blockType_i18n(ErrorMessage_component)

/* harmony default export */ const builder_ErrorMessage = (ErrorMessage_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=template&id=1d94c456
var ErrorDisplayvue_type_template_id_1d94c456_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticStyle:{"display":"contents"}},[_c('div',{staticClass:"modal-overlay"}),_c('div',{staticClass:"save-error u-flex u-justify-content-center"},[_c('div',{staticClass:"save-error-inner"},[_c('div',{staticClass:"errors-important u-flex u-align-items-baseline"},[_c('i',{staticClass:"fa fa-exclamation-circle mod-icon"}),_c('div',[_vm._t("default"),_c('div',{staticStyle:{"text-align":"center","margin-top":"5px"}},[_c('button',{staticClass:"btn btn-success btn-sm",staticStyle:{"padding":"3px 10px"},on:{"click":function($event){return _vm.$emit('close')}}},[_vm._v("OK")])])],2)])])])])
}
var ErrorDisplayvue_type_template_id_1d94c456_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=script&lang=ts


let ErrorDisplay = class ErrorDisplay extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
};
ErrorDisplay = __decorate([
    vue_class_component_esm({
        name: 'error-display'
    })
], ErrorDisplay);
/* harmony default export */ const ErrorDisplayvue_type_script_lang_ts = (ErrorDisplay);

;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_ErrorDisplayvue_type_script_lang_ts = (ErrorDisplayvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=style&index=0&id=1d94c456&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=style&index=0&id=1d94c456&prod&lang=css

;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue



;


/* normalize component */

var ErrorDisplay_component = normalizeComponent(
  components_ErrorDisplayvue_type_script_lang_ts,
  ErrorDisplayvue_type_template_id_1d94c456_render,
  ErrorDisplayvue_type_template_id_1d94c456_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const components_ErrorDisplay = (ErrorDisplay_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/VerificationIcon.vue?vue&type=template&id=35108449&scoped=true
var VerificationIconvue_type_template_id_35108449_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{class:{'u-flex u-gap-small-2x m-fill': _vm.useBuilder}},[(_vm.useBuilder)?_c('div',{staticClass:"u-flex u-gap-small-2x u-flex-wrap shapes-mw"},[_c('verification-shape',{attrs:{"selected":_vm.isRect},on:{"click":function($event){_vm.shape = 'r'}}},[_c('rect',{attrs:{"x":"-25","y":"-25","width":"50","height":"50","fill":"var(--fillcolor)"}})]),_c('verification-shape',{attrs:{"selected":_vm.isCircle},on:{"click":function($event){_vm.shape = 'c'}}},[_c('circle',{attrs:{"cx":"0","cy":"0","r":"25","fill":"var(--fillcolor)"}})]),_c('verification-shape',{attrs:{"selected":_vm.shape === 'p11'},on:{"click":function($event){_vm.shape = 'p11'}}},[_c('polygon',{attrs:{"points":"25,-25 25,25 -25,25","fill":"var(--fillcolor)"}})]),_c('verification-shape',{attrs:{"selected":_vm.shape === 'p01'},on:{"click":function($event){_vm.shape = 'p01'}}},[_c('polygon',{attrs:{"points":"-25,-25 25,25 -25,25","fill":"var(--fillcolor)"}})]),_c('verification-shape',{attrs:{"selected":_vm.shape === 'p10'},on:{"click":function($event){_vm.shape = 'p10'}}},[_c('polygon',{attrs:{"points":"25,-25 25,25 -25,-25","fill":"var(--fillcolor)"}})]),_c('verification-shape',{attrs:{"selected":_vm.shape === 'p00'},on:{"click":function($event){_vm.shape = 'p00'}}},[_c('polygon',{attrs:{"points":"25,-25 -25,25 -25,-25","fill":"var(--fillcolor)"}})])],1):_vm._e(),(_vm.useBuilder)?_c('div',{staticClass:"u-flex u-gap-small-2x u-flex-wrap u-justify-content-space-between colors-mw"},[_vm._l((_vm.colors),function(color,index){return _c('div',{key:`vc-${index}`,staticClass:"verification-color u-flex u-align-items-center u-justify-content-center",style:(`--color: ${color}`),on:{"click":function($event){_vm.colorIndex = index}}},[(index === _vm.colorIndex)?_c('span',{staticClass:"verification-color-check"},[_c('i',{staticClass:"fa fa-check"})]):_vm._e()])}),(_vm.colors.length % 2)?_c('div'):_vm._e(),_c('div',{staticClass:"shapestyle",class:{'is-selected': _vm.hasFill },on:{"click":function($event){_vm.stroked = false}}},[_c('svg',{attrs:{"width":"36","height":"36","xmlns":"http://www.w3.org/2000/svg"}},[_c('rect',{attrs:{"x":"0","y":"0","width":"36","height":"36","fill":"white","stroke":"#e6e6e6","stroke-width":"1"}}),_c('g',{attrs:{"transform":"translate(18, 18)"}},[_c('rect',{attrs:{"x":"-12.5","y":"-12.5","width":"25","height":"25","fill":"var(--fillcolor)"}})])])]),_c('div',{staticClass:"shapestyle",class:{'is-selected': _vm.hasStroke },on:{"click":function($event){_vm.stroked = true}}},[_c('svg',{attrs:{"width":"36","height":"36","xmlns":"http://www.w3.org/2000/svg"}},[_c('rect',{attrs:{"x":"0","y":"0","width":"36","height":"36","fill":"white","stroke":"#e6e6e6","stroke-width":"1"}}),_c('g',{attrs:{"transform":"translate(18, 18)"}},[_c('rect',{attrs:{"x":"-12.5","y":"-12.5","width":"25","height":"25","fill":"none","stroke-width":"3","stroke":"currentColor"}})])])])],2):_vm._e(),_c('div',{class:{'icon-ml': _vm.useBuilder }},[_c('svg',{attrs:{"width":"120","height":"120","xmlns":"http://www.w3.org/2000/svg"}},[_c('rect',{attrs:{"x":"0","y":"0","width":"120","height":"120","fill":"white","stroke":"currentColor","stroke-width":"1"}}),_c('g',{attrs:{"transform":"translate(60, 60)"}},[(_vm.isRect && _vm.hasFill)?_c('rect',{attrs:{"x":"-50","y":"-50","width":"100","height":"100","fill":_vm.hexColor}}):(_vm.isRect && _vm.hasStroke)?_c('rect',{attrs:{"x":"-50","y":"-50","width":"100","height":"100","fill":"white","stroke":_vm.hexColor,"stroke-width":"5"}}):(_vm.isCircle && _vm.hasFill)?_c('circle',{attrs:{"cx":"0","cy":"0","r":"50","fill":_vm.hexColor}}):(_vm.isCircle && _vm.hasStroke)?_c('circle',{attrs:{"cx":"0","cy":"0","r":"50","fill":"white","stroke":_vm.hexColor,"stroke-width":"5"}}):(_vm.isPolygon && _vm.hasFill)?_c('polygon',{attrs:{"points":_vm.points,"fill":_vm.hexColor}}):(_vm.isPolygon && _vm.hasStroke)?_c('polygon',{attrs:{"points":_vm.points,"fill":"white","stroke":_vm.hexColor,"stroke-width":"5"}}):_vm._e()])])])])
}
var VerificationIconvue_type_template_id_35108449_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/VerificationShape.vue?vue&type=template&id=2ac5c54a&scoped=true
var VerificationShapevue_type_template_id_2ac5c54a_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"shape",class:{'is-selected': _vm.selected},on:{"click":function($event){return _vm.$emit('click')}}},[_c('svg',{attrs:{"width":"60","height":"60","xmlns":"http://www.w3.org/2000/svg"}},[_c('rect',{attrs:{"x":"0","y":"0","width":"60","height":"60","fill":"white","stroke":"#e6e6e6","stroke-width":"1"}}),_c('g',{attrs:{"transform":"translate(30, 30)"}},[_vm._t("default")],2)])])
}
var VerificationShapevue_type_template_id_2ac5c54a_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/VerificationShape.vue?vue&type=script&lang=ts


let VerificationShape = class VerificationShape extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    selected;
};
__decorate([
    Prop({ type: Boolean, default: false })
], VerificationShape.prototype, "selected", void 0);
VerificationShape = __decorate([
    vue_class_component_esm({
        name: 'verification-shape',
    })
], VerificationShape);
/* harmony default export */ const VerificationShapevue_type_script_lang_ts = (VerificationShape);

;// CONCATENATED MODULE: ./src/components/builder/VerificationShape.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_VerificationShapevue_type_script_lang_ts = (VerificationShapevue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/VerificationShape.vue?vue&type=style&index=0&id=2ac5c54a&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/builder/VerificationShape.vue?vue&type=style&index=0&id=2ac5c54a&prod&scoped=true&lang=css

;// CONCATENATED MODULE: ./src/components/builder/VerificationShape.vue



;


/* normalize component */

var VerificationShape_component = normalizeComponent(
  builder_VerificationShapevue_type_script_lang_ts,
  VerificationShapevue_type_template_id_2ac5c54a_scoped_true_render,
  VerificationShapevue_type_template_id_2ac5c54a_scoped_true_staticRenderFns,
  false,
  null,
  "2ac5c54a",
  null
  
)

/* harmony default export */ const builder_VerificationShape = (VerificationShape_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/VerificationIcon.vue?vue&type=script&lang=ts



let VerificationIcon = class VerificationIcon extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    colors = ['#000000', '#ff0000', '#ffed00', '#306eff', '#ff69b4', '#228b22', '#fbb117', '#00ff00', '#d462ff'];
    position = 'q0';
    shapeType = 'r';
    shapeMeta = '00';
    colorIndex = 0;
    stroked = false;
    iconData;
    useBuilder;
    get verificationIconCode() {
        const colorMeta = this.padIndex(this.colorIndex);
        ;
        return `${this.position}${this.shapeType}${this.shapeMeta}${this.hasFill ? `f${colorMeta}` : 'fxx'}${this.hasStroke ? `s${colorMeta}` : 'sxx'}`;
    }
    get isRect() {
        return this.shapeType === 'r';
    }
    get isCircle() {
        return this.shapeType === 'c';
    }
    get isPolygon() {
        return this.shapeType === 'p';
    }
    get shape() {
        return `${this.shapeType}${this.shapeMeta}`;
    }
    set shape(s) {
        this.shapeType = s[0];
        this.shapeMeta = s.slice(1, 3) || '00';
    }
    get points() {
        if (this.shapeType !== 'p') {
            return '';
        }
        switch (this.shapeMeta) {
            case '00':
                return '50,-50 -50,50 -50,-50';
            case '01':
                return '-50,-50 50,50 -50,50';
            case '10':
                return '50,-50 50,50 -50,-50';
            case '11':
                return '50,-50 50,50 -50,50';
            default:
                return '';
        }
    }
    padIndex(index) {
        return index < 10 ? `0${index}` : String(index);
    }
    get hexColor() {
        return this.colors[this.colorIndex];
    }
    get hasFill() {
        return !this.stroked;
    }
    get hasStroke() {
        return this.stroked;
    }
    mounted() {
        this.parseIconData();
    }
    parseIconData() {
        if (this.iconData) {
            const result = this.iconData.result;
            if (result) {
                this.position = result.slice(0, 2);
                this.shape = result.slice(2, 5);
                if (result.slice(5, 8) !== 'fxx') {
                    this.stroked = false;
                    this.colorIndex = parseInt(result.slice(6, 8));
                }
                else {
                    this.stroked = true;
                    this.colorIndex = parseInt(result.slice(9, 11));
                }
                return;
            }
        }
        this.position = 'q0';
        this.shape = 'r00';
        this.colorIndex = 0;
        this.stroked = false;
    }
};
__decorate([
    Prop({ type: Object, default: () => null })
], VerificationIcon.prototype, "iconData", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], VerificationIcon.prototype, "useBuilder", void 0);
__decorate([
    Watch('iconData')
], VerificationIcon.prototype, "parseIconData", null);
VerificationIcon = __decorate([
    vue_class_component_esm({
        name: 'verification-icon',
        components: {
            VerificationShape: builder_VerificationShape
        },
    })
], VerificationIcon);
/* harmony default export */ const VerificationIconvue_type_script_lang_ts = (VerificationIcon);

;// CONCATENATED MODULE: ./src/components/builder/VerificationIcon.vue?vue&type=script&lang=ts
 /* harmony default export */ const builder_VerificationIconvue_type_script_lang_ts = (VerificationIconvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/builder/VerificationIcon.vue?vue&type=style&index=0&id=35108449&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/builder/VerificationIcon.vue?vue&type=style&index=0&id=35108449&prod&scoped=true&lang=css

;// CONCATENATED MODULE: ./src/components/builder/VerificationIcon.vue



;


/* normalize component */

var VerificationIcon_component = normalizeComponent(
  builder_VerificationIconvue_type_script_lang_ts,
  VerificationIconvue_type_template_id_35108449_scoped_true_render,
  VerificationIconvue_type_template_id_35108449_scoped_true_staticRenderFns,
  false,
  null,
  "35108449",
  null
  
)

/* harmony default export */ const builder_VerificationIcon = (VerificationIcon_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/OnOffSwitch.vue?vue&type=template&id=af24b412
var OnOffSwitchvue_type_template_id_af24b412_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"onoffswitch",class:[_vm.switchClass]},[_c('input',{staticClass:"onoffswitch-checkbox",attrs:{"type":"checkbox","id":`onoffswitch-${_vm.id}`},domProps:{"checked":_vm.checked},on:{"input":function($event){return _vm.$emit('toggle')}}}),_c('label',{staticClass:"onoffswitch-label",class:[_vm.switchClass],attrs:{"for":`onoffswitch-${_vm.id}`}},[_c('span',{staticClass:"onoffswitch-inner"},[_c('span',{staticClass:"onoffswitch-inner-before",class:[_vm.switchClass]},[_vm._v(_vm._s(_vm.onText))]),_c('span',{staticClass:"onoffswitch-inner-after",class:[_vm.switchClass]},[_vm._v(_vm._s(_vm.offText))])]),_c('span',{staticClass:"onoffswitch-switch",class:[_vm.switchClass]})])])
}
var OnOffSwitchvue_type_template_id_af24b412_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/OnOffSwitch.vue?vue&type=script&lang=ts


let OnOffSwitch = class OnOffSwitch extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    checked;
    id;
    onText;
    offText;
    switchClass;
};
__decorate([
    Prop({ type: Boolean })
], OnOffSwitch.prototype, "checked", void 0);
__decorate([
    Prop({ type: String, default: '' })
], OnOffSwitch.prototype, "id", void 0);
__decorate([
    Prop({ type: String, default: '' })
], OnOffSwitch.prototype, "onText", void 0);
__decorate([
    Prop({ type: String, default: '' })
], OnOffSwitch.prototype, "offText", void 0);
__decorate([
    Prop({ type: String, default: '' })
], OnOffSwitch.prototype, "switchClass", void 0);
OnOffSwitch = __decorate([
    vue_class_component_esm({
        name: 'on-off-switch'
    })
], OnOffSwitch);
/* harmony default export */ const OnOffSwitchvue_type_script_lang_ts = (OnOffSwitch);

;// CONCATENATED MODULE: ./src/components/OnOffSwitch.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_OnOffSwitchvue_type_script_lang_ts = (OnOffSwitchvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ./src/components/OnOffSwitch.vue





/* normalize component */
;
var OnOffSwitch_component = normalizeComponent(
  components_OnOffSwitchvue_type_script_lang_ts,
  OnOffSwitchvue_type_template_id_af24b412_render,
  OnOffSwitchvue_type_template_id_af24b412_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const components_OnOffSwitch = (OnOffSwitch_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=script&lang=ts















const DEFAULT_COLOR_NEW = 'yellow-100';
const CONFLICT_ERRORS = ['PresenceStatusMissing', 'InvalidType', 'NoTitleGiven', 'TitleUpdateForbidden', 'InvalidAlias', 'AliasUpdateForbidden', 'NoCodeGiven', 'NoColorGiven', 'InvalidColor'];
let Builder = class Builder extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    fields = [
        { key: 'label', sortable: false },
        { key: 'title', sortable: false },
        { key: 'aliasses', sortable: false },
        { key: 'color', sortable: false },
        { key: 'actions', sortable: false, label: '', variant: 'actions' }
    ];
    statusDefaults = [];
    presence = null;
    selectedStatus = null;
    savedEntryStatuses = [];
    useVerificationIcon = false;
    connector = null;
    errorData = null;
    createNew = false;
    codeNew = '';
    titleNew = '';
    aliasNew = { aliasses: 3 };
    colorNew = DEFAULT_COLOR_NEW;
    apiConfig;
    loadIndex;
    async load() {
        const presenceData = await this.connector?.loadPresence();
        this.statusDefaults = presenceData?.['status-defaults'] || [];
        this.presence = presenceData?.presence || null;
        if (this.presence?.verification_icon_data) {
            this.useVerificationIcon = true;
        }
    }
    async loadSavedEntryStatuses() {
        const data = await this.connector?.loadRegisteredPresenceEntryStatuses();
        this.savedEntryStatuses = data?.statuses;
    }
    mounted() {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this);
        this.load();
    }
    get isEditEnabled() {
        return !this.createNew;
    }
    get isEditDisabled() {
        return this.createNew;
    }
    get isSaving() {
        return this.connector?.isSaving || false;
    }
    get presenceStatuses() {
        return this.presence?.statuses || [];
    }
    get fixedStatusDefaults() {
        return this.statusDefaults.filter(sd => sd.type === 'fixed');
    }
    getStatusDefault(status, fixed = false) {
        const statusDefault = this.statusDefaults.find(sd => sd.id === status.id);
        if (!fixed) {
            return statusDefault;
        }
        return statusDefault.type === 'fixed' ? statusDefault : this.statusDefaults.find(sd => sd.id === statusDefault.aliasses);
    }
    isStatusEditable(status) {
        return !(status.type === 'fixed' || status.type === 'semifixed' || this.savedEntryStatuses.includes(status.id));
    }
    getStatusTitle(status) {
        if (status.type === 'fixed' || status.type === 'semifixed') {
            return this.getStatusDefault(status)?.title || '';
        }
        return status.title;
    }
    getAliasedTitle(status) {
        if (status.type === 'fixed' || status.type === 'semifixed') {
            return this.getStatusDefault(status, true)?.title || '';
        }
        return this.statusDefaults.find(sd => sd.id === status.aliasses)?.title || '';
    }
    setStatusColor(status, color) {
        if (status.color !== color) {
            status.color = color;
        }
    }
    rowClass(status) {
        return `table-body-row presence-builder-row${status === this.selectedStatus ? ' is-selected' : ''}${this.createNew ? '' : ' is-enabled'}`;
    }
    hasEmptyFields() {
        let hasEmptyFields = false;
        const inputs = [...document.querySelectorAll('.presence-builder .form-control')];
        inputs.reverse();
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.reportValidity();
                hasEmptyFields = true;
            }
        });
        return hasEmptyFields;
    }
    isConflictError(errorType) {
        return CONFLICT_ERRORS.includes(errorType);
    }
    setError(data) {
        this.errorData = data;
    }
    resetNew() {
        this.createNew = false;
        this.codeNew = '';
        this.titleNew = '';
        this.aliasNew.aliasses = 3;
        this.colorNew = DEFAULT_COLOR_NEW;
    }
    onCreateNew() {
        this.createNew = true;
        this.selectedStatus = null;
        this.$nextTick(() => {
            document.getElementById('new-presence-code')?.focus();
        });
    }
    onCancelNew() {
        this.resetNew();
    }
    onSaveNew() {
        if (!this.presence) {
            return;
        }
        this.presence.statuses.push({
            id: Math.max(this.statusDefaults.length, Math.max.apply(null, this.presence.statuses.map(s => s.id))) + 1,
            type: 'custom', code: this.codeNew, title: this.titleNew, aliasses: this.aliasNew.aliasses, color: this.colorNew
        });
        this.resetNew();
        this.$nextTick(() => {
            this.selectedStatus = this.presenceStatuses[this.presenceStatuses.length - 1];
        });
    }
    onSelectStatus(status, index = 0) {
        if (!this.createNew) {
            this.selectedStatus = status;
            this.$refs['builder'].selectRow(index);
        }
    }
    onRowSelected(items) {
        this.selectedStatus = items[0] || null;
    }
    onMoveDown(id, index) {
        if (!this.presence || index >= this.presence.statuses.length - 1) {
            return;
        }
        const statuses = this.presence.statuses;
        this.presence.statuses = statuses.slice(0, index).concat(statuses[index + 1], statuses[index]).concat(statuses.slice(index + 2));
        this.$nextTick(() => {
            let el = document.querySelector(`#btn-down-${id}`);
            if (el?.disabled) {
                el = el?.previousSibling;
            }
            el?.focus();
        });
    }
    onMoveUp(id, index) {
        if (!this.presence || index <= 0) {
            return;
        }
        const statuses = this.presence.statuses;
        this.presence.statuses = statuses.slice(0, index - 1).concat(statuses[index], statuses[index - 1]).concat(statuses.slice(index + 1));
        this.$nextTick(() => {
            let el = document.querySelector(`#btn-up-${id}`);
            if (el?.disabled) {
                el = el?.nextSibling;
            }
            el?.focus();
        });
    }
    onRemove(status) {
        if (!this.presence || status.type === 'fixed') {
            return;
        }
        const statuses = this.presence.statuses;
        const index = statuses.findIndex(s => s === status);
        if (index === -1) {
            return;
        }
        this.presence.statuses = statuses.slice(0, index).concat(statuses.slice(index + 1));
    }
    onSave() {
        if (!this.presence) {
            return;
        }
        if (this.hasEmptyFields()) {
            return;
        }
        this.setError(null);
        if (!this.useVerificationIcon) {
            this.presence.verification_icon_data = null;
        }
        else {
            this.presence.verification_icon_data = { version: 1, result: this.$refs['verification-icon'].verificationIconCode };
        }
        this.connector?.updatePresence(this.presence.id, this.presenceStatuses, this.presence.has_checkout, this.presence.verification_icon_data, (data) => {
            if (data?.status === 'ok') {
                this.$emit('presence-data-changed', { statusDefaults: this.statusDefaults, presence: this.presence });
            }
        });
    }
    _loadIndex() {
        this.loadSavedEntryStatuses();
    }
};
__decorate([
    Prop({ type: APIConfig, required: true })
], Builder.prototype, "apiConfig", void 0);
__decorate([
    Prop({ type: Number, default: 0 })
], Builder.prototype, "loadIndex", void 0);
__decorate([
    Watch('loadIndex')
], Builder.prototype, "_loadIndex", null);
Builder = __decorate([
    vue_class_component_esm({
        components: {
            OnOffSwitch: components_OnOffSwitch, TitleControl: builder_TitleControl, AliasControl: builder_AliasControl, ColorControl: builder_ColorControl, SelectionControls: builder_SelectionControls, NewStatusControls: builder_NewStatusControls, SelectionPreview: builder_SelectionPreview, SaveControl: builder_SaveControl, ErrorMessage: builder_ErrorMessage, ErrorDisplay: components_ErrorDisplay, VerificationIcon: builder_VerificationIcon
        }
    })
], Builder);
/* harmony default export */ const Buildervue_type_script_lang_ts = (Builder);

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_Buildervue_type_script_lang_ts = (Buildervue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=0&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=0&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=1&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=1&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=2&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=2&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=3&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=3&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=4&id=79e0346f&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=4&id=79e0346f&prod&scoped=true&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=5&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=5&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=6&id=79e0346f&prod&lang=scss
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=6&id=79e0346f&prod&lang=scss

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=7&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=7&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=8&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=8&id=79e0346f&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=style&index=9&id=79e0346f&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=style&index=9&id=79e0346f&prod&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Builder.vue?vue&type=custom&index=0&blockType=i18n
var Buildervue_type_custom_index_0_blockType_i18n = __webpack_require__(983);
var Buildervue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(Buildervue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_Buildervue_type_custom_index_0_blockType_i18n = ((Buildervue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/Builder.vue



;











/* normalize component */

var Builder_component = normalizeComponent(
  components_Buildervue_type_script_lang_ts,
  render,
  staticRenderFns,
  false,
  null,
  "79e0346f",
  null
  
)

/* custom blocks */
;
if (typeof components_Buildervue_type_custom_index_0_blockType_i18n === 'function') components_Buildervue_type_custom_index_0_blockType_i18n(Builder_component)

/* harmony default export */ const components_Builder = (Builder_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Entry.vue?vue&type=template&id=07388830&scoped=true
var Entryvue_type_template_id_07388830_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('div',{staticClass:"u-flex u-gap-small-3x u-align-items-center",class:[{'m-controls': _vm.defaultTableShown}]},[(_vm.defaultTableShown)?_c('search-bar',{attrs:{"search-options":_vm.searchOptions},on:{"filter-changed":_vm.onFilterChanged,"filter-cleared":_vm.onFilterCleared}}):_vm._e(),(_vm.statusFiltersShown)?_c('div',{staticClass:"status-filters u-flex u-gap-small u-align-items-baseline"},[_vm._m(0),_vm._l((_vm.presenceStatuses),function(status,index){return _c('filter-status-button',{key:`status-${index}`,attrs:{"title":_vm.getPresenceStatusTitle(status),"label":status.code,"color":status.color,"is-selected":_vm.statusFilters.indexOf(status) !== -1},on:{"toggle-filter":function($event){return _vm.toggleStatusFilters(status)}}})}),_c('filter-status-button',{attrs:{"label":_vm.$t('without-status'),"color":"grey-100","is-selected":_vm.withoutStatusSelected},on:{"toggle-filter":_vm.toggleWithoutStatus}})],2):(!_vm.useStatistics)?[_c('a',{staticClass:"btn btn-default btn-sm mod-export",attrs:{"href":_vm.apiConfig.exportURL}},[_vm._v(_vm._s(_vm.$t('export')))]),_c('button',{staticClass:"btn btn-default btn-sm mod-create-period",on:{"click":_vm.createResultPeriod}},[_c('i',{staticClass:"fa fa-plus",attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('new-period')))])]:_vm._e()],2),_c('div',{staticClass:"u-flex"},[_c('div',{staticClass:"w-max-content"},[_c('div',{staticClass:"u-relative"},[_c('entry-table',{directives:[{name:"show",rawName:"v-show",value:(_vm.defaultTableShown),expression:"defaultTableShown"}],attrs:{"id":"course-students","items":_vm.itemsProvider,"periods":_vm.periods,"status-defaults":_vm.statusDefaults,"presence":_vm.presence,"selected-period":_vm.selectedPeriod,"global-search-query":_vm.globalSearchQuery,"pagination":_vm.pagination,"is-saving":_vm.isSaving,"checkout-mode":_vm.checkoutMode,"is-creating-new-period":_vm.creatingNew,"statistics":_vm.statistics,"use-statistics":_vm.useStatistics},on:{"create-period":_vm.createResultPeriod,"period-label-changed":_vm.setSelectedPeriodLabel,"select-student-status":_vm.setSelectedStudentStatus,"toggle-checkout-mode":function($event){_vm.checkoutMode = !_vm.checkoutMode},"toggle-checkout":_vm.toggleCheckout,"change-selected-period":_vm.setSelectedPeriod},scopedSlots:_vm._u([(_vm.hasSelectedPeriod)?{key:"slot-top",fn:function(){return [_c('div',{staticClass:"u-flex u-align-items-baseline u-justify-content-space-between u-gap-small-2x minw-100"},[_c('button',{staticClass:"btn btn-sm mod-period-action mod-show-periods",on:{"click":function($event){return _vm.setSelectedPeriod(null)}}},[_vm._v(_vm._s(_vm.$t('show-all-periods')))]),_c('button',{staticClass:"btn btn-default btn-sm mod-more",attrs:{"id":"show-more"},on:{"click":function($event){_vm.showMore = !_vm.showMore}}},[_vm._v(_vm._s(_vm.$t('more'))+"…")]),_c('period-menu',{attrs:{"target":"show-more","is-visible":_vm.showMore,"presence-statuses":_vm.presenceStatuses,"status-defaults":_vm.statusDefaults,"print-qr-code-url":`${_vm.apiConfig.printQrCodeURL}&presence_period_id=${_vm.selectedPeriod.id}`,"self-registration-disabled":_vm.selectedPeriod.period_self_registration_disabled},on:{"self-registration-disabled-changed":function($event){return _vm.setSelectedPeriodSelfRegistrationDisabled(_vm.selectedPeriod, $event)},"apply-bulk":_vm.applyBulkStatus,"cancel-bulk":_vm.cancelBulkStatus}})],1)]},proxy:true}:null,(_vm.hasSelectedPeriod)?{key:"slot-bottom",fn:function(){return [_c('button',{staticClass:"btn btn-sm mod-period-action mod-remove-period",attrs:{"disabled":_vm.toRemovePeriod === _vm.selectedPeriod},on:{"click":_vm.removeSelectedPeriod}},[_vm._v(_vm._s(_vm.$t('remove-period')))])]},proxy:true}:null],null,true)}),(_vm.periodStatsShown)?_c('periods-stats-table',{attrs:{"id":"course-students","periods":_vm.periods,"is-busy":_vm.loadingStatistics,"status-defaults":_vm.statusDefaults,"presence":_vm.presence,"statistics":_vm.statistics}}):_vm._e(),(!_vm.creatingNew)?_c('div',{staticClass:"lds-ellipsis",attrs:{"aria-hidden":"true"}},[_c('div'),_c('div'),_c('div'),_c('div')]):_vm._e()],1),(_vm.paginationShown)?_c('div',{staticClass:"pagination-container u-flex u-justify-content-end my-3"},[_c('b-pagination',{attrs:{"total-rows":_vm.pagination.total,"per-page":_vm.pagination.perPage,"aria-controls":"course-students","disabled":_vm.changeAfterStatusFilters},model:{value:(_vm.pagination.currentPage),callback:function ($$v) {_vm.$set(_vm.pagination, "currentPage", $$v)},expression:"pagination.currentPage"}}),_c('ul',{staticClass:"pagination"},[_c('li',{staticClass:"page-item",class:{active: !_vm.changeAfterStatusFilters, disabled: _vm.changeAfterStatusFilters}},[_c('a',{staticClass:"page-link",class:{'u-text-line-through': _vm.changeAfterStatusFilters}},[_vm._v(_vm._s(_vm.$t('total'))+" "+_vm._s(_vm.pagination.total))])]),(_vm.changeAfterStatusFilters)?_c('li',{staticClass:"page-item active"},[_c('a',{directives:[{name:"b-popover",rawName:"v-b-popover.hover.right",value:(_vm.$t('changes-filters')),expression:"$t('changes-filters')",modifiers:{"hover":true,"right":true}}],staticClass:"page-link u-cursor-pointer",on:{"click":_vm.refreshFilters}},[_vm._v(_vm._s(_vm.$t('refresh'))+" "),_c('i',{staticClass:"fa fa-info-circle"})])]):_vm._e()])],1):_vm._e()])]),(_vm.errorData)?_c('error-display',{on:{"close":function($event){_vm.errorData = null}}},[(_vm.errorData.code === 500)?_c('span',[_vm._v(_vm._s(_vm.errorData.message))]):(!!_vm.errorData.type)?_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type)))]):_vm._e()]):_vm._e(),(_vm.nonCourseStudentsShown)?[_c('h4',{staticClass:"u-font-medium h-not-in-course"},[_vm._v(_vm._s(_vm.$t('students-not-in-course')))]),_c('entry-table',{attrs:{"id":"non-course-students","items":_vm.nonCourseStudents,"selected-period":_vm.selectedPeriod,"periods":_vm.periods,"status-defaults":_vm.statusDefaults,"presence":_vm.presence,"is-fully-editable":false,"is-saving":_vm.isSavingNonCourse,"is-creating-new-period":_vm.creatingNew,"checkout-mode":_vm.checkoutMode,"use-statistics":_vm.useStatistics},on:{"select-student-status":_vm.setSelectedStudentStatus,"toggle-checkout":_vm.toggleCheckout}})]:_vm._e()],2)
}
var Entryvue_type_template_id_07388830_scoped_true_staticRenderFns = [function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('span',{staticClass:"lbl-filters"},[_c('i',{staticClass:"fa fa-filter"}),_vm._v("Filters:")])
}]


;// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=template&id=07388830&scoped=true

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/LegendItem.vue?vue&type=template&id=128fddd8
var LegendItemvue_type_template_id_128fddd8_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"u-flex u-align-items-baseline u-gap-small"},[_c('div',{class:['color-code', _vm.color],staticStyle:{"height":"20px","padding":"2px 4px"}},[_c('span',[_vm._v(_vm._s(_vm.label))])]),_c('span',[_vm._v(_vm._s(_vm.title))])])
}
var LegendItemvue_type_template_id_128fddd8_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/LegendItem.vue?vue&type=script&lang=ts


let LegendItem = class LegendItem extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    label;
    color;
    title;
};
__decorate([
    Prop({ type: String, default: '' })
], LegendItem.prototype, "label", void 0);
__decorate([
    Prop({ type: String, default: '' })
], LegendItem.prototype, "color", void 0);
__decorate([
    Prop({ type: String, default: '' })
], LegendItem.prototype, "title", void 0);
LegendItem = __decorate([
    vue_class_component_esm({
        name: 'legend-item'
    })
], LegendItem);
/* harmony default export */ const LegendItemvue_type_script_lang_ts = (LegendItem);

;// CONCATENATED MODULE: ./src/components/entry/LegendItem.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_LegendItemvue_type_script_lang_ts = (LegendItemvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ./src/components/entry/LegendItem.vue





/* normalize component */
;
var LegendItem_component = normalizeComponent(
  entry_LegendItemvue_type_script_lang_ts,
  LegendItemvue_type_template_id_128fddd8_render,
  LegendItemvue_type_template_id_128fddd8_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const entry_LegendItem = (LegendItem_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/SearchBar.vue?vue&type=template&id=1de83810&scoped=true
var SearchBarvue_type_template_id_1de83810_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"action-bar input-group"},[_c('b-form-input',{staticClass:"form-group action-bar-search shadow-none",attrs:{"type":"text","placeholder":_vm.$t('search'),"debounce":"750","autocomplete":"off"},on:{"input":function($event){return _vm.$emit('filter-changed')}},model:{value:(_vm.searchOptions.globalSearchQuery),callback:function ($$v) {_vm.$set(_vm.searchOptions, "globalSearchQuery", $$v)},expression:"searchOptions.globalSearchQuery"}}),_c('div',{staticClass:"input-group-append"},[_c('button',{staticClass:"btn btn-default",attrs:{"name":"clear","value":"clear"},on:{"click":function($event){return _vm.$emit('filter-cleared')}}},[_c('span',{staticClass:"glyphicon glyphicon-remove",attrs:{"aria-hidden":"true"}})])])],1)
}
var SearchBarvue_type_template_id_1de83810_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/SearchBar.vue?vue&type=script&lang=ts


let SearchBar = class SearchBar extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    searchOptions;
};
__decorate([
    Prop({ type: Object })
], SearchBar.prototype, "searchOptions", void 0);
SearchBar = __decorate([
    vue_class_component_esm({
        name: 'search-bar'
    })
], SearchBar);
/* harmony default export */ const SearchBarvue_type_script_lang_ts = (SearchBar);

;// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_SearchBarvue_type_script_lang_ts = (SearchBarvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/SearchBar.vue?vue&type=style&index=0&id=1de83810&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue?vue&type=style&index=0&id=1de83810&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/SearchBar.vue?vue&type=custom&index=0&blockType=i18n
var SearchBarvue_type_custom_index_0_blockType_i18n = __webpack_require__(57);
var SearchBarvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(SearchBarvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const entry_SearchBarvue_type_custom_index_0_blockType_i18n = ((SearchBarvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue



;


/* normalize component */

var SearchBar_component = normalizeComponent(
  entry_SearchBarvue_type_script_lang_ts,
  SearchBarvue_type_template_id_1de83810_scoped_true_render,
  SearchBarvue_type_template_id_1de83810_scoped_true_staticRenderFns,
  false,
  null,
  "1de83810",
  null
  
)

/* custom blocks */
;
if (typeof entry_SearchBarvue_type_custom_index_0_blockType_i18n === 'function') entry_SearchBarvue_type_custom_index_0_blockType_i18n(SearchBar_component)

/* harmony default export */ const entry_SearchBar = (SearchBar_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/DynamicFieldKey.vue?vue&type=template&id=e5b6cc76
var DynamicFieldKeyvue_type_template_id_e5b6cc76_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.isEditable)?_c('div',{attrs:{"role":"button","tabindex":"0"},on:{"keyup":function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter"))return null;return _vm.$emit('select')},"click":function($event){return _vm.$emit('select')}}},[_vm._t("default")],2):_c('div',{staticClass:"u-cursor-default u-text-center"},[_vm._t("default")],2)
}
var DynamicFieldKeyvue_type_template_id_e5b6cc76_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/DynamicFieldKey.vue?vue&type=script&lang=ts


let DynamicFieldKeyvue_type_script_lang_ts_SearchBar = class SearchBar extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isEditable;
};
__decorate([
    Prop({ type: Boolean })
], DynamicFieldKeyvue_type_script_lang_ts_SearchBar.prototype, "isEditable", void 0);
DynamicFieldKeyvue_type_script_lang_ts_SearchBar = __decorate([
    vue_class_component_esm({
        name: 'dynamic-field-key'
    })
], DynamicFieldKeyvue_type_script_lang_ts_SearchBar);
/* harmony default export */ const DynamicFieldKeyvue_type_script_lang_ts = (DynamicFieldKeyvue_type_script_lang_ts_SearchBar);

;// CONCATENATED MODULE: ./src/components/entry/DynamicFieldKey.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_DynamicFieldKeyvue_type_script_lang_ts = (DynamicFieldKeyvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ./src/components/entry/DynamicFieldKey.vue





/* normalize component */
;
var DynamicFieldKey_component = normalizeComponent(
  entry_DynamicFieldKeyvue_type_script_lang_ts,
  DynamicFieldKeyvue_type_template_id_e5b6cc76_render,
  DynamicFieldKeyvue_type_template_id_e5b6cc76_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const DynamicFieldKey = (DynamicFieldKey_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=template&id=b7a91320&scoped=true
var EntryTablevue_type_template_id_b7a91320_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('b-table',{ref:"table",staticClass:"mod-presence mod-entry",attrs:{"id":_vm.id,"bordered":"","busy":_vm.isBusy,"items":_vm.items,"fields":_vm.fields,"tbody-tr-class":"table-body-row","sort-by":_vm.sortBy,"sort-desc":_vm.sortDesc,"per-page":_vm.pagination.perPage,"current-page":_vm.pagination.currentPage,"filter":_vm.globalSearchQuery,"no-sort-reset":""},on:{"update:busy":function($event){_vm.isBusy=$event},"update:sortBy":function($event){_vm.sortBy=$event},"update:sort-by":function($event){_vm.sortBy=$event},"update:sortDesc":function($event){_vm.sortDesc=$event},"update:sort-desc":function($event){_vm.sortDesc=$event}},scopedSlots:_vm._u([{key:"cell(photo)",fn:function({item}){return [_c('img',{attrs:{"src":item.photo}})]}},(_vm.isFullyEditable)?{key:"head(fullname)",fn:function(){return [_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('lastname')},on:{"click":function($event){return _vm.sortByNameField('lastname')}}},[_vm._v(_vm._s(_vm.$t('last-name')))]),_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('firstname')},on:{"click":function($event){return _vm.sortByNameField('firstname')}}},[_vm._v(_vm._s(_vm.$t('first-name')))])]},proxy:true}:{key:"head(fullname)",fn:function(){return [_vm._v(_vm._s(_vm.$t('last-name'))+", "+_vm._s(_vm.$t('first-name')))]},proxy:true},{key:"cell(fullname)",fn:function({item, toggleDetails, detailsShowing}){return [(!item.tableEmpty)?[(!(_vm.selectedPeriod || _vm.useStatistics))?_c('a',{staticClass:"u-text-no-underline u-cursor-pointer",class:{'u-font-bold': detailsShowing},on:{"click":toggleDetails}},[_vm._v(_vm._s(item.lastname.toUpperCase())+", "+_vm._s(item.firstname))]):[_vm._v(_vm._s(item.lastname.toUpperCase())+", "+_vm._s(item.firstname))]]:_c('span')]}},(!_vm.selectedPeriod && !_vm.useStatistics)?{key:"row-details",fn:function({item}){return [_c('div',{staticStyle:{"margin":"0 auto","max-width":"fit-content"}},[_c('b-table-simple',{staticClass:"student-checkinout"},[_c('b-thead',[_c('b-tr',[_c('b-th',[_vm._v(_vm._s(_vm.$t('period')))]),_c('b-th',[_vm._v(_vm._s(_vm.$t('checked-in')))]),(_vm.presence && _vm.presence.has_checkout)?_c('b-th',[_vm._v(_vm._s(_vm.$t('checked-out')))]):_vm._e()],1)],1),_c('b-tbody',_vm._l((_vm.periodsReversed),function(period){return _c('student-details',{attrs:{"has-checkout":_vm.presence && _vm.presence.has_checkout,"period-title":period.label || _vm.getPlaceHolder(period.id),"check-in-date":item[`period#${period.id}-checked_in_date`],"check-out-date":item[`period#${period.id}-checked_out_date`]}})}),1)],1)],1)]}}:null,(_vm.isFullyEditable)?{key:"head(official_code)",fn:function(){return [_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('official_code')},on:{"click":function($event){return _vm.sortByNameField('official_code')}}},[_vm._v(_vm._s(_vm.$t('official-code')))])]},proxy:true}:{key:"head(official_code)",fn:function(){return [_vm._v(_vm._s(_vm.$t('official-code')))]},proxy:true},{key:"head(period-entry-plh)",fn:function(){return [_c('div',{staticClass:"u-flex u-align-items-center u-gap-small"},[_c('b-input',{staticClass:"u-bg-none u-font-normal u-font-italic u-pointer-events-none ti-label mod-border",attrs:{"type":"text","autocomplete":"off","placeholder":_vm.$t('new-period') + '...'}}),_c('div',{staticClass:"spin"},[(_vm.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin"}):_vm._e()])],1)]},proxy:true},{key:"cell(period-entry-plh)",fn:function(){return [_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap u-pointer-events-none"},_vm._l((_vm.presenceStatuses),function(status,index){return _c('button',{key:`status-${index}`,staticClass:"color-code mod-plh",class:[status.color]},[_c('span',[_vm._v(_vm._s(status.code))])])}),0)]},proxy:true},_vm._l((_vm.presenceStatuses),function(status){return {key:`head(status-${status.id})`,fn:function(){return [_c('div',{staticClass:"color-code",class:[status.color],staticStyle:{"width":"fit-content"},attrs:{"title":_vm.getPresenceStatusTitle(status)}},[_c('span',[_vm._v(_vm._s(status.code))])])]},proxy:true}}),{key:"head(status-none)",fn:function(){return [_c('div',{staticClass:"color-code grey-100"},[_c('span',[_vm._v("Zonder status")])])]},proxy:true},_vm._l(([..._vm.presenceStatuses, null]),function(status){return {key:`cell(status-${status && status.id || 'none'})`,fn:function({item}){return [_vm._l(([_vm.getStudentStats(item, status)]),function(count){return [(count)?_c('div',{staticClass:"color-code grey-100",staticStyle:{"width":"fit-content","margin":"0 auto"}},[_c('span',{staticStyle:{"font-variant":"initial","font-size":"13px"}},[_vm._v(_vm._s(count))])]):_c('span',{staticClass:"u-block u-text-center",staticStyle:{"color":"#a9b9bc"}},[_vm._v("0")])]})]}}}),_vm._l((_vm.dynamicFieldKeys),function(fieldKey){return {key:`head(${fieldKey.key})`,fn:function({label}){return [_c('dynamic-field-key',{class:[{'btn-select-period' : _vm.isFullyEditable}, 'u-txt-truncate'],attrs:{"is-editable":_vm.isFullyEditable,"title":label},on:{"select":function($event){return _vm.$emit('change-selected-period', fieldKey.id)}},scopedSlots:_vm._u([{key:"default",fn:function(){return [(label)?_c('span',[_vm._v(_vm._s(label))]):_c('span',{staticClass:"u-font-italic"},[_vm._v(_vm._s(_vm.getPlaceHolder(fieldKey.id)))])]},proxy:true}],null,true)})]}}}),_vm._l((_vm.dynamicFieldKeys),function(fieldKey){return {key:`cell(${fieldKey.key})`,fn:function({item}){return [_c('presence-status-display',{attrs:{"title":_vm.getStatusTitleForStudent(item, fieldKey.id),"label":_vm.getStatusCodeForStudent(item, fieldKey.id),"color":_vm.getStatusColorForStudent(item, fieldKey.id),"has-checkout":_vm.presence && _vm.presence.has_checkout,"check-in-date":item[`period#${fieldKey.id}-checked_in_date`],"check-out-date":item[`period#${fieldKey.id}-checked_out_date`]}})]}}}),{key:"head(period-entry)",fn:function(){return [_c('div',{staticClass:"u-flex u-align-items-center u-gap-small"},[(_vm.isFullyEditable)?_c('b-input',{staticClass:"u-font-normal ti-label",attrs:{"type":"text","debounce":"750","autocomplete":"off","placeholder":_vm.getPlaceHolder(_vm.selectedPeriod.id)},model:{value:(_vm.selectedPeriodLabel),callback:function ($$v) {_vm.selectedPeriodLabel=$$v},expression:"selectedPeriodLabel"}}):(_vm.selectedPeriodLabel)?_c('span',{staticStyle:{"width":"100%"}},[_vm._v(_vm._s(_vm.selectedPeriodLabel))]):_c('span',{staticClass:"u-font-italic",staticStyle:{"width":"100%"}},[_vm._v(_vm._s(_vm.getPlaceHolder(_vm.selectedPeriod.id)))]),_c('div',{staticClass:"spin"},[(_vm.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin"}):_vm._e()])],1)]},proxy:true},(_vm.isFullyEditable && _vm.selectedPeriod && !_vm.useStatistics)?{key:"thead-top",fn:function(data){return [_c('b-tr',[_c('b-td'),_c('b-td',{attrs:{"colspan":"2"}},[_c('span',{staticClass:"u-font-medium",staticStyle:{"color":"#47686b","font-size":"14px"}},[_vm._v(_vm._s(_vm.selectedPeriod.label || _vm.getPlaceHolder(_vm.selectedPeriod.id)))])]),_c('b-td',[_vm._t("slot-top")],2),(_vm.checkoutMode)?_c('b-td'):_vm._e()],1)]}}:null,(_vm.isFullyEditable && _vm.selectedPeriod && !_vm.useStatistics)?{key:"bottom-row",fn:function(){return [_c('b-td',{attrs:{"colspan":"3"}}),_c('b-td',[_vm._t("slot-bottom")],2),(_vm.checkoutMode)?_c('b-td'):_vm._e()]},proxy:true}:null,{key:"cell(period-entry)",fn:function({item}){return [(item.tableEmpty)?_c('div',{staticClass:"u-font-italic"},[_vm._v(_vm._s(_vm.$t('no-results')))]):[_c('label',{staticClass:"sr-only",attrs:{"id":`lbl-item-${item.id}-status`}},[_vm._v("Status")]),_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap",attrs:{"role":"radiogroup","aria-labelledby":`lbl-item-${item.id}-status`}},_vm._l((_vm.presenceStatuses),function(status,index){return _c('presence-status-button',{key:`status-${index}`,attrs:{"status":status,"title":_vm.getPresenceStatusTitle(status),"is-selected":_vm.hasSelectedStudentStatus(item, status.id),"is-disabled":_vm.checkoutMode},on:{"select":function($event){return _vm.$emit('select-student-status', item, _vm.selectedPeriod, status.id, _vm.isFullyEditable)}}})}),1)]]}},{key:"head(period-checkout)",fn:function(){return [(_vm.isFullyEditable)?_c('on-off-switch',{attrs:{"id":`checkout-${_vm.id}`,"switch-class":"mod-checkout-choice","on-text":_vm.$t('checkout-mode'),"off-text":_vm.$t('checkout-mode'),"checked":_vm.checkoutMode},on:{"toggle":function($event){return _vm.$emit('toggle-checkout-mode')}}}):_c('span')]},proxy:true},(_vm.checkoutMode)?{key:"cell(period-checkout)",fn:function({item}){return [(item.tableEmpty)?_c('span'):(item[`period#${_vm.selectedPeriod.id}-checked_in_date`])?_c('on-off-switch',{attrs:{"id":item.id,"on-text":_vm.$t('checked-out'),"off-text":_vm.$t('not-checked-out'),"checked":item[`period#${_vm.selectedPeriod.id}-checked_out_date`] > item[`period#${_vm.selectedPeriod.id}-checked_in_date`],"switch-class":"mod-checkout"},on:{"toggle":function($event){return _vm.$emit('toggle-checkout', item, _vm.selectedPeriod, _vm.isFullyEditable)}}}):_c('span',{staticStyle:{"color":"#999"}},[_vm._v(_vm._s(_vm.$t('not-applicable')))])]}}:{key:"cell(period-checkout)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true}],null,true)})
}
var EntryTablevue_type_template_id_b7a91320_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusButton.vue?vue&type=template&id=3d724f18
var PresenceStatusButtonvue_type_template_id_3d724f18_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('button',{staticClass:"color-code",class:[_vm.isActive ? _vm.status.color : 'mod-disabled', {'mod-selectable': _vm.isActive, 'mod-shadow-grey': _vm.isActive && !_vm.isSelected, 'mod-shadow is-selected': _vm.isSelected}],attrs:{"title":_vm.isActive ? _vm.title : '',"disabled":_vm.isDisabled,"role":"radio","aria-checked":_vm.isSelected ? 'true': 'false'},on:{"click":_vm.select}},[_c('span',[_vm._v(_vm._s(_vm.status.code))])])
}
var PresenceStatusButtonvue_type_template_id_3d724f18_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusButton.vue?vue&type=script&lang=ts


let PresenceStatusButton = class PresenceStatusButton extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isSelected;
    isDisabled;
    status;
    title;
    get isActive() {
        return this.isSelected || !this.isDisabled;
    }
    select() {
        if (!this.isSelected) {
            this.$emit('select');
        }
    }
};
__decorate([
    Prop({ type: Boolean })
], PresenceStatusButton.prototype, "isSelected", void 0);
__decorate([
    Prop({ type: Boolean })
], PresenceStatusButton.prototype, "isDisabled", void 0);
__decorate([
    Prop({ type: Object, required: true })
], PresenceStatusButton.prototype, "status", void 0);
__decorate([
    Prop({ type: String, default: '' })
], PresenceStatusButton.prototype, "title", void 0);
PresenceStatusButton = __decorate([
    vue_class_component_esm({
        name: 'presence-status-button'
    })
], PresenceStatusButton);
/* harmony default export */ const PresenceStatusButtonvue_type_script_lang_ts = (PresenceStatusButton);

;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusButton.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_PresenceStatusButtonvue_type_script_lang_ts = (PresenceStatusButtonvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusButton.vue





/* normalize component */
;
var PresenceStatusButton_component = normalizeComponent(
  entry_PresenceStatusButtonvue_type_script_lang_ts,
  PresenceStatusButtonvue_type_template_id_3d724f18_render,
  PresenceStatusButtonvue_type_template_id_3d724f18_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const entry_PresenceStatusButton = (PresenceStatusButton_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=template&id=2542bfb6&scoped=true
var PresenceStatusDisplayvue_type_template_id_2542bfb6_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"result-wrap",class:{'u-flex': _vm.showCheckout, 'u-align-items-center': _vm.showCheckout}},[_c('div',{staticClass:"color-code u-cursor-default",class:[_vm.color || 'mod-none'],attrs:{"title":_vm.title}},[_c('span',[_vm._v(_vm._s(_vm.label))])]),(_vm.showCheckout)?[_c('i',{staticClass:"fa fa-sign-out checkout-indicator",class:{'is-checked-out': _vm.isCheckedOut },attrs:{"aria-hidden":"true","title":_vm.$t(_vm.isCheckedOut ? 'checked-out' : 'not-checked-out')}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t(_vm.isCheckedOut ? 'checked-out' : 'not-checked-out')))])]:_vm._e()],2)
}
var PresenceStatusDisplayvue_type_template_id_2542bfb6_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=script&lang=ts


let PresenceStatusDisplay = class PresenceStatusDisplay extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    hasCheckout;
    title;
    label;
    color;
    checkInDate;
    checkOutDate;
    get showCheckout() {
        return this.hasCheckout && !!this.checkInDate;
    }
    get isCheckedOut() {
        if (typeof this.checkOutDate !== 'number' || typeof this.checkInDate !== 'number') {
            return false;
        }
        return this.checkOutDate > this.checkInDate;
    }
};
__decorate([
    Prop({ type: Boolean, default: false })
], PresenceStatusDisplay.prototype, "hasCheckout", void 0);
__decorate([
    Prop({ type: String, default: '' })
], PresenceStatusDisplay.prototype, "title", void 0);
__decorate([
    Prop({ type: String, default: '' })
], PresenceStatusDisplay.prototype, "label", void 0);
__decorate([
    Prop({ type: String, default: '' })
], PresenceStatusDisplay.prototype, "color", void 0);
__decorate([
    Prop({ type: Number, default: 0 })
], PresenceStatusDisplay.prototype, "checkInDate", void 0);
__decorate([
    Prop({ type: Number, default: 0 })
], PresenceStatusDisplay.prototype, "checkOutDate", void 0);
PresenceStatusDisplay = __decorate([
    vue_class_component_esm({
        name: 'presence-status-display'
    })
], PresenceStatusDisplay);
/* harmony default export */ const PresenceStatusDisplayvue_type_script_lang_ts = (PresenceStatusDisplay);

;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_PresenceStatusDisplayvue_type_script_lang_ts = (PresenceStatusDisplayvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=style&index=0&id=2542bfb6&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=style&index=0&id=2542bfb6&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=style&index=1&id=2542bfb6&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=style&index=1&id=2542bfb6&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=custom&index=0&blockType=i18n
var PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n = __webpack_require__(752);
var PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const entry_PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n = ((PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue



;



/* normalize component */

var PresenceStatusDisplay_component = normalizeComponent(
  entry_PresenceStatusDisplayvue_type_script_lang_ts,
  PresenceStatusDisplayvue_type_template_id_2542bfb6_scoped_true_render,
  PresenceStatusDisplayvue_type_template_id_2542bfb6_scoped_true_staticRenderFns,
  false,
  null,
  "2542bfb6",
  null
  
)

/* custom blocks */
;
if (typeof entry_PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n === 'function') entry_PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n(PresenceStatusDisplay_component)

/* harmony default export */ const entry_PresenceStatusDisplay = (PresenceStatusDisplay_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/StudentDetails.vue?vue&type=template&id=5e7449e4&scoped=true
var StudentDetailsvue_type_template_id_5e7449e4_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('b-tr',[_c('b-td',[_vm._v(_vm._s(_vm.periodTitle))]),(_vm.showStatus)?_c('b-td',[_c('presence-status-display',{attrs:{"title":_vm.title,"label":_vm.label,"color":_vm.color}})],1):_vm._e(),(_vm.hasCheckout)?[(_vm.showCheckout)?[_c('b-td',[_vm._v(_vm._s(_vm.checkInDateFormatted))]),(_vm.isCheckedOut)?_c('b-td',[_vm._v(_vm._s(_vm.checkOutDateFormatted))]):_c('b-td',{staticClass:"not-checked-out"},[_vm._v(_vm._s(_vm.$t('not-checked-out')))])]:[_c('b-td',[_c('div',{staticClass:"color-code mod-none"})]),_c('b-td',[_c('div',{staticClass:"color-code mod-none"})])]]:[(!!_vm.checkInDate)?_c('b-td',[_vm._v(_vm._s(_vm.checkInDateFormatted))]):_c('b-td',[_c('div',{staticClass:"color-code mod-none"})])]],2)
}
var StudentDetailsvue_type_template_id_5e7449e4_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/StudentDetails.vue?vue&type=script&lang=ts



function pad(num) {
    return `${num < 10 ? '0' : ''}${num}`;
}
let StudentDetails = class StudentDetails extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    hasCheckout;
    showStatus;
    periodTitle;
    title;
    label;
    color;
    checkInDate;
    checkOutDate;
    get showCheckout() {
        return this.hasCheckout && !!this.checkInDate;
    }
    createDate(timestamp) {
        const d = new Date(0);
        d.setUTCSeconds(timestamp);
        return d;
    }
    formatDate(timestamp) {
        const d = new Date(0);
        d.setUTCSeconds(timestamp);
        return d.toLocaleString();
    }
    get isCheckedOut() {
        if (typeof this.checkOutDate !== 'number' || typeof this.checkInDate !== 'number') {
            return false;
        }
        return this.checkOutDate > this.checkInDate;
    }
    getDateFormatted(timestamp) {
        const date = this.createDate(timestamp);
        if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
            return '';
        }
        return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }
    get checkInDateFormatted() {
        if (typeof this.checkInDate !== 'number') {
            return '';
        }
        return this.getDateFormatted(this.checkInDate);
    }
    get checkOutDateFormatted() {
        if (typeof this.checkOutDate !== 'number') {
            return '';
        }
        return this.getDateFormatted(this.checkOutDate);
    }
};
__decorate([
    Prop({ type: Boolean, default: false })
], StudentDetails.prototype, "hasCheckout", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], StudentDetails.prototype, "showStatus", void 0);
__decorate([
    Prop({ type: String, default: '' })
], StudentDetails.prototype, "periodTitle", void 0);
__decorate([
    Prop({ type: String, default: '' })
], StudentDetails.prototype, "title", void 0);
__decorate([
    Prop({ type: String, default: '' })
], StudentDetails.prototype, "label", void 0);
__decorate([
    Prop({ type: String, default: '' })
], StudentDetails.prototype, "color", void 0);
__decorate([
    Prop({ type: Number, default: 0 })
], StudentDetails.prototype, "checkInDate", void 0);
__decorate([
    Prop({ type: Number, default: 0 })
], StudentDetails.prototype, "checkOutDate", void 0);
StudentDetails = __decorate([
    vue_class_component_esm({
        name: 'student-details',
        components: { PresenceStatusDisplay: entry_PresenceStatusDisplay },
        filters: {
            fDate: function (date) {
                if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                    return '';
                }
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
            }
        }
    })
], StudentDetails);
/* harmony default export */ const StudentDetailsvue_type_script_lang_ts = (StudentDetails);

;// CONCATENATED MODULE: ./src/components/entry/StudentDetails.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_StudentDetailsvue_type_script_lang_ts = (StudentDetailsvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/StudentDetails.vue?vue&type=style&index=0&id=5e7449e4&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/StudentDetails.vue?vue&type=style&index=0&id=5e7449e4&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/StudentDetails.vue?vue&type=custom&index=0&blockType=i18n
var StudentDetailsvue_type_custom_index_0_blockType_i18n = __webpack_require__(496);
var StudentDetailsvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(StudentDetailsvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/entry/StudentDetails.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const entry_StudentDetailsvue_type_custom_index_0_blockType_i18n = ((StudentDetailsvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/entry/StudentDetails.vue



;


/* normalize component */

var StudentDetails_component = normalizeComponent(
  entry_StudentDetailsvue_type_script_lang_ts,
  StudentDetailsvue_type_template_id_5e7449e4_scoped_true_render,
  StudentDetailsvue_type_template_id_5e7449e4_scoped_true_staticRenderFns,
  false,
  null,
  "5e7449e4",
  null
  
)

/* custom blocks */
;
if (typeof entry_StudentDetailsvue_type_custom_index_0_blockType_i18n === 'function') entry_StudentDetailsvue_type_custom_index_0_blockType_i18n(StudentDetails_component)

/* harmony default export */ const entry_StudentDetails = (StudentDetails_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=script&lang=ts







let EntryTable = class EntryTable extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    sortBy = 'lastname';
    sortDesc = false;
    isBusy = false;
    id;
    items;
    selectedPeriod;
    pagination;
    isSaving;
    checkoutMode;
    globalSearchQuery;
    statusDefaults;
    periods;
    presence;
    isFullyEditable;
    isCreatingNewPeriod;
    statistics;
    useStatistics;
    getStudentStats(studentItem, status) {
        let count = 0;
        this.periods.forEach(p => {
            const statusId = studentItem[`period#${p.id}-status`];
            if ((statusId && status?.id === statusId) || !(statusId || status)) {
                count++;
            }
        });
        return count;
    }
    getStudentAllStats(studentItem) {
        const studentStats = [...this.presenceStatuses, null].map(status => ({ status, count: 0 }));
        this.periods.forEach(p => {
            const statusId = studentItem[`period#${p.id}-status`];
            let stat;
            if (statusId) {
                stat = studentStats.find(stat => stat.status?.id === statusId) || null;
            }
            else {
                stat = studentStats[studentStats.length - 1];
            }
            if (stat) {
                stat.count++;
            }
        });
        return studentStats;
    }
    get hasResults() {
        return this.pagination.total !== 0;
    }
    getSortStatus(name) {
        if (this.sortBy !== name) {
            return 'none';
        }
        return this.sortDesc ? 'descending' : 'ascending';
    }
    sortByNameField(namefield) {
        if (this.sortBy === namefield) {
            this.sortDesc = !this.sortDesc;
            return;
        }
        this.sortBy = namefield;
        this.sortDesc = false;
    }
    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }
    get dynamicFieldKeys() {
        return this.periods.map((period) => ({ key: `period#${period.id}`, id: period.id }));
    }
    get selectedPeriodLabel() {
        return this.selectedPeriod?.label || '';
    }
    set selectedPeriodLabel(label) {
        if (!this.selectedPeriod) {
            return;
        }
        this.$emit('period-label-changed', this.selectedPeriod, label);
    }
    getPlaceHolder(periodId) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }
    getStudentStatusForPeriod(student, periodId) {
        return student[`period#${periodId}-status`];
    }
    getPresenceStatusTitle(status) {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }
    get presenceStatuses() {
        return this.presence?.statuses || [];
    }
    getPresenceStatus(statusId) {
        return this.presenceStatuses.find(status => status.id === statusId);
    }
    hasSelectedStudentStatus(student, status) {
        if (!this.selectedPeriod) {
            return false;
        }
        return this.getStudentStatusForPeriod(student, this.selectedPeriod.id) === status;
    }
    getStatusCodeForStudent(student, periodId = undefined) {
        if (periodId === undefined) {
            if (!this.selectedPeriod) {
                return '';
            }
            periodId = this.selectedPeriod.id;
        }
        return this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId))?.code || '';
    }
    getStatusColorForStudent(student, periodId = undefined) {
        if (periodId === undefined) {
            if (!this.selectedPeriod) {
                return '';
            }
            periodId = this.selectedPeriod.id;
        }
        return this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId))?.color || '';
    }
    getStatusTitleForStudent(student, periodId = undefined) {
        if (periodId === undefined) {
            if (!this.selectedPeriod) {
                return '';
            }
            periodId = this.selectedPeriod.id;
        }
        const status = this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId));
        return status ? this.getPresenceStatusTitle(status) : '';
    }
    get userFields() {
        return [
            { key: 'photo', sortable: false, label: '', variant: 'photo' },
            { key: 'fullname', sortable: false, label: 'Student' },
            { key: 'official_code', sortable: false }
        ];
    }
    get periodFields() {
        if (this.isCreatingNewPeriod) {
            return [];
        }
        if (this.selectedPeriod) {
            return [
                { key: 'period-entry', sortable: false, label: this.selectedPeriod.label, variant: 'period' },
                this.presence && this.presence.has_checkout && (this.isFullyEditable || this.checkoutMode) ? { key: 'period-checkout', sortable: false, variant: 'checkout' } : null
            ];
        }
        const periodFields = this.periods.map(period => ({ key: `period#${period.id}`, sortable: false, label: period.label || '', variant: 'result' }));
        periodFields.reverse();
        return periodFields;
    }
    get statusFields() {
        const statusFields = this.presenceStatuses.map(status => ({ key: 'status-' + status.id, sortable: false }));
        statusFields.push({ key: 'status-none', sortable: false });
        return statusFields;
    }
    get fields() {
        return [
            ...this.userFields,
            ...(this.useStatistics ? this.statusFields : [
                this.isCreatingNewPeriod ? { key: 'period-entry-plh', sortable: false, variant: 'period' } : null,
                ...this.periodFields
            ])
        ];
    }
    created() {
        this.$parent?.$on('refresh', () => {
            if (this.isFullyEditable) {
                this.$refs.table.refresh();
            }
        });
    }
};
__decorate([
    Prop({ type: String, default: '' })
], EntryTable.prototype, "id", void 0);
__decorate([
    Prop()
], EntryTable.prototype, "items", void 0);
__decorate([
    Prop({ type: Object, default: null })
], EntryTable.prototype, "selectedPeriod", void 0);
__decorate([
    Prop({ type: Object, default: () => ({ perPage: 0, currentPage: 0, total: 0 }) })
], EntryTable.prototype, "pagination", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], EntryTable.prototype, "isSaving", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], EntryTable.prototype, "checkoutMode", void 0);
__decorate([
    Prop({ type: String, default: '' })
], EntryTable.prototype, "globalSearchQuery", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], EntryTable.prototype, "statusDefaults", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], EntryTable.prototype, "periods", void 0);
__decorate([
    Prop({ type: Object, default: null })
], EntryTable.prototype, "presence", void 0);
__decorate([
    Prop({ type: Boolean, default: true })
], EntryTable.prototype, "isFullyEditable", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], EntryTable.prototype, "isCreatingNewPeriod", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], EntryTable.prototype, "statistics", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], EntryTable.prototype, "useStatistics", void 0);
EntryTable = __decorate([
    vue_class_component_esm({
        name: 'entry-table',
        components: { StudentDetails: entry_StudentDetails, PresenceStatusDisplay: entry_PresenceStatusDisplay, PresenceStatusButton: entry_PresenceStatusButton, OnOffSwitch: components_OnOffSwitch, DynamicFieldKey: DynamicFieldKey }
    })
], EntryTable);
/* harmony default export */ const EntryTablevue_type_script_lang_ts = (EntryTable);

;// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_EntryTablevue_type_script_lang_ts = (EntryTablevue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=style&index=0&id=b7a91320&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue?vue&type=style&index=0&id=b7a91320&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=style&index=1&id=b7a91320&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue?vue&type=style&index=1&id=b7a91320&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=custom&index=0&blockType=i18n
var EntryTablevue_type_custom_index_0_blockType_i18n = __webpack_require__(98);
var EntryTablevue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(EntryTablevue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const entry_EntryTablevue_type_custom_index_0_blockType_i18n = ((EntryTablevue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue



;



/* normalize component */

var EntryTable_component = normalizeComponent(
  entry_EntryTablevue_type_script_lang_ts,
  EntryTablevue_type_template_id_b7a91320_scoped_true_render,
  EntryTablevue_type_template_id_b7a91320_scoped_true_staticRenderFns,
  false,
  null,
  "b7a91320",
  null
  
)

/* custom blocks */
;
if (typeof entry_EntryTablevue_type_custom_index_0_blockType_i18n === 'function') entry_EntryTablevue_type_custom_index_0_blockType_i18n(EntryTable_component)

/* harmony default export */ const entry_EntryTable = (EntryTable_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/FilterStatusButton.vue?vue&type=template&id=2cf36b0e
var FilterStatusButtonvue_type_template_id_2cf36b0e_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('button',{staticClass:"color-code mod-selectable",class:[_vm.color, {'mod-off': !_vm.isSelected, 'is-selected': _vm.isSelected}],attrs:{"title":_vm.title,"aria-pressed":_vm.isSelected ? 'true': 'false'},on:{"click":function($event){return _vm.$emit('toggle-filter')}}},[_c('span',[_vm._v(_vm._s(_vm.label))])])
}
var FilterStatusButtonvue_type_template_id_2cf36b0e_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/FilterStatusButton.vue?vue&type=script&lang=ts


let FilterStatusButton = class FilterStatusButton extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isSelected;
    title;
    label;
    color;
};
__decorate([
    Prop({ type: Boolean })
], FilterStatusButton.prototype, "isSelected", void 0);
__decorate([
    Prop({ type: String, default: '' })
], FilterStatusButton.prototype, "title", void 0);
__decorate([
    Prop({ type: String, default: '' })
], FilterStatusButton.prototype, "label", void 0);
__decorate([
    Prop({ type: String, default: '' })
], FilterStatusButton.prototype, "color", void 0);
FilterStatusButton = __decorate([
    vue_class_component_esm({
        name: 'filter-status-button'
    })
], FilterStatusButton);
/* harmony default export */ const FilterStatusButtonvue_type_script_lang_ts = (FilterStatusButton);

;// CONCATENATED MODULE: ./src/components/entry/FilterStatusButton.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_FilterStatusButtonvue_type_script_lang_ts = (FilterStatusButtonvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ./src/components/entry/FilterStatusButton.vue





/* normalize component */
;
var FilterStatusButton_component = normalizeComponent(
  entry_FilterStatusButtonvue_type_script_lang_ts,
  FilterStatusButtonvue_type_template_id_2cf36b0e_render,
  FilterStatusButtonvue_type_template_id_2cf36b0e_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const entry_FilterStatusButton = (FilterStatusButton_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodMenu.vue?vue&type=template&id=b32f5ca2&scoped=true
var PeriodMenuvue_type_template_id_b32f5ca2_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('b-popover',{attrs:{"target":_vm.target,"show":_vm.isVisible,"triggers":"click","placement":"right","custom-class":"bulk-status"},on:{"update:show":function($event){_vm.isVisible=$event}}},[_c('div',{staticStyle:{"padding":"8px","border-bottom":"1px solid #d4d4d4","font-size":"13px"}},[_c('on-off-switch',{attrs:{"id":"disable-selfreg-check","switch-class":"mod-self-disable mod-period","on-text":_vm.$t('self-registration-on'),"off-text":_vm.$t('self-registration-off'),"checked":!_vm.selfRegistrationDisabled},on:{"toggle":_vm.selfRegistrationChanged}}),(!_vm.selfRegistrationDisabled)?_c('a',{staticStyle:{"display":"inline-block","margin-top":"7px"},attrs:{"href":_vm.printQrCodeUrl,"target":"_blank"}},[_c('i',{staticClass:"fa fa-print",staticStyle:{"margin-right":"5px"},attrs:{"aria-hidden":"true"}}),_vm._v("Toon QR code voor zelfregistratie")]):_vm._e()],1),_c('div',{staticStyle:{"padding":"8px 8px 8px"}},[_c('div',{staticClass:"u-flex u-justify-content-start u-align-items-center msg-text u-cursor-pointer",attrs:{"id":"lbl-bulk"}},[_vm._v(_vm._s(_vm.$t('set-students-without-status'))+" "),_c('i',{staticClass:"fa fa-chevron-right",staticStyle:{"color":"#999","margin-left":"3px"},attrs:{"aria-hidden":"true"}})]),_c('b-popover',{attrs:{"target":"lbl-bulk","triggers":"hover","placement":"rightbottom"}},[_c('div',{staticClass:"p-08"},[_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap mb-12",attrs:{"role":"radiogroup","aria-labelledby":"lbl-bulk"}},_vm._l((_vm.presenceStatuses),function(status,index){return _c('button',{key:`status-${index}`,staticClass:"color-code mod-selectable",class:[status.color, _vm.selectedStatus === status ? 'mod-shadow is-selected' : 'mod-shadow-grey'],attrs:{"title":_vm.getPresenceStatusTitle(status),"role":"radio","aria-checked":_vm.selectedStatus === status},on:{"click":function($event){_vm.selectedStatus = status}}},[_c('span',[_vm._v(_vm._s(status.code))])])}),0),_c('div',{staticClass:"u-flex u-gap-small u-justify-content-end"},[_c('button',{staticClass:"btn btn-primary btn-sm px-08 py-02",attrs:{"disabled":_vm.selectedStatus === null},on:{"click":_vm.apply}},[_vm._v(_vm._s(_vm.$t('apply')))]),_c('button',{staticClass:"btn btn-default btn-sm px-08 py-02",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])])],1)])
}
var PeriodMenuvue_type_template_id_b32f5ca2_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodMenu.vue?vue&type=script&lang=ts



let PeriodMenu = class PeriodMenu extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    selectedStatus = null;
    target;
    isVisible;
    presenceStatuses;
    statusDefaults;
    selfRegistrationDisabled;
    printQrCodeUrl;
    apply() {
        this.$emit('apply-bulk', this.selectedStatus);
        this.selectedStatus = null;
    }
    cancel() {
        this.$emit('cancel-bulk');
        this.selectedStatus = null;
    }
    selfRegistrationChanged() {
        this.$emit('self-registration-disabled-changed', !this.selfRegistrationDisabled);
    }
    getPresenceStatusTitle(status) {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }
};
__decorate([
    Prop({ type: String, required: true })
], PeriodMenu.prototype, "target", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], PeriodMenu.prototype, "isVisible", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], PeriodMenu.prototype, "presenceStatuses", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], PeriodMenu.prototype, "statusDefaults", void 0);
__decorate([
    Prop({ type: Boolean, required: true })
], PeriodMenu.prototype, "selfRegistrationDisabled", void 0);
__decorate([
    Prop({ type: String, default: '' })
], PeriodMenu.prototype, "printQrCodeUrl", void 0);
PeriodMenu = __decorate([
    vue_class_component_esm({
        name: 'period-menu',
        components: { OnOffSwitch: components_OnOffSwitch }
    })
], PeriodMenu);
/* harmony default export */ const PeriodMenuvue_type_script_lang_ts = (PeriodMenu);

;// CONCATENATED MODULE: ./src/components/entry/PeriodMenu.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_PeriodMenuvue_type_script_lang_ts = (PeriodMenuvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodMenu.vue?vue&type=style&index=0&id=b32f5ca2&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/PeriodMenu.vue?vue&type=style&index=0&id=b32f5ca2&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodMenu.vue?vue&type=style&index=1&id=b32f5ca2&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/entry/PeriodMenu.vue?vue&type=style&index=1&id=b32f5ca2&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodMenu.vue?vue&type=custom&index=0&blockType=i18n
var PeriodMenuvue_type_custom_index_0_blockType_i18n = __webpack_require__(269);
var PeriodMenuvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(PeriodMenuvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/entry/PeriodMenu.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const entry_PeriodMenuvue_type_custom_index_0_blockType_i18n = ((PeriodMenuvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/entry/PeriodMenu.vue



;



/* normalize component */

var PeriodMenu_component = normalizeComponent(
  entry_PeriodMenuvue_type_script_lang_ts,
  PeriodMenuvue_type_template_id_b32f5ca2_scoped_true_render,
  PeriodMenuvue_type_template_id_b32f5ca2_scoped_true_staticRenderFns,
  false,
  null,
  "b32f5ca2",
  null
  
)

/* custom blocks */
;
if (typeof entry_PeriodMenuvue_type_custom_index_0_blockType_i18n === 'function') entry_PeriodMenuvue_type_custom_index_0_blockType_i18n(PeriodMenu_component)

/* harmony default export */ const entry_PeriodMenu = (PeriodMenu_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodsStatsTable.vue?vue&type=template&id=f3f390e2
var PeriodsStatsTablevue_type_template_id_f3f390e2_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('b-table',{ref:"table",staticClass:"mod-presence mod-entry",attrs:{"id":_vm.id,"bordered":"","busy":_vm.isBusy,"items":_vm.items,"fields":_vm.fields,"tbody-tr-class":"table-body-row","no-sort-reset":""},on:{"update:busy":function($event){_vm.isBusy=$event}},scopedSlots:_vm._u([{key:"head(period-stats)",fn:function(){return [_vm._v(_vm._s(_vm.$t('period')))]},proxy:true},{key:"cell(period-stats)",fn:function({item}){return [_vm._v(" "+_vm._s(item.label || _vm.getPlaceHolder(item.id))+" ")]}},_vm._l((_vm.presenceStatuses),function(status){return {key:`head(status-${status.id})`,fn:function(){return [_c('div',{staticClass:"color-code",class:[status.color],staticStyle:{"width":"fit-content"},attrs:{"title":_vm.getPresenceStatusTitle(status)}},[_c('span',[_vm._v(_vm._s(status.code))])])]},proxy:true}}),{key:"head(status-none)",fn:function(){return [_c('div',{staticClass:"color-code grey-100"},[_c('span',[_vm._v(_vm._s(_vm.$t('without-status')))])])]},proxy:true},(_vm.statistics.length)?_vm._l(([..._vm.presenceStatuses, null]),function(status){return {key:`cell(status-${status && status.id || 'none'})`,fn:function({item}){return [_vm._l(([_vm.getPeriodStats(item, status)]),function(count){return [(count)?_c('div',{staticClass:"color-code",staticStyle:{"width":"fit-content","margin":"0 auto","background":"#f9f9f9"}},[_c('span',{staticStyle:{"font-variant":"initial","font-size":"13px"}},[_vm._v(_vm._s(count))])]):_c('span',{staticClass:"u-block u-text-center",staticStyle:{"color":"#a9b9bc"}},[_vm._v("0")])]})]}}}):null],null,true)})
}
var PeriodsStatsTablevue_type_template_id_f3f390e2_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodsStatsTable.vue?vue&type=script&lang=ts


let PeriodsStatsTable = class PeriodsStatsTable extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    id;
    isBusy;
    selectedPeriod;
    statusDefaults;
    periods;
    presence;
    statistics;
    get items() {
        return [...this.periodsReversed, { id: null, label: 'Gem./periode' }];
    }
    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }
    get presenceStatuses() {
        return this.presence?.statuses || [];
    }
    get fields() {
        const statusFields = this.presenceStatuses.map(status => ({ key: 'status-' + status.id, sortable: false }));
        statusFields.push({ key: 'status-none', sortable: false });
        return [
            { key: 'period-stats', sortable: false },
            ...statusFields
        ];
    }
    getPlaceHolder(periodId) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }
    getPeriodStats(periodItem, status) {
        if (!this.periods.length) {
            return 0;
        }
        if (periodItem.id === null) {
            const sum = this.periods.map(p => this.getPeriodStats(p, status)).reduce((v1, v2) => v1 + v2, 0);
            return parseFloat((sum / this.periods.length).toFixed(1));
        }
        const stat = this.statistics.find(s => s.period_id === periodItem.id && s.choice_id === (status?.id || null));
        return stat?.count || 0;
    }
    getPresenceStatus(statusId) {
        return this.presenceStatuses.find(status => status.id === statusId);
    }
    getPresenceStatusTitle(status) {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }
};
__decorate([
    Prop({ type: String, default: '' })
], PeriodsStatsTable.prototype, "id", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], PeriodsStatsTable.prototype, "isBusy", void 0);
__decorate([
    Prop({ type: Object, default: null })
], PeriodsStatsTable.prototype, "selectedPeriod", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], PeriodsStatsTable.prototype, "statusDefaults", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], PeriodsStatsTable.prototype, "periods", void 0);
__decorate([
    Prop({ type: Object, default: null })
], PeriodsStatsTable.prototype, "presence", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], PeriodsStatsTable.prototype, "statistics", void 0);
PeriodsStatsTable = __decorate([
    vue_class_component_esm({
        name: 'periods-stats-table'
    })
], PeriodsStatsTable);
/* harmony default export */ const PeriodsStatsTablevue_type_script_lang_ts = (PeriodsStatsTable);

;// CONCATENATED MODULE: ./src/components/entry/PeriodsStatsTable.vue?vue&type=script&lang=ts
 /* harmony default export */ const entry_PeriodsStatsTablevue_type_script_lang_ts = (PeriodsStatsTablevue_type_script_lang_ts); 
// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/entry/PeriodsStatsTable.vue?vue&type=custom&index=0&blockType=i18n
var PeriodsStatsTablevue_type_custom_index_0_blockType_i18n = __webpack_require__(744);
var PeriodsStatsTablevue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(PeriodsStatsTablevue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/entry/PeriodsStatsTable.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const entry_PeriodsStatsTablevue_type_custom_index_0_blockType_i18n = ((PeriodsStatsTablevue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/entry/PeriodsStatsTable.vue





/* normalize component */
;
var PeriodsStatsTable_component = normalizeComponent(
  entry_PeriodsStatsTablevue_type_script_lang_ts,
  PeriodsStatsTablevue_type_template_id_f3f390e2_render,
  PeriodsStatsTablevue_type_template_id_f3f390e2_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof entry_PeriodsStatsTablevue_type_custom_index_0_blockType_i18n === 'function') entry_PeriodsStatsTablevue_type_custom_index_0_blockType_i18n(PeriodsStatsTable_component)

/* harmony default export */ const entry_PeriodsStatsTable = (PeriodsStatsTable_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Entry.vue?vue&type=script&lang=ts













let Entry = class Entry extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    statusDefaults = [];
    presence = null;
    connector = null;
    connectorNonCourse = null;
    periods = [];
    toRemovePeriod = null;
    students = [];
    nonCourseStudents = [];
    creatingNew = false;
    createdId = null;
    pageLoaded = false;
    errorData = null;
    statusFilters = [];
    withoutStatusSelected = false;
    checkoutMode = false;
    showMore = false;
    statistics = [];
    loadingStatistics = false;
    statOptions = [
        { text: 'Geen statistiek', value: 0 },
        { text: 'Student/Status', value: 1 },
        { text: 'Status/Periode', value: 2 }
    ];
    pagination = {
        currentPage: 1,
        perPage: 15,
        total: 0
    };
    searchOptions = {
        globalSearchQuery: ''
    };
    requestCount = true;
    requestNonCourseStudents = true;
    selectedPeriod = null;
    changeAfterStatusFilters = false;
    apiConfig;
    loadIndex;
    useStatistics;
    statMode;
    get globalSearchQuery() {
        return this.searchOptions.globalSearchQuery;
    }
    set globalSearchQuery(query) {
        this.searchOptions.globalSearchQuery = query;
    }
    get defaultTableShown() {
        return !this.useStatistics || (this.useStatistics && this.statMode === 'student');
    }
    get nonCourseStudentsShown() {
        return this.defaultTableShown && this.nonCourseStudents.length;
    }
    get statusFiltersShown() {
        return this.hasSelectedPeriod && !this.useStatistics;
    }
    get paginationShown() {
        return this.defaultTableShown && this.pageLoaded && this.pagination.total > 0;
    }
    get periodStatsShown() {
        return this.useStatistics && this.statMode === 'period';
    }
    get hasSelectedPeriod() {
        return !!this.selectedPeriod;
    }
    toggleStatusFilters(status) {
        const statusFilters = this.statusFilters;
        const index = statusFilters.indexOf(status);
        if (index === -1) {
            this.statusFilters.push(status);
        }
        else {
            this.statusFilters = statusFilters.slice(0, index).concat(statusFilters.slice(index + 1));
        }
        this.requestCount = true;
        this.$emit('refresh');
    }
    refreshFilters() {
        this.requestCount = true;
        this.$emit('refresh');
    }
    toggleWithoutStatus() {
        this.withoutStatusSelected = !this.withoutStatusSelected;
        this.requestCount = true;
        this.$emit('refresh');
    }
    onFilterChanged() {
        this.requestCount = true;
    }
    onFilterCleared() {
        if (this.globalSearchQuery !== '') {
            this.globalSearchQuery = '';
            this.requestCount = true;
        }
    }
    getPresenceStatusTitle(status) {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }
    getPlaceHolder(periodId) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }
    get presenceStatuses() {
        return this.presence?.statuses || [];
    }
    applyBulkStatus(status) {
        this.showMore = false;
        if (!this.selectedPeriod) {
            return;
        }
        this.errorData = null;
        this.connector?.bulkSavePresenceEntries(this.selectedPeriod.id, status.id, (data) => {
            if (data?.status === 'ok') {
                this.$emit('refresh');
            }
        });
    }
    cancelBulkStatus() {
        this.showMore = false;
    }
    async load() {
        const presenceData = await this.connector?.loadPresence();
        if (presenceData) {
            this.statusDefaults = presenceData['status-defaults'];
            this.presence = presenceData.presence;
        }
        if (!this.presence?.has_checkout) {
            this.checkoutMode = false;
        }
    }
    async itemsProvider(ctx) {
        const parameters = {
            global_search_query: ctx.filter,
            sort_field: ctx.sortBy,
            sort_direction: ctx.sortDesc ? 'desc' : 'asc',
            items_per_page: ctx.perPage,
            page_number: ctx.currentPage,
            request_count: this.requestCount,
            request_non_course_students: this.requestNonCourseStudents
        };
        if (this.selectedPeriod && (this.statusFilters.length || this.withoutStatusSelected)) {
            parameters['period_id'] = this.selectedPeriod.id;
            parameters['status_filters'] = this.statusFilters.map(status => status.id);
            parameters['without_status'] = this.withoutStatusSelected;
        }
        const data = await this.connector?.loadPresenceEntries(parameters);
        this.changeAfterStatusFilters = false;
        const { periods, students } = data;
        this.periods = periods;
        this.students = students;
        if (data.count !== undefined) {
            this.pagination.total = data.count;
            this.requestCount = false;
        }
        if (this.requestNonCourseStudents) {
            if (data['non_course_students'] !== undefined) {
                this.nonCourseStudents = data['non_course_students'];
            }
            this.requestNonCourseStudents = false;
        }
        const selectedPeriod = this.selectedPeriod;
        if (!this.pageLoaded && this.periods.length) {
            this.setSelectedPeriod(this.periods[this.periods.length - 1].id);
            this.pageLoaded = true;
        }
        else if (this.createdId !== null) {
            this.setSelectedPeriod(this.createdId);
            this.createdId = null;
            this.creatingNew = false;
        }
        else if (selectedPeriod) {
            this.setSelectedPeriod(selectedPeriod.id);
        }
        if (!students.length) {
            return [{ tableEmpty: true }];
        }
        return students;
    }
    get isSaving() {
        return this.connector?.isSaving || false;
    }
    get isSavingNonCourse() {
        return this.connectorNonCourse?.isSaving || false;
    }
    setError(data) {
        this.errorData = data;
        console.log(this.errorData);
    }
    async createResultPeriod() {
        this.selectedPeriod = null;
        this.creatingNew = true;
        this.errorData = null;
        this.checkoutMode = false;
        await this.connector?.createResultPeriod((data) => {
            if (data?.status === 'ok') {
                this.createdId = data.id;
                this.$emit('refresh');
            }
        });
    }
    removeSelectedPeriod() {
        if (!this.selectedPeriod) {
            return;
        }
        this.errorData = null;
        const selectedPeriod = this.selectedPeriod;
        this.toRemovePeriod = selectedPeriod;
        const index = this.periods.indexOf(selectedPeriod);
        this.connector?.deletePresencePeriod(selectedPeriod.id, (data) => {
            this.toRemovePeriod = null;
            if (data?.status === 'ok') {
                this.periods.splice(index, 1);
                this.setSelectedPeriod(null);
            }
        });
    }
    setSelectedPeriod(periodId) {
        if (periodId === null) {
            this.selectedPeriod = null;
        }
        else {
            this.selectedPeriod = this.periods.find((p) => p.id === periodId) || null;
        }
        const hasFiltersSet = (this.statusFilters.length || this.withoutStatusSelected);
        if (this.selectedPeriod === null && hasFiltersSet) {
            this.statusFilters = [];
            this.withoutStatusSelected = false;
            this.requestCount = true;
            this.$emit('refresh');
        }
        else if (hasFiltersSet) {
            this.requestCount = true;
            this.$emit('refresh');
        }
        //this.checkoutMode = false;
    }
    setSelectedPeriodLabel(selectedPeriod, label) {
        this.errorData = null;
        selectedPeriod.label = label;
        this.connector?.updatePresencePeriod(selectedPeriod.id, label, selectedPeriod.period_self_registration_disabled);
    }
    setSelectedPeriodSelfRegistrationDisabled(selectedPeriod, selfRegistrationDisabled) {
        this.errorData = null;
        selectedPeriod.period_self_registration_disabled = selfRegistrationDisabled;
        this.connector?.updatePresencePeriod(selectedPeriod.id, selectedPeriod.label, selfRegistrationDisabled);
    }
    async setSelectedStudentStatus(student, selectedPeriod, status, isFullyEditable = true) {
        this.errorData = null;
        const periodId = selectedPeriod.id;
        student[`period#${periodId}-status`] = status;
        if (isFullyEditable && (this.statusFilters.length || this.withoutStatusSelected)) {
            this.changeAfterStatusFilters = true;
        }
        const connector = isFullyEditable ? this.connector : this.connectorNonCourse;
        connector?.savePresenceEntry(periodId, student.id, status, function (data) {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }
    toggleCheckout(student, selectedPeriod, isFullyEditable = true) {
        const periodId = selectedPeriod.id;
        if (!student[`period#${periodId}-checked_in_date`]) {
            return;
        }
        const connector = isFullyEditable ? this.connector : this.connectorNonCourse;
        connector?.togglePresenceEntryCheckout(periodId, student.id, (data) => {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }
    mounted() {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this);
        this.connectorNonCourse = new Connector(this.apiConfig);
        this.connectorNonCourse.addErrorListener(this);
        this.load();
    }
    _loadIndex() {
        this.load();
    }
    async _statMode() {
        if (this.statMode === 'period') {
            this.loadingStatistics = true;
            this.statistics = [];
            const data = await this.connector?.loadStatistics() || null;
            this.loadingStatistics = false;
            this.statistics = data?.statistics || [];
        }
    }
};
__decorate([
    Prop({ type: APIConfig, required: true })
], Entry.prototype, "apiConfig", void 0);
__decorate([
    Prop({ type: Number, default: 0 })
], Entry.prototype, "loadIndex", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], Entry.prototype, "useStatistics", void 0);
__decorate([
    Prop({ type: String, default: '' })
], Entry.prototype, "statMode", void 0);
__decorate([
    Watch('loadIndex')
], Entry.prototype, "_loadIndex", null);
__decorate([
    Watch('statMode')
], Entry.prototype, "_statMode", null);
Entry = __decorate([
    vue_class_component_esm({
        name: 'entry',
        components: { PeriodsStatsTable: entry_PeriodsStatsTable, EntryTable: entry_EntryTable, OnOffSwitch: components_OnOffSwitch, FilterStatusButton: entry_FilterStatusButton, SearchBar: entry_SearchBar, LegendItem: entry_LegendItem, DynamicFieldKey: DynamicFieldKey, PeriodMenu: entry_PeriodMenu, ErrorDisplay: components_ErrorDisplay }
    })
], Entry);
/* harmony default export */ const Entryvue_type_script_lang_ts = (Entry);

;// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_Entryvue_type_script_lang_ts = (Entryvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Entry.vue?vue&type=style&index=0&id=07388830&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=style&index=0&id=07388830&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Entry.vue?vue&type=style&index=1&id=07388830&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=style&index=1&id=07388830&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Entry.vue?vue&type=custom&index=0&blockType=i18n
var Entryvue_type_custom_index_0_blockType_i18n = __webpack_require__(296);
var Entryvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(Entryvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_Entryvue_type_custom_index_0_blockType_i18n = ((Entryvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/Entry.vue



;



/* normalize component */

var Entry_component = normalizeComponent(
  components_Entryvue_type_script_lang_ts,
  Entryvue_type_template_id_07388830_scoped_true_render,
  Entryvue_type_template_id_07388830_scoped_true_staticRenderFns,
  false,
  null,
  "07388830",
  null
  
)

/* custom blocks */
;
if (typeof components_Entryvue_type_custom_index_0_blockType_i18n === 'function') components_Entryvue_type_custom_index_0_blockType_i18n(Entry_component)

/* harmony default export */ const components_Entry = (Entry_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserEntry.vue?vue&type=template&id=afa2f7a2&scoped=true
var UserEntryvue_type_template_id_afa2f7a2_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"u-flex"},[_c('div',{staticClass:"w-max-content"},[_c('div',{staticClass:"u-relative"},[_c('div',{staticClass:"u-flex u-align-items-baseline u-flex-wrap u-gap-small-3x m-legend"},[_c('span',{staticClass:"lbl-legend"},[_vm._v(_vm._s(_vm.$t('legend'))+":")]),_vm._l((_vm.presenceStatuses),function(status){return _c('legend-item',{attrs:{"title":_vm.getPresenceStatusTitle(status),"label":status.code,"color":status.color}})})],2),(_vm.student)?_c('b-table-simple',{staticClass:"mod-presence mod-user"},[_c('b-thead',[_c('b-tr',{staticClass:"table-body-row"},[_c('b-th',[_vm._v(_vm._s(_vm.$t('period')))]),_c('b-th',[_vm._v("Status")]),_c('b-th',[_vm._v(_vm._s(_vm.$t('checked-in')))]),(_vm.presence && _vm.presence.has_checkout)?_c('b-th',[_vm._v(_vm._s(_vm.$t('checked-out')))]):_vm._e()],1)],1),_c('b-tbody',_vm._l((_vm.periodsReversed),function(period){return _c('student-details',{staticClass:"table-body-row",attrs:{"has-checkout":_vm.presence && _vm.presence.has_checkout,"show-status":true,"period-title":period.label || _vm.getPlaceHolder(period.id),"title":_vm.getStatusTitleForStudent(period.id),"label":_vm.getStatusCodeForStudent(period.id),"color":_vm.getStatusColorForStudent(period.id),"check-in-date":_vm.student[`period#${period.id}-checked_in_date`],"check-out-date":_vm.student[`period#${period.id}-checked_out_date`]}})}),1)],1):_vm._e()],1),(_vm.errorData)?_c('error-display',{on:{"close":function($event){_vm.errorData = null}}},[(_vm.errorData.code === 500)?_c('span',[_vm._v(_vm._s(_vm.errorData.message))]):(!!_vm.errorData.type)?_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type)))]):_vm._e()]):_vm._e()],1)])
}
var UserEntryvue_type_template_id_afa2f7a2_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserEntry.vue?vue&type=script&lang=ts







let UserEntry = class UserEntry extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    statusDefaults = [];
    presence = null;
    connector = null;
    periods = [];
    student = {};
    errorData = null;
    apiConfig;
    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }
    getPresenceStatus(statusId) {
        return this.presenceStatuses.find(status => status.id === statusId);
    }
    getStudentStatusForPeriod(periodId) {
        return this.student[`period#${periodId}-status`];
    }
    getStatusCodeForStudent(periodId) {
        return this.getPresenceStatus(this.getStudentStatusForPeriod(periodId))?.code || '';
    }
    getStatusColorForStudent(periodId) {
        return this.getPresenceStatus(this.getStudentStatusForPeriod(periodId))?.color || '';
    }
    getStatusTitleForStudent(periodId) {
        const status = this.getPresenceStatus(this.getStudentStatusForPeriod(periodId));
        return status ? this.getPresenceStatusTitle(status) : '';
    }
    getPresenceStatusTitle(status) {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }
    getPlaceHolder(periodId) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }
    get presenceStatuses() {
        return this.presence?.statuses || [];
    }
    async load() {
        const presenceData = await this.connector?.loadPresence();
        if (presenceData) {
            this.statusDefaults = presenceData['status-defaults'];
            this.presence = presenceData.presence;
        }
        const { periods, students } = await this.connector?.loadPresenceEntries({});
        this.periods = periods;
        this.student = students[0];
    }
    setError(data) {
        this.errorData = data;
    }
    mounted() {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this);
        this.load();
    }
};
__decorate([
    Prop({ type: APIConfig, required: true })
], UserEntry.prototype, "apiConfig", void 0);
UserEntry = __decorate([
    vue_class_component_esm({
        name: 'user-entry',
        components: { StudentDetails: entry_StudentDetails, LegendItem: entry_LegendItem, ErrorDisplay: components_ErrorDisplay }
    })
], UserEntry);
/* harmony default export */ const UserEntryvue_type_script_lang_ts = (UserEntry);

;// CONCATENATED MODULE: ./src/components/UserEntry.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_UserEntryvue_type_script_lang_ts = (UserEntryvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserEntry.vue?vue&type=style&index=0&id=afa2f7a2&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/UserEntry.vue?vue&type=style&index=0&id=afa2f7a2&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserEntry.vue?vue&type=style&index=1&id=afa2f7a2&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/UserEntry.vue?vue&type=style&index=1&id=afa2f7a2&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserEntry.vue?vue&type=custom&index=0&blockType=i18n
var UserEntryvue_type_custom_index_0_blockType_i18n = __webpack_require__(356);
var UserEntryvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(UserEntryvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/UserEntry.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_UserEntryvue_type_custom_index_0_blockType_i18n = ((UserEntryvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/UserEntry.vue



;



/* normalize component */

var UserEntry_component = normalizeComponent(
  components_UserEntryvue_type_script_lang_ts,
  UserEntryvue_type_template_id_afa2f7a2_scoped_true_render,
  UserEntryvue_type_template_id_afa2f7a2_scoped_true_staticRenderFns,
  false,
  null,
  "afa2f7a2",
  null
  
)

/* custom blocks */
;
if (typeof components_UserEntryvue_type_custom_index_0_blockType_i18n === 'function') components_UserEntryvue_type_custom_index_0_blockType_i18n(UserEntry_component)

/* harmony default export */ const components_UserEntry = (UserEntry_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Properties.vue?vue&type=template&id=0b36abfe
var Propertiesvue_type_template_id_0b36abfe_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.presence)?_c('div',[_c('on-off-switch',{staticStyle:{"margin-bottom":"15px"},attrs:{"id":"disable-selfreg-global-check","switch-class":"mod-self-disable","on-text":_vm.$t('self-registration-on'),"off-text":_vm.$t('self-registration-off'),"checked":!_vm.presence.global_self_registration_disabled},on:{"toggle":_vm.selfRegistrationChanged}}),(!_vm.presence.global_self_registration_disabled)?_c('a',{staticStyle:{"display":"block","font-size":"15px","margin-bottom":"15px"},attrs:{"href":_vm.apiConfig.printQrCodeURL,"target":"_blank"}},[_c('i',{staticClass:"fa fa-print",staticStyle:{"margin-right":"5px"},attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('print-qr')))]):_vm._e(),(_vm.errorData)?_c('error-display',{on:{"close":function($event){_vm.errorData = null}}},[(_vm.errorData.code === 500)?_c('span',[_vm._v(_vm._s(_vm.errorData.message))]):(!!_vm.errorData.type)?_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type)))]):_vm._e()]):_vm._e()],1):_vm._e()
}
var Propertiesvue_type_template_id_0b36abfe_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Properties.vue?vue&type=script&lang=ts






let Properties = class Properties extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    presence = null;
    connector = null;
    errorData = null;
    apiConfig;
    async load() {
        const presenceData = await this.connector?.loadPresence();
        this.presence = presenceData?.presence || null;
    }
    selfRegistrationChanged() {
        if (!this.presence) {
            return;
        }
        this.errorData = null;
        const finished = !this.presence.global_self_registration_disabled;
        this.presence.global_self_registration_disabled = finished;
        this.connector?.updatePresenceGlobalSelfRegistration(this.presence.id, finished);
    }
    setError(data) {
        this.errorData = data;
    }
    mounted() {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this);
        this.load();
    }
};
__decorate([
    Prop({ type: APIConfig, required: true })
], Properties.prototype, "apiConfig", void 0);
Properties = __decorate([
    vue_class_component_esm({
        name: 'properties',
        components: { OnOffSwitch: components_OnOffSwitch, ErrorDisplay: components_ErrorDisplay }
    })
], Properties);
/* harmony default export */ const Propertiesvue_type_script_lang_ts = (Properties);

;// CONCATENATED MODULE: ./src/components/Properties.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_Propertiesvue_type_script_lang_ts = (Propertiesvue_type_script_lang_ts); 
// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Properties.vue?vue&type=custom&index=0&blockType=i18n
var Propertiesvue_type_custom_index_0_blockType_i18n = __webpack_require__(26);
var Propertiesvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(Propertiesvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/Properties.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_Propertiesvue_type_custom_index_0_blockType_i18n = ((Propertiesvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/Properties.vue





/* normalize component */
;
var Properties_component = normalizeComponent(
  components_Propertiesvue_type_script_lang_ts,
  Propertiesvue_type_template_id_0b36abfe_render,
  Propertiesvue_type_template_id_0b36abfe_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */
;
if (typeof components_Propertiesvue_type_custom_index_0_blockType_i18n === 'function') components_Propertiesvue_type_custom_index_0_blockType_i18n(Properties_component)

/* harmony default export */ const components_Properties = (Properties_component.exports);
;// CONCATENATED MODULE: ./src/plugin.ts




/* harmony default export */ const src_plugin = ({
    install(Vue) {
        Vue.component('PresenceBuilder', components_Builder);
        Vue.component('PresenceEntry', components_Entry);
        Vue.component('PresenceUserEntry', components_UserEntry);
        Vue.component('PresenceProperties', components_Properties);
    }
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ const entry_lib = (src_plugin);


})();

/******/ 	return __webpack_exports__;
/******/ })()
;
});
//# sourceMappingURL=cosnics-presence.umd.js.map