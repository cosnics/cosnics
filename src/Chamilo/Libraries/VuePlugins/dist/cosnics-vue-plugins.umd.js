(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("vue"));
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["cosnics-vue-plugins"] = factory(require("vue"));
	else
		root["cosnics-vue-plugins"] = factory(root["Vue"]);
})((typeof self !== 'undefined' ? self : this), function(__WEBPACK_EXTERNAL_MODULE__8bbf__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
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
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
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
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "fa16");
/******/ })
/************************************************************************/
/******/ ({

/***/ "015e":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var $reduce = __webpack_require__("3e56").left;
var sloppyArrayMethod = __webpack_require__("5028");

// `Array.prototype.reduce` method
// https://tc39.github.io/ecma262/#sec-array.prototype.reduce
$({ target: 'Array', proto: true, forced: sloppyArrayMethod('reduce') }, {
  reduce: function reduce(callbackfn /* , initialValue */) {
    return $reduce(this, callbackfn, arguments.length, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "03de":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("7313");

// `ToPrimitive` abstract operation
// https://tc39.github.io/ecma262/#sec-toprimitive
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (input, PREFERRED_STRING) {
  if (!isObject(input)) return input;
  var fn, val;
  if (PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
  if (typeof (fn = input.valueOf) == 'function' && !isObject(val = fn.call(input))) return val;
  if (!PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
  throw TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ "0427":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var $map = __webpack_require__("a1d8").map;
var fails = __webpack_require__("3903");
var arrayMethodHasSpeciesSupport = __webpack_require__("1feb");

var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('map');
// FF49- issue
var USES_TO_LENGTH = HAS_SPECIES_SUPPORT && !fails(function () {
  [].map.call({ length: -1, 0: 1 }, function (it) { throw it; });
});

// `Array.prototype.map` method
// https://tc39.github.io/ecma262/#sec-array.prototype.map
// with adding support of @@species
$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT || !USES_TO_LENGTH }, {
  map: function map(callbackfn /* , thisArg */) {
    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "05e9":
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

/***/ "07b9":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("7313");

module.exports = function (it) {
  if (!isObject(it) && it !== null) {
    throw TypeError("Can't set " + String(it) + ' as a prototype');
  } return it;
};


/***/ }),

/***/ "07c1":
/***/ (function(module, exports, __webpack_require__) {

var hiddenKeys = __webpack_require__("cc24");
var isObject = __webpack_require__("7313");
var has = __webpack_require__("20bb");
var defineProperty = __webpack_require__("eae2").f;
var uid = __webpack_require__("267a");
var FREEZING = __webpack_require__("f7f6");

var METADATA = uid('meta');
var id = 0;

var isExtensible = Object.isExtensible || function () {
  return true;
};

var setMetadata = function (it) {
  defineProperty(it, METADATA, { value: {
    objectID: 'O' + ++id, // object ID
    weakData: {}          // weak collections IDs
  } });
};

var fastKey = function (it, create) {
  // return a primitive with prefix
  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
  if (!has(it, METADATA)) {
    // can't set metadata to uncaught frozen object
    if (!isExtensible(it)) return 'F';
    // not necessary to add metadata
    if (!create) return 'E';
    // add missing metadata
    setMetadata(it);
  // return object ID
  } return it[METADATA].objectID;
};

var getWeakData = function (it, create) {
  if (!has(it, METADATA)) {
    // can't set metadata to uncaught frozen object
    if (!isExtensible(it)) return true;
    // not necessary to add metadata
    if (!create) return false;
    // add missing metadata
    setMetadata(it);
  // return the store of weak collections IDs
  } return it[METADATA].weakData;
};

// add metadata on freeze-family methods calling
var onFreeze = function (it) {
  if (FREEZING && meta.REQUIRED && isExtensible(it) && !has(it, METADATA)) setMetadata(it);
  return it;
};

var meta = module.exports = {
  REQUIRED: false,
  fastKey: fastKey,
  getWeakData: getWeakData,
  onFreeze: onFreeze
};

hiddenKeys[METADATA] = true;


/***/ }),

/***/ "0de9":
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),

/***/ "0e66":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("7313");

module.exports = function (it) {
  if (!isObject(it)) {
    throw TypeError(String(it) + ' is not an object');
  } return it;
};


/***/ }),

/***/ "105f":
/***/ (function(module, exports, __webpack_require__) {

var redefine = __webpack_require__("8b07");

var DatePrototype = Date.prototype;
var INVALID_DATE = 'Invalid Date';
var TO_STRING = 'toString';
var nativeDateToString = DatePrototype[TO_STRING];
var getTime = DatePrototype.getTime;

// `Date.prototype.toString` method
// https://tc39.github.io/ecma262/#sec-date.prototype.tostring
if (new Date(NaN) + '' != INVALID_DATE) {
  redefine(DatePrototype, TO_STRING, function toString() {
    var value = getTime.call(this);
    // eslint-disable-next-line no-self-compare
    return value === value ? nativeDateToString.call(this) : INVALID_DATE;
  });
}


/***/ }),

/***/ "10b3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var global = __webpack_require__("92f7");
var getBuiltIn = __webpack_require__("fe3e");
var IS_PURE = __webpack_require__("c009");
var DESCRIPTORS = __webpack_require__("50ce");
var NATIVE_SYMBOL = __webpack_require__("3fb6");
var USE_SYMBOL_AS_UID = __webpack_require__("bd63");
var fails = __webpack_require__("3903");
var has = __webpack_require__("20bb");
var isArray = __webpack_require__("3b4d");
var isObject = __webpack_require__("7313");
var anObject = __webpack_require__("0e66");
var toObject = __webpack_require__("40f9");
var toIndexedObject = __webpack_require__("f11f");
var toPrimitive = __webpack_require__("03de");
var createPropertyDescriptor = __webpack_require__("7234");
var nativeObjectCreate = __webpack_require__("ad3e");
var objectKeys = __webpack_require__("58c5");
var getOwnPropertyNamesModule = __webpack_require__("3768");
var getOwnPropertyNamesExternal = __webpack_require__("7ff6");
var getOwnPropertySymbolsModule = __webpack_require__("1524");
var getOwnPropertyDescriptorModule = __webpack_require__("84e7");
var definePropertyModule = __webpack_require__("eae2");
var propertyIsEnumerableModule = __webpack_require__("eda6");
var createNonEnumerableProperty = __webpack_require__("88a7");
var redefine = __webpack_require__("8b07");
var shared = __webpack_require__("b802");
var sharedKey = __webpack_require__("7408");
var hiddenKeys = __webpack_require__("cc24");
var uid = __webpack_require__("267a");
var wellKnownSymbol = __webpack_require__("df9f");
var wrappedWellKnownSymbolModule = __webpack_require__("d4cb");
var defineWellKnownSymbol = __webpack_require__("6238");
var setToStringTag = __webpack_require__("9f8d");
var InternalStateModule = __webpack_require__("1a2e");
var $forEach = __webpack_require__("a1d8").forEach;

var HIDDEN = sharedKey('hidden');
var SYMBOL = 'Symbol';
var PROTOTYPE = 'prototype';
var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');
var setInternalState = InternalStateModule.set;
var getInternalState = InternalStateModule.getterFor(SYMBOL);
var ObjectPrototype = Object[PROTOTYPE];
var $Symbol = global.Symbol;
var $stringify = getBuiltIn('JSON', 'stringify');
var nativeGetOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
var nativeDefineProperty = definePropertyModule.f;
var nativeGetOwnPropertyNames = getOwnPropertyNamesExternal.f;
var nativePropertyIsEnumerable = propertyIsEnumerableModule.f;
var AllSymbols = shared('symbols');
var ObjectPrototypeSymbols = shared('op-symbols');
var StringToSymbolRegistry = shared('string-to-symbol-registry');
var SymbolToStringRegistry = shared('symbol-to-string-registry');
var WellKnownSymbolsStore = shared('wks');
var QObject = global.QObject;
// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
var USE_SETTER = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
var setSymbolDescriptor = DESCRIPTORS && fails(function () {
  return nativeObjectCreate(nativeDefineProperty({}, 'a', {
    get: function () { return nativeDefineProperty(this, 'a', { value: 7 }).a; }
  })).a != 7;
}) ? function (O, P, Attributes) {
  var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor(ObjectPrototype, P);
  if (ObjectPrototypeDescriptor) delete ObjectPrototype[P];
  nativeDefineProperty(O, P, Attributes);
  if (ObjectPrototypeDescriptor && O !== ObjectPrototype) {
    nativeDefineProperty(ObjectPrototype, P, ObjectPrototypeDescriptor);
  }
} : nativeDefineProperty;

var wrap = function (tag, description) {
  var symbol = AllSymbols[tag] = nativeObjectCreate($Symbol[PROTOTYPE]);
  setInternalState(symbol, {
    type: SYMBOL,
    tag: tag,
    description: description
  });
  if (!DESCRIPTORS) symbol.description = description;
  return symbol;
};

var isSymbol = NATIVE_SYMBOL && typeof $Symbol.iterator == 'symbol' ? function (it) {
  return typeof it == 'symbol';
} : function (it) {
  return Object(it) instanceof $Symbol;
};

var $defineProperty = function defineProperty(O, P, Attributes) {
  if (O === ObjectPrototype) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
  anObject(O);
  var key = toPrimitive(P, true);
  anObject(Attributes);
  if (has(AllSymbols, key)) {
    if (!Attributes.enumerable) {
      if (!has(O, HIDDEN)) nativeDefineProperty(O, HIDDEN, createPropertyDescriptor(1, {}));
      O[HIDDEN][key] = true;
    } else {
      if (has(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
      Attributes = nativeObjectCreate(Attributes, { enumerable: createPropertyDescriptor(0, false) });
    } return setSymbolDescriptor(O, key, Attributes);
  } return nativeDefineProperty(O, key, Attributes);
};

var $defineProperties = function defineProperties(O, Properties) {
  anObject(O);
  var properties = toIndexedObject(Properties);
  var keys = objectKeys(properties).concat($getOwnPropertySymbols(properties));
  $forEach(keys, function (key) {
    if (!DESCRIPTORS || $propertyIsEnumerable.call(properties, key)) $defineProperty(O, key, properties[key]);
  });
  return O;
};

var $create = function create(O, Properties) {
  return Properties === undefined ? nativeObjectCreate(O) : $defineProperties(nativeObjectCreate(O), Properties);
};

var $propertyIsEnumerable = function propertyIsEnumerable(V) {
  var P = toPrimitive(V, true);
  var enumerable = nativePropertyIsEnumerable.call(this, P);
  if (this === ObjectPrototype && has(AllSymbols, P) && !has(ObjectPrototypeSymbols, P)) return false;
  return enumerable || !has(this, P) || !has(AllSymbols, P) || has(this, HIDDEN) && this[HIDDEN][P] ? enumerable : true;
};

var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
  var it = toIndexedObject(O);
  var key = toPrimitive(P, true);
  if (it === ObjectPrototype && has(AllSymbols, key) && !has(ObjectPrototypeSymbols, key)) return;
  var descriptor = nativeGetOwnPropertyDescriptor(it, key);
  if (descriptor && has(AllSymbols, key) && !(has(it, HIDDEN) && it[HIDDEN][key])) {
    descriptor.enumerable = true;
  }
  return descriptor;
};

var $getOwnPropertyNames = function getOwnPropertyNames(O) {
  var names = nativeGetOwnPropertyNames(toIndexedObject(O));
  var result = [];
  $forEach(names, function (key) {
    if (!has(AllSymbols, key) && !has(hiddenKeys, key)) result.push(key);
  });
  return result;
};

var $getOwnPropertySymbols = function getOwnPropertySymbols(O) {
  var IS_OBJECT_PROTOTYPE = O === ObjectPrototype;
  var names = nativeGetOwnPropertyNames(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject(O));
  var result = [];
  $forEach(names, function (key) {
    if (has(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || has(ObjectPrototype, key))) {
      result.push(AllSymbols[key]);
    }
  });
  return result;
};

// `Symbol` constructor
// https://tc39.github.io/ecma262/#sec-symbol-constructor
if (!NATIVE_SYMBOL) {
  $Symbol = function Symbol() {
    if (this instanceof $Symbol) throw TypeError('Symbol is not a constructor');
    var description = !arguments.length || arguments[0] === undefined ? undefined : String(arguments[0]);
    var tag = uid(description);
    var setter = function (value) {
      if (this === ObjectPrototype) setter.call(ObjectPrototypeSymbols, value);
      if (has(this, HIDDEN) && has(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
      setSymbolDescriptor(this, tag, createPropertyDescriptor(1, value));
    };
    if (DESCRIPTORS && USE_SETTER) setSymbolDescriptor(ObjectPrototype, tag, { configurable: true, set: setter });
    return wrap(tag, description);
  };

  redefine($Symbol[PROTOTYPE], 'toString', function toString() {
    return getInternalState(this).tag;
  });

  propertyIsEnumerableModule.f = $propertyIsEnumerable;
  definePropertyModule.f = $defineProperty;
  getOwnPropertyDescriptorModule.f = $getOwnPropertyDescriptor;
  getOwnPropertyNamesModule.f = getOwnPropertyNamesExternal.f = $getOwnPropertyNames;
  getOwnPropertySymbolsModule.f = $getOwnPropertySymbols;

  if (DESCRIPTORS) {
    // https://github.com/tc39/proposal-Symbol-description
    nativeDefineProperty($Symbol[PROTOTYPE], 'description', {
      configurable: true,
      get: function description() {
        return getInternalState(this).description;
      }
    });
    if (!IS_PURE) {
      redefine(ObjectPrototype, 'propertyIsEnumerable', $propertyIsEnumerable, { unsafe: true });
    }
  }
}

if (!USE_SYMBOL_AS_UID) {
  wrappedWellKnownSymbolModule.f = function (name) {
    return wrap(wellKnownSymbol(name), name);
  };
}

$({ global: true, wrap: true, forced: !NATIVE_SYMBOL, sham: !NATIVE_SYMBOL }, {
  Symbol: $Symbol
});

$forEach(objectKeys(WellKnownSymbolsStore), function (name) {
  defineWellKnownSymbol(name);
});

$({ target: SYMBOL, stat: true, forced: !NATIVE_SYMBOL }, {
  // `Symbol.for` method
  // https://tc39.github.io/ecma262/#sec-symbol.for
  'for': function (key) {
    var string = String(key);
    if (has(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
    var symbol = $Symbol(string);
    StringToSymbolRegistry[string] = symbol;
    SymbolToStringRegistry[symbol] = string;
    return symbol;
  },
  // `Symbol.keyFor` method
  // https://tc39.github.io/ecma262/#sec-symbol.keyfor
  keyFor: function keyFor(sym) {
    if (!isSymbol(sym)) throw TypeError(sym + ' is not a symbol');
    if (has(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
  },
  useSetter: function () { USE_SETTER = true; },
  useSimple: function () { USE_SETTER = false; }
});

$({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL, sham: !DESCRIPTORS }, {
  // `Object.create` method
  // https://tc39.github.io/ecma262/#sec-object.create
  create: $create,
  // `Object.defineProperty` method
  // https://tc39.github.io/ecma262/#sec-object.defineproperty
  defineProperty: $defineProperty,
  // `Object.defineProperties` method
  // https://tc39.github.io/ecma262/#sec-object.defineproperties
  defineProperties: $defineProperties,
  // `Object.getOwnPropertyDescriptor` method
  // https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptors
  getOwnPropertyDescriptor: $getOwnPropertyDescriptor
});

$({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL }, {
  // `Object.getOwnPropertyNames` method
  // https://tc39.github.io/ecma262/#sec-object.getownpropertynames
  getOwnPropertyNames: $getOwnPropertyNames,
  // `Object.getOwnPropertySymbols` method
  // https://tc39.github.io/ecma262/#sec-object.getownpropertysymbols
  getOwnPropertySymbols: $getOwnPropertySymbols
});

// Chrome 38 and 39 `Object.getOwnPropertySymbols` fails on primitives
// https://bugs.chromium.org/p/v8/issues/detail?id=3443
$({ target: 'Object', stat: true, forced: fails(function () { getOwnPropertySymbolsModule.f(1); }) }, {
  getOwnPropertySymbols: function getOwnPropertySymbols(it) {
    return getOwnPropertySymbolsModule.f(toObject(it));
  }
});

// `JSON.stringify` method behavior with symbols
// https://tc39.github.io/ecma262/#sec-json.stringify
if ($stringify) {
  var FORCED_JSON_STRINGIFY = !NATIVE_SYMBOL || fails(function () {
    var symbol = $Symbol();
    // MS Edge converts symbol values to JSON as {}
    return $stringify([symbol]) != '[null]'
      // WebKit converts symbol values to JSON as null
      || $stringify({ a: symbol }) != '{}'
      // V8 throws on boxed symbols
      || $stringify(Object(symbol)) != '{}';
  });

  $({ target: 'JSON', stat: true, forced: FORCED_JSON_STRINGIFY }, {
    // eslint-disable-next-line no-unused-vars
    stringify: function stringify(it, replacer, space) {
      var args = [it];
      var index = 1;
      var $replacer;
      while (arguments.length > index) args.push(arguments[index++]);
      $replacer = replacer;
      if (!isObject(replacer) && it === undefined || isSymbol(it)) return; // IE8 returns string on undefined
      if (!isArray(replacer)) replacer = function (key, value) {
        if (typeof $replacer == 'function') value = $replacer.call(this, key, value);
        if (!isSymbol(value)) return value;
      };
      args[1] = replacer;
      return $stringify.apply(null, args);
    }
  });
}

// `Symbol.prototype[@@toPrimitive]` method
// https://tc39.github.io/ecma262/#sec-symbol.prototype-@@toprimitive
if (!$Symbol[PROTOTYPE][TO_PRIMITIVE]) {
  createNonEnumerableProperty($Symbol[PROTOTYPE], TO_PRIMITIVE, $Symbol[PROTOTYPE].valueOf);
}
// `Symbol.prototype[@@toStringTag]` property
// https://tc39.github.io/ecma262/#sec-symbol.prototype-@@tostringtag
setToStringTag($Symbol, SYMBOL);

hiddenKeys[HIDDEN] = true;


/***/ }),

/***/ "1524":
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ "16e3":
/***/ (function(module, exports, __webpack_require__) {

var aFunction = __webpack_require__("4fb0");

// optional / simple context binding
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 0: return function () {
      return fn.call(that);
    };
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

/***/ "172e":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("df9f");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var test = {};

test[TO_STRING_TAG] = 'z';

module.exports = String(test) === '[object z]';


/***/ }),

/***/ "175d":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "18a2":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("df9f");

var ITERATOR = wellKnownSymbol('iterator');
var SAFE_CLOSING = false;

try {
  var called = 0;
  var iteratorWithReturn = {
    next: function () {
      return { done: !!called++ };
    },
    'return': function () {
      SAFE_CLOSING = true;
    }
  };
  iteratorWithReturn[ITERATOR] = function () {
    return this;
  };
  // eslint-disable-next-line no-throw-literal
  Array.from(iteratorWithReturn, function () { throw 2; });
} catch (error) { /* empty */ }

module.exports = function (exec, SKIP_CLOSING) {
  if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
  var ITERATION_SUPPORT = false;
  try {
    var object = {};
    object[ITERATOR] = function () {
      return {
        next: function () {
          return { done: ITERATION_SUPPORT = true };
        }
      };
    };
    exec(object);
  } catch (error) { /* empty */ }
  return ITERATION_SUPPORT;
};


/***/ }),

/***/ "1a2e":
/***/ (function(module, exports, __webpack_require__) {

var NATIVE_WEAK_MAP = __webpack_require__("299c");
var global = __webpack_require__("92f7");
var isObject = __webpack_require__("7313");
var createNonEnumerableProperty = __webpack_require__("88a7");
var objectHas = __webpack_require__("20bb");
var sharedKey = __webpack_require__("7408");
var hiddenKeys = __webpack_require__("cc24");

var WeakMap = global.WeakMap;
var set, get, has;

var enforce = function (it) {
  return has(it) ? get(it) : set(it, {});
};

var getterFor = function (TYPE) {
  return function (it) {
    var state;
    if (!isObject(it) || (state = get(it)).type !== TYPE) {
      throw TypeError('Incompatible receiver, ' + TYPE + ' required');
    } return state;
  };
};

if (NATIVE_WEAK_MAP) {
  var store = new WeakMap();
  var wmget = store.get;
  var wmhas = store.has;
  var wmset = store.set;
  set = function (it, metadata) {
    wmset.call(store, it, metadata);
    return metadata;
  };
  get = function (it) {
    return wmget.call(store, it) || {};
  };
  has = function (it) {
    return wmhas.call(store, it);
  };
} else {
  var STATE = sharedKey('state');
  hiddenKeys[STATE] = true;
  set = function (it, metadata) {
    createNonEnumerableProperty(it, STATE, metadata);
    return metadata;
  };
  get = function (it) {
    return objectHas(it, STATE) ? it[STATE] : {};
  };
  has = function (it) {
    return objectHas(it, STATE);
  };
}

module.exports = {
  set: set,
  get: get,
  has: has,
  enforce: enforce,
  getterFor: getterFor
};


/***/ }),

/***/ "1feb":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");
var wellKnownSymbol = __webpack_require__("df9f");
var V8_VERSION = __webpack_require__("606e");

var SPECIES = wellKnownSymbol('species');

module.exports = function (METHOD_NAME) {
  // We can't use this feature detection in V8 since it causes
  // deoptimization and serious performance degradation
  // https://github.com/zloirock/core-js/issues/677
  return V8_VERSION >= 51 || !fails(function () {
    var array = [];
    var constructor = array.constructor = {};
    constructor[SPECIES] = function () {
      return { foo: 1 };
    };
    return array[METHOD_NAME](Boolean).foo !== 1;
  });
};


/***/ }),

/***/ "20bb":
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;

module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ "2373":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "24f5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var defineProperty = __webpack_require__("eae2").f;
var create = __webpack_require__("ad3e");
var redefineAll = __webpack_require__("fb1e");
var bind = __webpack_require__("16e3");
var anInstance = __webpack_require__("39e9");
var iterate = __webpack_require__("fe22");
var defineIterator = __webpack_require__("f7da");
var setSpecies = __webpack_require__("ad0f");
var DESCRIPTORS = __webpack_require__("50ce");
var fastKey = __webpack_require__("07c1").fastKey;
var InternalStateModule = __webpack_require__("1a2e");

var setInternalState = InternalStateModule.set;
var internalStateGetterFor = InternalStateModule.getterFor;

module.exports = {
  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
    var C = wrapper(function (that, iterable) {
      anInstance(that, C, CONSTRUCTOR_NAME);
      setInternalState(that, {
        type: CONSTRUCTOR_NAME,
        index: create(null),
        first: undefined,
        last: undefined,
        size: 0
      });
      if (!DESCRIPTORS) that.size = 0;
      if (iterable != undefined) iterate(iterable, that[ADDER], that, IS_MAP);
    });

    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

    var define = function (that, key, value) {
      var state = getInternalState(that);
      var entry = getEntry(that, key);
      var previous, index;
      // change existing entry
      if (entry) {
        entry.value = value;
      // create new entry
      } else {
        state.last = entry = {
          index: index = fastKey(key, true),
          key: key,
          value: value,
          previous: previous = state.last,
          next: undefined,
          removed: false
        };
        if (!state.first) state.first = entry;
        if (previous) previous.next = entry;
        if (DESCRIPTORS) state.size++;
        else that.size++;
        // add to index
        if (index !== 'F') state.index[index] = entry;
      } return that;
    };

    var getEntry = function (that, key) {
      var state = getInternalState(that);
      // fast case
      var index = fastKey(key);
      var entry;
      if (index !== 'F') return state.index[index];
      // frozen object case
      for (entry = state.first; entry; entry = entry.next) {
        if (entry.key == key) return entry;
      }
    };

    redefineAll(C.prototype, {
      // 23.1.3.1 Map.prototype.clear()
      // 23.2.3.2 Set.prototype.clear()
      clear: function clear() {
        var that = this;
        var state = getInternalState(that);
        var data = state.index;
        var entry = state.first;
        while (entry) {
          entry.removed = true;
          if (entry.previous) entry.previous = entry.previous.next = undefined;
          delete data[entry.index];
          entry = entry.next;
        }
        state.first = state.last = undefined;
        if (DESCRIPTORS) state.size = 0;
        else that.size = 0;
      },
      // 23.1.3.3 Map.prototype.delete(key)
      // 23.2.3.4 Set.prototype.delete(value)
      'delete': function (key) {
        var that = this;
        var state = getInternalState(that);
        var entry = getEntry(that, key);
        if (entry) {
          var next = entry.next;
          var prev = entry.previous;
          delete state.index[entry.index];
          entry.removed = true;
          if (prev) prev.next = next;
          if (next) next.previous = prev;
          if (state.first == entry) state.first = next;
          if (state.last == entry) state.last = prev;
          if (DESCRIPTORS) state.size--;
          else that.size--;
        } return !!entry;
      },
      // 23.2.3.6 Set.prototype.forEach(callbackfn, thisArg = undefined)
      // 23.1.3.5 Map.prototype.forEach(callbackfn, thisArg = undefined)
      forEach: function forEach(callbackfn /* , that = undefined */) {
        var state = getInternalState(this);
        var boundFunction = bind(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
        var entry;
        while (entry = entry ? entry.next : state.first) {
          boundFunction(entry.value, entry.key, this);
          // revert to the last existing entry
          while (entry && entry.removed) entry = entry.previous;
        }
      },
      // 23.1.3.7 Map.prototype.has(key)
      // 23.2.3.7 Set.prototype.has(value)
      has: function has(key) {
        return !!getEntry(this, key);
      }
    });

    redefineAll(C.prototype, IS_MAP ? {
      // 23.1.3.6 Map.prototype.get(key)
      get: function get(key) {
        var entry = getEntry(this, key);
        return entry && entry.value;
      },
      // 23.1.3.9 Map.prototype.set(key, value)
      set: function set(key, value) {
        return define(this, key === 0 ? 0 : key, value);
      }
    } : {
      // 23.2.3.1 Set.prototype.add(value)
      add: function add(value) {
        return define(this, value = value === 0 ? 0 : value, value);
      }
    });
    if (DESCRIPTORS) defineProperty(C.prototype, 'size', {
      get: function () {
        return getInternalState(this).size;
      }
    });
    return C;
  },
  setStrong: function (C, CONSTRUCTOR_NAME, IS_MAP) {
    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
    var getInternalCollectionState = internalStateGetterFor(CONSTRUCTOR_NAME);
    var getInternalIteratorState = internalStateGetterFor(ITERATOR_NAME);
    // add .keys, .values, .entries, [@@iterator]
    // 23.1.3.4, 23.1.3.8, 23.1.3.11, 23.1.3.12, 23.2.3.5, 23.2.3.8, 23.2.3.10, 23.2.3.11
    defineIterator(C, CONSTRUCTOR_NAME, function (iterated, kind) {
      setInternalState(this, {
        type: ITERATOR_NAME,
        target: iterated,
        state: getInternalCollectionState(iterated),
        kind: kind,
        last: undefined
      });
    }, function () {
      var state = getInternalIteratorState(this);
      var kind = state.kind;
      var entry = state.last;
      // revert to the last existing entry
      while (entry && entry.removed) entry = entry.previous;
      // get next entry
      if (!state.target || !(state.last = entry = entry ? entry.next : state.state.first)) {
        // or finish the iteration
        state.target = undefined;
        return { value: undefined, done: true };
      }
      // return step by kind
      if (kind == 'keys') return { value: entry.key, done: false };
      if (kind == 'values') return { value: entry.value, done: false };
      return { value: [entry.key, entry.value], done: false };
    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

    // add [@@species], 23.1.2.2, 23.2.2.2
    setSpecies(CONSTRUCTOR_NAME);
  }
};


/***/ }),

/***/ "267a":
/***/ (function(module, exports) {

var id = 0;
var postfix = Math.random();

module.exports = function (key) {
  return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id + postfix).toString(36);
};


/***/ }),

/***/ "2739":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("b6b3");
var from = __webpack_require__("6fb7");
var checkCorrectnessOfIteration = __webpack_require__("18a2");

var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
  Array.from(iterable);
});

// `Array.from` method
// https://tc39.github.io/ecma262/#sec-array.from
$({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
  from: from
});


/***/ }),

/***/ "299c":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var inspectSource = __webpack_require__("ab8d");

var WeakMap = global.WeakMap;

module.exports = typeof WeakMap === 'function' && /native code/.test(inspectSource(WeakMap));


/***/ }),

