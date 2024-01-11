(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("vue"));
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["cosnics-gradebook"] = factory(require("vue"));
	else
		root["cosnics-gradebook"] = factory(root["Vue"]);
})((typeof self !== 'undefined' ? self : this), (__WEBPACK_EXTERNAL_MODULE__203__) => {
return /******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 762:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"choose-file":"Choose file","choose-type":"Choose type","correct-mistakes":"Please correct any errors and","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","file-with":"File with","go-to-gradebook":"Go to gradebook","import":"Import","import-complete":"Import complete","import-preview":"Preview","import-results-overview":"You can find an overview of the results from the CSV file below. Click the button below to import. Only results that are valid will be imported.","import-steps":"Import steps","import-successful":"The results have been successfully imported.","no-results-some-students":"Careful! For some subscribed students no matching results have been found. See below.<br>You can still make manual adjustments.","question-upload":"What kind of file do you want to upload?","reupload-results":"reupload","select-file":"Select a file...","type-scores":"1 or more score columns","type-scores-comments":"1 score column and 1 feedback column","upload":"Upload","user-not-in-course":"Student is not subscribed to this course","without-results":"No results"},"nl":{"choose-file":"Kies bestand","choose-type":"Kies type","correct-mistakes":"Gelieve de fout(en) te verbeteren en de resultaten","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","file-with":"Bestand met","go-to-gradebook":"Ga naar puntenboekje","import":"Importeer","import-complete":"Importeren voltooid","import-preview":"Voorbeeldweergave","import-results-overview":"Hieronder vind je een overzicht van de resultaten uit het CSV-bestand. Klik op de knop hieronder om te importeren. Enkel de geldige resultaten zullen worden geïmporteerd.","import-steps":"Import-stappen","import-successful":"De resultaten werden succesvol geïmporteerd.","no-results-some-students":"Let op! Voor sommige ingeschreven studenten werden geen resultaten gevonden. Zie hieronder.<br>Gelieve deze handmatig aan te passen.","question-upload":"Wat voor bestand wil je opladen?","reupload-results":"opnieuw op te laden","select-file":"Kies een bestand...","type-scores":"1 of meerdere scorekolommen","type-scores-comments":"1 scorekolom en 1 feedbackkolom","upload":"Upload","user-not-in-course":"Student maakt geen deel uit van deze cursus","without-results":"Zonder resultaat"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 793:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"comment":"comment","csv-must-look-like":"The CSV file must look like this","import-comment-title":"Choose a title for the column of feedback you wish to import (can be left empty).","import-id":"One of the following: <ul><li>email address</li><li>username</li><li>official code</li></ul>","import-score":"One of the following: <ul><li>number</li><li>aabs (authorized absent)</li></ul>","import-score-title":"Choose a title for the column of scores you wish to import.","mandatory-fields":"mandatory fields are marked in bold","max-score":"Max score (for example: 20)","title":"title"},"nl":{"comment":"commentaar","csv-must-look-like":"Het CSV bestand moet er als volgt uit zien","import-comment-title":"Kies hier een titel voor de feedbackkolom die je wenst te importeren (mag ook leeggelaten worden).","import-id":"Een van de volgende: <ul><li>e-mailadres</li><li>gebruikersnaam</li><li>officiële code (stamboeknummer)</li></ul>","import-score":"Een van de volgende: <ul><li>cijfer</li><li>gafw (gewettigd afwezig)</li></ul>","import-score-title":"Kies hier een titel voor de scorekolom die je wenst te importeren.","mandatory-fields":"verplichte velden zijn in het vet aangeduid","max-score":"Maximumscore (bvb.: 20)","title":"titel"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 169:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"cancel":"Cancel","category-settings":"Category settings","close":"Close","remove":"Remove","remove-category":"Remove category?"},"nl":{"cancel":"Annuleren","category-settings":"Categorie-instellingen","close":"Sluiten","remove":"Verwijderen","remove-category":"Categorie verwijderen?"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 220:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"display-total":"Max score"},"nl":{"display-total":"Max score"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 820:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"errors":"Error(s)"},"nl":{"errors":"Fout(en)"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 802:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"close":"Close","display-total":"Max score","final-score":"Final score","final-score-settings":"Final score settings","settings":"Settings"},"nl":{"close":"Sluiten","display-total":"Max score","final-score":"Eindcijfer","final-score-settings":"Eindscore-instellingen","settings":"Instellingen"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 218:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"add-remove-scores":"Add/Remove scores","cancel":"Cancel","remove":"Remove","remove-from-overview":"Remove score \u0027{title}\u0027 from overview?"},"nl":{"add-remove-scores":"Scores toevoegen/verwijderen","cancel":"Annuleren","remove":"Verwijderen","remove-from-overview":"Score \u0027{title}\u0027 verwijderen uit overzicht?"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 900:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"adjust-title":"Adjust title","adjust-weight":"Adjust weight","category-settings":"Category Settings","count-towards-endresult-not":"Score does not count towards final result","final-score":"Final score","final-score-settings":"Final score Settings","first-name":"First name","grouped-score":"Grouped score","invisible":"Final score is hidden","item-settings":"Score Settings","last-name":"NAME","make-invisible":"Score is shown. Click to hide.","make-visible":"Score is hidden. Click to show.","saving":"Saving","source-results-warning":"The results of this column refers to source data that no longer exists. You can keep on using this data but synchronizing will have no effect on this column. If you remove the column its results will be gone forever.","total":"Total","uncounted":"Not counted","visible":"Final score is shown","without-category":"Without category"},"nl":{"adjust-title":"Pas titel aan","adjust-weight":"Pas gewicht aan","category-settings":"Categorie-instellingen","count-towards-endresult-not":"Score wordt niet meegeteld voor het eindresultaat","final-score":"Eindcijfer","final-score-settings":"Eindcijfer instellingen","first-name":"Voornaam","grouped-score":"Gegroepeerde score","invisible":"Eindscore is verborgen","item-settings":"Score-instellingen","last-name":"FAMILIENAAM","make-invisible":"Score wordt weergegeven. Klik om te verbergen.","make-visible":"Score is verborgen. Klik om te tonen.","saving":"Aan het opslaan","source-results-warning":"De resultaten in deze kolom verwijzen naar brondata die niet meer bestaat. Je kan de data verder blijven gebruiken maar synchroniseren zal op deze kolom geen effect hebben. Als je de kolom verwijdert zijn de resultaten ervan voorgoed weg.","total":"Totaal","uncounted":"Niet meegeteld","visible":"Eindscore wordt weergegeven","without-category":"Zonder categorie"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 212:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"all-imports":"All imports","auth-absent":"Authorized absent","no-score-found":"No score found","not-subscribed":"Not subscribed to course","show":"Show","total":"Total","user-not-in-course":"Student is not subscribed to this course","valid-imports":"Valid imports"},"nl":{"all-imports":"Alle imports","auth-absent":"Gewettigd afwezig","no-score-found":"Geen score gevonden","not-subscribed":"Niet ingeschreven in cursus","show":"Toon","total":"Totaal","user-not-in-course":"Student maakt geen deel uit van deze cursus","valid-imports":"Geldige imports"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 980:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"aabs":"aabs","auth-absent":"Authorized absent","authorized-absence":"In the event of authorized absence","cancel":"Cancel","close":"Close","column-settings":"Column settings","count-towards-endresult":"Count towards end result","count-towards-endresult-not":"Score does not count towards final result","group-scores":"Group scores","grouped-scores":"Grouped scores","make-visible":"Show score to student","maximum-towards-endresult":"Maximum score (100%) counts towards final result","minimum-towards-endresult":"Minimum score (0%) counts towards final result","no-score-found":"No score found","remove":"Remove","remove-from-overview":"Remove score \u0027{title}\u0027 from overview?","settings":"Settings","unauthorized-absence":"In the absence of a score (without authorized absence)","weight":"Weight"},"nl":{"aabs":"gafw","auth-absent":"Gewettigd afwezig","authorized-absence":"Bij gewettigde afwezigheid","cancel":"Annuleren","close":"Sluiten","column-settings":"Kolominstellingen","count-towards-endresult":"Meetellen voor eindresultaat","count-towards-endresult-not":"Score niet meetellen voor het eindresultaat","group-scores":"Scores groeperen","grouped-scores":"Gegroepeerde scores","make-visible":"Score weergeven voor student","maximum-towards-endresult":"Maximale score (100%) meetellen voor het eindresultaat","minimum-towards-endresult":"Minimale score (0%) meetellen voor het eindresultaat","no-score-found":"Geen score gevonden","remove":"Verwijderen","remove-from-overview":"Score \u0027{title}\u0027 verwijderen uit overzicht?","settings":"Instellingen","unauthorized-absence":"Bij ontbreken van score (zonder gewettigde afwezigheid)","weight":"Gewicht"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 852:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"category":"Category","error-Conflict":"The server responded with an error due to a conflict. Probably someone else is working on the same gradebook at this time. Please refresh the page and try again.","error-Forbidden":"The server responded with an error. Possibly your last change(s) haven\u0027t been saved correctly. Please refresh the page and try again.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-NotFound":"The server responded with an error. Possibly your last change(s) haven\u0027t been saved correctly. Please refresh the page and try again.","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-Unknown":"An unknown error happened. Possibly your last change(s) haven\u0027t been saved. Please refresh the page and try again.","export":"Export","find-student":"Find student","import":"Import","new":"New","new-category":"New category","new-score":"New score","show":"Show","synchronize-scores":"Synchronize","update-final-scores":"Update final scores","update-final-scores-before-exporting":"Update final scores before exporting"},"nl":{"category":"Categorie","error-Conflict":"Serverfout vanwege een conflict. Misschien werkt iemand anders ook nog aan dit puntenboekje op dit moment. Gelieve de pagina te herladen en opnieuw te proberen.","error-Forbidden":"Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-NotFound":"Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Je laatste wijzigingen werden mogelijk niet opgeslagen vanwege een onbekende fout. Gelieve de pagina te herladen en opnieuw te proberen.","export":"Exporteer","find-student":"Zoek student","import":"Importeer","new":"Nieuw","new-category":"Nieuwe categorie","new-score":"Nieuwe score","show":"Toon","synchronize-scores":"Synchroniseer","update-final-scores":"Update eindcijfers","update-final-scores-before-exporting":"Update eindcijfers alvorens te exporteren"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 887:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"first-name":"First name","last-name":"Last name","official-code":"Official code"},"nl":{"first-name":"Voornaam","last-name":"Achternaam","official-code":"Officiële code"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 713:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"aabs":"AABS","absent":"Absent","auth-absent":"Authorized absent","comments":"Comments","score":"Score","use-source-result":"Use source result"},"nl":{"aabs":"GAFW","absent":"Afwezig","auth-absent":"Gewettigd afwezig","comments":"Opmerkingen","score":"Score","use-source-result":"Gebruik bronresultaat"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 667:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"aabs":"aabs","auth-absent":"Authorized absent","edit-comment":"Edit comments","no-score":"No score","no-score-abbr":"n/a","no-score-found":"No score found"},"nl":{"aabs":"gafw","auth-absent":"Gewettigd afwezig","edit-comment":"Wijzig opmerkingen","no-score":"Geen score","no-score-abbr":"n.b.","no-score-found":"Geen score gevonden"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 309:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"not-synchronized":"Not synchronized","not-yet-updated":"Final score not yet updated"},"nl":{"not-synchronized":"Niet gesynchroniseerd","not-yet-updated":"Eindcijfer nog niet geüpdated"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 694:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"cancel":"Cancel","edit":"Edit"},"nl":{"cancel":"Annuleren","edit":"Wijzigen"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 480:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"count-towards-endresult-not":"Score does not count towards final result","final-score":"Final score","not-yet-released":"Not yet released","title":"Title","score":"Score","weight":"Weight"},"nl":{"count-towards-endresult-not":"Score telt niet mee voor het eindresultaat","final-score":"Eindcijfer","not-yet-released":"Nog niet vrijgegeven","title":"Titel","score":"Score","weight":"Gewicht"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 481:
/***/ ((module) => {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"weight":"Weight"},"nl":{"weight":"Gewicht"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ 678:
/***/ ((module) => {

/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing. The function also has a property 'clear' 
 * that is a function which will clear the timer to prevent previously scheduled executions. 
 *
 * @source underscore.js
 * @see http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
 * @param {Function} function to wrap
 * @param {Number} timeout in ms (`100`)
 * @param {Boolean} whether to execute at the beginning (`false`)
 * @api public
 */
function debounce(func, wait, immediate){
  var timeout, args, context, timestamp, result;
  if (null == wait) wait = 100;

  function later() {
    var last = Date.now() - timestamp;

    if (last < wait && last >= 0) {
      timeout = setTimeout(later, wait - last);
    } else {
      timeout = null;
      if (!immediate) {
        result = func.apply(context, args);
        context = args = null;
      }
    }
  };

  var debounced = function(){
    context = this;
    args = arguments;
    timestamp = Date.now();
    var callNow = immediate && !timeout;
    if (!timeout) timeout = setTimeout(later, wait);
    if (callNow) {
      result = func.apply(context, args);
      context = args = null;
    }

    return result;
  };

  debounced.clear = function() {
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  };
  
  debounced.flush = function() {
    if (timeout) {
      result = func.apply(context, args);
      context = args = null;
      
      clearTimeout(timeout);
      timeout = null;
    }
  };

  return debounced;
};

// Adds compatibility for ES modules
debounce.debounce = debounce;

module.exports = debounce;


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

/***/ 386:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MultiDrag: () => (/* binding */ MultiDragPlugin),
/* harmony export */   Sortable: () => (/* binding */ Sortable),
/* harmony export */   Swap: () => (/* binding */ SwapPlugin),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/**!
 * Sortable 1.10.2
 * @author	RubaXa   <trash@rubaxa.org>
 * @author	owenm    <owen23355@gmail.com>
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

function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      _defineProperty(target, key, source[key]);
    });
  }

  return target;
}

function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};

  var target = _objectWithoutPropertiesLoose(source, excluded);

  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
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

var version = "1.10.2";

function userAgent(pattern) {
  if (typeof window !== 'undefined' && window.navigator) {
    return !!
    /*@__PURE__*/
    navigator.userAgent.match(pattern);
  }
}

var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
var Edge = userAgent(/Edge/i);
var FireFox = userAgent(/firefox/i);
var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
var IOS = userAgent(/iP(ad|od|hone)/i);
var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);

var captureMode = {
  capture: false,
  passive: false
};

function on(el, event, fn) {
  el.addEventListener(event, fn, !IE11OrLess && captureMode);
}

function off(el, event, fn) {
  el.removeEventListener(event, fn, !IE11OrLess && captureMode);
}

function matches(
/**HTMLElement*/
el,
/**String*/
selector) {
  if (!selector) return;
  selector[0] === '>' && (selector = selector.substring(1));

  if (el) {
    try {
      if (el.matches) {
        return el.matches(selector);
      } else if (el.msMatchesSelector) {
        return el.msMatchesSelector(selector);
      } else if (el.webkitMatchesSelector) {
        return el.webkitMatchesSelector(selector);
      }
    } catch (_) {
      return false;
    }
  }

  return false;
}

function getParentOrHost(el) {
  return el.host && el !== document && el.host.nodeType ? el.host : el.parentNode;
}

function closest(
/**HTMLElement*/
el,
/**String*/
selector,
/**HTMLElement*/
ctx, includeCTX) {
  if (el) {
    ctx = ctx || document;

    do {
      if (selector != null && (selector[0] === '>' ? el.parentNode === ctx && matches(el, selector) : matches(el, selector)) || includeCTX && el === ctx) {
        return el;
      }

      if (el === ctx) break;
      /* jshint boss:true */
    } while (el = getParentOrHost(el));
  }

  return null;
}

var R_SPACE = /\s+/g;

function toggleClass(el, name, state) {
  if (el && name) {
    if (el.classList) {
      el.classList[state ? 'add' : 'remove'](name);
    } else {
      var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
      el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
    }
  }
}

function css(el, prop, val) {
  var style = el && el.style;

  if (style) {
    if (val === void 0) {
      if (document.defaultView && document.defaultView.getComputedStyle) {
        val = document.defaultView.getComputedStyle(el, '');
      } else if (el.currentStyle) {
        val = el.currentStyle;
      }

      return prop === void 0 ? val : val[prop];
    } else {
      if (!(prop in style) && prop.indexOf('webkit') === -1) {
        prop = '-webkit-' + prop;
      }

      style[prop] = val + (typeof val === 'string' ? '' : 'px');
    }
  }
}

function matrix(el, selfOnly) {
  var appliedTransforms = '';

  if (typeof el === 'string') {
    appliedTransforms = el;
  } else {
    do {
      var transform = css(el, 'transform');

      if (transform && transform !== 'none') {
        appliedTransforms = transform + ' ' + appliedTransforms;
      }
      /* jshint boss:true */

    } while (!selfOnly && (el = el.parentNode));
  }

  var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
  /*jshint -W056 */

  return matrixFn && new matrixFn(appliedTransforms);
}

function find(ctx, tagName, iterator) {
  if (ctx) {
    var list = ctx.getElementsByTagName(tagName),
        i = 0,
        n = list.length;

    if (iterator) {
      for (; i < n; i++) {
        iterator(list[i], i);
      }
    }

    return list;
  }

  return [];
}

function getWindowScrollingElement() {
  var scrollingElement = document.scrollingElement;

  if (scrollingElement) {
    return scrollingElement;
  } else {
    return document.documentElement;
  }
}
/**
 * Returns the "bounding client rect" of given element
 * @param  {HTMLElement} el                       The element whose boundingClientRect is wanted
 * @param  {[Boolean]} relativeToContainingBlock  Whether the rect should be relative to the containing block of (including) the container
 * @param  {[Boolean]} relativeToNonStaticParent  Whether the rect should be relative to the relative parent of (including) the contaienr
 * @param  {[Boolean]} undoScale                  Whether the container's scale() should be undone
 * @param  {[HTMLElement]} container              The parent the element will be placed in
 * @return {Object}                               The boundingClientRect of el, with specified adjustments
 */


function getRect(el, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
  if (!el.getBoundingClientRect && el !== window) return;
  var elRect, top, left, bottom, right, height, width;

  if (el !== window && el !== getWindowScrollingElement()) {
    elRect = el.getBoundingClientRect();
    top = elRect.top;
    left = elRect.left;
    bottom = elRect.bottom;
    right = elRect.right;
    height = elRect.height;
    width = elRect.width;
  } else {
    top = 0;
    left = 0;
    bottom = window.innerHeight;
    right = window.innerWidth;
    height = window.innerHeight;
    width = window.innerWidth;
  }

  if ((relativeToContainingBlock || relativeToNonStaticParent) && el !== window) {
    // Adjust for translate()
    container = container || el.parentNode; // solves #1123 (see: https://stackoverflow.com/a/37953806/6088312)
    // Not needed on <= IE11

    if (!IE11OrLess) {
      do {
        if (container && container.getBoundingClientRect && (css(container, 'transform') !== 'none' || relativeToNonStaticParent && css(container, 'position') !== 'static')) {
          var containerRect = container.getBoundingClientRect(); // Set relative to edges of padding box of container

          top -= containerRect.top + parseInt(css(container, 'border-top-width'));
          left -= containerRect.left + parseInt(css(container, 'border-left-width'));
          bottom = top + elRect.height;
          right = left + elRect.width;
          break;
        }
        /* jshint boss:true */

      } while (container = container.parentNode);
    }
  }

  if (undoScale && el !== window) {
    // Adjust for scale()
    var elMatrix = matrix(container || el),
        scaleX = elMatrix && elMatrix.a,
        scaleY = elMatrix && elMatrix.d;

    if (elMatrix) {
      top /= scaleY;
      left /= scaleX;
      width /= scaleX;
      height /= scaleY;
      bottom = top + height;
      right = left + width;
    }
  }

  return {
    top: top,
    left: left,
    bottom: bottom,
    right: right,
    width: width,
    height: height
  };
}
/**
 * Checks if a side of an element is scrolled past a side of its parents
 * @param  {HTMLElement}  el           The element who's side being scrolled out of view is in question
 * @param  {String}       elSide       Side of the element in question ('top', 'left', 'right', 'bottom')
 * @param  {String}       parentSide   Side of the parent in question ('top', 'left', 'right', 'bottom')
 * @return {HTMLElement}               The parent scroll element that the el's side is scrolled past, or null if there is no such element
 */


function isScrolledPast(el, elSide, parentSide) {
  var parent = getParentAutoScrollElement(el, true),
      elSideVal = getRect(el)[elSide];
  /* jshint boss:true */

  while (parent) {
    var parentSideVal = getRect(parent)[parentSide],
        visible = void 0;

    if (parentSide === 'top' || parentSide === 'left') {
      visible = elSideVal >= parentSideVal;
    } else {
      visible = elSideVal <= parentSideVal;
    }

    if (!visible) return parent;
    if (parent === getWindowScrollingElement()) break;
    parent = getParentAutoScrollElement(parent, false);
  }

  return false;
}
/**
 * Gets nth child of el, ignoring hidden children, sortable's elements (does not ignore clone if it's visible)
 * and non-draggable elements
 * @param  {HTMLElement} el       The parent element
 * @param  {Number} childNum      The index of the child
 * @param  {Object} options       Parent Sortable's options
 * @return {HTMLElement}          The child at index childNum, or null if not found
 */


function getChild(el, childNum, options) {
  var currentChild = 0,
      i = 0,
      children = el.children;

  while (i < children.length) {
    if (children[i].style.display !== 'none' && children[i] !== Sortable.ghost && children[i] !== Sortable.dragged && closest(children[i], options.draggable, el, false)) {
      if (currentChild === childNum) {
        return children[i];
      }

      currentChild++;
    }

    i++;
  }

  return null;
}
/**
 * Gets the last child in the el, ignoring ghostEl or invisible elements (clones)
 * @param  {HTMLElement} el       Parent element
 * @param  {selector} selector    Any other elements that should be ignored
 * @return {HTMLElement}          The last child, ignoring ghostEl
 */


function lastChild(el, selector) {
  var last = el.lastElementChild;

  while (last && (last === Sortable.ghost || css(last, 'display') === 'none' || selector && !matches(last, selector))) {
    last = last.previousElementSibling;
  }

  return last || null;
}
/**
 * Returns the index of an element within its parent for a selected set of
 * elements
 * @param  {HTMLElement} el
 * @param  {selector} selector
 * @return {number}
 */


function index(el, selector) {
  var index = 0;

  if (!el || !el.parentNode) {
    return -1;
  }
  /* jshint boss:true */


  while (el = el.previousElementSibling) {
    if (el.nodeName.toUpperCase() !== 'TEMPLATE' && el !== Sortable.clone && (!selector || matches(el, selector))) {
      index++;
    }
  }

  return index;
}
/**
 * Returns the scroll offset of the given element, added with all the scroll offsets of parent elements.
 * The value is returned in real pixels.
 * @param  {HTMLElement} el
 * @return {Array}             Offsets in the format of [left, top]
 */


function getRelativeScrollOffset(el) {
  var offsetLeft = 0,
      offsetTop = 0,
      winScroller = getWindowScrollingElement();

  if (el) {
    do {
      var elMatrix = matrix(el),
          scaleX = elMatrix.a,
          scaleY = elMatrix.d;
      offsetLeft += el.scrollLeft * scaleX;
      offsetTop += el.scrollTop * scaleY;
    } while (el !== winScroller && (el = el.parentNode));
  }

  return [offsetLeft, offsetTop];
}
/**
 * Returns the index of the object within the given array
 * @param  {Array} arr   Array that may or may not hold the object
 * @param  {Object} obj  An object that has a key-value pair unique to and identical to a key-value pair in the object you want to find
 * @return {Number}      The index of the object in the array, or -1
 */


function indexOfObject(arr, obj) {
  for (var i in arr) {
    if (!arr.hasOwnProperty(i)) continue;

    for (var key in obj) {
      if (obj.hasOwnProperty(key) && obj[key] === arr[i][key]) return Number(i);
    }
  }

  return -1;
}

function getParentAutoScrollElement(el, includeSelf) {
  // skip to window
  if (!el || !el.getBoundingClientRect) return getWindowScrollingElement();
  var elem = el;
  var gotSelf = false;

  do {
    // we don't need to get elem css if it isn't even overflowing in the first place (performance)
    if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
      var elemCSS = css(elem);

      if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == 'auto' || elemCSS.overflowX == 'scroll') || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == 'auto' || elemCSS.overflowY == 'scroll')) {
        if (!elem.getBoundingClientRect || elem === document.body) return getWindowScrollingElement();
        if (gotSelf || includeSelf) return elem;
        gotSelf = true;
      }
    }
    /* jshint boss:true */

  } while (elem = elem.parentNode);

  return getWindowScrollingElement();
}

function extend(dst, src) {
  if (dst && src) {
    for (var key in src) {
      if (src.hasOwnProperty(key)) {
        dst[key] = src[key];
      }
    }
  }

  return dst;
}

function isRectEqual(rect1, rect2) {
  return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
}

var _throttleTimeout;

function throttle(callback, ms) {
  return function () {
    if (!_throttleTimeout) {
      var args = arguments,
          _this = this;

      if (args.length === 1) {
        callback.call(_this, args[0]);
      } else {
        callback.apply(_this, args);
      }

      _throttleTimeout = setTimeout(function () {
        _throttleTimeout = void 0;
      }, ms);
    }
  };
}

function cancelThrottle() {
  clearTimeout(_throttleTimeout);
  _throttleTimeout = void 0;
}

function scrollBy(el, x, y) {
  el.scrollLeft += x;
  el.scrollTop += y;
}

function clone(el) {
  var Polymer = window.Polymer;
  var $ = window.jQuery || window.Zepto;

  if (Polymer && Polymer.dom) {
    return Polymer.dom(el).cloneNode(true);
  } else if ($) {
    return $(el).clone(true)[0];
  } else {
    return el.cloneNode(true);
  }
}

function setRect(el, rect) {
  css(el, 'position', 'absolute');
  css(el, 'top', rect.top);
  css(el, 'left', rect.left);
  css(el, 'width', rect.width);
  css(el, 'height', rect.height);
}

function unsetRect(el) {
  css(el, 'position', '');
  css(el, 'top', '');
  css(el, 'left', '');
  css(el, 'width', '');
  css(el, 'height', '');
}

var expando = 'Sortable' + new Date().getTime();

function AnimationStateManager() {
  var animationStates = [],
      animationCallbackId;
  return {
    captureAnimationState: function captureAnimationState() {
      animationStates = [];
      if (!this.options.animation) return;
      var children = [].slice.call(this.el.children);
      children.forEach(function (child) {
        if (css(child, 'display') === 'none' || child === Sortable.ghost) return;
        animationStates.push({
          target: child,
          rect: getRect(child)
        });

        var fromRect = _objectSpread({}, animationStates[animationStates.length - 1].rect); // If animating: compensate for current animation


        if (child.thisAnimationDuration) {
          var childMatrix = matrix(child, true);

          if (childMatrix) {
            fromRect.top -= childMatrix.f;
            fromRect.left -= childMatrix.e;
          }
        }

        child.fromRect = fromRect;
      });
    },
    addAnimationState: function addAnimationState(state) {
      animationStates.push(state);
    },
    removeAnimationState: function removeAnimationState(target) {
      animationStates.splice(indexOfObject(animationStates, {
        target: target
      }), 1);
    },
    animateAll: function animateAll(callback) {
      var _this = this;

      if (!this.options.animation) {
        clearTimeout(animationCallbackId);
        if (typeof callback === 'function') callback();
        return;
      }

      var animating = false,
          animationTime = 0;
      animationStates.forEach(function (state) {
        var time = 0,
            target = state.target,
            fromRect = target.fromRect,
            toRect = getRect(target),
            prevFromRect = target.prevFromRect,
            prevToRect = target.prevToRect,
            animatingRect = state.rect,
            targetMatrix = matrix(target, true);

        if (targetMatrix) {
          // Compensate for current animation
          toRect.top -= targetMatrix.f;
          toRect.left -= targetMatrix.e;
        }

        target.toRect = toRect;

        if (target.thisAnimationDuration) {
          // Could also check if animatingRect is between fromRect and toRect
          if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) && // Make sure animatingRect is on line between toRect & fromRect
          (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) {
            // If returning to same place as started from animation and on same axis
            time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
          }
        } // if fromRect != toRect: animate


        if (!isRectEqual(toRect, fromRect)) {
          target.prevFromRect = fromRect;
          target.prevToRect = toRect;

          if (!time) {
            time = _this.options.animation;
          }

          _this.animate(target, animatingRect, toRect, time);
        }

        if (time) {
          animating = true;
          animationTime = Math.max(animationTime, time);
          clearTimeout(target.animationResetTimer);
          target.animationResetTimer = setTimeout(function () {
            target.animationTime = 0;
            target.prevFromRect = null;
            target.fromRect = null;
            target.prevToRect = null;
            target.thisAnimationDuration = null;
          }, time);
          target.thisAnimationDuration = time;
        }
      });
      clearTimeout(animationCallbackId);

      if (!animating) {
        if (typeof callback === 'function') callback();
      } else {
        animationCallbackId = setTimeout(function () {
          if (typeof callback === 'function') callback();
        }, animationTime);
      }

      animationStates = [];
    },
    animate: function animate(target, currentRect, toRect, duration) {
      if (duration) {
        css(target, 'transition', '');
        css(target, 'transform', '');
        var elMatrix = matrix(this.el),
            scaleX = elMatrix && elMatrix.a,
            scaleY = elMatrix && elMatrix.d,
            translateX = (currentRect.left - toRect.left) / (scaleX || 1),
            translateY = (currentRect.top - toRect.top) / (scaleY || 1);
        target.animatingX = !!translateX;
        target.animatingY = !!translateY;
        css(target, 'transform', 'translate3d(' + translateX + 'px,' + translateY + 'px,0)');
        repaint(target); // repaint

        css(target, 'transition', 'transform ' + duration + 'ms' + (this.options.easing ? ' ' + this.options.easing : ''));
        css(target, 'transform', 'translate3d(0,0,0)');
        typeof target.animated === 'number' && clearTimeout(target.animated);
        target.animated = setTimeout(function () {
          css(target, 'transition', '');
          css(target, 'transform', '');
          target.animated = false;
          target.animatingX = false;
          target.animatingY = false;
        }, duration);
      }
    }
  };
}

function repaint(target) {
  return target.offsetWidth;
}

function calculateRealTime(animatingRect, fromRect, toRect, options) {
  return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options.animation;
}

var plugins = [];
var defaults = {
  initializeByDefault: true
};
var PluginManager = {
  mount: function mount(plugin) {
    // Set default static properties
    for (var option in defaults) {
      if (defaults.hasOwnProperty(option) && !(option in plugin)) {
        plugin[option] = defaults[option];
      }
    }

    plugins.push(plugin);
  },
  pluginEvent: function pluginEvent(eventName, sortable, evt) {
    var _this = this;

    this.eventCanceled = false;

    evt.cancel = function () {
      _this.eventCanceled = true;
    };

    var eventNameGlobal = eventName + 'Global';
    plugins.forEach(function (plugin) {
      if (!sortable[plugin.pluginName]) return; // Fire global events if it exists in this sortable

      if (sortable[plugin.pluginName][eventNameGlobal]) {
        sortable[plugin.pluginName][eventNameGlobal](_objectSpread({
          sortable: sortable
        }, evt));
      } // Only fire plugin event if plugin is enabled in this sortable,
      // and plugin has event defined


      if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
        sortable[plugin.pluginName][eventName](_objectSpread({
          sortable: sortable
        }, evt));
      }
    });
  },
  initializePlugins: function initializePlugins(sortable, el, defaults, options) {
    plugins.forEach(function (plugin) {
      var pluginName = plugin.pluginName;
      if (!sortable.options[pluginName] && !plugin.initializeByDefault) return;
      var initialized = new plugin(sortable, el, sortable.options);
      initialized.sortable = sortable;
      initialized.options = sortable.options;
      sortable[pluginName] = initialized; // Add default options from plugin

      _extends(defaults, initialized.defaults);
    });

    for (var option in sortable.options) {
      if (!sortable.options.hasOwnProperty(option)) continue;
      var modified = this.modifyOption(sortable, option, sortable.options[option]);

      if (typeof modified !== 'undefined') {
        sortable.options[option] = modified;
      }
    }
  },
  getEventProperties: function getEventProperties(name, sortable) {
    var eventProperties = {};
    plugins.forEach(function (plugin) {
      if (typeof plugin.eventProperties !== 'function') return;

      _extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
    });
    return eventProperties;
  },
  modifyOption: function modifyOption(sortable, name, value) {
    var modifiedValue;
    plugins.forEach(function (plugin) {
      // Plugin must exist on the Sortable
      if (!sortable[plugin.pluginName]) return; // If static option listener exists for this option, call in the context of the Sortable's instance of this plugin

      if (plugin.optionListeners && typeof plugin.optionListeners[name] === 'function') {
        modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
      }
    });
    return modifiedValue;
  }
};

function dispatchEvent(_ref) {
  var sortable = _ref.sortable,
      rootEl = _ref.rootEl,
      name = _ref.name,
      targetEl = _ref.targetEl,
      cloneEl = _ref.cloneEl,
      toEl = _ref.toEl,
      fromEl = _ref.fromEl,
      oldIndex = _ref.oldIndex,
      newIndex = _ref.newIndex,
      oldDraggableIndex = _ref.oldDraggableIndex,
      newDraggableIndex = _ref.newDraggableIndex,
      originalEvent = _ref.originalEvent,
      putSortable = _ref.putSortable,
      extraEventProperties = _ref.extraEventProperties;
  sortable = sortable || rootEl && rootEl[expando];
  if (!sortable) return;
  var evt,
      options = sortable.options,
      onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1); // Support for new CustomEvent feature

  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent(name, {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent('Event');
    evt.initEvent(name, true, true);
  }

  evt.to = toEl || rootEl;
  evt.from = fromEl || rootEl;
  evt.item = targetEl || rootEl;
  evt.clone = cloneEl;
  evt.oldIndex = oldIndex;
  evt.newIndex = newIndex;
  evt.oldDraggableIndex = oldDraggableIndex;
  evt.newDraggableIndex = newDraggableIndex;
  evt.originalEvent = originalEvent;
  evt.pullMode = putSortable ? putSortable.lastPutMode : undefined;

  var allEventProperties = _objectSpread({}, extraEventProperties, PluginManager.getEventProperties(name, sortable));

  for (var option in allEventProperties) {
    evt[option] = allEventProperties[option];
  }

  if (rootEl) {
    rootEl.dispatchEvent(evt);
  }

  if (options[onName]) {
    options[onName].call(sortable, evt);
  }
}

var pluginEvent = function pluginEvent(eventName, sortable) {
  var _ref = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
      originalEvent = _ref.evt,
      data = _objectWithoutProperties(_ref, ["evt"]);

  PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread({
    dragEl: dragEl,
    parentEl: parentEl,
    ghostEl: ghostEl,
    rootEl: rootEl,
    nextEl: nextEl,
    lastDownEl: lastDownEl,
    cloneEl: cloneEl,
    cloneHidden: cloneHidden,
    dragStarted: moved,
    putSortable: putSortable,
    activeSortable: Sortable.active,
    originalEvent: originalEvent,
    oldIndex: oldIndex,
    oldDraggableIndex: oldDraggableIndex,
    newIndex: newIndex,
    newDraggableIndex: newDraggableIndex,
    hideGhostForTarget: _hideGhostForTarget,
    unhideGhostForTarget: _unhideGhostForTarget,
    cloneNowHidden: function cloneNowHidden() {
      cloneHidden = true;
    },
    cloneNowShown: function cloneNowShown() {
      cloneHidden = false;
    },
    dispatchSortableEvent: function dispatchSortableEvent(name) {
      _dispatchEvent({
        sortable: sortable,
        name: name,
        originalEvent: originalEvent
      });
    }
  }, data));
};

function _dispatchEvent(info) {
  dispatchEvent(_objectSpread({
    putSortable: putSortable,
    cloneEl: cloneEl,
    targetEl: dragEl,
    rootEl: rootEl,
    oldIndex: oldIndex,
    oldDraggableIndex: oldDraggableIndex,
    newIndex: newIndex,
    newDraggableIndex: newDraggableIndex
  }, info));
}

var dragEl,
    parentEl,
    ghostEl,
    rootEl,
    nextEl,
    lastDownEl,
    cloneEl,
    cloneHidden,
    oldIndex,
    newIndex,
    oldDraggableIndex,
    newDraggableIndex,
    activeGroup,
    putSortable,
    awaitingDragStarted = false,
    ignoreNextClick = false,
    sortables = [],
    tapEvt,
    touchEvt,
    lastDx,
    lastDy,
    tapDistanceLeft,
    tapDistanceTop,
    moved,
    lastTarget,
    lastDirection,
    pastFirstInvertThresh = false,
    isCircumstantialInvert = false,
    targetMoveDistance,
    // For positioning ghost absolutely
ghostRelativeParent,
    ghostRelativeParentInitialScroll = [],
    // (left, top)
_silent = false,
    savedInputChecked = [];
/** @const */

var documentExists = typeof document !== 'undefined',
    PositionGhostAbsolutely = IOS,
    CSSFloatProperty = Edge || IE11OrLess ? 'cssFloat' : 'float',
    // This will not pass for IE9, because IE9 DnD only works on anchors
supportDraggable = documentExists && !ChromeForAndroid && !IOS && 'draggable' in document.createElement('div'),
    supportCssPointerEvents = function () {
  if (!documentExists) return; // false when <= IE11

  if (IE11OrLess) {
    return false;
  }

  var el = document.createElement('x');
  el.style.cssText = 'pointer-events:auto';
  return el.style.pointerEvents === 'auto';
}(),
    _detectDirection = function _detectDirection(el, options) {
  var elCSS = css(el),
      elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth),
      child1 = getChild(el, 0, options),
      child2 = getChild(el, 1, options),
      firstChildCSS = child1 && css(child1),
      secondChildCSS = child2 && css(child2),
      firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width,
      secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;

  if (elCSS.display === 'flex') {
    return elCSS.flexDirection === 'column' || elCSS.flexDirection === 'column-reverse' ? 'vertical' : 'horizontal';
  }

  if (elCSS.display === 'grid') {
    return elCSS.gridTemplateColumns.split(' ').length <= 1 ? 'vertical' : 'horizontal';
  }

  if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== 'none') {
    var touchingSideChild2 = firstChildCSS["float"] === 'left' ? 'left' : 'right';
    return child2 && (secondChildCSS.clear === 'both' || secondChildCSS.clear === touchingSideChild2) ? 'vertical' : 'horizontal';
  }

  return child1 && (firstChildCSS.display === 'block' || firstChildCSS.display === 'flex' || firstChildCSS.display === 'table' || firstChildCSS.display === 'grid' || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === 'none' || child2 && elCSS[CSSFloatProperty] === 'none' && firstChildWidth + secondChildWidth > elWidth) ? 'vertical' : 'horizontal';
},
    _dragElInRowColumn = function _dragElInRowColumn(dragRect, targetRect, vertical) {
  var dragElS1Opp = vertical ? dragRect.left : dragRect.top,
      dragElS2Opp = vertical ? dragRect.right : dragRect.bottom,
      dragElOppLength = vertical ? dragRect.width : dragRect.height,
      targetS1Opp = vertical ? targetRect.left : targetRect.top,
      targetS2Opp = vertical ? targetRect.right : targetRect.bottom,
      targetOppLength = vertical ? targetRect.width : targetRect.height;
  return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
},

/**
 * Detects first nearest empty sortable to X and Y position using emptyInsertThreshold.
 * @param  {Number} x      X position
 * @param  {Number} y      Y position
 * @return {HTMLElement}   Element of the first found nearest Sortable
 */
_detectNearestEmptySortable = function _detectNearestEmptySortable(x, y) {
  var ret;
  sortables.some(function (sortable) {
    if (lastChild(sortable)) return;
    var rect = getRect(sortable),
        threshold = sortable[expando].options.emptyInsertThreshold,
        insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold,
        insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;

    if (threshold && insideHorizontally && insideVertically) {
      return ret = sortable;
    }
  });
  return ret;
},
    _prepareGroup = function _prepareGroup(options) {
  function toFn(value, pull) {
    return function (to, from, dragEl, evt) {
      var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;

      if (value == null && (pull || sameGroup)) {
        // Default pull value
        // Default pull and put value if same group
        return true;
      } else if (value == null || value === false) {
        return false;
      } else if (pull && value === 'clone') {
        return value;
      } else if (typeof value === 'function') {
        return toFn(value(to, from, dragEl, evt), pull)(to, from, dragEl, evt);
      } else {
        var otherGroup = (pull ? to : from).options.group.name;
        return value === true || typeof value === 'string' && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
      }
    };
  }

  var group = {};
  var originalGroup = options.group;

  if (!originalGroup || _typeof(originalGroup) != 'object') {
    originalGroup = {
      name: originalGroup
    };
  }

  group.name = originalGroup.name;
  group.checkPull = toFn(originalGroup.pull, true);
  group.checkPut = toFn(originalGroup.put);
  group.revertClone = originalGroup.revertClone;
  options.group = group;
},
    _hideGhostForTarget = function _hideGhostForTarget() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, 'display', 'none');
  }
},
    _unhideGhostForTarget = function _unhideGhostForTarget() {
  if (!supportCssPointerEvents && ghostEl) {
    css(ghostEl, 'display', '');
  }
}; // #1184 fix - Prevent click event on fallback if dragged but item not changed position


if (documentExists) {
  document.addEventListener('click', function (evt) {
    if (ignoreNextClick) {
      evt.preventDefault();
      evt.stopPropagation && evt.stopPropagation();
      evt.stopImmediatePropagation && evt.stopImmediatePropagation();
      ignoreNextClick = false;
      return false;
    }
  }, true);
}

var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent(evt) {
  if (dragEl) {
    evt = evt.touches ? evt.touches[0] : evt;

    var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);

    if (nearest) {
      // Create imitation event
      var event = {};

      for (var i in evt) {
        if (evt.hasOwnProperty(i)) {
          event[i] = evt[i];
        }
      }

      event.target = event.rootEl = nearest;
      event.preventDefault = void 0;
      event.stopPropagation = void 0;

      nearest[expando]._onDragOver(event);
    }
  }
};

var _checkOutsideTargetEl = function _checkOutsideTargetEl(evt) {
  if (dragEl) {
    dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
  }
};
/**
 * @class  Sortable
 * @param  {HTMLElement}  el
 * @param  {Object}       [options]
 */


function Sortable(el, options) {
  if (!(el && el.nodeType && el.nodeType === 1)) {
    throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el));
  }

  this.el = el; // root element

  this.options = options = _extends({}, options); // Export instance

  el[expando] = this;
  var defaults = {
    group: null,
    sort: true,
    disabled: false,
    store: null,
    handle: null,
    draggable: /^[uo]l$/i.test(el.nodeName) ? '>li' : '>*',
    swapThreshold: 1,
    // percentage; 0 <= x <= 1
    invertSwap: false,
    // invert always
    invertedSwapThreshold: null,
    // will be set to same as swapThreshold if default
    removeCloneOnHide: true,
    direction: function direction() {
      return _detectDirection(el, this.options);
    },
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    dragClass: 'sortable-drag',
    ignore: 'a, img',
    filter: null,
    preventOnFilter: true,
    animation: 0,
    easing: null,
    setData: function setData(dataTransfer, dragEl) {
      dataTransfer.setData('Text', dragEl.textContent);
    },
    dropBubble: false,
    dragoverBubble: false,
    dataIdAttr: 'data-id',
    delay: 0,
    delayOnTouchOnly: false,
    touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
    forceFallback: false,
    fallbackClass: 'sortable-fallback',
    fallbackOnBody: false,
    fallbackTolerance: 0,
    fallbackOffset: {
      x: 0,
      y: 0
    },
    supportPointer: Sortable.supportPointer !== false && 'PointerEvent' in window,
    emptyInsertThreshold: 5
  };
  PluginManager.initializePlugins(this, el, defaults); // Set default options

  for (var name in defaults) {
    !(name in options) && (options[name] = defaults[name]);
  }

  _prepareGroup(options); // Bind all private methods


  for (var fn in this) {
    if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
      this[fn] = this[fn].bind(this);
    }
  } // Setup drag mode


  this.nativeDraggable = options.forceFallback ? false : supportDraggable;

  if (this.nativeDraggable) {
    // Touch start threshold cannot be greater than the native dragstart threshold
    this.options.touchStartThreshold = 1;
  } // Bind events


  if (options.supportPointer) {
    on(el, 'pointerdown', this._onTapStart);
  } else {
    on(el, 'mousedown', this._onTapStart);
    on(el, 'touchstart', this._onTapStart);
  }

  if (this.nativeDraggable) {
    on(el, 'dragover', this);
    on(el, 'dragenter', this);
  }

  sortables.push(this.el); // Restore sorting

  options.store && options.store.get && this.sort(options.store.get(this) || []); // Add animation state manager

  _extends(this, AnimationStateManager());
}

Sortable.prototype =
/** @lends Sortable.prototype */
{
  constructor: Sortable,
  _isOutsideThisEl: function _isOutsideThisEl(target) {
    if (!this.el.contains(target) && target !== this.el) {
      lastTarget = null;
    }
  },
  _getDirection: function _getDirection(evt, target) {
    return typeof this.options.direction === 'function' ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
  },
  _onTapStart: function _onTapStart(
  /** Event|TouchEvent */
  evt) {
    if (!evt.cancelable) return;

    var _this = this,
        el = this.el,
        options = this.options,
        preventOnFilter = options.preventOnFilter,
        type = evt.type,
        touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === 'touch' && evt,
        target = (touch || evt).target,
        originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target,
        filter = options.filter;

    _saveInputCheckedState(el); // Don't trigger start event when an element is been dragged, otherwise the evt.oldindex always wrong when set option.group.


    if (dragEl) {
      return;
    }

    if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) {
      return; // only left button and enabled
    } // cancel dnd if original target is content editable


    if (originalTarget.isContentEditable) {
      return;
    }

    target = closest(target, options.draggable, el, false);

    if (target && target.animated) {
      return;
    }

    if (lastDownEl === target) {
      // Ignoring duplicate `down`
      return;
    } // Get the index of the dragged element within its parent


    oldIndex = index(target);
    oldDraggableIndex = index(target, options.draggable); // Check filter

    if (typeof filter === 'function') {
      if (filter.call(this, evt, target, this)) {
        _dispatchEvent({
          sortable: _this,
          rootEl: originalTarget,
          name: 'filter',
          targetEl: target,
          toEl: el,
          fromEl: el
        });

        pluginEvent('filter', _this, {
          evt: evt
        });
        preventOnFilter && evt.cancelable && evt.preventDefault();
        return; // cancel dnd
      }
    } else if (filter) {
      filter = filter.split(',').some(function (criteria) {
        criteria = closest(originalTarget, criteria.trim(), el, false);

        if (criteria) {
          _dispatchEvent({
            sortable: _this,
            rootEl: criteria,
            name: 'filter',
            targetEl: target,
            fromEl: el,
            toEl: el
          });

          pluginEvent('filter', _this, {
            evt: evt
          });
          return true;
        }
      });

      if (filter) {
        preventOnFilter && evt.cancelable && evt.preventDefault();
        return; // cancel dnd
      }
    }

    if (options.handle && !closest(originalTarget, options.handle, el, false)) {
      return;
    } // Prepare `dragstart`


    this._prepareDragStart(evt, touch, target);
  },
  _prepareDragStart: function _prepareDragStart(
  /** Event */
  evt,
  /** Touch */
  touch,
  /** HTMLElement */
  target) {
    var _this = this,
        el = _this.el,
        options = _this.options,
        ownerDocument = el.ownerDocument,
        dragStartFn;

    if (target && !dragEl && target.parentNode === el) {
      var dragRect = getRect(target);
      rootEl = el;
      dragEl = target;
      parentEl = dragEl.parentNode;
      nextEl = dragEl.nextSibling;
      lastDownEl = target;
      activeGroup = options.group;
      Sortable.dragged = dragEl;
      tapEvt = {
        target: dragEl,
        clientX: (touch || evt).clientX,
        clientY: (touch || evt).clientY
      };
      tapDistanceLeft = tapEvt.clientX - dragRect.left;
      tapDistanceTop = tapEvt.clientY - dragRect.top;
      this._lastX = (touch || evt).clientX;
      this._lastY = (touch || evt).clientY;
      dragEl.style['will-change'] = 'all';

      dragStartFn = function dragStartFn() {
        pluginEvent('delayEnded', _this, {
          evt: evt
        });

        if (Sortable.eventCanceled) {
          _this._onDrop();

          return;
        } // Delayed drag has been triggered
        // we can re-enable the events: touchmove/mousemove


        _this._disableDelayedDragEvents();

        if (!FireFox && _this.nativeDraggable) {
          dragEl.draggable = true;
        } // Bind the events: dragstart/dragend


        _this._triggerDragStart(evt, touch); // Drag start event


        _dispatchEvent({
          sortable: _this,
          name: 'choose',
          originalEvent: evt
        }); // Chosen item


        toggleClass(dragEl, options.chosenClass, true);
      }; // Disable "draggable"


      options.ignore.split(',').forEach(function (criteria) {
        find(dragEl, criteria.trim(), _disableDraggable);
      });
      on(ownerDocument, 'dragover', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'mousemove', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'touchmove', nearestEmptyInsertDetectEvent);
      on(ownerDocument, 'mouseup', _this._onDrop);
      on(ownerDocument, 'touchend', _this._onDrop);
      on(ownerDocument, 'touchcancel', _this._onDrop); // Make dragEl draggable (must be before delay for FireFox)

      if (FireFox && this.nativeDraggable) {
        this.options.touchStartThreshold = 4;
        dragEl.draggable = true;
      }

      pluginEvent('delayStart', this, {
        evt: evt
      }); // Delay is impossible for native DnD in Edge or IE

      if (options.delay && (!options.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
        if (Sortable.eventCanceled) {
          this._onDrop();

          return;
        } // If the user moves the pointer or let go the click or touch
        // before the delay has been reached:
        // disable the delayed drag


        on(ownerDocument, 'mouseup', _this._disableDelayedDrag);
        on(ownerDocument, 'touchend', _this._disableDelayedDrag);
        on(ownerDocument, 'touchcancel', _this._disableDelayedDrag);
        on(ownerDocument, 'mousemove', _this._delayedDragTouchMoveHandler);
        on(ownerDocument, 'touchmove', _this._delayedDragTouchMoveHandler);
        options.supportPointer && on(ownerDocument, 'pointermove', _this._delayedDragTouchMoveHandler);
        _this._dragStartTimer = setTimeout(dragStartFn, options.delay);
      } else {
        dragStartFn();
      }
    }
  },
  _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(
  /** TouchEvent|PointerEvent **/
  e) {
    var touch = e.touches ? e.touches[0] : e;

    if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
      this._disableDelayedDrag();
    }
  },
  _disableDelayedDrag: function _disableDelayedDrag() {
    dragEl && _disableDraggable(dragEl);
    clearTimeout(this._dragStartTimer);

    this._disableDelayedDragEvents();
  },
  _disableDelayedDragEvents: function _disableDelayedDragEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, 'mouseup', this._disableDelayedDrag);
    off(ownerDocument, 'touchend', this._disableDelayedDrag);
    off(ownerDocument, 'touchcancel', this._disableDelayedDrag);
    off(ownerDocument, 'mousemove', this._delayedDragTouchMoveHandler);
    off(ownerDocument, 'touchmove', this._delayedDragTouchMoveHandler);
    off(ownerDocument, 'pointermove', this._delayedDragTouchMoveHandler);
  },
  _triggerDragStart: function _triggerDragStart(
  /** Event */
  evt,
  /** Touch */
  touch) {
    touch = touch || evt.pointerType == 'touch' && evt;

    if (!this.nativeDraggable || touch) {
      if (this.options.supportPointer) {
        on(document, 'pointermove', this._onTouchMove);
      } else if (touch) {
        on(document, 'touchmove', this._onTouchMove);
      } else {
        on(document, 'mousemove', this._onTouchMove);
      }
    } else {
      on(dragEl, 'dragend', this);
      on(rootEl, 'dragstart', this._onDragStart);
    }

    try {
      if (document.selection) {
        // Timeout neccessary for IE9
        _nextTick(function () {
          document.selection.empty();
        });
      } else {
        window.getSelection().removeAllRanges();
      }
    } catch (err) {}
  },
  _dragStarted: function _dragStarted(fallback, evt) {

    awaitingDragStarted = false;

    if (rootEl && dragEl) {
      pluginEvent('dragStarted', this, {
        evt: evt
      });

      if (this.nativeDraggable) {
        on(document, 'dragover', _checkOutsideTargetEl);
      }

      var options = this.options; // Apply effect

      !fallback && toggleClass(dragEl, options.dragClass, false);
      toggleClass(dragEl, options.ghostClass, true);
      Sortable.active = this;
      fallback && this._appendGhost(); // Drag start event

      _dispatchEvent({
        sortable: this,
        name: 'start',
        originalEvent: evt
      });
    } else {
      this._nulling();
    }
  },
  _emulateDragOver: function _emulateDragOver() {
    if (touchEvt) {
      this._lastX = touchEvt.clientX;
      this._lastY = touchEvt.clientY;

      _hideGhostForTarget();

      var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
      var parent = target;

      while (target && target.shadowRoot) {
        target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
        if (target === parent) break;
        parent = target;
      }

      dragEl.parentNode[expando]._isOutsideThisEl(target);

      if (parent) {
        do {
          if (parent[expando]) {
            var inserted = void 0;
            inserted = parent[expando]._onDragOver({
              clientX: touchEvt.clientX,
              clientY: touchEvt.clientY,
              target: target,
              rootEl: parent
            });

            if (inserted && !this.options.dragoverBubble) {
              break;
            }
          }

          target = parent; // store last element
        }
        /* jshint boss:true */
        while (parent = parent.parentNode);
      }

      _unhideGhostForTarget();
    }
  },
  _onTouchMove: function _onTouchMove(
  /**TouchEvent*/
  evt) {
    if (tapEvt) {
      var options = this.options,
          fallbackTolerance = options.fallbackTolerance,
          fallbackOffset = options.fallbackOffset,
          touch = evt.touches ? evt.touches[0] : evt,
          ghostMatrix = ghostEl && matrix(ghostEl, true),
          scaleX = ghostEl && ghostMatrix && ghostMatrix.a,
          scaleY = ghostEl && ghostMatrix && ghostMatrix.d,
          relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent),
          dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1),
          dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1); // only set the status to dragging, when we are actually dragging

      if (!Sortable.active && !awaitingDragStarted) {
        if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) {
          return;
        }

        this._onDragStart(evt, true);
      }

      if (ghostEl) {
        if (ghostMatrix) {
          ghostMatrix.e += dx - (lastDx || 0);
          ghostMatrix.f += dy - (lastDy || 0);
        } else {
          ghostMatrix = {
            a: 1,
            b: 0,
            c: 0,
            d: 1,
            e: dx,
            f: dy
          };
        }

        var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
        css(ghostEl, 'webkitTransform', cssMatrix);
        css(ghostEl, 'mozTransform', cssMatrix);
        css(ghostEl, 'msTransform', cssMatrix);
        css(ghostEl, 'transform', cssMatrix);
        lastDx = dx;
        lastDy = dy;
        touchEvt = touch;
      }

      evt.cancelable && evt.preventDefault();
    }
  },
  _appendGhost: function _appendGhost() {
    // Bug if using scale(): https://stackoverflow.com/questions/2637058
    // Not being adjusted for
    if (!ghostEl) {
      var container = this.options.fallbackOnBody ? document.body : rootEl,
          rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container),
          options = this.options; // Position absolutely

      if (PositionGhostAbsolutely) {
        // Get relatively positioned parent
        ghostRelativeParent = container;

        while (css(ghostRelativeParent, 'position') === 'static' && css(ghostRelativeParent, 'transform') === 'none' && ghostRelativeParent !== document) {
          ghostRelativeParent = ghostRelativeParent.parentNode;
        }

        if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
          if (ghostRelativeParent === document) ghostRelativeParent = getWindowScrollingElement();
          rect.top += ghostRelativeParent.scrollTop;
          rect.left += ghostRelativeParent.scrollLeft;
        } else {
          ghostRelativeParent = getWindowScrollingElement();
        }

        ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
      }

      ghostEl = dragEl.cloneNode(true);
      toggleClass(ghostEl, options.ghostClass, false);
      toggleClass(ghostEl, options.fallbackClass, true);
      toggleClass(ghostEl, options.dragClass, true);
      css(ghostEl, 'transition', '');
      css(ghostEl, 'transform', '');
      css(ghostEl, 'box-sizing', 'border-box');
      css(ghostEl, 'margin', 0);
      css(ghostEl, 'top', rect.top);
      css(ghostEl, 'left', rect.left);
      css(ghostEl, 'width', rect.width);
      css(ghostEl, 'height', rect.height);
      css(ghostEl, 'opacity', '0.8');
      css(ghostEl, 'position', PositionGhostAbsolutely ? 'absolute' : 'fixed');
      css(ghostEl, 'zIndex', '100000');
      css(ghostEl, 'pointerEvents', 'none');
      Sortable.ghost = ghostEl;
      container.appendChild(ghostEl); // Set transform-origin

      css(ghostEl, 'transform-origin', tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + '% ' + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + '%');
    }
  },
  _onDragStart: function _onDragStart(
  /**Event*/
  evt,
  /**boolean*/
  fallback) {
    var _this = this;

    var dataTransfer = evt.dataTransfer;
    var options = _this.options;
    pluginEvent('dragStart', this, {
      evt: evt
    });

    if (Sortable.eventCanceled) {
      this._onDrop();

      return;
    }

    pluginEvent('setupClone', this);

    if (!Sortable.eventCanceled) {
      cloneEl = clone(dragEl);
      cloneEl.draggable = false;
      cloneEl.style['will-change'] = '';

      this._hideClone();

      toggleClass(cloneEl, this.options.chosenClass, false);
      Sortable.clone = cloneEl;
    } // #1143: IFrame support workaround


    _this.cloneId = _nextTick(function () {
      pluginEvent('clone', _this);
      if (Sortable.eventCanceled) return;

      if (!_this.options.removeCloneOnHide) {
        rootEl.insertBefore(cloneEl, dragEl);
      }

      _this._hideClone();

      _dispatchEvent({
        sortable: _this,
        name: 'clone'
      });
    });
    !fallback && toggleClass(dragEl, options.dragClass, true); // Set proper drop events

    if (fallback) {
      ignoreNextClick = true;
      _this._loopId = setInterval(_this._emulateDragOver, 50);
    } else {
      // Undo what was set in _prepareDragStart before drag started
      off(document, 'mouseup', _this._onDrop);
      off(document, 'touchend', _this._onDrop);
      off(document, 'touchcancel', _this._onDrop);

      if (dataTransfer) {
        dataTransfer.effectAllowed = 'move';
        options.setData && options.setData.call(_this, dataTransfer, dragEl);
      }

      on(document, 'drop', _this); // #1276 fix:

      css(dragEl, 'transform', 'translateZ(0)');
    }

    awaitingDragStarted = true;
    _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
    on(document, 'selectstart', _this);
    moved = true;

    if (Safari) {
      css(document.body, 'user-select', 'none');
    }
  },
  // Returns true - if no further action is needed (either inserted or another condition)
  _onDragOver: function _onDragOver(
  /**Event*/
  evt) {
    var el = this.el,
        target = evt.target,
        dragRect,
        targetRect,
        revert,
        options = this.options,
        group = options.group,
        activeSortable = Sortable.active,
        isOwner = activeGroup === group,
        canSort = options.sort,
        fromSortable = putSortable || activeSortable,
        vertical,
        _this = this,
        completedFired = false;

    if (_silent) return;

    function dragOverEvent(name, extra) {
      pluginEvent(name, _this, _objectSpread({
        evt: evt,
        isOwner: isOwner,
        axis: vertical ? 'vertical' : 'horizontal',
        revert: revert,
        dragRect: dragRect,
        targetRect: targetRect,
        canSort: canSort,
        fromSortable: fromSortable,
        target: target,
        completed: completed,
        onMove: function onMove(target, after) {
          return _onMove(rootEl, el, dragEl, dragRect, target, getRect(target), evt, after);
        },
        changed: changed
      }, extra));
    } // Capture animation state


    function capture() {
      dragOverEvent('dragOverAnimationCapture');

      _this.captureAnimationState();

      if (_this !== fromSortable) {
        fromSortable.captureAnimationState();
      }
    } // Return invocation when dragEl is inserted (or completed)


    function completed(insertion) {
      dragOverEvent('dragOverCompleted', {
        insertion: insertion
      });

      if (insertion) {
        // Clones must be hidden before folding animation to capture dragRectAbsolute properly
        if (isOwner) {
          activeSortable._hideClone();
        } else {
          activeSortable._showClone(_this);
        }

        if (_this !== fromSortable) {
          // Set ghost class to new sortable's ghost class
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
          toggleClass(dragEl, options.ghostClass, true);
        }

        if (putSortable !== _this && _this !== Sortable.active) {
          putSortable = _this;
        } else if (_this === Sortable.active && putSortable) {
          putSortable = null;
        } // Animation


        if (fromSortable === _this) {
          _this._ignoreWhileAnimating = target;
        }

        _this.animateAll(function () {
          dragOverEvent('dragOverAnimationComplete');
          _this._ignoreWhileAnimating = null;
        });

        if (_this !== fromSortable) {
          fromSortable.animateAll();
          fromSortable._ignoreWhileAnimating = null;
        }
      } // Null lastTarget if it is not inside a previously swapped element


      if (target === dragEl && !dragEl.animated || target === el && !target.animated) {
        lastTarget = null;
      } // no bubbling and not fallback


      if (!options.dragoverBubble && !evt.rootEl && target !== document) {
        dragEl.parentNode[expando]._isOutsideThisEl(evt.target); // Do not detect for empty insert if already inserted


        !insertion && nearestEmptyInsertDetectEvent(evt);
      }

      !options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
      return completedFired = true;
    } // Call when dragEl has been inserted


    function changed() {
      newIndex = index(dragEl);
      newDraggableIndex = index(dragEl, options.draggable);

      _dispatchEvent({
        sortable: _this,
        name: 'change',
        toEl: el,
        newIndex: newIndex,
        newDraggableIndex: newDraggableIndex,
        originalEvent: evt
      });
    }

    if (evt.preventDefault !== void 0) {
      evt.cancelable && evt.preventDefault();
    }

    target = closest(target, options.draggable, el, true);
    dragOverEvent('dragOver');
    if (Sortable.eventCanceled) return completedFired;

    if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) {
      return completed(false);
    }

    ignoreNextClick = false;

    if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = !rootEl.contains(dragEl)) // Reverting item into the original list
    : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
      vertical = this._getDirection(evt, target) === 'vertical';
      dragRect = getRect(dragEl);
      dragOverEvent('dragOverValid');
      if (Sortable.eventCanceled) return completedFired;

      if (revert) {
        parentEl = rootEl; // actualization

        capture();

        this._hideClone();

        dragOverEvent('revert');

        if (!Sortable.eventCanceled) {
          if (nextEl) {
            rootEl.insertBefore(dragEl, nextEl);
          } else {
            rootEl.appendChild(dragEl);
          }
        }

        return completed(true);
      }

      var elLastChild = lastChild(el, options.draggable);

      if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
        // If already at end of list: Do not insert
        if (elLastChild === dragEl) {
          return completed(false);
        } // assign target only if condition is true


        if (elLastChild && el === evt.target) {
          target = elLastChild;
        }

        if (target) {
          targetRect = getRect(target);
        }

        if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
          capture();
          el.appendChild(dragEl);
          parentEl = el; // actualization

          changed();
          return completed(true);
        }
      } else if (target.parentNode === el) {
        targetRect = getRect(target);
        var direction = 0,
            targetBeforeFirstSwap,
            differentLevel = dragEl.parentNode !== el,
            differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical),
            side1 = vertical ? 'top' : 'left',
            scrolledPastTop = isScrolledPast(target, 'top', 'top') || isScrolledPast(dragEl, 'top', 'top'),
            scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;

        if (lastTarget !== target) {
          targetBeforeFirstSwap = targetRect[side1];
          pastFirstInvertThresh = false;
          isCircumstantialInvert = !differentRowCol && options.invertSwap || differentLevel;
        }

        direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
        var sibling;

        if (direction !== 0) {
          // Check if target is beside dragEl in respective direction (ignoring hidden elements)
          var dragIndex = index(dragEl);

          do {
            dragIndex -= direction;
            sibling = parentEl.children[dragIndex];
          } while (sibling && (css(sibling, 'display') === 'none' || sibling === ghostEl));
        } // If dragEl is already beside target: Do not insert


        if (direction === 0 || sibling === target) {
          return completed(false);
        }

        lastTarget = target;
        lastDirection = direction;
        var nextSibling = target.nextElementSibling,
            after = false;
        after = direction === 1;

        var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);

        if (moveVector !== false) {
          if (moveVector === 1 || moveVector === -1) {
            after = moveVector === 1;
          }

          _silent = true;
          setTimeout(_unsilent, 30);
          capture();

          if (after && !nextSibling) {
            el.appendChild(dragEl);
          } else {
            target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
          } // Undo chrome's scroll adjustment (has no effect on other browsers)


          if (scrolledPastTop) {
            scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
          }

          parentEl = dragEl.parentNode; // actualization
          // must be done before animation

          if (targetBeforeFirstSwap !== undefined && !isCircumstantialInvert) {
            targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
          }

          changed();
          return completed(true);
        }
      }

      if (el.contains(dragEl)) {
        return completed(false);
      }
    }

    return false;
  },
  _ignoreWhileAnimating: null,
  _offMoveEvents: function _offMoveEvents() {
    off(document, 'mousemove', this._onTouchMove);
    off(document, 'touchmove', this._onTouchMove);
    off(document, 'pointermove', this._onTouchMove);
    off(document, 'dragover', nearestEmptyInsertDetectEvent);
    off(document, 'mousemove', nearestEmptyInsertDetectEvent);
    off(document, 'touchmove', nearestEmptyInsertDetectEvent);
  },
  _offUpEvents: function _offUpEvents() {
    var ownerDocument = this.el.ownerDocument;
    off(ownerDocument, 'mouseup', this._onDrop);
    off(ownerDocument, 'touchend', this._onDrop);
    off(ownerDocument, 'pointerup', this._onDrop);
    off(ownerDocument, 'touchcancel', this._onDrop);
    off(document, 'selectstart', this);
  },
  _onDrop: function _onDrop(
  /**Event*/
  evt) {
    var el = this.el,
        options = this.options; // Get the index of the dragged element within its parent

    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options.draggable);
    pluginEvent('drop', this, {
      evt: evt
    });
    parentEl = dragEl && dragEl.parentNode; // Get again after plugin event

    newIndex = index(dragEl);
    newDraggableIndex = index(dragEl, options.draggable);

    if (Sortable.eventCanceled) {
      this._nulling();

      return;
    }

    awaitingDragStarted = false;
    isCircumstantialInvert = false;
    pastFirstInvertThresh = false;
    clearInterval(this._loopId);
    clearTimeout(this._dragStartTimer);

    _cancelNextTick(this.cloneId);

    _cancelNextTick(this._dragStartId); // Unbind events


    if (this.nativeDraggable) {
      off(document, 'drop', this);
      off(el, 'dragstart', this._onDragStart);
    }

    this._offMoveEvents();

    this._offUpEvents();

    if (Safari) {
      css(document.body, 'user-select', '');
    }

    css(dragEl, 'transform', '');

    if (evt) {
      if (moved) {
        evt.cancelable && evt.preventDefault();
        !options.dropBubble && evt.stopPropagation();
      }

      ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);

      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
        // Remove clone(s)
        cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
      }

      if (dragEl) {
        if (this.nativeDraggable) {
          off(dragEl, 'dragend', this);
        }

        _disableDraggable(dragEl);

        dragEl.style['will-change'] = ''; // Remove classes
        // ghostClass is added in dragStarted

        if (moved && !awaitingDragStarted) {
          toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
        }

        toggleClass(dragEl, this.options.chosenClass, false); // Drag stop event

        _dispatchEvent({
          sortable: this,
          name: 'unchoose',
          toEl: parentEl,
          newIndex: null,
          newDraggableIndex: null,
          originalEvent: evt
        });

        if (rootEl !== parentEl) {
          if (newIndex >= 0) {
            // Add event
            _dispatchEvent({
              rootEl: parentEl,
              name: 'add',
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            }); // Remove event


            _dispatchEvent({
              sortable: this,
              name: 'remove',
              toEl: parentEl,
              originalEvent: evt
            }); // drag from one list and drop into another


            _dispatchEvent({
              rootEl: parentEl,
              name: 'sort',
              toEl: parentEl,
              fromEl: rootEl,
              originalEvent: evt
            });

            _dispatchEvent({
              sortable: this,
              name: 'sort',
              toEl: parentEl,
              originalEvent: evt
            });
          }

          putSortable && putSortable.save();
        } else {
          if (newIndex !== oldIndex) {
            if (newIndex >= 0) {
              // drag & drop within the same list
              _dispatchEvent({
                sortable: this,
                name: 'update',
                toEl: parentEl,
                originalEvent: evt
              });

              _dispatchEvent({
                sortable: this,
                name: 'sort',
                toEl: parentEl,
                originalEvent: evt
              });
            }
          }
        }

        if (Sortable.active) {
          /* jshint eqnull:true */
          if (newIndex == null || newIndex === -1) {
            newIndex = oldIndex;
            newDraggableIndex = oldDraggableIndex;
          }

          _dispatchEvent({
            sortable: this,
            name: 'end',
            toEl: parentEl,
            originalEvent: evt
          }); // Save sorting


          this.save();
        }
      }
    }

    this._nulling();
  },
  _nulling: function _nulling() {
    pluginEvent('nulling', this);
    rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable.dragged = Sortable.ghost = Sortable.clone = Sortable.active = null;
    savedInputChecked.forEach(function (el) {
      el.checked = true;
    });
    savedInputChecked.length = lastDx = lastDy = 0;
  },
  handleEvent: function handleEvent(
  /**Event*/
  evt) {
    switch (evt.type) {
      case 'drop':
      case 'dragend':
        this._onDrop(evt);

        break;

      case 'dragenter':
      case 'dragover':
        if (dragEl) {
          this._onDragOver(evt);

          _globalDragOver(evt);
        }

        break;

      case 'selectstart':
        evt.preventDefault();
        break;
    }
  },

  /**
   * Serializes the item into an array of string.
   * @returns {String[]}
   */
  toArray: function toArray() {
    var order = [],
        el,
        children = this.el.children,
        i = 0,
        n = children.length,
        options = this.options;

    for (; i < n; i++) {
      el = children[i];

      if (closest(el, options.draggable, this.el, false)) {
        order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
      }
    }

    return order;
  },

  /**
   * Sorts the elements according to the array.
   * @param  {String[]}  order  order of the items
   */
  sort: function sort(order) {
    var items = {},
        rootEl = this.el;
    this.toArray().forEach(function (id, i) {
      var el = rootEl.children[i];

      if (closest(el, this.options.draggable, rootEl, false)) {
        items[id] = el;
      }
    }, this);
    order.forEach(function (id) {
      if (items[id]) {
        rootEl.removeChild(items[id]);
        rootEl.appendChild(items[id]);
      }
    });
  },

  /**
   * Save the current sorting
   */
  save: function save() {
    var store = this.options.store;
    store && store.set && store.set(this);
  },

  /**
   * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
   * @param   {HTMLElement}  el
   * @param   {String}       [selector]  default: `options.draggable`
   * @returns {HTMLElement|null}
   */
  closest: function closest$1(el, selector) {
    return closest(el, selector || this.options.draggable, this.el, false);
  },

  /**
   * Set/get option
   * @param   {string} name
   * @param   {*}      [value]
   * @returns {*}
   */
  option: function option(name, value) {
    var options = this.options;

    if (value === void 0) {
      return options[name];
    } else {
      var modifiedValue = PluginManager.modifyOption(this, name, value);

      if (typeof modifiedValue !== 'undefined') {
        options[name] = modifiedValue;
      } else {
        options[name] = value;
      }

      if (name === 'group') {
        _prepareGroup(options);
      }
    }
  },

  /**
   * Destroy
   */
  destroy: function destroy() {
    pluginEvent('destroy', this);
    var el = this.el;
    el[expando] = null;
    off(el, 'mousedown', this._onTapStart);
    off(el, 'touchstart', this._onTapStart);
    off(el, 'pointerdown', this._onTapStart);

    if (this.nativeDraggable) {
      off(el, 'dragover', this);
      off(el, 'dragenter', this);
    } // Remove draggable attributes


    Array.prototype.forEach.call(el.querySelectorAll('[draggable]'), function (el) {
      el.removeAttribute('draggable');
    });

    this._onDrop();

    this._disableDelayedDragEvents();

    sortables.splice(sortables.indexOf(this.el), 1);
    this.el = el = null;
  },
  _hideClone: function _hideClone() {
    if (!cloneHidden) {
      pluginEvent('hideClone', this);
      if (Sortable.eventCanceled) return;
      css(cloneEl, 'display', 'none');

      if (this.options.removeCloneOnHide && cloneEl.parentNode) {
        cloneEl.parentNode.removeChild(cloneEl);
      }

      cloneHidden = true;
    }
  },
  _showClone: function _showClone(putSortable) {
    if (putSortable.lastPutMode !== 'clone') {
      this._hideClone();

      return;
    }

    if (cloneHidden) {
      pluginEvent('showClone', this);
      if (Sortable.eventCanceled) return; // show clone at dragEl or original position

      if (rootEl.contains(dragEl) && !this.options.group.revertClone) {
        rootEl.insertBefore(cloneEl, dragEl);
      } else if (nextEl) {
        rootEl.insertBefore(cloneEl, nextEl);
      } else {
        rootEl.appendChild(cloneEl);
      }

      if (this.options.group.revertClone) {
        this.animate(dragEl, cloneEl);
      }

      css(cloneEl, 'display', '');
      cloneHidden = false;
    }
  }
};

function _globalDragOver(
/**Event*/
evt) {
  if (evt.dataTransfer) {
    evt.dataTransfer.dropEffect = 'move';
  }

  evt.cancelable && evt.preventDefault();
}

function _onMove(fromEl, toEl, dragEl, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
  var evt,
      sortable = fromEl[expando],
      onMoveFn = sortable.options.onMove,
      retVal; // Support for new CustomEvent feature

  if (window.CustomEvent && !IE11OrLess && !Edge) {
    evt = new CustomEvent('move', {
      bubbles: true,
      cancelable: true
    });
  } else {
    evt = document.createEvent('Event');
    evt.initEvent('move', true, true);
  }

  evt.to = toEl;
  evt.from = fromEl;
  evt.dragged = dragEl;
  evt.draggedRect = dragRect;
  evt.related = targetEl || toEl;
  evt.relatedRect = targetRect || getRect(toEl);
  evt.willInsertAfter = willInsertAfter;
  evt.originalEvent = originalEvent;
  fromEl.dispatchEvent(evt);

  if (onMoveFn) {
    retVal = onMoveFn.call(sortable, evt, originalEvent);
  }

  return retVal;
}

function _disableDraggable(el) {
  el.draggable = false;
}

function _unsilent() {
  _silent = false;
}

function _ghostIsLast(evt, vertical, sortable) {
  var rect = getRect(lastChild(sortable.el, sortable.options.draggable));
  var spacer = 10;
  return vertical ? evt.clientX > rect.right + spacer || evt.clientX <= rect.right && evt.clientY > rect.bottom && evt.clientX >= rect.left : evt.clientX > rect.right && evt.clientY > rect.top || evt.clientX <= rect.right && evt.clientY > rect.bottom + spacer;
}

function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
  var mouseOnAxis = vertical ? evt.clientY : evt.clientX,
      targetLength = vertical ? targetRect.height : targetRect.width,
      targetS1 = vertical ? targetRect.top : targetRect.left,
      targetS2 = vertical ? targetRect.bottom : targetRect.right,
      invert = false;

  if (!invertSwap) {
    // Never invert or create dragEl shadow when target movemenet causes mouse to move past the end of regular swapThreshold
    if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
      // multiplied only by swapThreshold because mouse will already be inside target by (1 - threshold) * targetLength / 2
      // check if past first invert threshold on side opposite of lastDirection
      if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
        // past first invert threshold, do not restrict inverted threshold to dragEl shadow
        pastFirstInvertThresh = true;
      }

      if (!pastFirstInvertThresh) {
        // dragEl shadow (target move distance shadow)
        if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance // over dragEl shadow
        : mouseOnAxis > targetS2 - targetMoveDistance) {
          return -lastDirection;
        }
      } else {
        invert = true;
      }
    } else {
      // Regular
      if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
        return _getInsertDirection(target);
      }
    }
  }

  invert = invert || invertSwap;

  if (invert) {
    // Invert of regular
    if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
      return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
    }
  }

  return 0;
}
/**
 * Gets the direction dragEl must be swapped relative to target in order to make it
 * seem that dragEl has been "inserted" into that element's position
 * @param  {HTMLElement} target       The target whose position dragEl is being inserted at
 * @return {Number}                   Direction dragEl must be swapped
 */


function _getInsertDirection(target) {
  if (index(dragEl) < index(target)) {
    return 1;
  } else {
    return -1;
  }
}
/**
 * Generate id
 * @param   {HTMLElement} el
 * @returns {String}
 * @private
 */


function _generateId(el) {
  var str = el.tagName + el.className + el.src + el.href + el.textContent,
      i = str.length,
      sum = 0;

  while (i--) {
    sum += str.charCodeAt(i);
  }

  return sum.toString(36);
}

function _saveInputCheckedState(root) {
  savedInputChecked.length = 0;
  var inputs = root.getElementsByTagName('input');
  var idx = inputs.length;

  while (idx--) {
    var el = inputs[idx];
    el.checked && savedInputChecked.push(el);
  }
}

function _nextTick(fn) {
  return setTimeout(fn, 0);
}

function _cancelNextTick(id) {
  return clearTimeout(id);
} // Fixed #973:


if (documentExists) {
  on(document, 'touchmove', function (evt) {
    if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
      evt.preventDefault();
    }
  });
} // Export utils


Sortable.utils = {
  on: on,
  off: off,
  css: css,
  find: find,
  is: function is(el, selector) {
    return !!closest(el, selector, el, false);
  },
  extend: extend,
  throttle: throttle,
  closest: closest,
  toggleClass: toggleClass,
  clone: clone,
  index: index,
  nextTick: _nextTick,
  cancelNextTick: _cancelNextTick,
  detectDirection: _detectDirection,
  getChild: getChild
};
/**
 * Get the Sortable instance of an element
 * @param  {HTMLElement} element The element
 * @return {Sortable|undefined}         The instance of Sortable
 */

Sortable.get = function (element) {
  return element[expando];
};
/**
 * Mount a plugin to Sortable
 * @param  {...SortablePlugin|SortablePlugin[]} plugins       Plugins being mounted
 */


Sortable.mount = function () {
  for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) {
    plugins[_key] = arguments[_key];
  }

  if (plugins[0].constructor === Array) plugins = plugins[0];
  plugins.forEach(function (plugin) {
    if (!plugin.prototype || !plugin.prototype.constructor) {
      throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
    }

    if (plugin.utils) Sortable.utils = _objectSpread({}, Sortable.utils, plugin.utils);
    PluginManager.mount(plugin);
  });
};
/**
 * Create sortable instance
 * @param {HTMLElement}  el
 * @param {Object}      [options]
 */


Sortable.create = function (el, options) {
  return new Sortable(el, options);
}; // Export


Sortable.version = version;

var autoScrolls = [],
    scrollEl,
    scrollRootEl,
    scrolling = false,
    lastAutoScrollX,
    lastAutoScrollY,
    touchEvt$1,
    pointerElemChangedInterval;

function AutoScrollPlugin() {
  function AutoScroll() {
    this.defaults = {
      scroll: true,
      scrollSensitivity: 30,
      scrollSpeed: 10,
      bubbleScroll: true
    }; // Bind all private methods

    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    }
  }

  AutoScroll.prototype = {
    dragStarted: function dragStarted(_ref) {
      var originalEvent = _ref.originalEvent;

      if (this.sortable.nativeDraggable) {
        on(document, 'dragover', this._handleAutoScroll);
      } else {
        if (this.options.supportPointer) {
          on(document, 'pointermove', this._handleFallbackAutoScroll);
        } else if (originalEvent.touches) {
          on(document, 'touchmove', this._handleFallbackAutoScroll);
        } else {
          on(document, 'mousemove', this._handleFallbackAutoScroll);
        }
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref2) {
      var originalEvent = _ref2.originalEvent;

      // For when bubbling is canceled and using fallback (fallback 'touchmove' always reached)
      if (!this.options.dragOverBubble && !originalEvent.rootEl) {
        this._handleAutoScroll(originalEvent);
      }
    },
    drop: function drop() {
      if (this.sortable.nativeDraggable) {
        off(document, 'dragover', this._handleAutoScroll);
      } else {
        off(document, 'pointermove', this._handleFallbackAutoScroll);
        off(document, 'touchmove', this._handleFallbackAutoScroll);
        off(document, 'mousemove', this._handleFallbackAutoScroll);
      }

      clearPointerElemChangedInterval();
      clearAutoScrolls();
      cancelThrottle();
    },
    nulling: function nulling() {
      touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
      autoScrolls.length = 0;
    },
    _handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
      this._handleAutoScroll(evt, true);
    },
    _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
      var _this = this;

      var x = (evt.touches ? evt.touches[0] : evt).clientX,
          y = (evt.touches ? evt.touches[0] : evt).clientY,
          elem = document.elementFromPoint(x, y);
      touchEvt$1 = evt; // IE does not seem to have native autoscroll,
      // Edge's autoscroll seems too conditional,
      // MACOS Safari does not have autoscroll,
      // Firefox and Chrome are good

      if (fallback || Edge || IE11OrLess || Safari) {
        autoScroll(evt, this.options, elem, fallback); // Listener for pointer element change

        var ogElemScroller = getParentAutoScrollElement(elem, true);

        if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
          pointerElemChangedInterval && clearPointerElemChangedInterval(); // Detect for pointer elem change, emulating native DnD behaviour

          pointerElemChangedInterval = setInterval(function () {
            var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);

            if (newElem !== ogElemScroller) {
              ogElemScroller = newElem;
              clearAutoScrolls();
            }

            autoScroll(evt, _this.options, newElem, fallback);
          }, 10);
          lastAutoScrollX = x;
          lastAutoScrollY = y;
        }
      } else {
        // if DnD is enabled (and browser has good autoscrolling), first autoscroll will already scroll, so get parent autoscroll of first autoscroll
        if (!this.options.bubbleScroll || getParentAutoScrollElement(elem, true) === getWindowScrollingElement()) {
          clearAutoScrolls();
          return;
        }

        autoScroll(evt, this.options, getParentAutoScrollElement(elem, false), false);
      }
    }
  };
  return _extends(AutoScroll, {
    pluginName: 'scroll',
    initializeByDefault: true
  });
}

function clearAutoScrolls() {
  autoScrolls.forEach(function (autoScroll) {
    clearInterval(autoScroll.pid);
  });
  autoScrolls = [];
}

function clearPointerElemChangedInterval() {
  clearInterval(pointerElemChangedInterval);
}

var autoScroll = throttle(function (evt, options, rootEl, isFallback) {
  // Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=505521
  if (!options.scroll) return;
  var x = (evt.touches ? evt.touches[0] : evt).clientX,
      y = (evt.touches ? evt.touches[0] : evt).clientY,
      sens = options.scrollSensitivity,
      speed = options.scrollSpeed,
      winScroller = getWindowScrollingElement();
  var scrollThisInstance = false,
      scrollCustomFn; // New scroll root, set scrollEl

  if (scrollRootEl !== rootEl) {
    scrollRootEl = rootEl;
    clearAutoScrolls();
    scrollEl = options.scroll;
    scrollCustomFn = options.scrollFn;

    if (scrollEl === true) {
      scrollEl = getParentAutoScrollElement(rootEl, true);
    }
  }

  var layersOut = 0;
  var currentParent = scrollEl;

  do {
    var el = currentParent,
        rect = getRect(el),
        top = rect.top,
        bottom = rect.bottom,
        left = rect.left,
        right = rect.right,
        width = rect.width,
        height = rect.height,
        canScrollX = void 0,
        canScrollY = void 0,
        scrollWidth = el.scrollWidth,
        scrollHeight = el.scrollHeight,
        elCSS = css(el),
        scrollPosX = el.scrollLeft,
        scrollPosY = el.scrollTop;

    if (el === winScroller) {
      canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll' || elCSS.overflowX === 'visible');
      canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll' || elCSS.overflowY === 'visible');
    } else {
      canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll');
      canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll');
    }

    var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
    var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);

    if (!autoScrolls[layersOut]) {
      for (var i = 0; i <= layersOut; i++) {
        if (!autoScrolls[i]) {
          autoScrolls[i] = {};
        }
      }
    }

    if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
      autoScrolls[layersOut].el = el;
      autoScrolls[layersOut].vx = vx;
      autoScrolls[layersOut].vy = vy;
      clearInterval(autoScrolls[layersOut].pid);

      if (vx != 0 || vy != 0) {
        scrollThisInstance = true;
        /* jshint loopfunc:true */

        autoScrolls[layersOut].pid = setInterval(function () {
          // emulate drag over during autoscroll (fallback), emulating native DnD behaviour
          if (isFallback && this.layer === 0) {
            Sortable.active._onTouchMove(touchEvt$1); // To move ghost if it is positioned absolutely

          }

          var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
          var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;

          if (typeof scrollCustomFn === 'function') {
            if (scrollCustomFn.call(Sortable.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== 'continue') {
              return;
            }
          }

          scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
        }.bind({
          layer: layersOut
        }), 24);
      }
    }

    layersOut++;
  } while (options.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));

  scrolling = scrollThisInstance; // in case another function catches scrolling as false in between when it is not
}, 30);

var drop = function drop(_ref) {
  var originalEvent = _ref.originalEvent,
      putSortable = _ref.putSortable,
      dragEl = _ref.dragEl,
      activeSortable = _ref.activeSortable,
      dispatchSortableEvent = _ref.dispatchSortableEvent,
      hideGhostForTarget = _ref.hideGhostForTarget,
      unhideGhostForTarget = _ref.unhideGhostForTarget;
  if (!originalEvent) return;
  var toSortable = putSortable || activeSortable;
  hideGhostForTarget();
  var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
  var target = document.elementFromPoint(touch.clientX, touch.clientY);
  unhideGhostForTarget();

  if (toSortable && !toSortable.el.contains(target)) {
    dispatchSortableEvent('spill');
    this.onSpill({
      dragEl: dragEl,
      putSortable: putSortable
    });
  }
};

function Revert() {}

Revert.prototype = {
  startIndex: null,
  dragStart: function dragStart(_ref2) {
    var oldDraggableIndex = _ref2.oldDraggableIndex;
    this.startIndex = oldDraggableIndex;
  },
  onSpill: function onSpill(_ref3) {
    var dragEl = _ref3.dragEl,
        putSortable = _ref3.putSortable;
    this.sortable.captureAnimationState();

    if (putSortable) {
      putSortable.captureAnimationState();
    }

    var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);

    if (nextSibling) {
      this.sortable.el.insertBefore(dragEl, nextSibling);
    } else {
      this.sortable.el.appendChild(dragEl);
    }

    this.sortable.animateAll();

    if (putSortable) {
      putSortable.animateAll();
    }
  },
  drop: drop
};

_extends(Revert, {
  pluginName: 'revertOnSpill'
});

function Remove() {}

Remove.prototype = {
  onSpill: function onSpill(_ref4) {
    var dragEl = _ref4.dragEl,
        putSortable = _ref4.putSortable;
    var parentSortable = putSortable || this.sortable;
    parentSortable.captureAnimationState();
    dragEl.parentNode && dragEl.parentNode.removeChild(dragEl);
    parentSortable.animateAll();
  },
  drop: drop
};

_extends(Remove, {
  pluginName: 'removeOnSpill'
});

var lastSwapEl;

function SwapPlugin() {
  function Swap() {
    this.defaults = {
      swapClass: 'sortable-swap-highlight'
    };
  }

  Swap.prototype = {
    dragStart: function dragStart(_ref) {
      var dragEl = _ref.dragEl;
      lastSwapEl = dragEl;
    },
    dragOverValid: function dragOverValid(_ref2) {
      var completed = _ref2.completed,
          target = _ref2.target,
          onMove = _ref2.onMove,
          activeSortable = _ref2.activeSortable,
          changed = _ref2.changed,
          cancel = _ref2.cancel;
      if (!activeSortable.options.swap) return;
      var el = this.sortable.el,
          options = this.options;

      if (target && target !== el) {
        var prevSwapEl = lastSwapEl;

        if (onMove(target) !== false) {
          toggleClass(target, options.swapClass, true);
          lastSwapEl = target;
        } else {
          lastSwapEl = null;
        }

        if (prevSwapEl && prevSwapEl !== lastSwapEl) {
          toggleClass(prevSwapEl, options.swapClass, false);
        }
      }

      changed();
      completed(true);
      cancel();
    },
    drop: function drop(_ref3) {
      var activeSortable = _ref3.activeSortable,
          putSortable = _ref3.putSortable,
          dragEl = _ref3.dragEl;
      var toSortable = putSortable || this.sortable;
      var options = this.options;
      lastSwapEl && toggleClass(lastSwapEl, options.swapClass, false);

      if (lastSwapEl && (options.swap || putSortable && putSortable.options.swap)) {
        if (dragEl !== lastSwapEl) {
          toSortable.captureAnimationState();
          if (toSortable !== activeSortable) activeSortable.captureAnimationState();
          swapNodes(dragEl, lastSwapEl);
          toSortable.animateAll();
          if (toSortable !== activeSortable) activeSortable.animateAll();
        }
      }
    },
    nulling: function nulling() {
      lastSwapEl = null;
    }
  };
  return _extends(Swap, {
    pluginName: 'swap',
    eventProperties: function eventProperties() {
      return {
        swapItem: lastSwapEl
      };
    }
  });
}

function swapNodes(n1, n2) {
  var p1 = n1.parentNode,
      p2 = n2.parentNode,
      i1,
      i2;
  if (!p1 || !p2 || p1.isEqualNode(n2) || p2.isEqualNode(n1)) return;
  i1 = index(n1);
  i2 = index(n2);

  if (p1.isEqualNode(p2) && i1 < i2) {
    i2++;
  }

  p1.insertBefore(n2, p1.children[i1]);
  p2.insertBefore(n1, p2.children[i2]);
}

var multiDragElements = [],
    multiDragClones = [],
    lastMultiDragSelect,
    // for selection with modifier key down (SHIFT)
multiDragSortable,
    initialFolding = false,
    // Initial multi-drag fold when drag started
folding = false,
    // Folding any other time
dragStarted = false,
    dragEl$1,
    clonesFromRect,
    clonesHidden;

function MultiDragPlugin() {
  function MultiDrag(sortable) {
    // Bind all private methods
    for (var fn in this) {
      if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
        this[fn] = this[fn].bind(this);
      }
    }

    if (sortable.options.supportPointer) {
      on(document, 'pointerup', this._deselectMultiDrag);
    } else {
      on(document, 'mouseup', this._deselectMultiDrag);
      on(document, 'touchend', this._deselectMultiDrag);
    }

    on(document, 'keydown', this._checkKeyDown);
    on(document, 'keyup', this._checkKeyUp);
    this.defaults = {
      selectedClass: 'sortable-selected',
      multiDragKey: null,
      setData: function setData(dataTransfer, dragEl) {
        var data = '';

        if (multiDragElements.length && multiDragSortable === sortable) {
          multiDragElements.forEach(function (multiDragElement, i) {
            data += (!i ? '' : ', ') + multiDragElement.textContent;
          });
        } else {
          data = dragEl.textContent;
        }

        dataTransfer.setData('Text', data);
      }
    };
  }

  MultiDrag.prototype = {
    multiDragKeyDown: false,
    isMultiDrag: false,
    delayStartGlobal: function delayStartGlobal(_ref) {
      var dragged = _ref.dragEl;
      dragEl$1 = dragged;
    },
    delayEnded: function delayEnded() {
      this.isMultiDrag = ~multiDragElements.indexOf(dragEl$1);
    },
    setupClone: function setupClone(_ref2) {
      var sortable = _ref2.sortable,
          cancel = _ref2.cancel;
      if (!this.isMultiDrag) return;

      for (var i = 0; i < multiDragElements.length; i++) {
        multiDragClones.push(clone(multiDragElements[i]));
        multiDragClones[i].sortableIndex = multiDragElements[i].sortableIndex;
        multiDragClones[i].draggable = false;
        multiDragClones[i].style['will-change'] = '';
        toggleClass(multiDragClones[i], this.options.selectedClass, false);
        multiDragElements[i] === dragEl$1 && toggleClass(multiDragClones[i], this.options.chosenClass, false);
      }

      sortable._hideClone();

      cancel();
    },
    clone: function clone(_ref3) {
      var sortable = _ref3.sortable,
          rootEl = _ref3.rootEl,
          dispatchSortableEvent = _ref3.dispatchSortableEvent,
          cancel = _ref3.cancel;
      if (!this.isMultiDrag) return;

      if (!this.options.removeCloneOnHide) {
        if (multiDragElements.length && multiDragSortable === sortable) {
          insertMultiDragClones(true, rootEl);
          dispatchSortableEvent('clone');
          cancel();
        }
      }
    },
    showClone: function showClone(_ref4) {
      var cloneNowShown = _ref4.cloneNowShown,
          rootEl = _ref4.rootEl,
          cancel = _ref4.cancel;
      if (!this.isMultiDrag) return;
      insertMultiDragClones(false, rootEl);
      multiDragClones.forEach(function (clone) {
        css(clone, 'display', '');
      });
      cloneNowShown();
      clonesHidden = false;
      cancel();
    },
    hideClone: function hideClone(_ref5) {
      var _this = this;

      var sortable = _ref5.sortable,
          cloneNowHidden = _ref5.cloneNowHidden,
          cancel = _ref5.cancel;
      if (!this.isMultiDrag) return;
      multiDragClones.forEach(function (clone) {
        css(clone, 'display', 'none');

        if (_this.options.removeCloneOnHide && clone.parentNode) {
          clone.parentNode.removeChild(clone);
        }
      });
      cloneNowHidden();
      clonesHidden = true;
      cancel();
    },
    dragStartGlobal: function dragStartGlobal(_ref6) {
      var sortable = _ref6.sortable;

      if (!this.isMultiDrag && multiDragSortable) {
        multiDragSortable.multiDrag._deselectMultiDrag();
      }

      multiDragElements.forEach(function (multiDragElement) {
        multiDragElement.sortableIndex = index(multiDragElement);
      }); // Sort multi-drag elements

      multiDragElements = multiDragElements.sort(function (a, b) {
        return a.sortableIndex - b.sortableIndex;
      });
      dragStarted = true;
    },
    dragStarted: function dragStarted(_ref7) {
      var _this2 = this;

      var sortable = _ref7.sortable;
      if (!this.isMultiDrag) return;

      if (this.options.sort) {
        // Capture rects,
        // hide multi drag elements (by positioning them absolute),
        // set multi drag elements rects to dragRect,
        // show multi drag elements,
        // animate to rects,
        // unset rects & remove from DOM
        sortable.captureAnimationState();

        if (this.options.animation) {
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            css(multiDragElement, 'position', 'absolute');
          });
          var dragRect = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRect);
          });
          folding = true;
          initialFolding = true;
        }
      }

      sortable.animateAll(function () {
        folding = false;
        initialFolding = false;

        if (_this2.options.animation) {
          multiDragElements.forEach(function (multiDragElement) {
            unsetRect(multiDragElement);
          });
        } // Remove all auxiliary multidrag items from el, if sorting enabled


        if (_this2.options.sort) {
          removeMultiDragElements();
        }
      });
    },
    dragOver: function dragOver(_ref8) {
      var target = _ref8.target,
          completed = _ref8.completed,
          cancel = _ref8.cancel;

      if (folding && ~multiDragElements.indexOf(target)) {
        completed(false);
        cancel();
      }
    },
    revert: function revert(_ref9) {
      var fromSortable = _ref9.fromSortable,
          rootEl = _ref9.rootEl,
          sortable = _ref9.sortable,
          dragRect = _ref9.dragRect;

      if (multiDragElements.length > 1) {
        // Setup unfold animation
        multiDragElements.forEach(function (multiDragElement) {
          sortable.addAnimationState({
            target: multiDragElement,
            rect: folding ? getRect(multiDragElement) : dragRect
          });
          unsetRect(multiDragElement);
          multiDragElement.fromRect = dragRect;
          fromSortable.removeAnimationState(multiDragElement);
        });
        folding = false;
        insertMultiDragElements(!this.options.removeCloneOnHide, rootEl);
      }
    },
    dragOverCompleted: function dragOverCompleted(_ref10) {
      var sortable = _ref10.sortable,
          isOwner = _ref10.isOwner,
          insertion = _ref10.insertion,
          activeSortable = _ref10.activeSortable,
          parentEl = _ref10.parentEl,
          putSortable = _ref10.putSortable;
      var options = this.options;

      if (insertion) {
        // Clones must be hidden before folding animation to capture dragRectAbsolute properly
        if (isOwner) {
          activeSortable._hideClone();
        }

        initialFolding = false; // If leaving sort:false root, or already folding - Fold to new location

        if (options.animation && multiDragElements.length > 1 && (folding || !isOwner && !activeSortable.options.sort && !putSortable)) {
          // Fold: Set all multi drag elements's rects to dragEl's rect when multi-drag elements are invisible
          var dragRectAbsolute = getRect(dragEl$1, false, true, true);
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            setRect(multiDragElement, dragRectAbsolute); // Move element(s) to end of parentEl so that it does not interfere with multi-drag clones insertion if they are inserted
            // while folding, and so that we can capture them again because old sortable will no longer be fromSortable

            parentEl.appendChild(multiDragElement);
          });
          folding = true;
        } // Clones must be shown (and check to remove multi drags) after folding when interfering multiDragElements are moved out


        if (!isOwner) {
          // Only remove if not folding (folding will remove them anyways)
          if (!folding) {
            removeMultiDragElements();
          }

          if (multiDragElements.length > 1) {
            var clonesHiddenBefore = clonesHidden;

            activeSortable._showClone(sortable); // Unfold animation for clones if showing from hidden


            if (activeSortable.options.animation && !clonesHidden && clonesHiddenBefore) {
              multiDragClones.forEach(function (clone) {
                activeSortable.addAnimationState({
                  target: clone,
                  rect: clonesFromRect
                });
                clone.fromRect = clonesFromRect;
                clone.thisAnimationDuration = null;
              });
            }
          } else {
            activeSortable._showClone(sortable);
          }
        }
      }
    },
    dragOverAnimationCapture: function dragOverAnimationCapture(_ref11) {
      var dragRect = _ref11.dragRect,
          isOwner = _ref11.isOwner,
          activeSortable = _ref11.activeSortable;
      multiDragElements.forEach(function (multiDragElement) {
        multiDragElement.thisAnimationDuration = null;
      });

      if (activeSortable.options.animation && !isOwner && activeSortable.multiDrag.isMultiDrag) {
        clonesFromRect = _extends({}, dragRect);
        var dragMatrix = matrix(dragEl$1, true);
        clonesFromRect.top -= dragMatrix.f;
        clonesFromRect.left -= dragMatrix.e;
      }
    },
    dragOverAnimationComplete: function dragOverAnimationComplete() {
      if (folding) {
        folding = false;
        removeMultiDragElements();
      }
    },
    drop: function drop(_ref12) {
      var evt = _ref12.originalEvent,
          rootEl = _ref12.rootEl,
          parentEl = _ref12.parentEl,
          sortable = _ref12.sortable,
          dispatchSortableEvent = _ref12.dispatchSortableEvent,
          oldIndex = _ref12.oldIndex,
          putSortable = _ref12.putSortable;
      var toSortable = putSortable || this.sortable;
      if (!evt) return;
      var options = this.options,
          children = parentEl.children; // Multi-drag selection

      if (!dragStarted) {
        if (options.multiDragKey && !this.multiDragKeyDown) {
          this._deselectMultiDrag();
        }

        toggleClass(dragEl$1, options.selectedClass, !~multiDragElements.indexOf(dragEl$1));

        if (!~multiDragElements.indexOf(dragEl$1)) {
          multiDragElements.push(dragEl$1);
          dispatchEvent({
            sortable: sortable,
            rootEl: rootEl,
            name: 'select',
            targetEl: dragEl$1,
            originalEvt: evt
          }); // Modifier activated, select from last to dragEl

          if (evt.shiftKey && lastMultiDragSelect && sortable.el.contains(lastMultiDragSelect)) {
            var lastIndex = index(lastMultiDragSelect),
                currentIndex = index(dragEl$1);

            if (~lastIndex && ~currentIndex && lastIndex !== currentIndex) {
              // Must include lastMultiDragSelect (select it), in case modified selection from no selection
              // (but previous selection existed)
              var n, i;

              if (currentIndex > lastIndex) {
                i = lastIndex;
                n = currentIndex;
              } else {
                i = currentIndex;
                n = lastIndex + 1;
              }

              for (; i < n; i++) {
                if (~multiDragElements.indexOf(children[i])) continue;
                toggleClass(children[i], options.selectedClass, true);
                multiDragElements.push(children[i]);
                dispatchEvent({
                  sortable: sortable,
                  rootEl: rootEl,
                  name: 'select',
                  targetEl: children[i],
                  originalEvt: evt
                });
              }
            }
          } else {
            lastMultiDragSelect = dragEl$1;
          }

          multiDragSortable = toSortable;
        } else {
          multiDragElements.splice(multiDragElements.indexOf(dragEl$1), 1);
          lastMultiDragSelect = null;
          dispatchEvent({
            sortable: sortable,
            rootEl: rootEl,
            name: 'deselect',
            targetEl: dragEl$1,
            originalEvt: evt
          });
        }
      } // Multi-drag drop


      if (dragStarted && this.isMultiDrag) {
        // Do not "unfold" after around dragEl if reverted
        if ((parentEl[expando].options.sort || parentEl !== rootEl) && multiDragElements.length > 1) {
          var dragRect = getRect(dragEl$1),
              multiDragIndex = index(dragEl$1, ':not(.' + this.options.selectedClass + ')');
          if (!initialFolding && options.animation) dragEl$1.thisAnimationDuration = null;
          toSortable.captureAnimationState();

          if (!initialFolding) {
            if (options.animation) {
              dragEl$1.fromRect = dragRect;
              multiDragElements.forEach(function (multiDragElement) {
                multiDragElement.thisAnimationDuration = null;

                if (multiDragElement !== dragEl$1) {
                  var rect = folding ? getRect(multiDragElement) : dragRect;
                  multiDragElement.fromRect = rect; // Prepare unfold animation

                  toSortable.addAnimationState({
                    target: multiDragElement,
                    rect: rect
                  });
                }
              });
            } // Multi drag elements are not necessarily removed from the DOM on drop, so to reinsert
            // properly they must all be removed


            removeMultiDragElements();
            multiDragElements.forEach(function (multiDragElement) {
              if (children[multiDragIndex]) {
                parentEl.insertBefore(multiDragElement, children[multiDragIndex]);
              } else {
                parentEl.appendChild(multiDragElement);
              }

              multiDragIndex++;
            }); // If initial folding is done, the elements may have changed position because they are now
            // unfolding around dragEl, even though dragEl may not have his index changed, so update event
            // must be fired here as Sortable will not.

            if (oldIndex === index(dragEl$1)) {
              var update = false;
              multiDragElements.forEach(function (multiDragElement) {
                if (multiDragElement.sortableIndex !== index(multiDragElement)) {
                  update = true;
                  return;
                }
              });

              if (update) {
                dispatchSortableEvent('update');
              }
            }
          } // Must be done after capturing individual rects (scroll bar)


          multiDragElements.forEach(function (multiDragElement) {
            unsetRect(multiDragElement);
          });
          toSortable.animateAll();
        }

        multiDragSortable = toSortable;
      } // Remove clones if necessary


      if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
        multiDragClones.forEach(function (clone) {
          clone.parentNode && clone.parentNode.removeChild(clone);
        });
      }
    },
    nullingGlobal: function nullingGlobal() {
      this.isMultiDrag = dragStarted = false;
      multiDragClones.length = 0;
    },
    destroyGlobal: function destroyGlobal() {
      this._deselectMultiDrag();

      off(document, 'pointerup', this._deselectMultiDrag);
      off(document, 'mouseup', this._deselectMultiDrag);
      off(document, 'touchend', this._deselectMultiDrag);
      off(document, 'keydown', this._checkKeyDown);
      off(document, 'keyup', this._checkKeyUp);
    },
    _deselectMultiDrag: function _deselectMultiDrag(evt) {
      if (typeof dragStarted !== "undefined" && dragStarted) return; // Only deselect if selection is in this sortable

      if (multiDragSortable !== this.sortable) return; // Only deselect if target is not item in this sortable

      if (evt && closest(evt.target, this.options.draggable, this.sortable.el, false)) return; // Only deselect if left click

      if (evt && evt.button !== 0) return;

      while (multiDragElements.length) {
        var el = multiDragElements[0];
        toggleClass(el, this.options.selectedClass, false);
        multiDragElements.shift();
        dispatchEvent({
          sortable: this.sortable,
          rootEl: this.sortable.el,
          name: 'deselect',
          targetEl: el,
          originalEvt: evt
        });
      }
    },
    _checkKeyDown: function _checkKeyDown(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = true;
      }
    },
    _checkKeyUp: function _checkKeyUp(evt) {
      if (evt.key === this.options.multiDragKey) {
        this.multiDragKeyDown = false;
      }
    }
  };
  return _extends(MultiDrag, {
    // Static methods & properties
    pluginName: 'multiDrag',
    utils: {
      /**
       * Selects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be selected
       */
      select: function select(el) {
        var sortable = el.parentNode[expando];
        if (!sortable || !sortable.options.multiDrag || ~multiDragElements.indexOf(el)) return;

        if (multiDragSortable && multiDragSortable !== sortable) {
          multiDragSortable.multiDrag._deselectMultiDrag();

          multiDragSortable = sortable;
        }

        toggleClass(el, sortable.options.selectedClass, true);
        multiDragElements.push(el);
      },

      /**
       * Deselects the provided multi-drag item
       * @param  {HTMLElement} el    The element to be deselected
       */
      deselect: function deselect(el) {
        var sortable = el.parentNode[expando],
            index = multiDragElements.indexOf(el);
        if (!sortable || !sortable.options.multiDrag || !~index) return;
        toggleClass(el, sortable.options.selectedClass, false);
        multiDragElements.splice(index, 1);
      }
    },
    eventProperties: function eventProperties() {
      var _this3 = this;

      var oldIndicies = [],
          newIndicies = [];
      multiDragElements.forEach(function (multiDragElement) {
        oldIndicies.push({
          multiDragElement: multiDragElement,
          index: multiDragElement.sortableIndex
        }); // multiDragElements will already be sorted if folding

        var newIndex;

        if (folding && multiDragElement !== dragEl$1) {
          newIndex = -1;
        } else if (folding) {
          newIndex = index(multiDragElement, ':not(.' + _this3.options.selectedClass + ')');
        } else {
          newIndex = index(multiDragElement);
        }

        newIndicies.push({
          multiDragElement: multiDragElement,
          index: newIndex
        });
      });
      return {
        items: _toConsumableArray(multiDragElements),
        clones: [].concat(multiDragClones),
        oldIndicies: oldIndicies,
        newIndicies: newIndicies
      };
    },
    optionListeners: {
      multiDragKey: function multiDragKey(key) {
        key = key.toLowerCase();

        if (key === 'ctrl') {
          key = 'Control';
        } else if (key.length > 1) {
          key = key.charAt(0).toUpperCase() + key.substr(1);
        }

        return key;
      }
    }
  });
}

function insertMultiDragElements(clonesInserted, rootEl) {
  multiDragElements.forEach(function (multiDragElement, i) {
    var target = rootEl.children[multiDragElement.sortableIndex + (clonesInserted ? Number(i) : 0)];

    if (target) {
      rootEl.insertBefore(multiDragElement, target);
    } else {
      rootEl.appendChild(multiDragElement);
    }
  });
}
/**
 * Insert multi-drag clones
 * @param  {[Boolean]} elementsInserted  Whether the multi-drag elements are inserted
 * @param  {HTMLElement} rootEl
 */


function insertMultiDragClones(elementsInserted, rootEl) {
  multiDragClones.forEach(function (clone, i) {
    var target = rootEl.children[clone.sortableIndex + (elementsInserted ? Number(i) : 0)];

    if (target) {
      rootEl.insertBefore(clone, target);
    } else {
      rootEl.appendChild(clone);
    }
  });
}

function removeMultiDragElements() {
  multiDragElements.forEach(function (multiDragElement) {
    if (multiDragElement === dragEl$1) return;
    multiDragElement.parentNode && multiDragElement.parentNode.removeChild(multiDragElement);
  });
}

Sortable.mount(new AutoScrollPlugin());
Sortable.mount(Remove, Revert);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Sortable);



/***/ }),

/***/ 530:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

(function webpackUniversalModuleDefinition(root, factory) {
	if(true)
		module.exports = factory(__webpack_require__(386));
	else {}
})((typeof self !== 'undefined' ? self : this), function(__WEBPACK_EXTERNAL_MODULE_a352__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __nested_webpack_require_688__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __nested_webpack_require_688__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__nested_webpack_require_688__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__nested_webpack_require_688__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__nested_webpack_require_688__.d = function(exports, name, getter) {
/******/ 		if(!__nested_webpack_require_688__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__nested_webpack_require_688__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__nested_webpack_require_688__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __nested_webpack_require_688__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__nested_webpack_require_688__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __nested_webpack_require_688__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__nested_webpack_require_688__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__nested_webpack_require_688__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__nested_webpack_require_688__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__nested_webpack_require_688__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __nested_webpack_require_688__(__nested_webpack_require_688__.s = "fb15");
/******/ })
/************************************************************************/
/******/ ({

/***/ "01f9":
/***/ (function(module, exports, __nested_webpack_require_4164__) {

"use strict";

var LIBRARY = __nested_webpack_require_4164__("2d00");
var $export = __nested_webpack_require_4164__("5ca1");
var redefine = __nested_webpack_require_4164__("2aba");
var hide = __nested_webpack_require_4164__("32e9");
var Iterators = __nested_webpack_require_4164__("84f2");
var $iterCreate = __nested_webpack_require_4164__("41a0");
var setToStringTag = __nested_webpack_require_4164__("7f20");
var getPrototypeOf = __nested_webpack_require_4164__("38fd");
var ITERATOR = __nested_webpack_require_4164__("2b4c")('iterator');
var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`
var FF_ITERATOR = '@@iterator';
var KEYS = 'keys';
var VALUES = 'values';

var returnThis = function () { return this; };

module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
  $iterCreate(Constructor, NAME, next);
  var getMethod = function (kind) {
    if (!BUGGY && kind in proto) return proto[kind];
    switch (kind) {
      case KEYS: return function keys() { return new Constructor(this, kind); };
      case VALUES: return function values() { return new Constructor(this, kind); };
    } return function entries() { return new Constructor(this, kind); };
  };
  var TAG = NAME + ' Iterator';
  var DEF_VALUES = DEFAULT == VALUES;
  var VALUES_BUG = false;
  var proto = Base.prototype;
  var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
  var $default = $native || getMethod(DEFAULT);
  var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
  var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
  var methods, key, IteratorPrototype;
  // Fix native
  if ($anyNative) {
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));
    if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if (DEF_VALUES && $native && $native.name !== VALUES) {
    VALUES_BUG = true;
    $default = function values() { return $native.call(this); };
  }
  // Define iterator
  if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG] = returnThis;
  if (DEFAULT) {
    methods = {
      values: DEF_VALUES ? $default : getMethod(VALUES),
      keys: IS_SET ? $default : getMethod(KEYS),
      entries: $entries
    };
    if (FORCED) for (key in methods) {
      if (!(key in proto)) redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};


/***/ }),

/***/ "02f4":
/***/ (function(module, exports, __nested_webpack_require_7070__) {

var toInteger = __nested_webpack_require_7070__("4588");
var defined = __nested_webpack_require_7070__("be13");
// true  -> String#at
// false -> String#codePointAt
module.exports = function (TO_STRING) {
  return function (that, pos) {
    var s = String(defined(that));
    var i = toInteger(pos);
    var l = s.length;
    var a, b;
    if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
    a = s.charCodeAt(i);
    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
      ? TO_STRING ? s.charAt(i) : a
      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
  };
};


/***/ }),

/***/ "0390":
/***/ (function(module, exports, __nested_webpack_require_7783__) {

"use strict";

var at = __nested_webpack_require_7783__("02f4")(true);

 // `AdvanceStringIndex` abstract operation
// https://tc39.github.io/ecma262/#sec-advancestringindex
module.exports = function (S, index, unicode) {
  return index + (unicode ? at(S, index).length : 1);
};


/***/ }),

/***/ "0bfb":
/***/ (function(module, exports, __nested_webpack_require_8134__) {

"use strict";

// 21.2.5.3 get RegExp.prototype.flags
var anObject = __nested_webpack_require_8134__("cb7c");
module.exports = function () {
  var that = anObject(this);
  var result = '';
  if (that.global) result += 'g';
  if (that.ignoreCase) result += 'i';
  if (that.multiline) result += 'm';
  if (that.unicode) result += 'u';
  if (that.sticky) result += 'y';
  return result;
};


/***/ }),

/***/ "0d58":
/***/ (function(module, exports, __nested_webpack_require_8593__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __nested_webpack_require_8593__("ce10");
var enumBugKeys = __nested_webpack_require_8593__("e11e");

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),

/***/ "1495":
/***/ (function(module, exports, __nested_webpack_require_8892__) {

var dP = __nested_webpack_require_8892__("86cc");
var anObject = __nested_webpack_require_8892__("cb7c");
var getKeys = __nested_webpack_require_8892__("0d58");

module.exports = __nested_webpack_require_8892__("9e1e") ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = getKeys(Properties);
  var length = keys.length;
  var i = 0;
  var P;
  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
  return O;
};


/***/ }),

/***/ "214f":
/***/ (function(module, exports, __nested_webpack_require_9392__) {

"use strict";

__nested_webpack_require_9392__("b0c5");
var redefine = __nested_webpack_require_9392__("2aba");
var hide = __nested_webpack_require_9392__("32e9");
var fails = __nested_webpack_require_9392__("79e5");
var defined = __nested_webpack_require_9392__("be13");
var wks = __nested_webpack_require_9392__("2b4c");
var regexpExec = __nested_webpack_require_9392__("520a");

var SPECIES = wks('species');

var REPLACE_SUPPORTS_NAMED_GROUPS = !fails(function () {
  // #replace needs built-in support for named groups.
  // #match works fine because it just return the exec results, even if it has
  // a "grops" property.
  var re = /./;
  re.exec = function () {
    var result = [];
    result.groups = { a: '7' };
    return result;
  };
  return ''.replace(re, '$<a>') !== '7';
});

var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = (function () {
  // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
  var re = /(?:)/;
  var originalExec = re.exec;
  re.exec = function () { return originalExec.apply(this, arguments); };
  var result = 'ab'.split(re);
  return result.length === 2 && result[0] === 'a' && result[1] === 'b';
})();

module.exports = function (KEY, length, exec) {
  var SYMBOL = wks(KEY);

  var DELEGATES_TO_SYMBOL = !fails(function () {
    // String methods call symbol-named RegEp methods
    var O = {};
    O[SYMBOL] = function () { return 7; };
    return ''[KEY](O) != 7;
  });

  var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL ? !fails(function () {
    // Symbol-named RegExp methods call .exec
    var execCalled = false;
    var re = /a/;
    re.exec = function () { execCalled = true; return null; };
    if (KEY === 'split') {
      // RegExp[@@split] doesn't call the regex's exec method, but first creates
      // a new one. We need to return the patched regex when creating the new one.
      re.constructor = {};
      re.constructor[SPECIES] = function () { return re; };
    }
    re[SYMBOL]('');
    return !execCalled;
  }) : undefined;

  if (
    !DELEGATES_TO_SYMBOL ||
    !DELEGATES_TO_EXEC ||
    (KEY === 'replace' && !REPLACE_SUPPORTS_NAMED_GROUPS) ||
    (KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC)
  ) {
    var nativeRegExpMethod = /./[SYMBOL];
    var fns = exec(
      defined,
      SYMBOL,
      ''[KEY],
      function maybeCallNative(nativeMethod, regexp, str, arg2, forceStringMethod) {
        if (regexp.exec === regexpExec) {
          if (DELEGATES_TO_SYMBOL && !forceStringMethod) {
            // The native String method already delegates to @@method (this
            // polyfilled function), leasing to infinite recursion.
            // We avoid it by directly calling the native @@method method.
            return { done: true, value: nativeRegExpMethod.call(regexp, str, arg2) };
          }
          return { done: true, value: nativeMethod.call(str, regexp, arg2) };
        }
        return { done: false };
      }
    );
    var strfn = fns[0];
    var rxfn = fns[1];

    redefine(String.prototype, KEY, strfn);
    hide(RegExp.prototype, SYMBOL, length == 2
      // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
      // 21.2.5.11 RegExp.prototype[@@split](string, limit)
      ? function (string, arg) { return rxfn.call(string, this, arg); }
      // 21.2.5.6 RegExp.prototype[@@match](string)
      // 21.2.5.9 RegExp.prototype[@@search](string)
      : function (string) { return rxfn.call(string, this); }
    );
  }
};


/***/ }),

/***/ "230e":
/***/ (function(module, exports, __nested_webpack_require_12849__) {

var isObject = __nested_webpack_require_12849__("d3f4");
var document = __nested_webpack_require_12849__("7726").document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),

/***/ "23c6":
/***/ (function(module, exports, __nested_webpack_require_13233__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __nested_webpack_require_13233__("2d95");
var TAG = __nested_webpack_require_13233__("2b4c")('toStringTag');
// ES3 wrong here
var ARG = cof(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (e) { /* empty */ }
};

module.exports = function (it) {
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};


/***/ }),

/***/ "2621":
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ "2aba":
/***/ (function(module, exports, __nested_webpack_require_14160__) {

var global = __nested_webpack_require_14160__("7726");
var hide = __nested_webpack_require_14160__("32e9");
var has = __nested_webpack_require_14160__("69a8");
var SRC = __nested_webpack_require_14160__("ca5a")('src');
var $toString = __nested_webpack_require_14160__("fa5b");
var TO_STRING = 'toString';
var TPL = ('' + $toString).split(TO_STRING);

__nested_webpack_require_14160__("8378").inspectSource = function (it) {
  return $toString.call(it);
};

(module.exports = function (O, key, val, safe) {
  var isFunction = typeof val == 'function';
  if (isFunction) has(val, 'name') || hide(val, 'name', key);
  if (O[key] === val) return;
  if (isFunction) has(val, SRC) || hide(val, SRC, O[key] ? '' + O[key] : TPL.join(String(key)));
  if (O === global) {
    O[key] = val;
  } else if (!safe) {
    delete O[key];
    hide(O, key, val);
  } else if (O[key]) {
    O[key] = val;
  } else {
    hide(O, key, val);
  }
// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
})(Function.prototype, TO_STRING, function toString() {
  return typeof this == 'function' && this[SRC] || $toString.call(this);
});


/***/ }),

/***/ "2aeb":
/***/ (function(module, exports, __nested_webpack_require_15334__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject = __nested_webpack_require_15334__("cb7c");
var dPs = __nested_webpack_require_15334__("1495");
var enumBugKeys = __nested_webpack_require_15334__("e11e");
var IE_PROTO = __nested_webpack_require_15334__("613b")('IE_PROTO');
var Empty = function () { /* empty */ };
var PROTOTYPE = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __nested_webpack_require_15334__("230e")('iframe');
  var i = enumBugKeys.length;
  var lt = '<';
  var gt = '>';
  var iframeDocument;
  iframe.style.display = 'none';
  __nested_webpack_require_15334__("fab2").appendChild(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while (i--) delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty();
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};


/***/ }),

/***/ "2b4c":
/***/ (function(module, exports, __nested_webpack_require_16945__) {

var store = __nested_webpack_require_16945__("5537")('wks');
var uid = __nested_webpack_require_16945__("ca5a");
var Symbol = __nested_webpack_require_16945__("7726").Symbol;
var USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function (name) {
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;


/***/ }),

/***/ "2d00":
/***/ (function(module, exports) {

module.exports = false;


/***/ }),

/***/ "2d95":
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),

/***/ "2fdb":
/***/ (function(module, exports, __nested_webpack_require_17667__) {

"use strict";
// 21.1.3.7 String.prototype.includes(searchString, position = 0)

var $export = __nested_webpack_require_17667__("5ca1");
var context = __nested_webpack_require_17667__("d2c8");
var INCLUDES = 'includes';

$export($export.P + $export.F * __nested_webpack_require_17667__("5147")(INCLUDES), 'String', {
  includes: function includes(searchString /* , position = 0 */) {
    return !!~context(this, searchString, INCLUDES)
      .indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "32e9":
/***/ (function(module, exports, __nested_webpack_require_18235__) {

var dP = __nested_webpack_require_18235__("86cc");
var createDesc = __nested_webpack_require_18235__("4630");
module.exports = __nested_webpack_require_18235__("9e1e") ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "38fd":
/***/ (function(module, exports, __nested_webpack_require_18611__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has = __nested_webpack_require_18611__("69a8");
var toObject = __nested_webpack_require_18611__("4bf8");
var IE_PROTO = __nested_webpack_require_18611__("613b")('IE_PROTO');
var ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};


/***/ }),

/***/ "41a0":
/***/ (function(module, exports, __nested_webpack_require_19205__) {

"use strict";

var create = __nested_webpack_require_19205__("2aeb");
var descriptor = __nested_webpack_require_19205__("4630");
var setToStringTag = __nested_webpack_require_19205__("7f20");
var IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__nested_webpack_require_19205__("32e9")(IteratorPrototype, __nested_webpack_require_19205__("2b4c")('iterator'), function () { return this; });

module.exports = function (Constructor, NAME, next) {
  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
  setToStringTag(Constructor, NAME + ' Iterator');
};


/***/ }),

/***/ "456d":
/***/ (function(module, exports, __nested_webpack_require_19831__) {

// 19.1.2.14 Object.keys(O)
var toObject = __nested_webpack_require_19831__("4bf8");
var $keys = __nested_webpack_require_19831__("0d58");

__nested_webpack_require_19831__("5eda")('keys', function () {
  return function keys(it) {
    return $keys(toObject(it));
  };
});


/***/ }),

/***/ "4588":
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),

/***/ "4630":
/***/ (function(module, exports) {

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),

/***/ "4bf8":
/***/ (function(module, exports, __nested_webpack_require_20609__) {

// 7.1.13 ToObject(argument)
var defined = __nested_webpack_require_20609__("be13");
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),

/***/ "5147":
/***/ (function(module, exports, __nested_webpack_require_20831__) {

var MATCH = __nested_webpack_require_20831__("2b4c")('match');
module.exports = function (KEY) {
  var re = /./;
  try {
    '/./'[KEY](re);
  } catch (e) {
    try {
      re[MATCH] = false;
      return !'/./'[KEY](re);
    } catch (f) { /* empty */ }
  } return true;
};


/***/ }),

/***/ "520a":
/***/ (function(module, exports, __nested_webpack_require_21176__) {

"use strict";


var regexpFlags = __nested_webpack_require_21176__("0bfb");

var nativeExec = RegExp.prototype.exec;
// This always refers to the native implementation, because the
// String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
// which loads this file before patching the method.
var nativeReplace = String.prototype.replace;

var patchedExec = nativeExec;

var LAST_INDEX = 'lastIndex';

var UPDATES_LAST_INDEX_WRONG = (function () {
  var re1 = /a/,
      re2 = /b*/g;
  nativeExec.call(re1, 'a');
  nativeExec.call(re2, 'a');
  return re1[LAST_INDEX] !== 0 || re2[LAST_INDEX] !== 0;
})();

// nonparticipating capturing group, copied from es5-shim's String#split patch.
var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;

var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED;

if (PATCH) {
  patchedExec = function exec(str) {
    var re = this;
    var lastIndex, reCopy, match, i;

    if (NPCG_INCLUDED) {
      reCopy = new RegExp('^' + re.source + '$(?!\\s)', regexpFlags.call(re));
    }
    if (UPDATES_LAST_INDEX_WRONG) lastIndex = re[LAST_INDEX];

    match = nativeExec.call(re, str);

    if (UPDATES_LAST_INDEX_WRONG && match) {
      re[LAST_INDEX] = re.global ? match.index + match[0].length : lastIndex;
    }
    if (NPCG_INCLUDED && match && match.length > 1) {
      // Fix browsers whose `exec` methods don't consistently return `undefined`
      // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
      // eslint-disable-next-line no-loop-func
      nativeReplace.call(match[0], reCopy, function () {
        for (i = 1; i < arguments.length - 2; i++) {
          if (arguments[i] === undefined) match[i] = undefined;
        }
      });
    }

    return match;
  };
}

module.exports = patchedExec;


/***/ }),

/***/ "52a7":
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),

/***/ "5537":
/***/ (function(module, exports, __nested_webpack_require_23109__) {

var core = __nested_webpack_require_23109__("8378");
var global = __nested_webpack_require_23109__("7726");
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __nested_webpack_require_23109__("2d00") ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ "5ca1":
/***/ (function(module, exports, __nested_webpack_require_23642__) {

var global = __nested_webpack_require_23642__("7726");
var core = __nested_webpack_require_23642__("8378");
var hide = __nested_webpack_require_23642__("32e9");
var redefine = __nested_webpack_require_23642__("2aba");
var ctx = __nested_webpack_require_23642__("9b43");
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] || (global[name] = {}) : (global[name] || {})[PROTOTYPE];
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE] || (exports[PROTOTYPE] = {});
  var key, own, out, exp;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    // export native or passed
    out = (own ? target : source)[key];
    // bind timers to global for call from export context
    exp = IS_BIND && own ? ctx(out, global) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // extend global
    if (target) redefine(target, key, out, type & $export.U);
    // export
    if (exports[key] != out) hide(exports, key, exp);
    if (IS_PROTO && expProto[key] != out) expProto[key] = out;
  }
};
global.core = core;
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library`
module.exports = $export;


/***/ }),

/***/ "5eda":
/***/ (function(module, exports, __nested_webpack_require_25367__) {

// most Object methods by ES6 should accept primitives
var $export = __nested_webpack_require_25367__("5ca1");
var core = __nested_webpack_require_25367__("8378");
var fails = __nested_webpack_require_25367__("79e5");
module.exports = function (KEY, exec) {
  var fn = (core.Object || {})[KEY] || Object[KEY];
  var exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function () { fn(1); }), 'Object', exp);
};


/***/ }),

/***/ "5f1b":
/***/ (function(module, exports, __nested_webpack_require_25845__) {

"use strict";


var classof = __nested_webpack_require_25845__("23c6");
var builtinExec = RegExp.prototype.exec;

 // `RegExpExec` abstract operation
// https://tc39.github.io/ecma262/#sec-regexpexec
module.exports = function (R, S) {
  var exec = R.exec;
  if (typeof exec === 'function') {
    var result = exec.call(R, S);
    if (typeof result !== 'object') {
      throw new TypeError('RegExp exec method returned something other than an Object or null');
    }
    return result;
  }
  if (classof(R) !== 'RegExp') {
    throw new TypeError('RegExp#exec called on incompatible receiver');
  }
  return builtinExec.call(R, S);
};


/***/ }),

/***/ "613b":
/***/ (function(module, exports, __nested_webpack_require_26551__) {

var shared = __nested_webpack_require_26551__("5537")('keys');
var uid = __nested_webpack_require_26551__("ca5a");
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),

/***/ "626a":
/***/ (function(module, exports, __nested_webpack_require_26811__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __nested_webpack_require_26811__("2d95");
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),

/***/ "6762":
/***/ (function(module, exports, __nested_webpack_require_27194__) {

"use strict";

// https://github.com/tc39/Array.prototype.includes
var $export = __nested_webpack_require_27194__("5ca1");
var $includes = __nested_webpack_require_27194__("c366")(true);

$export($export.P, 'Array', {
  includes: function includes(el /* , fromIndex = 0 */) {
    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
  }
});

__nested_webpack_require_27194__("9c6c")('includes');


/***/ }),

/***/ "6821":
/***/ (function(module, exports, __nested_webpack_require_27659__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __nested_webpack_require_27659__("626a");
var defined = __nested_webpack_require_27659__("be13");
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),

/***/ "69a8":
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ "6a99":
/***/ (function(module, exports, __nested_webpack_require_28155__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __nested_webpack_require_28155__("d3f4");
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (it, S) {
  if (!isObject(it)) return it;
  var fn, val;
  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  throw TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ "7333":
/***/ (function(module, exports, __nested_webpack_require_28898__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var getKeys = __nested_webpack_require_28898__("0d58");
var gOPS = __nested_webpack_require_28898__("2621");
var pIE = __nested_webpack_require_28898__("52a7");
var toObject = __nested_webpack_require_28898__("4bf8");
var IObject = __nested_webpack_require_28898__("626a");
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __nested_webpack_require_28898__("79e5")(function () {
  var A = {};
  var B = {};
  // eslint-disable-next-line no-undef
  var S = Symbol();
  var K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function (k) { B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
  var T = toObject(target);
  var aLen = arguments.length;
  var index = 1;
  var getSymbols = gOPS.f;
  var isEnum = pIE.f;
  while (aLen > index) {
    var S = IObject(arguments[index++]);
    var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
    var length = keys.length;
    var j = 0;
    var key;
    while (length > j) if (isEnum.call(S, key = keys[j++])) T[key] = S[key];
  } return T;
} : $assign;


/***/ }),

/***/ "7726":
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),

/***/ "77f1":
/***/ (function(module, exports, __nested_webpack_require_30635__) {

var toInteger = __nested_webpack_require_30635__("4588");
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),

/***/ "79e5":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),

/***/ "7f20":
/***/ (function(module, exports, __nested_webpack_require_31112__) {

var def = __nested_webpack_require_31112__("86cc").f;
var has = __nested_webpack_require_31112__("69a8");
var TAG = __nested_webpack_require_31112__("2b4c")('toStringTag');

module.exports = function (it, tag, stat) {
  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
};


/***/ }),

/***/ "8378":
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.5' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),

/***/ "84f2":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "86cc":
/***/ (function(module, exports, __nested_webpack_require_31751__) {

var anObject = __nested_webpack_require_31751__("cb7c");
var IE8_DOM_DEFINE = __nested_webpack_require_31751__("c69a");
var toPrimitive = __nested_webpack_require_31751__("6a99");
var dP = Object.defineProperty;

exports.f = __nested_webpack_require_31751__("9e1e") ? Object.defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return dP(O, P, Attributes);
  } catch (e) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ "9b43":
/***/ (function(module, exports, __nested_webpack_require_32441__) {

// optional / simple context binding
var aFunction = __nested_webpack_require_32441__("d8e8");
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 1: return function (a) {
      return fn.call(that, a);
    };
    case 2: return function (a, b) {
      return fn.call(that, a, b);
    };
    case 3: return function (a, b, c) {
      return fn.call(that, a, b, c);
    };
  }
  return function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),

/***/ "9c6c":
/***/ (function(module, exports, __nested_webpack_require_33048__) {

// 22.1.3.31 Array.prototype[@@unscopables]
var UNSCOPABLES = __nested_webpack_require_33048__("2b4c")('unscopables');
var ArrayProto = Array.prototype;
if (ArrayProto[UNSCOPABLES] == undefined) __nested_webpack_require_33048__("32e9")(ArrayProto, UNSCOPABLES, {});
module.exports = function (key) {
  ArrayProto[UNSCOPABLES][key] = true;
};


/***/ }),

/***/ "9def":
/***/ (function(module, exports, __nested_webpack_require_33448__) {

// 7.1.15 ToLength
var toInteger = __nested_webpack_require_33448__("4588");
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),

/***/ "9e1e":
/***/ (function(module, exports, __nested_webpack_require_33750__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__nested_webpack_require_33750__("79e5")(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "a352":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_a352__;

/***/ }),

/***/ "a481":
/***/ (function(module, exports, __nested_webpack_require_34139__) {

"use strict";


var anObject = __nested_webpack_require_34139__("cb7c");
var toObject = __nested_webpack_require_34139__("4bf8");
var toLength = __nested_webpack_require_34139__("9def");
var toInteger = __nested_webpack_require_34139__("4588");
var advanceStringIndex = __nested_webpack_require_34139__("0390");
var regExpExec = __nested_webpack_require_34139__("5f1b");
var max = Math.max;
var min = Math.min;
var floor = Math.floor;
var SUBSTITUTION_SYMBOLS = /\$([$&`']|\d\d?|<[^>]*>)/g;
var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&`']|\d\d?)/g;

var maybeToString = function (it) {
  return it === undefined ? it : String(it);
};

// @@replace logic
__nested_webpack_require_34139__("214f")('replace', 2, function (defined, REPLACE, $replace, maybeCallNative) {
  return [
    // `String.prototype.replace` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.replace
    function replace(searchValue, replaceValue) {
      var O = defined(this);
      var fn = searchValue == undefined ? undefined : searchValue[REPLACE];
      return fn !== undefined
        ? fn.call(searchValue, O, replaceValue)
        : $replace.call(String(O), searchValue, replaceValue);
    },
    // `RegExp.prototype[@@replace]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@replace
    function (regexp, replaceValue) {
      var res = maybeCallNative($replace, regexp, this, replaceValue);
      if (res.done) return res.value;

      var rx = anObject(regexp);
      var S = String(this);
      var functionalReplace = typeof replaceValue === 'function';
      if (!functionalReplace) replaceValue = String(replaceValue);
      var global = rx.global;
      if (global) {
        var fullUnicode = rx.unicode;
        rx.lastIndex = 0;
      }
      var results = [];
      while (true) {
        var result = regExpExec(rx, S);
        if (result === null) break;
        results.push(result);
        if (!global) break;
        var matchStr = String(result[0]);
        if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
      }
      var accumulatedResult = '';
      var nextSourcePosition = 0;
      for (var i = 0; i < results.length; i++) {
        result = results[i];
        var matched = String(result[0]);
        var position = max(min(toInteger(result.index), S.length), 0);
        var captures = [];
        // NOTE: This is equivalent to
        //   captures = result.slice(1).map(maybeToString)
        // but for some reason `nativeSlice.call(result, 1, result.length)` (called in
        // the slice polyfill when slicing native arrays) "doesn't work" in safari 9 and
        // causes a crash (https://pastebin.com/N21QzeQA) when trying to debug it.
        for (var j = 1; j < result.length; j++) captures.push(maybeToString(result[j]));
        var namedCaptures = result.groups;
        if (functionalReplace) {
          var replacerArgs = [matched].concat(captures, position, S);
          if (namedCaptures !== undefined) replacerArgs.push(namedCaptures);
          var replacement = String(replaceValue.apply(undefined, replacerArgs));
        } else {
          replacement = getSubstitution(matched, S, position, captures, namedCaptures, replaceValue);
        }
        if (position >= nextSourcePosition) {
          accumulatedResult += S.slice(nextSourcePosition, position) + replacement;
          nextSourcePosition = position + matched.length;
        }
      }
      return accumulatedResult + S.slice(nextSourcePosition);
    }
  ];

    // https://tc39.github.io/ecma262/#sec-getsubstitution
  function getSubstitution(matched, str, position, captures, namedCaptures, replacement) {
    var tailPos = position + matched.length;
    var m = captures.length;
    var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;
    if (namedCaptures !== undefined) {
      namedCaptures = toObject(namedCaptures);
      symbols = SUBSTITUTION_SYMBOLS;
    }
    return $replace.call(replacement, symbols, function (match, ch) {
      var capture;
      switch (ch.charAt(0)) {
        case '$': return '$';
        case '&': return matched;
        case '`': return str.slice(0, position);
        case "'": return str.slice(tailPos);
        case '<':
          capture = namedCaptures[ch.slice(1, -1)];
          break;
        default: // \d\d?
          var n = +ch;
          if (n === 0) return match;
          if (n > m) {
            var f = floor(n / 10);
            if (f === 0) return match;
            if (f <= m) return captures[f - 1] === undefined ? ch.charAt(1) : captures[f - 1] + ch.charAt(1);
            return match;
          }
          capture = captures[n - 1];
      }
      return capture === undefined ? '' : capture;
    });
  }
});


/***/ }),

/***/ "aae3":
/***/ (function(module, exports, __nested_webpack_require_38885__) {

// 7.2.8 IsRegExp(argument)
var isObject = __nested_webpack_require_38885__("d3f4");
var cof = __nested_webpack_require_38885__("2d95");
var MATCH = __nested_webpack_require_38885__("2b4c")('match');
module.exports = function (it) {
  var isRegExp;
  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
};


/***/ }),

/***/ "ac6a":
/***/ (function(module, exports, __nested_webpack_require_39282__) {

var $iterators = __nested_webpack_require_39282__("cadf");
var getKeys = __nested_webpack_require_39282__("0d58");
var redefine = __nested_webpack_require_39282__("2aba");
var global = __nested_webpack_require_39282__("7726");
var hide = __nested_webpack_require_39282__("32e9");
var Iterators = __nested_webpack_require_39282__("84f2");
var wks = __nested_webpack_require_39282__("2b4c");
var ITERATOR = wks('iterator');
var TO_STRING_TAG = wks('toStringTag');
var ArrayValues = Iterators.Array;

var DOMIterables = {
  CSSRuleList: true, // TODO: Not spec compliant, should be false.
  CSSStyleDeclaration: false,
  CSSValueList: false,
  ClientRectList: false,
  DOMRectList: false,
  DOMStringList: false,
  DOMTokenList: true,
  DataTransferItemList: false,
  FileList: false,
  HTMLAllCollection: false,
  HTMLCollection: false,
  HTMLFormElement: false,
  HTMLSelectElement: false,
  MediaList: true, // TODO: Not spec compliant, should be false.
  MimeTypeArray: false,
  NamedNodeMap: false,
  NodeList: true,
  PaintRequestList: false,
  Plugin: false,
  PluginArray: false,
  SVGLengthList: false,
  SVGNumberList: false,
  SVGPathSegList: false,
  SVGPointList: false,
  SVGStringList: false,
  SVGTransformList: false,
  SourceBufferList: false,
  StyleSheetList: true, // TODO: Not spec compliant, should be false.
  TextTrackCueList: false,
  TextTrackList: false,
  TouchList: false
};

for (var collections = getKeys(DOMIterables), i = 0; i < collections.length; i++) {
  var NAME = collections[i];
  var explicit = DOMIterables[NAME];
  var Collection = global[NAME];
  var proto = Collection && Collection.prototype;
  var key;
  if (proto) {
    if (!proto[ITERATOR]) hide(proto, ITERATOR, ArrayValues);
    if (!proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
    Iterators[NAME] = ArrayValues;
    if (explicit) for (key in $iterators) if (!proto[key]) redefine(proto, key, $iterators[key], true);
  }
}


/***/ }),

/***/ "b0c5":
/***/ (function(module, exports, __nested_webpack_require_41209__) {

"use strict";

var regexpExec = __nested_webpack_require_41209__("520a");
__nested_webpack_require_41209__("5ca1")({
  target: 'RegExp',
  proto: true,
  forced: regexpExec !== /./.exec
}, {
  exec: regexpExec
});


/***/ }),

/***/ "be13":
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),

/***/ "c366":
/***/ (function(module, exports, __nested_webpack_require_41706__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __nested_webpack_require_41706__("6821");
var toLength = __nested_webpack_require_41706__("9def");
var toAbsoluteIndex = __nested_webpack_require_41706__("77f1");
module.exports = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIObject($this);
    var length = toLength(O.length);
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare
    if (IS_INCLUDES && el != el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare
      if (value != value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) if (IS_INCLUDES || index in O) {
      if (O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};


/***/ }),

/***/ "c649":
/***/ (function(module, __nested_webpack_exports__, __nested_webpack_require_42729__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __nested_webpack_require_42729__.d(__nested_webpack_exports__, "c", function() { return insertNodeAt; });
/* harmony export (binding) */ __nested_webpack_require_42729__.d(__nested_webpack_exports__, "a", function() { return camelize; });
/* harmony export (binding) */ __nested_webpack_require_42729__.d(__nested_webpack_exports__, "b", function() { return console; });
/* harmony export (binding) */ __nested_webpack_require_42729__.d(__nested_webpack_exports__, "d", function() { return removeNode; });
/* harmony import */ var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__ = __nested_webpack_require_42729__("a481");
/* harmony import */ var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__nested_webpack_require_42729__.n(core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__);


function getConsole() {
  if (typeof window !== "undefined") {
    return window.console;
  }

  return global.console;
}

var console = getConsole();

function cached(fn) {
  var cache = Object.create(null);
  return function cachedFn(str) {
    var hit = cache[str];
    return hit || (cache[str] = fn(str));
  };
}

var regex = /-(\w)/g;
var camelize = cached(function (str) {
  return str.replace(regex, function (_, c) {
    return c ? c.toUpperCase() : "";
  });
});

function removeNode(node) {
  if (node.parentElement !== null) {
    node.parentElement.removeChild(node);
  }
}

function insertNodeAt(fatherNode, node, position) {
  var refNode = position === 0 ? fatherNode.children[0] : fatherNode.children[position - 1].nextSibling;
  fatherNode.insertBefore(node, refNode);
}


/* WEBPACK VAR INJECTION */}.call(this, __nested_webpack_require_42729__("c8ba")))

/***/ }),

/***/ "c69a":
/***/ (function(module, exports, __nested_webpack_require_44512__) {

module.exports = !__nested_webpack_require_44512__("9e1e") && !__nested_webpack_require_44512__("79e5")(function () {
  return Object.defineProperty(__nested_webpack_require_44512__("230e")('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "c8ba":
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "ca5a":
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),

/***/ "cadf":
/***/ (function(module, exports, __nested_webpack_require_45568__) {

"use strict";

var addToUnscopables = __nested_webpack_require_45568__("9c6c");
var step = __nested_webpack_require_45568__("d53b");
var Iterators = __nested_webpack_require_45568__("84f2");
var toIObject = __nested_webpack_require_45568__("6821");

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __nested_webpack_require_45568__("01f9")(Array, 'Array', function (iterated, kind) {
  this._t = toIObject(iterated); // target
  this._i = 0;                   // next index
  this._k = kind;                // kind
// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var kind = this._k;
  var index = this._i++;
  if (!O || index >= O.length) {
    this._t = undefined;
    return step(1);
  }
  if (kind == 'keys') return step(0, index);
  if (kind == 'values') return step(0, O[index]);
  return step(0, [index, O[index]]);
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
Iterators.Arguments = Iterators.Array;

addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');


/***/ }),

/***/ "cb7c":
/***/ (function(module, exports, __nested_webpack_require_46777__) {

var isObject = __nested_webpack_require_46777__("d3f4");
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),

/***/ "ce10":
/***/ (function(module, exports, __nested_webpack_require_47019__) {

var has = __nested_webpack_require_47019__("69a8");
var toIObject = __nested_webpack_require_47019__("6821");
var arrayIndexOf = __nested_webpack_require_47019__("c366")(false);
var IE_PROTO = __nested_webpack_require_47019__("613b")('IE_PROTO');

module.exports = function (object, names) {
  var O = toIObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) if (key != IE_PROTO) has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (has(O, key = names[i++])) {
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};


/***/ }),

/***/ "d2c8":
/***/ (function(module, exports, __nested_webpack_require_47655__) {

// helper for String#{startsWith, endsWith, includes}
var isRegExp = __nested_webpack_require_47655__("aae3");
var defined = __nested_webpack_require_47655__("be13");

module.exports = function (that, searchString, NAME) {
  if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
  return String(defined(that));
};


/***/ }),

/***/ "d3f4":
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ "d53b":
/***/ (function(module, exports) {

module.exports = function (done, value) {
  return { value: value, done: !!done };
};


/***/ }),

/***/ "d8e8":
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),

/***/ "e11e":
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),

/***/ "f559":
/***/ (function(module, exports, __nested_webpack_require_48796__) {

"use strict";
// 21.1.3.18 String.prototype.startsWith(searchString [, position ])

var $export = __nested_webpack_require_48796__("5ca1");
var toLength = __nested_webpack_require_48796__("9def");
var context = __nested_webpack_require_48796__("d2c8");
var STARTS_WITH = 'startsWith';
var $startsWith = ''[STARTS_WITH];

$export($export.P + $export.F * __nested_webpack_require_48796__("5147")(STARTS_WITH), 'String', {
  startsWith: function startsWith(searchString /* , position = 0 */) {
    var that = context(this, searchString, STARTS_WITH);
    var index = toLength(Math.min(arguments.length > 1 ? arguments[1] : undefined, that.length));
    var search = String(searchString);
    return $startsWith
      ? $startsWith.call(that, search, index)
      : that.slice(index, index + search.length) === search;
  }
});


/***/ }),

/***/ "f6fd":
/***/ (function(module, exports) {

// document.currentScript polyfill by Adam Miller

// MIT license

(function(document){
  var currentScript = "currentScript",
      scripts = document.getElementsByTagName('script'); // Live NodeList collection

  // If browser needs currentScript polyfill, add get currentScript() to the document object
  if (!(currentScript in document)) {
    Object.defineProperty(document, currentScript, {
      get: function(){

        // IE 6-10 supports script readyState
        // IE 10+ support stack trace
        try { throw new Error(); }
        catch (err) {

          // Find the second match for the "at" string to get file src url from stack.
          // Specifically works with the format of stack traces in IE.
          var i, res = ((/.*at [^\(]*\((.*):.+:.+\)$/ig).exec(err.stack) || [false])[1];

          // For all scripts on the page, if src matches or if ready state is interactive, return the script tag
          for(i in scripts){
            if(scripts[i].src == res || scripts[i].readyState == "interactive"){
              return scripts[i];
            }
          }

          // If no match, return null
          return null;
        }
      }
    });
  }
})(document);


/***/ }),

/***/ "f751":
/***/ (function(module, exports, __nested_webpack_require_50913__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __nested_webpack_require_50913__("5ca1");

$export($export.S + $export.F, 'Object', { assign: __nested_webpack_require_50913__("7333") });


/***/ }),

/***/ "fa5b":
/***/ (function(module, exports, __nested_webpack_require_51166__) {

module.exports = __nested_webpack_require_51166__("5537")('native-function-to-string', Function.toString);


/***/ }),

/***/ "fab2":
/***/ (function(module, exports, __nested_webpack_require_51344__) {

var document = __nested_webpack_require_51344__("7726").document;
module.exports = document && document.documentElement;


/***/ }),

/***/ "fb15":
/***/ (function(module, __nested_webpack_exports__, __nested_webpack_require_51548__) {

"use strict";
// ESM COMPAT FLAG
__nested_webpack_require_51548__.r(__nested_webpack_exports__);

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  if (true) {
    __nested_webpack_require_51548__("f6fd")
  }

  var setPublicPath_i
  if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __nested_webpack_require_51548__.p = setPublicPath_i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.assign.js
var es6_object_assign = __nested_webpack_require_51548__("f751");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.starts-with.js
var es6_string_starts_with = __nested_webpack_require_51548__("f559");

// EXTERNAL MODULE: ./node_modules/core-js/modules/web.dom.iterable.js
var web_dom_iterable = __nested_webpack_require_51548__("ac6a");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.iterator.js
var es6_array_iterator = __nested_webpack_require_51548__("cadf");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.keys.js
var es6_object_keys = __nested_webpack_require_51548__("456d");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return _arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
}
// EXTERNAL MODULE: ./node_modules/core-js/modules/es7.array.includes.js
var es7_array_includes = __nested_webpack_require_51548__("6762");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.includes.js
var es6_string_includes = __nested_webpack_require_51548__("2fdb");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return _arrayLikeToArray(arr);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
}
// EXTERNAL MODULE: external {"commonjs":"sortablejs","commonjs2":"sortablejs","amd":"sortablejs","root":"Sortable"}
var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_ = __nested_webpack_require_51548__("a352");
var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default = /*#__PURE__*/__nested_webpack_require_51548__.n(external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_);

// EXTERNAL MODULE: ./src/util/helper.js
var helper = __nested_webpack_require_51548__("c649");

// CONCATENATED MODULE: ./src/vuedraggable.js












function buildAttribute(object, propName, value) {
  if (value === undefined) {
    return object;
  }

  object = object || {};
  object[propName] = value;
  return object;
}

function computeVmIndex(vnodes, element) {
  return vnodes.map(function (elt) {
    return elt.elm;
  }).indexOf(element);
}

function _computeIndexes(slots, children, isTransition, footerOffset) {
  if (!slots) {
    return [];
  }

  var elmFromNodes = slots.map(function (elt) {
    return elt.elm;
  });
  var footerIndex = children.length - footerOffset;

  var rawIndexes = _toConsumableArray(children).map(function (elt, idx) {
    return idx >= footerIndex ? elmFromNodes.length : elmFromNodes.indexOf(elt);
  });

  return isTransition ? rawIndexes.filter(function (ind) {
    return ind !== -1;
  }) : rawIndexes;
}

function emit(evtName, evtData) {
  var _this = this;

  this.$nextTick(function () {
    return _this.$emit(evtName.toLowerCase(), evtData);
  });
}

function delegateAndEmit(evtName) {
  var _this2 = this;

  return function (evtData) {
    if (_this2.realList !== null) {
      _this2["onDrag" + evtName](evtData);
    }

    emit.call(_this2, evtName, evtData);
  };
}

function isTransitionName(name) {
  return ["transition-group", "TransitionGroup"].includes(name);
}

function vuedraggable_isTransition(slots) {
  if (!slots || slots.length !== 1) {
    return false;
  }

  var _slots = _slicedToArray(slots, 1),
      componentOptions = _slots[0].componentOptions;

  if (!componentOptions) {
    return false;
  }

  return isTransitionName(componentOptions.tag);
}

function getSlot(slot, scopedSlot, key) {
  return slot[key] || (scopedSlot[key] ? scopedSlot[key]() : undefined);
}

function computeChildrenAndOffsets(children, slot, scopedSlot) {
  var headerOffset = 0;
  var footerOffset = 0;
  var header = getSlot(slot, scopedSlot, "header");

  if (header) {
    headerOffset = header.length;
    children = children ? [].concat(_toConsumableArray(header), _toConsumableArray(children)) : _toConsumableArray(header);
  }

  var footer = getSlot(slot, scopedSlot, "footer");

  if (footer) {
    footerOffset = footer.length;
    children = children ? [].concat(_toConsumableArray(children), _toConsumableArray(footer)) : _toConsumableArray(footer);
  }

  return {
    children: children,
    headerOffset: headerOffset,
    footerOffset: footerOffset
  };
}

function getComponentAttributes($attrs, componentData) {
  var attributes = null;

  var update = function update(name, value) {
    attributes = buildAttribute(attributes, name, value);
  };

  var attrs = Object.keys($attrs).filter(function (key) {
    return key === "id" || key.startsWith("data-");
  }).reduce(function (res, key) {
    res[key] = $attrs[key];
    return res;
  }, {});
  update("attrs", attrs);

  if (!componentData) {
    return attributes;
  }

  var on = componentData.on,
      props = componentData.props,
      componentDataAttrs = componentData.attrs;
  update("on", on);
  update("props", props);
  Object.assign(attributes.attrs, componentDataAttrs);
  return attributes;
}

var eventsListened = ["Start", "Add", "Remove", "Update", "End"];
var eventsToEmit = ["Choose", "Unchoose", "Sort", "Filter", "Clone"];
var readonlyProperties = ["Move"].concat(eventsListened, eventsToEmit).map(function (evt) {
  return "on" + evt;
});
var draggingElement = null;
var props = {
  options: Object,
  list: {
    type: Array,
    required: false,
    default: null
  },
  value: {
    type: Array,
    required: false,
    default: null
  },
  noTransitionOnDrag: {
    type: Boolean,
    default: false
  },
  clone: {
    type: Function,
    default: function _default(original) {
      return original;
    }
  },
  element: {
    type: String,
    default: "div"
  },
  tag: {
    type: String,
    default: null
  },
  move: {
    type: Function,
    default: null
  },
  componentData: {
    type: Object,
    required: false,
    default: null
  }
};
var draggableComponent = {
  name: "draggable",
  inheritAttrs: false,
  props: props,
  data: function data() {
    return {
      transitionMode: false,
      noneFunctionalComponentMode: false
    };
  },
  render: function render(h) {
    var slots = this.$slots.default;
    this.transitionMode = vuedraggable_isTransition(slots);

    var _computeChildrenAndOf = computeChildrenAndOffsets(slots, this.$slots, this.$scopedSlots),
        children = _computeChildrenAndOf.children,
        headerOffset = _computeChildrenAndOf.headerOffset,
        footerOffset = _computeChildrenAndOf.footerOffset;

    this.headerOffset = headerOffset;
    this.footerOffset = footerOffset;
    var attributes = getComponentAttributes(this.$attrs, this.componentData);
    return h(this.getTag(), attributes, children);
  },
  created: function created() {
    if (this.list !== null && this.value !== null) {
      helper["b" /* console */].error("Value and list props are mutually exclusive! Please set one or another.");
    }

    if (this.element !== "div") {
      helper["b" /* console */].warn("Element props is deprecated please use tag props instead. See https://github.com/SortableJS/Vue.Draggable/blob/master/documentation/migrate.md#element-props");
    }

    if (this.options !== undefined) {
      helper["b" /* console */].warn("Options props is deprecated, add sortable options directly as vue.draggable item, or use v-bind. See https://github.com/SortableJS/Vue.Draggable/blob/master/documentation/migrate.md#options-props");
    }
  },
  mounted: function mounted() {
    var _this3 = this;

    this.noneFunctionalComponentMode = this.getTag().toLowerCase() !== this.$el.nodeName.toLowerCase() && !this.getIsFunctional();

    if (this.noneFunctionalComponentMode && this.transitionMode) {
      throw new Error("Transition-group inside component is not supported. Please alter tag value or remove transition-group. Current tag value: ".concat(this.getTag()));
    }

    var optionsAdded = {};
    eventsListened.forEach(function (elt) {
      optionsAdded["on" + elt] = delegateAndEmit.call(_this3, elt);
    });
    eventsToEmit.forEach(function (elt) {
      optionsAdded["on" + elt] = emit.bind(_this3, elt);
    });
    var attributes = Object.keys(this.$attrs).reduce(function (res, key) {
      res[Object(helper["a" /* camelize */])(key)] = _this3.$attrs[key];
      return res;
    }, {});
    var options = Object.assign({}, this.options, attributes, optionsAdded, {
      onMove: function onMove(evt, originalEvent) {
        return _this3.onDragMove(evt, originalEvent);
      }
    });
    !("draggable" in options) && (options.draggable = ">*");
    this._sortable = new external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default.a(this.rootContainer, options);
    this.computeIndexes();
  },
  beforeDestroy: function beforeDestroy() {
    if (this._sortable !== undefined) this._sortable.destroy();
  },
  computed: {
    rootContainer: function rootContainer() {
      return this.transitionMode ? this.$el.children[0] : this.$el;
    },
    realList: function realList() {
      return this.list ? this.list : this.value;
    }
  },
  watch: {
    options: {
      handler: function handler(newOptionValue) {
        this.updateOptions(newOptionValue);
      },
      deep: true
    },
    $attrs: {
      handler: function handler(newOptionValue) {
        this.updateOptions(newOptionValue);
      },
      deep: true
    },
    realList: function realList() {
      this.computeIndexes();
    }
  },
  methods: {
    getIsFunctional: function getIsFunctional() {
      var fnOptions = this._vnode.fnOptions;
      return fnOptions && fnOptions.functional;
    },
    getTag: function getTag() {
      return this.tag || this.element;
    },
    updateOptions: function updateOptions(newOptionValue) {
      for (var property in newOptionValue) {
        var value = Object(helper["a" /* camelize */])(property);

        if (readonlyProperties.indexOf(value) === -1) {
          this._sortable.option(value, newOptionValue[property]);
        }
      }
    },
    getChildrenNodes: function getChildrenNodes() {
      if (this.noneFunctionalComponentMode) {
        return this.$children[0].$slots.default;
      }

      var rawNodes = this.$slots.default;
      return this.transitionMode ? rawNodes[0].child.$slots.default : rawNodes;
    },
    computeIndexes: function computeIndexes() {
      var _this4 = this;

      this.$nextTick(function () {
        _this4.visibleIndexes = _computeIndexes(_this4.getChildrenNodes(), _this4.rootContainer.children, _this4.transitionMode, _this4.footerOffset);
      });
    },
    getUnderlyingVm: function getUnderlyingVm(htmlElt) {
      var index = computeVmIndex(this.getChildrenNodes() || [], htmlElt);

      if (index === -1) {
        //Edge case during move callback: related element might be
        //an element different from collection
        return null;
      }

      var element = this.realList[index];
      return {
        index: index,
        element: element
      };
    },
    getUnderlyingPotencialDraggableComponent: function getUnderlyingPotencialDraggableComponent(_ref) {
      var vue = _ref.__vue__;

      if (!vue || !vue.$options || !isTransitionName(vue.$options._componentTag)) {
        if (!("realList" in vue) && vue.$children.length === 1 && "realList" in vue.$children[0]) return vue.$children[0];
        return vue;
      }

      return vue.$parent;
    },
    emitChanges: function emitChanges(evt) {
      var _this5 = this;

      this.$nextTick(function () {
        _this5.$emit("change", evt);
      });
    },
    alterList: function alterList(onList) {
      if (this.list) {
        onList(this.list);
        return;
      }

      var newList = _toConsumableArray(this.value);

      onList(newList);
      this.$emit("input", newList);
    },
    spliceList: function spliceList() {
      var _arguments = arguments;

      var spliceList = function spliceList(list) {
        return list.splice.apply(list, _toConsumableArray(_arguments));
      };

      this.alterList(spliceList);
    },
    updatePosition: function updatePosition(oldIndex, newIndex) {
      var updatePosition = function updatePosition(list) {
        return list.splice(newIndex, 0, list.splice(oldIndex, 1)[0]);
      };

      this.alterList(updatePosition);
    },
    getRelatedContextFromMoveEvent: function getRelatedContextFromMoveEvent(_ref2) {
      var to = _ref2.to,
          related = _ref2.related;
      var component = this.getUnderlyingPotencialDraggableComponent(to);

      if (!component) {
        return {
          component: component
        };
      }

      var list = component.realList;
      var context = {
        list: list,
        component: component
      };

      if (to !== related && list && component.getUnderlyingVm) {
        var destination = component.getUnderlyingVm(related);

        if (destination) {
          return Object.assign(destination, context);
        }
      }

      return context;
    },
    getVmIndex: function getVmIndex(domIndex) {
      var indexes = this.visibleIndexes;
      var numberIndexes = indexes.length;
      return domIndex > numberIndexes - 1 ? numberIndexes : indexes[domIndex];
    },
    getComponent: function getComponent() {
      return this.$slots.default[0].componentInstance;
    },
    resetTransitionData: function resetTransitionData(index) {
      if (!this.noTransitionOnDrag || !this.transitionMode) {
        return;
      }

      var nodes = this.getChildrenNodes();
      nodes[index].data = null;
      var transitionContainer = this.getComponent();
      transitionContainer.children = [];
      transitionContainer.kept = undefined;
    },
    onDragStart: function onDragStart(evt) {
      this.context = this.getUnderlyingVm(evt.item);
      evt.item._underlying_vm_ = this.clone(this.context.element);
      draggingElement = evt.item;
    },
    onDragAdd: function onDragAdd(evt) {
      var element = evt.item._underlying_vm_;

      if (element === undefined) {
        return;
      }

      Object(helper["d" /* removeNode */])(evt.item);
      var newIndex = this.getVmIndex(evt.newIndex);
      this.spliceList(newIndex, 0, element);
      this.computeIndexes();
      var added = {
        element: element,
        newIndex: newIndex
      };
      this.emitChanges({
        added: added
      });
    },
    onDragRemove: function onDragRemove(evt) {
      Object(helper["c" /* insertNodeAt */])(this.rootContainer, evt.item, evt.oldIndex);

      if (evt.pullMode === "clone") {
        Object(helper["d" /* removeNode */])(evt.clone);
        return;
      }

      var oldIndex = this.context.index;
      this.spliceList(oldIndex, 1);
      var removed = {
        element: this.context.element,
        oldIndex: oldIndex
      };
      this.resetTransitionData(oldIndex);
      this.emitChanges({
        removed: removed
      });
    },
    onDragUpdate: function onDragUpdate(evt) {
      Object(helper["d" /* removeNode */])(evt.item);
      Object(helper["c" /* insertNodeAt */])(evt.from, evt.item, evt.oldIndex);
      var oldIndex = this.context.index;
      var newIndex = this.getVmIndex(evt.newIndex);
      this.updatePosition(oldIndex, newIndex);
      var moved = {
        element: this.context.element,
        oldIndex: oldIndex,
        newIndex: newIndex
      };
      this.emitChanges({
        moved: moved
      });
    },
    updateProperty: function updateProperty(evt, propertyName) {
      evt.hasOwnProperty(propertyName) && (evt[propertyName] += this.headerOffset);
    },
    computeFutureIndex: function computeFutureIndex(relatedContext, evt) {
      if (!relatedContext.element) {
        return 0;
      }

      var domChildren = _toConsumableArray(evt.to.children).filter(function (el) {
        return el.style["display"] !== "none";
      });

      var currentDOMIndex = domChildren.indexOf(evt.related);
      var currentIndex = relatedContext.component.getVmIndex(currentDOMIndex);
      var draggedInList = domChildren.indexOf(draggingElement) !== -1;
      return draggedInList || !evt.willInsertAfter ? currentIndex : currentIndex + 1;
    },
    onDragMove: function onDragMove(evt, originalEvent) {
      var onMove = this.move;

      if (!onMove || !this.realList) {
        return true;
      }

      var relatedContext = this.getRelatedContextFromMoveEvent(evt);
      var draggedContext = this.context;
      var futureIndex = this.computeFutureIndex(relatedContext, evt);
      Object.assign(draggedContext, {
        futureIndex: futureIndex
      });
      var sendEvt = Object.assign({}, evt, {
        relatedContext: relatedContext,
        draggedContext: draggedContext
      });
      return onMove(sendEvt, originalEvent);
    },
    onDragEnd: function onDragEnd() {
      this.computeIndexes();
      draggingElement = null;
    }
  }
};

if (typeof window !== "undefined" && "Vue" in window) {
  window.Vue.component("draggable", draggableComponent);
}

/* harmony default export */ var vuedraggable = (draggableComponent);
// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ var entry_lib = __nested_webpack_exports__["default"] = (vuedraggable);



/***/ })

/******/ })["default"];
});
//# sourceMappingURL=vuedraggable.umd.js.map

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
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/App.vue?vue&type=template&id=09bcfe23
var render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{attrs:{"id":"app"}},[_c('Main',{attrs:{"api-config":_vm.apiConfig}}),(_vm.debugServerResponse)?_c('div',{attrs:{"id":"server-response"}}):_vm._e()],1)
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
















;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Main.vue?vue&type=template&id=76e65df4&scoped=true
var Mainvue_type_template_id_76e65df4_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"u-contents"},[(_vm.gradeBook)?_c('div',{attrs:{"aria-hidden":(_vm.itemSettings !== null || !!_vm.selectedCategory || !!_vm.errorData)}},[_c('div',{staticClass:"u-flex u-flex-wrap gradebook-toolbar"},[_c('div',{staticClass:"input-group"},[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.searchTerm),expression:"searchTerm"}],staticClass:"form-control",attrs:{"type":"text","placeholder":_vm.$t('find-student')},domProps:{"value":(_vm.searchTerm)},on:{"input":function($event){if($event.target.composing)return;_vm.searchTerm=$event.target.value}}}),_c('div',{staticClass:"input-group-append"},[_c('button',{staticClass:"btn btn-default",attrs:{"name":"clear","value":"clear"},on:{"click":function($event){_vm.searchTerm = ''}}},[_c('span',{staticClass:"glyphicon glyphicon-remove",attrs:{"aria-hidden":"true"}})])])]),_c('grades-dropdown',{attrs:{"id":"dropdown-main","graded-items":_vm.gradeBook.statusGradedItems},on:{"toggle":_vm.toggleGradeItem}}),_c('div',{staticClass:"u-flex u-justify-content-end u-gap-small u-ml-auto gradebook-create-actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.synchronizeGradeBook}},[_c('i',{staticClass:"fa fa-refresh",attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('synchronize-scores')))]),_c('div',{staticClass:"btn-group"},[_c('a',{staticClass:"btn btn-default btn-sm dropdown-toggle",attrs:{"data-toggle":"dropdown","aria-haspopup":"true"}},[_c('i',{staticClass:"fa fa-plus",attrs:{"aria-hidden":"true"}}),_c('span',[_vm._v(_vm._s(_vm.$t('new')))]),_vm._v(" "),_c('span',{staticClass:"caret",attrs:{"aria-hidden":"true"}})]),_c('ul',{staticClass:"dropdown-menu",attrs:{"role":"menu"}},[_c('li',{staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{staticClass:"dropdown-item",attrs:{"role":"menuitem"},on:{"click":function($event){$event.preventDefault();return _vm.createNewScore.apply(null, arguments)}}},[_vm._v(_vm._s(_vm.$t('new-score')))])]),_c('li',{staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{staticClass:"dropdown-item",attrs:{"role":"menuitem"},on:{"click":function($event){$event.preventDefault();return _vm.createNewCategory.apply(null, arguments)}}},[_vm._v(_vm._s(_vm.$t('new-category')))])]),_c('li',{staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{staticClass:"dropdown-item",attrs:{"role":"menuitem","href":_vm.apiConfig.gradeBookImportCsvURL}},[_vm._v(_vm._s(_vm.$t('import'))+"…")])])])]),(_vm.gradeBook.totalsNeedUpdating)?_c('button',{staticClass:"btn btn-update-totals btn-primary btn-sm u-font-medium u-text-upper",on:{"click":_vm.updateTotalScores}},[_c('i',{staticClass:"fa fa-exclamation-circle",attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('update-final-scores'))+" ")]):_vm._e(),_c('button',{staticClass:"btn btn-default btn-sm",attrs:{"disabled":_vm.gradeBook.totalsNeedUpdating,"title":_vm.gradeBook.totalsNeedUpdating && _vm.$t('update-final-scores-before-exporting')},on:{"click":_vm.exportGradeBook}},[_vm._v(_vm._s(_vm.$t('export')))]),_c('div',{staticClass:"btn-group"},[_c('a',{staticClass:"btn btn-default btn-sm dropdown-toggle",attrs:{"data-toggle":"dropdown","aria-haspopup":"true","title":`${_vm.$t('show')} ${_vm.itemsPerPage} items`}},[_c('span',[_vm._v(_vm._s(_vm.$t('show'))+" "+_vm._s(_vm.itemsPerPage)+" items")]),_vm._v(" "),_c('span',{staticClass:"caret",attrs:{"aria-hidden":"true"}})]),_c('ul',{staticClass:"dropdown-menu dropdown-menu-right",attrs:{"role":"listbox"}},_vm._l(([5, 10, 15, 20, 50]),function(count){return _c('li',{key:'per-page-' + count,staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{staticClass:"dropdown-item",class:_vm.itemsPerPage === count ? 'selected' : 'not-selected',attrs:{"role":"option","aria-selected":_vm.itemsPerPage === count ? 'true' : 'false'},on:{"click":function($event){return _vm.setItemsPerPage(count)}}},[_c('span',[_vm._v(_vm._s(_vm.$t('show'))+" "+_vm._s(count)+" items")])])])}),0)])])],1),_c('div',{staticClass:"gradebook-table-container"},[_c('grades-table',{attrs:{"grade-book":_vm.gradeBook,"search-terms":_vm.studentSearchTerms,"busy":_vm.tableBusy,"add-column-id":_vm.addColumnId,"save-column-id":_vm.saveColumnId,"save-category-id":_vm.saveCategoryId,"save-display-total":_vm.saveDisplayTotal,"items-per-page":_vm.itemsPerPage,"grade-book-root-url":_vm.apiConfig.gradeBookRootURL},on:{"item-settings":function($event){_vm.itemSettings = $event},"category-settings":function($event){_vm.categorySettings = $event},"final-score-settings":function($event){_vm.showFinalScoreSettings = true},"update-score-comment":_vm.onUpdateScoreComment,"overwrite-result":_vm.onOverwriteResult,"revert-overwritten-result":_vm.onRevertOverwrittenResult,"change-category":_vm.onChangeCategory,"move-category":_vm.onMoveCategory,"change-gradecolumn":_vm.onChangeGradeColumn,"change-gradecolumn-category":_vm.onChangeGradeColumnCategory,"move-gradecolumn":_vm.onMoveGradeColumn,"change-display-total":_vm.onChangeDisplayTotal}})],1)]):_c('div',{staticClass:"lds-ellipsis",attrs:{"aria-hidden":"true"}},[_c('div'),_c('div'),_c('div'),_c('div')]),(_vm.itemSettings !== null)?_c('item-settings',{attrs:{"grade-book":_vm.gradeBook,"column-id":_vm.itemSettings},on:{"close":function($event){_vm.itemSettings = null},"item-settings":function($event){_vm.itemSettings = $event},"change-gradecolumn":_vm.onChangeGradeColumn,"add-subitem":_vm.onAddSubItem,"remove-subitem":_vm.onRemoveSubItem,"remove-column":_vm.onRemoveColumn}}):_vm._e(),(_vm.selectedCategory)?_c('category-settings',{attrs:{"grade-book":_vm.gradeBook,"category":_vm.selectedCategory},on:{"close":_vm.closeSelectedCategory,"change-category":_vm.onChangeCategory,"remove-category":_vm.onRemoveCategory}}):_vm._e(),(_vm.showFinalScoreSettings)?_c('final-score-settings',{attrs:{"grade-book":_vm.gradeBook},on:{"close":function($event){_vm.showFinalScoreSettings = false},"change-display-total":_vm.onChangeDisplayTotal}}):_vm._e(),(_vm.errorData)?_c('error-display',{on:{"close":_vm.closeErrorDisplay}},[_vm._v(_vm._s(_vm.$t(`error-${_vm.errorData.type}`)))]):_vm._e()],1)
}
var Mainvue_type_template_id_76e65df4_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesDropdown.vue?vue&type=template&id=4d8dfb0f&scoped=true
var GradesDropdownvue_type_template_id_4d8dfb0f_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{directives:[{name:"clickoutside",rawName:"v-clickoutside",value:(_vm.close),expression:"close"}],staticClass:"btn-group",attrs:{"id":_vm.id}},[_c('button',{staticClass:"u-flex u-align-items-center u-justify-content-between btn dropdown-toggle",attrs:{"aria-haspopup":"true","aria-expanded":_vm.isOpen,"title":_vm.$t('add-remove-scores')},on:{"click":function($event){_vm.isOpen = !_vm.isOpen}}},[_c('span',[_vm._v(_vm._s(_vm.$t('add-remove-scores')))]),_vm._v(" "),_c('span',{staticClass:"caret",attrs:{"aria-hidden":"true"}})]),_c('ul',{staticClass:"dropdown-menu",class:{'show': _vm.isOpen}},_vm._l((_vm.gradedItems),function(item,index){return _c('li',{key:`item-${index}`,attrs:{"role":"presentation"},on:{"click":function($event){$event.stopPropagation();}}},[_c('a',{staticClass:"dropdown-item",class:{'mod-removed': item.removed, 'mod-checked': item.checked},attrs:{"role":"menuitem","href":"#","target":"_self"}},[_c('b-form-checkbox',{class:{'is-disabled': item.disabled},attrs:{"id":`${_vm.id}-item-${index}`,"checked":item.checked,"disabled":item.disabled || (item.removed && !item.checked)},on:{"change":function($event){return _vm.toggleItem(item, index)}}},[_vm._v(" "+_vm._s(item.title)+" "),_c('div',{staticClass:"score-breadcrumb-trail"},[_vm._v(_vm._s(_vm._f("breadcrumb")(item)))])])],1)])}),0),(_vm.gradeItemToRemove)?_c('div',{staticClass:"modal-wrapper",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center modal-content"},[_c('div',{staticClass:"modal-content-title"},[_vm._v(_vm._s(_vm.$t('remove-from-overview', {title: _vm.gradeItemToRemove.title})))]),_c('div',{staticClass:"u-flex actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.remove}},[_vm._v(_vm._s(_vm.$t('remove')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])]):_vm._e()])
}
var GradesDropdownvue_type_template_id_4d8dfb0f_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesDropdown.vue?vue&type=script&lang=ts


external_commonjs_vue_commonjs2_vue_root_Vue_default().directive('clickoutside', {
    inserted: (el, binding, vnode) => {
        el.clickOutsideEvent = function (event) {
            // here we check if the click event is outside the element and it's children
            if (!(el == event.target || el.contains(event.target))) {
                // if clicked outside, call the provided method
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
        document.body.addEventListener('touchstart', el.clickOutsideEvent);
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
        document.body.removeEventListener('touchstart', el.clickOutsideEvent);
    }
});
let GradesDropdown = class GradesDropdown extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isOpen = false;
    gradeItemToRemove = null;
    id;
    gradedItems;
    // eslint-disable-next-line no-unused-vars
    toggleItem(item, index) {
        if (item.checked) {
            this.gradeItemToRemove = item;
            return;
        }
        this.$emit('toggle', item, !item.checked);
    }
    open() {
        this.isOpen = true;
    }
    close() {
        this.isOpen = false;
    }
    cancel() {
        if (this.gradeItemToRemove) {
            const index = this.gradedItems.indexOf(this.gradeItemToRemove);
            if (index !== -1) {
                this.$nextTick(() => document.querySelector(`#${this.id}-item-${index}`).checked = true);
            }
        }
        this.gradeItemToRemove = null;
    }
    remove() {
        if (this.gradeItemToRemove) {
            this.$emit('toggle', this.gradeItemToRemove, false);
        }
        this.gradeItemToRemove = null;
    }
};
__decorate([
    Prop({ type: String, default: '' })
], GradesDropdown.prototype, "id", void 0);
__decorate([
    Prop({ type: Array, required: true })
], GradesDropdown.prototype, "gradedItems", void 0);
GradesDropdown = __decorate([
    vue_class_component_esm({
        filters: {
            breadcrumb: function (gradedItem) {
                return gradedItem.breadcrumb.join(' » ');
            }
        }
    })
], GradesDropdown);
/* harmony default export */ const GradesDropdownvue_type_script_lang_ts = (GradesDropdown);

;// CONCATENATED MODULE: ./src/components/GradesDropdown.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_GradesDropdownvue_type_script_lang_ts = (GradesDropdownvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesDropdown.vue?vue&type=style&index=0&id=4d8dfb0f&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/GradesDropdown.vue?vue&type=style&index=0&id=4d8dfb0f&prod&scoped=true&lang=css

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

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesDropdown.vue?vue&type=custom&index=0&blockType=i18n
var GradesDropdownvue_type_custom_index_0_blockType_i18n = __webpack_require__(218);
var GradesDropdownvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(GradesDropdownvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/GradesDropdown.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_GradesDropdownvue_type_custom_index_0_blockType_i18n = ((GradesDropdownvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/GradesDropdown.vue



;


/* normalize component */

var component = normalizeComponent(
  components_GradesDropdownvue_type_script_lang_ts,
  GradesDropdownvue_type_template_id_4d8dfb0f_scoped_true_render,
  GradesDropdownvue_type_template_id_4d8dfb0f_scoped_true_staticRenderFns,
  false,
  null,
  "4d8dfb0f",
  null
  
)

/* custom blocks */
;
if (typeof components_GradesDropdownvue_type_custom_index_0_blockType_i18n === 'function') components_GradesDropdownvue_type_custom_index_0_blockType_i18n(component)

/* harmony default export */ const components_GradesDropdown = (component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesTable.vue?vue&type=template&id=a94e1dae&scoped=true
var GradesTablevue_type_template_id_a94e1dae_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('div',{staticClass:"table-wrap u-relative"},[_c('table',{staticClass:"gradebook-table",class:{'is-dragging': _vm.isDraggingColumn, 'is-category-drop': _vm.categoryDropArea !== null },attrs:{"id":"gradebook-table","aria-busy":_vm.busy}},[_c('thead',[(_vm.gradeBook.categories.length)?_c('tr',{staticClass:"table-row table-head-row table-categories-row"},[_c('th',{staticClass:"col-sticky table-student"}),_c('draggable',{staticClass:"u-contents",attrs:{"list":_vm.gradeBook.categories,"tag":"div","disabled":_vm.catEditItemId !== null},on:{"end":_vm.onDragEnd}},_vm._l((_vm.gradeBook.categories),function({id, title, color, columnIds}){return _c('th',{key:`category-${id}`,staticClass:"category u-relative u-font-medium",class:{'is-droppable': _vm.categoryDropArea === id},style:(`--color: ${color};`),attrs:{"draggable":"","colspan":Math.max(columnIds.length, 1)},on:{"dragstart":function($event){return _vm.startDragCategory($event, id)},"dragover":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, id)},"dragenter":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, id)},"dragleave":function($event){_vm.categoryDropArea = null},"drop":function($event){(_vm.isDraggingColumn || _vm.isDraggingCategory) && _vm.onDrop($event, id)}}},[(_vm.catEditItemId === id)?_c('item-title-input',{staticClass:"item-title-input",attrs:{"item-title":title},on:{"cancel":function($event){_vm.catEditItemId = null},"ok":function($event){return _vm.setCategoryTitle(id, $event)}}}):(id !== 0)?_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-between u-cursor-pointer",attrs:{"title":_vm.$t('adjust-title')},on:{"dblclick":function($event){return _vm.showCategoryTitleDialog(id)}}},[_vm._v(_vm._s(title)+" "),(_vm.isSavingCategoryWithId(id))?_c('div',{staticClass:"spin",attrs:{"role":"status","aria-busy":"true","aria-label":_vm.$t('saving')}},[_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin",attrs:{"aria-hidden":"true"}})]):_vm._e(),_c('button',{staticClass:"btn-settings",attrs:{"title":_vm.$t('category-settings')},on:{"click":function($event){return _vm.showCategorySettings(id)}}},[_c('i',{staticClass:"fa fa-gear u-inline-block",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('category-settings')))])])]):_vm._e()],1)}),0),(_vm.showNullCategory)?_c('th',{staticClass:"mod-no-category-assigned",class:{'is-droppable': _vm.categoryDropArea === 0},attrs:{"colspan":Math.max(_vm.gradeBook.nullCategory.columnIds.length, 1),"title":_vm.$t('without-category')},on:{"dragover":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, 0)},"dragenter":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, 0)},"dragleave":function($event){_vm.categoryDropArea = null},"drop":function($event){(_vm.isDraggingColumn || _vm.isDraggingCategory) && _vm.onDrop($event, 0)}}}):_vm._e(),_c('th',{staticClass:"col-sticky table-student-total"})],1):_vm._e(),_c('tr',{staticClass:"table-row table-head-row table-scores-row"},[_c('th',{staticClass:"col-sticky table-student"},[_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('lastname')},on:{"click":function($event){return _vm.sortByNameField('lastname')}}},[_vm._v(_vm._s(_vm.$t('last-name')))]),_vm._v(" "),_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('firstname')},on:{"click":function($event){return _vm.sortByNameField('firstname')}}},[_vm._v(_vm._s(_vm.$t('first-name')))])]),_vm._l((_vm.displayedCategories),function(category){return _c('draggable',{key:`category-score-${category.id}`,staticClass:"u-contents",attrs:{"list":category.columnIds,"tag":"div","ghost-class":"ghost","disabled":_vm.editItemId !== null || _vm.weightEditItemId !== null},on:{"end":_vm.onDragEnd}},[(category.columnIds.length === 0)?_c('th',{key:`item-id-${category.id}`}):_vm._l((_vm.getColumns(category)),function(column){return _c('th',{key:`item-id-${category.id}--${column.id}-name`,class:{'unreleased-score-cell': !column.released, 'uncounted-score-cell': !column.countsForEndResult, 'u-relative': column.isEditing},attrs:{"draggable":""},on:{"dragstart":function($event){return _vm.startDragColumn($event, column.id)},"drop":function($event){(_vm.isDraggingColumn || _vm.isDraggingCategory) && _vm.onDrop($event, -1)}}},[(column.isEditingTitle)?_c('item-title-input',{staticClass:"item-title-input",attrs:{"item-title":column.title},on:{"cancel":function($event){_vm.editItemId = null},"ok":function($event){return _vm.setTitle(column.id, $event)}}}):(column.isEditingWeight)?[_c('span',{staticClass:"column-title"},[(column.isGrouped)?_c('i',{staticClass:"fa fa-group",attrs:{"aria-hidden":"true"}}):_vm._e(),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('grouped-score')))]),_vm._v(_vm._s(column.title))]),_c('weight-input',{staticClass:"m-dialog",attrs:{"item-weight":column.weight},on:{"cancel":function($event){_vm.weightEditItemId = null},"ok":function($event){return _vm.setWeight(column.id, $event)}}})]:[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-between u-cursor-pointer",attrs:{"title":_vm.$t('adjust-title')},on:{"dblclick":function($event){return _vm.showColumnTitleDialog(column.id)}}},[_c('span',{staticClass:"column-title",attrs:{"id":`${column.id}-title`}},[(column.isGrouped)?_c('i',{staticClass:"fa fa-group",attrs:{"aria-hidden":"true"}}):_vm._e(),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('grouped-score')))]),_vm._v(_vm._s(column.title)+" "),(column.hasRemovedSourceData)?_c('i',{staticClass:"fa fa-exclamation-circle",attrs:{"aria-hidden":"true"}}):_vm._e()]),(column.hasRemovedSourceData)?_c('b-popover',{attrs:{"target":`${column.id}-title`,"triggers":"hover","placement":"bottom"}},[_c('p',{staticClass:"source-results-warning"},[_vm._v(_vm._s(_vm.$t('source-results-warning')))])]):_vm._e(),_c('button',{staticClass:"btn-settings",attrs:{"title":_vm.$t('item-settings')},on:{"click":function($event){return _vm.showColumnSettings(column.id)}}},[_c('i',{staticClass:"fa fa-gear u-inline-block",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('item-settings')))])])],1),_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-between"},[(column.countsForEndResult)?_c('div',{staticClass:"weight u-font-normal u-cursor-pointer",class:{'mod-custom': column.hasWeightSet , 'is-error': _vm.gradeBook.eqRestWeight < 0},attrs:{"title":_vm.$t('adjust-weight')},on:{"dblclick":function($event){return _vm.showColumnWeightDialog(column.id)}}},[_vm._v(_vm._s(_vm._f("formatNum")(column.weight))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]):_c('div',{staticClass:"weight u-font-normal u-font-italic",attrs:{"title":_vm.$t('count-towards-endresult-not')}},[_c('span',{attrs:{"aria-hidden":"true"}},[_vm._v(_vm._s(_vm.$t('uncounted')))]),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),(!column.isSaving)?_c('button',{staticClass:"btn-released u-ml-auto",attrs:{"title":column.released ? _vm.$t('make-invisible') : _vm.$t('make-visible')},on:{"click":function($event){return _vm.toggleVisibility(column.id)}}},[_c('i',{staticClass:"fa",class:{'fa-eye': column.released, 'fa-eye-slash': !column.released},attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(column.released ? _vm.$t('make-invisible') : _vm.$t('make-visible')))])]):_vm._e(),_c('div',{staticClass:"spin",attrs:{"role":"status","aria-busy":column.isSaving,"aria-label":_vm.$t('saving')}},[(column.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin",attrs:{"aria-hidden":"true"}}):_vm._e()])])]],2)})],2)}),_c('th',{staticClass:"col-sticky table-student-total",class:{'unreleased-score-cell': _vm.gradeBook.hasUnreleasedScores, 'u-text-end': !_vm.editDisplayTotalDialog}},[(_vm.editDisplayTotalDialog)?[_c('div',[_vm._v(_vm._s(_vm.$t('final-score')))]),_c('display-total-input',{staticClass:"m-dialog",attrs:{"display-total":_vm.gradeBook.getDisplayTotal()},on:{"cancel":function($event){_vm.editDisplayTotalDialog = false},"ok":function($event){return _vm.setDisplayTotal($event)}}})]:[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end"},[_c('div',[_vm._v(_vm._s(_vm.$t('final-score')))]),_c('button',{staticClass:"btn-settings",attrs:{"title":_vm.$t('final-score-settings')},on:{"click":_vm.showFinalScoreSettings}},[_c('i',{staticClass:"fa fa-gear u-inline-block",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('final-score-settings')))])])]),_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end u-gap-small-2x"},[_c('div',{staticClass:"weight u-font-normal u-cursor-pointer",staticStyle:{"width":"40px"},on:{"dblclick":_vm.showFinalScoreDialog}},[(_vm.gradeBook.getDisplayTotal() === 100)?[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]:[_vm._v("/ "+_vm._s(_vm.gradeBook.getDisplayTotal()))]],2),_c('div',[(!_vm.saveDisplayTotal)?_c('div',{staticClass:"final-score-released",attrs:{"title":_vm.gradeBook.hasUnreleasedScores ? _vm.$t('invisible') : _vm.$t('visible')}},[_c('i',{staticClass:"fa",class:{'fa-eye': !_vm.gradeBook.hasUnreleasedScores, 'fa-eye-slash': _vm.gradeBook.hasUnreleasedScores},attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.gradeBook.hasUnreleasedScores ? _vm.$t('invisible') : _vm.$t('visible')))])]):_vm._e(),_c('div',{staticClass:"spin",attrs:{"role":"status","aria-busy":_vm.saveDisplayTotal,"aria-label":_vm.$t('saving')}},[(_vm.saveDisplayTotal)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin",attrs:{"aria-hidden":"true"}}):_vm._e()])])])]],2)],2)]),_c('tbody',_vm._l((_vm.displayedUsers),function(user){return _c('student-result-row',{key:'user-' + user.id,attrs:{"grade-book":_vm.gradeBook,"user":user,"grade-book-root-url":_vm.gradeBookRootUrl,"exclude-column-id":_vm.addColumnId,"show-null-category":_vm.showNullCategory,"edit-score-id":_vm.editScoreId,"edit-student-score-id":_vm.editStudentScoreId,"score-menu-tab":_vm.scoreMenuTab},on:{"edit-score":function($event){return _vm.showStudentScoreDialog(user.id, $event)},"edit-canceled":_vm.hideStudentScoreDialog,"edit-comment":function($event){return _vm.showStudentScoreDialog(user.id, $event, 'comment')},"menu-tab-changed":function($event){_vm.scoreMenuTab = $event},"result-updated":function($event){return _vm.overwriteResult(user.id, $event)},"result-reverted":function($event){return _vm.revertOverwrittenResult(user.id, $event)},"comment-updated":function($event){return _vm.updateResultComment(user.id, $event)}}})}),1)]),_vm._m(0)]),_c('div',{staticClass:"pagination-container u-flex u-justify-content-end my-3"},[_c('b-pagination',{attrs:{"total-rows":_vm.sortedUsers.length,"per-page":_vm.itemsPerPage,"aria-controls":"gradebook-table"},model:{value:(_vm.pagination.currentPage),callback:function ($$v) {_vm.$set(_vm.pagination, "currentPage", $$v)},expression:"pagination.currentPage"}}),_c('ul',{staticClass:"pagination"},[_c('li',{staticClass:"page-item active"},[_c('a',{staticClass:"page-link"},[_vm._v(_vm._s(_vm.$t('total'))+" "+_vm._s(_vm.sortedUsers.length))])])])],1)])
}
var GradesTablevue_type_template_id_a94e1dae_scoped_true_staticRenderFns = [function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"lds-ellipsis",attrs:{"aria-hidden":"true"}},[_c('div'),_c('div'),_c('div'),_c('div')])
}]


;// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=template&id=a94e1dae&scoped=true

;// CONCATENATED MODULE: ./src/domain/GradeBook.ts
class GradeBook {
    static NO_SCORE = 0;
    static MAX_SCORE = 1;
    static MIN_SCORE = 2;
    gradeItems = [];
    gradeColumns = [];
    categories = [];
    nullCategory = { id: 0, title: '', color: '', columnIds: [] };
    users = [];
    resultsData = {};
    dataId;
    currentVersion;
    title;
    displayTotal;
    constructor(dataId, currentVersion, title, displayTotal) {
        this.dataId = dataId;
        this.title = title;
        this.currentVersion = currentVersion;
        this.displayTotal = displayTotal;
    }
    get allCategories() {
        return [...this.categories, this.nullCategory];
    }
    getGradeItem(itemId) {
        return this.gradeItems.find(item => item.id === itemId);
    }
    getGradeColumn(columnId) {
        return this.gradeColumns.find(column => column.id === columnId);
    }
    getCategory(categoryId) {
        return this.allCategories.find(category => category.id === categoryId);
    }
    get statusGradedItems() {
        const itemIds = this.gradeColumns.reduce((ids, column) => ids.concat(column.subItemIds), []);
        return this.gradeItems.map(item => ({
            ...item, checked: itemIds.indexOf(item.id) !== -1, disabled: false
        }));
    }
    getStatusGradedItemsByColumn(columnId) {
        const column = this.getGradeColumn(columnId);
        if (!column) {
            return [];
        }
        return this.gradeItems.map(item => {
            const checked = column.subItemIds.indexOf(item.id) !== -1;
            let disabled = false;
            if (checked && column.type !== 'group') {
                disabled = true;
            }
            else {
                const col = this.findGradeColumnWithGradeItem(item.id);
                if (col && col.type === 'group' && col !== column) {
                    disabled = true;
                }
            }
            return { ...item, checked, disabled };
        });
    }
    get hasUnreleasedScores() {
        return this.gradeColumns.some(column => column.countForEndResult && !column.released);
    }
    getWeight(column) {
        if (column.weight === null) {
            return this.eqRestWeight;
        }
        return column.weight;
    }
    get eqRestWeight() {
        let rest = 100;
        let noRest = 0;
        this.gradeColumns.filter(column => column.countForEndResult)
            .forEach(column => {
            if (column.weight !== null) {
                rest -= column.weight;
            }
            else {
                noRest += 1;
            }
        });
        return rest / noRest;
    }
    setWeight(columnId, weight) {
        const column = this.getGradeColumn(columnId);
        if (column) {
            column.weight = weight;
        }
    }
    getTitle(column) {
        if (column.title) {
            return column.title;
        }
        if (column.type === 'item' || column.type === 'group') {
            return this.getGradeItem(column.subItemIds[0])?.title || '';
        }
        return '';
    }
    setTitle(columnId, title) {
        const column = this.getGradeColumn(columnId);
        if (column) {
            column.title = title || null;
        }
    }
    hasRemovedSourceData(column) {
        const subItems = this.getColumnSubItems(column);
        return subItems.some(item => item.removed);
    }
    getColumnSubItems(column) {
        return column.subItemIds.map(itemId => this.getGradeItem(itemId));
    }
    hasResult(columnId, userId) {
        if (!this.resultsData[columnId]) {
            return false;
        }
        const score = this.resultsData[columnId][userId];
        return !!score;
    }
    getResult(columnId, userId) {
        if (!this.resultsData[columnId]) {
            return null;
        }
        const score = this.resultsData[columnId][userId];
        if (!score) {
            return null;
        }
        if (score.overwritten) {
            if (score.newScoreAuthAbsent) {
                return 'aabs';
            }
            return score.newScore;
        }
        if (score.sourceScoreAuthAbsent) {
            return 'aabs';
        }
        return score.sourceScore;
    }
    getEndResult(userId, useDisplayTotal = true) {
        let endResult = 0;
        let maxWeight = 0;
        this.gradeColumns.filter(column => column.countForEndResult).forEach(column => {
            const result = this.getResult(column.id, userId);
            const weight = this.getWeight(column);
            if (typeof result === 'number') {
                maxWeight += weight;
            }
            else if (result === 'aabs') {
                if (column.authPresenceEndResult !== GradeBook.NO_SCORE) {
                    maxWeight += weight;
                    if (column.authPresenceEndResult === GradeBook.MAX_SCORE) {
                        endResult += weight;
                    }
                }
            }
            else if (result === null) {
                if (column.unauthPresenceEndResult !== GradeBook.NO_SCORE) {
                    maxWeight += weight;
                    if (column.unauthPresenceEndResult === GradeBook.MAX_SCORE) {
                        endResult += weight;
                    }
                }
            }
            if (typeof result === 'number') {
                endResult += (result * weight * 0.01);
            }
        });
        if (maxWeight === 0) {
            return 0;
        }
        if (useDisplayTotal) {
            return (endResult / maxWeight) * this.getDisplayTotal();
        }
        return (endResult / maxWeight) * 100;
    }
    getDisplayTotal() {
        if (this.displayTotal !== null && this.displayTotal !== 100) {
            return this.displayTotal;
        }
        return 100;
    }
    isOverwrittenResult(columnId, userId) {
        if (!this.resultsData[columnId]) {
            return false;
        }
        const score = this.resultsData[columnId][userId];
        if (!score) {
            return false;
        }
        return score.overwritten;
    }
    overwriteResult(columnId, userId, value) {
        if (!this.resultsData[columnId]) {
            return false;
        }
        const score = this.resultsData[columnId][userId];
        if (!score) {
            return false;
        }
        score.overwritten = true;
        if (value === 'aabs') {
            score.newScoreAuthAbsent = true;
            score.newScore = null;
        }
        else {
            score.newScoreAuthAbsent = false;
            score.newScore = value;
        }
        return score;
    }
    revertOverwrittenResult(columnId, userId) {
        if (!this.resultsData[columnId]) {
            return false;
        }
        const score = this.resultsData[columnId][userId];
        if (!score) {
            return false;
        }
        score.overwritten = false;
        score.newScoreAuthAbsent = false;
        score.newScore = null;
        return score;
    }
    userTotalNeedsUpdating(user) {
        const total = this.getResult('totals', user.id);
        if (total === null) {
            return false;
        } // unsynchronized user, cannot update
        if (typeof total !== 'number') {
            return true;
        }
        return total.toFixed(2) !== parseFloat(this.getEndResult(user.id, false).toPrecision(8)).toFixed(2);
    }
    get totalsNeedUpdating() {
        return this.users.some(user => this.userTotalNeedsUpdating(user));
    }
    getResultComment(columnId, userId) {
        if (!this.resultsData[columnId]) {
            return null;
        }
        const score = this.resultsData[columnId][userId];
        if (!score) {
            return null;
        }
        return score.comment;
    }
    updateResultComment(columnId, userId, comment) {
        if (!this.resultsData[columnId]) {
            return false;
        }
        const score = this.resultsData[columnId][userId];
        if (!score) {
            return false;
        }
        score.comment = comment;
        return score;
    }
    addItemToCategory(categoryId, columnId) {
        const category = categoryId === 0 ? this.nullCategory : this.getCategory(categoryId);
        if (category?.columnIds.indexOf(columnId) === -1) {
            this.allCategories.forEach(cat => {
                if (cat.columnIds.indexOf(columnId) !== -1) {
                    cat.columnIds = cat.columnIds.filter(id => id !== columnId);
                }
            });
            category.columnIds.push(columnId);
        }
    }
    removeCategory(category) {
        if (category === this.nullCategory) {
            return;
        }
        const columnIds = category.columnIds;
        const index = this.categories.indexOf(category);
        if (index < 0) {
            return;
        }
        this.categories.splice(index, 1);
        if (columnIds.length) {
            this.nullCategory.columnIds = [...this.nullCategory.columnIds, ...columnIds];
        }
    }
    updateGradeColumnId(column, newId) {
        const oldId = column.id;
        column.id = newId;
        this.allCategories.forEach(cat => {
            const index = cat.columnIds.indexOf(oldId);
            if (index !== -1) {
                cat.columnIds[index] = newId;
            }
        });
    }
    addGradeColumnFromItem(item) {
        const newId = this.createNewColumnId();
        const column = {
            id: newId, type: 'item', title: null, subItemIds: [item.id], weight: null,
            countForEndResult: true,
            released: true,
            authPresenceEndResult: GradeBook.NO_SCORE,
            unauthPresenceEndResult: GradeBook.MIN_SCORE
        };
        this.gradeColumns.push(column);
        this.addItemToCategory(0, newId);
        return column;
    }
    findGradeColumnWithGradeItem(itemId) {
        const column = this.gradeColumns.find(column => column.subItemIds.indexOf(itemId) !== -1);
        return column || null;
    }
    removeSubItem(item) {
        this.gradeColumns.forEach(column => {
            if (column.subItemIds.length) {
                column.subItemIds = column.subItemIds.filter(id => id !== item.id);
            }
        });
        if (item.removed) {
            this.gradeItems = this.gradeItems.filter(gradeItem => gradeItem !== item);
        }
    }
    createNewIdWithPrefix(prefix) {
        const itemIds = this.gradeColumns.map(column => column.id);
        let i = 1;
        while (itemIds.indexOf(prefix + i) !== -1) {
            i += 1;
        }
        return prefix + i;
    }
    createNewColumnId() {
        return this.createNewIdWithPrefix('col');
    }
    createNewStandaloneScoreId() {
        return this.createNewIdWithPrefix('sc');
    }
    createNewScore() {
        const id = this.createNewStandaloneScoreId();
        const newScore = { id, title: 'Score', type: 'standalone', subItemIds: [], weight: null, countForEndResult: true, released: true, authPresenceEndResult: GradeBook.NO_SCORE, unauthPresenceEndResult: GradeBook.MIN_SCORE };
        this.gradeColumns.push(newScore);
        this.nullCategory.columnIds.push(id);
        return newScore;
    }
    createNewCategory() {
        const id = this.categories.length ? Math.max.apply(null, this.categories.map(cat => cat.id)) + 1 : 1;
        const newCategory = { id, title: 'Categorie', color: '#92eded', columnIds: [] };
        this.categories.push(newCategory);
        return newCategory;
    }
    addSubItem(item, columnId) {
        const column = this.getGradeColumn(columnId);
        if (!column) {
            return;
        }
        const srcColumn = this.findGradeColumnWithGradeItem(item.id);
        column.title = this.getTitle(column);
        column.type = 'group';
        column.subItemIds.push(item.id);
        if (srcColumn) {
            this.gradeColumns = this.gradeColumns.filter(column => column !== srcColumn);
            this.allCategories.forEach(cat => {
                cat.columnIds = cat.columnIds.filter(id => id !== srcColumn.id);
            });
            delete this.resultsData[srcColumn.id];
        }
    }
    removeColumn(column) {
        column.subItemIds.forEach(itemId => {
            this.removeSubItem(this.getGradeItem(itemId));
        });
        delete this.resultsData[column.id];
        this.gradeColumns = this.gradeColumns.filter(col => col !== column);
        this.allCategories.forEach(cat => {
            cat.columnIds = cat.columnIds.filter(id => id !== column.id);
        });
    }
    static from(gradeBookObject) {
        const gradeBook = new GradeBook(gradeBookObject.dataId, gradeBookObject.version, gradeBookObject.title, gradeBookObject.displayTotal);
        gradeBook.gradeItems = gradeBookObject.gradeItems;
        gradeBook.gradeColumns = gradeBookObject.gradeColumns;
        gradeBook.categories = gradeBookObject.categories;
        gradeBook.nullCategory = gradeBookObject.nullCategory;
        return gradeBook;
    }
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResultRow.vue?vue&type=template&id=61d708b5&scoped=true
var StudentResultRowvue_type_template_id_61d708b5_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('tr',{staticClass:"table-row table-body-row"},[_c('td',{staticClass:"col-sticky table-student"},[(_vm.gradeBookRootUrl)?_c('a',{attrs:{"href":`${_vm.gradeBookRootUrl}&gradebook_display_action=UserScores&user_id=${_vm.userId}`}},[_vm._v(_vm._s(_vm.lastName)+", "+_vm._s(_vm.firstName))]):[_vm._v(_vm._s(_vm.lastName)+", "+_vm._s(_vm.firstName))]],2),(_vm.isSynchronized)?[_vm._l((_vm.columns),function(column,index){return [(column.isScoreColumn)?_c('td',{key:`col-${index}`,class:{'unreleased-score-cell': !column.released, 'uncounted-score-cell': !column.countsForEndResult, 'u-relative': column.isEditing}},[(column.hasResult && !column.isEditing)?_c('student-result',{staticClass:"u-flex u-align-items-center u-justify-content-end u-cursor-pointer",class:{'uncounted-score': !column.countsForEndResult},attrs:{"id":`result-${column.id}-${_vm.userId}`,"result":column.result,"comment":column.comment,"is-standalone-score":column.isStandaloneScore,"use-overwritten-flag":true,"is-overwritten":column.isOverwrittenResult},on:{"edit":function($event){return _vm.$emit('edit-score', column.id)},"edit-comment":function($event){return _vm.$emit('edit-comment', column.id)}}}):_vm._e(),(column.isEditing)?_c('score-input',{attrs:{"menu-tab":_vm.scoreMenuTab,"score":column.result,"comment":column.comment,"use-revert":column.isOverwrittenResult && !column.isStandaloneScore},on:{"menu-tab-changed":function($event){return _vm.$emit('menu-tab-changed', $event)},"cancel":function($event){return _vm.$emit('edit-canceled')},"comment-updated":function($event){return _vm.$emit('comment-updated', {columnId: column.id, comment: $event})},"ok":function($event){return _vm.$emit('result-updated', {columnId: column.id, value: $event})},"revert":function($event){return _vm.$emit('result-reverted', column.id)}}}):_vm._e()],1):_c('td',{key:`col-${index}`})]}),_c('td',{staticClass:"col-sticky table-student-total u-text-end",class:{'unreleased-score-cell': _vm.gradeBook.hasUnreleasedScores, 'mod-needs-update': _vm.totalNeedsUpdate}},[_c('div',{staticClass:"u-flex u-align-items-baseline u-gap-small-3x",class:_vm.gradeBook.getDisplayTotal() === 100 ? 'u-justify-content-end' : 'u-justify-content-between'},[(_vm.gradeBook.getDisplayTotal() !== 100)?_c('div',{staticStyle:{"font-size":".6875rem","color":"#437070","width":"50px","text-align":"right"}},[_vm._v(" ("+_vm._s(_vm._f("formatNum2")(_vm.endResultPct))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")]),_vm._v(") ")]):_vm._e(),_c('div',[(_vm.totalNeedsUpdate)?_c('i',{staticClass:"fa fa-exclamation-circle",attrs:{"title":_vm.$t('not-yet-updated'),"aria-hidden":"true"}}):_vm._e(),(_vm.totalNeedsUpdate)?_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('not-yet-updated')))]):_vm._e(),_vm._v(_vm._s(_vm._f("formatNum2")(_vm.endResult))),(_vm.gradeBook.getDisplayTotal() === 100)?[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]:_vm._e()],2)])])]:_c('td',{staticClass:"table-student-unsychronized",attrs:{"colspan":_vm.gradeBook.gradeColumns.length + 1}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center"},[_vm._v(_vm._s(_vm.$t('not-synchronized')))])])],2)
}
var StudentResultRowvue_type_template_id_61d708b5_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ScoreInput.vue?vue&type=template&id=6aed5479&scoped=true
var ScoreInputvue_type_template_id_6aed5479_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"menu",fn:function(){return [_c('div',{staticClass:"cell-content content-tabs"},[_c('div',{staticClass:"u-flex u-justify-content-end u-text-end",attrs:{"role":"tablist"}},[_c('div',{staticClass:"menu-tab u-cursor-pointer",class:{'mod-active': _vm.menuTab === 'score'},attrs:{"role":"tab","aria-selected":_vm.menuTab === 'score' ? 'true' : 'false',"aria-controls":"score-panel"},on:{"click":function($event){return _vm.$emit('menu-tab-changed', 'score')}}},[_vm._v(_vm._s(_vm.$t('score')))]),_c('div',{staticClass:"menu-tab u-cursor-pointer",class:{'mod-active': _vm.menuTab === 'comment'},attrs:{"role":"tab","aria-selected":_vm.menuTab === 'comment' ? 'true' : 'false',"aria-controls":"score-panel"},on:{"click":function($event){return _vm.$emit('menu-tab-changed', 'comment')}}},[_vm._v(_vm._s(_vm.$t('comments')))])])])]},proxy:true},{key:"content",fn:function(){return [(_vm.menuTab === 'score')?_c('div',{staticClass:"u-flex u-gap-small",attrs:{"role":"tabpanel","id":"score-panel"}},[_c('div',{staticClass:"number-input u-relative",class:{'is-selected': _vm.type === 'number'}},[_c('input',{ref:"score-input",staticClass:"percent-input u-font-normal",attrs:{"id":"score","type":"number","min":"0","max":"100","step":".01","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.numValue)},on:{"input":function($event){_vm.type = 'number'},"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter"))return null;return _vm.onEdit.apply(null, arguments)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"]))return null;return _vm.$emit('cancel')}],"focus":function($event){_vm.type = 'number'}}}),_c('div',{staticClass:"percent"},[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])]),_c('button',{staticClass:"color-code amber-700",class:{'is-selected': _vm.type === 'aabs'},attrs:{"title":_vm.$t('auth-absent')},on:{"click":_vm.setAuthAbsent}},[_c('span',[_vm._v(_vm._s(_vm.$t('aabs')))])]),(_vm.useRevert)?_c('button',{staticClass:"btn btn-secundary btn-sm btn-revert",attrs:{"title":_vm.$t('use-source-result')},on:{"click":_vm.setRevert}},[_c('i',{staticClass:"fa fa-undo",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('use-source-result')))])]):_vm._e()]):_vm._e(),(_vm.menuTab === 'comment')?_c('div',{attrs:{"role":"tabpanel","id":"score-panel"}},[_c('textarea',{directives:[{name:"model",rawName:"v-model",value:(_vm.commentValue),expression:"commentValue"}],ref:"comment-input",staticClass:"comment-field",domProps:{"value":(_vm.commentValue)},on:{"input":function($event){if($event.target.composing)return;_vm.commentValue=$event.target.value}}})]):_vm._e()]},proxy:true}])})
}
var ScoreInputvue_type_template_id_6aed5479_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/TableCellInput.vue?vue&type=template&id=557783c1&scoped=true
var TableCellInputvue_type_template_id_557783c1_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_vm._t("menu"),_c('div',{staticClass:"cell-content"},[_vm._t("content"),_c('div',{staticClass:"u-flex name-input-actions"},[_c('button',{staticClass:"btn btn-primary btn-sm",on:{"click":function($event){return _vm.$emit('edit')}}},[_vm._v(_vm._s(_vm.$t('edit')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":function($event){return _vm.$emit('cancel')}}},[_vm._v(_vm._s(_vm.$t('cancel')))])])],2)],2)
}
var TableCellInputvue_type_template_id_557783c1_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/TableCellInput.vue?vue&type=script&lang=ts


let TableCellInput = class TableCellInput extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
};
TableCellInput = __decorate([
    vue_class_component_esm({
        name: 'table-cell-input'
    })
], TableCellInput);
/* harmony default export */ const TableCellInputvue_type_script_lang_ts = (TableCellInput);

;// CONCATENATED MODULE: ./src/components/TableCellInput.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_TableCellInputvue_type_script_lang_ts = (TableCellInputvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/TableCellInput.vue?vue&type=style&index=0&id=557783c1&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/TableCellInput.vue?vue&type=style&index=0&id=557783c1&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/TableCellInput.vue?vue&type=custom&index=0&blockType=i18n
var TableCellInputvue_type_custom_index_0_blockType_i18n = __webpack_require__(694);
var TableCellInputvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(TableCellInputvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/TableCellInput.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_TableCellInputvue_type_custom_index_0_blockType_i18n = ((TableCellInputvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/TableCellInput.vue



;


/* normalize component */

var TableCellInput_component = normalizeComponent(
  components_TableCellInputvue_type_script_lang_ts,
  TableCellInputvue_type_template_id_557783c1_scoped_true_render,
  TableCellInputvue_type_template_id_557783c1_scoped_true_staticRenderFns,
  false,
  null,
  "557783c1",
  null
  
)

/* custom blocks */
;
if (typeof components_TableCellInputvue_type_custom_index_0_blockType_i18n === 'function') components_TableCellInputvue_type_custom_index_0_blockType_i18n(TableCellInput_component)

/* harmony default export */ const components_TableCellInput = (TableCellInput_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ScoreInput.vue?vue&type=script&lang=ts



let ScoreInput = class ScoreInput extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    type = 'number';
    numValue = '';
    commentValue = '';
    score;
    comment;
    menuTab;
    useRevert;
    get scoreInput() {
        return this.$refs['score-input'];
    }
    get commentInput() {
        return this.$refs['comment-input'];
    }
    onEdit() {
        if (this.menuTab === 'comment') {
            this.$emit('comment-updated', this.commentValue || null);
            return;
        }
        if (this.type === 'number') {
            const el = this.scoreInput;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            const value = parseFloat(this.scoreInput.value);
            this.$emit('ok', isNaN(value) ? null : value);
        }
        else if (this.type === 'aabs') {
            this.$emit('ok', 'aabs');
        }
        else if (this.type === 'revert') {
            this.$emit('revert');
        }
    }
    setAuthAbsent() {
        this.type = 'aabs';
        this.$nextTick(() => this.numValue = '');
    }
    setRevert() {
        this.type = 'revert';
        this.$nextTick(() => this.numValue = '');
    }
    mounted() {
        if (this.score === 'aabs') {
            this.type = 'aabs';
            return;
        }
        this.type = 'number';
        this.numValue = String(this.score);
        this.commentValue = this.comment || '';
        if (this.menuTab === 'comment') {
            this.$nextTick(() => this.commentInput.focus());
        }
        else {
            this.$nextTick(() => this.scoreInput.focus());
        }
    }
};
__decorate([
    Prop({ type: [Number, String], default: null })
], ScoreInput.prototype, "score", void 0);
__decorate([
    Prop({ type: String, default: null })
], ScoreInput.prototype, "comment", void 0);
__decorate([
    Prop({ type: String, default: 'score' })
], ScoreInput.prototype, "menuTab", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], ScoreInput.prototype, "useRevert", void 0);
ScoreInput = __decorate([
    vue_class_component_esm({
        name: 'score-input',
        components: { TableCellInput: components_TableCellInput },
        filters: {
            formatNum: function (v) {
                if (v === null) {
                    return '';
                }
                return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], ScoreInput);
/* harmony default export */ const ScoreInputvue_type_script_lang_ts = (ScoreInput);

;// CONCATENATED MODULE: ./src/components/ScoreInput.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_ScoreInputvue_type_script_lang_ts = (ScoreInputvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ScoreInput.vue?vue&type=style&index=0&id=6aed5479&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/ScoreInput.vue?vue&type=style&index=0&id=6aed5479&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ScoreInput.vue?vue&type=custom&index=0&blockType=i18n
var ScoreInputvue_type_custom_index_0_blockType_i18n = __webpack_require__(713);
var ScoreInputvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(ScoreInputvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/ScoreInput.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_ScoreInputvue_type_custom_index_0_blockType_i18n = ((ScoreInputvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/ScoreInput.vue



;


/* normalize component */

var ScoreInput_component = normalizeComponent(
  components_ScoreInputvue_type_script_lang_ts,
  ScoreInputvue_type_template_id_6aed5479_scoped_true_render,
  ScoreInputvue_type_template_id_6aed5479_scoped_true_staticRenderFns,
  false,
  null,
  "6aed5479",
  null
  
)

/* custom blocks */
;
if (typeof components_ScoreInputvue_type_custom_index_0_blockType_i18n === 'function') components_ScoreInputvue_type_custom_index_0_blockType_i18n(ScoreInput_component)

/* harmony default export */ const components_ScoreInput = (ScoreInput_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResult.vue?vue&type=template&id=39963f2d&scoped=true
var StudentResultvue_type_template_id_39963f2d_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{on:{"dblclick":function($event){return _vm.$emit('edit')}}},[_c('div',{staticClass:"u-flex u-align-items-center u-gap-small"},[(_vm.comment)?[_c('a',{staticClass:"fa fa-comment-o",attrs:{"id":`result-comment-${_vm.id}`,"title":_vm.$t('edit-comment')},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('edit-comment')}}},[_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('edit-comment')))])]),_c('b-popover',{attrs:{"custom-class":"gradebook-comment-popover","target":`result-comment-${_vm.id}`,"triggers":"hover","placement":"top"}},[_c('div',{staticClass:"comment"},[_c('div',{staticClass:"comment-header"},[_vm._v("Feedback:")]),_vm._v(" "+_vm._s(_vm.comment)+" ")])])]:_vm._e(),_c('div',{staticClass:"result u-flex u-align-items-center u-justify-content-end",class:{'overwritten-score': !_vm.isStandaloneScore && _vm.useOverwrittenFlag && _vm.isOverwritten, 'mod-none': _vm.result === null, 'mod-aabs': _vm.result === 'aabs'}},[(_vm.result === 'aabs')?_c('div',{staticClass:"color-code amber-700",attrs:{"title":_vm.$t('auth-absent')}},[_c('span',[_vm._v(_vm._s(_vm.$t('aabs')))])]):(_vm.result === null)?_c('div',{staticClass:"color-code mod-none",attrs:{"title":_vm.$t('no-score-found')}},[_c('i',{staticClass:"fa fa-question",class:{'mod-none': _vm.isStandaloneScore || !_vm.useOverwrittenFlag || !_vm.isOverwritten},attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('no-score-found')))])]):_c('div',[_vm._v(_vm._s(_vm._f("formatNum2")(_vm.result))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])])],2)])
}
var StudentResultvue_type_template_id_39963f2d_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResult.vue?vue&type=script&lang=ts


let StudentResult = class StudentResult extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    id;
    result;
    useOverwrittenFlag;
    isOverwritten;
    isStandaloneScore;
    comment;
};
__decorate([
    Prop({ type: String, default: '' })
], StudentResult.prototype, "id", void 0);
__decorate([
    Prop({ type: [Number, String], default: null })
], StudentResult.prototype, "result", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], StudentResult.prototype, "useOverwrittenFlag", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], StudentResult.prototype, "isOverwritten", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], StudentResult.prototype, "isStandaloneScore", void 0);
__decorate([
    Prop({ type: String, default: '' })
], StudentResult.prototype, "comment", void 0);
StudentResult = __decorate([
    vue_class_component_esm({
        name: 'student-result',
        filters: {
            formatNum2: function (v) {
                if (v === null) {
                    return '';
                }
                return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], StudentResult);
/* harmony default export */ const StudentResultvue_type_script_lang_ts = (StudentResult);

;// CONCATENATED MODULE: ./src/components/StudentResult.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_StudentResultvue_type_script_lang_ts = (StudentResultvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResult.vue?vue&type=style&index=0&id=39963f2d&prod&scoped=true&lang=scss
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/StudentResult.vue?vue&type=style&index=0&id=39963f2d&prod&scoped=true&lang=scss

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResult.vue?vue&type=style&index=1&id=39963f2d&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/StudentResult.vue?vue&type=style&index=1&id=39963f2d&prod&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResult.vue?vue&type=custom&index=0&blockType=i18n
var StudentResultvue_type_custom_index_0_blockType_i18n = __webpack_require__(667);
var StudentResultvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(StudentResultvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/StudentResult.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_StudentResultvue_type_custom_index_0_blockType_i18n = ((StudentResultvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/StudentResult.vue



;



/* normalize component */

var StudentResult_component = normalizeComponent(
  components_StudentResultvue_type_script_lang_ts,
  StudentResultvue_type_template_id_39963f2d_scoped_true_render,
  StudentResultvue_type_template_id_39963f2d_scoped_true_staticRenderFns,
  false,
  null,
  "39963f2d",
  null
  
)

/* custom blocks */
;
if (typeof components_StudentResultvue_type_custom_index_0_blockType_i18n === 'function') components_StudentResultvue_type_custom_index_0_blockType_i18n(StudentResult_component)

/* harmony default export */ const components_StudentResult = (StudentResult_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResultRow.vue?vue&type=script&lang=ts





let StudentResultRow = class StudentResultRow extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    gradeBook;
    user;
    gradeBookRootUrl;
    excludeColumnId;
    editStudentScoreId;
    editScoreId;
    scoreMenuTab;
    showNullCategory;
    get userId() {
        return this.user.id;
    }
    get firstName() {
        return this.user.firstName;
    }
    get lastName() {
        return this.user.lastName.toUpperCase();
    }
    get isSynchronized() {
        return !(this.gradeBook.gradeColumns.filter(column => column.id !== this.excludeColumnId).some(column => !this.gradeBook.hasResult(column.id, this.userId)));
    }
    get totalNeedsUpdate() {
        return this.gradeBook.userTotalNeedsUpdating(this.user);
    }
    get endResult() {
        return this.gradeBook.getEndResult(this.userId);
    }
    get endResultPct() {
        return this.gradeBook.getEndResult(this.userId, false);
    }
    get displayedCategories() {
        if (this.showNullCategory) {
            return [...this.gradeBook.categories, this.gradeBook.nullCategory];
        }
        return this.gradeBook.categories;
    }
    getColumnData(columnId) {
        const gradeBook = this.gradeBook;
        const userId = this.userId;
        const column = gradeBook.getGradeColumn(columnId);
        if (!column) {
            throw new Error(`GradeColumn with id ${columnId} not found.`);
        }
        return {
            id: columnId,
            isScoreColumn: true,
            released: column.released,
            isStandaloneScore: column.type === 'standalone',
            countsForEndResult: column.countForEndResult,
            isEditing: this.editStudentScoreId === userId && this.editScoreId === columnId,
            hasResult: gradeBook.hasResult(columnId, userId),
            result: gradeBook.getResult(columnId, userId),
            isOverwrittenResult: gradeBook.isOverwrittenResult(columnId, userId),
            comment: gradeBook.getResultComment(columnId, userId)
        };
    }
    get columns() {
        return this.displayedCategories.reduce((columns, currentCategory) => {
            if (currentCategory.columnIds.length) {
                return [...columns, ...currentCategory.columnIds.map(columnId => this.getColumnData(columnId))];
            }
            return [...columns, { isScoreColumn: false }];
        }, []);
    }
};
__decorate([
    Prop({ type: GradeBook, required: true })
], StudentResultRow.prototype, "gradeBook", void 0);
__decorate([
    Prop({ type: Object, required: true })
], StudentResultRow.prototype, "user", void 0);
__decorate([
    Prop({ type: String, default: '' })
], StudentResultRow.prototype, "gradeBookRootUrl", void 0);
__decorate([
    Prop({ type: [String, Number], default: null })
], StudentResultRow.prototype, "excludeColumnId", void 0);
__decorate([
    Prop({ type: Number, default: null })
], StudentResultRow.prototype, "editStudentScoreId", void 0);
__decorate([
    Prop({ type: [String, Number], default: null })
], StudentResultRow.prototype, "editScoreId", void 0);
__decorate([
    Prop({ type: String, default: 'score' })
], StudentResultRow.prototype, "scoreMenuTab", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], StudentResultRow.prototype, "showNullCategory", void 0);
StudentResultRow = __decorate([
    vue_class_component_esm({
        name: 'student-result-row',
        components: { ScoreInput: components_ScoreInput, StudentResult: components_StudentResult },
        filters: {
            formatNum2: function (v) {
                if (v === null) {
                    return '';
                }
                return parseFloat(v.toPrecision(8)).toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], StudentResultRow);
/* harmony default export */ const StudentResultRowvue_type_script_lang_ts = (StudentResultRow);

;// CONCATENATED MODULE: ./src/components/StudentResultRow.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_StudentResultRowvue_type_script_lang_ts = (StudentResultRowvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResultRow.vue?vue&type=style&index=0&id=61d708b5&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/StudentResultRow.vue?vue&type=style&index=0&id=61d708b5&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/StudentResultRow.vue?vue&type=custom&index=0&blockType=i18n
var StudentResultRowvue_type_custom_index_0_blockType_i18n = __webpack_require__(309);
var StudentResultRowvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(StudentResultRowvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/StudentResultRow.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_StudentResultRowvue_type_custom_index_0_blockType_i18n = ((StudentResultRowvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/StudentResultRow.vue



;


/* normalize component */

var StudentResultRow_component = normalizeComponent(
  components_StudentResultRowvue_type_script_lang_ts,
  StudentResultRowvue_type_template_id_61d708b5_scoped_true_render,
  StudentResultRowvue_type_template_id_61d708b5_scoped_true_staticRenderFns,
  false,
  null,
  "61d708b5",
  null
  
)

/* custom blocks */
;
if (typeof components_StudentResultRowvue_type_custom_index_0_blockType_i18n === 'function') components_StudentResultRowvue_type_custom_index_0_blockType_i18n(StudentResultRow_component)

/* harmony default export */ const components_StudentResultRow = (StudentResultRow_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemTitleInput.vue?vue&type=template&id=97c5d59e&scoped=true
var ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"content",fn:function(){return [_c('input',{ref:"title-input",staticClass:"u-font-normal",attrs:{"type":"text"},domProps:{"value":_vm.itemTitle},on:{"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter"))return null;return _vm.onEdit.apply(null, arguments)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"]))return null;return _vm.$emit('cancel')}]}})]},proxy:true}])})
}
var ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemTitleInput.vue?vue&type=script&lang=ts



let ItemTitleInput = class ItemTitleInput extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    itemTitle;
    get titleInput() {
        return this.$refs['title-input'];
    }
    onEdit() {
        this.$emit('ok', this.titleInput.value);
    }
    mounted() {
        this.$nextTick(() => this.titleInput.focus());
    }
};
__decorate([
    Prop({ type: String, default: '' })
], ItemTitleInput.prototype, "itemTitle", void 0);
ItemTitleInput = __decorate([
    vue_class_component_esm({
        name: 'item-title-input',
        components: { TableCellInput: components_TableCellInput },
    })
], ItemTitleInput);
/* harmony default export */ const ItemTitleInputvue_type_script_lang_ts = (ItemTitleInput);

;// CONCATENATED MODULE: ./src/components/ItemTitleInput.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_ItemTitleInputvue_type_script_lang_ts = (ItemTitleInputvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemTitleInput.vue?vue&type=style&index=0&id=97c5d59e&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/ItemTitleInput.vue?vue&type=style&index=0&id=97c5d59e&prod&scoped=true&lang=css

;// CONCATENATED MODULE: ./src/components/ItemTitleInput.vue



;


/* normalize component */

var ItemTitleInput_component = normalizeComponent(
  components_ItemTitleInputvue_type_script_lang_ts,
  ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_render,
  ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_staticRenderFns,
  false,
  null,
  "97c5d59e",
  null
  
)

/* harmony default export */ const components_ItemTitleInput = (ItemTitleInput_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/WeightInput.vue?vue&type=template&id=06f6a2a8&scoped=true
var WeightInputvue_type_template_id_06f6a2a8_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"content",fn:function(){return [_c('label',{staticClass:"u-font-medium",attrs:{"for":"weight"}},[_vm._v(_vm._s(_vm.$t('weight'))+":")]),_c('div',{staticClass:"u-relative"},[_c('input',{ref:"weight-input",staticClass:"percent-input u-font-normal",attrs:{"id":"weight","type":"number","min":"0","max":"100","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.itemWeight)},on:{"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter"))return null;return _vm.onEdit.apply(null, arguments)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"]))return null;return _vm.$emit('cancel')}]}}),_c('div',{staticClass:"percent"},[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])])]},proxy:true}])})
}
var WeightInputvue_type_template_id_06f6a2a8_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/WeightInput.vue?vue&type=script&lang=ts



let WeightInput = class WeightInput extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    itemWeight;
    get weightInput() {
        return this.$refs['weight-input'];
    }
    onEdit() {
        const el = this.weightInput;
        if (!el.checkValidity()) {
            el.reportValidity();
            return;
        }
        const value = parseFloat(this.weightInput.value);
        this.$emit('ok', isNaN(value) ? null : value);
    }
    mounted() {
        this.$nextTick(() => this.weightInput.focus());
    }
};
__decorate([
    Prop({ type: Number, default: '' })
], WeightInput.prototype, "itemWeight", void 0);
WeightInput = __decorate([
    vue_class_component_esm({
        name: 'weight-input',
        components: { TableCellInput: components_TableCellInput },
        filters: {
            formatNum: function (v) {
                if (v === null) {
                    return '';
                }
                return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], WeightInput);
/* harmony default export */ const WeightInputvue_type_script_lang_ts = (WeightInput);

;// CONCATENATED MODULE: ./src/components/WeightInput.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_WeightInputvue_type_script_lang_ts = (WeightInputvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/WeightInput.vue?vue&type=style&index=0&id=06f6a2a8&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/WeightInput.vue?vue&type=style&index=0&id=06f6a2a8&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/WeightInput.vue?vue&type=custom&index=0&blockType=i18n
var WeightInputvue_type_custom_index_0_blockType_i18n = __webpack_require__(481);
var WeightInputvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(WeightInputvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/WeightInput.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_WeightInputvue_type_custom_index_0_blockType_i18n = ((WeightInputvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/WeightInput.vue



;


/* normalize component */

var WeightInput_component = normalizeComponent(
  components_WeightInputvue_type_script_lang_ts,
  WeightInputvue_type_template_id_06f6a2a8_scoped_true_render,
  WeightInputvue_type_template_id_06f6a2a8_scoped_true_staticRenderFns,
  false,
  null,
  "06f6a2a8",
  null
  
)

/* custom blocks */
;
if (typeof components_WeightInputvue_type_custom_index_0_blockType_i18n === 'function') components_WeightInputvue_type_custom_index_0_blockType_i18n(WeightInput_component)

/* harmony default export */ const components_WeightInput = (WeightInput_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/DisplayTotalInput.vue?vue&type=template&id=b8095f98&scoped=true
var DisplayTotalInputvue_type_template_id_b8095f98_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"content",fn:function(){return [_c('label',{staticClass:"u-font-medium",attrs:{"for":"display-total"}},[_vm._v(_vm._s(_vm.$t('display-total'))+":")]),_c('div',{staticClass:"u-relative"},[_c('input',{ref:"display-total-input",staticClass:"percent-input u-font-normal",attrs:{"id":"display-total","type":"number","min":"0","max":"100","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.displayTotal)},on:{"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter"))return null;return _vm.onEdit.apply(null, arguments)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"]))return null;return _vm.$emit('cancel')}]}})])]},proxy:true}])})
}
var DisplayTotalInputvue_type_template_id_b8095f98_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/DisplayTotalInput.vue?vue&type=script&lang=ts



let DisplayTotalInputvue_type_script_lang_ts_WeightInput = class WeightInput extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    displayTotal;
    get displayTotalInput() {
        return this.$refs['display-total-input'];
    }
    onEdit() {
        const el = this.displayTotalInput;
        if (!el.checkValidity()) {
            el.reportValidity();
            return;
        }
        const value = parseFloat(this.displayTotalInput.value);
        this.$emit('ok', isNaN(value) ? null : value);
    }
    mounted() {
        this.$nextTick(() => this.displayTotalInput.focus());
    }
};
__decorate([
    Prop({ type: Number, default: '' })
], DisplayTotalInputvue_type_script_lang_ts_WeightInput.prototype, "displayTotal", void 0);
DisplayTotalInputvue_type_script_lang_ts_WeightInput = __decorate([
    vue_class_component_esm({
        name: 'display-total-input',
        components: { TableCellInput: components_TableCellInput },
        filters: {
            formatNum: function (v) {
                if (v === null) {
                    return '';
                }
                return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], DisplayTotalInputvue_type_script_lang_ts_WeightInput);
/* harmony default export */ const DisplayTotalInputvue_type_script_lang_ts = (DisplayTotalInputvue_type_script_lang_ts_WeightInput);

;// CONCATENATED MODULE: ./src/components/DisplayTotalInput.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_DisplayTotalInputvue_type_script_lang_ts = (DisplayTotalInputvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/DisplayTotalInput.vue?vue&type=style&index=0&id=b8095f98&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/DisplayTotalInput.vue?vue&type=style&index=0&id=b8095f98&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/DisplayTotalInput.vue?vue&type=custom&index=0&blockType=i18n
var DisplayTotalInputvue_type_custom_index_0_blockType_i18n = __webpack_require__(220);
var DisplayTotalInputvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(DisplayTotalInputvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/DisplayTotalInput.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_DisplayTotalInputvue_type_custom_index_0_blockType_i18n = ((DisplayTotalInputvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/DisplayTotalInput.vue



;


/* normalize component */

var DisplayTotalInput_component = normalizeComponent(
  components_DisplayTotalInputvue_type_script_lang_ts,
  DisplayTotalInputvue_type_template_id_b8095f98_scoped_true_render,
  DisplayTotalInputvue_type_template_id_b8095f98_scoped_true_staticRenderFns,
  false,
  null,
  "b8095f98",
  null
  
)

/* custom blocks */
;
if (typeof components_DisplayTotalInputvue_type_custom_index_0_blockType_i18n === 'function') components_DisplayTotalInputvue_type_custom_index_0_blockType_i18n(DisplayTotalInput_component)

/* harmony default export */ const DisplayTotalInput = (DisplayTotalInput_component.exports);
// EXTERNAL MODULE: ../../../../../../../../node_modules/vuedraggable/dist/vuedraggable.umd.js
var vuedraggable_umd = __webpack_require__(530);
var vuedraggable_umd_default = /*#__PURE__*/__webpack_require__.n(vuedraggable_umd);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesTable.vue?vue&type=script&lang=ts










let GradesTable = class GradesTable extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    isDraggingColumn = false;
    isDraggingCategory = false;
    categoryDropArea = null;
    editItemId = null;
    catEditItemId = null;
    weightEditItemId = null;
    editStudentScoreId = null;
    editScoreId = null;
    editDisplayTotalDialog = false;
    scoreMenuTab = 'score';
    sortBy = 'lastname';
    sortDesc = false;
    pagination = {
        currentPage: 1
    };
    gradeBook;
    searchTerms;
    busy;
    addColumnId;
    saveColumnId;
    saveCategoryId;
    saveDisplayTotal;
    itemsPerPage;
    gradeBookRootUrl;
    get showNullCategory() {
        return this.isDraggingColumn || this.gradeBook.nullCategory.columnIds.length > 0;
    }
    get displayedCategories() {
        if (this.showNullCategory) {
            return [...this.gradeBook.categories, this.gradeBook.nullCategory];
        }
        return this.gradeBook.categories;
    }
    get displayedUsers() {
        const { currentPage } = this.pagination;
        const perPage = this.itemsPerPage;
        return this.sortedUsers.slice((currentPage - 1) * perPage, currentPage * perPage);
    }
    get filteredUsers() {
        if (!this.searchTerms) {
            return this.gradeBook.users;
        }
        return this.gradeBook.users.filter(user => {
            const fullName = user.firstName.toLowerCase() + ' ' + user.lastName.toLowerCase();
            return this.searchTerms.every(term => fullName.indexOf(term) !== -1);
        });
    }
    get sortedUsers() {
        let field;
        if (this.sortBy === 'lastname') {
            field = 'lastName';
        }
        else if (this.sortBy === 'firstname') {
            field = 'firstName';
        }
        else {
            return this.filteredUsers;
        }
        const users = [...this.filteredUsers];
        const mul = this.sortDesc ? -1 : 1;
        users.sort((u1, u2) => {
            if (u1[field] > u2[field]) {
                return 1 * mul;
            }
            if (u1[field] < u2[field]) {
                return -1 * mul;
            }
            return 0;
        });
        return users;
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
    resetDialogs() {
        this.editItemId = null;
        this.catEditItemId = null;
        this.weightEditItemId = null;
        this.editStudentScoreId = null;
        this.editScoreId = null;
        this.editDisplayTotalDialog = false;
    }
    showCategorySettings(categoryId) {
        this.resetDialogs();
        this.$emit('category-settings', categoryId);
    }
    showColumnSettings(columnId) {
        this.resetDialogs();
        this.$emit('item-settings', columnId);
    }
    showFinalScoreSettings() {
        this.resetDialogs();
        this.$emit('final-score-settings');
    }
    showCategoryTitleDialog(categoryId) {
        this.resetDialogs();
        this.catEditItemId = categoryId;
    }
    showColumnTitleDialog(columnId) {
        this.resetDialogs();
        this.editItemId = columnId;
    }
    showColumnWeightDialog(columnId) {
        this.resetDialogs();
        this.weightEditItemId = columnId;
    }
    showFinalScoreDialog() {
        this.resetDialogs();
        this.editDisplayTotalDialog = true;
    }
    showStudentScoreDialog(userId, itemId, menuTab = 'score') {
        this.resetDialogs();
        this.scoreMenuTab = menuTab;
        this.editStudentScoreId = userId;
        this.editScoreId = itemId;
    }
    hideStudentScoreDialog() {
        this.editStudentScoreId = null;
        this.editScoreId = null;
    }
    getColumnData(columnId) {
        const gradeBook = this.gradeBook;
        const column = gradeBook.getGradeColumn(columnId);
        if (!column) {
            throw new Error(`GradeColumn with id ${columnId} not found.`);
        }
        return {
            id: columnId,
            released: column.released,
            countsForEndResult: column.countForEndResult,
            isGrouped: column.type === 'group',
            title: gradeBook.getTitle(column),
            hasWeightSet: column.weight !== null,
            weight: gradeBook.getWeight(column),
            hasRemovedSourceData: gradeBook.hasRemovedSourceData(column),
            isEditingTitle: this.editItemId === columnId,
            isEditingWeight: this.weightEditItemId === columnId,
            isEditing: this.editItemId === columnId || this.weightEditItemId === columnId,
            isSaving: this.isSavingColumnWithId(columnId)
        };
    }
    getColumns(category) {
        return category.columnIds.map(columnId => this.getColumnData(columnId));
    }
    setCategoryTitle(id, title) {
        const category = this.gradeBook.getCategory(id);
        if (category) {
            category.title = title;
            this.$emit('change-category', category);
        }
        this.catEditItemId = null;
    }
    setTitle(columnId, title) {
        const gradeColumn = this.gradeBook.getGradeColumn(columnId);
        if (gradeColumn) {
            this.gradeBook.setTitle(columnId, title);
            this.$emit('change-gradecolumn', gradeColumn);
        }
        this.editItemId = null;
    }
    setWeight(columnId, weight) {
        const gradeColumn = this.gradeBook.getGradeColumn(columnId);
        if (gradeColumn) {
            this.gradeBook.setWeight(columnId, weight);
            this.$emit('change-gradecolumn', gradeColumn);
        }
        this.weightEditItemId = null;
    }
    setDisplayTotal(displayTotal) {
        this.gradeBook.displayTotal = displayTotal;
        this.$emit('change-display-total');
        this.editDisplayTotalDialog = false;
    }
    toggleVisibility(columnId) {
        const gradeColumn = this.gradeBook.getGradeColumn(columnId);
        if (gradeColumn) {
            gradeColumn.released = !gradeColumn.released;
            this.$emit('change-gradecolumn', gradeColumn);
        }
    }
    overwriteResult(userId, { columnId, value }) {
        const score = this.gradeBook.overwriteResult(columnId, userId, value);
        if (!score) {
            return;
        }
        this.$emit('overwrite-result', score);
        this.hideStudentScoreDialog();
    }
    revertOverwrittenResult(userId, columnId) {
        const score = this.gradeBook.revertOverwrittenResult(columnId, userId);
        if (!score) {
            return;
        }
        this.$emit('revert-overwritten-result', score);
        this.hideStudentScoreDialog();
    }
    updateResultComment(userId, { columnId, comment }) {
        const score = this.gradeBook.updateResultComment(columnId, userId, comment);
        if (!score) {
            return;
        }
        this.$emit('update-score-comment', score);
        this.hideStudentScoreDialog();
    }
    isSavingColumnWithId(columnId) {
        return this.saveColumnId === columnId;
    }
    isSavingCategoryWithId(categoryId) {
        return this.saveCategoryId === categoryId;
    }
    startDragColumn(evt, id) {
        if (!evt.dataTransfer) {
            return;
        }
        evt.dataTransfer.setData('__COLUMN_ID', JSON.stringify({ id }));
        this.isDraggingColumn = true;
    }
    startDragCategory(evt, id) {
        if (!evt.dataTransfer) {
            return;
        }
        evt.dataTransfer.setData('__CATEGORY_ID', JSON.stringify({ id }));
        this.isDraggingCategory = true;
    }
    onDropAreaOverEnter(evt, index) {
        if (!evt.dataTransfer) {
            return;
        }
        this.categoryDropArea = index;
        evt.dataTransfer.dropEffect = 'move';
        evt.dataTransfer.effectAllowed = 'copyMove';
    }
    onDragEnd() {
        this.categoryDropArea = null;
        this.isDraggingColumn = false;
        this.isDraggingColumn = false;
    }
    onDrop(evt, categoryId) {
        if (!evt.dataTransfer) {
            return;
        }
        if (this.isDraggingColumn) {
            const id = JSON.parse(evt.dataTransfer.getData('__COLUMN_ID')).id;
            if (categoryId === -1) {
                window.setTimeout(() => {
                    this.$emit('move-gradecolumn', this.gradeBook.getGradeColumn(id));
                });
            }
            else {
                this.gradeBook.addItemToCategory(categoryId, id);
                this.$emit('change-gradecolumn-category', this.gradeBook.getGradeColumn(id), categoryId || null);
            }
        }
        else if (this.isDraggingCategory) {
            const id = JSON.parse(evt.dataTransfer.getData('__CATEGORY_ID')).id;
            window.setTimeout(() => {
                this.$emit('move-category', this.gradeBook.getCategory(id));
            }, 200);
        }
    }
    onShowNullCategoryChange(showNullCategory) {
        if (showNullCategory) {
            window.setTimeout(() => {
                document.querySelector('.table-wrap')?.scrollBy(21, 0);
            }, 100);
        }
    }
};
__decorate([
    Prop({ type: GradeBook, required: true })
], GradesTable.prototype, "gradeBook", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], GradesTable.prototype, "searchTerms", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], GradesTable.prototype, "busy", void 0);
__decorate([
    Prop({ type: [String, Number], default: null })
], GradesTable.prototype, "addColumnId", void 0);
__decorate([
    Prop({ type: [String, Number], default: null })
], GradesTable.prototype, "saveColumnId", void 0);
__decorate([
    Prop({ type: Number, default: null })
], GradesTable.prototype, "saveCategoryId", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], GradesTable.prototype, "saveDisplayTotal", void 0);
__decorate([
    Prop({ type: Number, default: 5 })
], GradesTable.prototype, "itemsPerPage", void 0);
__decorate([
    Prop({ type: String, default: '' })
], GradesTable.prototype, "gradeBookRootUrl", void 0);
__decorate([
    Watch('showNullCategory')
], GradesTable.prototype, "onShowNullCategoryChange", null);
GradesTable = __decorate([
    vue_class_component_esm({
        name: 'grades-table',
        components: { StudentResultRow: components_StudentResultRow, ItemTitleInput: components_ItemTitleInput, WeightInput: components_WeightInput, DisplayTotalInput: DisplayTotalInput, ScoreInput: components_ScoreInput, StudentResult: components_StudentResult, draggable: (vuedraggable_umd_default()) },
        filters: {
            formatNum: function (v) {
                if (v === null) {
                    return '';
                }
                return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], GradesTable);
/* harmony default export */ const GradesTablevue_type_script_lang_ts = (GradesTable);

;// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_GradesTablevue_type_script_lang_ts = (GradesTablevue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesTable.vue?vue&type=style&index=0&id=a94e1dae&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=style&index=0&id=a94e1dae&prod&lang=scss&scoped=true

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesTable.vue?vue&type=style&index=1&id=a94e1dae&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=style&index=1&id=a94e1dae&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/GradesTable.vue?vue&type=custom&index=0&blockType=i18n
var GradesTablevue_type_custom_index_0_blockType_i18n = __webpack_require__(900);
var GradesTablevue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(GradesTablevue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_GradesTablevue_type_custom_index_0_blockType_i18n = ((GradesTablevue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/GradesTable.vue



;



/* normalize component */

var GradesTable_component = normalizeComponent(
  components_GradesTablevue_type_script_lang_ts,
  GradesTablevue_type_template_id_a94e1dae_scoped_true_render,
  GradesTablevue_type_template_id_a94e1dae_scoped_true_staticRenderFns,
  false,
  null,
  "a94e1dae",
  null
  
)

/* custom blocks */
;
if (typeof components_GradesTablevue_type_custom_index_0_blockType_i18n === 'function') components_GradesTablevue_type_custom_index_0_blockType_i18n(GradesTable_component)

/* harmony default export */ const components_GradesTable = (GradesTable_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemSettings.vue?vue&type=template&id=768c35ba&scoped=true
var ItemSettingsvue_type_template_id_768c35ba_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"modal-wrapper",attrs:{"role":"dialog","aria-modal":"true","aria-label":_vm.$t('column-settings')}},[_c('div',{staticClass:"modal-content"},[_c('div',{staticClass:"u-flex u-justify-content-between modal-header"},[_c('div',[_c('input',{ref:"column-title",attrs:{"type":"text","autocomplete":"off"},domProps:{"value":_vm.title},on:{"input":function($event){_vm.title = $event}}}),_c('button',{staticClass:"btn btn-link",on:{"click":function($event){_vm.showRemoveItemDialog = true}}},[_vm._v(_vm._s(_vm.$t('remove')))])]),_c('button',{staticClass:"btn-close u-ml-auto",attrs:{"title":_vm.$t('close')},on:{"click":function($event){return _vm.$emit('close')}}},[_c('i',{staticClass:"fa fa-times",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('close')))])])]),_c('div',{staticClass:"modal-body"},[(_vm.column.type !== 'standalone')?[(_vm.isGrouped)?_c('h5',[_vm._v(_vm._s(_vm.$t('grouped-scores')))]):_vm._e(),_c('ul',{staticClass:"grouped-scores"},_vm._l((_vm.subItems),function(item){return _c('li',{key:item.id},[_c('span',[_vm._v(_vm._s(item.title))]),_c('div',{staticClass:"score-breadcrumb-trail"},[_vm._v(_vm._s(_vm._f("breadcrumb")(item)))])])}),0),_c('div',{staticClass:"ml-20"},[(!(_vm.isGrouped || _vm.groupButtonPressed))?_c('button',{staticClass:"btn btn-default",on:{"click":_vm.openGradesDropdown}},[_vm._v(_vm._s(_vm.$t('group-scores')))]):_c('grades-dropdown',{ref:"dropdown",attrs:{"id":"dropdown-settings","graded-items":_vm.gradedItems},on:{"toggle":_vm.toggleSubItem}})],1)]:_vm._e(),_c('h5',{class:{'standalone': _vm.column.type === 'standalone'}},[_vm._v(_vm._s(_vm.$t('settings')))]),_c('div',{staticClass:"settings"},[_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.column.released),expression:"column.released"}],staticClass:"mr-05",attrs:{"type":"checkbox","id":"released"},domProps:{"checked":Array.isArray(_vm.column.released)?_vm._i(_vm.column.released,null)>-1:(_vm.column.released)},on:{"input":_vm.onGradeColumnChange,"change":function($event){var $$a=_vm.column.released,$$el=$event.target,$$c=$$el.checked?(true):(false);if(Array.isArray($$a)){var $$v=null,$$i=_vm._i($$a,$$v);if($$el.checked){$$i<0&&(_vm.$set(_vm.column, "released", $$a.concat([$$v])))}else{$$i>-1&&(_vm.$set(_vm.column, "released", $$a.slice(0,$$i).concat($$a.slice($$i+1))))}}else{_vm.$set(_vm.column, "released", $$c)}}}}),_c('label',{staticClass:"settings-label u-font-medium",attrs:{"for":"released"}},[_vm._v(_vm._s(_vm.$t('make-visible')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.column.countForEndResult),expression:"column.countForEndResult"}],staticClass:"mr-05",attrs:{"type":"checkbox","id":"countForEndResult"},domProps:{"checked":Array.isArray(_vm.column.countForEndResult)?_vm._i(_vm.column.countForEndResult,null)>-1:(_vm.column.countForEndResult)},on:{"input":_vm.onGradeColumnChange,"change":function($event){var $$a=_vm.column.countForEndResult,$$el=$event.target,$$c=$$el.checked?(true):(false);if(Array.isArray($$a)){var $$v=null,$$i=_vm._i($$a,$$v);if($$el.checked){$$i<0&&(_vm.$set(_vm.column, "countForEndResult", $$a.concat([$$v])))}else{$$i>-1&&(_vm.$set(_vm.column, "countForEndResult", $$a.slice(0,$$i).concat($$a.slice($$i+1))))}}else{_vm.$set(_vm.column, "countForEndResult", $$c)}}}}),_c('label',{staticClass:"settings-label u-font-medium",attrs:{"for":"countForEndResult"}},[_vm._v(_vm._s(_vm.$t('count-towards-endresult')))])]),(_vm.column.countForEndResult)?_c('div',[_c('div',{staticClass:"mt-10"},[_c('label',{staticClass:"settings-label u-block",attrs:{"for":"weight"}},[_vm._v(_vm._s(_vm.$t('weight'))+":")]),_c('div',{staticClass:"number-input u-relative"},[_c('input',{attrs:{"type":"number","id":"weight","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.gradeBook.getWeight(_vm.column))},on:{"input":_vm.setWeight}}),_vm._m(0)])]),_c('div',{staticClass:"mt-20",attrs:{"role":"radiogroup","aria-labelledby":"setting-aabs"}},[_c('label',{staticClass:"settings-label",attrs:{"id":"setting-aabs"}},[_vm._v(_vm._s(_vm.$t('authorized-absence'))+" "),_c('div',{staticClass:"color-code amber-700 mx-03",attrs:{"aria-hidden":"true","title":_vm.$t('auth-absent')}},[_c('span',[_vm._v(_vm._s(_vm.$t('aabs')))])]),_vm._v(":")]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.authPresenceEndResult),expression:"column.authPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-05",attrs:{"type":"radio","name":"gafw-option","id":"gafw-option1","value":"0"},domProps:{"checked":_vm._q(_vm.column.authPresenceEndResult,_vm._n("0"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "authPresenceEndResult", _vm._n("0"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"gafw-option1"}},[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.authPresenceEndResult),expression:"column.authPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-05",attrs:{"type":"radio","name":"gafw-option","id":"gafw-option2","value":"1"},domProps:{"checked":_vm._q(_vm.column.authPresenceEndResult,_vm._n("1"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "authPresenceEndResult", _vm._n("1"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"gafw-option2"}},[_vm._v(_vm._s(_vm.$t('maximum-towards-endresult')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.authPresenceEndResult),expression:"column.authPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-05",attrs:{"type":"radio","name":"gafw-option","id":"gafw-option3","value":"2"},domProps:{"checked":_vm._q(_vm.column.authPresenceEndResult,_vm._n("2"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "authPresenceEndResult", _vm._n("2"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"gafw-option3"}},[_vm._v(_vm._s(_vm.$t('minimum-towards-endresult')))])])]),_c('div',{staticClass:"mt-20",attrs:{"role":"radiogroup","aria-labelledby":"setting-uaabs"}},[_c('label',{staticClass:"settings-label",attrs:{"id":"setting-uaabs"}},[_vm._v(_vm._s(_vm.$t('unauthorized-absence'))+" "),_c('div',{staticClass:"color-code mod-none mx-03",attrs:{"title":_vm.$t('no-score-found')}},[_c('i',{staticClass:"fa fa-question",attrs:{"aria-hidden":"true"}})]),_vm._v(":")]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.unauthPresenceEndResult),expression:"column.unauthPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-05",attrs:{"type":"radio","name":"nogafw-option","id":"nogafw-option1","value":"0"},domProps:{"checked":_vm._q(_vm.column.unauthPresenceEndResult,_vm._n("0"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "unauthPresenceEndResult", _vm._n("0"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"nogafw-option1"}},[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.unauthPresenceEndResult),expression:"column.unauthPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-05",attrs:{"type":"radio","name":"nogafw-option","id":"nogafw-option2","value":"1"},domProps:{"checked":_vm._q(_vm.column.unauthPresenceEndResult,_vm._n("1"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "unauthPresenceEndResult", _vm._n("1"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"nogafw-option2"}},[_vm._v(_vm._s(_vm.$t('maximum-towards-endresult')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.unauthPresenceEndResult),expression:"column.unauthPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-05",attrs:{"type":"radio","name":"nogafw-option","id":"nogafw-option3","value":"2"},domProps:{"checked":_vm._q(_vm.column.unauthPresenceEndResult,_vm._n("2"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "unauthPresenceEndResult", _vm._n("2"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"nogafw-option3"}},[_vm._v(_vm._s(_vm.$t('minimum-towards-endresult')))])])])]):_vm._e()])],2)]),_c('div',{staticClass:"modal-overlay",on:{"click":function($event){return _vm.$emit('close')}}}),(_vm.showRemoveItemDialog)?_c('div',{staticClass:"modal-remove",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center modal-remove-content"},[_c('div',[_vm._v(_vm._s(_vm.$t('remove-from-overview', {title: _vm.title})))]),_c('div',{staticClass:"u-flex modal-remove-actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.removeColumn}},[_vm._v(_vm._s(_vm.$t('remove')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])]):_vm._e()])
}
var ItemSettingsvue_type_template_id_768c35ba_scoped_true_staticRenderFns = [function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"percent"},[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])
}]


;// CONCATENATED MODULE: ./src/components/ItemSettings.vue?vue&type=template&id=768c35ba&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/debounce/index.js
var debounce = __webpack_require__(678);
var debounce_default = /*#__PURE__*/__webpack_require__.n(debounce);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemSettings.vue?vue&type=script&lang=ts





let ItemSettings = class ItemSettings extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    groupButtonPressed = false;
    showRemoveItemDialog = false;
    gradeBook;
    columnId;
    constructor() {
        super();
        this.onGradeColumnChange = debounce_default()(this.onGradeColumnChange, 750);
    }
    openGradesDropdown() {
        this.groupButtonPressed = true;
        window.setTimeout(() => { this.$refs['dropdown'].open(); }, 100);
    }
    get column() {
        return this.gradeBook.getGradeColumn(this.columnId);
    }
    get isGrouped() {
        return this.column?.type === 'group';
    }
    get subItems() {
        return this.column ? this.gradeBook.getColumnSubItems(this.column) : [];
    }
    get title() {
        return this.column ? this.gradeBook.getTitle(this.column) : '';
    }
    set title(event) {
        this.gradeBook.setTitle(this.columnId, event.target.value);
        this.onGradeColumnChange();
    }
    setWeight(event) {
        const weight = parseFloat(event.target.value);
        this.gradeBook.setWeight(this.columnId, isNaN(weight) ? null : weight);
        this.onGradeColumnChange();
    }
    get gradedItems() {
        return this.gradeBook.getStatusGradedItemsByColumn(this.columnId);
    }
    toggleSubItem(gradeItem, isAdding) {
        const item = this.gradeBook.gradeItems.find(item => item.id === gradeItem.id);
        if (isAdding) {
            this.addSubItem(item);
        }
        else {
            this.removeSubItem(item);
        }
    }
    addSubItem(item) {
        this.gradeBook.addSubItem(item, this.columnId);
        this.$emit('add-subitem', item, this.columnId);
    }
    removeSubItem(item) {
        this.gradeBook.removeSubItem(item);
        this.$emit('remove-subitem', item, this.columnId);
        if (item.id === this.columnId) {
            this.$emit('close');
        }
    }
    removeColumn() {
        const column = this.column;
        if (column) {
            this.gradeBook.removeColumn(column);
            this.$emit('remove-column', column);
        }
        this.showRemoveItemDialog = false;
        this.$emit('close');
    }
    cancel() {
        this.showRemoveItemDialog = false;
    }
    onGradeColumnChange() {
        this.$emit('change-gradecolumn', this.column);
    }
    mounted() {
        this.$refs['column-title'].focus();
    }
};
__decorate([
    Prop({ type: GradeBook, required: true })
], ItemSettings.prototype, "gradeBook", void 0);
__decorate([
    Prop({ type: [String, Number] })
], ItemSettings.prototype, "columnId", void 0);
ItemSettings = __decorate([
    vue_class_component_esm({
        components: { GradesDropdown: components_GradesDropdown },
        filters: {
            formatNum: function (v) {
                if (v === null) {
                    return '';
                }
                return v.toLocaleString(undefined, { maximumFractionDigits: 2 });
            },
            breadcrumb: function (gradedItem) {
                return gradedItem.breadcrumb.join(' » ');
            }
        }
    })
], ItemSettings);
/* harmony default export */ const ItemSettingsvue_type_script_lang_ts = (ItemSettings);

;// CONCATENATED MODULE: ./src/components/ItemSettings.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_ItemSettingsvue_type_script_lang_ts = (ItemSettingsvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemSettings.vue?vue&type=style&index=0&id=768c35ba&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/ItemSettings.vue?vue&type=style&index=0&id=768c35ba&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ItemSettings.vue?vue&type=custom&index=0&blockType=i18n
var ItemSettingsvue_type_custom_index_0_blockType_i18n = __webpack_require__(980);
var ItemSettingsvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(ItemSettingsvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/ItemSettings.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_ItemSettingsvue_type_custom_index_0_blockType_i18n = ((ItemSettingsvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/ItemSettings.vue



;


/* normalize component */

var ItemSettings_component = normalizeComponent(
  components_ItemSettingsvue_type_script_lang_ts,
  ItemSettingsvue_type_template_id_768c35ba_scoped_true_render,
  ItemSettingsvue_type_template_id_768c35ba_scoped_true_staticRenderFns,
  false,
  null,
  "768c35ba",
  null
  
)

/* custom blocks */
;
if (typeof components_ItemSettingsvue_type_custom_index_0_blockType_i18n === 'function') components_ItemSettingsvue_type_custom_index_0_blockType_i18n(ItemSettings_component)

/* harmony default export */ const components_ItemSettings = (ItemSettings_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CategorySettings.vue?vue&type=template&id=727f84bc&scoped=true
var CategorySettingsvue_type_template_id_727f84bc_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"modal-wrapper",attrs:{"role":"dialog","aria-modal":"true","aria-label":_vm.$t('category-settings')}},[_c('div',{staticClass:"modal-content"},[_c('div',{staticClass:"u-flex u-justify-content-between modal-header"},[_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.category.title),expression:"category.title"}],ref:"category-title",attrs:{"type":"text","autocomplete":"off"},domProps:{"value":(_vm.category.title)},on:{"input":[function($event){if($event.target.composing)return;_vm.$set(_vm.category, "title", $event.target.value)},_vm.onCategoryChange]}}),_c('button',{staticClass:"btn btn-link",on:{"click":function($event){_vm.showRemoveItemDialog = true}}},[_vm._v(_vm._s(_vm.$t('remove')))])]),_c('button',{staticClass:"btn-close u-ml-auto",attrs:{"title":_vm.$t('close')},on:{"click":function($event){return _vm.$emit('close')}}},[_c('i',{staticClass:"fa fa-times",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('close')))])])]),_c('div',{staticClass:"modal-body"},[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.category.color),expression:"category.color"}],attrs:{"type":"color"},domProps:{"value":(_vm.category.color)},on:{"input":[function($event){if($event.target.composing)return;_vm.$set(_vm.category, "color", $event.target.value)},_vm.onCategoryChange]}})])]),_c('div',{staticClass:"modal-overlay",on:{"click":function($event){return _vm.$emit('close')}}}),(_vm.showRemoveItemDialog)?_c('div',{staticClass:"modal-remove",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center modal-remove-content"},[_c('div',[_vm._v(_vm._s(_vm.$t('remove-category')))]),_c('div',{staticClass:"u-flex modal-remove-actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.removeCategory}},[_vm._v(_vm._s(_vm.$t('remove')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])]):_vm._e()])
}
var CategorySettingsvue_type_template_id_727f84bc_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CategorySettings.vue?vue&type=script&lang=ts




let CategorySettings = class CategorySettings extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    showRemoveItemDialog = false;
    gradeBook;
    category;
    constructor() {
        super();
        this.onCategoryChange = debounce_default()(this.onCategoryChange, 750);
    }
    onCategoryChange() {
        this.$emit('change-category', this.category);
    }
    removeCategory() {
        const category = this.category;
        this.gradeBook.removeCategory(category);
        this.$emit('remove-category', category);
        this.showRemoveItemDialog = false;
        this.$emit('close');
    }
    cancel() {
        this.showRemoveItemDialog = false;
    }
    mounted() {
        this.$refs['category-title'].focus();
    }
};
__decorate([
    Prop({ type: GradeBook, required: true })
], CategorySettings.prototype, "gradeBook", void 0);
__decorate([
    Prop({ type: Object, required: true })
], CategorySettings.prototype, "category", void 0);
CategorySettings = __decorate([
    vue_class_component_esm({
        components: {}
    })
], CategorySettings);
/* harmony default export */ const CategorySettingsvue_type_script_lang_ts = (CategorySettings);

;// CONCATENATED MODULE: ./src/components/CategorySettings.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_CategorySettingsvue_type_script_lang_ts = (CategorySettingsvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CategorySettings.vue?vue&type=style&index=0&id=727f84bc&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/CategorySettings.vue?vue&type=style&index=0&id=727f84bc&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CategorySettings.vue?vue&type=custom&index=0&blockType=i18n
var CategorySettingsvue_type_custom_index_0_blockType_i18n = __webpack_require__(169);
var CategorySettingsvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(CategorySettingsvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/CategorySettings.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_CategorySettingsvue_type_custom_index_0_blockType_i18n = ((CategorySettingsvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/CategorySettings.vue



;


/* normalize component */

var CategorySettings_component = normalizeComponent(
  components_CategorySettingsvue_type_script_lang_ts,
  CategorySettingsvue_type_template_id_727f84bc_scoped_true_render,
  CategorySettingsvue_type_template_id_727f84bc_scoped_true_staticRenderFns,
  false,
  null,
  "727f84bc",
  null
  
)

/* custom blocks */
;
if (typeof components_CategorySettingsvue_type_custom_index_0_blockType_i18n === 'function') components_CategorySettingsvue_type_custom_index_0_blockType_i18n(CategorySettings_component)

/* harmony default export */ const components_CategorySettings = (CategorySettings_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/FinalScoreSettings.vue?vue&type=template&id=dea65ebe&scoped=true
var FinalScoreSettingsvue_type_template_id_dea65ebe_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"modal-wrapper",attrs:{"role":"dialog","aria-modal":"true","aria-label":_vm.$t('final-score-settings')}},[_c('div',{staticClass:"modal-content"},[_c('div',{staticClass:"u-flex u-align-items-baseline u-justify-content-between modal-header"},[_c('h4',{staticStyle:{"font-size":"1.25rem","margin-block":"0"}},[_vm._v(_vm._s(_vm.$t('final-score')))]),_c('button',{staticClass:"btn-close u-ml-auto",attrs:{"title":_vm.$t('close')},on:{"click":function($event){return _vm.$emit('close')}}},[_c('i',{staticClass:"fa fa-times",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('close')))])])]),_c('div',{staticClass:"modal-body mb-5"},[_c('div',{staticClass:"mb-10"},[_c('label',{staticClass:"settings-label u-block",attrs:{"for":"display-total"}},[_vm._v(_vm._s(_vm.$t('display-total'))+":")]),_c('div',{staticClass:"number-input u-relative"},[_c('input',{attrs:{"type":"number","placeholder":"100","step":"1","id":"display-total","autocomplete":"off"},domProps:{"value":_vm.gradeBook.displayTotal},on:{"input":_vm.setDisplayTotal}})])])])]),_c('div',{staticClass:"modal-overlay",on:{"click":function($event){return _vm.$emit('close')}}})])
}
var FinalScoreSettingsvue_type_template_id_dea65ebe_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/FinalScoreSettings.vue?vue&type=script&lang=ts



let FinalScoreSettings = class FinalScoreSettings extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    gradeBook;
    setDisplayTotal(event) {
        const displayTotal = parseFloat(event.target.value);
        this.gradeBook.displayTotal = isNaN(displayTotal) ? null : displayTotal;
        this.$emit('change-display-total');
    }
};
__decorate([
    Prop({ type: GradeBook, required: true })
], FinalScoreSettings.prototype, "gradeBook", void 0);
FinalScoreSettings = __decorate([
    vue_class_component_esm({
        components: {}
    })
], FinalScoreSettings);
/* harmony default export */ const FinalScoreSettingsvue_type_script_lang_ts = (FinalScoreSettings);

;// CONCATENATED MODULE: ./src/components/FinalScoreSettings.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_FinalScoreSettingsvue_type_script_lang_ts = (FinalScoreSettingsvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/FinalScoreSettings.vue?vue&type=style&index=0&id=dea65ebe&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/FinalScoreSettings.vue?vue&type=style&index=0&id=dea65ebe&prod&lang=scss&scoped=true

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/FinalScoreSettings.vue?vue&type=custom&index=0&blockType=i18n
var FinalScoreSettingsvue_type_custom_index_0_blockType_i18n = __webpack_require__(802);
var FinalScoreSettingsvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(FinalScoreSettingsvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/FinalScoreSettings.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_FinalScoreSettingsvue_type_custom_index_0_blockType_i18n = ((FinalScoreSettingsvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/FinalScoreSettings.vue



;


/* normalize component */

var FinalScoreSettings_component = normalizeComponent(
  components_FinalScoreSettingsvue_type_script_lang_ts,
  FinalScoreSettingsvue_type_template_id_dea65ebe_scoped_true_render,
  FinalScoreSettingsvue_type_template_id_dea65ebe_scoped_true_staticRenderFns,
  false,
  null,
  "dea65ebe",
  null
  
)

/* custom blocks */
;
if (typeof components_FinalScoreSettingsvue_type_custom_index_0_blockType_i18n === 'function') components_FinalScoreSettingsvue_type_custom_index_0_blockType_i18n(FinalScoreSettings_component)

/* harmony default export */ const components_FinalScoreSettings = (FinalScoreSettings_component.exports);
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
;// CONCATENATED MODULE: ./src/domain/Log.ts
function logResponse(data) {
    const responseEl = document.getElementById('server-response');
    if (!responseEl) {
        return;
    }
    responseEl.innerHTML = typeof data === 'object' ? JSON.stringify(data, null, 4) : `<div>An error occurred:</div>${data}`;
}

;// CONCATENATED MODULE: ./src/connector/Connector.ts



const HTTP_FORBIDDEN = 403;
const HTTP_NOT_FOUND = 404;
const HTTP_CONFLICT = 409;
const ERROR_UNKNOWN = 'UNKNOWN';
const TIMEOUT_SEC = 30;
function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
class Connector {
    apiConfig;
    queue = new dist/* default */.Z({ concurrency: 1 });
    gradebookDataId;
    currentVersion;
    _isSaving = false;
    errorListeners = [];
    constructor(apiConfig, gradebookDataId, currentVersion) {
        this.apiConfig = apiConfig;
        this.gradebookDataId = gradebookDataId;
        this.currentVersion = currentVersion;
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
    static async loadGradeBookData(loadAllURL, csrfToken) {
        const params = csrfToken ? { '_csrf_token': csrfToken } : {};
        const res = await lib_axios.get(loadAllURL, { params });
        return res.data;
    }
    addCategory(category, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category)
            };
            const data = await this.executeAPIRequest(this.apiConfig.addCategoryURL, parameters);
            callback(data.category);
        });
    }
    updateCategory(category, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category)
            };
            await this.executeAPIRequest(this.apiConfig.updateCategoryURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    moveCategory(category, newIndex, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category),
                'newSort': newIndex + 1
            };
            await this.executeAPIRequest(this.apiConfig.moveCategoryURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    removeCategory(category, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category)
            };
            await this.executeAPIRequest(this.apiConfig.removeCategoryURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    addGradeColumn(gradeColumn, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnData': JSON.stringify(gradeColumn)
            };
            const data = await this.executeAPIRequest(this.apiConfig.addColumnURL, parameters);
            callback(data.column, data.scores);
        });
    }
    addColumnSubItem(gradeColumnId, gradeItemId, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumnId,
                'gradeItemId': gradeItemId
            };
            const data = await this.executeAPIRequest(this.apiConfig.addColumnSubItemURL, parameters);
            callback(data.column, data.scores);
        });
    }
    removeColumnSubItem(gradeColumnId, gradeItemId, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumnId,
                'gradeItemId': gradeItemId
            };
            const data = await this.executeAPIRequest(this.apiConfig.removeColumnSubItemURL, parameters);
            callback(data.column, data.scores);
        });
    }
    updateGradeColumn(gradeColumn, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnData': JSON.stringify(gradeColumn)
            };
            await this.executeAPIRequest(this.apiConfig.updateColumnURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    updateGradeColumnCategory(gradeColumn, categoryId, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumn.id,
                'categoryId': categoryId
            };
            await this.executeAPIRequest(this.apiConfig.updateColumnCategoryURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    moveGradeColumn(gradeColumn, newIndex, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumn.id,
                'newSort': newIndex + 1
            };
            await this.executeAPIRequest(this.apiConfig.moveColumnURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    removeGradeColumn(gradeColumn, callback = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumn.id
            };
            await this.executeAPIRequest(this.apiConfig.removeColumnURL, parameters);
            if (callback) {
                callback();
            }
        });
    }
    synchronizeGradeBook(callback) {
        this.addToQueue(async () => {
            const data = await this.executeAPIRequest(this.apiConfig.synchronizeGradeBookURL);
            callback(data.scores);
        });
        /*        return new Promise(resolve => {
                    this.addToQueue(async () => {
                        const data = await this.executeAPIRequest(this.apiConfig.synchronizeGradeBookURL);
                        resolve(data);
                    });
                })*/
    }
    overwriteGradeResult(result, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeScoreId': result.id,
                'newScore': result.newScore,
                'newScoreAuthAbsent': result.newScoreAuthAbsent
            };
            const data = await this.executeAPIRequest(this.apiConfig.overwriteScoreURL, parameters);
            callback(data.score);
        });
    }
    revertOverwrittenGradeResult(result, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeScoreId': result.id
            };
            const data = await this.executeAPIRequest(this.apiConfig.revertOverwrittenScoreURL, parameters);
            callback(data.score);
        });
    }
    updateGradeResultComment(result, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeScoreId': result.id,
                'comment': result.comment
            };
            const data = await this.executeAPIRequest(this.apiConfig.updateScoreCommentURL, parameters);
            callback(data.score);
        });
    }
    calculateTotalScores(callback) {
        this.addToQueue(async () => {
            const data = await this.executeAPIRequest(this.apiConfig.calculateTotalScoresURL);
            callback(data.totalScores);
        });
    }
    updateDisplayTotal(displayTotal, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'displayTotal': displayTotal
            };
            await this.executeAPIRequest(this.apiConfig.updateDisplayTotalURL, parameters);
            callback();
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
        parameters['gradebookDataId'] = this.gradebookDataId;
        parameters['version'] = this.currentVersion;
        const formData = new FormData();
        if (this.apiConfig.csrfToken) {
            formData.set('_csrf_token', this.apiConfig.csrfToken);
        }
        for (const [key, value] of Object.entries(parameters)) {
            formData.set(key, value);
        }
        try {
            const res = await lib_axios.post(apiURL, formData, { timeout: TIMEOUT_SEC * 1000 });
            logResponse(res.data);
            if (typeof res.data === 'object') {
                this.gradebookDataId = res.data.gradebook.dataId;
                this.currentVersion = res.data.gradebook.version;
                return res.data;
            }
            else if (typeof res.data === 'string' && res.data.indexOf('login') !== -1) {
                throw { 'type': 'LoggedOut' };
            }
            else {
                throw { 'type': 'Unknown' };
            }
        }
        catch (err) {
            logResponse(err);
            let error;
            if (err?.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
                error = { 'type': 'Timeout' };
            }
            else if ([HTTP_FORBIDDEN, HTTP_NOT_FOUND, HTTP_CONFLICT, ERROR_UNKNOWN].includes(err?.response?.status)) {
                const status = err.response.status;
                if (status === HTTP_FORBIDDEN) {
                    error = { 'type': 'Forbidden' };
                }
                else if (status === HTTP_NOT_FOUND) {
                    error = { 'type': 'NotFound' };
                }
                else if (status === HTTP_CONFLICT) {
                    error = { 'type': 'Conflict' };
                }
                else {
                    error = { 'type': 'Unknown' };
                }
            }
            else if (err?.response?.data?.error) {
                error = err.response.data.error;
            }
            else if (err?.type) {
                error = err;
            }
            else {
                error = { 'type': 'Unknown' };
            }
            this.errorListeners.forEach(errorListener => errorListener.setError(error));
        }
    }
}

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=template&id=00a31407&scoped=true
var ErrorDisplayvue_type_template_id_00a31407_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{staticClass:"u-contents",attrs:{"role":"alertdialog","aria-modal":"true","aria-label":_vm.$t('errors')}},[_c('div',{staticClass:"modal-overlay"}),_c('div',{staticClass:"save-error u-flex u-justify-content-center"},[_c('div',{staticClass:"save-error-inner"},[_c('div',{staticClass:"errors-important u-flex u-align-items-baseline"},[_c('i',{staticClass:"fa fa-exclamation-circle mod-icon",attrs:{"aria-hidden":"true"}}),_c('div',[_vm._t("default"),_c('div',{staticClass:"u-text-center mt-5"},[_c('button',{ref:"btn-ok",staticClass:"btn btn-success btn-sm",on:{"click":function($event){return _vm.$emit('close')}}},[_vm._v("OK")])])],2)])])])])
}
var ErrorDisplayvue_type_template_id_00a31407_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=script&lang=ts


let ErrorDisplay = class ErrorDisplay extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    mounted() {
        this.$refs['btn-ok'].focus();
    }
};
ErrorDisplay = __decorate([
    vue_class_component_esm({
        name: 'error-display'
    })
], ErrorDisplay);
/* harmony default export */ const ErrorDisplayvue_type_script_lang_ts = (ErrorDisplay);

;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_ErrorDisplayvue_type_script_lang_ts = (ErrorDisplayvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=style&index=0&id=00a31407&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=style&index=0&id=00a31407&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=custom&index=0&blockType=i18n
var ErrorDisplayvue_type_custom_index_0_blockType_i18n = __webpack_require__(820);
var ErrorDisplayvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(ErrorDisplayvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_ErrorDisplayvue_type_custom_index_0_blockType_i18n = ((ErrorDisplayvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue



;


/* normalize component */

var ErrorDisplay_component = normalizeComponent(
  components_ErrorDisplayvue_type_script_lang_ts,
  ErrorDisplayvue_type_template_id_00a31407_scoped_true_render,
  ErrorDisplayvue_type_template_id_00a31407_scoped_true_staticRenderFns,
  false,
  null,
  "00a31407",
  null
  
)

/* custom blocks */
;
if (typeof components_ErrorDisplayvue_type_custom_index_0_blockType_i18n === 'function') components_ErrorDisplayvue_type_custom_index_0_blockType_i18n(ErrorDisplay_component)

/* harmony default export */ const components_ErrorDisplay = (ErrorDisplay_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Main.vue?vue&type=script&lang=ts










const ITEMS_PER_PAGE_KEY = 'chamilo-gradebook.itemsPerPage';
let Main = class Main extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    gradeBook = null;
    connector = null;
    itemSettings = null;
    categorySettings = null;
    showFinalScoreSettings = false;
    studentSearchTerm = '';
    studentSearchTerms = [];
    tableBusy = false;
    saveColumnId = null;
    saveCategoryId = null;
    saveDisplayTotal = false;
    itemsPerPage = 5;
    errorData = null;
    addColumnId = null;
    apiConfig;
    constructor() {
        super();
        this.updateResult = this.updateResult.bind(this);
    }
    get searchTerm() {
        return this.studentSearchTerm;
    }
    set searchTerm(term) {
        this.studentSearchTerm = term;
        this.studentSearchTerms = term.toLowerCase().split(' ').filter(s => s.length);
    }
    updateGradeColumnWithScores(column, id, scores) {
        if (!this.gradeBook) {
            return;
        }
        this.gradeBook.updateGradeColumnId(column, id);
        const resultsData = this.gradeBook.resultsData;
        scores.forEach(score => {
            if (!resultsData[score.columnId]) {
                external_commonjs_vue_commonjs2_vue_root_Vue_default().set(resultsData, score.columnId, {});
            }
            resultsData[score.columnId][score.targetUserId] = score;
        });
    }
    addGradeItem(item) {
        if (!this.gradeBook) {
            return;
        }
        const column = this.gradeBook.addGradeColumnFromItem(item);
        this.addColumnId = column.id;
        this.tableBusy = true;
        this.connector?.addGradeColumn(column, ({ id }, scores) => {
            this.updateGradeColumnWithScores(column, id, scores);
            this.resetGradeBook();
            this.tableBusy = false;
            this.addColumnId = null;
        });
    }
    removeGradeItem(item) {
        if (!this.gradeBook) {
            return;
        }
        const column = this.gradeBook.findGradeColumnWithGradeItem(item.id);
        if (!column) {
            return;
        }
        if (column.type === 'item') {
            this.gradeBook.removeColumn(column);
            this.onRemoveColumn(column);
        }
        else {
            this.gradeBook.removeSubItem(item);
            this.onRemoveSubItem(item, column.id);
        }
    }
    toggleGradeItem(item, isAdding) {
        if (isAdding) {
            this.addGradeItem(item);
        }
        else {
            this.removeGradeItem(item);
        }
    }
    get selectedCategory() {
        return this.gradeBook?.categories.find(cat => cat.id === this.categorySettings) || null;
    }
    resetGradeBook() {
        const gradeBook = this.gradeBook;
        this.gradeBook = null;
        this.$nextTick(() => { this.gradeBook = gradeBook; });
    }
    createNewCategory() {
        if (!this.gradeBook) {
            return;
        }
        const category = this.gradeBook.createNewCategory();
        this.tableBusy = true;
        this.connector?.addCategory(category, (cat) => {
            category.id = cat.id;
            this.categorySettings = cat.id;
            this.resetGradeBook();
            this.tableBusy = false;
        });
    }
    async synchronizeGradeBook() {
        if (!this.gradeBook) {
            return;
        }
        const gradeBook = this.gradeBook;
        this.tableBusy = true;
        await this.connector?.synchronizeGradeBook((scores) => {
            const resultsData = gradeBook.resultsData;
            if (!resultsData['totals']) {
                external_commonjs_vue_commonjs2_vue_root_Vue_default().set(resultsData, 'totals', {});
            }
            scores.forEach(score => {
                if (score.isTotal) {
                    resultsData['totals'][score.targetUserId] = score;
                    return;
                }
                if (!resultsData[score.columnId]) {
                    external_commonjs_vue_commonjs2_vue_root_Vue_default().set(resultsData, score.columnId, {});
                }
                resultsData[score.columnId][score.targetUserId] = score;
            });
            this.tableBusy = false;
        });
    }
    async updateTotalScores() {
        if (!this.gradeBook) {
            return;
        }
        const gradeBook = this.gradeBook;
        this.tableBusy = true;
        await this.connector?.calculateTotalScores((scores) => {
            const resultsData = gradeBook.resultsData;
            if (!resultsData['totals']) {
                external_commonjs_vue_commonjs2_vue_root_Vue_default().set(resultsData, 'totals', {});
            }
            scores.forEach(score => {
                resultsData['totals'][score.targetUserId] = score;
            });
            this.tableBusy = false;
        });
    }
    createNewScore() {
        if (!this.gradeBook) {
            return;
        }
        const column = this.gradeBook.createNewScore();
        this.addColumnId = column.id;
        this.tableBusy = true;
        this.connector?.addGradeColumn(column, ({ id }, scores) => {
            this.updateGradeColumnWithScores(column, id, scores);
            this.resetGradeBook();
            this.tableBusy = false;
            this.addColumnId = null;
        });
    }
    closeSelectedCategory() {
        this.categorySettings = null;
    }
    onChangeCategory(category) {
        this.saveCategoryId = category.id;
        this.connector?.updateCategory(category, () => {
            this.saveCategoryId = null;
        });
    }
    onChangeDisplayTotal() {
        if (!this.gradeBook) {
            return;
        }
        this.saveDisplayTotal = true;
        this.connector?.updateDisplayTotal(this.gradeBook.displayTotal, () => {
            this.saveDisplayTotal = false;
        });
    }
    async onMoveCategory(category) {
        if (!this.gradeBook) {
            return;
        }
        this.tableBusy = true;
        await this.connector?.moveCategory(category, this.gradeBook.categories.indexOf(category), () => {
            this.tableBusy = false;
        });
    }
    onRemoveCategory(category) {
        this.tableBusy = true;
        this.connector?.removeCategory(category, () => {
            this.tableBusy = false;
        });
    }
    onChangeGradeColumn(gradeColumn) {
        this.saveColumnId = gradeColumn.id;
        this.connector?.updateGradeColumn(gradeColumn, () => {
            this.saveColumnId = null;
        });
    }
    onChangeGradeColumnCategory(gradeColumn, categoryId) {
        this.tableBusy = true;
        this.connector?.updateGradeColumnCategory(gradeColumn, categoryId, () => {
            this.tableBusy = false;
        });
    }
    onMoveGradeColumn(column) {
        if (!this.gradeBook) {
            return;
        }
        const category = this.gradeBook.allCategories.find(category => category.columnIds.indexOf(column.id) !== -1);
        if (category) {
            this.tableBusy = true;
            this.connector?.moveGradeColumn(column, category.columnIds.indexOf(column.id), () => {
                this.tableBusy = false;
            });
        }
    }
    onAddSubItem(item, columnId) {
        if (!this.gradeBook) {
            return;
        }
        const gradeBook = this.gradeBook;
        this.tableBusy = true;
        this.connector?.addColumnSubItem(columnId, item.id, (column, scores) => {
            const resultsData = gradeBook.resultsData;
            delete resultsData[columnId];
            scores.forEach(score => {
                if (!resultsData[columnId]) {
                    external_commonjs_vue_commonjs2_vue_root_Vue_default().set(resultsData, columnId, {});
                }
                resultsData[columnId][score.targetUserId] = score;
            });
            this.tableBusy = false;
        });
    }
    onRemoveSubItem(item, columnId) {
        if (!this.gradeBook) {
            return;
        }
        const gradeBook = this.gradeBook;
        this.tableBusy = true;
        this.connector?.removeColumnSubItem(columnId, item.id, (column, scores) => {
            const resultsData = gradeBook.resultsData;
            delete resultsData[columnId];
            scores.forEach(score => {
                if (!resultsData[columnId]) {
                    external_commonjs_vue_commonjs2_vue_root_Vue_default().set(resultsData, columnId, {});
                }
                resultsData[columnId][score.targetUserId] = score;
            });
            this.tableBusy = false;
        });
    }
    onRemoveColumn(column) {
        this.tableBusy = true;
        this.connector?.removeGradeColumn(column, () => {
            this.tableBusy = false;
        });
    }
    updateResult(result) {
        if (!this.gradeBook) {
            return;
        }
        this.saveColumnId = null;
        const colScores = this.gradeBook.resultsData[result.columnId];
        if (!colScores) {
            return;
        }
        colScores[result.targetUserId] = result;
    }
    onOverwriteResult(result) {
        this.saveColumnId = result.columnId;
        this.connector?.overwriteGradeResult(result, this.updateResult);
    }
    onRevertOverwrittenResult(result) {
        this.saveColumnId = result.columnId;
        this.connector?.revertOverwrittenGradeResult(result, this.updateResult);
    }
    onUpdateScoreComment(result) {
        this.saveColumnId = result.columnId;
        this.connector?.updateGradeResultComment(result, this.updateResult);
    }
    loadItemsPerPage() {
        this.itemsPerPage = parseInt(localStorage.getItem(ITEMS_PER_PAGE_KEY) || '5');
    }
    setItemsPerPage(count) {
        this.itemsPerPage = count;
        localStorage.setItem(ITEMS_PER_PAGE_KEY, String(count));
    }
    setError(data) {
        this.errorData = data;
    }
    closeErrorDisplay() {
        this.errorData = null;
        this.saveColumnId = null;
        this.saveCategoryId = null;
        this.tableBusy = false;
    }
    exportGradeBook() {
        window.open(this.apiConfig.gradeBookExportURL, '_blank');
    }
    async load() {
        const allData = await Connector.loadGradeBookData(this.apiConfig.loadGradeBookDataURL, this.apiConfig.csrfToken);
        if (allData) {
            this.gradeBook = GradeBook.from(allData.gradebook);
            this.gradeBook.users = allData.users;
            this.connector = new Connector(this.apiConfig, this.gradeBook.dataId, this.gradeBook.currentVersion);
            this.connector.addErrorListener(this);
            const resultsData = { 'totals': {} };
            allData.scores.forEach((score) => {
                if (score.isTotal) {
                    resultsData['totals'][score.targetUserId] = score;
                    return;
                }
                if (!resultsData[score.columnId]) {
                    resultsData[score.columnId] = {};
                }
                resultsData[score.columnId][score.targetUserId] = score;
            });
            this.gradeBook.resultsData = resultsData;
        }
    }
    mounted() {
        this.load();
        this.loadItemsPerPage();
    }
};
__decorate([
    Prop({ type: Object, default: () => null })
], Main.prototype, "apiConfig", void 0);
Main = __decorate([
    vue_class_component_esm({
        components: { ErrorDisplay: components_ErrorDisplay, GradesTable: components_GradesTable, GradesDropdown: components_GradesDropdown, ItemSettings: components_ItemSettings, CategorySettings: components_CategorySettings, FinalScoreSettings: components_FinalScoreSettings }
    })
], Main);
/* harmony default export */ const Mainvue_type_script_lang_ts = (Main);

;// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_Mainvue_type_script_lang_ts = (Mainvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Main.vue?vue&type=style&index=0&id=76e65df4&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=style&index=0&id=76e65df4&prod&lang=css

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Main.vue?vue&type=style&index=1&id=76e65df4&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=style&index=1&id=76e65df4&prod&lang=scss&scoped=true

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Main.vue?vue&type=style&index=2&id=76e65df4&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=style&index=2&id=76e65df4&prod&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/Main.vue?vue&type=custom&index=0&blockType=i18n
var Mainvue_type_custom_index_0_blockType_i18n = __webpack_require__(852);
var Mainvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(Mainvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_Mainvue_type_custom_index_0_blockType_i18n = ((Mainvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/Main.vue



;




/* normalize component */

var Main_component = normalizeComponent(
  components_Mainvue_type_script_lang_ts,
  Mainvue_type_template_id_76e65df4_scoped_true_render,
  Mainvue_type_template_id_76e65df4_scoped_true_staticRenderFns,
  false,
  null,
  "76e65df4",
  null
  
)

/* custom blocks */
;
if (typeof components_Mainvue_type_custom_index_0_blockType_i18n === 'function') components_Mainvue_type_custom_index_0_blockType_i18n(Main_component)

/* harmony default export */ const components_Main = (Main_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/App.vue?vue&type=script&lang=ts



let App = class App extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    apiConfig;
    debugServerResponse;
};
__decorate([
    Prop({ type: Object, default: () => null })
], App.prototype, "apiConfig", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], App.prototype, "debugServerResponse", void 0);
App = __decorate([
    vue_class_component_esm({
        components: { Main: components_Main }
    })
], App);
/* harmony default export */ const Appvue_type_script_lang_ts = (App);

;// CONCATENATED MODULE: ./src/App.vue?vue&type=script&lang=ts
 /* harmony default export */ const src_Appvue_type_script_lang_ts = (Appvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/App.vue?vue&type=style&index=0&id=09bcfe23&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/App.vue?vue&type=style&index=0&id=09bcfe23&prod&lang=css

;// CONCATENATED MODULE: ./src/App.vue



;


/* normalize component */

var App_component = normalizeComponent(
  src_Appvue_type_script_lang_ts,
  render,
  staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ const src_App = (App_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/ImporterApp.vue?vue&type=template&id=1cbc4767&scoped=true
var ImporterAppvue_type_template_id_1cbc4767_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',{attrs:{"id":"app"}},[_c('div',{attrs:{"id":"gradebook-import"}},[_c('ol',{staticClass:"nav nav-tabs mod-steps",attrs:{"role":"navigation","aria-label":_vm.$t('import-steps')}},[_c('li',{staticClass:"nav-item u-cursor-pointer",class:{'active': _vm.chooseTypeActive, 'done': _vm.importType},attrs:{"aria-current":_vm.chooseTypeActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block",on:{"click":_vm.reload}},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("1")]),_vm._v(_vm._s(_vm.$t('choose-type')))])]),_c('li',{staticClass:"nav-item",class:{'active': _vm.chooseFileActive, 'done': _vm.imported || _vm.resultsLoaded},attrs:{"aria-current":_vm.chooseFileActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block"},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("2")]),_vm._v(_vm._s(_vm.$t('choose-file')))])]),_c('li',{staticClass:"nav-item",class:{'active': _vm.previewActive, 'done': _vm.resultsLoaded},attrs:{"aria-current":_vm.previewActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block"},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("3")]),_vm._v(_vm._s(_vm.$t('import-preview')))])]),_c('li',{staticClass:"nav-item",class:{'active': _vm.importCompleteActive},attrs:{"aria-current":_vm.importCompleteActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block"},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("4")]),_vm._v(_vm._s(_vm.$t('import-complete')))])])]),(_vm.chooseTypeActive)?_c('div',[_c('p',{staticClass:"gradebook-import-type u-font-medium"},[_vm._v(_vm._s(_vm.$t('question-upload')))]),_c('div',{staticClass:"u-flex u-gap-small-2x"},[_c('button',{staticClass:"btn btn-light fs-13",on:{"click":function($event){_vm.importType = 'scores'}}},[_vm._v(_vm._s(_vm.$t('type-scores')))]),_c('button',{staticClass:"btn btn-default fs-13",on:{"click":function($event){_vm.importType = 'scores_comments'}}},[_vm._v(_vm._s(_vm.$t('type-scores-comments')))])])]):_vm._e(),(_vm.chooseFileActive && !_vm.hasError)?_c('div',[_c('div',{staticClass:"gradebook-import-file u-font-medium"},[_vm._v(_vm._s(_vm.$t('file-with'))+" "+_vm._s(_vm.importType === 'scores' ? _vm.$t('type-scores') : _vm.$t('type-scores-comments')))]),_c('csv-import-info',{attrs:{"import-type":_vm.importType}}),_c('input',{ref:"inputfile",staticClass:"inputfile",attrs:{"type":"file","name":"file","id":"file"},on:{"change":function($event){_vm.filename=$event.target.value.split('\\').pop()}}}),_c('div',{staticClass:"u-flex"},[_c('label',{staticClass:"btn btn-default lbl-input-file u-font-normal",class:{'mod-selected': !!_vm.filename},attrs:{"for":"file","title":_vm.$t('select-file')}},[_c('svg',{attrs:{"xmlns":"http://www.w3.org/2000/svg","width":"20","height":"17","viewBox":"0 0 20 17"}},[_c('path',{attrs:{"d":"M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"}})]),_vm._v(" "),_c('span',[_vm._v(_vm._s(_vm.filename || _vm.$t('select-file')))])]),(_vm.filename)?_c('button',{staticClass:"btn btn-primary",attrs:{"id":"uploadbutton","type":"button","value":"Upload","disabled":_vm.hasError || !_vm.importType},on:{"click":_vm.uploadCSV}},[_vm._v(_vm._s(_vm.$t('upload')))]):_vm._e()])],1):_vm._e(),(_vm.chooseFileActive && _vm.hasError)?_c('div',{staticClass:"import-errors alert alert-danger"},[(_vm.has500Error)?_c('span',{staticClass:"error-filename u-inline-block u-font-medium"},[_vm._v(_vm._s(_vm.filename)+":")]):_vm._e(),_c('div',{staticClass:"errors",class:{'mb-20': _vm.has500Error},domProps:{"innerHTML":_vm._s(_vm.error)}}),(_vm.has500Error)?_c('div',{staticClass:"u-font-medium"},[_vm._v(_vm._s(_vm.$t('correct-mistakes'))+" "),_c('a',{attrs:{"href":"#"},on:{"click":function($event){$event.stopPropagation();return _vm.reload.apply(null, arguments)}}},[_vm._v(_vm._s(_vm.$t('reupload-results')))]),_vm._v(".")]):_vm._e()]):_vm._e(),(_vm.previewActive && !_vm.hasError)?[_c('div',{staticClass:"csv-import-info u-flex u-align-items-start"},[_c('p',[_vm._v(_vm._s(_vm.$t('import-results-overview')))]),_c('div',[_c('button',{staticClass:"btn btn-primary",attrs:{"title":_vm.$t('import')},on:{"click":_vm.uploadResults}},[_c('span',{staticClass:"glyphicon glyphicon-arrow-right",attrs:{"aria-hidden":"true"}}),_vm._v(" "+_vm._s(_vm.$t('import')))])])]),_c('imports-table',{attrs:{"fields":_vm.fields,"results":_vm.results,"max-scores":_vm.maxScores}})]:_vm._e(),(_vm.previewActive && _vm.hasError)?_c('div',{staticClass:"import-errors alert alert-danger"},[_c('div',{staticClass:"errors",domProps:{"innerHTML":_vm._s(_vm.error)}})]):_vm._e(),(_vm.importCompleteActive)?[_c('div',{staticClass:"alert alert-info mod-import-completed"},[_c('p',[_vm._v(_vm._s(_vm.$t('import-successful')))]),(_vm.missingUsers.length)?_c('p',{domProps:{"innerHTML":_vm._s(_vm.$t('no-results-some-students'))}}):_vm._e(),_c('p',[_c('a',{staticClass:"u-font-medium",attrs:{"href":_vm.apiConfig.gradeBookRootURL}},[_c('i',{staticClass:"fa fa-arrow-right",attrs:{"aria-hidden":"true"}}),_vm._v(" "+_vm._s(_vm.$t('go-to-gradebook')))])])]),(_vm.missingUsers.length)?_c('p',{staticClass:"gradebook-import-missing-users u-font-medium"},[_vm._v(_vm._s(_vm.$t('without-results'))+":")]):_vm._e(),(_vm.missingUsers.length)?_c('missing-users-table',{attrs:{"missing-users":_vm.missingUsers}}):_vm._e()]:_vm._e()],2),(_vm.debugServerResponse)?_c('div',{attrs:{"id":"server-response"}}):_vm._e()])
}
var ImporterAppvue_type_template_id_1cbc4767_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ImportsTable.vue?vue&type=template&id=17a82250&scoped=true
var ImportsTablevue_type_template_id_17a82250_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[(_vm.hasInvalidResults)?_c('ul',{staticClass:"nav mod-imports u-flex u-align-items-baseline",attrs:{"role":"tablist"}},[_c('li',{class:{active: _vm.tab === 'all'},attrs:{"role":"presentation"},on:{"click":function($event){_vm.tab = 'all'}}},[_c('a',{attrs:{"aria-selected":_vm.tab === 'all' ? 'true' : 'false',"aria-controls":"imports-table","role":"tab"}},[_vm._v(_vm._s(_vm.$t('all-imports')))])]),_c('li',{class:{active: _vm.tab === 'valid'},attrs:{"role":"presentation"},on:{"click":function($event){_vm.tab = 'valid'}}},[_c('a',{attrs:{"aria-selected":_vm.tab === 'valid' ? 'true' : 'false',"aria-controls":"imports-table","role":"tab"}},[_vm._v(_vm._s(_vm.$t('valid-imports')))])]),_c('li',{class:{active: _vm.tab === 'invalid'},attrs:{"role":"presentation"},on:{"click":function($event){_vm.tab = 'invalid'}}},[_c('a',{attrs:{"aria-selected":_vm.tab === 'invalid' ? 'true' : 'false',"aria-controls":"imports-table","role":"tab"}},[_vm._v(_vm._s(_vm.$t('not-subscribed'))),_c('span',{staticClass:"badge mod-invalid"},[_vm._v(_vm._s(_vm.invalidResultRows.length))])])])]):_vm._e(),_c('table',{staticClass:"imports-table",attrs:{"id":"imports-table"}},[_c('thead',[_c('tr',{staticClass:"table-row table-head-row"},_vm._l((_vm.fields),function(field){return _c('th',{key:`field-${field.key}`,staticClass:"table-cell",class:{'mod-score': field.type === 'score'}},[_vm._v(" "+_vm._s(field.label)+" "),(_vm.maxScores[field.key])?_c('span',{staticClass:"total"},[_vm._v(" "+_vm._s(_vm.maxScores[field.key])+" ")]):_vm._e()])}),0)]),_c('tbody',_vm._l((_vm.filteredResultRows),function(result,row_index){return _c('tr',{key:`result-row-${row_index}`,staticClass:"table-row table-body-row",class:{ 'mod-invalid': (_vm.showAll || !_vm.hasInvalidResults) ? !result.valid : _vm.showInvalid}},_vm._l((_vm.fields),function(field,col_index){return _c('td',{key:`result-${row_index}-${col_index}`,staticClass:"table-cell",class:{'mod-score': field.type === 'score', 'mod-comment': col_index === 4 && field.type === 'string'},attrs:{"title":(!result.valid && field.key === 'id') ? _vm.$t('user-not-in-course') : ((col_index === 4 && field.type === 'string') ? result[field.key] : '')}},[((_vm.showAll || !_vm.hasInvalidResults) && field.key === 'id')?_c('div',{staticClass:"u-flex u-justify-content-between u-align-items-center"},[_vm._v(" "+_vm._s(result[field.key])+" "),_c('i',{staticClass:"fa",class:result.valid ? 'fa-check-circle' : 'fa-exclamation-circle',attrs:{"aria-hidden":"true"}}),(!result.valid)?_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('user-not-in-course')))]):_vm._e()]):(field.type === 'score' && _vm.isNullScore(result[field.key]))?_c('div',{staticClass:"color-code mod-none",attrs:{"title":_vm.$t('no-score-found')}},[_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('no-score-found')))])]):(field.type === 'score' && _vm.isAuthAbsentScore(result[field.key]))?_c('div',{staticClass:"color-code amber-700",attrs:{"title":_vm.$t('auth-absent')}},[_c('span',[_vm._v(_vm._s(result[field.key]))])]):[_c('span',[_vm._v(_vm._s(result[field.key]))])]],2)}),0)}),0)])])
}
var ImportsTablevue_type_template_id_17a82250_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ImportsTable.vue?vue&type=script&lang=ts


let ImportsTable = class ImportsTable extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    tab = 'all';
    pagination = {
        currentPage: 1,
        itemsPerPage: 5
    };
    sortBy = 'lastname';
    sortDesc = false;
    fields;
    results;
    maxScores;
    isNullScore(score) {
        return score === null;
    }
    isAuthAbsentScore(score) {
        return typeof score === 'string' && (score.toLowerCase() === 'aabs' || score.toLowerCase() === 'gafw');
    }
    get showValid() {
        return this.tab === 'valid';
    }
    get showInvalid() {
        return this.tab === 'invalid';
    }
    get showAll() {
        return this.tab === 'all';
    }
    get validResultRows() {
        return this.results.filter(v => v.valid);
    }
    get invalidResultRows() {
        return this.results.filter(v => !v.valid);
    }
    get hasInvalidResults() {
        return this.invalidResultRows.length > 0;
    }
    get filteredResultRows() {
        if (this.showValid) {
            return this.validResultRows;
        }
        if (this.showInvalid) {
            return this.invalidResultRows;
        }
        return this.results;
    }
};
__decorate([
    Prop({ type: Array, default: () => [] })
], ImportsTable.prototype, "fields", void 0);
__decorate([
    Prop({ type: Array, default: () => [] })
], ImportsTable.prototype, "results", void 0);
__decorate([
    Prop({ type: Object })
], ImportsTable.prototype, "maxScores", void 0);
ImportsTable = __decorate([
    vue_class_component_esm({})
], ImportsTable);
/* harmony default export */ const ImportsTablevue_type_script_lang_ts = (ImportsTable);

;// CONCATENATED MODULE: ./src/components/ImportsTable.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_ImportsTablevue_type_script_lang_ts = (ImportsTablevue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ImportsTable.vue?vue&type=style&index=0&id=17a82250&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/ImportsTable.vue?vue&type=style&index=0&id=17a82250&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/ImportsTable.vue?vue&type=custom&index=0&blockType=i18n
var ImportsTablevue_type_custom_index_0_blockType_i18n = __webpack_require__(212);
var ImportsTablevue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(ImportsTablevue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/ImportsTable.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_ImportsTablevue_type_custom_index_0_blockType_i18n = ((ImportsTablevue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/ImportsTable.vue



;


/* normalize component */

var ImportsTable_component = normalizeComponent(
  components_ImportsTablevue_type_script_lang_ts,
  ImportsTablevue_type_template_id_17a82250_scoped_true_render,
  ImportsTablevue_type_template_id_17a82250_scoped_true_staticRenderFns,
  false,
  null,
  "17a82250",
  null
  
)

/* custom blocks */
;
if (typeof components_ImportsTablevue_type_custom_index_0_blockType_i18n === 'function') components_ImportsTablevue_type_custom_index_0_blockType_i18n(ImportsTable_component)

/* harmony default export */ const components_ImportsTable = (ImportsTable_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/MissingUsersTable.vue?vue&type=template&id=37c72fd4&scoped=true
var MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('table',{staticClass:"users-table"},[_c('thead',[_c('tr',{staticClass:"table-row table-head-row"},[_c('th',{staticClass:"table-cell"},[_vm._v(_vm._s(_vm.$t('last-name')))]),_c('th',{staticClass:"table-cell"},[_vm._v(_vm._s(_vm.$t('first-name')))]),_c('th',{staticClass:"table-cell"},[_vm._v(_vm._s(_vm.$t('official-code')))])])]),_c('tbody',_vm._l((_vm.missingUsers),function(user,row_index){return _c('tr',{key:`result-row-${row_index}`,staticClass:"table-row table-body-row"},[_c('td',{staticClass:"table-cell"},[_vm._v(_vm._s(user.lastname))]),_c('td',{staticClass:"table-cell"},[_vm._v(_vm._s(user.firstname))]),_c('td',{staticClass:"table-cell"},[_vm._v(_vm._s(user.official_code))])])}),0)])
}
var MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/MissingUsersTable.vue?vue&type=script&lang=ts


let MissingUsersTable = class MissingUsersTable extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    missingUsers;
};
__decorate([
    Prop({ type: Array, default: () => [] })
], MissingUsersTable.prototype, "missingUsers", void 0);
MissingUsersTable = __decorate([
    vue_class_component_esm({})
], MissingUsersTable);
/* harmony default export */ const MissingUsersTablevue_type_script_lang_ts = (MissingUsersTable);

;// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_MissingUsersTablevue_type_script_lang_ts = (MissingUsersTablevue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/MissingUsersTable.vue?vue&type=style&index=0&id=37c72fd4&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue?vue&type=style&index=0&id=37c72fd4&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/MissingUsersTable.vue?vue&type=custom&index=0&blockType=i18n
var MissingUsersTablevue_type_custom_index_0_blockType_i18n = __webpack_require__(887);
var MissingUsersTablevue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(MissingUsersTablevue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_MissingUsersTablevue_type_custom_index_0_blockType_i18n = ((MissingUsersTablevue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue



;


/* normalize component */

var MissingUsersTable_component = normalizeComponent(
  components_MissingUsersTablevue_type_script_lang_ts,
  MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_render,
  MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_staticRenderFns,
  false,
  null,
  "37c72fd4",
  null
  
)

/* custom blocks */
;
if (typeof components_MissingUsersTablevue_type_custom_index_0_blockType_i18n === 'function') components_MissingUsersTablevue_type_custom_index_0_blockType_i18n(MissingUsersTable_component)

/* harmony default export */ const components_MissingUsersTable = (MissingUsersTable_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CSVImportInfo.vue?vue&type=template&id=213fb475&scoped=true
var CSVImportInfovue_type_template_id_213fb475_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('p',[_vm._v(_vm._s(_vm.$t('csv-must-look-like'))+" ("+_vm._s(_vm.$t('mandatory-fields'))+"):")]),(_vm.importType === 'scores')?_c('div',{staticClass:"csv-example"},[_c('div',[_c('b',[_vm._v("lastname")]),_vm._v(";"),_c('b',[_vm._v("firstname")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-id"},[_vm._v("id")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-title-1 u-cursor-help",attrs:{"id":"csv-header-title-1"}},[_vm._v(_vm._s(_vm.$t('title'))+" 1")]),_vm._v(";"),_c('span',{staticClass:"csv-field csv-header-title-2 u-cursor-help",attrs:{"id":"csv-header-title-2"}},[_vm._v(_vm._s(_vm.$t('title'))+" 2")]),_vm._v(";…")]),_vm._m(0)]):_c('div',{staticClass:"csv-example"},[_c('div',[_c('b',[_vm._v("lastname")]),_vm._v(";"),_c('b',[_vm._v("firstname")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-id"},[_vm._v("id")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-title-1 u-cursor-help",attrs:{"id":"csv-header-title-1"}},[_vm._v(_vm._s(_vm.$t('title')))]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-comment u-cursor-help",attrs:{"id":"csv-header-comment"}},[_vm._v(_vm._s(_vm.$t('comment')))])]),_vm._m(1)]),_c('p',{staticStyle:{"max-width":"90ch"}},[_vm._v("Indien je geen procentuele score wil importeren maar een andere totaalscore (bvb. op 20 punten) voeg je een extra tweede regel in die er als volgt uitziet:")]),(_vm.importType === 'scores')?_c('div',{staticClass:"csv-example"},[_vm._m(2)]):_c('div',{staticClass:"csv-example"},[_vm._m(3)]),_c('b-popover',{attrs:{"target":"csv-expl-id","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help mod-list"},[_c('div',{staticClass:"u-font-medium",staticStyle:{"color":"#507e86"}},[_vm._v("id")]),_c('div',{domProps:{"innerHTML":_vm._s(_vm.$t('import-id'))}})])]),_c('b-popover',{attrs:{"target":"csv-header-title-1","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('import-score-title'))}})]),_c('b-popover',{attrs:{"target":"csv-expl-title-1","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help mod-list",domProps:{"innerHTML":_vm._s(_vm.$t('import-score'))}})]),_c('b-popover',{attrs:{"target":"csv-expl-max-1","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('max-score'))}})]),_c('b-popover',{attrs:{"target":"csv-header-title-2","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('import-score-title'))}})]),_c('b-popover',{attrs:{"target":"csv-expl-title-2","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help mod-list",domProps:{"innerHTML":_vm._s(_vm.$t('import-score'))}})]),_c('b-popover',{attrs:{"target":"csv-expl-max-2","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('max-score'))}})]),_c('b-popover',{attrs:{"target":"csv-header-comment","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('import-comment-title'))}})])],1)
}
var CSVImportInfovue_type_template_id_213fb475_scoped_true_staticRenderFns = [function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-id u-cursor-help",attrs:{"id":"csv-expl-id"}},[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-title-1 u-cursor-help",attrs:{"id":"csv-expl-title-1"}},[_vm._v("xxx")]),_vm._v(";"),_c('span',{staticClass:"csv-field csv-field-title-2 u-cursor-help",attrs:{"id":"csv-expl-title-2"}},[_vm._v("xxx")]),_vm._v(";…")])
},function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-id u-cursor-help",attrs:{"id":"csv-expl-id"}},[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-title-1 u-cursor-help",attrs:{"id":"csv-expl-title-1"}},[_vm._v("xxx")]),_vm._v(";"),_c('b',[_vm._v("xxx")])])
},function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('b',[_vm._v("totaal")]),_vm._v(";;;"),_c('b',{staticClass:"csv-field csv-field-title-1 u-cursor-help",attrs:{"id":"csv-expl-max-1"}},[_vm._v("xxx")]),_vm._v(";"),_c('span',{staticClass:"csv-field csv-field-title-2 u-cursor-help",attrs:{"id":"csv-expl-max-2"}},[_vm._v("xxx")]),_vm._v(";…")])
},function (){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('b',[_vm._v("totaal")]),_vm._v(";;;"),_c('b',{staticClass:"csv-field csv-field-title-1 u-cursor-help",attrs:{"id":"csv-expl-max-1"}},[_vm._v("xxx")]),_vm._v(";")])
}]


;// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue?vue&type=template&id=213fb475&scoped=true

;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CSVImportInfo.vue?vue&type=script&lang=ts


let CsvImportInfo = class CsvImportInfo extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    importType;
};
__decorate([
    Prop({ type: String, default: 'scores' })
], CsvImportInfo.prototype, "importType", void 0);
CsvImportInfo = __decorate([
    vue_class_component_esm({})
], CsvImportInfo);
/* harmony default export */ const CSVImportInfovue_type_script_lang_ts = (CsvImportInfo);

;// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_CSVImportInfovue_type_script_lang_ts = (CSVImportInfovue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CSVImportInfo.vue?vue&type=style&index=0&id=213fb475&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue?vue&type=style&index=0&id=213fb475&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/CSVImportInfo.vue?vue&type=custom&index=0&blockType=i18n
var CSVImportInfovue_type_custom_index_0_blockType_i18n = __webpack_require__(793);
var CSVImportInfovue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(CSVImportInfovue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_CSVImportInfovue_type_custom_index_0_blockType_i18n = ((CSVImportInfovue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue



;


/* normalize component */

var CSVImportInfo_component = normalizeComponent(
  components_CSVImportInfovue_type_script_lang_ts,
  CSVImportInfovue_type_template_id_213fb475_scoped_true_render,
  CSVImportInfovue_type_template_id_213fb475_scoped_true_staticRenderFns,
  false,
  null,
  "213fb475",
  null
  
)

/* custom blocks */
;
if (typeof components_CSVImportInfovue_type_custom_index_0_blockType_i18n === 'function') components_CSVImportInfovue_type_custom_index_0_blockType_i18n(CSVImportInfo_component)

/* harmony default export */ const CSVImportInfo = (CSVImportInfo_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/ImporterApp.vue?vue&type=script&lang=ts







const ImporterAppvue_type_script_lang_ts_TIMEOUT_SEC = 30;
let ImporterApp = class ImporterApp extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    importType = null;
    filename = '';
    hasError = false;
    has500Error = false;
    error = '';
    imported = false;
    missingUsers = [];
    results = [];
    fields = [];
    maxScores = {};
    apiConfig;
    gradebookDataId;
    currentVersion;
    debugServerResponse;
    get chooseTypeActive() {
        return !this.importType;
    }
    get chooseFileActive() {
        return this.importType && !(this.imported || this.resultsLoaded);
    }
    get previewActive() {
        return !this.imported && this.resultsLoaded;
    }
    get importCompleteActive() {
        return this.imported;
    }
    get inputFile() {
        return this.$refs['inputfile'];
    }
    get resultsLoaded() {
        return this.fields.length > 0;
    }
    setError(msg) {
        this.hasError = true;
        this.error = msg;
    }
    handleError(err) {
        let error;
        if (err?.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
            error = { 'type': 'Timeout' };
        }
        else if (err?.response?.data?.error) {
            error = err.response.data.error;
        }
        else if (err?.type) {
            error = err;
        }
        if (!error.type) {
            error = { 'type': 'Unknown' };
        }
        this.setError(`${this.$t('error-' + error.type)}`);
    }
    async uploadCSV() {
        if (!this.importType) {
            return;
        }
        const fileData = this.inputFile.files[0];
        const formData = new FormData();
        if (this.apiConfig.csrfToken) {
            formData.append('_csrf_token', this.apiConfig.csrfToken);
        }
        formData.append('importType', this.importType);
        formData.append('file', fileData);
        try {
            const res = await lib_axios.post(this.apiConfig.processCsvURL, formData, { timeout: ImporterAppvue_type_script_lang_ts_TIMEOUT_SEC * 1000 });
            logResponse(res.data);
            if (res.data?.fields !== undefined && res.data?.results !== undefined) {
                const { fields, max_scores, results } = res.data;
                this.fields = fields;
                this.maxScores = max_scores || {};
                this.results = results;
            }
            else if (res.data?.result_code === 500) {
                this.has500Error = true;
                this.setError(res.data.result_message);
            }
            else if (typeof res.data === 'string' && res.data.toLowerCase().indexOf('login') !== -1) {
                throw { 'type': 'LoggedOut' };
            }
            else {
                throw { 'type': 'Unknown' };
            }
        }
        catch (err) {
            logResponse(err);
            this.handleError(err);
        }
    }
    async uploadResults() {
        const formData = new FormData();
        formData.set('gradebookDataId', String(this.gradebookDataId));
        formData.set('version', String(this.currentVersion));
        if (this.apiConfig.csrfToken) {
            formData.append('_csrf_token', this.apiConfig.csrfToken);
        }
        formData.append('importType', this.importType);
        const scores = this.importType === 'scores_comments' ?
            [this.getResultsForField(this.fields[3], this.fields[4])] :
            this.fields.slice(3).map(field => this.getResultsForField(field));
        formData.set('importScores', JSON.stringify(scores));
        try {
            const res = await lib_axios.post(this.apiConfig.importCsvURL, formData, { timeout: ImporterAppvue_type_script_lang_ts_TIMEOUT_SEC * 1000 });
            logResponse(res.data);
            if (res.data?.missing_users !== undefined) {
                this.missingUsers = res.data.missing_users;
                this.imported = true;
            }
            else if (res.data?.result_code === 500) {
                this.has500Error = true;
                this.setError(res.data.result_message);
            }
            else if (typeof res.data === 'string' && res.data.toLowerCase().indexOf('login') !== -1) {
                throw { 'type': 'LoggedOut' };
            }
            else {
                throw { 'type': 'Unknown' };
            }
        }
        catch (err) {
            logResponse(err);
            this.handleError(err);
        }
    }
    getResultsForField(scoreField, commentField = null) {
        return {
            label: scoreField.label,
            maxScore: this.maxScores[scoreField.key] || null,
            results: this.results.filter(v => v.valid).map(v => {
                const score = v[scoreField.key];
                const comment = commentField === null ? null : (v[commentField.key] || null);
                const authAbsent = typeof score === 'string' && (score.toLowerCase() === 'aabs' || score.toLowerCase() === 'gafw');
                return {
                    id: v.user_id,
                    score: authAbsent ? null : score,
                    authAbsent,
                    comment
                };
            })
        };
    }
    reload() {
        window.location.reload();
    }
};
__decorate([
    Prop({ type: Object, default: () => null })
], ImporterApp.prototype, "apiConfig", void 0);
__decorate([
    Prop({ type: Number, required: true })
], ImporterApp.prototype, "gradebookDataId", void 0);
__decorate([
    Prop({ type: Number, required: true })
], ImporterApp.prototype, "currentVersion", void 0);
__decorate([
    Prop({ type: Boolean, default: false })
], ImporterApp.prototype, "debugServerResponse", void 0);
ImporterApp = __decorate([
    vue_class_component_esm({
        components: { ImportsTable: components_ImportsTable, MissingUsersTable: components_MissingUsersTable, CsvImportInfo: CSVImportInfo }
    })
], ImporterApp);
/* harmony default export */ const ImporterAppvue_type_script_lang_ts = (ImporterApp);

;// CONCATENATED MODULE: ./src/ImporterApp.vue?vue&type=script&lang=ts
 /* harmony default export */ const src_ImporterAppvue_type_script_lang_ts = (ImporterAppvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/ImporterApp.vue?vue&type=style&index=0&id=1cbc4767&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/ImporterApp.vue?vue&type=style&index=0&id=1cbc4767&prod&scoped=true&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/ImporterApp.vue?vue&type=custom&index=0&blockType=i18n
var ImporterAppvue_type_custom_index_0_blockType_i18n = __webpack_require__(762);
var ImporterAppvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(ImporterAppvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/ImporterApp.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const src_ImporterAppvue_type_custom_index_0_blockType_i18n = ((ImporterAppvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/ImporterApp.vue



;


/* normalize component */

var ImporterApp_component = normalizeComponent(
  src_ImporterAppvue_type_script_lang_ts,
  ImporterAppvue_type_template_id_1cbc4767_scoped_true_render,
  ImporterAppvue_type_template_id_1cbc4767_scoped_true_staticRenderFns,
  false,
  null,
  "1cbc4767",
  null
  
)

/* custom blocks */
;
if (typeof src_ImporterAppvue_type_custom_index_0_blockType_i18n === 'function') src_ImporterAppvue_type_custom_index_0_blockType_i18n(ImporterApp_component)

/* harmony default export */ const src_ImporterApp = (ImporterApp_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/UserScoresApp.vue?vue&type=template&id=022d871c&scoped=true
var UserScoresAppvue_type_template_id_022d871c_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return (_vm.gradeBook)?_c('user-scores',{staticClass:"gradebook-user-scores",attrs:{"grade-book":_vm.gradeBook}}):_vm._e()
}
var UserScoresAppvue_type_template_id_022d871c_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserScores.vue?vue&type=template&id=c01141d2&scoped=true
var UserScoresvue_type_template_id_c01141d2_scoped_true_render = function render(){var _vm=this,_c=_vm._self._c,_setup=_vm._self._setupProxy;return _c('div',[_c('b-table-simple',{staticClass:"gradebook-table"},[_c('b-thead',[_c('b-tr',{staticClass:"table-row table-head-row"},[_c('b-th',[_vm._v(_vm._s(_vm.$t('title')))]),_c('b-th',{staticClass:"u-text-end"},[_vm._v(_vm._s(_vm.$t('score')))])],1)],1),_c('b-tbody',[_vm._l((_vm.gradeBook.allCategories),function(category){return [(category.columnIds.length && _vm.gradeBook.allCategories.length && _vm.gradeBook.allCategories[0].id !== 0)?_c('b-tr',{key:`cat-${category.id}`,staticClass:"table-row table-body-row"},[_c('b-td',{staticClass:"table-category u-font-medium",attrs:{"colspan":"2"}},[_vm._v(_vm._s(category.title))])],1):_vm._e(),_vm._l((_vm.getColumns(category)),function(column){return _c('b-tr',{key:`col-${category.id}-${column.id}`,staticClass:"table-row table-body-row result-row",attrs:{"id":`col-${category.id}-${column.id}`}},[_c('b-td',{staticClass:"category-color u-relative",style:(`--color: ${category.color};`)},[_vm._v(_vm._s(column.title))]),_c('b-td',[(column.released)?_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end"},[(column.comment)?_c('i',{staticClass:"fa fa-comment-o",attrs:{"aria-hidden":"true"}}):_vm._e(),_c('student-result',{staticClass:"u-flex u-align-items-center u-justify-content-end",class:{'uncounted-score': !column.countsForEndResult},attrs:{"id":`result-${column.id}`,"result":column.result}})],1):_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end not-yet-released"},[_vm._v(_vm._s(_vm.$t('not-yet-released')))])]),_c('b-popover',{attrs:{"custom-class":"gradebook-score-popover","target":`col-${category.id}-${column.id}`,"triggers":"hover","placement":"rightbottom"}},[_c('div',{staticClass:"score-info"},[(column.countsForEndResult)?_c('div',{staticClass:"u-flex u-align-items-center popover-weight-header"},[_vm._v(_vm._s(_vm.$t('weight'))+": "+_vm._s(_vm._f("formatNum2")(column.weight))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]):_c('div',{staticClass:"popover-count-endresult-not"},[_c('i',[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),(column.comment)?[_c('div',{staticClass:"popover-feedback-header"},[_vm._v("Feedback:")]),_vm._v(" "+_vm._s(column.comment)+" ")]:_vm._e()],2)])],1)})]}),(_vm.gradeBook.allCategories.length && _vm.gradeBook.allCategories[0].id !== 0)?_c('b-tr',{staticClass:"table-row table-body-row"},[_c('b-td',{staticClass:"table-empty-cell",attrs:{"colspan":"2"}})],1):_vm._e(),_c('b-tr',{staticClass:"table-row table-body-row"},[_c('b-td',{staticClass:"table-final-score-header"},[_vm._v(_vm._s(_vm.$t('final-score')))]),_c('b-td',{staticClass:"table-final-score u-font-medium"},[(!_vm.gradeBook.hasUnreleasedScores)?_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end"},[_vm._v(_vm._s(_vm._f("formatNum2")(_vm.gradeBook.getEndResult(_vm.userId)))),(_vm.gradeBook.getDisplayTotal() === 100)?[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]:[_vm._v(" / "+_vm._s(_vm.gradeBook.getDisplayTotal()))]],2):_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end not-yet-released"},[_vm._v(_vm._s(_vm.$t('not-yet-released')))])])],1)],2)],1)],1)
}
var UserScoresvue_type_template_id_c01141d2_scoped_true_staticRenderFns = []


;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserScores.vue?vue&type=script&lang=ts




let UserScores = class UserScores extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    gradeBook;
    get userId() {
        return this.gradeBook.users[0].id;
    }
    getColumnData(columnId) {
        const gradeBook = this.gradeBook;
        const column = gradeBook.getGradeColumn(columnId);
        if (!column) {
            throw new Error(`GradeColumn with id ${columnId} not found.`);
        }
        return {
            id: columnId,
            released: column.released,
            countsForEndResult: column.countForEndResult,
            title: gradeBook.getTitle(column),
            weight: gradeBook.getWeight(column),
            result: gradeBook.getResult(columnId, this.userId),
            comment: gradeBook.getResultComment(columnId, this.userId)
        };
    }
    getColumns(category) {
        return category.columnIds.map(columnId => this.getColumnData(columnId));
    }
};
__decorate([
    Prop({ type: GradeBook, required: true })
], UserScores.prototype, "gradeBook", void 0);
UserScores = __decorate([
    vue_class_component_esm({
        components: { StudentResult: components_StudentResult },
        filters: {
            formatNum2: function (v) {
                if (v === null) {
                    return '';
                }
                return parseFloat(v.toPrecision(8)).toLocaleString(undefined, { maximumFractionDigits: 2 });
            }
        }
    })
], UserScores);
/* harmony default export */ const UserScoresvue_type_script_lang_ts = (UserScores);

;// CONCATENATED MODULE: ./src/components/UserScores.vue?vue&type=script&lang=ts
 /* harmony default export */ const components_UserScoresvue_type_script_lang_ts = (UserScoresvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-66.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-66.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-66.use[2]!../../../../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-66.use[3]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserScores.vue?vue&type=style&index=0&id=c01141d2&prod&lang=scss&scoped=true
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/UserScores.vue?vue&type=style&index=0&id=c01141d2&prod&lang=scss&scoped=true

;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserScores.vue?vue&type=style&index=1&id=c01141d2&prod&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/components/UserScores.vue?vue&type=style&index=1&id=c01141d2&prod&lang=css

// EXTERNAL MODULE: ../../../../../../../../node_modules/@intlify/vue-i18n-loader/lib/index.js!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/components/UserScores.vue?vue&type=custom&index=0&blockType=i18n
var UserScoresvue_type_custom_index_0_blockType_i18n = __webpack_require__(480);
var UserScoresvue_type_custom_index_0_blockType_i18n_default = /*#__PURE__*/__webpack_require__.n(UserScoresvue_type_custom_index_0_blockType_i18n);
;// CONCATENATED MODULE: ./src/components/UserScores.vue?vue&type=custom&index=0&blockType=i18n
 /* harmony default export */ const components_UserScoresvue_type_custom_index_0_blockType_i18n = ((UserScoresvue_type_custom_index_0_blockType_i18n_default())); 
;// CONCATENATED MODULE: ./src/components/UserScores.vue



;



/* normalize component */

var UserScores_component = normalizeComponent(
  components_UserScoresvue_type_script_lang_ts,
  UserScoresvue_type_template_id_c01141d2_scoped_true_render,
  UserScoresvue_type_template_id_c01141d2_scoped_true_staticRenderFns,
  false,
  null,
  "c01141d2",
  null
  
)

/* custom blocks */
;
if (typeof components_UserScoresvue_type_custom_index_0_blockType_i18n === 'function') components_UserScoresvue_type_custom_index_0_blockType_i18n(UserScores_component)

/* harmony default export */ const components_UserScores = (UserScores_component.exports);
;// CONCATENATED MODULE: ../../../../../../../../node_modules/thread-loader/dist/cjs.js!../../../../../../../../node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader/index.js??clonedRuleSet-84.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/UserScoresApp.vue?vue&type=script&lang=ts




let UserScoresApp = class UserScoresApp extends (external_commonjs_vue_commonjs2_vue_root_Vue_default()) {
    gradeBook = null;
    gradeBookData;
    users;
    scores;
    mounted() {
        this.gradeBook = GradeBook.from(this.gradeBookData);
        const resultsData = { 'totals': {} };
        this.gradeBook.users = this.users;
        this.scores.forEach((score) => {
            if (score.isTotal) {
                resultsData['totals'][score.targetUserId] = score;
                return;
            }
            if (!resultsData[score.columnId]) {
                resultsData[score.columnId] = {};
            }
            resultsData[score.columnId][score.targetUserId] = score;
        });
        this.gradeBook.resultsData = resultsData;
    }
};
__decorate([
    Prop({ type: Object, required: true })
], UserScoresApp.prototype, "gradeBookData", void 0);
__decorate([
    Prop({ type: Array, required: true })
], UserScoresApp.prototype, "users", void 0);
__decorate([
    Prop({ type: Array, required: true })
], UserScoresApp.prototype, "scores", void 0);
UserScoresApp = __decorate([
    vue_class_component_esm({
        components: { UserScores: components_UserScores }
    })
], UserScoresApp);
/* harmony default export */ const UserScoresAppvue_type_script_lang_ts = (UserScoresApp);

;// CONCATENATED MODULE: ./src/UserScoresApp.vue?vue&type=script&lang=ts
 /* harmony default export */ const src_UserScoresAppvue_type_script_lang_ts = (UserScoresAppvue_type_script_lang_ts); 
;// CONCATENATED MODULE: ../../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??clonedRuleSet-56.use[0]!../../../../../../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-56.use[1]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/@vue/cli-service/node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-56.use[2]!../../../../../../../../node_modules/@vue/vue-loader-v15/lib/index.js??vue-loader-options!./src/UserScoresApp.vue?vue&type=style&index=0&id=022d871c&prod&scoped=true&lang=css
// extracted by mini-css-extract-plugin

;// CONCATENATED MODULE: ./src/UserScoresApp.vue?vue&type=style&index=0&id=022d871c&prod&scoped=true&lang=css

;// CONCATENATED MODULE: ./src/UserScoresApp.vue



;


/* normalize component */

var UserScoresApp_component = normalizeComponent(
  src_UserScoresAppvue_type_script_lang_ts,
  UserScoresAppvue_type_template_id_022d871c_scoped_true_render,
  UserScoresAppvue_type_template_id_022d871c_scoped_true_staticRenderFns,
  false,
  null,
  "022d871c",
  null
  
)

/* harmony default export */ const src_UserScoresApp = (UserScoresApp_component.exports);
;// CONCATENATED MODULE: ./src/plugin.ts



/* harmony default export */ const src_plugin = ({
    install(Vue, options) {
        Vue.component('GradeBookApp', src_App);
        Vue.component('ImporterApp', src_ImporterApp);
        Vue.component('GradeBookUserScoresApp', src_UserScoresApp);
    }
});

;// CONCATENATED MODULE: ../../../../../../../../node_modules/@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ const entry_lib = (src_plugin);


})();

/******/ 	return __webpack_exports__;
/******/ })()
;
});
//# sourceMappingURL=cosnics-gradebook.umd.js.map