/***/ "2f3b":
/***/ (function(module, exports, __webpack_require__) {

var getBuiltIn = __webpack_require__("fe3e");

module.exports = getBuiltIn('navigator', 'userAgent') || '';


/***/ }),

/***/ "31af":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var $filter = __webpack_require__("a1d8").filter;
var fails = __webpack_require__("3903");
var arrayMethodHasSpeciesSupport = __webpack_require__("1feb");

var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');
// Edge 14- issue
var USES_TO_LENGTH = HAS_SPECIES_SUPPORT && !fails(function () {
  [].filter.call({ length: -1, 0: 1 }, function (it) { throw it; });
});

// `Array.prototype.filter` method
// https://tc39.github.io/ecma262/#sec-array.prototype.filter
// with adding support of @@species
$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT || !USES_TO_LENGTH }, {
  filter: function filter(callbackfn /* , thisArg */) {
    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "3596":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("b6b3");
var isObject = __webpack_require__("7313");
var anObject = __webpack_require__("0e66");
var has = __webpack_require__("20bb");
var getOwnPropertyDescriptorModule = __webpack_require__("84e7");
var getPrototypeOf = __webpack_require__("c503");

// `Reflect.get` method
// https://tc39.github.io/ecma262/#sec-reflect.get
function get(target, propertyKey /* , receiver */) {
  var receiver = arguments.length < 3 ? target : arguments[2];
  var descriptor, prototype;
  if (anObject(target) === receiver) return target[propertyKey];
  if (descriptor = getOwnPropertyDescriptorModule.f(target, propertyKey)) return has(descriptor, 'value')
    ? descriptor.value
    : descriptor.get === undefined
      ? undefined
      : descriptor.get.call(receiver);
  if (isObject(prototype = getPrototypeOf(target))) return get(prototype, propertyKey, receiver);
}

$({ target: 'Reflect', stat: true }, {
  get: get
});


/***/ }),

/***/ "362c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var toPrimitive = __webpack_require__("03de");
var definePropertyModule = __webpack_require__("eae2");
var createPropertyDescriptor = __webpack_require__("7234");

module.exports = function (object, key, value) {
  var propertyKey = toPrimitive(key);
  if (propertyKey in object) definePropertyModule.f(object, propertyKey, createPropertyDescriptor(0, value));
  else object[propertyKey] = value;
};


/***/ }),

/***/ "3768":
/***/ (function(module, exports, __webpack_require__) {

var internalObjectKeys = __webpack_require__("a988");
var enumBugKeys = __webpack_require__("757f");

var hiddenKeys = enumBugKeys.concat('length', 'prototype');

// `Object.getOwnPropertyNames` method
// https://tc39.github.io/ecma262/#sec-object.getownpropertynames
exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return internalObjectKeys(O, hiddenKeys);
};


/***/ }),

/***/ "3815":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var fails = __webpack_require__("3903");
var toObject = __webpack_require__("40f9");
var toPrimitive = __webpack_require__("03de");

var FORCED = fails(function () {
  return new Date(NaN).toJSON() !== null
    || Date.prototype.toJSON.call({ toISOString: function () { return 1; } }) !== 1;
});

// `Date.prototype.toJSON` method
// https://tc39.github.io/ecma262/#sec-date.prototype.tojson
$({ target: 'Date', proto: true, forced: FORCED }, {
  // eslint-disable-next-line no-unused-vars
  toJSON: function toJSON(key) {
    var O = toObject(this);
    var pv = toPrimitive(O);
    return typeof pv == 'number' && !isFinite(pv) ? null : O.toISOString();
  }
});


/***/ }),

/***/ "389a":
/***/ (function(module, exports) {

// iterable DOM collections
// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
module.exports = {
  CSSRuleList: 0,
  CSSStyleDeclaration: 0,
  CSSValueList: 0,
  ClientRectList: 0,
  DOMRectList: 0,
  DOMStringList: 0,
  DOMTokenList: 1,
  DataTransferItemList: 0,
  FileList: 0,
  HTMLAllCollection: 0,
  HTMLCollection: 0,
  HTMLFormElement: 0,
  HTMLSelectElement: 0,
  MediaList: 0,
  MimeTypeArray: 0,
  NamedNodeMap: 0,
  NodeList: 1,
  PaintRequestList: 0,
  Plugin: 0,
  PluginArray: 0,
  SVGLengthList: 0,
  SVGNumberList: 0,
  SVGPathSegList: 0,
  SVGPointList: 0,
  SVGStringList: 0,
  SVGTransformList: 0,
  SourceBufferList: 0,
  StyleSheetList: 0,
  TextTrackCueList: 0,
  TextTrackList: 0,
  TouchList: 0
};


/***/ }),

/***/ "3903":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (error) {
    return true;
  }
};


/***/ }),

/***/ "39e9":
/***/ (function(module, exports) {

module.exports = function (it, Constructor, name) {
  if (!(it instanceof Constructor)) {
    throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
  } return it;
};


/***/ }),

/***/ "3b4d":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("0de9");

// `IsArray` abstract operation
// https://tc39.github.io/ecma262/#sec-isarray
module.exports = Array.isArray || function isArray(arg) {
  return classof(arg) == 'Array';
};


/***/ }),

/***/ "3e56":
/***/ (function(module, exports, __webpack_require__) {

var aFunction = __webpack_require__("4fb0");
var toObject = __webpack_require__("40f9");
var IndexedObject = __webpack_require__("e561");
var toLength = __webpack_require__("fcce");

// `Array.prototype.{ reduce, reduceRight }` methods implementation
var createMethod = function (IS_RIGHT) {
  return function (that, callbackfn, argumentsLength, memo) {
    aFunction(callbackfn);
    var O = toObject(that);
    var self = IndexedObject(O);
    var length = toLength(O.length);
    var index = IS_RIGHT ? length - 1 : 0;
    var i = IS_RIGHT ? -1 : 1;
    if (argumentsLength < 2) while (true) {
      if (index in self) {
        memo = self[index];
        index += i;
        break;
      }
      index += i;
      if (IS_RIGHT ? index < 0 : length <= index) {
        throw TypeError('Reduce of empty array with no initial value');
      }
    }
    for (;IS_RIGHT ? index >= 0 : length > index; index += i) if (index in self) {
      memo = callbackfn(memo, self[index], index, O);
    }
    return memo;
  };
};

module.exports = {
  // `Array.prototype.reduce` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.reduce
  left: createMethod(false),
  // `Array.prototype.reduceRight` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.reduceright
  right: createMethod(true)
};


/***/ }),

/***/ "3fb6":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");

module.exports = !!Object.getOwnPropertySymbols && !fails(function () {
  // Chrome 38 Symbol has incorrect toString conversion
  // eslint-disable-next-line no-undef
  return !String(Symbol());
});


/***/ }),

/***/ "40f9":
/***/ (function(module, exports, __webpack_require__) {

var requireObjectCoercible = __webpack_require__("9522");

// `ToObject` abstract operation
// https://tc39.github.io/ecma262/#sec-toobject
module.exports = function (argument) {
  return Object(requireObjectCoercible(argument));
};


/***/ }),

/***/ "4e83":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var DOMIterables = __webpack_require__("389a");
var ArrayIteratorMethods = __webpack_require__("7f72");
var createNonEnumerableProperty = __webpack_require__("88a7");
var wellKnownSymbol = __webpack_require__("df9f");

var ITERATOR = wellKnownSymbol('iterator');
var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var ArrayValues = ArrayIteratorMethods.values;

for (var COLLECTION_NAME in DOMIterables) {
  var Collection = global[COLLECTION_NAME];
  var CollectionPrototype = Collection && Collection.prototype;
  if (CollectionPrototype) {
    // some Chrome versions have non-configurable methods on DOMTokenList
    if (CollectionPrototype[ITERATOR] !== ArrayValues) try {
      createNonEnumerableProperty(CollectionPrototype, ITERATOR, ArrayValues);
    } catch (error) {
      CollectionPrototype[ITERATOR] = ArrayValues;
    }
    if (!CollectionPrototype[TO_STRING_TAG]) {
      createNonEnumerableProperty(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
    }
    if (DOMIterables[COLLECTION_NAME]) for (var METHOD_NAME in ArrayIteratorMethods) {
      // some Chrome versions have non-configurable methods on DOMTokenList
      if (CollectionPrototype[METHOD_NAME] !== ArrayIteratorMethods[METHOD_NAME]) try {
        createNonEnumerableProperty(CollectionPrototype, METHOD_NAME, ArrayIteratorMethods[METHOD_NAME]);
      } catch (error) {
        CollectionPrototype[METHOD_NAME] = ArrayIteratorMethods[METHOD_NAME];
      }
    }
  }
}


/***/ }),

/***/ "4fb0":
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') {
    throw TypeError(String(it) + ' is not a function');
  } return it;
};


/***/ }),

/***/ "5028":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var fails = __webpack_require__("3903");

module.exports = function (METHOD_NAME, argument) {
  var method = [][METHOD_NAME];
  return !method || !fails(function () {
    // eslint-disable-next-line no-useless-call,no-throw-literal
    method.call(null, argument || function () { throw 1; }, 1);
  });
};


/***/ }),

/***/ "50ce":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");

// Thank's IE8 for his funny defineProperty
module.exports = !fails(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "50e5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var redefine = __webpack_require__("8b07");
var anObject = __webpack_require__("0e66");
var fails = __webpack_require__("3903");
var flags = __webpack_require__("9b01");

var TO_STRING = 'toString';
var RegExpPrototype = RegExp.prototype;
var nativeToString = RegExpPrototype[TO_STRING];

var NOT_GENERIC = fails(function () { return nativeToString.call({ source: 'a', flags: 'b' }) != '/a/b'; });
// FF44- RegExp#toString has a wrong name
var INCORRECT_NAME = nativeToString.name != TO_STRING;

// `RegExp.prototype.toString` method
// https://tc39.github.io/ecma262/#sec-regexp.prototype.tostring
if (NOT_GENERIC || INCORRECT_NAME) {
  redefine(RegExp.prototype, TO_STRING, function toString() {
    var R = anObject(this);
    var p = String(R.source);
    var rf = R.flags;
    var f = String(rf === undefined && R instanceof RegExp && !('flags' in RegExpPrototype) ? flags.call(R) : rf);
    return '/' + p + '/' + f;
  }, { unsafe: true });
}


/***/ }),

/***/ "569f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $forEach = __webpack_require__("a1d8").forEach;
var sloppyArrayMethod = __webpack_require__("5028");

// `Array.prototype.forEach` method implementation
// https://tc39.github.io/ecma262/#sec-array.prototype.foreach
module.exports = sloppyArrayMethod('forEach') ? function forEach(callbackfn /* , thisArg */) {
  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
} : [].forEach;


/***/ }),

/***/ "58c5":
/***/ (function(module, exports, __webpack_require__) {

var internalObjectKeys = __webpack_require__("a988");
var enumBugKeys = __webpack_require__("757f");

// `Object.keys` method
// https://tc39.github.io/ecma262/#sec-object.keys
module.exports = Object.keys || function keys(O) {
  return internalObjectKeys(O, enumBugKeys);
};


/***/ }),

/***/ "5929":
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

/***/ "5dc1":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");

var replacement = /#|\.prototype\./;

var isForced = function (feature, detection) {
  var value = data[normalize(feature)];
  return value == POLYFILL ? true
    : value == NATIVE ? false
    : typeof detection == 'function' ? fails(detection)
    : !!detection;
};

var normalize = isForced.normalize = function (string) {
  return String(string).replace(replacement, '.').toLowerCase();
};

var data = isForced.data = {};
var NATIVE = isForced.NATIVE = 'N';
var POLYFILL = isForced.POLYFILL = 'P';

module.exports = isForced;


/***/ }),

/***/ "5ff6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Collapse_vue_vue_type_style_index_0_id_0488f0db_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("b631");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Collapse_vue_vue_type_style_index_0_id_0488f0db_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Collapse_vue_vue_type_style_index_0_id_0488f0db_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Collapse_vue_vue_type_style_index_0_id_0488f0db_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "606e":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var userAgent = __webpack_require__("2f3b");

var process = global.process;
var versions = process && process.versions;
var v8 = versions && versions.v8;
var match, version;

if (v8) {
  match = v8.split('.');
  version = match[0] + match[1];
} else if (userAgent) {
  match = userAgent.match(/Edge\/(\d+)/);
  if (!match || match[1] >= 74) {
    match = userAgent.match(/Chrome\/(\d+)/);
    if (match) version = match[1];
  }
}

module.exports = version && +version;


/***/ }),

/***/ "6238":
/***/ (function(module, exports, __webpack_require__) {

var path = __webpack_require__("757d");
var has = __webpack_require__("20bb");
var wrappedWellKnownSymbolModule = __webpack_require__("d4cb");
var defineProperty = __webpack_require__("eae2").f;

module.exports = function (NAME) {
  var Symbol = path.Symbol || (path.Symbol = {});
  if (!has(Symbol, NAME)) defineProperty(Symbol, NAME, {
    value: wrappedWellKnownSymbolModule.f(NAME)
  });
};


/***/ }),

/***/ "64e0":
/***/ (function(module, exports, __webpack_require__) {

var toIndexedObject = __webpack_require__("f11f");
var toLength = __webpack_require__("fcce");
var toAbsoluteIndex = __webpack_require__("d909");

// `Array.prototype.{ indexOf, includes }` methods implementation
var createMethod = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIndexedObject($this);
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
    } else for (;length > index; index++) {
      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

module.exports = {
  // `Array.prototype.includes` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.includes
  includes: createMethod(true),
  // `Array.prototype.indexOf` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.indexof
  indexOf: createMethod(false)
};


/***/ }),

/***/ "6540":
/***/ (function(module, exports, __webpack_require__) {

var defineWellKnownSymbol = __webpack_require__("6238");

// `Symbol.iterator` well-known symbol
// https://tc39.github.io/ecma262/#sec-symbol.iterator
defineWellKnownSymbol('iterator');


/***/ }),

/***/ "654a":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var createNonEnumerableProperty = __webpack_require__("88a7");

module.exports = function (key, value) {
  try {
    createNonEnumerableProperty(global, key, value);
  } catch (error) {
    global[key] = value;
  } return value;
};


/***/ }),

/***/ "6d5f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var $indexOf = __webpack_require__("64e0").indexOf;
var sloppyArrayMethod = __webpack_require__("5028");

var nativeIndexOf = [].indexOf;

var NEGATIVE_ZERO = !!nativeIndexOf && 1 / [1].indexOf(1, -0) < 0;
var SLOPPY_METHOD = sloppyArrayMethod('indexOf');

// `Array.prototype.indexOf` method
// https://tc39.github.io/ecma262/#sec-array.prototype.indexof
$({ target: 'Array', proto: true, forced: NEGATIVE_ZERO || SLOPPY_METHOD }, {
  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
    return NEGATIVE_ZERO
      // convert -0 to +0
      ? nativeIndexOf.apply(this, arguments) || 0
      : $indexOf(this, searchElement, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "6d8c":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var DOMIterables = __webpack_require__("389a");
var forEach = __webpack_require__("569f");
var createNonEnumerableProperty = __webpack_require__("88a7");

for (var COLLECTION_NAME in DOMIterables) {
  var Collection = global[COLLECTION_NAME];
  var CollectionPrototype = Collection && Collection.prototype;
  // some Chrome versions have non-configurable methods on DOMTokenList
  if (CollectionPrototype && CollectionPrototype.forEach !== forEach) try {
    createNonEnumerableProperty(CollectionPrototype, 'forEach', forEach);
  } catch (error) {
    CollectionPrototype.forEach = forEach;
  }
}


/***/ }),

/***/ "6e52":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("7313");
var isArray = __webpack_require__("3b4d");
var wellKnownSymbol = __webpack_require__("df9f");

var SPECIES = wellKnownSymbol('species');

// `ArraySpeciesCreate` abstract operation
// https://tc39.github.io/ecma262/#sec-arrayspeciescreate
module.exports = function (originalArray, length) {
  var C;
  if (isArray(originalArray)) {
    C = originalArray.constructor;
    // cross-realm fallback
    if (typeof C == 'function' && (C === Array || isArray(C.prototype))) C = undefined;
    else if (isObject(C)) {
      C = C[SPECIES];
      if (C === null) C = undefined;
    }
  } return new (C === undefined ? Array : C)(length === 0 ? 0 : length);
};


/***/ }),

/***/ "6e9e":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var getPrototypeOf = __webpack_require__("c503");
var createNonEnumerableProperty = __webpack_require__("88a7");
var has = __webpack_require__("20bb");
var wellKnownSymbol = __webpack_require__("df9f");
var IS_PURE = __webpack_require__("c009");

var ITERATOR = wellKnownSymbol('iterator');
var BUGGY_SAFARI_ITERATORS = false;

var returnThis = function () { return this; };

// `%IteratorPrototype%` object
// https://tc39.github.io/ecma262/#sec-%iteratorprototype%-object
var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

if ([].keys) {
  arrayIterator = [].keys();
  // Safari 8 has buggy iterators w/o `next`
  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;
  else {
    PrototypeOfArrayIteratorPrototype = getPrototypeOf(getPrototypeOf(arrayIterator));
    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
  }
}

if (IteratorPrototype == undefined) IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
if (!IS_PURE && !has(IteratorPrototype, ITERATOR)) {
  createNonEnumerableProperty(IteratorPrototype, ITERATOR, returnThis);
}

module.exports = {
  IteratorPrototype: IteratorPrototype,
  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
};


/***/ }),

/***/ "6eee":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var collection = __webpack_require__("8303");
var collectionStrong = __webpack_require__("24f5");

// `Map` constructor
// https://tc39.github.io/ecma262/#sec-map-objects
module.exports = collection('Map', function (init) {
  return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
}, collectionStrong);


/***/ }),

/***/ "6f0f":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("f4c8");
var Iterators = __webpack_require__("ae74");
var wellKnownSymbol = __webpack_require__("df9f");

var ITERATOR = wellKnownSymbol('iterator');

module.exports = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),

/***/ "6fb7":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var bind = __webpack_require__("16e3");
var toObject = __webpack_require__("40f9");
var callWithSafeIterationClosing = __webpack_require__("ecaa");
var isArrayIteratorMethod = __webpack_require__("e5a5");
var toLength = __webpack_require__("fcce");
var createProperty = __webpack_require__("362c");
var getIteratorMethod = __webpack_require__("6f0f");

// `Array.from` method implementation
// https://tc39.github.io/ecma262/#sec-array.from
module.exports = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
  var O = toObject(arrayLike);
  var C = typeof this == 'function' ? this : Array;
  var argumentsLength = arguments.length;
  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
  var mapping = mapfn !== undefined;
  var index = 0;
  var iteratorMethod = getIteratorMethod(O);
  var length, result, step, iterator, next;
  if (mapping) mapfn = bind(mapfn, argumentsLength > 2 ? arguments[2] : undefined, 2);
  // if the target is not iterable or it's an array with the default iterator - use a simple case
  if (iteratorMethod != undefined && !(C == Array && isArrayIteratorMethod(iteratorMethod))) {
    iterator = iteratorMethod.call(O);
    next = iterator.next;
    result = new C();
    for (;!(step = next.call(iterator)).done; index++) {
      createProperty(result, index, mapping
        ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true)
        : step.value
      );
    }
  } else {
    length = toLength(O.length);
    result = new C(length);
    for (;length > index; index++) {
      createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
    }
  }
  result.length = index;
  return result;
};


/***/ }),

/***/ "7234":
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

/***/ "7313":
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ "7408":
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__("b802");
var uid = __webpack_require__("267a");

var keys = shared('keys');

module.exports = function (key) {
  return keys[key] || (keys[key] = uid(key));
};


/***/ }),

/***/ "757d":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");

module.exports = global;


/***/ }),

/***/ "757f":
/***/ (function(module, exports) {

// IE8- don't enum bug keys
module.exports = [
  'constructor',
  'hasOwnProperty',
  'isPrototypeOf',
  'propertyIsEnumerable',
  'toLocaleString',
  'toString',
  'valueOf'
];


/***/ }),

/***/ "7a70":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Configuration_vue_vue_type_style_index_0_id_921e9e6a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("175d");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Configuration_vue_vue_type_style_index_0_id_921e9e6a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Configuration_vue_vue_type_style_index_0_id_921e9e6a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Configuration_vue_vue_type_style_index_0_id_921e9e6a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "7bb8":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("b6b3");
var fails = __webpack_require__("3903");
var toIndexedObject = __webpack_require__("f11f");
var nativeGetOwnPropertyDescriptor = __webpack_require__("84e7").f;
var DESCRIPTORS = __webpack_require__("50ce");

var FAILS_ON_PRIMITIVES = fails(function () { nativeGetOwnPropertyDescriptor(1); });
var FORCED = !DESCRIPTORS || FAILS_ON_PRIMITIVES;

// `Object.getOwnPropertyDescriptor` method
// https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptor
$({ target: 'Object', stat: true, forced: FORCED, sham: !DESCRIPTORS }, {
  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(it, key) {
    return nativeGetOwnPropertyDescriptor(toIndexedObject(it), key);
  }
});


/***/ }),

/***/ "7f72":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var toIndexedObject = __webpack_require__("f11f");
var addToUnscopables = __webpack_require__("d5a6");
var Iterators = __webpack_require__("ae74");
var InternalStateModule = __webpack_require__("1a2e");
var defineIterator = __webpack_require__("f7da");

var ARRAY_ITERATOR = 'Array Iterator';
var setInternalState = InternalStateModule.set;
var getInternalState = InternalStateModule.getterFor(ARRAY_ITERATOR);

// `Array.prototype.entries` method
// https://tc39.github.io/ecma262/#sec-array.prototype.entries
// `Array.prototype.keys` method
// https://tc39.github.io/ecma262/#sec-array.prototype.keys
// `Array.prototype.values` method
// https://tc39.github.io/ecma262/#sec-array.prototype.values
// `Array.prototype[@@iterator]` method
// https://tc39.github.io/ecma262/#sec-array.prototype-@@iterator
// `CreateArrayIterator` internal method
// https://tc39.github.io/ecma262/#sec-createarrayiterator
module.exports = defineIterator(Array, 'Array', function (iterated, kind) {
  setInternalState(this, {
    type: ARRAY_ITERATOR,
    target: toIndexedObject(iterated), // target
    index: 0,                          // next index
    kind: kind                         // kind
  });
// `%ArrayIteratorPrototype%.next` method
// https://tc39.github.io/ecma262/#sec-%arrayiteratorprototype%.next
}, function () {
  var state = getInternalState(this);
  var target = state.target;
  var kind = state.kind;
  var index = state.index++;
  if (!target || index >= target.length) {
    state.target = undefined;
    return { value: undefined, done: true };
  }
  if (kind == 'keys') return { value: index, done: false };
  if (kind == 'values') return { value: target[index], done: false };
  return { value: [index, target[index]], done: false };
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values%
// https://tc39.github.io/ecma262/#sec-createunmappedargumentsobject
// https://tc39.github.io/ecma262/#sec-createmappedargumentsobject
Iterators.Arguments = Iterators.Array;

// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');


/***/ }),

/***/ "7fbc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var fails = __webpack_require__("3903");
var isArray = __webpack_require__("3b4d");
var isObject = __webpack_require__("7313");
var toObject = __webpack_require__("40f9");
var toLength = __webpack_require__("fcce");
var createProperty = __webpack_require__("362c");
var arraySpeciesCreate = __webpack_require__("6e52");
var arrayMethodHasSpeciesSupport = __webpack_require__("1feb");
var wellKnownSymbol = __webpack_require__("df9f");
var V8_VERSION = __webpack_require__("606e");

var IS_CONCAT_SPREADABLE = wellKnownSymbol('isConcatSpreadable');
var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF;
var MAXIMUM_ALLOWED_INDEX_EXCEEDED = 'Maximum allowed index exceeded';

// We can't use this feature detection in V8 since it causes
// deoptimization and serious performance degradation
// https://github.com/zloirock/core-js/issues/679
var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION >= 51 || !fails(function () {
  var array = [];
  array[IS_CONCAT_SPREADABLE] = false;
  return array.concat()[0] !== array;
});

var SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('concat');

var isConcatSpreadable = function (O) {
  if (!isObject(O)) return false;
  var spreadable = O[IS_CONCAT_SPREADABLE];
  return spreadable !== undefined ? !!spreadable : isArray(O);
};

var FORCED = !IS_CONCAT_SPREADABLE_SUPPORT || !SPECIES_SUPPORT;

// `Array.prototype.concat` method
// https://tc39.github.io/ecma262/#sec-array.prototype.concat
// with adding support of @@isConcatSpreadable and @@species
$({ target: 'Array', proto: true, forced: FORCED }, {
  concat: function concat(arg) { // eslint-disable-line no-unused-vars
    var O = toObject(this);
    var A = arraySpeciesCreate(O, 0);
    var n = 0;
    var i, k, length, len, E;
    for (i = -1, length = arguments.length; i < length; i++) {
      E = i === -1 ? O : arguments[i];
      if (isConcatSpreadable(E)) {
        len = toLength(E.length);
        if (n + len > MAX_SAFE_INTEGER) throw TypeError(MAXIMUM_ALLOWED_INDEX_EXCEEDED);
        for (k = 0; k < len; k++, n++) if (k in E) createProperty(A, n, E[k]);
      } else {
        if (n >= MAX_SAFE_INTEGER) throw TypeError(MAXIMUM_ALLOWED_INDEX_EXCEEDED);
        createProperty(A, n++, E);
      }
    }
    A.length = n;
    return A;
  }
});


/***/ }),

/***/ "7ff6":
/***/ (function(module, exports, __webpack_require__) {

var toIndexedObject = __webpack_require__("f11f");
var nativeGetOwnPropertyNames = __webpack_require__("3768").f;

var toString = {}.toString;

var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
  ? Object.getOwnPropertyNames(window) : [];

var getWindowNames = function (it) {
  try {
    return nativeGetOwnPropertyNames(it);
  } catch (error) {
    return windowNames.slice();
  }
};

// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
module.exports.f = function getOwnPropertyNames(it) {
  return windowNames && toString.call(it) == '[object Window]'
    ? getWindowNames(it)
    : nativeGetOwnPropertyNames(toIndexedObject(it));
};


/***/ }),

/***/ "82ae":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("7313");
var setPrototypeOf = __webpack_require__("a6d2");

// makes subclassing work correct for wrapped built-ins
module.exports = function ($this, dummy, Wrapper) {
  var NewTarget, NewTargetPrototype;
  if (
    // it can work only with native `setPrototypeOf`
    setPrototypeOf &&
    // we haven't completely correct pre-ES6 way for getting `new.target`, so use this
    typeof (NewTarget = dummy.constructor) == 'function' &&
    NewTarget !== Wrapper &&
    isObject(NewTargetPrototype = NewTarget.prototype) &&
    NewTargetPrototype !== Wrapper.prototype
  ) setPrototypeOf($this, NewTargetPrototype);
  return $this;
};


/***/ }),

/***/ "8303":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var global = __webpack_require__("92f7");
var isForced = __webpack_require__("5dc1");
var redefine = __webpack_require__("8b07");
var InternalMetadataModule = __webpack_require__("07c1");
var iterate = __webpack_require__("fe22");
var anInstance = __webpack_require__("39e9");
var isObject = __webpack_require__("7313");
var fails = __webpack_require__("3903");
var checkCorrectnessOfIteration = __webpack_require__("18a2");
var setToStringTag = __webpack_require__("9f8d");
var inheritIfRequired = __webpack_require__("82ae");

module.exports = function (CONSTRUCTOR_NAME, wrapper, common) {
  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
  var ADDER = IS_MAP ? 'set' : 'add';
  var NativeConstructor = global[CONSTRUCTOR_NAME];
  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
  var Constructor = NativeConstructor;
  var exported = {};

  var fixMethod = function (KEY) {
    var nativeMethod = NativePrototype[KEY];
    redefine(NativePrototype, KEY,
      KEY == 'add' ? function add(value) {
        nativeMethod.call(this, value === 0 ? 0 : value);
        return this;
      } : KEY == 'delete' ? function (key) {
        return IS_WEAK && !isObject(key) ? false : nativeMethod.call(this, key === 0 ? 0 : key);
      } : KEY == 'get' ? function get(key) {
        return IS_WEAK && !isObject(key) ? undefined : nativeMethod.call(this, key === 0 ? 0 : key);
      } : KEY == 'has' ? function has(key) {
        return IS_WEAK && !isObject(key) ? false : nativeMethod.call(this, key === 0 ? 0 : key);
      } : function set(key, value) {
        nativeMethod.call(this, key === 0 ? 0 : key, value);
        return this;
      }
    );
  };

  // eslint-disable-next-line max-len
  if (isForced(CONSTRUCTOR_NAME, typeof NativeConstructor != 'function' || !(IS_WEAK || NativePrototype.forEach && !fails(function () {
    new NativeConstructor().entries().next();
  })))) {
    // create collection constructor
    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
    InternalMetadataModule.REQUIRED = true;
  } else if (isForced(CONSTRUCTOR_NAME, true)) {
    var instance = new Constructor();
    // early implementations not supports chaining
    var HASNT_CHAINING = instance[ADDER](IS_WEAK ? {} : -0, 1) != instance;
    // V8 ~ Chromium 40- weak-collections throws on primitives, but should return false
    var THROWS_ON_PRIMITIVES = fails(function () { instance.has(1); });
    // most early implementations doesn't supports iterables, most modern - not close it correctly
    // eslint-disable-next-line no-new
    var ACCEPT_ITERABLES = checkCorrectnessOfIteration(function (iterable) { new NativeConstructor(iterable); });
    // for early implementations -0 and +0 not the same
    var BUGGY_ZERO = !IS_WEAK && fails(function () {
      // V8 ~ Chromium 42- fails only with 5+ elements
      var $instance = new NativeConstructor();
      var index = 5;
      while (index--) $instance[ADDER](index, index);
      return !$instance.has(-0);
    });

    if (!ACCEPT_ITERABLES) {
      Constructor = wrapper(function (dummy, iterable) {
        anInstance(dummy, Constructor, CONSTRUCTOR_NAME);
        var that = inheritIfRequired(new NativeConstructor(), dummy, Constructor);
        if (iterable != undefined) iterate(iterable, that[ADDER], that, IS_MAP);
        return that;
      });
      Constructor.prototype = NativePrototype;
      NativePrototype.constructor = Constructor;
    }

    if (THROWS_ON_PRIMITIVES || BUGGY_ZERO) {
      fixMethod('delete');
      fixMethod('has');
      IS_MAP && fixMethod('get');
    }

    if (BUGGY_ZERO || HASNT_CHAINING) fixMethod(ADDER);

    // weak collections should not contains .clear method
    if (IS_WEAK && NativePrototype.clear) delete NativePrototype.clear;
  }

  exported[CONSTRUCTOR_NAME] = Constructor;
  $({ global: true, forced: Constructor != NativeConstructor }, exported);

  setToStringTag(Constructor, CONSTRUCTOR_NAME);

  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

  return Constructor;
};


/***/ }),

/***/ "840a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LevelsTable_vue_vue_type_style_index_0_id_5938d079_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("2373");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LevelsTable_vue_vue_type_style_index_0_id_5938d079_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LevelsTable_vue_vue_type_style_index_0_id_5938d079_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LevelsTable_vue_vue_type_style_index_0_id_5938d079_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "84e7":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("50ce");
var propertyIsEnumerableModule = __webpack_require__("eda6");
var createPropertyDescriptor = __webpack_require__("7234");
var toIndexedObject = __webpack_require__("f11f");
var toPrimitive = __webpack_require__("03de");
var has = __webpack_require__("20bb");
var IE8_DOM_DEFINE = __webpack_require__("c344");

var nativeGetOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// `Object.getOwnPropertyDescriptor` method
// https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptor
exports.f = DESCRIPTORS ? nativeGetOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
  O = toIndexedObject(O);
  P = toPrimitive(P, true);
  if (IE8_DOM_DEFINE) try {
    return nativeGetOwnPropertyDescriptor(O, P);
  } catch (error) { /* empty */ }
  if (has(O, P)) return createPropertyDescriptor(!propertyIsEnumerableModule.f.call(O, P), O[P]);
};


/***/ }),

/***/ "88a7":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("50ce");
var definePropertyModule = __webpack_require__("eae2");
var createPropertyDescriptor = __webpack_require__("7234");

module.exports = DESCRIPTORS ? function (object, key, value) {
  return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "8b07":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var createNonEnumerableProperty = __webpack_require__("88a7");
var has = __webpack_require__("20bb");
var setGlobal = __webpack_require__("654a");
var inspectSource = __webpack_require__("ab8d");
var InternalStateModule = __webpack_require__("1a2e");

var getInternalState = InternalStateModule.get;
var enforceInternalState = InternalStateModule.enforce;
var TEMPLATE = String(String).split('String');

(module.exports = function (O, key, value, options) {
  var unsafe = options ? !!options.unsafe : false;
  var simple = options ? !!options.enumerable : false;
  var noTargetGet = options ? !!options.noTargetGet : false;
  if (typeof value == 'function') {
    if (typeof key == 'string' && !has(value, 'name')) createNonEnumerableProperty(value, 'name', key);
    enforceInternalState(value).source = TEMPLATE.join(typeof key == 'string' ? key : '');
  }
  if (O === global) {
    if (simple) O[key] = value;
    else setGlobal(key, value);
    return;
  } else if (!unsafe) {
    delete O[key];
  } else if (!noTargetGet && O[key]) {
    simple = true;
  }
  if (simple) O[key] = value;
  else createNonEnumerableProperty(O, key, value);
// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
})(Function.prototype, 'toString', function toString() {
  return typeof this == 'function' && getInternalState(this).source || inspectSource(this);
});


/***/ }),

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__8bbf__;

/***/ }),

/***/ "8cea":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("b6b3");
var fails = __webpack_require__("3903");
var toObject = __webpack_require__("40f9");
var nativeGetPrototypeOf = __webpack_require__("c503");
var CORRECT_PROTOTYPE_GETTER = __webpack_require__("c5d7");

var FAILS_ON_PRIMITIVES = fails(function () { nativeGetPrototypeOf(1); });

// `Object.getPrototypeOf` method
// https://tc39.github.io/ecma262/#sec-object.getprototypeof
$({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES, sham: !CORRECT_PROTOTYPE_GETTER }, {
  getPrototypeOf: function getPrototypeOf(it) {
    return nativeGetPrototypeOf(toObject(it));
  }
});



/***/ }),

/***/ "8d97":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// `Symbol.prototype.description` getter
// https://tc39.github.io/ecma262/#sec-symbol.prototype.description

var $ = __webpack_require__("b6b3");
var DESCRIPTORS = __webpack_require__("50ce");
var global = __webpack_require__("92f7");
var has = __webpack_require__("20bb");
var isObject = __webpack_require__("7313");
var defineProperty = __webpack_require__("eae2").f;
var copyConstructorProperties = __webpack_require__("e560");

var NativeSymbol = global.Symbol;

if (DESCRIPTORS && typeof NativeSymbol == 'function' && (!('description' in NativeSymbol.prototype) ||
  // Safari 12 bug
  NativeSymbol().description !== undefined
)) {
  var EmptyStringDescriptionStore = {};
  // wrap Symbol constructor for correct work with undefined description
  var SymbolWrapper = function Symbol() {
    var description = arguments.length < 1 || arguments[0] === undefined ? undefined : String(arguments[0]);
    var result = this instanceof SymbolWrapper
      ? new NativeSymbol(description)
      // in Edge 13, String(Symbol(undefined)) === 'Symbol(undefined)'
      : description === undefined ? NativeSymbol() : NativeSymbol(description);
    if (description === '') EmptyStringDescriptionStore[result] = true;
    return result;
  };
  copyConstructorProperties(SymbolWrapper, NativeSymbol);
  var symbolPrototype = SymbolWrapper.prototype = NativeSymbol.prototype;
  symbolPrototype.constructor = SymbolWrapper;

  var symbolToString = symbolPrototype.toString;
  var native = String(NativeSymbol('test')) == 'Symbol(test)';
  var regexp = /^Symbol\((.*)\)[^)]+$/;
  defineProperty(symbolPrototype, 'description', {
    configurable: true,
    get: function description() {
      var symbol = isObject(this) ? this.valueOf() : this;
      var string = symbolToString.call(symbol);
      if (has(EmptyStringDescriptionStore, symbol)) return '';
      var desc = native ? string.slice(7, -1) : string.replace(regexp, '$1');
      return desc === '' ? undefined : desc;
    }
  });

  $({ global: true, forced: true }, {
    Symbol: SymbolWrapper
  });
}


/***/ }),

/***/ "9186":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var isObject = __webpack_require__("7313");
var isArray = __webpack_require__("3b4d");
var toAbsoluteIndex = __webpack_require__("d909");
var toLength = __webpack_require__("fcce");
var toIndexedObject = __webpack_require__("f11f");
var createProperty = __webpack_require__("362c");
var arrayMethodHasSpeciesSupport = __webpack_require__("1feb");
var wellKnownSymbol = __webpack_require__("df9f");

var SPECIES = wellKnownSymbol('species');
var nativeSlice = [].slice;
var max = Math.max;

// `Array.prototype.slice` method
// https://tc39.github.io/ecma262/#sec-array.prototype.slice
// fallback for not array-like ES3 strings and DOM objects
$({ target: 'Array', proto: true, forced: !arrayMethodHasSpeciesSupport('slice') }, {
  slice: function slice(start, end) {
    var O = toIndexedObject(this);
    var length = toLength(O.length);
    var k = toAbsoluteIndex(start, length);
    var fin = toAbsoluteIndex(end === undefined ? length : end, length);
    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
    var Constructor, result, n;
    if (isArray(O)) {
      Constructor = O.constructor;
      // cross-realm fallback
      if (typeof Constructor == 'function' && (Constructor === Array || isArray(Constructor.prototype))) {
        Constructor = undefined;
      } else if (isObject(Constructor)) {
        Constructor = Constructor[SPECIES];
        if (Constructor === null) Constructor = undefined;
      }
      if (Constructor === Array || Constructor === undefined) {
        return nativeSlice.call(O, k, fin);
      }
    }
    result = new (Constructor === undefined ? Array : Constructor)(max(fin - k, 0));
    for (n = 0; k < fin; k++, n++) if (k in O) createProperty(result, n, O[k]);
    result.length = n;
    return result;
  }
});


/***/ }),

/***/ "92f7":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var check = function (it) {
  return it && it.Math == Math && it;
};

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
module.exports =
  // eslint-disable-next-line no-undef
  check(typeof globalThis == 'object' && globalThis) ||
  check(typeof window == 'object' && window) ||
  check(typeof self == 'object' && self) ||
  check(typeof global == 'object' && global) ||
  // eslint-disable-next-line no-new-func
  Function('return this')();

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("05e9")))

/***/ }),

/***/ "9522":
/***/ (function(module, exports) {

// `RequireObjectCoercible` abstract operation
// https://tc39.github.io/ecma262/#sec-requireobjectcoercible
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on " + it);
  return it;
};


/***/ }),

/***/ "9b01":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var anObject = __webpack_require__("0e66");

// `RegExp.prototype.flags` getter implementation
// https://tc39.github.io/ecma262/#sec-get-regexp.prototype.flags
module.exports = function () {
  var that = anObject(this);
  var result = '';
  if (that.global) result += 'g';
  if (that.ignoreCase) result += 'i';
  if (that.multiline) result += 'm';
  if (that.dotAll) result += 's';
  if (that.unicode) result += 'u';
  if (that.sticky) result += 'y';
  return result;
};


/***/ }),

/***/ "9f8d":
/***/ (function(module, exports, __webpack_require__) {

var defineProperty = __webpack_require__("eae2").f;
var has = __webpack_require__("20bb");
var wellKnownSymbol = __webpack_require__("df9f");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');

module.exports = function (it, TAG, STATIC) {
  if (it && !has(it = STATIC ? it : it.prototype, TO_STRING_TAG)) {
    defineProperty(it, TO_STRING_TAG, { configurable: true, value: TAG });
  }
};


/***/ }),

/***/ "a071":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var toAbsoluteIndex = __webpack_require__("d909");
var toInteger = __webpack_require__("a3d8");
var toLength = __webpack_require__("fcce");
var toObject = __webpack_require__("40f9");
var arraySpeciesCreate = __webpack_require__("6e52");
var createProperty = __webpack_require__("362c");
var arrayMethodHasSpeciesSupport = __webpack_require__("1feb");

var max = Math.max;
var min = Math.min;
var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF;
var MAXIMUM_ALLOWED_LENGTH_EXCEEDED = 'Maximum allowed length exceeded';

// `Array.prototype.splice` method
// https://tc39.github.io/ecma262/#sec-array.prototype.splice
// with adding support of @@species
$({ target: 'Array', proto: true, forced: !arrayMethodHasSpeciesSupport('splice') }, {
  splice: function splice(start, deleteCount /* , ...items */) {
    var O = toObject(this);
    var len = toLength(O.length);
    var actualStart = toAbsoluteIndex(start, len);
    var argumentsLength = arguments.length;
    var insertCount, actualDeleteCount, A, k, from, to;
    if (argumentsLength === 0) {
      insertCount = actualDeleteCount = 0;
    } else if (argumentsLength === 1) {
      insertCount = 0;
      actualDeleteCount = len - actualStart;
    } else {
      insertCount = argumentsLength - 2;
      actualDeleteCount = min(max(toInteger(deleteCount), 0), len - actualStart);
    }
    if (len + insertCount - actualDeleteCount > MAX_SAFE_INTEGER) {
      throw TypeError(MAXIMUM_ALLOWED_LENGTH_EXCEEDED);
    }
    A = arraySpeciesCreate(O, actualDeleteCount);
    for (k = 0; k < actualDeleteCount; k++) {
      from = actualStart + k;
      if (from in O) createProperty(A, k, O[from]);
    }
    A.length = actualDeleteCount;
    if (insertCount < actualDeleteCount) {
      for (k = actualStart; k < len - actualDeleteCount; k++) {
        from = k + actualDeleteCount;
        to = k + insertCount;
        if (from in O) O[to] = O[from];
        else delete O[to];
      }
      for (k = len; k > len - actualDeleteCount + insertCount; k--) delete O[k - 1];
    } else if (insertCount > actualDeleteCount) {
      for (k = len - actualDeleteCount; k > actualStart; k--) {
        from = k + actualDeleteCount - 1;
        to = k + insertCount - 1;
        if (from in O) O[to] = O[from];
        else delete O[to];
      }
    }
    for (k = 0; k < insertCount; k++) {
      O[k + actualStart] = arguments[k + 2];
    }
    O.length = len - actualDeleteCount + insertCount;
    return A;
  }
});


/***/ }),

/***/ "a1d8":
/***/ (function(module, exports, __webpack_require__) {

var bind = __webpack_require__("16e3");
var IndexedObject = __webpack_require__("e561");
var toObject = __webpack_require__("40f9");
var toLength = __webpack_require__("fcce");
var arraySpeciesCreate = __webpack_require__("6e52");

var push = [].push;

// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex }` methods implementation
var createMethod = function (TYPE) {
  var IS_MAP = TYPE == 1;
  var IS_FILTER = TYPE == 2;
  var IS_SOME = TYPE == 3;
  var IS_EVERY = TYPE == 4;
  var IS_FIND_INDEX = TYPE == 6;
  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
  return function ($this, callbackfn, that, specificCreate) {
    var O = toObject($this);
    var self = IndexedObject(O);
    var boundFunction = bind(callbackfn, that, 3);
    var length = toLength(self.length);
    var index = 0;
    var create = specificCreate || arraySpeciesCreate;
    var target = IS_MAP ? create($this, length) : IS_FILTER ? create($this, 0) : undefined;
    var value, result;
    for (;length > index; index++) if (NO_HOLES || index in self) {
      value = self[index];
      result = boundFunction(value, index, O);
      if (TYPE) {
        if (IS_MAP) target[index] = result; // map
        else if (result) switch (TYPE) {
          case 3: return true;              // some
          case 5: return value;             // find
          case 6: return index;             // findIndex
          case 2: push.call(target, value); // filter
        } else if (IS_EVERY) return false;  // every
      }
    }
    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
  };
};

module.exports = {
  // `Array.prototype.forEach` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.foreach
  forEach: createMethod(0),
  // `Array.prototype.map` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.map
  map: createMethod(1),
  // `Array.prototype.filter` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.filter
  filter: createMethod(2),
  // `Array.prototype.some` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.some
  some: createMethod(3),
  // `Array.prototype.every` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.every
  every: createMethod(4),
  // `Array.prototype.find` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.find
  find: createMethod(5),
  // `Array.prototype.findIndex` method
  // https://tc39.github.io/ecma262/#sec-array.prototype.findIndex
  findIndex: createMethod(6)
};


/***/ }),

/***/ "a3d8":
/***/ (function(module, exports) {

var ceil = Math.ceil;
var floor = Math.floor;

// `ToInteger` abstract operation
// https://tc39.github.io/ecma262/#sec-tointeger
module.exports = function (argument) {
  return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
};


/***/ }),

/***/ "a6d2":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("0e66");
var aPossiblePrototype = __webpack_require__("07b9");

// `Object.setPrototypeOf` method
// https://tc39.github.io/ecma262/#sec-object.setprototypeof
// Works with __proto__ only. Old v8 can't work with null proto objects.
/* eslint-disable no-proto */
module.exports = Object.setPrototypeOf || ('__proto__' in {} ? function () {
  var CORRECT_SETTER = false;
  var test = {};
  var setter;
  try {
    setter = Object.getOwnPropertyDescriptor(Object.prototype, '__proto__').set;
    setter.call(test, []);
    CORRECT_SETTER = test instanceof Array;
  } catch (error) { /* empty */ }
  return function setPrototypeOf(O, proto) {
    anObject(O);
    aPossiblePrototype(proto);
    if (CORRECT_SETTER) setter.call(O, proto);
    else O.__proto__ = proto;
    return O;
  };
}() : undefined);


/***/ }),

/***/ "a7da":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var charAt = __webpack_require__("aac4").charAt;
var InternalStateModule = __webpack_require__("1a2e");
var defineIterator = __webpack_require__("f7da");

var STRING_ITERATOR = 'String Iterator';
var setInternalState = InternalStateModule.set;
var getInternalState = InternalStateModule.getterFor(STRING_ITERATOR);

// `String.prototype[@@iterator]` method
// https://tc39.github.io/ecma262/#sec-string.prototype-@@iterator
defineIterator(String, 'String', function (iterated) {
  setInternalState(this, {
    type: STRING_ITERATOR,
    string: String(iterated),
    index: 0
  });
// `%StringIteratorPrototype%.next` method
// https://tc39.github.io/ecma262/#sec-%stringiteratorprototype%.next
}, function next() {
  var state = getInternalState(this);
  var string = state.string;
  var index = state.index;
  var point;
  if (index >= string.length) return { value: undefined, done: true };
  point = charAt(string, index);
  state.index += point.length;
  return { value: point, done: false };
});


/***/ }),

/***/ "a862":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");

// `URL.prototype.toJSON` method
// https://url.spec.whatwg.org/#dom-url-tojson
$({ target: 'URL', proto: true, enumerable: true }, {
  toJSON: function toJSON() {
    return URL.prototype.toString.call(this);
  }
});


/***/ }),

/***/ "a988":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("20bb");
var toIndexedObject = __webpack_require__("f11f");
var indexOf = __webpack_require__("64e0").indexOf;
var hiddenKeys = __webpack_require__("cc24");

module.exports = function (object, names) {
  var O = toIndexedObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) !has(hiddenKeys, key) && has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (has(O, key = names[i++])) {
    ~indexOf(result, key) || result.push(key);
  }
  return result;
};


/***/ }),

/***/ "aac4":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("a3d8");
var requireObjectCoercible = __webpack_require__("9522");

// `String.prototype.{ codePointAt, at }` methods implementation
var createMethod = function (CONVERT_TO_STRING) {
  return function ($this, pos) {
    var S = String(requireObjectCoercible($this));
    var position = toInteger(pos);
    var size = S.length;
    var first, second;
    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
    first = S.charCodeAt(position);
    return first < 0xD800 || first > 0xDBFF || position + 1 === size
      || (second = S.charCodeAt(position + 1)) < 0xDC00 || second > 0xDFFF
        ? CONVERT_TO_STRING ? S.charAt(position) : first
        : CONVERT_TO_STRING ? S.slice(position, position + 2) : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
  };
};

module.exports = {
  // `String.prototype.codePointAt` method
  // https://tc39.github.io/ecma262/#sec-string.prototype.codepointat
  codeAt: createMethod(false),
  // `String.prototype.at` method
  // https://github.com/mathiasbynens/String.prototype.at
  charAt: createMethod(true)
};


/***/ }),

/***/ "ab8d":
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__("b802");

var functionToString = Function.toString;

module.exports = shared('inspectSource', function (it) {
  return functionToString.call(it);
});


/***/ }),

/***/ "abf2":
/***/ (function(module, exports, __webpack_require__) {

var getBuiltIn = __webpack_require__("fe3e");

module.exports = getBuiltIn('document', 'documentElement');


/***/ }),

/***/ "ac9b":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var forEach = __webpack_require__("569f");

// `Array.prototype.forEach` method
// https://tc39.github.io/ecma262/#sec-array.prototype.foreach
$({ target: 'Array', proto: true, forced: [].forEach != forEach }, {
  forEach: forEach
});


/***/ }),

/***/ "ad0f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var getBuiltIn = __webpack_require__("fe3e");
var definePropertyModule = __webpack_require__("eae2");
var wellKnownSymbol = __webpack_require__("df9f");
var DESCRIPTORS = __webpack_require__("50ce");

var SPECIES = wellKnownSymbol('species');

module.exports = function (CONSTRUCTOR_NAME) {
  var Constructor = getBuiltIn(CONSTRUCTOR_NAME);
  var defineProperty = definePropertyModule.f;

  if (DESCRIPTORS && Constructor && !Constructor[SPECIES]) {
    defineProperty(Constructor, SPECIES, {
      configurable: true,
      get: function () { return this; }
    });
  }
};


/***/ }),

/***/ "ad3e":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("0e66");
var defineProperties = __webpack_require__("c4dd");
var enumBugKeys = __webpack_require__("757f");
var hiddenKeys = __webpack_require__("cc24");
var html = __webpack_require__("abf2");
var documentCreateElement = __webpack_require__("eaef");
var sharedKey = __webpack_require__("7408");
var IE_PROTO = sharedKey('IE_PROTO');

var PROTOTYPE = 'prototype';
var Empty = function () { /* empty */ };

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = documentCreateElement('iframe');
  var length = enumBugKeys.length;
  var lt = '<';
  var script = 'script';
  var gt = '>';
  var js = 'java' + script + ':';
  var iframeDocument;
  iframe.style.display = 'none';
  html.appendChild(iframe);
  iframe.src = String(js);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + script + gt + 'document.F=Object' + lt + '/' + script + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while (length--) delete createDict[PROTOTYPE][enumBugKeys[length]];
  return createDict();
};

// `Object.create` method
// https://tc39.github.io/ecma262/#sec-object.create
module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty();
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : defineProperties(result, Properties);
};

hiddenKeys[IE_PROTO] = true;


/***/ }),

/***/ "ae74":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "b11f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var TO_STRING_TAG_SUPPORT = __webpack_require__("172e");
var classof = __webpack_require__("f4c8");

// `Object.prototype.toString` method implementation
// https://tc39.github.io/ecma262/#sec-object.prototype.tostring
module.exports = TO_STRING_TAG_SUPPORT ? {}.toString : function toString() {
  return '[object ' + classof(this) + ']';
};


/***/ }),

/***/ "b631":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "b6b3":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var getOwnPropertyDescriptor = __webpack_require__("84e7").f;
var createNonEnumerableProperty = __webpack_require__("88a7");
var redefine = __webpack_require__("8b07");
var setGlobal = __webpack_require__("654a");
var copyConstructorProperties = __webpack_require__("e560");
var isForced = __webpack_require__("5dc1");

/*
  options.target      - name of the target object
  options.global      - target is the global object
  options.stat        - export as static methods of target
  options.proto       - export as prototype methods of target
  options.real        - real prototype method for the `pure` version
  options.forced      - export even if the native feature is available
  options.bind        - bind methods to the target, required for the `pure` version
  options.wrap        - wrap constructors to preventing global pollution, required for the `pure` version
  options.unsafe      - use the simple assignment of property instead of delete + defineProperty
  options.sham        - add a flag to not completely full polyfills
  options.enumerable  - export as enumerable property
  options.noTargetGet - prevent calling a getter on target
*/
module.exports = function (options, source) {
  var TARGET = options.target;
  var GLOBAL = options.global;
  var STATIC = options.stat;
  var FORCED, target, key, targetProperty, sourceProperty, descriptor;
  if (GLOBAL) {
    target = global;
  } else if (STATIC) {
    target = global[TARGET] || setGlobal(TARGET, {});
  } else {
    target = (global[TARGET] || {}).prototype;
  }
  if (target) for (key in source) {
    sourceProperty = source[key];
    if (options.noTargetGet) {
      descriptor = getOwnPropertyDescriptor(target, key);
      targetProperty = descriptor && descriptor.value;
    } else targetProperty = target[key];
    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
    // contained in target
    if (!FORCED && targetProperty !== undefined) {
      if (typeof sourceProperty === typeof targetProperty) continue;
      copyConstructorProperties(sourceProperty, targetProperty);
    }
    // add a flag to not completely full polyfills
    if (options.sham || (targetProperty && targetProperty.sham)) {
      createNonEnumerableProperty(sourceProperty, 'sham', true);
    }
    // extend global
    redefine(target, key, sourceProperty, options);
  }
};


/***/ }),

/***/ "b802":
/***/ (function(module, exports, __webpack_require__) {

var IS_PURE = __webpack_require__("c009");
var store = __webpack_require__("c1de");

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: '3.4.5',
  mode: IS_PURE ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ "bd63":
/***/ (function(module, exports, __webpack_require__) {

var NATIVE_SYMBOL = __webpack_require__("3fb6");

module.exports = NATIVE_SYMBOL
  // eslint-disable-next-line no-undef
  && !Symbol.sham
  // eslint-disable-next-line no-undef
  && typeof Symbol() == 'symbol';


/***/ }),

/***/ "bf09":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "c009":
/***/ (function(module, exports) {

module.exports = false;


/***/ }),

/***/ "c1de":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var setGlobal = __webpack_require__("654a");

var SHARED = '__core-js_shared__';
var store = global[SHARED] || setGlobal(SHARED, {});

module.exports = store;


/***/ }),

/***/ "c27e":
/***/ (function(module, exports, __webpack_require__) {

var getBuiltIn = __webpack_require__("fe3e");
var getOwnPropertyNamesModule = __webpack_require__("3768");
var getOwnPropertySymbolsModule = __webpack_require__("1524");
var anObject = __webpack_require__("0e66");

// all object keys, includes non-enumerable and symbols
module.exports = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
  var keys = getOwnPropertyNamesModule.f(anObject(it));
  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
  return getOwnPropertySymbols ? keys.concat(getOwnPropertySymbols(it)) : keys;
};


/***/ }),

/***/ "c344":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("50ce");
var fails = __webpack_require__("3903");
var createElement = __webpack_require__("eaef");

// Thank's IE8 for his funny defineProperty
module.exports = !DESCRIPTORS && !fails(function () {
  return Object.defineProperty(createElement('div'), 'a', {
    get: function () { return 7; }
  }).a != 7;
});


/***/ }),

/***/ "c4dd":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("50ce");
var definePropertyModule = __webpack_require__("eae2");
var anObject = __webpack_require__("0e66");
var objectKeys = __webpack_require__("58c5");

// `Object.defineProperties` method
// https://tc39.github.io/ecma262/#sec-object.defineproperties
module.exports = DESCRIPTORS ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = objectKeys(Properties);
  var length = keys.length;
  var index = 0;
  var key;
  while (length > index) definePropertyModule.f(O, key = keys[index++], Properties[key]);
  return O;
};


/***/ }),

/***/ "c503":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("20bb");
var toObject = __webpack_require__("40f9");
var sharedKey = __webpack_require__("7408");
var CORRECT_PROTOTYPE_GETTER = __webpack_require__("c5d7");

var IE_PROTO = sharedKey('IE_PROTO');
var ObjectPrototype = Object.prototype;

// `Object.getPrototypeOf` method
// https://tc39.github.io/ecma262/#sec-object.getprototypeof
module.exports = CORRECT_PROTOTYPE_GETTER ? Object.getPrototypeOf : function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectPrototype : null;
};


/***/ }),

/***/ "c5d7":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");

module.exports = !fails(function () {
  function F() { /* empty */ }
  F.prototype.constructor = null;
  return Object.getPrototypeOf(new F()) !== F.prototype;
});


/***/ }),

/***/ "cc24":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "d1bf":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreRubricBuilder_vue_vue_type_style_index_0_id_8bbe3256_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("bf09");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreRubricBuilder_vue_vue_type_style_index_0_id_8bbe3256_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreRubricBuilder_vue_vue_type_style_index_0_id_8bbe3256_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreRubricBuilder_vue_vue_type_style_index_0_id_8bbe3256_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "d4cb":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("df9f");

exports.f = wellKnownSymbol;


/***/ }),

/***/ "d5a6":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("df9f");
var create = __webpack_require__("ad3e");
var createNonEnumerableProperty = __webpack_require__("88a7");

var UNSCOPABLES = wellKnownSymbol('unscopables');
var ArrayPrototype = Array.prototype;

// Array.prototype[@@unscopables]
// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
if (ArrayPrototype[UNSCOPABLES] == undefined) {
  createNonEnumerableProperty(ArrayPrototype, UNSCOPABLES, create(null));
}

// add a key to Array.prototype[@@unscopables]
module.exports = function (key) {
  ArrayPrototype[UNSCOPABLES][key] = true;
};


/***/ }),

/***/ "d909":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("a3d8");

var max = Math.max;
var min = Math.min;

// Helper for a popular repeating case of the spec:
// Let integer be ? ToInteger(index).
// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
module.exports = function (index, length) {
  var integer = toInteger(index);
  return integer < 0 ? max(integer + length, 0) : min(integer, length);
};


/***/ }),

/***/ "df9f":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var shared = __webpack_require__("b802");
var has = __webpack_require__("20bb");
var uid = __webpack_require__("267a");
var NATIVE_SYMBOL = __webpack_require__("3fb6");
var USE_SYMBOL_AS_UID = __webpack_require__("bd63");

var WellKnownSymbolsStore = shared('wks');
var Symbol = global.Symbol;
var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol : uid;

module.exports = function (name) {
  if (!has(WellKnownSymbolsStore, name)) {
    if (NATIVE_SYMBOL && has(Symbol, name)) WellKnownSymbolsStore[name] = Symbol[name];
    else WellKnownSymbolsStore[name] = createWellKnownSymbol('Symbol.' + name);
  } return WellKnownSymbolsStore[name];
};


/***/ }),

/***/ "e2c2":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("b6b3");
var setPrototypeOf = __webpack_require__("a6d2");

// `Object.setPrototypeOf` method
// https://tc39.github.io/ecma262/#sec-object.setprototypeof
$({ target: 'Object', stat: true }, {
  setPrototypeOf: setPrototypeOf
});


/***/ }),

/***/ "e560":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("20bb");
var ownKeys = __webpack_require__("c27e");
var getOwnPropertyDescriptorModule = __webpack_require__("84e7");
var definePropertyModule = __webpack_require__("eae2");

module.exports = function (target, source) {
  var keys = ownKeys(source);
  var defineProperty = definePropertyModule.f;
  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
  for (var i = 0; i < keys.length; i++) {
    var key = keys[i];
    if (!has(target, key)) defineProperty(target, key, getOwnPropertyDescriptor(source, key));
  }
};


/***/ }),

/***/ "e561":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");
var classof = __webpack_require__("0de9");

var split = ''.split;

// fallback for non-array-like ES3 and non-enumerable old V8 strings
module.exports = fails(function () {
  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
  // eslint-disable-next-line no-prototype-builtins
  return !Object('z').propertyIsEnumerable(0);
}) ? function (it) {
  return classof(it) == 'String' ? split.call(it, '') : Object(it);
} : Object;


/***/ }),

/***/ "e5a5":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("df9f");
var Iterators = __webpack_require__("ae74");

var ITERATOR = wellKnownSymbol('iterator');
var ArrayPrototype = Array.prototype;

// check on default Array iterator
module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
};


/***/ }),

/***/ "e790":
/***/ (function(module, exports, __webpack_require__) {

var TO_STRING_TAG_SUPPORT = __webpack_require__("172e");
var redefine = __webpack_require__("8b07");
var toString = __webpack_require__("b11f");

// `Object.prototype.toString` method
// https://tc39.github.io/ecma262/#sec-object.prototype.tostring
if (!TO_STRING_TAG_SUPPORT) {
  redefine(Object.prototype, 'toString', toString, { unsafe: true });
}


/***/ }),

/***/ "eae2":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("50ce");
var IE8_DOM_DEFINE = __webpack_require__("c344");
var anObject = __webpack_require__("0e66");
var toPrimitive = __webpack_require__("03de");

var nativeDefineProperty = Object.defineProperty;

// `Object.defineProperty` method
// https://tc39.github.io/ecma262/#sec-object.defineproperty
exports.f = DESCRIPTORS ? nativeDefineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return nativeDefineProperty(O, P, Attributes);
  } catch (error) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ "eaef":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("92f7");
var isObject = __webpack_require__("7313");

var document = global.document;
// typeof document.createElement is 'object' in old IE
var EXISTS = isObject(document) && isObject(document.createElement);

module.exports = function (it) {
  return EXISTS ? document.createElement(it) : {};
};


/***/ }),

/***/ "ecaa":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("0e66");

// call something on iterator step with safe closing on error
module.exports = function (iterator, fn, value, ENTRIES) {
  try {
    return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (error) {
    var returnMethod = iterator['return'];
    if (returnMethod !== undefined) anObject(returnMethod.call(iterator));
    throw error;
  }
};


/***/ }),

/***/ "eda6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var nativePropertyIsEnumerable = {}.propertyIsEnumerable;
var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// Nashorn ~ JDK8 bug
var NASHORN_BUG = getOwnPropertyDescriptor && !nativePropertyIsEnumerable.call({ 1: 2 }, 1);

// `Object.prototype.propertyIsEnumerable` method implementation
// https://tc39.github.io/ecma262/#sec-object.prototype.propertyisenumerable
exports.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
  var descriptor = getOwnPropertyDescriptor(this, V);
  return !!descriptor && descriptor.enumerable;
} : nativePropertyIsEnumerable;


/***/ }),

/***/ "f11f":
/***/ (function(module, exports, __webpack_require__) {

// toObject with fallback for non-array-like ES3 strings
var IndexedObject = __webpack_require__("e561");
var requireObjectCoercible = __webpack_require__("9522");

module.exports = function (it) {
  return IndexedObject(requireObjectCoercible(it));
};


/***/ }),

/***/ "f4c8":
/***/ (function(module, exports, __webpack_require__) {

var TO_STRING_TAG_SUPPORT = __webpack_require__("172e");
var classofRaw = __webpack_require__("0de9");
var wellKnownSymbol = __webpack_require__("df9f");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
// ES3 wrong here
var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (error) { /* empty */ }
};

// getting tag from ES6+ `Object.prototype.toString`
module.exports = TO_STRING_TAG_SUPPORT ? classofRaw : function (it) {
  var O, tag, result;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (tag = tryGet(O = Object(it), TO_STRING_TAG)) == 'string' ? tag
    // builtinTag case
    : CORRECT_ARGUMENTS ? classofRaw(O)
    // ES3 arguments fallback
    : (result = classofRaw(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : result;
};


/***/ }),

/***/ "f7da":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("b6b3");
var createIteratorConstructor = __webpack_require__("ffee");
var getPrototypeOf = __webpack_require__("c503");
var setPrototypeOf = __webpack_require__("a6d2");
var setToStringTag = __webpack_require__("9f8d");
var createNonEnumerableProperty = __webpack_require__("88a7");
var redefine = __webpack_require__("8b07");
var wellKnownSymbol = __webpack_require__("df9f");
var IS_PURE = __webpack_require__("c009");
var Iterators = __webpack_require__("ae74");
var IteratorsCore = __webpack_require__("6e9e");

var IteratorPrototype = IteratorsCore.IteratorPrototype;
var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
var ITERATOR = wellKnownSymbol('iterator');
var KEYS = 'keys';
var VALUES = 'values';
var ENTRIES = 'entries';

var returnThis = function () { return this; };

module.exports = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
  createIteratorConstructor(IteratorConstructor, NAME, next);

  var getIterationMethod = function (KIND) {
    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
    if (!BUGGY_SAFARI_ITERATORS && KIND in IterablePrototype) return IterablePrototype[KIND];
    switch (KIND) {
      case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
      case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
      case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
    } return function () { return new IteratorConstructor(this); };
  };

  var TO_STRING_TAG = NAME + ' Iterator';
  var INCORRECT_VALUES_NAME = false;
  var IterablePrototype = Iterable.prototype;
  var nativeIterator = IterablePrototype[ITERATOR]
    || IterablePrototype['@@iterator']
    || DEFAULT && IterablePrototype[DEFAULT];
  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
  var CurrentIteratorPrototype, methods, KEY;

  // fix native
  if (anyNativeIterator) {
    CurrentIteratorPrototype = getPrototypeOf(anyNativeIterator.call(new Iterable()));
    if (IteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
      if (!IS_PURE && getPrototypeOf(CurrentIteratorPrototype) !== IteratorPrototype) {
        if (setPrototypeOf) {
          setPrototypeOf(CurrentIteratorPrototype, IteratorPrototype);
        } else if (typeof CurrentIteratorPrototype[ITERATOR] != 'function') {
          createNonEnumerableProperty(CurrentIteratorPrototype, ITERATOR, returnThis);
        }
      }
      // Set @@toStringTag to native iterators
      setToStringTag(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
      if (IS_PURE) Iterators[TO_STRING_TAG] = returnThis;
    }
  }

  // fix Array#{values, @@iterator}.name in V8 / FF
  if (DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
    INCORRECT_VALUES_NAME = true;
    defaultIterator = function values() { return nativeIterator.call(this); };
  }

  // define iterator
  if ((!IS_PURE || FORCED) && IterablePrototype[ITERATOR] !== defaultIterator) {
    createNonEnumerableProperty(IterablePrototype, ITERATOR, defaultIterator);
  }
  Iterators[NAME] = defaultIterator;

  // export additional methods
  if (DEFAULT) {
    methods = {
      values: getIterationMethod(VALUES),
      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
      entries: getIterationMethod(ENTRIES)
    };
    if (FORCED) for (KEY in methods) {
      if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
        redefine(IterablePrototype, KEY, methods[KEY]);
      }
    } else $({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
  }

  return methods;
};


/***/ }),

/***/ "f7f6":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("3903");

module.exports = !fails(function () {
  return Object.isExtensible(Object.preventExtensions({}));
});


/***/ }),

/***/ "fa16":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  if (true) {
    __webpack_require__("5929")
  }

  var i
  if ((i = window.document.currentScript) && (i = i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __webpack_require__.p = i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"941cbcf2-vue-loader-template"}!D:/Sites/branch/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue?vue&type=template&id=8bbe3256&scoped=true&
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('b-container',{attrs:{"fluid":""}},[_c('h1',[_vm._v("Configureer ")]),_c('Configuration',{staticClass:"configuration"}),_c('h1',[_vm._v("Bepaal niveau's")]),_c('LevelsTable'),_c('br'),_c('h1',[_vm._v("Rubric")]),_vm._l((_vm.store.rubric.clusters),function(cluster,clusterIndex){return _c('table',{staticClass:"table table-bordered rubric-table"},[_c('tr',{staticClass:"cluster-header"},[_c('td',{staticClass:"cluster-title",attrs:{"colspan":"2"}},[_c('collapse',{attrs:{"collapsed":cluster.collapsed},on:{"toggle-collapse":function($event){return cluster.toggleCollapsed()}}},[_vm._t("default",[_c('div',{staticClass:"d-flex cluster-title-slot  w-100"},[_c('div',{staticClass:"d-flex w-100 cluster-title-slot-item"},[_c('textarea',{directives:[{name:"model",rawName:"v-model",value:(cluster.title),expression:"cluster.title"}],staticClass:"form-control text-area-level-description font-weight-bold ml-2",attrs:{"placeholder":"Vul aan"},domProps:{"value":(cluster.title)},on:{"input":function($event){if($event.target.composing){ return; }_vm.$set(cluster, "title", $event.target.value)}}}),_c('MoveDeleteBar',{attrs:{"index":clusterIndex,"max-index":_vm.store.rubric.clusters.length - 1},on:{"move-up":function($event){return _vm.store.rubric.moveClusterUp(cluster)},"move-down":function($event){return _vm.store.rubric.moveClusterDown(cluster)},"remove":function($event){return _vm.store.rubric.removeCluster(cluster)}}})],1),_c('b-button',{staticClass:"ml-2 mt-1",attrs:{"variant":"primary"}},[_vm._v("Koppel leerdoelstelling")])],1)])],2)],1),_vm._l((_vm.store.rubric.levels),function(level){return _c('td',{staticClass:"score-title"},[(level.description)?_c('i',{directives:[{name:"b-popover",rawName:"v-b-popover.hover.top",value:(level.description),expression:"level.description",modifiers:{"hover":true,"top":true}}],staticClass:"fa fa-info-circle mr-2",attrs:{"aria-hidden":"true"}}):_vm._e(),_vm._v(_vm._s(_vm._f("capitalize")(level.title))+" ")])})],2),_vm._l((cluster.categories),function(category){return (!cluster.collapsed)?_c('tbody',[_vm._l((category.criteria),function(criterium,index){return _c('tr',{staticClass:"category-tr"},[(index === 0)?_c('td',{staticClass:"category-td p-0",attrs:{"rowspan":category.criteria.length + 1}},[_c('div',{staticClass:"category"},[_c('div',{class:'category-' + category.color}),_c('div',{staticClass:"category-title"},[_vm._v(_vm._s(category.title))])])]):_vm._e(),_c('td',{staticClass:"criteria"},[_c('div',{staticClass:"criterium-title-container"},[_vm._v(" "+_vm._s(criterium.title)+" "),(_vm.store.rubric.useScores)?_c('b-input-group',{staticClass:"weight-input-group weight",attrs:{"prepend":"Gewicht: ","append":"%"}},[_c('input',{directives:[{name:"model",rawName:"v-model",value:(criterium.weight),expression:"criterium.weight"}],staticClass:"form-control ",attrs:{"type":"number","name":"Score","placeholder":"Gewicht %","min":"0","max":"100","maxlength":"3"},domProps:{"value":(criterium.weight)},on:{"input":function($event){if($event.target.composing){ return; }_vm.$set(criterium, "weight", $event.target.value)}}})]):_vm._e(),_c('b-button',{staticClass:"ml-2 mt-1",attrs:{"variant":"primary"}},[_vm._v("Koppel leerdoelstelling")])],1)]),_vm._l((_vm.store.rubric.levels),function(level){return _c('td',{staticClass:"score"},[_c('textarea',{directives:[{name:"model",rawName:"v-model",value:(_vm.store.rubric.getChoice(criterium, level).feedback),expression:"store.rubric.getChoice(criterium, level).feedback"}],staticClass:"form-control text-area-level-description mb-2 feedback-text",attrs:{"placeholder":"Vul aan"},domProps:{"value":(_vm.store.rubric.getChoice(criterium, level).feedback)},on:{"input":function($event){if($event.target.composing){ return; }_vm.$set(_vm.store.rubric.getChoice(criterium, level), "feedback", $event.target.value)}}}),(_vm.store.rubric.useScores)?_c('div',[_vm._v(" "+_vm._s(_vm.store.rubric.getChoiceScore(criterium, level))+" punten "),_c('b-button',{attrs:{"size":"sm"}},[_vm._v("Vaste score")])],1):_vm._e(),_c('b-checkbox',[_vm._v("Melding in rapport")])],1)})],2)}),_c('tr',[_c('td',{attrs:{"colspan":_vm.store.rubric.levels.length + 1}},[_c('b-button',{staticClass:"w-100",attrs:{"variant":"primary"}},[_vm._v("Voeg vrij criterium of leerdoelstelling toe")])],1)])],2):_vm._e()}),(!cluster.collapsed)?_c('tbody',[_c('tr',[_c('td',{attrs:{"colspan":2 + _vm.store.rubric.levels.length}},[_c('b-button',{staticClass:"w-100",attrs:{"variant":"primary"}},[_vm._v("Voeg Categorie toe")])],1)])]):_vm._e(),_c('tbody',[_c('tr',[_c('td',{staticClass:"cluster-score",attrs:{"colspan":_vm.store.rubric.levels.length + 2}},[_c('h5',{},[_vm._v("Cluster rapport")]),_c('p',[_vm._v("Maxmimum score: ")])])])])],2)}),_c('div',{staticClass:"row mb-4"},[_c('div',{staticClass:"col-12"},[_c('b-button',{staticClass:"pull-left w-100",attrs:{"variant":"primary","size":"lg"}},[_vm._v("Voeg nieuwe cluster toe")])],1)]),_c('div',{staticClass:"row"},[_c('div',{staticClass:"col-6"},[_c('div',{staticClass:"card"},[_c('div',{staticClass:"card-header"},[_c('h5',{staticClass:"card-title"},[_vm._v("Rubric Rapport")])]),_c('div',{staticClass:"card-body"},[_c('p',{staticClass:"pull-left"},[_vm._v("Maximum score: ")])])])])])],2)}
var staticRenderFns = []


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue?vue&type=template&id=8bbe3256&scoped=true&

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/classCallCheck.js
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.symbol.js
var es_symbol = __webpack_require__("10b3");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.symbol.description.js
var es_symbol_description = __webpack_require__("8d97");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.symbol.iterator.js
var es_symbol_iterator = __webpack_require__("6540");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.iterator.js
var es_array_iterator = __webpack_require__("7f72");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.object.to-string.js
var es_object_to_string = __webpack_require__("e790");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.string.iterator.js
var es_string_iterator = __webpack_require__("a7da");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/web.dom-collections.iterator.js
var web_dom_collections_iterator = __webpack_require__("4e83");

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/typeof.js








function _typeof3(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof3 = function _typeof3(obj) { return typeof obj; }; } else { _typeof3 = function _typeof3(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof3(obj); }

function _typeof2(obj) {
  if (typeof Symbol === "function" && _typeof3(Symbol.iterator) === "symbol") {
    _typeof2 = function _typeof2(obj) {
      return _typeof3(obj);
    };
  } else {
    _typeof2 = function _typeof2(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof3(obj);
    };
  }

  return _typeof2(obj);
}

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js


function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return _assertThisInitialized(self);
}
// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.object.get-prototype-of.js
var es_object_get_prototype_of = __webpack_require__("8cea");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.object.set-prototype-of.js
var es_object_set_prototype_of = __webpack_require__("e2c2");

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js


function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js

function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/inherits.js

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/tslib/tslib.es6.js
/*! *****************************************************************************
Copyright (c) Microsoft Corporation. All rights reserved.
Licensed under the Apache License, Version 2.0 (the "License"); you may not use
this file except in compliance with the License. You may obtain a copy of the
License at http://www.apache.org/licenses/LICENSE-2.0

THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
MERCHANTABLITY OR NON-INFRINGEMENT.

See the Apache Version 2.0 License for specific language governing permissions
and limitations under the License.
***************************************************************************** */
/* global Reflect, Promise */

var extendStatics = function(d, b) {
    extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return extendStatics(d, b);
};

function __extends(d, b) {
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

function __metadata(metadataKey, metadataValue) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : new P(function (resolve) { resolve(result.value); }).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
}

function __generator(thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
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

function __exportStar(m, exports) {
    for (var p in m) if (!exports.hasOwnProperty(p)) exports[p] = m[p];
}

function __values(o) {
    var m = typeof Symbol === "function" && o[Symbol.iterator], i = 0;
    if (m) return m.call(o);
    return {
        next: function () {
            if (o && i >= o.length) o = void 0;
            return { value: o && o[i++], done: !o };
        }
    };
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

function __spread() {
    for (var ar = [], i = 0; i < arguments.length; i++)
        ar = ar.concat(__read(arguments[i]));
    return ar;
}

function __spreadArrays() {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};

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
    function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: n === "return" } : f ? f(v) : v; } : f; }
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

function __importStar(mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (Object.hasOwnProperty.call(mod, k)) result[k] = mod[k];
    result.default = mod;
    return result;
}

function __importDefault(mod) {
    return (mod && mod.__esModule) ? mod : { default: mod };
}

// EXTERNAL MODULE: external {"commonjs":"vue","commonjs2":"vue","root":"Vue"}
var external_commonjs_vue_commonjs2_vue_root_Vue_ = __webpack_require__("8bbf");
var external_commonjs_vue_commonjs2_vue_root_Vue_default = /*#__PURE__*/__webpack_require__.n(external_commonjs_vue_commonjs2_vue_root_Vue_);

// CONCATENATED MODULE: D:/Sites/branch/node_modules/vue-class-component/dist/vue-class-component.esm.js
/**
  * vue-class-component v7.1.0
  * (c) 2015-present Evan You
  * @license MIT
  */


// The rational behind the verbose Reflect-feature check below is the fact that there are polyfills
// which add an implementation for Reflect.defineMetadata but not for Reflect.getOwnMetadataKeys.
// Without this check consumers will encounter hard to track down runtime errors.
var reflectionIsSupported = typeof Reflect !== 'undefined' && Reflect.defineMetadata && Reflect.getOwnMetadataKeys;
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
    var metaKeys = propertyKey
        ? Reflect.getOwnMetadataKeys(from, propertyKey)
        : Reflect.getOwnMetadataKeys(from);
    metaKeys.forEach(function (metaKey) {
        var metadata = propertyKey
            ? Reflect.getOwnMetadata(metaKey, from, propertyKey)
            : Reflect.getOwnMetadata(metaKey, from);
        if (propertyKey) {
            Reflect.defineMetadata(metaKey, metadata, to, propertyKey);
        }
        else {
            Reflect.defineMetadata(metaKey, metadata, to);
        }
    });
}

var fakeArray = { __proto__: [] };
var hasProto = fakeArray instanceof Array;
function createDecorator(factory) {
    return function (target, key, index) {
        var Ctor = typeof target === 'function'
            ? target
            : target.constructor;
        if (!Ctor.__decorators__) {
            Ctor.__decorators__ = [];
        }
        if (typeof index !== 'number') {
            index = undefined;
        }
        Ctor.__decorators__.push(function (options) { return factory(options, key, index); });
    };
}
function mixins() {
    var Ctors = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        Ctors[_i] = arguments[_i];
    }
    return external_commonjs_vue_commonjs2_vue_root_Vue_default.a.extend({ mixins: Ctors });
}
function isPrimitive(value) {
    var type = typeof value;
    return value == null || (type !== 'object' && type !== 'function');
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
        var keys = Object.getOwnPropertyNames(vm);
        // 2.2.0 compat (props are no longer exposed as self properties)
        if (vm.$options.props) {
            for (var key in vm.$options.props) {
                if (!vm.hasOwnProperty(key)) {
                    keys.push(key);
                }
            }
        }
        keys.forEach(function (key) {
            if (key.charAt(0) !== '_') {
                Object.defineProperty(_this, key, {
                    get: function () { return vm[key]; },
                    set: function (value) { vm[key] = value; },
                    configurable: true
                });
            }
        });
    };
    // should be acquired class property values
    var data = new Component();
    // restore original _init to avoid memory leak (#209)
    Component.prototype._init = originalInit;
    // create plain data object
    var plainData = {};
    Object.keys(data).forEach(function (key) {
        if (data[key] !== undefined) {
            plainData[key] = data[key];
        }
    });
    if (false) {}
    return plainData;
}

var $internalHooks = [
    'data',
    'beforeCreate',
    'created',
    'beforeMount',
    'mounted',
    'beforeDestroy',
    'destroyed',
    'beforeUpdate',
    'updated',
    'activated',
    'deactivated',
    'render',
    'errorCaptured',
    'serverPrefetch' // 2.6
];
function componentFactory(Component, options) {
    if (options === void 0) { options = {}; }
    options.name = options.name || Component._componentTag || Component.name;
    // prototype props.
    var proto = Component.prototype;
    Object.getOwnPropertyNames(proto).forEach(function (key) {
        if (key === 'constructor') {
            return;
        }
        // hooks
        if ($internalHooks.indexOf(key) > -1) {
            options[key] = proto[key];
            return;
        }
        var descriptor = Object.getOwnPropertyDescriptor(proto, key);
        if (descriptor.value !== void 0) {
            // methods
            if (typeof descriptor.value === 'function') {
                (options.methods || (options.methods = {}))[key] = descriptor.value;
            }
            else {
                // typescript decorated data
                (options.mixins || (options.mixins = [])).push({
                    data: function () {
                        var _a;
                        return _a = {}, _a[key] = descriptor.value, _a;
                    }
                });
            }
        }
        else if (descriptor.get || descriptor.set) {
            // computed properties
            (options.computed || (options.computed = {}))[key] = {
                get: descriptor.get,
                set: descriptor.set
            };
        }
    });
    (options.mixins || (options.mixins = [])).push({
        data: function () {
            return collectDataFromConstructor(this, Component);
        }
    });
    // decorate options
    var decorators = Component.__decorators__;
    if (decorators) {
        decorators.forEach(function (fn) { return fn(options); });
        delete Component.__decorators__;
    }
    // find super
    var superProto = Object.getPrototypeOf(Component.prototype);
    var Super = superProto instanceof external_commonjs_vue_commonjs2_vue_root_Vue_default.a
        ? superProto.constructor
        : external_commonjs_vue_commonjs2_vue_root_Vue_default.a;
    var Extended = Super.extend(options);
    forwardStaticMembers(Extended, Component, Super);
    if (reflectionIsSupported) {
        copyReflectionMetadata(Extended, Component);
    }
    return Extended;
}
var reservedPropertyNames = [
    // Unique id
    'cid',
    // Super Vue constructor
    'super',
    // Component options that will be used by the component
    'options',
    'superOptions',
    'extendOptions',
    'sealedOptions',
    // Private assets
    'component',
    'directive',
    'filter'
];
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
        }
        // Some browsers does not allow reconfigure built-in properties
        var extendedDescriptor = Object.getOwnPropertyDescriptor(Extended, key);
        if (extendedDescriptor && !extendedDescriptor.configurable) {
            return;
        }
        var descriptor = Object.getOwnPropertyDescriptor(Original, key);
        // If the user agent does not support `__proto__` or its family (IE <= 10),
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
            if (!isPrimitive(descriptor.value) &&
                superDescriptor &&
                superDescriptor.value === descriptor.value) {
                return;
            }
        }
        // Warn if the users manually declare reserved properties
        if (false) {}
        Object.defineProperty(Extended, key, descriptor);
    });
}

function vue_class_component_esm_Component(options) {
    if (typeof options === 'function') {
        return componentFactory(options);
    }
    return function (Component) {
        return componentFactory(Component, options);
    };
}
vue_class_component_esm_Component.registerHooks = function registerHooks(keys) {
    $internalHooks.push.apply($internalHooks, keys);
};

/* harmony default export */ var vue_class_component_esm = (vue_class_component_esm_Component);


// CONCATENATED MODULE: D:/Sites/branch/node_modules/vue-property-decorator/lib/vue-property-decorator.js
/** vue-property-decorator verson 8.2.2 MIT LICENSE copyright 2019 kaorun343 */
/// <reference types='reflect-metadata'/>




/** Used for keying reactive provide/inject properties */
var reactiveInjectKey = '__reactiveInject__';
/**
 * decorator of an inject
 * @param from key
 * @return PropertyDecorator
 */
function Inject(options) {
    return createDecorator(function (componentOptions, key) {
        if (typeof componentOptions.inject === 'undefined') {
            componentOptions.inject = {};
        }
        if (!Array.isArray(componentOptions.inject)) {
            componentOptions.inject[key] = options || key;
        }
    });
}
/**
 * decorator of a reactive inject
 * @param from key
 * @return PropertyDecorator
 */
function InjectReactive(options) {
    return createDecorator(function (componentOptions, key) {
        if (typeof componentOptions.inject === 'undefined') {
            componentOptions.inject = {};
        }
        if (!Array.isArray(componentOptions.inject)) {
            var fromKey_1 = !!options ? options.from || options : key;
            var defaultVal_1 = (!!options && options.default) || undefined;
            if (!componentOptions.computed)
                componentOptions.computed = {};
            componentOptions.computed[key] = function () {
                var obj = this[reactiveInjectKey];
                return obj ? obj[fromKey_1] : defaultVal_1;
            };
            componentOptions.inject[reactiveInjectKey] = reactiveInjectKey;
        }
    });
}
/**
 * decorator of a provide
 * @param key key
 * @return PropertyDecorator | void
 */
function Provide(key) {
    return createDecorator(function (componentOptions, k) {
        var provide = componentOptions.provide;
        if (typeof provide !== 'function' || !provide.managed) {
            var original_1 = componentOptions.provide;
            provide = componentOptions.provide = function () {
                var rv = Object.create((typeof original_1 === 'function' ? original_1.call(this) : original_1) ||
                    null);
                for (var i in provide.managed)
                    rv[provide.managed[i]] = this[i];
                return rv;
            };
            provide.managed = {};
        }
        provide.managed[k] = key || k;
    });
}
/**
 * decorator of a reactive provide
 * @param key key
 * @return PropertyDecorator | void
 */
function ProvideReactive(key) {
    return createDecorator(function (componentOptions, k) {
        var provide = componentOptions.provide;
        // inject parent reactive services (if any)
        if (!Array.isArray(componentOptions.inject)) {
            componentOptions.inject = componentOptions.inject || {};
            componentOptions.inject[reactiveInjectKey] = { from: reactiveInjectKey, default: {} };
        }
        if (typeof provide !== 'function' || !provide.managedReactive) {
            var original_2 = componentOptions.provide;
            provide = componentOptions.provide = function () {
                var _this = this;
                var rv = typeof original_2 === 'function'
                    ? original_2.call(this)
                    : original_2;
                rv = Object.create(rv || null);
                // set reactive services (propagates previous services if necessary)
                rv[reactiveInjectKey] = this[reactiveInjectKey] || {};
                var _loop_1 = function (i) {
                    rv[provide.managedReactive[i]] = this_1[i]; // Duplicates the behavior of `@Provide`
                    Object.defineProperty(rv[reactiveInjectKey], provide.managedReactive[i], {
                        enumerable: true,
                        get: function () { return _this[i]; },
                    });
                };
                var this_1 = this;
                for (var i in provide.managedReactive) {
                    _loop_1(i);
                }
                return rv;
            };
            provide.managedReactive = {};
        }
        provide.managedReactive[k] = key || k;
    });
}
/** @see {@link https://github.com/vuejs/vue-class-component/blob/master/src/reflect.ts} */
var reflectMetadataIsSupported = typeof Reflect !== 'undefined' && typeof Reflect.getMetadata !== 'undefined';
function applyMetadata(options, target, key) {
    if (reflectMetadataIsSupported) {
        if (!Array.isArray(options) &&
            typeof options !== 'function' &&
            typeof options.type === 'undefined') {
            options.type = Reflect.getMetadata('design:type', target, key);
        }
    }
}
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
/**
 * decorator of a prop
 * @param  options the options for the prop
 * @return PropertyDecorator | void
 */
function Prop(options) {
    if (options === void 0) { options = {}; }
    return function (target, key) {
        applyMetadata(options, target, key);
        createDecorator(function (componentOptions, k) {
            ;
            (componentOptions.props || (componentOptions.props = {}))[k] = options;
        })(target, key);
    };
}
/**
 * decorator of a synced prop
 * @param propName the name to interface with from outside, must be different from decorated property
 * @param options the options for the synced prop
 * @return PropertyDecorator | void
 */
function PropSync(propName, options) {
    if (options === void 0) { options = {}; }
    // @ts-ignore
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
                    // @ts-ignore
                    this.$emit("update:" + propName, value);
                },
            };
        })(target, key);
    };
}
/**
 * decorator of a watch function
 * @param  path the path or the expression to observe
 * @param  WatchOption
 * @return MethodDecorator
 */
function Watch(path, options) {
    if (options === void 0) { options = {}; }
    var _a = options.deep, deep = _a === void 0 ? false : _a, _b = options.immediate, immediate = _b === void 0 ? false : _b;
    return createDecorator(function (componentOptions, handler) {
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
// Code copied from Vue/src/shared/util.js
var hyphenateRE = /\B([A-Z])/g;
var hyphenate = function (str) { return str.replace(hyphenateRE, '-$1').toLowerCase(); };
/**
 * decorator of an event-emitter function
 * @param  event The name of the event
 * @return MethodDecorator
 */
function Emit(event) {
    return function (_target, key, descriptor) {
        key = hyphenate(key);
        var original = descriptor.value;
        descriptor.value = function emitter() {
            var _this = this;
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var emit = function (returnValue) {
                if (returnValue !== undefined)
                    args.unshift(returnValue);
                _this.$emit.apply(_this, [event || key].concat(args));
            };
            var returnValue = original.apply(this, args);
            if (isPromise(returnValue)) {
                returnValue.then(function (returnValue) {
                    emit(returnValue);
                });
            }
            else {
                emit(returnValue);
            }
            return returnValue;
        };
    };
}
/**
 * decorator of a ref prop
 * @param refKey the ref key defined in template
 */
function Ref(refKey) {
    return createDecorator(function (options, key) {
        options.computed = options.computed || {};
        options.computed[key] = {
            cache: false,
            get: function () {
                return this.$refs[refKey || key];
            },
        };
    });
}
function isPromise(obj) {
    return obj instanceof Promise || (obj && typeof obj.then === 'function');
}

// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"941cbcf2-vue-loader-template"}!D:/Sites/branch/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/LevelsTable.vue?vue&type=template&id=5938d079&scoped=true&
var LevelsTablevue_type_template_id_5938d079_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('table',{staticClass:"table table-bordered"},[_c('thead',[_c('tr',{staticClass:"levels-header"},[_c('th',{attrs:{"scope":"col"}},[_c('collapse',{attrs:{"collapsed":_vm.collapsed},on:{"toggle-collapse":_vm.toggleConfigurationCollapsed}},[_vm._t("default",[_c('div',{staticClass:"spacer"}),_c('div',{staticClass:"level-title"},[_vm._v(" Niveau ")]),_c('div',{staticClass:"spacer"})])],2)],1),_c('th',{staticClass:"levels-title",attrs:{"scope":"col"}},[_vm._v("Beschrijving")]),(_vm.store.useScore)?_c('th',{staticClass:"levels-title",attrs:{"scope":"col"}},[_vm._v("Score")]):_vm._e(),_c('th',{staticClass:"levels-title",attrs:{"scope":"col"}},[_vm._v("Standaard")]),_c('th',{staticClass:"levels-title",attrs:{"scope":"col"}})])]),_c('tbody',{directives:[{name:"show",rawName:"v-show",value:(!_vm.collapsed),expression:"!collapsed"}],staticClass:"table-striped"},[_vm._l((_vm.rubric.levels),function(level,levelIndex){return _c('tr',{attrs:{"scope":"row"}},[_c('td',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(level.title),expression:"level.title"}],staticClass:"form-control text-area-level-title font-weight-bold",attrs:{"placeholder":"Vul hier een titel in"},domProps:{"value":(level.title)},on:{"input":function($event){if($event.target.composing){ return; }_vm.$set(level, "title", $event.target.value)}}})]),_c('td',[_c('textarea',{directives:[{name:"model",rawName:"v-model",value:(level.description),expression:"level.description"}],staticClass:"form-control text-area-level-description",attrs:{"placeholder":"Vul hier een beschrijving in"},domProps:{"value":(level.description)},on:{"input":function($event){if($event.target.composing){ return; }_vm.$set(level, "description", $event.target.value)}}})]),(_vm.store.rubric.useScores)?_c('td',[_c('b-input-group',{staticClass:"score-input-group",attrs:{"append":"Punten"}},[_c('input',{directives:[{name:"model",rawName:"v-model",value:(level.score),expression:"level.score"}],staticClass:"form-control",attrs:{"type":"number","name":"Weight","maxlength":"3"},domProps:{"value":(level.score)},on:{"input":function($event){if($event.target.composing){ return; }_vm.$set(level, "score", $event.target.value)}}})])],1):_vm._e(),_c('td',[_c('b-form-radio',{attrs:{"name":"isDefault","value":""},model:{value:(level.isDefault),callback:function ($$v) {_vm.$set(level, "isDefault", $$v)},expression:"level.isDefault"}})],1),_c('td',[_c('MoveDeleteBar',{attrs:{"index":levelIndex,"max-index":_vm.rubric.levels.length - 1},on:{"move-up":function($event){return _vm.rubric.moveLevelUp(level)},"move-down":function($event){return _vm.rubric.moveLevelDown(level)},"remove":function($event){return _vm.removeLevel(level)}}})],1)])}),_c('tr',{attrs:{"scope":"row"}},[_c('td',{staticClass:"button-row",attrs:{"colspan":_vm.rubric.levels.length}},[_c('button',{staticClass:"btn btn-sm btn-primary ml-1 pull-left",on:{"click":function($event){return _vm.rubric.addLevel()}}},[_c('i',{staticClass:"fa fa-plus",attrs:{"aria-hidden":"true"}}),_vm._v(" Voeg niveau toe ")])])])],2)])])}
var LevelsTablevue_type_template_id_5938d079_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/LevelsTable.vue?vue&type=template&id=5938d079&scoped=true&

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/createClass.js
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"941cbcf2-vue-loader-template"}!D:/Sites/branch/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/Collapse.vue?vue&type=template&id=0488f0db&scoped=true&
var Collapsevue_type_template_id_0488f0db_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"collapse-header"},[_c('div',[_c('button',{staticClass:"btn btn-sm btn-secondary",on:{"click":function($event){return _vm.$emit('toggle-collapse')}}},[_c('i',{staticClass:"fa fa-2x pull-left caret",class:_vm.caretClass,attrs:{"aria-hidden":"true"}})])]),_vm._t("default")],2)}
var Collapsevue_type_template_id_0488f0db_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/Collapse.vue?vue&type=template&id=0488f0db&scoped=true&

// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--13-0!D:/Sites/branch/node_modules/thread-loader/dist/cjs.js!D:/Sites/branch/node_modules/babel-loader/lib!D:/Sites/branch/node_modules/ts-loader??ref--13-3!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/Collapse.vue?vue&type=script&lang=ts&








var Collapsevue_type_script_lang_ts_Collapse =
/*#__PURE__*/
function (_Vue) {
  _inherits(Collapse, _Vue);

  function Collapse() {
    _classCallCheck(this, Collapse);

    return _possibleConstructorReturn(this, _getPrototypeOf(Collapse).apply(this, arguments));
  }

  _createClass(Collapse, [{
    key: "caretClass",
    get: function get() {
      return {
        "fa-caret-down": !this.collapsed,
        "fa-caret-right": this.collapsed
      };
    }
  }]);

  return Collapse;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop()], Collapsevue_type_script_lang_ts_Collapse.prototype, "collapsed", void 0);

__decorate([Prop()], Collapsevue_type_script_lang_ts_Collapse.prototype, "title", void 0);

Collapsevue_type_script_lang_ts_Collapse = __decorate([vue_class_component_esm({
  components: {}
})], Collapsevue_type_script_lang_ts_Collapse);
/* harmony default export */ var Collapsevue_type_script_lang_ts_ = (Collapsevue_type_script_lang_ts_Collapse);
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/Collapse.vue?vue&type=script&lang=ts&
 /* harmony default export */ var Components_Collapsevue_type_script_lang_ts_ = (Collapsevue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/Plugins/ScoreRubric/Components/Collapse.vue?vue&type=style&index=0&id=0488f0db&scoped=true&lang=css&
var Collapsevue_type_style_index_0_id_0488f0db_scoped_true_lang_css_ = __webpack_require__("5ff6");

// CONCATENATED MODULE: D:/Sites/branch/node_modules/vue-loader/lib/runtime/componentNormalizer.js
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

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
  if (moduleIdentifier) { // server build
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
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}

// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/Collapse.vue






/* normalize component */

var component = normalizeComponent(
  Components_Collapsevue_type_script_lang_ts_,
  Collapsevue_type_template_id_0488f0db_scoped_true_render,
  Collapsevue_type_template_id_0488f0db_scoped_true_staticRenderFns,
  false,
  null,
  "0488f0db",
  null
  
)

/* harmony default export */ var Components_Collapse = (component.exports);
// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"941cbcf2-vue-loader-template"}!D:/Sites/branch/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/MoveDeleteBar.vue?vue&type=template&id=27edbb60&scoped=true&
var MoveDeleteBarvue_type_template_id_27edbb60_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"btn-group btn-group-sm",attrs:{"role":"group","aria-label":"Actie"}},[(_vm.index > 0)?_c('button',{directives:[{name:"b-popover",rawName:"v-b-popover.hover.top",value:('Verplaats naar boven'),expression:"'Verplaats naar boven'",modifiers:{"hover":true,"top":true}}],staticClass:"btn btn-secondary",on:{"click":function($event){return _vm.$emit('move-up')}}},[_c('i',{staticClass:"fa fa-arrow-up",attrs:{"aria-hidden":"true"}})]):_vm._e(),_c('button',{directives:[{name:"b-popover",rawName:"v-b-popover.hover.top",value:('Verwijder'),expression:"'Verwijder'",modifiers:{"hover":true,"top":true}}],staticClass:"btn btn-danger",on:{"click":function($event){return _vm.$emit('remove')}}},[_c('i',{staticClass:"fa fa-minus-circle",attrs:{"aria-hidden":"true"}})]),(_vm.index < _vm.maxIndex)?_c('button',{directives:[{name:"b-popover",rawName:"v-b-popover.hover.top",value:('Verplaats naar beneden'),expression:"'Verplaats naar beneden'",modifiers:{"hover":true,"top":true}}],staticClass:"btn btn-secondary",on:{"click":function($event){return _vm.$emit('move-down')}}},[_c('i',{staticClass:"fa fa-arrow-down",attrs:{"aria-hidden":"true"}})]):_vm._e()])}
var MoveDeleteBarvue_type_template_id_27edbb60_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/MoveDeleteBar.vue?vue&type=template&id=27edbb60&scoped=true&

// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--13-0!D:/Sites/branch/node_modules/thread-loader/dist/cjs.js!D:/Sites/branch/node_modules/babel-loader/lib!D:/Sites/branch/node_modules/ts-loader??ref--13-3!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/MoveDeleteBar.vue?vue&type=script&lang=ts&







var MoveDeleteBarvue_type_script_lang_ts_MoveDeleteBar =
/*#__PURE__*/
function (_Vue) {
  _inherits(MoveDeleteBar, _Vue);

  function MoveDeleteBar() {
    _classCallCheck(this, MoveDeleteBar);

    return _possibleConstructorReturn(this, _getPrototypeOf(MoveDeleteBar).apply(this, arguments));
  }

  return MoveDeleteBar;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop()], MoveDeleteBarvue_type_script_lang_ts_MoveDeleteBar.prototype, "index", void 0);

__decorate([Prop()], MoveDeleteBarvue_type_script_lang_ts_MoveDeleteBar.prototype, "maxIndex", void 0);

MoveDeleteBarvue_type_script_lang_ts_MoveDeleteBar = __decorate([vue_class_component_esm({
  components: {}
})], MoveDeleteBarvue_type_script_lang_ts_MoveDeleteBar);
/* harmony default export */ var MoveDeleteBarvue_type_script_lang_ts_ = (MoveDeleteBarvue_type_script_lang_ts_MoveDeleteBar);
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/MoveDeleteBar.vue?vue&type=script&lang=ts&
 /* harmony default export */ var Components_MoveDeleteBarvue_type_script_lang_ts_ = (MoveDeleteBarvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/MoveDeleteBar.vue





/* normalize component */

var MoveDeleteBar_component = normalizeComponent(
  Components_MoveDeleteBarvue_type_script_lang_ts_,
  MoveDeleteBarvue_type_template_id_27edbb60_scoped_true_render,
  MoveDeleteBarvue_type_template_id_27edbb60_scoped_true_staticRenderFns,
  false,
  null,
  "27edbb60",
  null
  
)

/* harmony default export */ var Components_MoveDeleteBar = (MoveDeleteBar_component.exports);
// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--13-0!D:/Sites/branch/node_modules/thread-loader/dist/cjs.js!D:/Sites/branch/node_modules/babel-loader/lib!D:/Sites/branch/node_modules/ts-loader??ref--13-3!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/LevelsTable.vue?vue&type=script&lang=ts&










var LevelsTablevue_type_script_lang_ts_LevelsTable =
/*#__PURE__*/
function (_Vue) {
  _inherits(LevelsTable, _Vue);

  function LevelsTable() {
    var _this;

    _classCallCheck(this, LevelsTable);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(LevelsTable).apply(this, arguments));
    _this.collapsed = true;
    return _this;
  }

  _createClass(LevelsTable, [{
    key: "toggleConfigurationCollapsed",
    value: function toggleConfigurationCollapsed() {
      this.collapsed = !this.collapsed;
    }
  }, {
    key: "removeLevel",
    value: function removeLevel(level) {
      if (confirm("Niveau verwijderen?") === false) {
        return;
      }

      this.rubric.removeLevel(level);
    }
  }, {
    key: "store",
    get: function get() {
      return this.$root.$data.store;
    }
  }, {
    key: "rubric",
    get: function get() {
      return this.store.rubric;
    }
  }]);

  return LevelsTable;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

LevelsTablevue_type_script_lang_ts_LevelsTable = __decorate([vue_class_component_esm({
  components: {
    MoveDeleteBar: Components_MoveDeleteBar,
    Collapse: Components_Collapse
  }
})], LevelsTablevue_type_script_lang_ts_LevelsTable);
/* harmony default export */ var LevelsTablevue_type_script_lang_ts_ = (LevelsTablevue_type_script_lang_ts_LevelsTable);
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/LevelsTable.vue?vue&type=script&lang=ts&
 /* harmony default export */ var Components_LevelsTablevue_type_script_lang_ts_ = (LevelsTablevue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/Plugins/ScoreRubric/Components/LevelsTable.vue?vue&type=style&index=0&id=5938d079&scoped=true&lang=css&
var LevelsTablevue_type_style_index_0_id_5938d079_scoped_true_lang_css_ = __webpack_require__("840a");

// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/LevelsTable.vue






/* normalize component */

var LevelsTable_component = normalizeComponent(
  Components_LevelsTablevue_type_script_lang_ts_,
  LevelsTablevue_type_template_id_5938d079_scoped_true_render,
  LevelsTablevue_type_template_id_5938d079_scoped_true_staticRenderFns,
  false,
  null,
  "5938d079",
  null
  
)

/* harmony default export */ var Components_LevelsTable = (LevelsTable_component.exports);
// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"941cbcf2-vue-loader-template"}!D:/Sites/branch/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/Configuration.vue?vue&type=template&id=921e9e6a&scoped=true&
var Configurationvue_type_template_id_921e9e6a_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"card"},[_vm._m(0),_c('div',{staticClass:"card-body options"},[_c('b-form-checkbox',{attrs:{"name":"score","value":true,"unchecked-value":false},model:{value:(_vm.store.rubric.useScores),callback:function ($$v) {_vm.$set(_vm.store.rubric, "useScores", $$v)},expression:"store.rubric.useScores"}},[_vm._v(" Gebruik score ")])],1)])}
var Configurationvue_type_template_id_921e9e6a_scoped_true_staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"card-header"},[_c('h5',{staticClass:"card-title"},[_vm._v(" Configuratie Rubric ")])])}]


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/Configuration.vue?vue&type=template&id=921e9e6a&scoped=true&

// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--13-0!D:/Sites/branch/node_modules/thread-loader/dist/cjs.js!D:/Sites/branch/node_modules/babel-loader/lib!D:/Sites/branch/node_modules/ts-loader??ref--13-3!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/Configuration.vue?vue&type=script&lang=ts&








var Configurationvue_type_script_lang_ts_Configuration =
/*#__PURE__*/
function (_Vue) {
  _inherits(Configuration, _Vue);

  function Configuration() {
    _classCallCheck(this, Configuration);

    return _possibleConstructorReturn(this, _getPrototypeOf(Configuration).apply(this, arguments));
  }

  _createClass(Configuration, [{
    key: "store",
    get: function get() {
      return this.$root.$data.store;
    }
  }]);

  return Configuration;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

Configurationvue_type_script_lang_ts_Configuration = __decorate([vue_class_component_esm({
  components: {}
})], Configurationvue_type_script_lang_ts_Configuration);
/* harmony default export */ var Configurationvue_type_script_lang_ts_ = (Configurationvue_type_script_lang_ts_Configuration);
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/Configuration.vue?vue&type=script&lang=ts&
 /* harmony default export */ var Components_Configurationvue_type_script_lang_ts_ = (Configurationvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/Plugins/ScoreRubric/Components/Configuration.vue?vue&type=style&index=0&id=921e9e6a&scoped=true&lang=css&
var Configurationvue_type_style_index_0_id_921e9e6a_scoped_true_lang_css_ = __webpack_require__("7a70");

// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/Configuration.vue






/* normalize component */

var Configuration_component = normalizeComponent(
  Components_Configurationvue_type_script_lang_ts_,
  Configurationvue_type_template_id_921e9e6a_scoped_true_render,
  Configurationvue_type_template_id_921e9e6a_scoped_true_staticRenderFns,
  false,
  null,
  "921e9e6a",
  null
  
)

/* harmony default export */ var Components_Configuration = (Configuration_component.exports);
// CONCATENATED MODULE: D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--13-0!D:/Sites/branch/node_modules/thread-loader/dist/cjs.js!D:/Sites/branch/node_modules/babel-loader/lib!D:/Sites/branch/node_modules/ts-loader??ref--13-3!D:/Sites/branch/node_modules/cache-loader/dist/cjs.js??ref--0-0!D:/Sites/branch/node_modules/vue-loader/lib??vue-loader-options!./src/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue?vue&type=script&lang=ts&











var ScoreRubricBuildervue_type_script_lang_ts_ScoreRubricBuilder =
/*#__PURE__*/
function (_Vue) {
  _inherits(ScoreRubricBuilder, _Vue);

  function ScoreRubricBuilder() {
    var _this;

    _classCallCheck(this, ScoreRubricBuilder);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ScoreRubricBuilder).apply(this, arguments));
    _this.store = _this.$root.$data.store;
    return _this;
  }

  return ScoreRubricBuilder;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

ScoreRubricBuildervue_type_script_lang_ts_ScoreRubricBuilder = __decorate([vue_class_component_esm({
  components: {
    MoveDeleteBar: Components_MoveDeleteBar,
    Collapse: Components_Collapse,
    Configuration: Components_Configuration,
    LevelsTable: Components_LevelsTable
  },
  filters: {
    capitalize: function capitalize(value) {
      if (!value) {
        return "";
      }

      return value.toUpperCase();
    }
  }
})], ScoreRubricBuildervue_type_script_lang_ts_ScoreRubricBuilder);
/* harmony default export */ var ScoreRubricBuildervue_type_script_lang_ts_ = (ScoreRubricBuildervue_type_script_lang_ts_ScoreRubricBuilder); //todo replace border with padding
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue?vue&type=script&lang=ts&
 /* harmony default export */ var Components_ScoreRubricBuildervue_type_script_lang_ts_ = (ScoreRubricBuildervue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue?vue&type=style&index=0&id=8bbe3256&scoped=true&lang=css&
var ScoreRubricBuildervue_type_style_index_0_id_8bbe3256_scoped_true_lang_css_ = __webpack_require__("d1bf");

// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue






/* normalize component */

var ScoreRubricBuilder_component = normalizeComponent(
  Components_ScoreRubricBuildervue_type_script_lang_ts_,
  render,
  staticRenderFns,
  false,
  null,
  "8bbe3256",
  null
  
)

/* harmony default export */ var Components_ScoreRubricBuilder = (ScoreRubricBuilder_component.exports);
// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.concat.js
var es_array_concat = __webpack_require__("7fbc");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.filter.js
var es_array_filter = __webpack_require__("31af");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.for-each.js
var es_array_for_each = __webpack_require__("ac9b");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.from.js
var es_array_from = __webpack_require__("2739");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.index-of.js
var es_array_index_of = __webpack_require__("6d5f");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.map.js
var es_array_map = __webpack_require__("0427");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.reduce.js
var es_array_reduce = __webpack_require__("015e");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.splice.js
var es_array_splice = __webpack_require__("a071");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.date.to-json.js
var es_date_to_json = __webpack_require__("3815");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.map.js
var es_map = __webpack_require__("6eee");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/web.dom-collections.for-each.js
var web_dom_collections_for_each = __webpack_require__("6d8c");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/web.url.to-json.js
var web_url_to_json = __webpack_require__("a862");

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.date.to-string.js
var es_date_to_string = __webpack_require__("105f");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.regexp.to-string.js
var es_regexp_to_string = __webpack_require__("50e5");

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/iterableToArray.js










function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}
// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.object.get-own-property-descriptor.js
var es_object_get_own_property_descriptor = __webpack_require__("7bb8");

// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.reflect.get.js
var es_reflect_get = __webpack_require__("3596");

// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/superPropBase.js

function _superPropBase(object, property) {
  while (!Object.prototype.hasOwnProperty.call(object, property)) {
    object = _getPrototypeOf(object);
    if (object === null) break;
  }

  return object;
}
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@babel/runtime/helpers/esm/get.js



function get_get(target, property, receiver) {
  if (typeof Reflect !== "undefined" && Reflect.get) {
    get_get = Reflect.get;
  } else {
    get_get = function _get(target, property, receiver) {
      var base = _superPropBase(target, property);
      if (!base) return;
      var desc = Object.getOwnPropertyDescriptor(base, property);

      if (desc.get) {
        return desc.get.call(receiver);
      }

      return desc.value;
    };
  }

  return get_get(target, property, receiver || target);
}
// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Criterium.ts







var Criterium_Criterium =
/*#__PURE__*/
function () {
  function Criterium(title, id) {
    _classCallCheck(this, Criterium);

    this.weight = 100;
    this.selectedLevelIndex = 0;
    this.parent = null;
    this.children = [];
    this.choices = [];
    if (!id) this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
    else this.id = id;
    this.title = title;
  }

  _createClass(Criterium, [{
    key: "weightToString",
    value: function weightToString() {
      return String(this.weight * 100);
    }
  }, {
    key: "getScore",
    value: function getScore() {
      return 0;
    }
  }, {
    key: "toString",
    value: function toString() {
      return "Criterium (id: ".concat(this.id, ", title: ").concat(this.title, ")");
    }
  }, {
    key: "toJSON",
    value: function toJSON() {
      return {
        id: this.id,
        title: this.title,
        weight: this.weight
      };
    }
  }], [{
    key: "fromJSON",
    value: function fromJSON(criterium) {
      var criteriumObject;

      if (typeof criterium === 'string') {
        criteriumObject = JSON.parse(criterium);
      } else {
        criteriumObject = criterium;
      }

      var newCriterium = new Criterium(criteriumObject.title, criteriumObject.id);
      newCriterium.weight = criteriumObject.weight;
      return newCriterium;
    }
  }]);

  return Criterium;
}();


// EXTERNAL MODULE: D:/Sites/branch/node_modules/core-js/modules/es.array.slice.js
var es_array_slice = __webpack_require__("9186");

// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Container.ts






var Container_Container =
/*#__PURE__*/
function () {
  function Container() {
    var title = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

    _classCallCheck(this, Container);

    this.title = '';
    this._children = [];
    this.parent = null;
    this.title = title;
  }

  _createClass(Container, [{
    key: "addChild",
    value: function addChild(element) {
      element.parent = this;

      this._children.push(element);

      if (this.parent) this.parent.notifyAddChild(element);
    }
  }, {
    key: "notifyAddChild",
    value: function notifyAddChild(element) {
      if (this.parent) {
        //bubble up the change
        this.parent.notifyAddChild(element);
      }
    }
  }, {
    key: "notifyRemoveChild",
    value: function notifyRemoveChild(container, element) {
      if (this.parent) //bubble up the chain
        this.parent.notifyRemoveChild(container, element);
    }
  }, {
    key: "removeChild",
    value: function removeChild(element) {
      if (element.parent !== this) {
        throw new Error("element: " + element.title + " not part of container: " + this.title);
      }

      var index = this._children.indexOf(element);

      this._children.splice(index, 1);

      if (this.parent) this.parent.notifyRemoveChild(this, element);
      element.parent = null;
    }
    /*static moveElementToContainerAtIndex(element: Element, container: Container, index:number ) {
        ContainerManager.removeElementFromContainer(element, container);
    }*/

  }, {
    key: "moveItemInArray",
    value: function moveItemInArray(array, from, to) {
      if (to >= array.length || from >= array.length) return;
      if (to < 0 || from < 0) return;
      array.splice(to, 0, array.splice(from, 1)[0]);
    }
  }, {
    key: "children",
    get: function get() {
      return this._children.slice();
    }
  }]);

  return Container;
}();


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Category.ts













var Category_Category =
/*#__PURE__*/
function (_Container) {
  _inherits(Category, _Container);

  function Category() {
    var _this;

    _classCallCheck(this, Category);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Category).apply(this, arguments));
    _this.color = 'blue';
    return _this;
  }

  _createClass(Category, [{
    key: "getScore",
    value: function getScore() {
      return 0;
    }
  }, {
    key: "addCriterium",
    value: function addCriterium(criterium) {
      get_get(_getPrototypeOf(Category.prototype), "addChild", this).call(this, criterium);
    }
  }, {
    key: "removeCriterium",
    value: function removeCriterium(criterium) {
      get_get(_getPrototypeOf(Category.prototype), "removeChild", this).call(this, criterium);
    }
  }, {
    key: "toJSON",
    value: function toJSON() {
      return {
        title: this.title,
        color: this.color,
        criteria: this._children
      };
    }
  }, {
    key: "criteria",
    get: function get() {
      return this.children.filter(function (child) {
        return child instanceof Criterium_Criterium;
      });
    }
  }], [{
    key: "fromJSON",
    value: function fromJSON(category) {
      var categoryObject;

      if (typeof category === 'string') {
        categoryObject = JSON.parse(category);
      } else {
        categoryObject = category;
      }

      var newCategory = new Category(categoryObject.title);
      newCategory.color = categoryObject.color;
      categoryObject.criteria.map(function (criteriumJsonObject) {
        return Criterium_Criterium.fromJSON(criteriumJsonObject);
      }).forEach(function (criterium) {
        return newCategory.addChild(criterium);
      });
      return newCategory;
    }
  }]);

  return Category;
}(Container_Container);


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Cluster.ts
















var Cluster_Cluster =
/*#__PURE__*/
function (_Container) {
  _inherits(Cluster, _Container);

  function Cluster() {
    var _this;

    _classCallCheck(this, Cluster);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Cluster).apply(this, arguments));
    _this.collapsed = false;
    return _this;
  }

  _createClass(Cluster, [{
    key: "getScore",
    value: function getScore() {
      return 0;
    }
  }, {
    key: "toggleCollapsed",
    value: function toggleCollapsed() {
      this.collapsed = !this.collapsed;
    }
  }, {
    key: "addCategory",
    value: function addCategory(category) {
      get_get(_getPrototypeOf(Cluster.prototype), "addChild", this).call(this, category);
    }
  }, {
    key: "addCriterium",
    value: function addCriterium(criterium) {
      get_get(_getPrototypeOf(Cluster.prototype), "addChild", this).call(this, criterium);
    }
  }, {
    key: "removeCriterium",
    value: function removeCriterium(criterium) {
      get_get(_getPrototypeOf(Cluster.prototype), "removeChild", this).call(this, criterium);
    }
  }, {
    key: "removeCategory",
    value: function removeCategory(category) {
      get_get(_getPrototypeOf(Cluster.prototype), "removeChild", this).call(this, category);
    }
  }, {
    key: "toJSON",
    value: function toJSON() {
      return {
        title: this.title,
        categories: this.children.filter(function (child) {
          return child instanceof Category_Category;
        }).map(function (category) {
          return category.toJSON();
        }),
        criteria: this.children.filter(function (child) {
          return child instanceof Criterium_Criterium;
        }).map(function (criterium) {
          return criterium.toJSON();
        })
      };
    }
  }, {
    key: "criteria",
    get: function get() {
      return this.children.filter(function (child) {
        return child instanceof Criterium_Criterium;
      });
    }
  }, {
    key: "categories",
    get: function get() {
      return this.children.filter(function (child) {
        return child instanceof Category_Category;
      });
    }
  }, {
    key: "clusters",
    get: function get() {
      return this.children; //invariant garded at addChild
    }
  }], [{
    key: "fromJSON",
    value: function fromJSON(cluster) {
      var clusterObject;

      if (typeof cluster === 'string') {
        clusterObject = JSON.parse(cluster);
      } else {
        clusterObject = cluster;
      }

      var newCluster = new Cluster(clusterObject.title);
      clusterObject.categories.map(function (categoryJsonObject) {
        return Category_Category.fromJSON(categoryJsonObject);
      }).forEach(function (category) {
        return newCluster.addCategory(category);
      });
      clusterObject.criteria.map(function (criteriumObject) {
        return Criterium_Criterium.fromJSON(criteriumObject);
      }).forEach(function (criterium) {
        return newCluster.addCriterium(criterium);
      });
      return newCluster;
    }
  }]);

  return Cluster;
}(Container_Container);


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Level.ts








var Signal;

(function (Signal) {
  Signal[Signal["GREEN"] = 0] = "GREEN";
  Signal[Signal["ORANGE"] = 1] = "ORANGE";
  Signal[Signal["RED"] = 2] = "RED";
})(Signal || (Signal = {}));

var Level_Level =
/*#__PURE__*/
function () {
  function Level(title) {
    var description = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    var score = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 10;
    var signal = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : Signal.GREEN;
    var isDefault = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;
    var id = arguments.length > 5 ? arguments[5] : undefined;

    _classCallCheck(this, Level);

    this.title = title;
    this.description = description;
    this.score = score;
    this.signal = signal;
    this.isDefault = isDefault;
    if (!id) this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); // GUID
    else this.id = id;
  }

  _createClass(Level, [{
    key: "toString",
    value: function toString() {
      return "Level (id: ".concat(this.id, ", title: ").concat(this.title, ")");
    }
  }, {
    key: "toJSON",
    value: function toJSON() {
      return {
        id: this.id,
        title: this.title,
        description: this.description,
        score: this.score,
        isDefault: this.isDefault
      };
    }
  }], [{
    key: "fromJSON",
    value: function fromJSON(level) {
      var levelObject;

      if (typeof level === 'string') {
        levelObject = JSON.parse(level);
      } else {
        levelObject = level;
      }

      var newLevel = new Level(levelObject.title, levelObject.description, levelObject.score);
      newLevel.isDefault = levelObject.isDefault;
      newLevel.id = levelObject.id;
      return newLevel;
    }
  }]);

  return Level;
}();


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Choice.ts



var Choice_Choice =
/*#__PURE__*/
function () {
  function Choice() {
    var selected = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var feedback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

    _classCallCheck(this, Choice);

    this.hasFixedScore = false;
    this.fixedScore = 10;
    this.selected = selected;
    this.feedback = feedback;
  }

  _createClass(Choice, [{
    key: "toJSON",
    value: function toJSON() {
      return {
        selected: this.selected,
        feedback: this.feedback,
        hasFixedScore: this.hasFixedScore,
        fixedScore: this.fixedScore
      };
    }
  }], [{
    key: "fromJSON",
    value: function fromJSON(choice) {
      var choiceObject;

      if (typeof choice === 'string') {
        choiceObject = JSON.parse(choice);
      } else {
        choiceObject = choice;
      }

      var newChoice = new Choice(choiceObject.selected, choiceObject.feedback);
      newChoice.hasFixedScore = choiceObject.hasFixedScore;
      newChoice.fixedScore = choiceObject.fixedScore;
      return newChoice;
    }
  }]);

  return Choice;
}();


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/Domain/Rubric.ts




























function isElement(object) {
  return 'parent' in object && 'title' in object;
}
function isContainer(object) {
  return 'children' in object && isElement(object);
}

var Rubric_Rubric =
/*#__PURE__*/
function (_Container) {
  _inherits(Rubric, _Container);

  function Rubric() {
    var _this;

    _classCallCheck(this, Rubric);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Rubric).apply(this, arguments));
    _this.useScores = true;
    _this.levels = [];
    _this.choices = new Map();
    return _this;
  }

  _createClass(Rubric, [{
    key: "addCluster",
    value: function addCluster(cluster) {
      this.addChild(cluster);
    }
  }, {
    key: "removeCluster",
    value: function removeCluster(cluster) {
      this.removeChild(cluster);
    }
  }, {
    key: "addChild",
    value: function addChild(element) {
      get_get(_getPrototypeOf(Rubric.prototype), "addChild", this).call(this, element);

      this.notifyAddChild(element);
    }
  }, {
    key: "notifyAddChild",
    value: function notifyAddChild(element) {
      var _this2 = this;

      if (element instanceof Criterium_Criterium) {
        this.onCriteriumAdded(element);
      } else if (element instanceof Container_Container) {
        var addedCriteria = this.getAllCriteria(element);
        addedCriteria.forEach(function (criterium) {
          _this2.levels.forEach(function (level) {
            var choice = _this2.findChoice(criterium, level);

            if (!choice) choice = new Choice_Choice(false, "");

            _this2.addChoice(choice, criterium.id, level.id);
          });
        });
      } //no more bubbling

    }
  }, {
    key: "onCriteriumAdded",
    value: function onCriteriumAdded(criterium) {
      var _this3 = this;

      this.levels.forEach(function (level) {
        //choice already exists for criterium? Could be through json bootstrapping.
        var choice = _this3.findChoice(criterium, level);

        if (!choice) choice = new Choice_Choice(false, "");

        _this3.addChoice(choice, criterium.id, level.id);
      });
    }
  }, {
    key: "notifyRemoveChild",
    value: function notifyRemoveChild(container, element) {
      var _this4 = this;

      var criteriaToBeRemoved = this.getAllCriteria(container);
      criteriaToBeRemoved.forEach(function (criterium) {
        return _this4.removeChoicesByCriterium(criterium);
      });
    }
  }, {
    key: "toJSON",
    value: function toJSON() {
      return {
        useScores: this.useScores,
        title: this.title,
        levels: this.levels,
        clusters: this._children.map(function (cluster) {
          return cluster.toJSON();
        }),
        choices: this.getChoicesJSON()
      };
    }
  }, {
    key: "getChoicesJSON",
    value: function getChoicesJSON() {
      var choicesArray = [];
      this.choices.forEach(function (levelMap, criteriumId) {
        levelMap.forEach(function (choice, levelId) {
          choicesArray.push({
            "criteriumId": criteriumId,
            "levelId": levelId,
            "choice": choice.toJSON()
          });
        });
      });
      return choicesArray;
    }
  }, {
    key: "addChoice",
    value: function addChoice(choice, criteriumId, levelId) {
      var criteriumChoices = this.choices.get(criteriumId);

      if (criteriumChoices === undefined) {
        criteriumChoices = new Map();
        this.choices.set(criteriumId, criteriumChoices);
      }

      criteriumChoices.set(levelId, choice);
    }
  }, {
    key: "removeChoicesByCriterium",
    value: function removeChoicesByCriterium(criterium) {
      this.choices.delete(criterium.id);
    }
  }, {
    key: "removeChoicesByLevel",
    value: function removeChoicesByLevel(level) {
      Array.from(this.choices.values()).forEach(function (levelChoices) {
        return levelChoices.delete(level.id);
      });
    }
  }, {
    key: "findChoice",
    value: function findChoice(criterium, level) {
      var criteriumChoices = this.choices.get(criterium.id);

      if (criteriumChoices === undefined) {
        return undefined;
      }

      return criteriumChoices.get(level.id);
    }
    /**
     * Invariant: to the outside world a choice is always available for a criterium and level of the rubric.
     * @param criterium
     * @param level
     */

  }, {
    key: "getChoice",
    value: function getChoice(criterium, level) {
      var choice = this.findChoice(criterium, level);

      if (!choice) {
        throw new Error("No choice found for criteria: ".concat(criterium, " and level: ").concat(level));
      }

      return choice;
    }
  }, {
    key: "addLevel",
    value: function addLevel(level) {
      var _this5 = this;

      this.levels.push(level);
      this.getAllCriteria().forEach(function (criterium) {
        _this5.addChoice(new Choice_Choice(false, ""), criterium.id, level.id);
      });
    }
  }, {
    key: "removeLevel",
    value: function removeLevel(level) {
      var index = this.levels.indexOf(level);
      this.levels.splice(index, 1);
      this.removeChoicesByLevel(level);
    }
  }, {
    key: "moveLevelDown",
    value: function moveLevelDown(level) {
      this.moveItemInArray(this.levels, this.levels.indexOf(level), this.levels.indexOf(level) + 1);
    }
  }, {
    key: "moveLevelUp",
    value: function moveLevelUp(level) {
      this.moveItemInArray(this.levels, this.levels.indexOf(level), this.levels.indexOf(level) - 1);
    }
  }, {
    key: "moveItemInArray",
    value: function moveItemInArray(array, from, to) {
      if (to >= array.length || from >= array.length) return;
      if (to < 0 || from < 0) return;
      array.splice(to, 0, array.splice(from, 1)[0]);
    }
  }, {
    key: "getChoiceScore",
    value: function getChoiceScore(criterium, level) {
      var choice = this.getChoice(criterium, level);
      if (choice.hasFixedScore) return choice.fixedScore;
      return Math.round(criterium.weight * level.score) / 100;
    }
  }, {
    key: "getScore",
    value: function getScore() {
      return this._children.reduce(function (accumulator, currentContainer) {
        return accumulator + currentContainer.getScore();
      }, 0);
    }
  }, {
    key: "getAllCriteria",
    value: function getAllCriteria() {
      var container = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this;
      var criteria = [];
      this.getCriteriaRecursive(container, criteria);
      return criteria;
    }
  }, {
    key: "getCriteriaRecursive",
    value: function getCriteriaRecursive(container, criteria) {
      var _this6 = this;

      container.children.filter(function (child) {
        return child instanceof Criterium_Criterium;
      }).forEach(function (criterium) {
        return criteria.push(criterium);
      });
      container.children.filter(function (child) {
        return isContainer(child);
      }).forEach(function (childContainer) {
        return _this6.getCriteriaRecursive(childContainer, criteria);
      });
    }
  }, {
    key: "clusters",
    get: function get() {
      return this.children; //invariant garded at addChild
    }
  }], [{
    key: "fromJSON",
    value: function fromJSON(rubric) {
      var _newRubric$levels;

      var rubricObject;

      if (typeof rubric === 'string') {
        rubricObject = JSON.parse(rubric);
      } else {
        rubricObject = rubric;
      }

      var newRubric = new Rubric(rubricObject.title);

      (_newRubric$levels = newRubric.levels).push.apply(_newRubric$levels, _toConsumableArray(rubricObject.levels.map(function (level) {
        return Level_Level.fromJSON(level);
      })));

      rubricObject.choices.forEach(function (rubricChoiceJsonObject) {
        newRubric.addChoice(Choice_Choice.fromJSON(rubricChoiceJsonObject.choice), rubricChoiceJsonObject.criteriumId, rubricChoiceJsonObject.levelId);
      });
      rubricObject.clusters.map(function (clusterJsonObject) {
        return Cluster_Cluster.fromJSON(clusterJsonObject);
      }).forEach(function (cluster) {
        return newRubric.addChild(cluster);
      });
      newRubric.useScores = rubricObject.useScores;
      return newRubric;
    }
  }]);

  return Rubric;
}(Container_Container);


// CONCATENATED MODULE: ./src/Plugins/ScoreRubric/plugin.ts


function ScoreRubric(Vue, options) {
  Vue.component("ScoreRubricBuilder", Components_ScoreRubricBuilder);

  Vue.prototype.$getRubricFromJSON = function (rubricJSON) {
    return Rubric_Rubric.fromJSON(rubricJSON);
  };
}
// CONCATENATED MODULE: ./src/Plugins/plugins.ts

var plugins = {
  ScoreRubric: ScoreRubric
};
/* harmony default export */ var Plugins_plugins = (plugins);
// CONCATENATED MODULE: D:/Sites/branch/node_modules/@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ var entry_lib = __webpack_exports__["default"] = (Plugins_plugins);



/***/ }),

/***/ "fb1e":
/***/ (function(module, exports, __webpack_require__) {

var redefine = __webpack_require__("8b07");

module.exports = function (target, src, options) {
  for (var key in src) redefine(target, key, src[key], options);
  return target;
};


/***/ }),

/***/ "fcce":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("a3d8");

var min = Math.min;

// `ToLength` abstract operation
// https://tc39.github.io/ecma262/#sec-tolength
module.exports = function (argument) {
  return argument > 0 ? min(toInteger(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
};


/***/ }),

/***/ "fe22":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("0e66");
var isArrayIteratorMethod = __webpack_require__("e5a5");
var toLength = __webpack_require__("fcce");
var bind = __webpack_require__("16e3");
var getIteratorMethod = __webpack_require__("6f0f");
var callWithSafeIterationClosing = __webpack_require__("ecaa");

var Result = function (stopped, result) {
  this.stopped = stopped;
  this.result = result;
};

var iterate = module.exports = function (iterable, fn, that, AS_ENTRIES, IS_ITERATOR) {
  var boundFunction = bind(fn, that, AS_ENTRIES ? 2 : 1);
  var iterator, iterFn, index, length, result, next, step;

  if (IS_ITERATOR) {
    iterator = iterable;
  } else {
    iterFn = getIteratorMethod(iterable);
    if (typeof iterFn != 'function') throw TypeError('Target is not iterable');
    // optimisation for array iterators
    if (isArrayIteratorMethod(iterFn)) {
      for (index = 0, length = toLength(iterable.length); length > index; index++) {
        result = AS_ENTRIES
          ? boundFunction(anObject(step = iterable[index])[0], step[1])
          : boundFunction(iterable[index]);
        if (result && result instanceof Result) return result;
      } return new Result(false);
    }
    iterator = iterFn.call(iterable);
  }

  next = iterator.next;
  while (!(step = next.call(iterator)).done) {
    result = callWithSafeIterationClosing(iterator, boundFunction, step.value, AS_ENTRIES);
    if (typeof result == 'object' && result && result instanceof Result) return result;
  } return new Result(false);
};

iterate.stop = function (result) {
  return new Result(true, result);
};


/***/ }),

/***/ "fe3e":
/***/ (function(module, exports, __webpack_require__) {

var path = __webpack_require__("757d");
var global = __webpack_require__("92f7");

var aFunction = function (variable) {
  return typeof variable == 'function' ? variable : undefined;
};

module.exports = function (namespace, method) {
  return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(global[namespace])
    : path[namespace] && path[namespace][method] || global[namespace] && global[namespace][method];
};


/***/ }),

/***/ "ffee":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var IteratorPrototype = __webpack_require__("6e9e").IteratorPrototype;
var create = __webpack_require__("ad3e");
var createPropertyDescriptor = __webpack_require__("7234");
var setToStringTag = __webpack_require__("9f8d");
var Iterators = __webpack_require__("ae74");

var returnThis = function () { return this; };

module.exports = function (IteratorConstructor, NAME, next) {
  var TO_STRING_TAG = NAME + ' Iterator';
  IteratorConstructor.prototype = create(IteratorPrototype, { next: createPropertyDescriptor(1, next) });
  setToStringTag(IteratorConstructor, TO_STRING_TAG, false, true);
  Iterators[TO_STRING_TAG] = returnThis;
  return IteratorConstructor;
};


/***/ })

/******/ });
});
//# sourceMappingURL=cosnics-vue-plugins.umd.js.map