(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("vue"));
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["cosnics-presence"] = factory(require("vue"));
	else
		root["cosnics-presence"] = factory(root["Vue"]);
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
/******/ 	return __webpack_require__(__webpack_require__.s = "cc54");
/******/ })
/************************************************************************/
/******/ ({

/***/ "000a":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn(data, headers);
  });

  return data;
};


/***/ }),

/***/ "0051":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var IteratorPrototype = __webpack_require__("7abc").IteratorPrototype;
var create = __webpack_require__("2c24");
var createPropertyDescriptor = __webpack_require__("62ca");
var setToStringTag = __webpack_require__("5c65");
var Iterators = __webpack_require__("0279");

var returnThis = function () { return this; };

module.exports = function (IteratorConstructor, NAME, next) {
  var TO_STRING_TAG = NAME + ' Iterator';
  IteratorConstructor.prototype = create(IteratorPrototype, { next: createPropertyDescriptor(1, next) });
  setToStringTag(IteratorConstructor, TO_STRING_TAG, false, true);
  Iterators[TO_STRING_TAG] = returnThis;
  return IteratorConstructor;
};


/***/ }),

/***/ "0209":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var definePropertyModule = __webpack_require__("e6a8");
var createPropertyDescriptor = __webpack_require__("62ca");

module.exports = DESCRIPTORS ? function (object, key, value) {
  return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "0279":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "02c0":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");

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

/***/ "0379":
/***/ (function(module, exports, __webpack_require__) {

var TO_STRING_TAG_SUPPORT = __webpack_require__("8702");
var redefine = __webpack_require__("6a8a");
var toString = __webpack_require__("4a04");

// `Object.prototype.toString` method
// https://tc39.github.io/ecma262/#sec-object.prototype.tostring
if (!TO_STRING_TAG_SUPPORT) {
  redefine(Object.prototype, 'toString', toString, { unsafe: true });
}


/***/ }),

/***/ "04be":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");
var settle = __webpack_require__("aff3");
var buildURL = __webpack_require__("18c2");
var buildFullPath = __webpack_require__("43e9");
var parseHeaders = __webpack_require__("f072");
var isURLSameOrigin = __webpack_require__("b0a1");
var createError = __webpack_require__("9b46");

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password || '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    var fullPath = buildFullPath(config.baseURL, config.url);
    request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    // Listen for ready state
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

      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
      var response = {
        data: responseData,
        status: request.status,
        statusText: request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(resolve, reject, response);

      // Clean up request
      request = null;
    };

    // Handle browser request cancellation (as opposed to a manual cancellation)
    request.onabort = function handleAbort() {
      if (!request) {
        return;
      }

      reject(createError('Request aborted', config, 'ECONNABORTED', request));

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded';
      if (config.timeoutErrorMessage) {
        timeoutErrorMessage = config.timeoutErrorMessage;
      }
      reject(createError(timeoutErrorMessage, config, 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      var cookies = __webpack_require__("15f8");

      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
        cookies.read(config.xsrfCookieName) :
        undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (!utils.isUndefined(config.withCredentials)) {
      request.withCredentials = !!config.withCredentials;
    }

    // Add responseType to request if needed
    if (config.responseType) {
      try {
        request.responseType = config.responseType;
      } catch (e) {
        // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
        // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
        if (config.responseType !== 'json') {
          throw e;
        }
      }
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken) {
      // Handle cancellation
      config.cancelToken.promise.then(function onCanceled(cancel) {
        if (!request) {
          return;
        }

        request.abort();
        reject(cancel);
        // Clean up request
        request = null;
      });
    }

    if (requestData === undefined) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};


/***/ }),

/***/ "05dc":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var propertyIsEnumerableModule = __webpack_require__("0ffc");
var createPropertyDescriptor = __webpack_require__("62ca");
var toIndexedObject = __webpack_require__("2060");
var toPrimitive = __webpack_require__("370b");
var has = __webpack_require__("e414");
var IE8_DOM_DEFINE = __webpack_require__("4bec");

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

/***/ "05e6":
/***/ (function(module, exports, __webpack_require__) {

var defineWellKnownSymbol = __webpack_require__("2afe");

// `Symbol.iterator` well-known symbol
// https://tc39.github.io/ecma262/#sec-symbol.iterator
defineWellKnownSymbol('iterator');


/***/ }),

/***/ "0876":
/***/ (function(module, exports, __webpack_require__) {

var NATIVE_WEAK_MAP = __webpack_require__("731d");
var global = __webpack_require__("b5f1");
var isObject = __webpack_require__("2f69");
var createNonEnumerableProperty = __webpack_require__("0209");
var objectHas = __webpack_require__("e414");
var sharedKey = __webpack_require__("691f");
var hiddenKeys = __webpack_require__("4427");

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

/***/ "091c":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");
var Iterators = __webpack_require__("0279");

var ITERATOR = wellKnownSymbol('iterator');
var ArrayPrototype = Array.prototype;

// check on default Array iterator
module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
};


/***/ }),

/***/ "0949":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");

module.exports = global;


/***/ }),

/***/ "0e29":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__("c097");

/*global toString:true*/

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return toString.call(val) === '[object Array]';
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is a Buffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
  return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
    && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return (typeof FormData !== 'undefined') && (val instanceof FormData);
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.replace(/^\s*/, '').replace(/\s*$/, '');
}

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
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
                                           navigator.product === 'NativeScript' ||
                                           navigator.product === 'NS')) {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

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
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

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
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (typeof result[key] === 'object' && typeof val === 'object') {
      result[key] = merge(result[key], val);
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Function equal to merge with the difference being that no reference
 * to original objects is kept.
 *
 * @see merge
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function deepMerge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (typeof result[key] === 'object' && typeof val === 'object') {
      result[key] = deepMerge(result[key], val);
    } else if (typeof val === 'object') {
      result[key] = deepMerge({}, val);
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  deepMerge: deepMerge,
  extend: extend,
  trim: trim
};


/***/ }),

/***/ "0e60":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var getBuiltIn = __webpack_require__("f914");
var definePropertyModule = __webpack_require__("e6a8");
var wellKnownSymbol = __webpack_require__("4736");
var DESCRIPTORS = __webpack_require__("70b9");

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

/***/ "0ffa":
/***/ (function(module, exports, __webpack_require__) {

var toIndexedObject = __webpack_require__("2060");
var nativeGetOwnPropertyNames = __webpack_require__("9161").f;

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

/***/ "0ffc":
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

/***/ "1169":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"total":"Total","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","export":"Export","legend":"Legend","students-not-in-course":"Students not in course","without-status":"Without status","checkout-mode":"Checkout mode","refresh":"Refresh","changes-filters":"You have made changes so that the shown results possibly no longer reflect the chosen filter criteria. Choose different criteria or click refresh to remedy.","remove-period":"Remove period","show-periods":"Show all periods","more":"More"},"nl":{"total":"Totaal","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","export":"Exporteer","legend":"Legende","students-not-in-course":"Studenten niet in cursus","without-status":"Zonder status","checkout-mode":"Uitcheckmodus","refresh":"Vernieuwen","changes-filters":"Je hebt een wijziging gedaan waardoor de getoonde resultaten mogelijk niet meer overeenkomen met de gekozen filtercriteria. Kies andere criteria of klik op Vernieuwen om dit op te lossen.","remove-period":"Verwijder periode","show-periods":"Toon alle perioden","more":"Meer"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "1172":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");

var MATCH = wellKnownSymbol('match');

module.exports = function (METHOD_NAME) {
  var regexp = /./;
  try {
    '/./'[METHOD_NAME](regexp);
  } catch (e) {
    try {
      regexp[MATCH] = false;
      return '/./'[METHOD_NAME](regexp);
    } catch (f) { /* empty */ }
  } return false;
};


/***/ }),

/***/ "15f8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
    (function standardBrowserEnv() {
      return {
        write: function write(name, value, expires, path, domain, secure) {
          var cookie = [];
          cookie.push(name + '=' + encodeURIComponent(value));

          if (utils.isNumber(expires)) {
            cookie.push('expires=' + new Date(expires).toGMTString());
          }

          if (utils.isString(path)) {
            cookie.push('path=' + path);
          }

          if (utils.isString(domain)) {
            cookie.push('domain=' + domain);
          }

          if (secure === true) {
            cookie.push('secure');
          }

          document.cookie = cookie.join('; ');
        },

        read: function read(name) {
          var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
          return (match ? decodeURIComponent(match[3]) : null);
        },

        remove: function remove(name) {
          this.write(name, '', Date.now() - 86400000);
        }
      };
    })() :

  // Non standard browser env (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return {
        write: function write() {},
        read: function read() { return null; },
        remove: function remove() {}
      };
    })()
);


/***/ }),

/***/ "164f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

function InterceptorManager() {
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
InterceptorManager.prototype.use = function use(fulfilled, rejected) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),

/***/ "17bb":
/***/ (function(module, exports, __webpack_require__) {

var getBuiltIn = __webpack_require__("f914");
var getOwnPropertyNamesModule = __webpack_require__("9161");
var getOwnPropertySymbolsModule = __webpack_require__("5dc3");
var anObject = __webpack_require__("6161");

// all object keys, includes non-enumerable and symbols
module.exports = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
  var keys = getOwnPropertyNamesModule.f(anObject(it));
  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
  return getOwnPropertySymbols ? keys.concat(getOwnPropertySymbols(it)) : keys;
};


/***/ }),

/***/ "18c2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

function encode(val) {
  return encodeURIComponent(val).
    replace(/%40/gi, '@').
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
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      } else {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    var hashmarkIndex = url.indexOf('#');
    if (hashmarkIndex !== -1) {
      url = url.slice(0, hashmarkIndex);
    }

    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),

/***/ "1a6b":
/***/ (function(module, exports) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

!(function(global) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  var inModule = typeof module === "object";
  var runtime = global.regeneratorRuntime;
  if (runtime) {
    if (inModule) {
      // If regeneratorRuntime is defined globally and we're in a module,
      // make the exports object identical to regeneratorRuntime.
      module.exports = runtime;
    }
    // Don't bother evaluating the rest of this file if the runtime was
    // already defined globally.
    return;
  }

  // Define the runtime globally (as expected by generated code) as either
  // module.exports (if we're in a module) or a new, empty object.
  runtime = global.regeneratorRuntime = inModule ? module.exports : {};

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  runtime.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  IteratorPrototype[iteratorSymbol] = function () {
    return this;
  };

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
  GeneratorFunctionPrototype.constructor = GeneratorFunction;
  GeneratorFunctionPrototype[toStringTagSymbol] =
    GeneratorFunction.displayName = "GeneratorFunction";

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      prototype[method] = function(arg) {
        return this._invoke(method, arg);
      };
    });
  }

  runtime.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  runtime.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      if (!(toStringTagSymbol in genFun)) {
        genFun[toStringTagSymbol] = "GeneratorFunction";
      }
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  runtime.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return Promise.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return Promise.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration. If the Promise is rejected, however, the
          // result for this iteration will be rejected with the same
          // reason. Note that rejections of yielded Promises are not
          // thrown back into the generator function, as is the case
          // when an awaited Promise is rejected. This difference in
          // behavior between yield and await is important, because it
          // allows the consumer to decide what to do with the yielded
          // rejection (swallow it and continue, manually .throw it back
          // into the generator, abandon iteration, whatever). With
          // await, by contrast, there is no opportunity to examine the
          // rejection reason outside the generator function, so the
          // only option is to throw it from the await expression, and
          // let the generator function handle the exception.
          result.value = unwrapped;
          resolve(result);
        }, reject);
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new Promise(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  AsyncIterator.prototype[asyncIteratorSymbol] = function () {
    return this;
  };
  runtime.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  runtime.async = function(innerFn, outerFn, self, tryLocsList) {
    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList)
    );

    return runtime.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        if (delegate.iterator.return) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  Gp[toStringTagSymbol] = "Generator";

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  Gp[iteratorSymbol] = function() {
    return this;
  };

  Gp.toString = function() {
    return "[object Generator]";
  };

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  runtime.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  runtime.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };
})(
  // In sloppy mode, unbound `this` refers to the global object, fallback to
  // Function constructor if we're in global strict mode. That is sadly a form
  // of indirect eval which violates Content Security Policy.
  (function() { return this })() || Function("return this")()
);


/***/ }),

/***/ "1b63":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("e414");
var toObject = __webpack_require__("64f1");
var sharedKey = __webpack_require__("691f");
var CORRECT_PROTOTYPE_GETTER = __webpack_require__("efa9");

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

/***/ "1ca3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("2b85");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "1ce3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var toAbsoluteIndex = __webpack_require__("dd93");
var toInteger = __webpack_require__("4ff6");
var toLength = __webpack_require__("7cf1");
var toObject = __webpack_require__("64f1");
var arraySpeciesCreate = __webpack_require__("62c9");
var createProperty = __webpack_require__("c46f");
var arrayMethodHasSpeciesSupport = __webpack_require__("7aeb");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');
var USES_TO_LENGTH = arrayMethodUsesToLength('splice', { ACCESSORS: true, 0: 0, 1: 2 });

var max = Math.max;
var min = Math.min;
var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF;
var MAXIMUM_ALLOWED_LENGTH_EXCEEDED = 'Maximum allowed length exceeded';

// `Array.prototype.splice` method
// https://tc39.github.io/ecma262/#sec-array.prototype.splice
// with adding support of @@species
$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT || !USES_TO_LENGTH }, {
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

/***/ "1d07":
/***/ (function(module, exports, __webpack_require__) {

var getBuiltIn = __webpack_require__("f914");

module.exports = getBuiltIn('document', 'documentElement');


/***/ }),

/***/ "1e4c":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("8af9");
var Iterators = __webpack_require__("0279");
var wellKnownSymbol = __webpack_require__("4736");

var ITERATOR = wellKnownSymbol('iterator');

module.exports = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),

/***/ "2060":
/***/ (function(module, exports, __webpack_require__) {

// toObject with fallback for non-array-like ES3 strings
var IndexedObject = __webpack_require__("2be1");
var requireObjectCoercible = __webpack_require__("b2c6");

module.exports = function (it) {
  return IndexedObject(requireObjectCoercible(it));
};


/***/ }),

/***/ "2103":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"error-NoTitleGiven":"You haven\u0027t given a title for the following status:","error-TitleUpdateForbidden":"The title of the status \u0027{title}\u0027 can no longer be changed.","error-InvalidType":"The status \u0027{title}\u0027 has a wrong type set.","error-PresenceStatusMissing":"The status \u0027{title}\u0027 can no longer be removed.","error-InvalidAlias":"The status with title \u0027{title}\u0027 has a wrong mapping set.","error-AliasUpdateForbidden":"The mapping of the status \u0027{title}\u0027 can no longer be changed.","error-NoCodeGiven":"You haven\u0027t given a label for the status \u0027{title}\u0027.","error-NoColorGiven":"You haven\u0027t given a color for the status \u0027{title}\u0027.","error-InvalidColor":"You have given the status \u0027{title}\u0027 an invalid color.","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","changes-not-saved":"Your changes have not been saved."},"nl":{"error-NoTitleGiven":"Je hebt geen titel opgegeven voor de volgende status:","error-TitleUpdateForbidden":"De titel van de status \u0027{title}\u0027 kan niet meer gewijzigd worden.","error-InvalidType":"De status \u0027{title}\u0027 status heeft een verkeerd type.","error-PresenceStatusMissing":"De status \u0027{title}\u0027 kan niet meer gewist worden.","error-InvalidAlias":"De status \u0027{title}\u0027 heeft een verkeerde mapping.","error-AliasUpdateForbidden":"De mapping van de status \u0027{title}\u0027 kan niet meer gewijzigd worden.","error-NoCodeGiven":"Je hebt geen label opgegeven voor de status \u0027{title}\u0027.","error-NoColorGiven":"Je hebt geen kleur opgegeven voor de status \u0027{title}\u0027.","error-InvalidColor":"Je hebt de status \u0027{title}\u0027 een verkeerde kleur toegewezen.","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","changes-not-saved":"Je wijzigingen werden niet opgeslagen."}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "21be":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var redefine = __webpack_require__("6a8a");
var anObject = __webpack_require__("6161");
var fails = __webpack_require__("7104");
var flags = __webpack_require__("89bd");

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

/***/ "236c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var fails = __webpack_require__("7104");
var isArray = __webpack_require__("8d52");
var isObject = __webpack_require__("2f69");
var toObject = __webpack_require__("64f1");
var toLength = __webpack_require__("7cf1");
var createProperty = __webpack_require__("c46f");
var arraySpeciesCreate = __webpack_require__("62c9");
var arrayMethodHasSpeciesSupport = __webpack_require__("7aeb");
var wellKnownSymbol = __webpack_require__("4736");
var V8_VERSION = __webpack_require__("39e8");

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

/***/ "250b":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var DESCRIPTORS = __webpack_require__("70b9");
var global = __webpack_require__("b5f1");
var isForced = __webpack_require__("02c0");
var redefine = __webpack_require__("6a8a");
var has = __webpack_require__("e414");
var classof = __webpack_require__("3ab7");
var inheritIfRequired = __webpack_require__("2ab9");
var toPrimitive = __webpack_require__("370b");
var fails = __webpack_require__("7104");
var create = __webpack_require__("2c24");
var getOwnPropertyNames = __webpack_require__("9161").f;
var getOwnPropertyDescriptor = __webpack_require__("05dc").f;
var defineProperty = __webpack_require__("e6a8").f;
var trim = __webpack_require__("7c64").trim;

var NUMBER = 'Number';
var NativeNumber = global[NUMBER];
var NumberPrototype = NativeNumber.prototype;

// Opera ~12 has broken Object#toString
var BROKEN_CLASSOF = classof(create(NumberPrototype)) == NUMBER;

// `ToNumber` abstract operation
// https://tc39.github.io/ecma262/#sec-tonumber
var toNumber = function (argument) {
  var it = toPrimitive(argument, false);
  var first, third, radix, maxCode, digits, length, index, code;
  if (typeof it == 'string' && it.length > 2) {
    it = trim(it);
    first = it.charCodeAt(0);
    if (first === 43 || first === 45) {
      third = it.charCodeAt(2);
      if (third === 88 || third === 120) return NaN; // Number('+0x1') should be NaN, old V8 fix
    } else if (first === 48) {
      switch (it.charCodeAt(1)) {
        case 66: case 98: radix = 2; maxCode = 49; break; // fast equal of /^0b[01]+$/i
        case 79: case 111: radix = 8; maxCode = 55; break; // fast equal of /^0o[0-7]+$/i
        default: return +it;
      }
      digits = it.slice(2);
      length = digits.length;
      for (index = 0; index < length; index++) {
        code = digits.charCodeAt(index);
        // parseInt parses a string to a first unavailable symbol
        // but ToNumber should return NaN if a string contains unavailable symbols
        if (code < 48 || code > maxCode) return NaN;
      } return parseInt(digits, radix);
    }
  } return +it;
};

// `Number` constructor
// https://tc39.github.io/ecma262/#sec-number-constructor
if (isForced(NUMBER, !NativeNumber(' 0o1') || !NativeNumber('0b1') || NativeNumber('+0x1'))) {
  var NumberWrapper = function Number(value) {
    var it = arguments.length < 1 ? 0 : value;
    var dummy = this;
    return dummy instanceof NumberWrapper
      // check on 1..constructor(foo) case
      && (BROKEN_CLASSOF ? fails(function () { NumberPrototype.valueOf.call(dummy); }) : classof(dummy) != NUMBER)
        ? inheritIfRequired(new NativeNumber(toNumber(it)), dummy, NumberWrapper) : toNumber(it);
  };
  for (var keys = DESCRIPTORS ? getOwnPropertyNames(NativeNumber) : (
    // ES3:
    'MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,' +
    // ES2015 (in case, if modules with ES2015 Number statics required before):
    'EPSILON,isFinite,isInteger,isNaN,isSafeInteger,MAX_SAFE_INTEGER,' +
    'MIN_SAFE_INTEGER,parseFloat,parseInt,isInteger'
  ).split(','), j = 0, key; keys.length > j; j++) {
    if (has(NativeNumber, key = keys[j]) && !has(NumberWrapper, key)) {
      defineProperty(NumberWrapper, key, getOwnPropertyDescriptor(NativeNumber, key));
    }
  }
  NumberWrapper.prototype = NumberPrototype;
  NumberPrototype.constructor = NumberWrapper;
  redefine(global, NUMBER, NumberWrapper);
}


/***/ }),

/***/ "287c":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var setPrototypeOf = __webpack_require__("a8a2");

// `Object.setPrototypeOf` method
// https://tc39.github.io/ecma262/#sec-object.setprototypeof
$({ target: 'Object', stat: true }, {
  setPrototypeOf: setPrototypeOf
});


/***/ }),

/***/ "2ab9":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("2f69");
var setPrototypeOf = __webpack_require__("a8a2");

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

/***/ "2afe":
/***/ (function(module, exports, __webpack_require__) {

var path = __webpack_require__("0949");
var has = __webpack_require__("e414");
var wrappedWellKnownSymbolModule = __webpack_require__("49ae");
var defineProperty = __webpack_require__("e6a8").f;

module.exports = function (NAME) {
  var Symbol = path.Symbol || (path.Symbol = {});
  if (!has(Symbol, NAME)) defineProperty(Symbol, NAME, {
    value: wrappedWellKnownSymbolModule.f(NAME)
  });
};


/***/ }),

/***/ "2b85":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"move-up":"Move up","move-down":"Move down","remove":"Remove"},"nl":{"move-up":"Verplaats naar boven","move-down":"Verplaats naar beneden","remove":"Verwijder"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "2be1":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");
var classof = __webpack_require__("3ab7");

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

/***/ "2c24":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("6161");
var defineProperties = __webpack_require__("2dc3");
var enumBugKeys = __webpack_require__("b337");
var hiddenKeys = __webpack_require__("4427");
var html = __webpack_require__("1d07");
var documentCreateElement = __webpack_require__("893b");
var sharedKey = __webpack_require__("691f");

var GT = '>';
var LT = '<';
var PROTOTYPE = 'prototype';
var SCRIPT = 'script';
var IE_PROTO = sharedKey('IE_PROTO');

var EmptyConstructor = function () { /* empty */ };

var scriptTag = function (content) {
  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
};

// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
var NullProtoObjectViaActiveX = function (activeXDocument) {
  activeXDocument.write(scriptTag(''));
  activeXDocument.close();
  var temp = activeXDocument.parentWindow.Object;
  activeXDocument = null; // avoid memory leak
  return temp;
};

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var NullProtoObjectViaIFrame = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = documentCreateElement('iframe');
  var JS = 'java' + SCRIPT + ':';
  var iframeDocument;
  iframe.style.display = 'none';
  html.appendChild(iframe);
  // https://github.com/zloirock/core-js/issues/475
  iframe.src = String(JS);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(scriptTag('document.F=Object'));
  iframeDocument.close();
  return iframeDocument.F;
};

// Check for document.domain and active x support
// No need to use active x approach when document.domain is not set
// see https://github.com/es-shims/es5-shim/issues/150
// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
// avoid IE GC bug
var activeXDocument;
var NullProtoObject = function () {
  try {
    /* global ActiveXObject */
    activeXDocument = document.domain && new ActiveXObject('htmlfile');
  } catch (error) { /* ignore */ }
  NullProtoObject = activeXDocument ? NullProtoObjectViaActiveX(activeXDocument) : NullProtoObjectViaIFrame();
  var length = enumBugKeys.length;
  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
  return NullProtoObject();
};

hiddenKeys[IE_PROTO] = true;

// `Object.create` method
// https://tc39.github.io/ecma262/#sec-object.create
module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    EmptyConstructor[PROTOTYPE] = anObject(O);
    result = new EmptyConstructor();
    EmptyConstructor[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = NullProtoObject();
  return Properties === undefined ? result : defineProperties(result, Properties);
};


/***/ }),

/***/ "2d4e":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var getOwnPropertyDescriptor = __webpack_require__("05dc").f;
var classof = __webpack_require__("3ab7");
var macrotask = __webpack_require__("371a").set;
var IS_IOS = __webpack_require__("edef");

var MutationObserver = global.MutationObserver || global.WebKitMutationObserver;
var process = global.process;
var Promise = global.Promise;
var IS_NODE = classof(process) == 'process';
// Node.js 11 shows ExperimentalWarning on getting `queueMicrotask`
var queueMicrotaskDescriptor = getOwnPropertyDescriptor(global, 'queueMicrotask');
var queueMicrotask = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;

var flush, head, last, notify, toggle, node, promise, then;

// modern engines have queueMicrotask method
if (!queueMicrotask) {
  flush = function () {
    var parent, fn;
    if (IS_NODE && (parent = process.domain)) parent.exit();
    while (head) {
      fn = head.fn;
      head = head.next;
      try {
        fn();
      } catch (error) {
        if (head) notify();
        else last = undefined;
        throw error;
      }
    } last = undefined;
    if (parent) parent.enter();
  };

  // Node.js
  if (IS_NODE) {
    notify = function () {
      process.nextTick(flush);
    };
  // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
  } else if (MutationObserver && !IS_IOS) {
    toggle = true;
    node = document.createTextNode('');
    new MutationObserver(flush).observe(node, { characterData: true });
    notify = function () {
      node.data = toggle = !toggle;
    };
  // environments with maybe non-completely correct, but existent Promise
  } else if (Promise && Promise.resolve) {
    // Promise.resolve without an argument throws an error in LG WebOS 2
    promise = Promise.resolve(undefined);
    then = promise.then;
    notify = function () {
      then.call(promise, flush);
    };
  // for other environments - macrotask based on:
  // - setImmediate
  // - MessageChannel
  // - window.postMessag
  // - onreadystatechange
  // - setTimeout
  } else {
    notify = function () {
      // strange IE + webpack dev server bug - use .call(global)
      macrotask.call(global, flush);
    };
  }
}

module.exports = queueMicrotask || function (fn) {
  var task = { fn: fn, next: undefined };
  if (last) last.next = task;
  if (!head) {
    head = task;
    notify();
  } last = task;
};


/***/ }),

/***/ "2dc3":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var definePropertyModule = __webpack_require__("e6a8");
var anObject = __webpack_require__("6161");
var objectKeys = __webpack_require__("ce57");

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

/***/ "2e43":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NewStatusControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("74f2");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NewStatusControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NewStatusControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NewStatusControls_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "2f4a":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");

module.exports = function (a, b) {
  var console = global.console;
  if (console && console.error) {
    arguments.length === 1 ? console.error(a) : console.error(a, b);
  }
};


/***/ }),

/***/ "2f69":
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ "326d":
/***/ (function(module, exports, __webpack_require__) {

var aFunction = __webpack_require__("6373");

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

/***/ "328d":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var DESCRIPTORS = __webpack_require__("70b9");
var objectDefinePropertyModile = __webpack_require__("e6a8");

// `Object.defineProperty` method
// https://tc39.github.io/ecma262/#sec-object.defineproperty
$({ target: 'Object', stat: true, forced: !DESCRIPTORS, sham: !DESCRIPTORS }, {
  defineProperty: objectDefinePropertyModile.f
});


/***/ }),

/***/ "33a3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


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
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ }),

/***/ "3558":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "370b":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("2f69");

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

/***/ "371a":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var fails = __webpack_require__("7104");
var classof = __webpack_require__("3ab7");
var bind = __webpack_require__("326d");
var html = __webpack_require__("1d07");
var createElement = __webpack_require__("893b");
var IS_IOS = __webpack_require__("edef");

var location = global.location;
var set = global.setImmediate;
var clear = global.clearImmediate;
var process = global.process;
var MessageChannel = global.MessageChannel;
var Dispatch = global.Dispatch;
var counter = 0;
var queue = {};
var ONREADYSTATECHANGE = 'onreadystatechange';
var defer, channel, port;

var run = function (id) {
  // eslint-disable-next-line no-prototype-builtins
  if (queue.hasOwnProperty(id)) {
    var fn = queue[id];
    delete queue[id];
    fn();
  }
};

var runner = function (id) {
  return function () {
    run(id);
  };
};

var listener = function (event) {
  run(event.data);
};

var post = function (id) {
  // old engines have not location.origin
  global.postMessage(id + '', location.protocol + '//' + location.host);
};

// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
if (!set || !clear) {
  set = function setImmediate(fn) {
    var args = [];
    var i = 1;
    while (arguments.length > i) args.push(arguments[i++]);
    queue[++counter] = function () {
      // eslint-disable-next-line no-new-func
      (typeof fn == 'function' ? fn : Function(fn)).apply(undefined, args);
    };
    defer(counter);
    return counter;
  };
  clear = function clearImmediate(id) {
    delete queue[id];
  };
  // Node.js 0.8-
  if (classof(process) == 'process') {
    defer = function (id) {
      process.nextTick(runner(id));
    };
  // Sphere (JS game engine) Dispatch API
  } else if (Dispatch && Dispatch.now) {
    defer = function (id) {
      Dispatch.now(runner(id));
    };
  // Browsers with MessageChannel, includes WebWorkers
  // except iOS - https://github.com/zloirock/core-js/issues/624
  } else if (MessageChannel && !IS_IOS) {
    channel = new MessageChannel();
    port = channel.port2;
    channel.port1.onmessage = listener;
    defer = bind(port.postMessage, port, 1);
  // Browsers with postMessage, skip WebWorkers
  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
  } else if (global.addEventListener && typeof postMessage == 'function' && !global.importScripts && !fails(post)) {
    defer = post;
    global.addEventListener('message', listener, false);
  // IE8-
  } else if (ONREADYSTATECHANGE in createElement('script')) {
    defer = function (id) {
      html.appendChild(createElement('script'))[ONREADYSTATECHANGE] = function () {
        html.removeChild(this);
        run(id);
      };
    };
  // Rest old browsers
  } else {
    defer = function (id) {
      setTimeout(runner(id), 0);
    };
  }
}

module.exports = {
  set: set,
  clear: clear
};


/***/ }),

/***/ "39e8":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var userAgent = __webpack_require__("e4e7");

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

/***/ "3ab7":
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),

/***/ "3b5d":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("e414");
var toIndexedObject = __webpack_require__("2060");
var indexOf = __webpack_require__("f7f3").indexOf;
var hiddenKeys = __webpack_require__("4427");

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

/***/ "3bd5":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var fails = __webpack_require__("7104");
var has = __webpack_require__("e414");

var defineProperty = Object.defineProperty;
var cache = {};

var thrower = function (it) { throw it; };

module.exports = function (METHOD_NAME, options) {
  if (has(cache, METHOD_NAME)) return cache[METHOD_NAME];
  if (!options) options = {};
  var method = [][METHOD_NAME];
  var ACCESSORS = has(options, 'ACCESSORS') ? options.ACCESSORS : false;
  var argument0 = has(options, 0) ? options[0] : thrower;
  var argument1 = has(options, 1) ? options[1] : undefined;

  return cache[METHOD_NAME] = !!method && !fails(function () {
    if (ACCESSORS && !DESCRIPTORS) return true;
    var O = { length: -1 };

    if (ACCESSORS) defineProperty(O, 1, { enumerable: true, get: thrower });
    else O[1] = 1;

    method.call(O, argument0, argument1);
  });
};


/***/ }),

/***/ "3cbf":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_style_index_0_id_b35f4f70_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("3fc1");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_style_index_0_id_b35f4f70_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_style_index_0_id_b35f4f70_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_style_index_0_id_b35f4f70_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "3f78":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var objectKeys = __webpack_require__("ce57");
var toIndexedObject = __webpack_require__("2060");
var propertyIsEnumerable = __webpack_require__("0ffc").f;

// `Object.{ entries, values }` methods implementation
var createMethod = function (TO_ENTRIES) {
  return function (it) {
    var O = toIndexedObject(it);
    var keys = objectKeys(O);
    var length = keys.length;
    var i = 0;
    var result = [];
    var key;
    while (length > i) {
      key = keys[i++];
      if (!DESCRIPTORS || propertyIsEnumerable.call(O, key)) {
        result.push(TO_ENTRIES ? [key, O[key]] : O[key]);
      }
    }
    return result;
  };
};

module.exports = {
  // `Object.entries` method
  // https://tc39.github.io/ecma262/#sec-object.entries
  entries: createMethod(true),
  // `Object.values` method
  // https://tc39.github.io/ecma262/#sec-object.values
  values: createMethod(false)
};


/***/ }),

/***/ "3fc1":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "4047":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("6161");

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

/***/ "4056":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
const lower_bound_1 = __webpack_require__("55c0");
class PriorityQueue {
    constructor() {
        Object.defineProperty(this, "_queue", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: []
        });
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
        return item && item.run;
    }
    filter(options) {
        return this._queue.filter(element => element.priority === options.priority).map(element => element.run);
    }
    get size() {
        return this._queue.length;
    }
}
exports.default = PriorityQueue;


/***/ }),

/***/ "40d3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var isArray = __webpack_require__("8d52");

var nativeReverse = [].reverse;
var test = [1, 2];

// `Array.prototype.reverse` method
// https://tc39.github.io/ecma262/#sec-array.prototype.reverse
// fix for Safari 12.0 bug
// https://bugs.webkit.org/show_bug.cgi?id=188794
$({ target: 'Array', proto: true, forced: String(test) === String(test.reverse()) }, {
  reverse: function reverse() {
    // eslint-disable-next-line no-self-assign
    if (isArray(this)) this.length = this.length;
    return nativeReverse.call(this);
  }
});


/***/ }),

/***/ "43e9":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var isAbsoluteURL = __webpack_require__("bd27");
var combineURLs = __webpack_require__("d821");

/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 * @returns {string} The combined full path
 */
module.exports = function buildFullPath(baseURL, requestedURL) {
  if (baseURL && !isAbsoluteURL(requestedURL)) {
    return combineURLs(baseURL, requestedURL);
  }
  return requestedURL;
};


/***/ }),

/***/ "4427":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "4736":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var shared = __webpack_require__("943e");
var has = __webpack_require__("e414");
var uid = __webpack_require__("5be7");
var NATIVE_SYMBOL = __webpack_require__("49cf");
var USE_SYMBOL_AS_UID = __webpack_require__("7f0c");

var WellKnownSymbolsStore = shared('wks');
var Symbol = global.Symbol;
var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol : Symbol && Symbol.withoutSetter || uid;

module.exports = function (name) {
  if (!has(WellKnownSymbolsStore, name)) {
    if (NATIVE_SYMBOL && has(Symbol, name)) WellKnownSymbolsStore[name] = Symbol[name];
    else WellKnownSymbolsStore[name] = createWellKnownSymbol('Symbol.' + name);
  } return WellKnownSymbolsStore[name];
};


/***/ }),

/***/ "49ae":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");

exports.f = wellKnownSymbol;


/***/ }),

/***/ "49b6":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var assign = __webpack_require__("ffbc");

// `Object.assign` method
// https://tc39.github.io/ecma262/#sec-object.assign
$({ target: 'Object', stat: true, forced: Object.assign !== assign }, {
  assign: assign
});


/***/ }),

/***/ "49cf":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");

module.exports = !!Object.getOwnPropertySymbols && !fails(function () {
  // Chrome 38 Symbol has incorrect toString conversion
  // eslint-disable-next-line no-undef
  return !String(Symbol());
});


/***/ }),

/***/ "4a04":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var TO_STRING_TAG_SUPPORT = __webpack_require__("8702");
var classof = __webpack_require__("8af9");

// `Object.prototype.toString` method implementation
// https://tc39.github.io/ecma262/#sec-object.prototype.tostring
module.exports = TO_STRING_TAG_SUPPORT ? {}.toString : function toString() {
  return '[object ' + classof(this) + ']';
};


/***/ }),

/***/ "4a1c":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var getOwnPropertyDescriptor = __webpack_require__("05dc").f;
var createNonEnumerableProperty = __webpack_require__("0209");
var redefine = __webpack_require__("6a8a");
var setGlobal = __webpack_require__("a134");
var copyConstructorProperties = __webpack_require__("6d94");
var isForced = __webpack_require__("02c0");

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

/***/ "4bec":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var fails = __webpack_require__("7104");
var createElement = __webpack_require__("893b");

// Thank's IE8 for his funny defineProperty
module.exports = !DESCRIPTORS && !fails(function () {
  return Object.defineProperty(createElement('div'), 'a', {
    get: function () { return 7; }
  }).a != 7;
});


/***/ }),

/***/ "4dfb":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8270");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "4fee":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_6_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("e681");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_6_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_6_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_6_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "4ff6":
/***/ (function(module, exports) {

var ceil = Math.ceil;
var floor = Math.floor;

// `ToInteger` abstract operation
// https://tc39.github.io/ecma262/#sec-tointeger
module.exports = function (argument) {
  return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
};


/***/ }),

/***/ "5091":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8938");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "5270":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var DOMIterables = __webpack_require__("68b7");
var forEach = __webpack_require__("8239");
var createNonEnumerableProperty = __webpack_require__("0209");

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

/***/ "529c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $find = __webpack_require__("ec68").find;
var addToUnscopables = __webpack_require__("9b73");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var FIND = 'find';
var SKIPS_HOLES = true;

var USES_TO_LENGTH = arrayMethodUsesToLength(FIND);

// Shouldn't skip holes
if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

// `Array.prototype.find` method
// https://tc39.github.io/ecma262/#sec-array.prototype.find
$({ target: 'Array', proto: true, forced: SKIPS_HOLES || !USES_TO_LENGTH }, {
  find: function find(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});

// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
addToUnscopables(FIND);


/***/ }),

/***/ "52b3":
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

/***/ "5301":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var toIndexedObject = __webpack_require__("2060");
var addToUnscopables = __webpack_require__("9b73");
var Iterators = __webpack_require__("0279");
var InternalStateModule = __webpack_require__("0876");
var defineIterator = __webpack_require__("7935");

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

/***/ "55c0":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
// Port of lower_bound from http://en.cppreference.com/w/cpp/algorithm/lower_bound
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
exports.default = lowerBound;


/***/ }),

/***/ "5792":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SaveControl_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("58db");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SaveControl_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SaveControl_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SaveControl_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "57bb":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("6161");
var isObject = __webpack_require__("2f69");
var newPromiseCapability = __webpack_require__("c517");

module.exports = function (C, x) {
  anObject(C);
  if (isObject(x) && x.constructor === C) return x;
  var promiseCapability = newPromiseCapability.f(C);
  var resolve = promiseCapability.resolve;
  resolve(x);
  return promiseCapability.promise;
};


/***/ }),

/***/ "5810":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "58db":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"save":"Save"},"nl":{"save":"Opslaan"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "5be7":
/***/ (function(module, exports) {

var id = 0;
var postfix = Math.random();

module.exports = function (key) {
  return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id + postfix).toString(36);
};


/***/ }),

/***/ "5c65":
/***/ (function(module, exports, __webpack_require__) {

var defineProperty = __webpack_require__("e6a8").f;
var has = __webpack_require__("e414");
var wellKnownSymbol = __webpack_require__("4736");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');

module.exports = function (it, TAG, STATIC) {
  if (it && !has(it = STATIC ? it : it.prototype, TO_STRING_TAG)) {
    defineProperty(it, TO_STRING_TAG, { configurable: true, value: TAG });
  }
};


/***/ }),

/***/ "5dc3":
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ "5eaa":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "6098":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("7318");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "615c":
/***/ (function(module, exports, __webpack_require__) {

var isRegExp = __webpack_require__("7350");

module.exports = function (it) {
  if (isRegExp(it)) {
    throw TypeError("The method doesn't accept regular expressions");
  } return it;
};


/***/ }),

/***/ "6161":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("2f69");

module.exports = function (it) {
  if (!isObject(it)) {
    throw TypeError(String(it) + ' is not an object');
  } return it;
};


/***/ }),

/***/ "62c8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $indexOf = __webpack_require__("f7f3").indexOf;
var arrayMethodIsStrict = __webpack_require__("9fac");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var nativeIndexOf = [].indexOf;

var NEGATIVE_ZERO = !!nativeIndexOf && 1 / [1].indexOf(1, -0) < 0;
var STRICT_METHOD = arrayMethodIsStrict('indexOf');
var USES_TO_LENGTH = arrayMethodUsesToLength('indexOf', { ACCESSORS: true, 1: 0 });

// `Array.prototype.indexOf` method
// https://tc39.github.io/ecma262/#sec-array.prototype.indexof
$({ target: 'Array', proto: true, forced: NEGATIVE_ZERO || !STRICT_METHOD || !USES_TO_LENGTH }, {
  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
    return NEGATIVE_ZERO
      // convert -0 to +0
      ? nativeIndexOf.apply(this, arguments) || 0
      : $indexOf(this, searchElement, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "62c9":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("2f69");
var isArray = __webpack_require__("8d52");
var wellKnownSymbol = __webpack_require__("4736");

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

/***/ "62ca":
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

/***/ "6373":
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') {
    throw TypeError(String(it) + ' is not a function');
  } return it;
};


/***/ }),

/***/ "64f1":
/***/ (function(module, exports, __webpack_require__) {

var requireObjectCoercible = __webpack_require__("b2c6");

// `ToObject` abstract operation
// https://tc39.github.io/ecma262/#sec-toobject
module.exports = function (argument) {
  return Object(requireObjectCoercible(argument));
};


/***/ }),

/***/ "68b7":
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

/***/ "68d3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_1_id_642e4e7a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("5eaa");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_1_id_642e4e7a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_1_id_642e4e7a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_1_id_642e4e7a_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "691f":
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__("943e");
var uid = __webpack_require__("5be7");

var keys = shared('keys');

module.exports = function (key) {
  return keys[key] || (keys[key] = uid(key));
};


/***/ }),

/***/ "6a8a":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var createNonEnumerableProperty = __webpack_require__("0209");
var has = __webpack_require__("e414");
var setGlobal = __webpack_require__("a134");
var inspectSource = __webpack_require__("b2be");
var InternalStateModule = __webpack_require__("0876");

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

/***/ "6bd9":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var bind = __webpack_require__("72a2");

// `Function.prototype.bind` method
// https://tc39.github.io/ecma262/#sec-function.prototype.bind
$({ target: 'Function', proto: true }, {
  bind: bind
});


/***/ }),

/***/ "6d94":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("e414");
var ownKeys = __webpack_require__("17bb");
var getOwnPropertyDescriptorModule = __webpack_require__("05dc");
var definePropertyModule = __webpack_require__("e6a8");

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

/***/ "6faf":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("2103");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "70b9":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");

// Thank's IE8 for his funny defineProperty
module.exports = !fails(function () {
  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
});


/***/ }),

/***/ "7104":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (error) {
    return true;
  }
};


/***/ }),

/***/ "72a2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var aFunction = __webpack_require__("6373");
var isObject = __webpack_require__("2f69");

var slice = [].slice;
var factories = {};

var construct = function (C, argsLength, args) {
  if (!(argsLength in factories)) {
    for (var list = [], i = 0; i < argsLength; i++) list[i] = 'a[' + i + ']';
    // eslint-disable-next-line no-new-func
    factories[argsLength] = Function('C,a', 'return new C(' + list.join(',') + ')');
  } return factories[argsLength](C, args);
};

// `Function.prototype.bind` method implementation
// https://tc39.github.io/ecma262/#sec-function.prototype.bind
module.exports = Function.bind || function bind(that /* , ...args */) {
  var fn = aFunction(this);
  var partArgs = slice.call(arguments, 1);
  var boundFunction = function bound(/* args... */) {
    var args = partArgs.concat(slice.call(arguments));
    return this instanceof boundFunction ? construct(fn, args.length, args) : fn.apply(that, args);
  };
  if (isObject(fn.prototype)) boundFunction.prototype = fn.prototype;
  return boundFunction;
};


/***/ }),

/***/ "7318":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "731d":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var inspectSource = __webpack_require__("b2be");

var WeakMap = global.WeakMap;

module.exports = typeof WeakMap === 'function' && /native code/.test(inspectSource(WeakMap));


/***/ }),

/***/ "7350":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("2f69");
var classof = __webpack_require__("3ab7");
var wellKnownSymbol = __webpack_require__("4736");

var MATCH = wellKnownSymbol('match');

// `IsRegExp` abstract operation
// https://tc39.github.io/ecma262/#sec-isregexp
module.exports = function (it) {
  var isRegExp;
  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : classof(it) == 'RegExp');
};


/***/ }),

/***/ "73f7":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "74f2":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"save":"Save","cancel":"Cancel"},"nl":{"save":"Opslaan","cancel":"Annuleren"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "764f":
/***/ (function(module, exports) {

module.exports = false;


/***/ }),

/***/ "7935":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var createIteratorConstructor = __webpack_require__("0051");
var getPrototypeOf = __webpack_require__("1b63");
var setPrototypeOf = __webpack_require__("a8a2");
var setToStringTag = __webpack_require__("5c65");
var createNonEnumerableProperty = __webpack_require__("0209");
var redefine = __webpack_require__("6a8a");
var wellKnownSymbol = __webpack_require__("4736");
var IS_PURE = __webpack_require__("764f");
var Iterators = __webpack_require__("0279");
var IteratorsCore = __webpack_require__("7abc");

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

/***/ "7abc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var getPrototypeOf = __webpack_require__("1b63");
var createNonEnumerableProperty = __webpack_require__("0209");
var has = __webpack_require__("e414");
var wellKnownSymbol = __webpack_require__("4736");
var IS_PURE = __webpack_require__("764f");

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

/***/ "7aeb":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");
var wellKnownSymbol = __webpack_require__("4736");
var V8_VERSION = __webpack_require__("39e8");

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

/***/ "7c42":
/***/ (function(module, exports, __webpack_require__) {

var redefine = __webpack_require__("6a8a");

module.exports = function (target, src, options) {
  for (var key in src) redefine(target, key, src[key], options);
  return target;
};


/***/ }),

/***/ "7c64":
/***/ (function(module, exports, __webpack_require__) {

var requireObjectCoercible = __webpack_require__("b2c6");
var whitespaces = __webpack_require__("9538");

var whitespace = '[' + whitespaces + ']';
var ltrim = RegExp('^' + whitespace + whitespace + '*');
var rtrim = RegExp(whitespace + whitespace + '*$');

// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
var createMethod = function (TYPE) {
  return function ($this) {
    var string = String(requireObjectCoercible($this));
    if (TYPE & 1) string = string.replace(ltrim, '');
    if (TYPE & 2) string = string.replace(rtrim, '');
    return string;
  };
};

module.exports = {
  // `String.prototype.{ trimLeft, trimStart }` methods
  // https://tc39.github.io/ecma262/#sec-string.prototype.trimstart
  start: createMethod(1),
  // `String.prototype.{ trimRight, trimEnd }` methods
  // https://tc39.github.io/ecma262/#sec-string.prototype.trimend
  end: createMethod(2),
  // `String.prototype.trim` method
  // https://tc39.github.io/ecma262/#sec-string.prototype.trim
  trim: createMethod(3)
};


/***/ }),

/***/ "7cf1":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("4ff6");

var min = Math.min;

// `ToLength` abstract operation
// https://tc39.github.io/ecma262/#sec-tolength
module.exports = function (argument) {
  return argument > 0 ? min(toInteger(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
};


/***/ }),

/***/ "7f0c":
/***/ (function(module, exports, __webpack_require__) {

var NATIVE_SYMBOL = __webpack_require__("49cf");

module.exports = NATIVE_SYMBOL
  // eslint-disable-next-line no-undef
  && !Symbol.sham
  // eslint-disable-next-line no-undef
  && typeof Symbol.iterator == 'symbol';


/***/ }),

/***/ "7f6d":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $includes = __webpack_require__("f7f3").includes;
var addToUnscopables = __webpack_require__("9b73");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var USES_TO_LENGTH = arrayMethodUsesToLength('indexOf', { ACCESSORS: true, 1: 0 });

// `Array.prototype.includes` method
// https://tc39.github.io/ecma262/#sec-array.prototype.includes
$({ target: 'Array', proto: true, forced: !USES_TO_LENGTH }, {
  includes: function includes(el /* , fromIndex = 0 */) {
    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
  }
});

// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
addToUnscopables('includes');


/***/ }),

/***/ "80fa":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var DOMIterables = __webpack_require__("68b7");
var ArrayIteratorMethods = __webpack_require__("5301");
var createNonEnumerableProperty = __webpack_require__("0209");
var wellKnownSymbol = __webpack_require__("4736");

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

/***/ "81b4":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(process) {// .dirname, .basename, and .extname methods are extracted from Node.js v8.11.1,
// backported and transplited with Babel, with backwards-compat fixes

// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

// resolves . and .. elements in a path array with directory names there
// must be no slashes, empty elements, or device names (c:\) in the array
// (so also no leading and trailing slashes - it does not distinguish
// relative and absolute paths)
function normalizeArray(parts, allowAboveRoot) {
  // if the path tries to go above the root, `up` ends up > 0
  var up = 0;
  for (var i = parts.length - 1; i >= 0; i--) {
    var last = parts[i];
    if (last === '.') {
      parts.splice(i, 1);
    } else if (last === '..') {
      parts.splice(i, 1);
      up++;
    } else if (up) {
      parts.splice(i, 1);
      up--;
    }
  }

  // if the path is allowed to go above the root, restore leading ..s
  if (allowAboveRoot) {
    for (; up--; up) {
      parts.unshift('..');
    }
  }

  return parts;
}

// path.resolve([from ...], to)
// posix version
exports.resolve = function() {
  var resolvedPath = '',
      resolvedAbsolute = false;

  for (var i = arguments.length - 1; i >= -1 && !resolvedAbsolute; i--) {
    var path = (i >= 0) ? arguments[i] : process.cwd();

    // Skip empty and invalid entries
    if (typeof path !== 'string') {
      throw new TypeError('Arguments to path.resolve must be strings');
    } else if (!path) {
      continue;
    }

    resolvedPath = path + '/' + resolvedPath;
    resolvedAbsolute = path.charAt(0) === '/';
  }

  // At this point the path should be resolved to a full absolute path, but
  // handle relative paths to be safe (might happen when process.cwd() fails)

  // Normalize the path
  resolvedPath = normalizeArray(filter(resolvedPath.split('/'), function(p) {
    return !!p;
  }), !resolvedAbsolute).join('/');

  return ((resolvedAbsolute ? '/' : '') + resolvedPath) || '.';
};

// path.normalize(path)
// posix version
exports.normalize = function(path) {
  var isAbsolute = exports.isAbsolute(path),
      trailingSlash = substr(path, -1) === '/';

  // Normalize the path
  path = normalizeArray(filter(path.split('/'), function(p) {
    return !!p;
  }), !isAbsolute).join('/');

  if (!path && !isAbsolute) {
    path = '.';
  }
  if (path && trailingSlash) {
    path += '/';
  }

  return (isAbsolute ? '/' : '') + path;
};

// posix version
exports.isAbsolute = function(path) {
  return path.charAt(0) === '/';
};

// posix version
exports.join = function() {
  var paths = Array.prototype.slice.call(arguments, 0);
  return exports.normalize(filter(paths, function(p, index) {
    if (typeof p !== 'string') {
      throw new TypeError('Arguments to path.join must be strings');
    }
    return p;
  }).join('/'));
};


// path.relative(from, to)
// posix version
exports.relative = function(from, to) {
  from = exports.resolve(from).substr(1);
  to = exports.resolve(to).substr(1);

  function trim(arr) {
    var start = 0;
    for (; start < arr.length; start++) {
      if (arr[start] !== '') break;
    }

    var end = arr.length - 1;
    for (; end >= 0; end--) {
      if (arr[end] !== '') break;
    }

    if (start > end) return [];
    return arr.slice(start, end - start + 1);
  }

  var fromParts = trim(from.split('/'));
  var toParts = trim(to.split('/'));

  var length = Math.min(fromParts.length, toParts.length);
  var samePartsLength = length;
  for (var i = 0; i < length; i++) {
    if (fromParts[i] !== toParts[i]) {
      samePartsLength = i;
      break;
    }
  }

  var outputParts = [];
  for (var i = samePartsLength; i < fromParts.length; i++) {
    outputParts.push('..');
  }

  outputParts = outputParts.concat(toParts.slice(samePartsLength));

  return outputParts.join('/');
};

exports.sep = '/';
exports.delimiter = ':';

exports.dirname = function (path) {
  if (typeof path !== 'string') path = path + '';
  if (path.length === 0) return '.';
  var code = path.charCodeAt(0);
  var hasRoot = code === 47 /*/*/;
  var end = -1;
  var matchedSlash = true;
  for (var i = path.length - 1; i >= 1; --i) {
    code = path.charCodeAt(i);
    if (code === 47 /*/*/) {
        if (!matchedSlash) {
          end = i;
          break;
        }
      } else {
      // We saw the first non-path separator
      matchedSlash = false;
    }
  }

  if (end === -1) return hasRoot ? '/' : '.';
  if (hasRoot && end === 1) {
    // return '//';
    // Backwards-compat fix:
    return '/';
  }
  return path.slice(0, end);
};

function basename(path) {
  if (typeof path !== 'string') path = path + '';

  var start = 0;
  var end = -1;
  var matchedSlash = true;
  var i;

  for (i = path.length - 1; i >= 0; --i) {
    if (path.charCodeAt(i) === 47 /*/*/) {
        // If we reached a path separator that was not part of a set of path
        // separators at the end of the string, stop now
        if (!matchedSlash) {
          start = i + 1;
          break;
        }
      } else if (end === -1) {
      // We saw the first non-path separator, mark this as the end of our
      // path component
      matchedSlash = false;
      end = i + 1;
    }
  }

  if (end === -1) return '';
  return path.slice(start, end);
}

// Uses a mixed approach for backwards-compatibility, as ext behavior changed
// in new Node.js versions, so only basename() above is backported here
exports.basename = function (path, ext) {
  var f = basename(path);
  if (ext && f.substr(-1 * ext.length) === ext) {
    f = f.substr(0, f.length - ext.length);
  }
  return f;
};

exports.extname = function (path) {
  if (typeof path !== 'string') path = path + '';
  var startDot = -1;
  var startPart = 0;
  var end = -1;
  var matchedSlash = true;
  // Track the state of characters (if any) we see before our first dot and
  // after any path separator we find
  var preDotState = 0;
  for (var i = path.length - 1; i >= 0; --i) {
    var code = path.charCodeAt(i);
    if (code === 47 /*/*/) {
        // If we reached a path separator that was not part of a set of path
        // separators at the end of the string, stop now
        if (!matchedSlash) {
          startPart = i + 1;
          break;
        }
        continue;
      }
    if (end === -1) {
      // We saw the first non-path separator, mark this as the end of our
      // extension
      matchedSlash = false;
      end = i + 1;
    }
    if (code === 46 /*.*/) {
        // If this is our first dot, mark it as the start of our extension
        if (startDot === -1)
          startDot = i;
        else if (preDotState !== 1)
          preDotState = 1;
    } else if (startDot !== -1) {
      // We saw a non-dot and non-path separator before our dot, so we should
      // have a good chance at having a non-empty extension
      preDotState = -1;
    }
  }

  if (startDot === -1 || end === -1 ||
      // We saw a non-dot character immediately before the dot
      preDotState === 0 ||
      // The (right-most) trimmed path component is exactly '..'
      preDotState === 1 && startDot === end - 1 && startDot === startPart + 1) {
    return '';
  }
  return path.slice(startDot, end);
};

function filter (xs, f) {
    if (xs.filter) return xs.filter(f);
    var res = [];
    for (var i = 0; i < xs.length; i++) {
        if (f(xs[i], i, xs)) res.push(xs[i]);
    }
    return res;
}

// String.prototype.substr - negative index don't work in IE8
var substr = 'ab'.substr(-1) === 'b'
    ? function (str, start, len) { return str.substr(start, len) }
    : function (str, start, len) {
        if (start < 0) start = str.length + start;
        return str.substr(start, len);
    }
;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("9e7e")))

/***/ }),

/***/ "8239":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $forEach = __webpack_require__("ec68").forEach;
var arrayMethodIsStrict = __webpack_require__("9fac");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var STRICT_METHOD = arrayMethodIsStrict('forEach');
var USES_TO_LENGTH = arrayMethodUsesToLength('forEach');

// `Array.prototype.forEach` method implementation
// https://tc39.github.io/ecma262/#sec-array.prototype.foreach
module.exports = (!STRICT_METHOD || !USES_TO_LENGTH) ? function forEach(callbackfn /* , thisArg */) {
  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
} : [].forEach;


/***/ }),

/***/ "8270":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "8324":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("e1f8");

/***/ }),

/***/ "8702":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var test = {};

test[TO_STRING_TAG] = 'z';

module.exports = String(test) === '[object z]';


/***/ }),

/***/ "8796":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");
var buildURL = __webpack_require__("18c2");
var InterceptorManager = __webpack_require__("164f");
var dispatchRequest = __webpack_require__("f23c");
var mergeConfig = __webpack_require__("eb13");

/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof config === 'string') {
    config = arguments[1] || {};
    config.url = arguments[0];
  } else {
    config = config || {};
  }

  config = mergeConfig(this.defaults, config);

  // Set config.method
  if (config.method) {
    config.method = config.method.toLowerCase();
  } else if (this.defaults.method) {
    config.method = this.defaults.method.toLowerCase();
  } else {
    config.method = 'get';
  }

  // Hook up interceptors middleware
  var chain = [dispatchRequest, undefined];
  var promise = Promise.resolve(config);

  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    chain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    chain.push(interceptor.fulfilled, interceptor.rejected);
  });

  while (chain.length) {
    promise = promise.then(chain.shift(), chain.shift());
  }

  return promise;
};

Axios.prototype.getUri = function getUri(config) {
  config = mergeConfig(this.defaults, config);
  return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(utils.merge(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),

/***/ "8898":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var IS_PURE = __webpack_require__("764f");
var global = __webpack_require__("b5f1");
var getBuiltIn = __webpack_require__("f914");
var NativePromise = __webpack_require__("f9d2");
var redefine = __webpack_require__("6a8a");
var redefineAll = __webpack_require__("7c42");
var setToStringTag = __webpack_require__("5c65");
var setSpecies = __webpack_require__("0e60");
var isObject = __webpack_require__("2f69");
var aFunction = __webpack_require__("6373");
var anInstance = __webpack_require__("93cb");
var classof = __webpack_require__("3ab7");
var inspectSource = __webpack_require__("b2be");
var iterate = __webpack_require__("8ea6");
var checkCorrectnessOfIteration = __webpack_require__("c968");
var speciesConstructor = __webpack_require__("bbec");
var task = __webpack_require__("371a").set;
var microtask = __webpack_require__("2d4e");
var promiseResolve = __webpack_require__("57bb");
var hostReportErrors = __webpack_require__("2f4a");
var newPromiseCapabilityModule = __webpack_require__("c517");
var perform = __webpack_require__("c7f9");
var InternalStateModule = __webpack_require__("0876");
var isForced = __webpack_require__("02c0");
var wellKnownSymbol = __webpack_require__("4736");
var V8_VERSION = __webpack_require__("39e8");

var SPECIES = wellKnownSymbol('species');
var PROMISE = 'Promise';
var getInternalState = InternalStateModule.get;
var setInternalState = InternalStateModule.set;
var getInternalPromiseState = InternalStateModule.getterFor(PROMISE);
var PromiseConstructor = NativePromise;
var TypeError = global.TypeError;
var document = global.document;
var process = global.process;
var $fetch = getBuiltIn('fetch');
var newPromiseCapability = newPromiseCapabilityModule.f;
var newGenericPromiseCapability = newPromiseCapability;
var IS_NODE = classof(process) == 'process';
var DISPATCH_EVENT = !!(document && document.createEvent && global.dispatchEvent);
var UNHANDLED_REJECTION = 'unhandledrejection';
var REJECTION_HANDLED = 'rejectionhandled';
var PENDING = 0;
var FULFILLED = 1;
var REJECTED = 2;
var HANDLED = 1;
var UNHANDLED = 2;
var Internal, OwnPromiseCapability, PromiseWrapper, nativeThen;

var FORCED = isForced(PROMISE, function () {
  var GLOBAL_CORE_JS_PROMISE = inspectSource(PromiseConstructor) !== String(PromiseConstructor);
  if (!GLOBAL_CORE_JS_PROMISE) {
    // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
    // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
    // We can't detect it synchronously, so just check versions
    if (V8_VERSION === 66) return true;
    // Unhandled rejections tracking support, NodeJS Promise without it fails @@species test
    if (!IS_NODE && typeof PromiseRejectionEvent != 'function') return true;
  }
  // We need Promise#finally in the pure version for preventing prototype pollution
  if (IS_PURE && !PromiseConstructor.prototype['finally']) return true;
  // We can't use @@species feature detection in V8 since it causes
  // deoptimization and performance degradation
  // https://github.com/zloirock/core-js/issues/679
  if (V8_VERSION >= 51 && /native code/.test(PromiseConstructor)) return false;
  // Detect correctness of subclassing with @@species support
  var promise = PromiseConstructor.resolve(1);
  var FakePromise = function (exec) {
    exec(function () { /* empty */ }, function () { /* empty */ });
  };
  var constructor = promise.constructor = {};
  constructor[SPECIES] = FakePromise;
  return !(promise.then(function () { /* empty */ }) instanceof FakePromise);
});

var INCORRECT_ITERATION = FORCED || !checkCorrectnessOfIteration(function (iterable) {
  PromiseConstructor.all(iterable)['catch'](function () { /* empty */ });
});

// helpers
var isThenable = function (it) {
  var then;
  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
};

var notify = function (promise, state, isReject) {
  if (state.notified) return;
  state.notified = true;
  var chain = state.reactions;
  microtask(function () {
    var value = state.value;
    var ok = state.state == FULFILLED;
    var index = 0;
    // variable length - can't use forEach
    while (chain.length > index) {
      var reaction = chain[index++];
      var handler = ok ? reaction.ok : reaction.fail;
      var resolve = reaction.resolve;
      var reject = reaction.reject;
      var domain = reaction.domain;
      var result, then, exited;
      try {
        if (handler) {
          if (!ok) {
            if (state.rejection === UNHANDLED) onHandleUnhandled(promise, state);
            state.rejection = HANDLED;
          }
          if (handler === true) result = value;
          else {
            if (domain) domain.enter();
            result = handler(value); // can throw
            if (domain) {
              domain.exit();
              exited = true;
            }
          }
          if (result === reaction.promise) {
            reject(TypeError('Promise-chain cycle'));
          } else if (then = isThenable(result)) {
            then.call(result, resolve, reject);
          } else resolve(result);
        } else reject(value);
      } catch (error) {
        if (domain && !exited) domain.exit();
        reject(error);
      }
    }
    state.reactions = [];
    state.notified = false;
    if (isReject && !state.rejection) onUnhandled(promise, state);
  });
};

var dispatchEvent = function (name, promise, reason) {
  var event, handler;
  if (DISPATCH_EVENT) {
    event = document.createEvent('Event');
    event.promise = promise;
    event.reason = reason;
    event.initEvent(name, false, true);
    global.dispatchEvent(event);
  } else event = { promise: promise, reason: reason };
  if (handler = global['on' + name]) handler(event);
  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
};

var onUnhandled = function (promise, state) {
  task.call(global, function () {
    var value = state.value;
    var IS_UNHANDLED = isUnhandled(state);
    var result;
    if (IS_UNHANDLED) {
      result = perform(function () {
        if (IS_NODE) {
          process.emit('unhandledRejection', value, promise);
        } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
      });
      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
      state.rejection = IS_NODE || isUnhandled(state) ? UNHANDLED : HANDLED;
      if (result.error) throw result.value;
    }
  });
};

var isUnhandled = function (state) {
  return state.rejection !== HANDLED && !state.parent;
};

var onHandleUnhandled = function (promise, state) {
  task.call(global, function () {
    if (IS_NODE) {
      process.emit('rejectionHandled', promise);
    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
  });
};

var bind = function (fn, promise, state, unwrap) {
  return function (value) {
    fn(promise, state, value, unwrap);
  };
};

var internalReject = function (promise, state, value, unwrap) {
  if (state.done) return;
  state.done = true;
  if (unwrap) state = unwrap;
  state.value = value;
  state.state = REJECTED;
  notify(promise, state, true);
};

var internalResolve = function (promise, state, value, unwrap) {
  if (state.done) return;
  state.done = true;
  if (unwrap) state = unwrap;
  try {
    if (promise === value) throw TypeError("Promise can't be resolved itself");
    var then = isThenable(value);
    if (then) {
      microtask(function () {
        var wrapper = { done: false };
        try {
          then.call(value,
            bind(internalResolve, promise, wrapper, state),
            bind(internalReject, promise, wrapper, state)
          );
        } catch (error) {
          internalReject(promise, wrapper, error, state);
        }
      });
    } else {
      state.value = value;
      state.state = FULFILLED;
      notify(promise, state, false);
    }
  } catch (error) {
    internalReject(promise, { done: false }, error, state);
  }
};

// constructor polyfill
if (FORCED) {
  // 25.4.3.1 Promise(executor)
  PromiseConstructor = function Promise(executor) {
    anInstance(this, PromiseConstructor, PROMISE);
    aFunction(executor);
    Internal.call(this);
    var state = getInternalState(this);
    try {
      executor(bind(internalResolve, this, state), bind(internalReject, this, state));
    } catch (error) {
      internalReject(this, state, error);
    }
  };
  // eslint-disable-next-line no-unused-vars
  Internal = function Promise(executor) {
    setInternalState(this, {
      type: PROMISE,
      done: false,
      notified: false,
      parent: false,
      reactions: [],
      rejection: false,
      state: PENDING,
      value: undefined
    });
  };
  Internal.prototype = redefineAll(PromiseConstructor.prototype, {
    // `Promise.prototype.then` method
    // https://tc39.github.io/ecma262/#sec-promise.prototype.then
    then: function then(onFulfilled, onRejected) {
      var state = getInternalPromiseState(this);
      var reaction = newPromiseCapability(speciesConstructor(this, PromiseConstructor));
      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
      reaction.fail = typeof onRejected == 'function' && onRejected;
      reaction.domain = IS_NODE ? process.domain : undefined;
      state.parent = true;
      state.reactions.push(reaction);
      if (state.state != PENDING) notify(this, state, false);
      return reaction.promise;
    },
    // `Promise.prototype.catch` method
    // https://tc39.github.io/ecma262/#sec-promise.prototype.catch
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });
  OwnPromiseCapability = function () {
    var promise = new Internal();
    var state = getInternalState(promise);
    this.promise = promise;
    this.resolve = bind(internalResolve, promise, state);
    this.reject = bind(internalReject, promise, state);
  };
  newPromiseCapabilityModule.f = newPromiseCapability = function (C) {
    return C === PromiseConstructor || C === PromiseWrapper
      ? new OwnPromiseCapability(C)
      : newGenericPromiseCapability(C);
  };

  if (!IS_PURE && typeof NativePromise == 'function') {
    nativeThen = NativePromise.prototype.then;

    // wrap native Promise#then for native async functions
    redefine(NativePromise.prototype, 'then', function then(onFulfilled, onRejected) {
      var that = this;
      return new PromiseConstructor(function (resolve, reject) {
        nativeThen.call(that, resolve, reject);
      }).then(onFulfilled, onRejected);
    // https://github.com/zloirock/core-js/issues/640
    }, { unsafe: true });

    // wrap fetch result
    if (typeof $fetch == 'function') $({ global: true, enumerable: true, forced: true }, {
      // eslint-disable-next-line no-unused-vars
      fetch: function fetch(input /* , init */) {
        return promiseResolve(PromiseConstructor, $fetch.apply(global, arguments));
      }
    });
  }
}

$({ global: true, wrap: true, forced: FORCED }, {
  Promise: PromiseConstructor
});

setToStringTag(PromiseConstructor, PROMISE, false, true);
setSpecies(PROMISE);

PromiseWrapper = getBuiltIn(PROMISE);

// statics
$({ target: PROMISE, stat: true, forced: FORCED }, {
  // `Promise.reject` method
  // https://tc39.github.io/ecma262/#sec-promise.reject
  reject: function reject(r) {
    var capability = newPromiseCapability(this);
    capability.reject.call(undefined, r);
    return capability.promise;
  }
});

$({ target: PROMISE, stat: true, forced: IS_PURE || FORCED }, {
  // `Promise.resolve` method
  // https://tc39.github.io/ecma262/#sec-promise.resolve
  resolve: function resolve(x) {
    return promiseResolve(IS_PURE && this === PromiseWrapper ? PromiseConstructor : this, x);
  }
});

$({ target: PROMISE, stat: true, forced: INCORRECT_ITERATION }, {
  // `Promise.all` method
  // https://tc39.github.io/ecma262/#sec-promise.all
  all: function all(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var resolve = capability.resolve;
    var reject = capability.reject;
    var result = perform(function () {
      var $promiseResolve = aFunction(C.resolve);
      var values = [];
      var counter = 0;
      var remaining = 1;
      iterate(iterable, function (promise) {
        var index = counter++;
        var alreadyCalled = false;
        values.push(undefined);
        remaining++;
        $promiseResolve.call(C, promise).then(function (value) {
          if (alreadyCalled) return;
          alreadyCalled = true;
          values[index] = value;
          --remaining || resolve(values);
        }, reject);
      });
      --remaining || resolve(values);
    });
    if (result.error) reject(result.value);
    return capability.promise;
  },
  // `Promise.race` method
  // https://tc39.github.io/ecma262/#sec-promise.race
  race: function race(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var reject = capability.reject;
    var result = perform(function () {
      var $promiseResolve = aFunction(C.resolve);
      iterate(iterable, function (promise) {
        $promiseResolve.call(C, promise).then(capability.resolve, reject);
      });
    });
    if (result.error) reject(result.value);
    return capability.promise;
  }
});


/***/ }),

/***/ "8938":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"last-name":"Last name","first-name":"First name","official-code":"Official code","new-period":"New period","checked-out":"Checked out","not-checked-out":"Not checked out","checkout-mode":"Checkout mode","no-results":"No results","not-applicable":"n/a"},"nl":{"last-name":"Familienaam","first-name":"Voornaam","official-code":"Officiële code","new-period":"Nieuwe periode","checked-out":"Uitgechecked","not-checked-out":"Niet uitgechecked","checkout-mode":"Uitcheckmodus","no-results":"Geen resultaten","not-applicable":"n.v.t."}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "893b":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var isObject = __webpack_require__("2f69");

var document = global.document;
// typeof document.createElement is 'object' in old IE
var EXISTS = isObject(document) && isObject(document.createElement);

module.exports = function (it) {
  return EXISTS ? document.createElement(it) : {};
};


/***/ }),

/***/ "89bd":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var anObject = __webpack_require__("6161");

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

/***/ "8a5f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_style_index_0_id_c42fd4f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("d612");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_style_index_0_id_c42fd4f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_style_index_0_id_c42fd4f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_style_index_0_id_c42fd4f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "8af9":
/***/ (function(module, exports, __webpack_require__) {

var TO_STRING_TAG_SUPPORT = __webpack_require__("8702");
var classofRaw = __webpack_require__("3ab7");
var wellKnownSymbol = __webpack_require__("4736");

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

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__8bbf__;

/***/ }),

/***/ "8bce":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_style_index_0_id_76b91b7e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a72b");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_style_index_0_id_76b91b7e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_style_index_0_id_76b91b7e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EntryTable_vue_vue_type_style_index_0_id_76b91b7e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "8d52":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("3ab7");

// `IsArray` abstract operation
// https://tc39.github.io/ecma262/#sec-isarray
module.exports = Array.isArray || function isArray(arg) {
  return classof(arg) == 'Array';
};


/***/ }),

/***/ "8e03":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"display":"Display"},"nl":{"display":"Weergave"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "8ea6":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("6161");
var isArrayIteratorMethod = __webpack_require__("091c");
var toLength = __webpack_require__("7cf1");
var bind = __webpack_require__("326d");
var getIteratorMethod = __webpack_require__("1e4c");
var callWithSafeIterationClosing = __webpack_require__("4047");

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

/***/ "9151":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var $entries = __webpack_require__("3f78").entries;

// `Object.entries` method
// https://tc39.github.io/ecma262/#sec-object.entries
$({ target: 'Object', stat: true }, {
  entries: function entries(O) {
    return $entries(O);
  }
});


/***/ }),

/***/ "9161":
/***/ (function(module, exports, __webpack_require__) {

var internalObjectKeys = __webpack_require__("3b5d");
var enumBugKeys = __webpack_require__("b337");

var hiddenKeys = enumBugKeys.concat('length', 'prototype');

// `Object.getOwnPropertyNames` method
// https://tc39.github.io/ecma262/#sec-object.getownpropertynames
exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return internalObjectKeys(O, hiddenKeys);
};


/***/ }),

/***/ "93cb":
/***/ (function(module, exports) {

module.exports = function (it, Constructor, name) {
  if (!(it instanceof Constructor)) {
    throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
  } return it;
};


/***/ }),

/***/ "943e":
/***/ (function(module, exports, __webpack_require__) {

var IS_PURE = __webpack_require__("764f");
var store = __webpack_require__("98a2");

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: '3.6.4',
  mode: IS_PURE ? 'pure' : 'global',
  copyright: '© 2020 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ "9538":
/***/ (function(module, exports) {

// a string of all valid unicode whitespaces
// eslint-disable-next-line max-len
module.exports = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';


/***/ }),

/***/ "96af":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var DESCRIPTORS = __webpack_require__("70b9");
var create = __webpack_require__("2c24");

// `Object.create` method
// https://tc39.github.io/ecma262/#sec-object.create
$({ target: 'Object', stat: true, sham: !DESCRIPTORS }, {
  create: create
});


/***/ }),

/***/ "96cd":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var charAt = __webpack_require__("d0d3").charAt;
var InternalStateModule = __webpack_require__("0876");
var defineIterator = __webpack_require__("7935");

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

/***/ "97f0":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),

/***/ "98a2":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var setGlobal = __webpack_require__("a134");

var SHARED = '__core-js_shared__';
var store = global[SHARED] || setGlobal(SHARED, {});

module.exports = store;


/***/ }),

/***/ "9a4e":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_5_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("eed1");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_5_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_5_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_5_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "9b46":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var enhanceError = __webpack_require__("fabe");

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),

/***/ "9b73":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");
var create = __webpack_require__("2c24");
var definePropertyModule = __webpack_require__("e6a8");

var UNSCOPABLES = wellKnownSymbol('unscopables');
var ArrayPrototype = Array.prototype;

// Array.prototype[@@unscopables]
// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
if (ArrayPrototype[UNSCOPABLES] == undefined) {
  definePropertyModule.f(ArrayPrototype, UNSCOPABLES, {
    configurable: true,
    value: create(null)
  });
}

// add a key to Array.prototype[@@unscopables]
module.exports = function (key) {
  ArrayPrototype[UNSCOPABLES][key] = true;
};


/***/ }),

/***/ "9c24":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"new-presence-status":"New presence status","label":"Label","title":"Title","aliasses":"Corresponds to","color":"Color","checkout":"Checkout possible","no-checkout":"No checkout"},"nl":{"new-presence-status":"Nieuwe aanwezigheidsstatus","label":"Label","title":"Titel","aliasses":"Komt overeen met","color":"Kleur","checkout":"Checkout mogelijk","no-checkout":"Geen checkout"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "9c4f":
/***/ (function(module, exports, __webpack_require__) {

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

/***/ "9df3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var isObject = __webpack_require__("2f69");
var isArray = __webpack_require__("8d52");
var toAbsoluteIndex = __webpack_require__("dd93");
var toLength = __webpack_require__("7cf1");
var toIndexedObject = __webpack_require__("2060");
var createProperty = __webpack_require__("c46f");
var wellKnownSymbol = __webpack_require__("4736");
var arrayMethodHasSpeciesSupport = __webpack_require__("7aeb");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('slice');
var USES_TO_LENGTH = arrayMethodUsesToLength('slice', { ACCESSORS: true, 0: 0, 1: 2 });

var SPECIES = wellKnownSymbol('species');
var nativeSlice = [].slice;
var max = Math.max;

// `Array.prototype.slice` method
// https://tc39.github.io/ecma262/#sec-array.prototype.slice
// fallback for not array-like ES3 strings and DOM objects
$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT || !USES_TO_LENGTH }, {
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

/***/ "9e7e":
/***/ (function(module, exports, __webpack_require__) {

exports.nextTick = function nextTick(fn) {
    var args = Array.prototype.slice.call(arguments);
    args.shift();
    setTimeout(function () {
        fn.apply(null, args);
    }, 0);
};

exports.platform = exports.arch = 
exports.execPath = exports.title = 'browser';
exports.pid = 1;
exports.browser = true;
exports.env = {};
exports.argv = [];

exports.binding = function (name) {
	throw new Error('No such module. (Possibly not yet loaded)')
};

(function () {
    var cwd = '/';
    var path;
    exports.cwd = function () { return cwd };
    exports.chdir = function (dir) {
        if (!path) path = __webpack_require__("81b4");
        cwd = path.resolve(dir, cwd);
    };
})();

exports.exit = exports.kill = 
exports.umask = exports.dlopen = 
exports.uptime = exports.memoryUsage = 
exports.uvCounters = function() {};
exports.features = {};


/***/ }),

/***/ "9ebe":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Cancel = __webpack_require__("97f0");

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;
  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;
  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),

/***/ "9fac":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var fails = __webpack_require__("7104");

module.exports = function (METHOD_NAME, argument) {
  var method = [][METHOD_NAME];
  return !!method && fails(function () {
    // eslint-disable-next-line no-useless-call,no-throw-literal
    method.call(null, argument || function () { throw 1; }, 1);
  });
};


/***/ }),

/***/ "a134":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var createNonEnumerableProperty = __webpack_require__("0209");

module.exports = function (key, value) {
  try {
    createNonEnumerableProperty(global, key, value);
  } catch (error) {
    global[key] = value;
  } return value;
};


/***/ }),

/***/ "a55d":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("9c24");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "a71e":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var notARegExp = __webpack_require__("615c");
var requireObjectCoercible = __webpack_require__("b2c6");
var correctIsRegExpLogic = __webpack_require__("1172");

// `String.prototype.includes` method
// https://tc39.github.io/ecma262/#sec-string.prototype.includes
$({ target: 'String', proto: true, forced: !correctIsRegExpLogic('includes') }, {
  includes: function includes(searchString /* , position = 0 */) {
    return !!~String(requireObjectCoercible(this))
      .indexOf(notARegExp(searchString), arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "a72b":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "a7a2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var bind = __webpack_require__("326d");
var toObject = __webpack_require__("64f1");
var callWithSafeIterationClosing = __webpack_require__("4047");
var isArrayIteratorMethod = __webpack_require__("091c");
var toLength = __webpack_require__("7cf1");
var createProperty = __webpack_require__("c46f");
var getIteratorMethod = __webpack_require__("1e4c");

// `Array.from` method implementation
// https://tc39.github.io/ecma262/#sec-array.from
module.exports = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
  var O = toObject(arrayLike);
  var C = typeof this == 'function' ? this : Array;
  var argumentsLength = arguments.length;
  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
  var mapping = mapfn !== undefined;
  var iteratorMethod = getIteratorMethod(O);
  var index = 0;
  var length, result, step, iterator, next, value;
  if (mapping) mapfn = bind(mapfn, argumentsLength > 2 ? arguments[2] : undefined, 2);
  // if the target is not iterable or it's an array with the default iterator - use a simple case
  if (iteratorMethod != undefined && !(C == Array && isArrayIteratorMethod(iteratorMethod))) {
    iterator = iteratorMethod.call(O);
    next = iterator.next;
    result = new C();
    for (;!(step = next.call(iterator)).done; index++) {
      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
      createProperty(result, index, value);
    }
  } else {
    length = toLength(O.length);
    result = new C(length);
    for (;length > index; index++) {
      value = mapping ? mapfn(O[index], index) : O[index];
      createProperty(result, index, value);
    }
  }
  result.length = index;
  return result;
};


/***/ }),

/***/ "a7ed":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionPreview_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8e03");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionPreview_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionPreview_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SelectionPreview_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "a8a2":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("6161");
var aPossiblePrototype = __webpack_require__("afc5");

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

/***/ "a976":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"checked-out":"Checked out","not-checked-out":"Not checked out"},"nl":{"checked-out":"Uitgechecked","not-checked-out":"Niet uitgechecked"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "a9f2":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ac29");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "ac06":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"search":"Find"},"nl":{"search":"Zoeken"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "ac29":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"apply":"Apply","cancel":"Cancel","set-students-without-status":"Set all students without a status to"},"nl":{"apply":"Pas toe","cancel":"Annuleer","set-students-without-status":"Zet alle studenten zonder status op"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "afc5":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("2f69");

module.exports = function (it) {
  if (!isObject(it) && it !== null) {
    throw TypeError("Can't set " + String(it) + ' as a prototype');
  } return it;
};


/***/ }),

/***/ "aff3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var createError = __webpack_require__("9b46");

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  if (!validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),

/***/ "b04b":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $findIndex = __webpack_require__("ec68").findIndex;
var addToUnscopables = __webpack_require__("9b73");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var FIND_INDEX = 'findIndex';
var SKIPS_HOLES = true;

var USES_TO_LENGTH = arrayMethodUsesToLength(FIND_INDEX);

// Shouldn't skip holes
if (FIND_INDEX in []) Array(1)[FIND_INDEX](function () { SKIPS_HOLES = false; });

// `Array.prototype.findIndex` method
// https://tc39.github.io/ecma262/#sec-array.prototype.findindex
$({ target: 'Array', proto: true, forced: SKIPS_HOLES || !USES_TO_LENGTH }, {
  findIndex: function findIndex(callbackfn /* , that = undefined */) {
    return $findIndex(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});

// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
addToUnscopables(FIND_INDEX);


/***/ }),

/***/ "b0a1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
    (function standardBrowserEnv() {
      var msie = /(msie|trident)/i.test(navigator.userAgent);
      var urlParsingNode = document.createElement('a');
      var originURL;

      /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
      function resolveURL(url) {
        var href = url;

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
        var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
        return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
      };
    })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return function isURLSameOrigin() {
        return true;
      };
    })()
);


/***/ }),

/***/ "b2be":
/***/ (function(module, exports, __webpack_require__) {

var store = __webpack_require__("98a2");

var functionToString = Function.toString;

// this helper broken in `3.4.1-3.4.4`, so we can't use `shared` helper
if (typeof store.inspectSource != 'function') {
  store.inspectSource = function (it) {
    return functionToString.call(it);
  };
}

module.exports = store.inspectSource;


/***/ }),

/***/ "b2c6":
/***/ (function(module, exports) {

// `RequireObjectCoercible` abstract operation
// https://tc39.github.io/ecma262/#sec-requireobjectcoercible
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on " + it);
  return it;
};


/***/ }),

/***/ "b337":
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

/***/ "b537":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var from = __webpack_require__("a7a2");
var checkCorrectnessOfIteration = __webpack_require__("c968");

var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
  Array.from(iterable);
});

// `Array.from` method
// https://tc39.github.io/ecma262/#sec-array.from
$({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
  from: from
});


/***/ }),

/***/ "b5f1":
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

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("52b3")))

/***/ }),

/***/ "b7cf":
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

/***/ "b8f8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
const EventEmitter = __webpack_require__("9c4f");
const p_timeout_1 = __webpack_require__("fde1");
const priority_queue_1 = __webpack_require__("4056");
const empty = () => { };
const timeoutError = new p_timeout_1.TimeoutError();
/**
Promise queue with concurrency control.
*/
class PQueue extends EventEmitter {
    constructor(options) {
        super();
        Object.defineProperty(this, "_carryoverConcurrencyCount", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_isIntervalIgnored", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_intervalCount", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: 0
        });
        Object.defineProperty(this, "_intervalCap", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_interval", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_intervalEnd", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: 0
        });
        Object.defineProperty(this, "_intervalId", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_timeoutId", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_queue", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_queueClass", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_pendingCount", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: 0
        });
        // The `!` is needed because of https://github.com/microsoft/TypeScript/issues/32194
        Object.defineProperty(this, "_concurrency", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_isPaused", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_resolveEmpty", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: empty
        });
        Object.defineProperty(this, "_resolveIdle", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: empty
        });
        Object.defineProperty(this, "_timeout", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        Object.defineProperty(this, "_throwOnTimeout", {
            enumerable: true,
            configurable: true,
            writable: true,
            value: void 0
        });
        // eslint-disable-next-line @typescript-eslint/no-object-literal-type-assertion
        options = Object.assign({ carryoverConcurrencyCount: false, intervalCap: Infinity, interval: 0, concurrency: Infinity, autoStart: true, queueClass: priority_queue_1.default }, options
        // TODO: Remove this `as`.
        );
        if (!(typeof options.intervalCap === 'number' && options.intervalCap >= 1)) {
            throw new TypeError(`Expected \`intervalCap\` to be a number from 1 and up, got \`${options.intervalCap}\` (${typeof options.intervalCap})`);
        }
        if (options.interval === undefined || !(Number.isFinite(options.interval) && options.interval >= 0)) {
            throw new TypeError(`Expected \`interval\` to be a finite number >= 0, got \`${options.interval}\` (${typeof options.interval})`);
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
    }
    _resolvePromises() {
        this._resolveEmpty();
        this._resolveEmpty = empty;
        if (this._pendingCount === 0) {
            this._resolveIdle();
            this._resolveIdle = empty;
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
                this.emit('active');
                this._queue.dequeue()();
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
    /**
    Set the timeout for future operations.
    */
    set timeout(milliseconds) {
        this._timeout = milliseconds;
    }
    get timeout() {
        return this._timeout;
    }
}
exports.default = PQueue;


/***/ }),

/***/ "badb":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var isArray = __webpack_require__("8d52");

// `Array.isArray` method
// https://tc39.github.io/ecma262/#sec-array.isarray
$({ target: 'Array', stat: true }, {
  isArray: isArray
});


/***/ }),

/***/ "bbec":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("6161");
var aFunction = __webpack_require__("6373");
var wellKnownSymbol = __webpack_require__("4736");

var SPECIES = wellKnownSymbol('species');

// `SpeciesConstructor` abstract operation
// https://tc39.github.io/ecma262/#sec-speciesconstructor
module.exports = function (O, defaultConstructor) {
  var C = anObject(O).constructor;
  var S;
  return C === undefined || (S = anObject(C)[SPECIES]) == undefined ? defaultConstructor : aFunction(S);
};


/***/ }),

/***/ "bd27":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),

/***/ "c097":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),

/***/ "c46f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var toPrimitive = __webpack_require__("370b");
var definePropertyModule = __webpack_require__("e6a8");
var createPropertyDescriptor = __webpack_require__("62ca");

module.exports = function (object, key, value) {
  var propertyKey = toPrimitive(key);
  if (propertyKey in object) definePropertyModule.f(object, propertyKey, createPropertyDescriptor(0, value));
  else object[propertyKey] = value;
};


/***/ }),

/***/ "c517":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var aFunction = __webpack_require__("6373");

var PromiseCapability = function (C) {
  var resolve, reject;
  this.promise = new C(function ($$resolve, $$reject) {
    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
    resolve = $$resolve;
    reject = $$reject;
  });
  this.resolve = aFunction(resolve);
  this.reject = aFunction(reject);
};

// 25.4.1.5 NewPromiseCapability(C)
module.exports.f = function (C) {
  return new PromiseCapability(C);
};


/***/ }),

/***/ "c790":
/***/ (function(module, exports, __webpack_require__) {

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

/***/ "c7f9":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return { error: false, value: exec() };
  } catch (error) {
    return { error: true, value: error };
  }
};


/***/ }),

/***/ "c968":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");

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

/***/ "cb81":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ac06");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SearchBar_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "cc54":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  if (true) {
    __webpack_require__("b7cf")
  }

  var setPublicPath_i
  if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __webpack_require__.p = setPublicPath_i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/Builder.vue?vue&type=template&id=642e4e7a&scoped=true&
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return (_vm.presence)?_c('div',{staticClass:"presence-builder",on:{"click":function($event){$event.stopPropagation();_vm.selectedStatus = null}}},[_c('b-table',{staticClass:"mod-presence mod-builder",class:{'is-changes-disabled': _vm.createNew},attrs:{"bordered":"","foot-clone":_vm.createNew,"items":_vm.presenceStatuses,"fields":_vm.fields,"tbody-tr-class":_vm.rowClass},scopedSlots:_vm._u([{key:"thead-top",fn:function(){return [_c('selection-preview',{attrs:{"presence-statuses":_vm.presenceStatuses}})]},proxy:true},{key:"head(label)",fn:function(){return [_vm._v(_vm._s(_vm.$t('label')))]},proxy:true},{key:"head(title)",fn:function(){return [_vm._v(_vm._s(_vm.$t('title')))]},proxy:true},{key:"head(aliasses)",fn:function(){return [_vm._v(_vm._s(_vm.$t('aliasses')))]},proxy:true},{key:"head(color)",fn:function(){return [_vm._v(_vm._s(_vm.$t('color')))]},proxy:true},{key:"cell(label)",fn:function(ref){
var item = ref.item;
return [_c('div',{staticClass:"cell-pad",on:{"click":function($event){$event.stopPropagation();return _vm.onSelectStatus(item)}}},[_c('b-input',{staticClass:"mod-input mod-pad mod-small",attrs:{"type":"text","required":"","autocomplete":"off","disabled":_vm.createNew},on:{"focus":function($event){return _vm.onSelectStatus(item)}},model:{value:(item.code),callback:function ($$v) {_vm.$set(item, "code", $$v)},expression:"item.code"}})],1)]}},{key:"cell(title)",fn:function(ref){
var item = ref.item;
return [_c('title-control',{staticClass:"cell-pad mod-lh",attrs:{"status":item,"status-title":_vm.getStatusTitle(item),"is-editable":_vm.isStatusEditable(item),"disabled":_vm.createNew},on:{"select":function($event){return _vm.onSelectStatus(item)}}})]}},{key:"cell(aliasses)",fn:function(ref){
var item = ref.item;
return [_c('alias-control',{staticClass:"cell-pad mod-lh",attrs:{"status":item,"alias-title":_vm.getAliasedTitle(item),"fixed-status-defaults":_vm.fixedStatusDefaults,"is-editable":_vm.isStatusEditable(item),"is-select-disabled":_vm.createNew},on:{"select":function($event){return _vm.onSelectStatus(item)}}})]}},{key:"cell(color)",fn:function(ref){
var item = ref.item;
var index = ref.index;
return [_c('color-control',{staticClass:"u-flex u-align-items-center cell-pad mod-h",attrs:{"id":index,"disabled":_vm.createNew,"color":item.color,"selected":item === _vm.selectedStatus},on:{"select":function($event){return _vm.onSelectStatus(item)},"color-selected":function($event){return _vm.setStatusColor(item, $event)}}})]}},{key:"cell(actions)",fn:function(ref){
var item = ref.item;
var index = ref.index;
return [_c('selection-controls',{staticClass:"cell-pad-x",attrs:{"id":item.id,"is-up-disabled":_vm.createNew || index === 0,"is-down-disabled":_vm.createNew || index >= _vm.presenceStatuses.length - 1,"is-remove-disabled":_vm.createNew || item.type === 'fixed' || _vm.savedEntryStatuses.includes(item.id)},on:{"move-down":function($event){return _vm.onMoveDown(item.id, index)},"move-up":function($event){return _vm.onMoveUp(item.id, index)},"remove":function($event){return _vm.onRemove(item)},"select":function($event){return _vm.onSelectStatus(item)}}})]}},{key:"foot(label)",fn:function(){return [_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.codeNew),expression:"codeNew"}],staticClass:"form-control mod-input mod-pad mod-small",attrs:{"required":"","type":"text","id":"new-presence-code"},domProps:{"value":(_vm.codeNew)},on:{"input":function($event){if($event.target.composing){ return; }_vm.codeNew=$event.target.value}}})]},proxy:true},{key:"foot(title)",fn:function(){return [_c('b-input',{staticClass:"mod-input mod-pad",attrs:{"required":"","type":"text"},model:{value:(_vm.titleNew),callback:function ($$v) {_vm.titleNew=$$v},expression:"titleNew"}})]},proxy:true},{key:"foot(aliasses)",fn:function(){return [_c('alias-control',{attrs:{"status":_vm.aliasNew,"fixed-status-defaults":_vm.fixedStatusDefaults}})]},proxy:true},{key:"foot(color)",fn:function(){return [_c('color-control',{staticClass:"u-flex",attrs:{"id":"999","color":_vm.colorNew},on:{"color-selected":function($event){_vm.colorNew = $event}}})]},proxy:true},{key:"foot(actions)",fn:function(){return [_c('new-status-controls',{staticClass:"u-flex u-gap-small presence-actions",attrs:{"isSavingDisabled":!(_vm.codeNew && _vm.titleNew && _vm.aliasNew.aliasses > 0)},on:{"save":_vm.onSaveNew,"cancel":_vm.onCancelNew}})]},proxy:true}],null,false,3458660957)}),(!_vm.createNew)?_c('div',{staticClass:"m-new"},[_c('a',{staticClass:"presence-new",on:{"click":_vm.onCreateNew}},[_c('i',{staticClass:"fa fa-plus",attrs:{"aria-hidden":"true"}}),_vm._v(" "+_vm._s(_vm.$t('new-presence-status')))])]):_vm._e(),_c('div',{staticClass:"m-checkout"},[_c('on-off-switch',{staticStyle:{"width":"136px"},attrs:{"id":"allow-checkout","checked":_vm.presence.has_checkout,"on-text":_vm.$t('checkout'),"off-text":_vm.$t('no-checkout'),"switch-class":"mod-checkout-choice"},on:{"toggle":function($event){_vm.presence.has_checkout = !_vm.presence.has_checkout}}})],1),(_vm.errorData)?_c('error-display',{staticClass:"m-errors",attrs:{"error-data":_vm.errorData}}):_vm._e(),(!_vm.createNew)?_c('save-control',{staticClass:"m-save",attrs:{"is-saving":_vm.isSaving},on:{"save":function($event){return _vm.onSave()}}}):_vm._e()],1):_vm._e()}
var staticRenderFns = []


// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=template&id=642e4e7a&scoped=true&

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.concat.js
var es_array_concat = __webpack_require__("236c");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.filter.js
var es_array_filter = __webpack_require__("e57b");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.find.js
var es_array_find = __webpack_require__("529c");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.find-index.js
var es_array_find_index = __webpack_require__("b04b");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.for-each.js
var es_array_for_each = __webpack_require__("d656");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.includes.js
var es_array_includes = __webpack_require__("7f6d");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.map.js
var es_array_map = __webpack_require__("f7ad");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.reverse.js
var es_array_reverse = __webpack_require__("40d3");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.slice.js
var es_array_slice = __webpack_require__("9df3");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.number.constructor.js
var es_number_constructor = __webpack_require__("250b");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.string.includes.js
var es_string_includes = __webpack_require__("a71e");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/web.dom-collections.for-each.js
var web_dom_collections_for_each = __webpack_require__("5270");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.is-array.js
var es_array_is_array = __webpack_require__("badb");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.symbol.js
var es_symbol = __webpack_require__("fba6");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.symbol.description.js
var es_symbol_description = __webpack_require__("ce1a");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.symbol.iterator.js
var es_symbol_iterator = __webpack_require__("05e6");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.from.js
var es_array_from = __webpack_require__("b537");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.iterator.js
var es_array_iterator = __webpack_require__("5301");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.date.to-string.js
var es_date_to_string = __webpack_require__("d667");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.to-string.js
var es_object_to_string = __webpack_require__("0379");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.regexp.to-string.js
var es_regexp_to_string = __webpack_require__("21be");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.string.iterator.js
var es_string_iterator = __webpack_require__("96cd");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/web.dom-collections.iterator.js
var web_dom_collections_iterator = __webpack_require__("80fa");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/iterableToArray.js










function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/regenerator-runtime/runtime.js
var runtime = __webpack_require__("1a6b");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.promise.js
var es_promise = __webpack_require__("8898");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js



function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/classCallCheck.js
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.define-property.js
var es_object_define_property = __webpack_require__("328d");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/createClass.js


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
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/typeof.js







function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js


function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return _assertThisInitialized(self);
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.get-prototype-of.js
var es_object_get_prototype_of = __webpack_require__("e789");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.set-prototype-of.js
var es_object_set_prototype_of = __webpack_require__("287c");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js


function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.create.js
var es_object_create = __webpack_require__("96af");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js

function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/inherits.js


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
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/tslib/tslib.es6.js
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

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-property-decorator/node_modules/vue-class-component/dist/vue-class-component.esm.js
/**
  * vue-class-component v7.2.3
  * (c) 2015-present Evan You
  * @license MIT
  */


function vue_class_component_esm_typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    vue_class_component_esm_typeof = function (obj) {
      return typeof obj;
    };
  } else {
    vue_class_component_esm_typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return vue_class_component_esm_typeof(obj);
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

function vue_class_component_esm_toConsumableArray(arr) {
  return vue_class_component_esm_arrayWithoutHoles(arr) || vue_class_component_esm_iterableToArray(arr) || vue_class_component_esm_nonIterableSpread();
}

function vue_class_component_esm_arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) arr2[i] = arr[i];

    return arr2;
  }
}

function vue_class_component_esm_iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}

function vue_class_component_esm_nonIterableSpread() {
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
function createDecorator(factory) {
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

  return external_commonjs_vue_commonjs2_vue_root_Vue_default.a.extend({
    mixins: Ctors
  });
}
function isPrimitive(value) {
  var type = vue_class_component_esm_typeof(value);

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
      if (key.charAt(0) !== '_') {
        Object.defineProperty(_this, key, {
          get: function get() {
            return vm[key];
          },
          set: function set(value) {
            vm[key] = value;
          },
          configurable: true
        });
      }
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
  var Super = superProto instanceof external_commonjs_vue_commonjs2_vue_root_Vue_default.a ? superProto.constructor : external_commonjs_vue_commonjs2_vue_root_Vue_default.a;
  var Extended = Super.extend(options);
  forwardStaticMembers(Extended, Component, Super);

  if (reflectionIsSupported()) {
    copyReflectionMetadata(Extended, Component);
  }

  return Extended;
}
var reservedPropertyNames = [// Unique id
'cid', // Super Vue constructor
'super', // Component options that will be used by the component
'options', 'superOptions', 'extendOptions', 'sealedOptions', // Private assets
'component', 'directive', 'filter'];
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

function vue_class_component_esm_Component(options) {
  if (typeof options === 'function') {
    return componentFactory(options);
  }

  return function (Component) {
    return componentFactory(Component, options);
  };
}

vue_class_component_esm_Component.registerHooks = function registerHooks(keys) {
  $internalHooks.push.apply($internalHooks, vue_class_component_esm_toConsumableArray(keys));
};

/* harmony default export */ var vue_class_component_esm = (vue_class_component_esm_Component);


// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-property-decorator/lib/vue-property-decorator.js
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

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.assign.js
var es_object_assign = __webpack_require__("49b6");

// CONCATENATED MODULE: ./src/connect/APIConfig.ts




var APIConfig_APIConfig =
/*#__PURE__*/
function () {
  function APIConfig(config) {
    _classCallCheck(this, APIConfig);

    this.loadPresenceEntriesURL = '';
    this.loadPresenceURL = '';
    this.updatePresenceURL = '';
    this.savePresenceEntryURL = '';
    this.bulkSavePresenceEntriesURL = '';
    this.createPresencePeriodURL = '';
    this.updatePresencePeriodURL = '';
    this.deletePresencePeriodURL = '';
    this.loadRegisteredPresenceEntryStatusesURL = '';
    this.togglePresenceEntryCheckoutURL = '';
    this.exportURL = '';
    this.csrfToken = '';
    Object.assign(this, config);
  }

  _createClass(APIConfig, null, [{
    key: "from",
    value: function from(config) {
      return new APIConfig(config);
    }
  }]);

  return APIConfig;
}();


// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.index-of.js
var es_array_index_of = __webpack_require__("62c8");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.splice.js
var es_array_splice = __webpack_require__("1ce3");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.function.bind.js
var es_function_bind = __webpack_require__("6bd9");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.entries.js
var es_object_entries = __webpack_require__("9151");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js









function _iterableToArrayLimit(arr, i) {
  if (!(Symbol.iterator in Object(arr) || Object.prototype.toString.call(arr) === "[object Arguments]")) {
    return;
  }

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
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/slicedToArray.js



function _slicedToArray(arr, i) {
  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/axios/index.js
var axios = __webpack_require__("8324");
var axios_default = /*#__PURE__*/__webpack_require__.n(axios);

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/p-queue/dist/index.js
var dist = __webpack_require__("b8f8");
var dist_default = /*#__PURE__*/__webpack_require__.n(dist);

// CONCATENATED MODULE: ./src/connect/Connector.ts














var TIMEOUT_SEC = 30;

var Connector_Connector =
/*#__PURE__*/
function () {
  function Connector(apiConfig) {
    _classCallCheck(this, Connector);

    this.queue = new dist_default.a({
      concurrency: 1
    });
    this._isSaving = false;
    this.errorListeners = [];
    this.apiConfig = apiConfig;
    this.finishSaving = this.finishSaving.bind(this);
  }

  _createClass(Connector, [{
    key: "addErrorListener",
    value: function addErrorListener(errorListener) {
      this.errorListeners.push(errorListener);
    }
  }, {
    key: "removeErrorListener",
    value: function removeErrorListener(errorListener) {
      var index = this.errorListeners.indexOf(errorListener);

      if (index >= 0) {
        this.errorListeners.splice(index, 1);
      }
    }
  }, {
    key: "beginSaving",
    value: function beginSaving() {
      this._isSaving = true;
    }
  }, {
    key: "finishSaving",
    value: function finishSaving() {
      this._isSaving = false;
    }
  }, {
    key: "createResultPeriod",
    value: function () {
      var _createResultPeriod = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2() {
        var _this = this;

        var callback,
            _args2 = arguments;
        return regeneratorRuntime.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                callback = _args2.length > 0 && _args2[0] !== undefined ? _args2[0] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee() {
                  var data;
                  return regeneratorRuntime.wrap(function _callee$(_context) {
                    while (1) {
                      switch (_context.prev = _context.next) {
                        case 0:
                          _context.next = 2;
                          return _this.executeAPIRequest(_this.apiConfig.createPresencePeriodURL);

                        case 2:
                          data = _context.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context.abrupt("return", data);

                        case 5:
                        case "end":
                          return _context.stop();
                      }
                    }
                  }, _callee);
                })));

              case 2:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function createResultPeriod() {
        return _createResultPeriod.apply(this, arguments);
      }

      return createResultPeriod;
    }() // eslint-disable-next-line

  }, {
    key: "loadPresenceEntries",
    value: function () {
      var _loadPresenceEntries = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee3(params) {
        var res;
        return regeneratorRuntime.wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                _context3.next = 2;
                return axios_default.a.get(this.apiConfig.loadPresenceEntriesURL, {
                  params: params
                });

              case 2:
                res = _context3.sent;
                return _context3.abrupt("return", res.data);

              case 4:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, this);
      }));

      function loadPresenceEntries(_x) {
        return _loadPresenceEntries.apply(this, arguments);
      }

      return loadPresenceEntries;
    }() // eslint-disable-next-line

  }, {
    key: "loadPresence",
    value: function () {
      var _loadPresence = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee4() {
        var res;
        return regeneratorRuntime.wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                _context4.next = 2;
                return axios_default.a.get(this.apiConfig.loadPresenceURL);

              case 2:
                res = _context4.sent;
                return _context4.abrupt("return", res.data);

              case 4:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4, this);
      }));

      function loadPresence() {
        return _loadPresence.apply(this, arguments);
      }

      return loadPresence;
    }()
  }, {
    key: "updatePresence",
    value: function () {
      var _updatePresence = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee6(id, statuses, has_checkout) {
        var _this2 = this;

        var callback,
            _args6 = arguments;
        return regeneratorRuntime.wrap(function _callee6$(_context6) {
          while (1) {
            switch (_context6.prev = _context6.next) {
              case 0:
                callback = _args6.length > 3 && _args6[3] !== undefined ? _args6[3] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee5() {
                  var parameters, data;
                  return regeneratorRuntime.wrap(function _callee5$(_context5) {
                    while (1) {
                      switch (_context5.prev = _context5.next) {
                        case 0:
                          parameters = {
                            data: JSON.stringify({
                              id: id,
                              statuses: statuses,
                              has_checkout: has_checkout
                            })
                          };
                          _context5.next = 3;
                          return _this2.executeAPIRequest(_this2.apiConfig.updatePresenceURL, parameters);

                        case 3:
                          data = _context5.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context5.abrupt("return", data);

                        case 6:
                        case "end":
                          return _context5.stop();
                      }
                    }
                  }, _callee5);
                })));

              case 2:
              case "end":
                return _context6.stop();
            }
          }
        }, _callee6, this);
      }));

      function updatePresence(_x2, _x3, _x4) {
        return _updatePresence.apply(this, arguments);
      }

      return updatePresence;
    }()
  }, {
    key: "loadRegisteredPresenceEntryStatuses",
    value: function () {
      var _loadRegisteredPresenceEntryStatuses = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee7() {
        var res;
        return regeneratorRuntime.wrap(function _callee7$(_context7) {
          while (1) {
            switch (_context7.prev = _context7.next) {
              case 0:
                _context7.next = 2;
                return axios_default.a.get(this.apiConfig.loadRegisteredPresenceEntryStatusesURL);

              case 2:
                res = _context7.sent;
                return _context7.abrupt("return", res.data);

              case 4:
              case "end":
                return _context7.stop();
            }
          }
        }, _callee7, this);
      }));

      function loadRegisteredPresenceEntryStatuses() {
        return _loadRegisteredPresenceEntryStatuses.apply(this, arguments);
      }

      return loadRegisteredPresenceEntryStatuses;
    }()
  }, {
    key: "updatePresencePeriod",
    value: function () {
      var _updatePresencePeriod = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee9(periodId, label) {
        var _this3 = this;

        var callback,
            _args9 = arguments;
        return regeneratorRuntime.wrap(function _callee9$(_context9) {
          while (1) {
            switch (_context9.prev = _context9.next) {
              case 0:
                callback = _args9.length > 2 && _args9[2] !== undefined ? _args9[2] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee8() {
                  var parameters, data;
                  return regeneratorRuntime.wrap(function _callee8$(_context8) {
                    while (1) {
                      switch (_context8.prev = _context8.next) {
                        case 0:
                          parameters = {
                            'period_id': periodId,
                            'period_label': label
                          };
                          _context8.next = 3;
                          return _this3.executeAPIRequest(_this3.apiConfig.updatePresencePeriodURL, parameters);

                        case 3:
                          data = _context8.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context8.abrupt("return", data);

                        case 6:
                        case "end":
                          return _context8.stop();
                      }
                    }
                  }, _callee8);
                })));

              case 2:
              case "end":
                return _context9.stop();
            }
          }
        }, _callee9, this);
      }));

      function updatePresencePeriod(_x5, _x6) {
        return _updatePresencePeriod.apply(this, arguments);
      }

      return updatePresencePeriod;
    }()
  }, {
    key: "deletePresencePeriod",
    value: function () {
      var _deletePresencePeriod = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee11(periodId) {
        var _this4 = this;

        var callback,
            _args11 = arguments;
        return regeneratorRuntime.wrap(function _callee11$(_context11) {
          while (1) {
            switch (_context11.prev = _context11.next) {
              case 0:
                callback = _args11.length > 1 && _args11[1] !== undefined ? _args11[1] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee10() {
                  var parameters, data;
                  return regeneratorRuntime.wrap(function _callee10$(_context10) {
                    while (1) {
                      switch (_context10.prev = _context10.next) {
                        case 0:
                          parameters = {
                            'period_id': periodId
                          };
                          _context10.next = 3;
                          return _this4.executeAPIRequest(_this4.apiConfig.deletePresencePeriodURL, parameters);

                        case 3:
                          data = _context10.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context10.abrupt("return", data);

                        case 6:
                        case "end":
                          return _context10.stop();
                      }
                    }
                  }, _callee10);
                })));

              case 2:
              case "end":
                return _context11.stop();
            }
          }
        }, _callee11, this);
      }));

      function deletePresencePeriod(_x7) {
        return _deletePresencePeriod.apply(this, arguments);
      }

      return deletePresencePeriod;
    }()
  }, {
    key: "savePresenceEntry",
    value: function () {
      var _savePresenceEntry = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee13(periodId, userId, statusId) {
        var _this5 = this;

        var callback,
            _args13 = arguments;
        return regeneratorRuntime.wrap(function _callee13$(_context13) {
          while (1) {
            switch (_context13.prev = _context13.next) {
              case 0:
                callback = _args13.length > 3 && _args13[3] !== undefined ? _args13[3] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee12() {
                  var parameters, data;
                  return regeneratorRuntime.wrap(function _callee12$(_context12) {
                    while (1) {
                      switch (_context12.prev = _context12.next) {
                        case 0:
                          parameters = {
                            'period_id': periodId,
                            'user_id': userId,
                            'status_id': statusId
                          };
                          _context12.next = 3;
                          return _this5.executeAPIRequest(_this5.apiConfig.savePresenceEntryURL, parameters);

                        case 3:
                          data = _context12.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context12.abrupt("return", data);

                        case 6:
                        case "end":
                          return _context12.stop();
                      }
                    }
                  }, _callee12);
                })));

              case 2:
              case "end":
                return _context13.stop();
            }
          }
        }, _callee13, this);
      }));

      function savePresenceEntry(_x8, _x9, _x10) {
        return _savePresenceEntry.apply(this, arguments);
      }

      return savePresenceEntry;
    }()
  }, {
    key: "bulkSavePresenceEntries",
    value: function () {
      var _bulkSavePresenceEntries = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee15(periodId, statusId) {
        var _this6 = this;

        var callback,
            _args15 = arguments;
        return regeneratorRuntime.wrap(function _callee15$(_context15) {
          while (1) {
            switch (_context15.prev = _context15.next) {
              case 0:
                callback = _args15.length > 2 && _args15[2] !== undefined ? _args15[2] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee14() {
                  var parameters, data;
                  return regeneratorRuntime.wrap(function _callee14$(_context14) {
                    while (1) {
                      switch (_context14.prev = _context14.next) {
                        case 0:
                          parameters = {
                            'period_id': periodId,
                            'status_id': statusId
                          };
                          _context14.next = 3;
                          return _this6.executeAPIRequest(_this6.apiConfig.bulkSavePresenceEntriesURL, parameters);

                        case 3:
                          data = _context14.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context14.abrupt("return", data);

                        case 6:
                        case "end":
                          return _context14.stop();
                      }
                    }
                  }, _callee14);
                })));

              case 2:
              case "end":
                return _context15.stop();
            }
          }
        }, _callee15, this);
      }));

      function bulkSavePresenceEntries(_x11, _x12) {
        return _bulkSavePresenceEntries.apply(this, arguments);
      }

      return bulkSavePresenceEntries;
    }()
  }, {
    key: "togglePresenceEntryCheckout",
    value: function () {
      var _togglePresenceEntryCheckout = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee17(periodId, userId) {
        var _this7 = this;

        var callback,
            _args17 = arguments;
        return regeneratorRuntime.wrap(function _callee17$(_context17) {
          while (1) {
            switch (_context17.prev = _context17.next) {
              case 0:
                callback = _args17.length > 2 && _args17[2] !== undefined ? _args17[2] : undefined;
                this.addToQueue(
                /*#__PURE__*/
                _asyncToGenerator(
                /*#__PURE__*/
                regeneratorRuntime.mark(function _callee16() {
                  var parameters, data;
                  return regeneratorRuntime.wrap(function _callee16$(_context16) {
                    while (1) {
                      switch (_context16.prev = _context16.next) {
                        case 0:
                          parameters = {
                            'period_id': periodId,
                            'user_id': userId
                          };
                          _context16.next = 3;
                          return _this7.executeAPIRequest(_this7.apiConfig.togglePresenceEntryCheckoutURL, parameters);

                        case 3:
                          data = _context16.sent;

                          if (callback) {
                            callback(data);
                          }

                          return _context16.abrupt("return", data);

                        case 6:
                        case "end":
                          return _context16.stop();
                      }
                    }
                  }, _callee16);
                })));

              case 2:
              case "end":
                return _context17.stop();
            }
          }
        }, _callee17, this);
      }));

      function togglePresenceEntryCheckout(_x13, _x14) {
        return _togglePresenceEntryCheckout.apply(this, arguments);
      }

      return togglePresenceEntryCheckout;
    }()
  }, {
    key: "addToQueue",
    value: function addToQueue(callback) {
      this.queue.add(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee18() {
        return regeneratorRuntime.wrap(function _callee18$(_context18) {
          while (1) {
            switch (_context18.prev = _context18.next) {
              case 0:
                _context18.next = 2;
                return callback();

              case 2:
              case "end":
                return _context18.stop();
            }
          }
        }, _callee18);
      })));
      this.queue.onIdle().then(this.finishSaving);
    }
  }, {
    key: "executeAPIRequest",
    value: function () {
      var _executeAPIRequest = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee19(apiURL) {
        var parameters,
            formData,
            _i,
            _Object$entries,
            _Object$entries$_i,
            key,
            value,
            res,
            _err$message,
            _err$response,
            _err$response$data,
            error,
            _args19 = arguments;

        return regeneratorRuntime.wrap(function _callee19$(_context19) {
          while (1) {
            switch (_context19.prev = _context19.next) {
              case 0:
                parameters = _args19.length > 1 && _args19[1] !== undefined ? _args19[1] : {};
                this.beginSaving();
                formData = new FormData();

                if (this.apiConfig.csrfToken) {
                  formData.set('_csrf_token', this.apiConfig.csrfToken);
                }

                for (_i = 0, _Object$entries = Object.entries(parameters); _i < _Object$entries.length; _i++) {
                  _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2), key = _Object$entries$_i[0], value = _Object$entries$_i[1];
                  formData.set(key, value);
                }

                _context19.prev = 5;
                _context19.next = 8;
                return axios_default.a.post(apiURL, formData, {
                  timeout: TIMEOUT_SEC * 1000
                });

              case 8:
                res = _context19.sent;

                if (!(_typeof(res.data) === 'object')) {
                  _context19.next = 13;
                  break;
                }

                return _context19.abrupt("return", res.data);

              case 13:
                if (!(typeof res.data === 'string' && res.data.indexOf('formLogin') !== -1)) {
                  _context19.next = 17;
                  break;
                }

                throw {
                  'type': 'LoggedOut'
                };

              case 17:
                throw {
                  'type': 'Unknown'
                };

              case 18:
                _context19.next = 24;
                break;

              case 20:
                _context19.prev = 20;
                _context19.t0 = _context19["catch"](5);

                if ((_context19.t0 === null || _context19.t0 === void 0 ? void 0 : _context19.t0.isAxiosError) && ((_err$message = _context19.t0.message) === null || _err$message === void 0 ? void 0 : _err$message.toLowerCase().indexOf('timeout')) !== -1) {
                  error = {
                    'type': 'Timeout'
                  };
                } else if (!!(_context19.t0 === null || _context19.t0 === void 0 ? void 0 : (_err$response = _context19.t0.response) === null || _err$response === void 0 ? void 0 : (_err$response$data = _err$response.data) === null || _err$response$data === void 0 ? void 0 : _err$response$data.error)) {
                  error = _context19.t0.response.data.error;
                } else if (!!(_context19.t0 === null || _context19.t0 === void 0 ? void 0 : _context19.t0.type)) {
                  error = _context19.t0;
                } else {
                  error = {
                    'type': 'Unknown'
                  };
                }

                this.errorListeners.forEach(function (errorListener) {
                  return errorListener.setError(error);
                });

              case 24:
              case "end":
                return _context19.stop();
            }
          }
        }, _callee19, this, [[5, 20]]);
      }));

      function executeAPIRequest(_x15) {
        return _executeAPIRequest.apply(this, arguments);
      }

      return executeAPIRequest;
    }()
  }, {
    key: "processingSize",
    get: function get() {
      return this.queue.pending + this.queue.size;
    }
  }, {
    key: "isSaving",
    get: function get() {
      return this._isSaving;
    }
  }]);

  return Connector;
}();


// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/TitleControl.vue?vue&type=template&id=1b842158&
var TitleControlvue_type_template_id_1b842158_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{on:{"click":function($event){$event.stopPropagation();return _vm.$emit('select')}}},[(_vm.isEditable)?_c('b-input',{staticClass:"mod-input mod-pad",attrs:{"type":"text","required":"","autocomplete":"off","disabled":_vm.disabled},on:{"focus":function($event){return _vm.$emit('select')}},model:{value:(_vm.status.title),callback:function ($$v) {_vm.$set(_vm.status, "title", $$v)},expression:"status.title"}}):_c('span',[_vm._v(_vm._s(_vm.statusTitle))])],1)}
var TitleControlvue_type_template_id_1b842158_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/TitleControl.vue?vue&type=template&id=1b842158&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/TitleControl.vue?vue&type=script&lang=ts&







var TitleControlvue_type_script_lang_ts_TitleControl =
/*#__PURE__*/
function (_Vue) {
  _inherits(TitleControl, _Vue);

  function TitleControl() {
    _classCallCheck(this, TitleControl);

    return _possibleConstructorReturn(this, _getPrototypeOf(TitleControl).apply(this, arguments));
  }

  return TitleControl;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object,
  required: true
})], TitleControlvue_type_script_lang_ts_TitleControl.prototype, "status", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], TitleControlvue_type_script_lang_ts_TitleControl.prototype, "statusTitle", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], TitleControlvue_type_script_lang_ts_TitleControl.prototype, "disabled", void 0);

__decorate([Prop({
  type: Boolean,
  "default": true
})], TitleControlvue_type_script_lang_ts_TitleControl.prototype, "isEditable", void 0);

TitleControlvue_type_script_lang_ts_TitleControl = __decorate([vue_class_component_esm({
  name: 'title-control'
})], TitleControlvue_type_script_lang_ts_TitleControl);
/* harmony default export */ var TitleControlvue_type_script_lang_ts_ = (TitleControlvue_type_script_lang_ts_TitleControl);
// CONCATENATED MODULE: ./src/components/builder/TitleControl.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_TitleControlvue_type_script_lang_ts_ = (TitleControlvue_type_script_lang_ts_); 
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/runtime/componentNormalizer.js
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
      // register for functional component in vue file
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

// CONCATENATED MODULE: ./src/components/builder/TitleControl.vue





/* normalize component */

var component = normalizeComponent(
  builder_TitleControlvue_type_script_lang_ts_,
  TitleControlvue_type_template_id_1b842158_render,
  TitleControlvue_type_template_id_1b842158_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var builder_TitleControl = (component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/AliasControl.vue?vue&type=template&id=89abd622&
var AliasControlvue_type_template_id_89abd622_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{on:{"click":function($event){$event.stopPropagation();return _vm.$emit('select')}}},[(_vm.isEditable)?_c('select',{directives:[{name:"model",rawName:"v-model",value:(_vm.status.aliasses),expression:"status.aliasses"}],staticClass:"form-control mod-select",attrs:{"disabled":_vm.isSelectDisabled},on:{"focus":function($event){return _vm.$emit('select')},"change":function($event){var $$selectedVal = Array.prototype.filter.call($event.target.options,function(o){return o.selected}).map(function(o){var val = "_value" in o ? o._value : o.value;return val}); _vm.$set(_vm.status, "aliasses", $event.target.multiple ? $$selectedVal : $$selectedVal[0])}}},_vm._l((_vm.fixedStatusDefaults),function(statusDefault,index){return _c('option',{key:("fs-" + index),domProps:{"value":statusDefault.id}},[_vm._v(_vm._s(statusDefault.title))])}),0):_c('span',[_vm._v(_vm._s(_vm.aliasTitle))])])}
var AliasControlvue_type_template_id_89abd622_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/AliasControl.vue?vue&type=template&id=89abd622&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/AliasControl.vue?vue&type=script&lang=ts&







var AliasControlvue_type_script_lang_ts_AliasControl =
/*#__PURE__*/
function (_Vue) {
  _inherits(AliasControl, _Vue);

  function AliasControl() {
    _classCallCheck(this, AliasControl);

    return _possibleConstructorReturn(this, _getPrototypeOf(AliasControl).apply(this, arguments));
  }

  return AliasControl;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], AliasControlvue_type_script_lang_ts_AliasControl.prototype, "fixedStatusDefaults", void 0);

__decorate([Prop({
  type: Object,
  required: true
})], AliasControlvue_type_script_lang_ts_AliasControl.prototype, "status", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], AliasControlvue_type_script_lang_ts_AliasControl.prototype, "aliasTitle", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], AliasControlvue_type_script_lang_ts_AliasControl.prototype, "isSelectDisabled", void 0);

__decorate([Prop({
  type: Boolean,
  "default": true
})], AliasControlvue_type_script_lang_ts_AliasControl.prototype, "isEditable", void 0);

AliasControlvue_type_script_lang_ts_AliasControl = __decorate([vue_class_component_esm({
  name: 'alias-control'
})], AliasControlvue_type_script_lang_ts_AliasControl);
/* harmony default export */ var AliasControlvue_type_script_lang_ts_ = (AliasControlvue_type_script_lang_ts_AliasControl);
// CONCATENATED MODULE: ./src/components/builder/AliasControl.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_AliasControlvue_type_script_lang_ts_ = (AliasControlvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/builder/AliasControl.vue





/* normalize component */

var AliasControl_component = normalizeComponent(
  builder_AliasControlvue_type_script_lang_ts_,
  AliasControlvue_type_template_id_89abd622_render,
  AliasControlvue_type_template_id_89abd622_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var builder_AliasControl = (AliasControl_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/ColorControl.vue?vue&type=template&id=43e1f89c&
var ColorControlvue_type_template_id_43e1f89c_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{on:{"click":function($event){$event.stopPropagation();return _vm.$emit('select')}}},[_c('button',{staticClass:"color",class:[{'is-selected': _vm.selected}, _vm.color],attrs:{"id":("color-" + _vm.id),"disabled":_vm.disabled},on:{"focus":function($event){return _vm.$emit('select')}}}),_c('color-picker',{attrs:{"target":("color-" + _vm.id),"selected-color":_vm.color,"triggers":"click blur","placement":"right"},on:{"color-selected":function($event){return _vm.$emit('color-selected', $event)}}})],1)}
var ColorControlvue_type_template_id_43e1f89c_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/ColorControl.vue?vue&type=template&id=43e1f89c&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/ColorPicker.vue?vue&type=template&id=26522452&
var ColorPickervue_type_template_id_26522452_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('b-popover',{attrs:{"target":_vm.target,"triggers":_vm.triggers,"placement":_vm.placement}},[_c('div',{staticClass:"presence-swatches"},[_vm._l((_vm.variants),function(variant){return [_vm._l((_vm.colors),function(color){return [_c('button',{key:("color-swatch-" + color + "-" + variant),class:[("color mod-swatch " + color + "-" + variant), {'is-selected': _vm.selectedColor === (color + "-" + variant)}],on:{"click":function($event){$event.stopPropagation();return _vm.$emit('color-selected', (color + "-" + variant))}}})]})]})],2)])}
var ColorPickervue_type_template_id_26522452_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/ColorPicker.vue?vue&type=template&id=26522452&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/ColorPicker.vue?vue&type=script&lang=ts&







var ColorPickervue_type_script_lang_ts_ColorPicker =
/*#__PURE__*/
function (_Vue) {
  _inherits(ColorPicker, _Vue);

  function ColorPicker() {
    var _this;

    _classCallCheck(this, ColorPicker);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ColorPicker).apply(this, arguments));
    _this.variants = [100, 300, 500, 700, 900];
    _this.colors = ['pink', 'blue', 'cyan', 'teal', 'green', 'light-green', 'lime', 'yellow', 'amber', 'deep-orange', 'grey'];
    return _this;
  }

  return ColorPicker;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  required: true
})], ColorPickervue_type_script_lang_ts_ColorPicker.prototype, "target", void 0);

__decorate([Prop({
  type: String,
  "default": 'click'
})], ColorPickervue_type_script_lang_ts_ColorPicker.prototype, "triggers", void 0);

__decorate([Prop({
  type: String,
  "default": 'bottom'
})], ColorPickervue_type_script_lang_ts_ColorPicker.prototype, "placement", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], ColorPickervue_type_script_lang_ts_ColorPicker.prototype, "selectedColor", void 0);

ColorPickervue_type_script_lang_ts_ColorPicker = __decorate([vue_class_component_esm({
  name: 'color-picker'
})], ColorPickervue_type_script_lang_ts_ColorPicker);
/* harmony default export */ var ColorPickervue_type_script_lang_ts_ = (ColorPickervue_type_script_lang_ts_ColorPicker);
// CONCATENATED MODULE: ./src/components/builder/ColorPicker.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_ColorPickervue_type_script_lang_ts_ = (ColorPickervue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/builder/ColorPicker.vue





/* normalize component */

var ColorPicker_component = normalizeComponent(
  builder_ColorPickervue_type_script_lang_ts_,
  ColorPickervue_type_template_id_26522452_render,
  ColorPickervue_type_template_id_26522452_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var builder_ColorPicker = (ColorPicker_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/ColorControl.vue?vue&type=script&lang=ts&









var ColorControlvue_type_script_lang_ts_ColorControl =
/*#__PURE__*/
function (_Vue) {
  _inherits(ColorControl, _Vue);

  function ColorControl() {
    _classCallCheck(this, ColorControl);

    return _possibleConstructorReturn(this, _getPrototypeOf(ColorControl).apply(this, arguments));
  }

  return ColorControl;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Number,
  "default": 0
})], ColorControlvue_type_script_lang_ts_ColorControl.prototype, "id", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], ColorControlvue_type_script_lang_ts_ColorControl.prototype, "disabled", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], ColorControlvue_type_script_lang_ts_ColorControl.prototype, "selected", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], ColorControlvue_type_script_lang_ts_ColorControl.prototype, "color", void 0);

ColorControlvue_type_script_lang_ts_ColorControl = __decorate([vue_class_component_esm({
  name: 'color-control',
  components: {
    ColorPicker: builder_ColorPicker
  }
})], ColorControlvue_type_script_lang_ts_ColorControl);
/* harmony default export */ var ColorControlvue_type_script_lang_ts_ = (ColorControlvue_type_script_lang_ts_ColorControl);
// CONCATENATED MODULE: ./src/components/builder/ColorControl.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_ColorControlvue_type_script_lang_ts_ = (ColorControlvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/builder/ColorControl.vue





/* normalize component */

var ColorControl_component = normalizeComponent(
  builder_ColorControlvue_type_script_lang_ts_,
  ColorControlvue_type_template_id_43e1f89c_render,
  ColorControlvue_type_template_id_43e1f89c_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var builder_ColorControl = (ColorControl_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/SelectionControls.vue?vue&type=template&id=13833ca2&
var SelectionControlsvue_type_template_id_13833ca2_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('div',{staticClass:"u-flex u-gap-small presence-actions",on:{"click":function($event){$event.stopPropagation();}}},[_c('button',{staticClass:"btn btn-default btn-sm mod-presence",attrs:{"id":("btn-up-" + _vm.id),"title":_vm.$t('move-up'),"disabled":_vm.isUpDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('move-up')},"focus":function($event){return _vm.$emit('select')}}},[_c('i',{staticClass:"fa fa-arrow-up",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('move-up')))])]),_c('button',{staticClass:"btn btn-default btn-sm mod-presence",attrs:{"id":("btn-down-" + _vm.id),"title":_vm.$t('move-down'),"disabled":_vm.isDownDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('move-down')},"focus":function($event){return _vm.$emit('select')}}},[_c('i',{staticClass:"fa fa-arrow-down",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('move-down')))])]),_c('button',{staticClass:"btn btn-default btn-sm mod-presence",attrs:{"title":_vm.$t('remove'),"disabled":_vm.isRemoveDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('remove')},"focus":function($event){return _vm.$emit('select')}}},[_c('i',{staticClass:"fa fa-minus-circle",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('remove')))])])])])}
var SelectionControlsvue_type_template_id_13833ca2_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/SelectionControls.vue?vue&type=template&id=13833ca2&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/SelectionControls.vue?vue&type=script&lang=ts&








var SelectionControlsvue_type_script_lang_ts_SelectionControls =
/*#__PURE__*/
function (_Vue) {
  _inherits(SelectionControls, _Vue);

  function SelectionControls() {
    _classCallCheck(this, SelectionControls);

    return _possibleConstructorReturn(this, _getPrototypeOf(SelectionControls).apply(this, arguments));
  }

  return SelectionControls;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Number,
  "default": 0
})], SelectionControlsvue_type_script_lang_ts_SelectionControls.prototype, "id", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], SelectionControlsvue_type_script_lang_ts_SelectionControls.prototype, "isUpDisabled", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], SelectionControlsvue_type_script_lang_ts_SelectionControls.prototype, "isDownDisabled", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], SelectionControlsvue_type_script_lang_ts_SelectionControls.prototype, "isRemoveDisabled", void 0);

SelectionControlsvue_type_script_lang_ts_SelectionControls = __decorate([vue_class_component_esm({
  name: 'selection-controls'
})], SelectionControlsvue_type_script_lang_ts_SelectionControls);
/* harmony default export */ var SelectionControlsvue_type_script_lang_ts_ = (SelectionControlsvue_type_script_lang_ts_SelectionControls);
// CONCATENATED MODULE: ./src/components/builder/SelectionControls.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_SelectionControlsvue_type_script_lang_ts_ = (SelectionControlsvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/builder/SelectionControls.vue?vue&type=custom&index=0&blockType=i18n
var SelectionControlsvue_type_custom_index_0_blockType_i18n = __webpack_require__("1ca3");

// CONCATENATED MODULE: ./src/components/builder/SelectionControls.vue





/* normalize component */

var SelectionControls_component = normalizeComponent(
  builder_SelectionControlsvue_type_script_lang_ts_,
  SelectionControlsvue_type_template_id_13833ca2_render,
  SelectionControlsvue_type_template_id_13833ca2_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */

if (typeof SelectionControlsvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(SelectionControlsvue_type_custom_index_0_blockType_i18n["default"])(SelectionControls_component)

/* harmony default export */ var builder_SelectionControls = (SelectionControls_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/NewStatusControls.vue?vue&type=template&id=526f2a04&
var NewStatusControlsvue_type_template_id_526f2a04_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('button',{staticClass:"btn btn-default btn-sm mod-presence",attrs:{"title":_vm.$t('save'),"disabled":_vm.isSavingDisabled},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('save')}}},[_c('i',{staticClass:"fa fa-check-circle",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('save')))])]),_c('button',{staticClass:"btn btn-default btn-sm mod-presence mod-cancel",attrs:{"title":_vm.$t('cancel')},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('cancel')}}},[_c('i',{staticClass:"fa fa-minus-circle",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('cancel')))])])])}
var NewStatusControlsvue_type_template_id_526f2a04_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/NewStatusControls.vue?vue&type=template&id=526f2a04&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/NewStatusControls.vue?vue&type=script&lang=ts&







var NewStatusControlsvue_type_script_lang_ts_NewStatusControls =
/*#__PURE__*/
function (_Vue) {
  _inherits(NewStatusControls, _Vue);

  function NewStatusControls() {
    _classCallCheck(this, NewStatusControls);

    return _possibleConstructorReturn(this, _getPrototypeOf(NewStatusControls).apply(this, arguments));
  }

  return NewStatusControls;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean,
  "default": false
})], NewStatusControlsvue_type_script_lang_ts_NewStatusControls.prototype, "isSavingDisabled", void 0);

NewStatusControlsvue_type_script_lang_ts_NewStatusControls = __decorate([vue_class_component_esm({
  name: 'new-status-controls'
})], NewStatusControlsvue_type_script_lang_ts_NewStatusControls);
/* harmony default export */ var NewStatusControlsvue_type_script_lang_ts_ = (NewStatusControlsvue_type_script_lang_ts_NewStatusControls);
// CONCATENATED MODULE: ./src/components/builder/NewStatusControls.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_NewStatusControlsvue_type_script_lang_ts_ = (NewStatusControlsvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/builder/NewStatusControls.vue?vue&type=custom&index=0&blockType=i18n
var NewStatusControlsvue_type_custom_index_0_blockType_i18n = __webpack_require__("2e43");

// CONCATENATED MODULE: ./src/components/builder/NewStatusControls.vue





/* normalize component */

var NewStatusControls_component = normalizeComponent(
  builder_NewStatusControlsvue_type_script_lang_ts_,
  NewStatusControlsvue_type_template_id_526f2a04_render,
  NewStatusControlsvue_type_template_id_526f2a04_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */

if (typeof NewStatusControlsvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(NewStatusControlsvue_type_custom_index_0_blockType_i18n["default"])(NewStatusControls_component)

/* harmony default export */ var builder_NewStatusControls = (NewStatusControls_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/SelectionPreview.vue?vue&type=template&id=d9b9df98&
var SelectionPreviewvue_type_template_id_d9b9df98_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('b-tr',[_c('b-th',{staticClass:"u-font-normal"},[_vm._v(_vm._s(_vm.$t('display')))]),_c('b-th',{attrs:{"colspan":"3"}},[_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap",staticStyle:{"padding":"4px 0"}},_vm._l((_vm.presenceStatuses),function(status,index){return _c('div',{key:("status-" + index),staticClass:"color-code",class:[status.color]},[_c('span',[_vm._v(_vm._s(status.code))])])}),0)])],1)}
var SelectionPreviewvue_type_template_id_d9b9df98_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/SelectionPreview.vue?vue&type=template&id=d9b9df98&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/SelectionPreview.vue?vue&type=script&lang=ts&







var SelectionPreviewvue_type_script_lang_ts_SelectionPreview =
/*#__PURE__*/
function (_Vue) {
  _inherits(SelectionPreview, _Vue);

  function SelectionPreview() {
    _classCallCheck(this, SelectionPreview);

    return _possibleConstructorReturn(this, _getPrototypeOf(SelectionPreview).apply(this, arguments));
  }

  return SelectionPreview;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], SelectionPreviewvue_type_script_lang_ts_SelectionPreview.prototype, "presenceStatuses", void 0);

SelectionPreviewvue_type_script_lang_ts_SelectionPreview = __decorate([vue_class_component_esm({
  name: 'selection-preview'
})], SelectionPreviewvue_type_script_lang_ts_SelectionPreview);
/* harmony default export */ var SelectionPreviewvue_type_script_lang_ts_ = (SelectionPreviewvue_type_script_lang_ts_SelectionPreview);
// CONCATENATED MODULE: ./src/components/builder/SelectionPreview.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_SelectionPreviewvue_type_script_lang_ts_ = (SelectionPreviewvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/builder/SelectionPreview.vue?vue&type=custom&index=0&blockType=i18n
var SelectionPreviewvue_type_custom_index_0_blockType_i18n = __webpack_require__("a7ed");

// CONCATENATED MODULE: ./src/components/builder/SelectionPreview.vue





/* normalize component */

var SelectionPreview_component = normalizeComponent(
  builder_SelectionPreviewvue_type_script_lang_ts_,
  SelectionPreviewvue_type_template_id_d9b9df98_render,
  SelectionPreviewvue_type_template_id_d9b9df98_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */

if (typeof SelectionPreviewvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(SelectionPreviewvue_type_custom_index_0_blockType_i18n["default"])(SelectionPreview_component)

/* harmony default export */ var builder_SelectionPreview = (SelectionPreview_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/SaveControl.vue?vue&type=template&id=0518a9fb&
var SaveControlvue_type_template_id_0518a9fb_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('button',{staticClass:"btn btn-primary mod-presence-save",attrs:{"disabled":_vm.isSaving},on:{"click":function($event){return _vm.$emit('save')}}},[(_vm.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin",staticStyle:{"margin-right":"5px"}}):_vm._e(),_vm._v(" "+_vm._s(_vm.$t('save'))+" ")])])}
var SaveControlvue_type_template_id_0518a9fb_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue?vue&type=template&id=0518a9fb&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/SaveControl.vue?vue&type=script&lang=ts&







var SaveControlvue_type_script_lang_ts_SaveControl =
/*#__PURE__*/
function (_Vue) {
  _inherits(SaveControl, _Vue);

  function SaveControl() {
    _classCallCheck(this, SaveControl);

    return _possibleConstructorReturn(this, _getPrototypeOf(SaveControl).apply(this, arguments));
  }

  return SaveControl;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean,
  "default": false
})], SaveControlvue_type_script_lang_ts_SaveControl.prototype, "isSaving", void 0);

SaveControlvue_type_script_lang_ts_SaveControl = __decorate([vue_class_component_esm({
  name: 'save-control'
})], SaveControlvue_type_script_lang_ts_SaveControl);
/* harmony default export */ var SaveControlvue_type_script_lang_ts_ = (SaveControlvue_type_script_lang_ts_SaveControl);
// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_SaveControlvue_type_script_lang_ts_ = (SaveControlvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/builder/SaveControl.vue?vue&type=custom&index=0&blockType=i18n
var SaveControlvue_type_custom_index_0_blockType_i18n = __webpack_require__("5792");

// CONCATENATED MODULE: ./src/components/builder/SaveControl.vue





/* normalize component */

var SaveControl_component = normalizeComponent(
  builder_SaveControlvue_type_script_lang_ts_,
  SaveControlvue_type_template_id_0518a9fb_render,
  SaveControlvue_type_template_id_0518a9fb_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */

if (typeof SaveControlvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(SaveControlvue_type_custom_index_0_blockType_i18n["default"])(SaveControl_component)

/* harmony default export */ var builder_SaveControl = (SaveControl_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/ErrorDisplay.vue?vue&type=template&id=779b0c97&
var ErrorDisplayvue_type_template_id_779b0c97_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"alert alert-danger"},[(_vm.errorData.type === 'NoTitleGiven')?[_vm._v(" "+_vm._s(_vm.$t('error-NoTitleGiven'))+" "),_c('span',{staticClass:"u-block"},[_vm._v(_vm._s(_vm.errorData.status))]),_vm._v(" "+_vm._s(_vm.$t('changes-not-saved'))+" ")]:(!!_vm.errorData.status)?_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type, {title: _vm.errorData.status.title}))+" "+_vm._s(_vm.$t('changes-not-saved')))]):_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type)))])],2)}
var ErrorDisplayvue_type_template_id_779b0c97_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/builder/ErrorDisplay.vue?vue&type=template&id=779b0c97&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/builder/ErrorDisplay.vue?vue&type=script&lang=ts&







var ErrorDisplayvue_type_script_lang_ts_ErrorDisplay =
/*#__PURE__*/
function (_Vue) {
  _inherits(ErrorDisplay, _Vue);

  function ErrorDisplay() {
    _classCallCheck(this, ErrorDisplay);

    return _possibleConstructorReturn(this, _getPrototypeOf(ErrorDisplay).apply(this, arguments));
  }

  return ErrorDisplay;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object,
  required: true
})], ErrorDisplayvue_type_script_lang_ts_ErrorDisplay.prototype, "errorData", void 0);

ErrorDisplayvue_type_script_lang_ts_ErrorDisplay = __decorate([vue_class_component_esm({
  name: 'error-display'
})], ErrorDisplayvue_type_script_lang_ts_ErrorDisplay);
/* harmony default export */ var ErrorDisplayvue_type_script_lang_ts_ = (ErrorDisplayvue_type_script_lang_ts_ErrorDisplay);
// CONCATENATED MODULE: ./src/components/builder/ErrorDisplay.vue?vue&type=script&lang=ts&
 /* harmony default export */ var builder_ErrorDisplayvue_type_script_lang_ts_ = (ErrorDisplayvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/builder/ErrorDisplay.vue?vue&type=custom&index=0&blockType=i18n
var ErrorDisplayvue_type_custom_index_0_blockType_i18n = __webpack_require__("6faf");

// CONCATENATED MODULE: ./src/components/builder/ErrorDisplay.vue





/* normalize component */

var ErrorDisplay_component = normalizeComponent(
  builder_ErrorDisplayvue_type_script_lang_ts_,
  ErrorDisplayvue_type_template_id_779b0c97_render,
  ErrorDisplayvue_type_template_id_779b0c97_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */

if (typeof ErrorDisplayvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(ErrorDisplayvue_type_custom_index_0_blockType_i18n["default"])(ErrorDisplay_component)

/* harmony default export */ var builder_ErrorDisplay = (ErrorDisplay_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/OnOffSwitch.vue?vue&type=template&id=af24b412&
var OnOffSwitchvue_type_template_id_af24b412_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"onoffswitch",class:[_vm.switchClass]},[_c('input',{staticClass:"onoffswitch-checkbox",attrs:{"type":"checkbox","id":("onoffswitch-" + _vm.id)},domProps:{"checked":_vm.checked},on:{"input":function($event){return _vm.$emit('toggle')}}}),_c('label',{staticClass:"onoffswitch-label",class:[_vm.switchClass],attrs:{"for":("onoffswitch-" + _vm.id)}},[_c('span',{staticClass:"onoffswitch-inner"},[_c('span',{staticClass:"onoffswitch-inner-before",class:[_vm.switchClass]},[_vm._v(_vm._s(_vm.onText))]),_c('span',{staticClass:"onoffswitch-inner-after",class:[_vm.switchClass]},[_vm._v(_vm._s(_vm.offText))])]),_c('span',{staticClass:"onoffswitch-switch",class:[_vm.switchClass]})])])}
var OnOffSwitchvue_type_template_id_af24b412_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/OnOffSwitch.vue?vue&type=template&id=af24b412&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/OnOffSwitch.vue?vue&type=script&lang=ts&







var OnOffSwitchvue_type_script_lang_ts_OnOffSwitch =
/*#__PURE__*/
function (_Vue) {
  _inherits(OnOffSwitch, _Vue);

  function OnOffSwitch() {
    _classCallCheck(this, OnOffSwitch);

    return _possibleConstructorReturn(this, _getPrototypeOf(OnOffSwitch).apply(this, arguments));
  }

  return OnOffSwitch;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean
})], OnOffSwitchvue_type_script_lang_ts_OnOffSwitch.prototype, "checked", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], OnOffSwitchvue_type_script_lang_ts_OnOffSwitch.prototype, "id", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], OnOffSwitchvue_type_script_lang_ts_OnOffSwitch.prototype, "onText", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], OnOffSwitchvue_type_script_lang_ts_OnOffSwitch.prototype, "offText", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], OnOffSwitchvue_type_script_lang_ts_OnOffSwitch.prototype, "switchClass", void 0);

OnOffSwitchvue_type_script_lang_ts_OnOffSwitch = __decorate([vue_class_component_esm({
  name: 'on-off-switch'
})], OnOffSwitchvue_type_script_lang_ts_OnOffSwitch);
/* harmony default export */ var OnOffSwitchvue_type_script_lang_ts_ = (OnOffSwitchvue_type_script_lang_ts_OnOffSwitch);
// CONCATENATED MODULE: ./src/components/OnOffSwitch.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_OnOffSwitchvue_type_script_lang_ts_ = (OnOffSwitchvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/OnOffSwitch.vue





/* normalize component */

var OnOffSwitch_component = normalizeComponent(
  components_OnOffSwitchvue_type_script_lang_ts_,
  OnOffSwitchvue_type_template_id_af24b412_render,
  OnOffSwitchvue_type_template_id_af24b412_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var components_OnOffSwitch = (OnOffSwitch_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/Builder.vue?vue&type=script&lang=ts&

































var DEFAULT_COLOR_NEW = 'yellow-100';
var CONFLICT_ERRORS = ['PresenceStatusMissing', 'InvalidType', 'NoTitleGiven', 'TitleUpdateForbidden', 'InvalidAlias', 'AliasUpdateForbidden', 'NoCodeGiven', 'NoColorGiven', 'InvalidColor'];

var Buildervue_type_script_lang_ts_Builder =
/*#__PURE__*/
function (_Vue) {
  _inherits(Builder, _Vue);

  function Builder() {
    var _this;

    _classCallCheck(this, Builder);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Builder).apply(this, arguments));
    _this.fields = [{
      key: 'label',
      sortable: false
    }, {
      key: 'title',
      sortable: false
    }, {
      key: 'aliasses',
      sortable: false
    }, {
      key: 'color',
      sortable: false
    }, {
      key: 'actions',
      sortable: false,
      label: '',
      variant: 'actions'
    }];
    _this.statusDefaults = [];
    _this.presence = null;
    _this.selectedStatus = null;
    _this.savedEntryStatuses = [];
    _this.connector = null;
    _this.errorData = null;
    _this.createNew = false;
    _this.codeNew = '';
    _this.titleNew = '';
    _this.aliasNew = {
      aliasses: 3
    };
    _this.colorNew = DEFAULT_COLOR_NEW;
    return _this;
  }

  _createClass(Builder, [{
    key: "load",
    value: function () {
      var _load = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        var _this$connector;

        var presenceData;
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return (_this$connector = this.connector) === null || _this$connector === void 0 ? void 0 : _this$connector.loadPresence();

              case 2:
                presenceData = _context.sent;
                this.statusDefaults = (presenceData === null || presenceData === void 0 ? void 0 : presenceData['status-defaults']) || [];
                this.presence = (presenceData === null || presenceData === void 0 ? void 0 : presenceData.presence) || null;

              case 5:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function load() {
        return _load.apply(this, arguments);
      }

      return load;
    }()
  }, {
    key: "loadSavedEntryStatuses",
    value: function () {
      var _loadSavedEntryStatuses = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2() {
        var _this$connector2;

        var data;
        return regeneratorRuntime.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _context2.next = 2;
                return (_this$connector2 = this.connector) === null || _this$connector2 === void 0 ? void 0 : _this$connector2.loadRegisteredPresenceEntryStatuses();

              case 2:
                data = _context2.sent;
                this.savedEntryStatuses = data === null || data === void 0 ? void 0 : data.statuses;

              case 4:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function loadSavedEntryStatuses() {
        return _loadSavedEntryStatuses.apply(this, arguments);
      }

      return loadSavedEntryStatuses;
    }()
  }, {
    key: "mounted",
    value: function mounted() {
      this.connector = new Connector_Connector(this.apiConfig);
      this.connector.addErrorListener(this);
      this.load();
    }
  }, {
    key: "getStatusDefault",
    value: function getStatusDefault(status) {
      var fixed = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var statusDefault = this.statusDefaults.find(function (sd) {
        return sd.id === status.id;
      });

      if (!fixed) {
        return statusDefault;
      }

      return statusDefault.type === 'fixed' ? statusDefault : this.statusDefaults.find(function (sd) {
        return sd.id === statusDefault.aliasses;
      });
    }
  }, {
    key: "isStatusEditable",
    value: function isStatusEditable(status) {
      return !(status.type === 'fixed' || status.type === 'semifixed' || this.savedEntryStatuses.includes(status.id));
    }
  }, {
    key: "getStatusTitle",
    value: function getStatusTitle(status) {
      if (status.type === 'fixed' || status.type === 'semifixed') {
        var _this$getStatusDefaul;

        return ((_this$getStatusDefaul = this.getStatusDefault(status)) === null || _this$getStatusDefaul === void 0 ? void 0 : _this$getStatusDefaul.title) || '';
      }

      return status.title;
    }
  }, {
    key: "getAliasedTitle",
    value: function getAliasedTitle(status) {
      var _this$statusDefaults$;

      if (status.type === 'fixed' || status.type === 'semifixed') {
        var _this$getStatusDefaul2;

        return ((_this$getStatusDefaul2 = this.getStatusDefault(status, true)) === null || _this$getStatusDefaul2 === void 0 ? void 0 : _this$getStatusDefaul2.title) || '';
      }

      return ((_this$statusDefaults$ = this.statusDefaults.find(function (sd) {
        return sd.id === status.aliasses;
      })) === null || _this$statusDefaults$ === void 0 ? void 0 : _this$statusDefaults$.title) || '';
    }
  }, {
    key: "setStatusColor",
    value: function setStatusColor(status, color) {
      if (status.color !== color) {
        status.color = color;
      }
    }
  }, {
    key: "rowClass",
    value: function rowClass(status) {
      return status === this.selectedStatus ? 'is-selected' : '';
    }
  }, {
    key: "hasEmptyFields",
    value: function hasEmptyFields() {
      var hasEmptyFields = false;

      var inputs = _toConsumableArray(document.querySelectorAll('.presence-builder .form-control'));

      inputs.reverse();
      inputs.forEach(function (input) {
        if (!input.checkValidity()) {
          input.reportValidity();
          hasEmptyFields = true;
        }
      });
      return hasEmptyFields;
    }
  }, {
    key: "isConflictError",
    value: function isConflictError(errorType) {
      return CONFLICT_ERRORS.includes(errorType);
    }
  }, {
    key: "setError",
    value: function setError(data) {
      this.errorData = data;
    }
  }, {
    key: "resetNew",
    value: function resetNew() {
      this.createNew = false;
      this.codeNew = '';
      this.titleNew = '';
      this.aliasNew.aliasses = 3;
      this.colorNew = DEFAULT_COLOR_NEW;
    }
  }, {
    key: "onCreateNew",
    value: function onCreateNew() {
      this.createNew = true;
      this.selectedStatus = null;
      this.$nextTick(function () {
        var _document$getElementB;

        (_document$getElementB = document.getElementById('new-presence-code')) === null || _document$getElementB === void 0 ? void 0 : _document$getElementB.focus();
      });
    }
  }, {
    key: "onCancelNew",
    value: function onCancelNew() {
      this.resetNew();
    }
  }, {
    key: "onSaveNew",
    value: function onSaveNew() {
      var _this2 = this;

      if (!this.presence) {
        return;
      }

      this.presence.statuses.push({
        id: Math.max(this.statusDefaults.length, Math.max.apply(null, this.presence.statuses.map(function (s) {
          return s.id;
        }))) + 1,
        type: 'custom',
        code: this.codeNew,
        title: this.titleNew,
        aliasses: this.aliasNew.aliasses,
        color: this.colorNew
      });
      this.resetNew();
      this.$nextTick(function () {
        _this2.selectedStatus = _this2.presenceStatuses[_this2.presenceStatuses.length - 1];
      });
    }
  }, {
    key: "onSelectStatus",
    value: function onSelectStatus(status) {
      if (!this.createNew) {
        this.selectedStatus = status;
      }
    }
  }, {
    key: "onMoveDown",
    value: function onMoveDown(id, index) {
      if (!this.presence || index >= this.presence.statuses.length - 1) {
        return;
      }

      var statuses = this.presence.statuses;
      this.presence.statuses = statuses.slice(0, index).concat(statuses[index + 1], statuses[index]).concat(statuses.slice(index + 2));
      this.$nextTick(function () {
        var _el, _el3;

        var el = document.querySelector("#btn-down-".concat(id));

        if ((_el = el) === null || _el === void 0 ? void 0 : _el.disabled) {
          var _el2;

          el = (_el2 = el) === null || _el2 === void 0 ? void 0 : _el2.previousSibling;
        }

        (_el3 = el) === null || _el3 === void 0 ? void 0 : _el3.focus();
      });
    }
  }, {
    key: "onMoveUp",
    value: function onMoveUp(id, index) {
      if (!this.presence || index <= 0) {
        return;
      }

      var statuses = this.presence.statuses;
      this.presence.statuses = statuses.slice(0, index - 1).concat(statuses[index], statuses[index - 1]).concat(statuses.slice(index + 1));
      this.$nextTick(function () {
        var _el4, _el6;

        var el = document.querySelector("#btn-up-".concat(id));

        if ((_el4 = el) === null || _el4 === void 0 ? void 0 : _el4.disabled) {
          var _el5;

          el = (_el5 = el) === null || _el5 === void 0 ? void 0 : _el5.nextSibling;
        }

        (_el6 = el) === null || _el6 === void 0 ? void 0 : _el6.focus();
      });
    }
  }, {
    key: "onRemove",
    value: function onRemove(status) {
      if (!this.presence || status.type === 'fixed') {
        return;
      }

      var statuses = this.presence.statuses;
      var index = statuses.findIndex(function (s) {
        return s === status;
      });

      if (index === -1) {
        return;
      }

      this.presence.statuses = statuses.slice(0, index).concat(statuses.slice(index + 1));
    }
  }, {
    key: "onSave",
    value: function onSave() {
      var _this$connector3,
          _this3 = this;

      if (!this.presence) {
        return;
      }

      if (this.hasEmptyFields()) {
        return;
      }

      this.setError(null);
      (_this$connector3 = this.connector) === null || _this$connector3 === void 0 ? void 0 : _this$connector3.updatePresence(this.presence.id, this.presenceStatuses, this.presence.has_checkout, function (data) {
        if ((data === null || data === void 0 ? void 0 : data.status) === 'ok') {
          _this3.$emit('presence-data-changed', {
            statusDefaults: _this3.statusDefaults,
            presence: _this3.presence
          });
        }
      });
    }
  }, {
    key: "_loadIndex",
    value: function _loadIndex() {
      this.loadSavedEntryStatuses();
    }
  }, {
    key: "isSaving",
    get: function get() {
      var _this$connector4;

      return ((_this$connector4 = this.connector) === null || _this$connector4 === void 0 ? void 0 : _this$connector4.isSaving) || false;
    }
  }, {
    key: "presenceStatuses",
    get: function get() {
      var _this$presence;

      return ((_this$presence = this.presence) === null || _this$presence === void 0 ? void 0 : _this$presence.statuses) || [];
    }
  }, {
    key: "fixedStatusDefaults",
    get: function get() {
      return this.statusDefaults.filter(function (sd) {
        return sd.type === 'fixed';
      });
    }
  }]);

  return Builder;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: APIConfig_APIConfig,
  required: true
})], Buildervue_type_script_lang_ts_Builder.prototype, "apiConfig", void 0);

__decorate([Prop({
  type: Number,
  "default": 0
})], Buildervue_type_script_lang_ts_Builder.prototype, "loadIndex", void 0);

__decorate([Watch('loadIndex')], Buildervue_type_script_lang_ts_Builder.prototype, "_loadIndex", null);

Buildervue_type_script_lang_ts_Builder = __decorate([vue_class_component_esm({
  components: {
    OnOffSwitch: components_OnOffSwitch,
    TitleControl: builder_TitleControl,
    AliasControl: builder_AliasControl,
    ColorControl: builder_ColorControl,
    SelectionControls: builder_SelectionControls,
    NewStatusControls: builder_NewStatusControls,
    SelectionPreview: builder_SelectionPreview,
    SaveControl: builder_SaveControl,
    ErrorDisplay: builder_ErrorDisplay
  }
})], Buildervue_type_script_lang_ts_Builder);
/* harmony default export */ var Buildervue_type_script_lang_ts_ = (Buildervue_type_script_lang_ts_Builder);
// CONCATENATED MODULE: ./src/components/Builder.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_Buildervue_type_script_lang_ts_ = (Buildervue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=0&lang=css&
var Buildervue_type_style_index_0_lang_css_ = __webpack_require__("df7c");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=1&id=642e4e7a&scoped=true&lang=css&
var Buildervue_type_style_index_1_id_642e4e7a_scoped_true_lang_css_ = __webpack_require__("68d3");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=2&lang=css&
var Buildervue_type_style_index_2_lang_css_ = __webpack_require__("4dfb");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=3&lang=scss&
var Buildervue_type_style_index_3_lang_scss_ = __webpack_require__("e34b");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=4&lang=css&
var Buildervue_type_style_index_4_lang_css_ = __webpack_require__("f01e");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=5&lang=css&
var Buildervue_type_style_index_5_lang_css_ = __webpack_require__("9a4e");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=style&index=6&lang=css&
var Buildervue_type_style_index_6_lang_css_ = __webpack_require__("4fee");

// EXTERNAL MODULE: ./src/components/Builder.vue?vue&type=custom&index=0&blockType=i18n
var Buildervue_type_custom_index_0_blockType_i18n = __webpack_require__("a55d");

// CONCATENATED MODULE: ./src/components/Builder.vue












/* normalize component */

var Builder_component = normalizeComponent(
  components_Buildervue_type_script_lang_ts_,
  render,
  staticRenderFns,
  false,
  null,
  "642e4e7a",
  null
  
)

/* custom blocks */

if (typeof Buildervue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(Buildervue_type_custom_index_0_blockType_i18n["default"])(Builder_component)

/* harmony default export */ var components_Builder = (Builder_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/Entry.vue?vue&type=template&id=b35f4f70&scoped=true&
var Entryvue_type_template_id_b35f4f70_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[(_vm.canEditPresence)?_c('div',{staticClass:"u-flex u-gap-small-3x m-controls",class:[_vm.selectedPeriod ? 'u-align-items-start': 'u-align-items-center']},[_c('search-bar',{attrs:{"search-options":_vm.searchOptions},on:{"filter-changed":_vm.onFilterChanged,"filter-cleared":_vm.onFilterCleared}}),(_vm.canEditPresence && !!_vm.selectedPeriod)?_c('div',{staticClass:"status-filters u-flex u-gap-small u-align-items-baseline"},[_vm._m(0),_vm._l((_vm.presenceStatuses),function(status,index){return _c('filter-status-button',{key:("status-" + index),attrs:{"title":_vm.getPresenceStatusTitle(status),"label":status.code,"color":status.color,"is-selected":_vm.statusFilters.indexOf(status) !== -1},on:{"toggle-filter":function($event){return _vm.toggleStatusFilters(status)}}})}),_c('filter-status-button',{attrs:{"label":_vm.$t('without-status'),"color":"grey-100","is-selected":_vm.withoutStatusSelected},on:{"toggle-filter":_vm.toggleWithoutStatus}})],2):_c('a',{staticClass:"btn btn-default btn-sm",attrs:{"href":_vm.apiConfig.exportURL}},[_vm._v(_vm._s(_vm.$t('export')))])],1):_vm._e(),_c('div',{staticClass:"u-flex",staticStyle:{"gap":"8px"}},[_c('div',{staticStyle:{"width":"max-content"}},[_c('div',{staticClass:"u-relative"},[(!_vm.canEditPresence)?_c('div',{staticClass:"u-flex u-align-items-baseline u-flex-wrap u-gap-small-3x m-legend"},[_c('span',{staticStyle:{"color":"#507177"}},[_vm._v(_vm._s(_vm.$t('legend'))+":")]),_vm._l((_vm.presenceStatuses),function(status){return _c('legend-item',{attrs:{"title":_vm.getPresenceStatusTitle(status),"label":status.code,"color":status.color}})})],2):_vm._e(),_c('entry-table',{style:(_vm.selectedPeriod ? 'margin-top: 3px' : ''),attrs:{"id":"course-students","items":_vm.itemsProvider,"periods":_vm.periods,"status-defaults":_vm.statusDefaults,"presence":_vm.presence,"selected-period":_vm.selectedPeriod,"can-edit-presence":_vm.canEditPresence,"global-search-query":_vm.globalSearchQuery,"pagination":_vm.pagination,"is-saving":_vm.isSaving,"checkout-mode":_vm.checkoutMode,"foot-clone":_vm.canEditPresence && !!_vm.selectedPeriod,"is-creating-new-period":_vm.creatingNew},on:{"create-period":_vm.createResultPeriod,"period-label-changed":_vm.setSelectedPeriodLabel,"select-student-status":_vm.setSelectedStudentStatus,"toggle-checkout-mode":function($event){_vm.checkoutMode = !_vm.checkoutMode},"toggle-checkout":_vm.toggleCheckout,"change-selected-period":_vm.setSelectedPeriod},scopedSlots:_vm._u([(_vm.canEditPresence)?{key:"entry-top",fn:function(){return [_c('div',{staticClass:"extra-actions u-flex u-align-items-baseline",staticStyle:{"gap":"15px","min-width":"100%","top":"-26px","justify-content":"space-between"}},[_c('a',{staticClass:"show-periods-btn",on:{"click":function($event){return _vm.setSelectedPeriod(null)}}},[_vm._v(_vm._s(_vm.$t('show-periods')))]),_c('a',{staticClass:"btn btn-default btn-sm",staticStyle:{"padding":"1px 4px 0 6px","margin-right":"8px"},attrs:{"id":"show-more"},on:{"click":function($event){_vm.showMore = !_vm.showMore}}},[_vm._v(_vm._s(_vm.$t('more'))+"…")]),_c('bulk-status-popup',{attrs:{"is-visible":_vm.showMore,"presence-statuses":_vm.presenceStatuses},on:{"apply":_vm.applyBulkStatus,"cancel":_vm.cancelBulkStatus}})],1)]},proxy:true}:null,(_vm.canEditPresence && !!_vm.selectedPeriod)?{key:"entry-foot",fn:function(){return [_c('button',{staticClass:"btn-remove",attrs:{"disabled":_vm.toRemovePeriod === _vm.selectedPeriod},on:{"click":_vm.removeSelectedPeriod}},[_vm._v(_vm._s(_vm.$t('remove-period')))])]},proxy:true}:null],null,true)}),(!_vm.creatingNew)?_c('div',{staticClass:"lds-ellipsis",attrs:{"aria-hidden":"true"}},[_c('div'),_c('div'),_c('div'),_c('div')]):_vm._e()],1),(_vm.canEditPresence && _vm.pageLoaded && _vm.pagination.total > 0)?_c('div',{staticClass:"pagination-container u-flex u-justify-content-end",style:(!!_vm.selectedPeriod ? 'margin-top: -20px': '')},[_c('b-pagination',{attrs:{"total-rows":_vm.pagination.total,"per-page":_vm.pagination.perPage,"aria-controls":"course-students","disabled":_vm.changeAfterStatusFilters},model:{value:(_vm.pagination.currentPage),callback:function ($$v) {_vm.$set(_vm.pagination, "currentPage", $$v)},expression:"pagination.currentPage"}}),_c('ul',{staticClass:"pagination"},[_c('li',{staticClass:"page-item",class:{active: !_vm.changeAfterStatusFilters, disabled: _vm.changeAfterStatusFilters}},[_c('a',{staticClass:"page-link",style:(_vm.changeAfterStatusFilters ? 'text-decoration: line-through' : '')},[_vm._v(_vm._s(_vm.$t('total'))+" "+_vm._s(_vm.pagination.total))])]),(_vm.changeAfterStatusFilters)?_c('li',{staticClass:"page-item active"},[_c('a',{directives:[{name:"b-popover",rawName:"v-b-popover.hover.right",value:(_vm.$t('changes-filters')),expression:"$t('changes-filters')",modifiers:{"hover":true,"right":true}}],staticClass:"page-link",staticStyle:{"cursor":"pointer"},on:{"click":_vm.refreshFilters}},[_vm._v(_vm._s(_vm.$t('refresh'))+" "),_c('i',{staticClass:"fa fa-info-circle"})])]):_vm._e()])],1):_vm._e()])]),(_vm.errorData)?_c('div',{staticClass:"alert alert-danger m-errors"},[(_vm.errorData.code === 500)?_c('span',[_vm._v(_vm._s(_vm.errorData.message))]):(!!_vm.errorData.type)?_c('span',[_vm._v(_vm._s(_vm.$t('error-' + _vm.errorData.type)))]):_vm._e()]):_vm._e(),(_vm.nonCourseStudents.length)?_c('h4',{staticStyle:{"color":"#507177","font-size":"14px","font-weight":"500","margin-top":"-5px","margin-bottom":"-10px"}},[_vm._v(_vm._s(_vm.$t('students-not-in-course')))]):_vm._e(),(_vm.nonCourseStudents.length)?_c('entry-table',{staticStyle:{"margin-top":"20px"},attrs:{"id":"non-course-students","items":_vm.nonCourseStudents,"selected-period":_vm.selectedPeriod,"periods":_vm.periods,"status-defaults":_vm.statusDefaults,"presence":_vm.presence,"can-edit-presence":_vm.canEditPresence,"has-non-course-students":true,"is-saving":_vm.isSavingNonCourse,"is-creating-new-period":_vm.creatingNew,"checkout-mode":_vm.checkoutMode},on:{"select-student-status":_vm.setSelectedStudentStatus,"toggle-checkout":_vm.toggleCheckout}}):_vm._e()],1)}
var Entryvue_type_template_id_b35f4f70_scoped_true_staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('span',{staticStyle:{"color":"#666","margin-right":"5px","width":"max-content"}},[_c('i',{staticClass:"fa fa-filter",staticStyle:{"margin-right":"2px"}}),_vm._v("Filters:")])}]


// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=template&id=b35f4f70&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/LegendItem.vue?vue&type=template&id=128fddd8&
var LegendItemvue_type_template_id_128fddd8_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"u-flex u-align-items-baseline u-gap-small"},[_c('div',{class:['color-code', _vm.color],staticStyle:{"height":"20px","padding":"2px 4px"}},[_c('span',[_vm._v(_vm._s(_vm.label))])]),_c('span',[_vm._v(_vm._s(_vm.title))])])}
var LegendItemvue_type_template_id_128fddd8_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/LegendItem.vue?vue&type=template&id=128fddd8&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/LegendItem.vue?vue&type=script&lang=ts&







var LegendItemvue_type_script_lang_ts_LegendItem =
/*#__PURE__*/
function (_Vue) {
  _inherits(LegendItem, _Vue);

  function LegendItem() {
    _classCallCheck(this, LegendItem);

    return _possibleConstructorReturn(this, _getPrototypeOf(LegendItem).apply(this, arguments));
  }

  return LegendItem;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  "default": ''
})], LegendItemvue_type_script_lang_ts_LegendItem.prototype, "label", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], LegendItemvue_type_script_lang_ts_LegendItem.prototype, "color", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], LegendItemvue_type_script_lang_ts_LegendItem.prototype, "title", void 0);

LegendItemvue_type_script_lang_ts_LegendItem = __decorate([vue_class_component_esm({
  name: 'legend-item'
})], LegendItemvue_type_script_lang_ts_LegendItem);
/* harmony default export */ var LegendItemvue_type_script_lang_ts_ = (LegendItemvue_type_script_lang_ts_LegendItem);
// CONCATENATED MODULE: ./src/components/entry/LegendItem.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_LegendItemvue_type_script_lang_ts_ = (LegendItemvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/entry/LegendItem.vue





/* normalize component */

var LegendItem_component = normalizeComponent(
  entry_LegendItemvue_type_script_lang_ts_,
  LegendItemvue_type_template_id_128fddd8_render,
  LegendItemvue_type_template_id_128fddd8_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var entry_LegendItem = (LegendItem_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/SearchBar.vue?vue&type=template&id=c42fd4f6&scoped=true&
var SearchBarvue_type_template_id_c42fd4f6_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"action-bar input-group"},[_c('b-form-input',{staticClass:"form-group action-bar-search shadow-none",attrs:{"type":"text","placeholder":_vm.$t('search'),"debounce":"750","autocomplete":"off"},on:{"input":function($event){return _vm.$emit('filter-changed')}},model:{value:(_vm.searchOptions.globalSearchQuery),callback:function ($$v) {_vm.$set(_vm.searchOptions, "globalSearchQuery", $$v)},expression:"searchOptions.globalSearchQuery"}}),_c('div',{staticClass:"input-group-btn"},[_c('button',{staticClass:"btn btn-default",attrs:{"name":"clear","value":"clear"},on:{"click":function($event){return _vm.$emit('filter-cleared')}}},[_c('span',{staticClass:"glyphicon glyphicon-remove",attrs:{"aria-hidden":"true"}})])])],1)}
var SearchBarvue_type_template_id_c42fd4f6_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue?vue&type=template&id=c42fd4f6&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/SearchBar.vue?vue&type=script&lang=ts&







var SearchBarvue_type_script_lang_ts_SearchBar =
/*#__PURE__*/
function (_Vue) {
  _inherits(SearchBar, _Vue);

  function SearchBar() {
    _classCallCheck(this, SearchBar);

    return _possibleConstructorReturn(this, _getPrototypeOf(SearchBar).apply(this, arguments));
  }

  return SearchBar;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object
})], SearchBarvue_type_script_lang_ts_SearchBar.prototype, "searchOptions", void 0);

SearchBarvue_type_script_lang_ts_SearchBar = __decorate([vue_class_component_esm({
  name: 'search-bar'
})], SearchBarvue_type_script_lang_ts_SearchBar);
/* harmony default export */ var SearchBarvue_type_script_lang_ts_ = (SearchBarvue_type_script_lang_ts_SearchBar);
// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_SearchBarvue_type_script_lang_ts_ = (SearchBarvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/entry/SearchBar.vue?vue&type=style&index=0&id=c42fd4f6&scoped=true&lang=css&
var SearchBarvue_type_style_index_0_id_c42fd4f6_scoped_true_lang_css_ = __webpack_require__("8a5f");

// EXTERNAL MODULE: ./src/components/entry/SearchBar.vue?vue&type=custom&index=0&blockType=i18n
var SearchBarvue_type_custom_index_0_blockType_i18n = __webpack_require__("cb81");

// CONCATENATED MODULE: ./src/components/entry/SearchBar.vue






/* normalize component */

var SearchBar_component = normalizeComponent(
  entry_SearchBarvue_type_script_lang_ts_,
  SearchBarvue_type_template_id_c42fd4f6_scoped_true_render,
  SearchBarvue_type_template_id_c42fd4f6_scoped_true_staticRenderFns,
  false,
  null,
  "c42fd4f6",
  null
  
)

/* custom blocks */

if (typeof SearchBarvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(SearchBarvue_type_custom_index_0_blockType_i18n["default"])(SearchBar_component)

/* harmony default export */ var entry_SearchBar = (SearchBar_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/DynamicFieldKey.vue?vue&type=template&id=788cf516&
var DynamicFieldKeyvue_type_template_id_788cf516_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return (_vm.isEditable)?_c('div',{attrs:{"role":"button","tabindex":"0"},on:{"keyup":function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter")){ return null; }return _vm.$emit('select')},"click":function($event){return _vm.$emit('select')}}},[_vm._t("default")],2):_c('div',{staticClass:"u-cursor-default u-text-center"},[_vm._t("default")],2)}
var DynamicFieldKeyvue_type_template_id_788cf516_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/DynamicFieldKey.vue?vue&type=template&id=788cf516&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/DynamicFieldKey.vue?vue&type=script&lang=ts&







var DynamicFieldKeyvue_type_script_lang_ts_SearchBar =
/*#__PURE__*/
function (_Vue) {
  _inherits(SearchBar, _Vue);

  function SearchBar() {
    _classCallCheck(this, SearchBar);

    return _possibleConstructorReturn(this, _getPrototypeOf(SearchBar).apply(this, arguments));
  }

  return SearchBar;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean
})], DynamicFieldKeyvue_type_script_lang_ts_SearchBar.prototype, "isEditable", void 0);

DynamicFieldKeyvue_type_script_lang_ts_SearchBar = __decorate([vue_class_component_esm({
  name: 'dynamic-field-key'
})], DynamicFieldKeyvue_type_script_lang_ts_SearchBar);
/* harmony default export */ var DynamicFieldKeyvue_type_script_lang_ts_ = (DynamicFieldKeyvue_type_script_lang_ts_SearchBar);
// CONCATENATED MODULE: ./src/components/entry/DynamicFieldKey.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_DynamicFieldKeyvue_type_script_lang_ts_ = (DynamicFieldKeyvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/entry/DynamicFieldKey.vue





/* normalize component */

var DynamicFieldKey_component = normalizeComponent(
  entry_DynamicFieldKeyvue_type_script_lang_ts_,
  DynamicFieldKeyvue_type_template_id_788cf516_render,
  DynamicFieldKeyvue_type_template_id_788cf516_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var DynamicFieldKey = (DynamicFieldKey_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=template&id=76b91b7e&scoped=true&
var EntryTablevue_type_template_id_76b91b7e_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('b-table',{ref:"table",staticClass:"mod-presence mod-entry",attrs:{"id":_vm.id,"foot-clone":_vm.footClone,"bordered":"","busy":_vm.isBusy,"items":_vm.items,"fields":_vm.fields,"sort-by":_vm.sortBy,"sort-desc":_vm.sortDesc,"per-page":_vm.pagination.perPage,"current-page":_vm.pagination.currentPage,"filter":_vm.globalSearchQuery,"no-sort-reset":""},on:{"update:busy":function($event){_vm.isBusy=$event},"update:sortBy":function($event){_vm.sortBy=$event},"update:sort-by":function($event){_vm.sortBy=$event},"update:sortDesc":function($event){_vm.sortDesc=$event},"update:sort-desc":function($event){_vm.sortDesc=$event}},scopedSlots:_vm._u([(_vm.canEditPresence)?{key:"cell(photo)",fn:function(ref){
var item = ref.item;
return [_c('img',{attrs:{"src":item.photo}})]}}:null,{key:"foot(photo)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true},(_vm.isFullyEditable)?{key:"head(fullname)",fn:function(){return [_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('lastname')},on:{"click":function($event){return _vm.sortByNameField('lastname')}}},[_vm._v(_vm._s(_vm.$t('last-name')))]),_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('firstname')},on:{"click":function($event){return _vm.sortByNameField('firstname')}}},[_vm._v(_vm._s(_vm.$t('first-name')))])]},proxy:true}:(_vm.canEditPresence)?{key:"head(fullname)",fn:function(){return [_vm._v(_vm._s(_vm.$t('last-name'))+", "+_vm._s(_vm.$t('first-name')))]},proxy:true}:{key:"head(fullname)",fn:function(){return [_vm._v("Student")]},proxy:true},{key:"foot(fullname)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true},{key:"cell(fullname)",fn:function(ref){
var item = ref.item;
return [(!item.tableEmpty)?_c('span',[_vm._v(_vm._s(item.lastname.toUpperCase())+", "+_vm._s(item.firstname))]):_c('span')]}},(_vm.isFullyEditable)?{key:"head(official_code)",fn:function(){return [_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('official_code')},on:{"click":function($event){return _vm.sortByNameField('official_code')}}},[_vm._v(_vm._s(_vm.$t('official-code')))])]},proxy:true}:{key:"head(official_code)",fn:function(){return [_vm._v(_vm._s(_vm.$t('official-code')))]},proxy:true},{key:"foot(official_code)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true},{key:"head(new_period)",fn:function(){return [_c('div',{staticClass:"select-period-btn mod-new",attrs:{"role":"button","tabindex":"0","title":_vm.$t('new-period')},on:{"keyup":function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter")){ return null; }return _vm.createPeriod($event)},"click":_vm.createPeriod}},[_c('i',{staticClass:"fa fa-plus",staticStyle:{"color":"#337ab7"},attrs:{"aria-hidden":"true"}}),_vm._v(" "),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('new-period')))])])]},proxy:true},{key:"foot(new_period)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true},{key:"head(period-entry-plh)",fn:function(){return [_c('div',{staticClass:"u-flex u-align-items-center u-gap-small"},[_c('b-input',{staticClass:"u-bg-none u-font-normal u-font-italic u-pointer-events-none ti-label mod-border",attrs:{"type":"text","autocomplete":"off","placeholder":_vm.$t('new-period') + '...'}}),_c('div',{staticClass:"spin"},[(_vm.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin"}):_vm._e()])],1)]},proxy:true},{key:"cell(period-entry-plh)",fn:function(){return [_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap u-pointer-events-none"},_vm._l((_vm.presenceStatuses),function(status,index){return _c('button',{key:("status-" + index),staticClass:"color-code mod-plh",class:[status.color]},[_c('span',[_vm._v(_vm._s(status.code))])])}),0)]},proxy:true},_vm._l((_vm.dynamicFieldKeys),function(fieldKey){return {key:("head(" + (fieldKey.key) + ")"),fn:function(ref){
var label = ref.label;
return [_c('dynamic-field-key',{class:[{'select-period-btn' : _vm.isFullyEditable}, 'u-txt-truncate'],attrs:{"is-editable":_vm.isFullyEditable,"title":label},on:{"select":function($event){return _vm.$emit('change-selected-period', fieldKey.id)}},scopedSlots:_vm._u([{key:"default",fn:function(){return [(label)?_c('span',[_vm._v(_vm._s(label))]):_c('span',{staticClass:"u-font-italic"},[_vm._v(_vm._s(_vm.getPlaceHolder(fieldKey.id)))])]},proxy:true}],null,true)})]}}}),_vm._l((_vm.dynamicFieldKeys),function(fieldKey){return {key:("cell(" + (fieldKey.key) + ")"),fn:function(ref){
var item = ref.item;
return [_c('PresenceStatusDisplay',{attrs:{"title":_vm.getStatusTitleForStudent(item, fieldKey.id),"label":_vm.getStatusCodeForStudent(item, fieldKey.id),"color":_vm.getStatusColorForStudent(item, fieldKey.id),"has-checkout":_vm.presence && _vm.presence.has_checkout,"check-in-date":item[("period#" + (fieldKey.id) + "-checked_in_date")],"check-out-date":item[("period#" + (fieldKey.id) + "-checked_out_date")]}})]}}}),_vm._l((_vm.dynamicFieldKeys),function(fieldKey){return {key:("foot(" + (fieldKey.key) + ")"),fn:function(){return [_vm._v(_vm._s(''))]},proxy:true}}),{key:"head(period-entry)",fn:function(){return [_c('div',{staticClass:"u-flex u-align-items-center u-gap-small"},[(_vm.isFullyEditable)?_c('b-input',{staticClass:"u-font-normal ti-label",attrs:{"type":"text","debounce":"750","autocomplete":"off","placeholder":_vm.getPlaceHolder(_vm.selectedPeriod.id)},model:{value:(_vm.selectedPeriodLabel),callback:function ($$v) {_vm.selectedPeriodLabel=$$v},expression:"selectedPeriodLabel"}}):(_vm.selectedPeriodLabel)?_c('span',{staticStyle:{"width":"100%"}},[_vm._v(_vm._s(_vm.selectedPeriodLabel))]):_c('span',{staticClass:"u-font-italic",staticStyle:{"width":"100%"}},[_vm._v(_vm._s(_vm.getPlaceHolder(_vm.selectedPeriod.id)))]),_c('div',{staticClass:"spin"},[(_vm.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin"}):_vm._e()])],1),_vm._t("entry-top")]},proxy:true},{key:"cell(period-entry)",fn:function(ref){
var item = ref.item;
return [(item.tableEmpty)?_c('div',{staticClass:"u-font-italic"},[_vm._v(_vm._s(_vm.$t('no-results')))]):_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap"},_vm._l((_vm.presenceStatuses),function(status,index){return _c('presence-status-button',{key:("status-" + index),attrs:{"status":status,"title":_vm.getPresenceStatusTitle(status),"is-selected":_vm.hasSelectedStudentStatus(item, status.id),"is-disabled":_vm.checkoutMode},on:{"select":function($event){return _vm.$emit('select-student-status', item, _vm.selectedPeriod, status.id, _vm.hasNonCourseStudents)}}})}),1)]}},{key:"foot(period-entry)",fn:function(){return [_vm._t("entry-foot")]},proxy:true},{key:"head(period-checkout)",fn:function(){return [(_vm.isFullyEditable)?_c('on-off-switch',{attrs:{"id":("checkout-" + _vm.id),"switch-class":"mod-checkout-choice","on-text":_vm.$t('checkout-mode'),"off-text":_vm.$t('checkout-mode'),"checked":_vm.checkoutMode},on:{"toggle":function($event){return _vm.$emit('toggle-checkout-mode')}}}):_c('span')]},proxy:true},(_vm.checkoutMode)?{key:"cell(period-checkout)",fn:function(ref){
var item = ref.item;
return [(item.tableEmpty)?_c('span'):(item[("period#" + (_vm.selectedPeriod.id) + "-checked_in_date")])?_c('on-off-switch',{attrs:{"id":item.id,"on-text":_vm.$t('checked-out'),"off-text":_vm.$t('not-checked-out'),"checked":item[("period#" + (_vm.selectedPeriod.id) + "-checked_out_date")] > item[("period#" + (_vm.selectedPeriod.id) + "-checked_in_date")],"switch-class":"mod-checkout"},on:{"toggle":function($event){return _vm.$emit('toggle-checkout', item, _vm.selectedPeriod, _vm.hasNonCourseStudents)}}}):_c('span',{staticStyle:{"color":"#999"}},[_vm._v(_vm._s(_vm.$t('not-applicable')))])]}}:{key:"cell(period-checkout)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true},{key:"foot(period-checkout)",fn:function(){return [_vm._v(_vm._s(''))]},proxy:true}],null,true)})}
var EntryTablevue_type_template_id_76b91b7e_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue?vue&type=template&id=76b91b7e&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/PresenceStatusButton.vue?vue&type=template&id=52f9435b&
var PresenceStatusButtonvue_type_template_id_52f9435b_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('button',{staticClass:"color-code",class:[_vm.isActive ? _vm.status.color : 'mod-disabled', {'mod-selectable': _vm.isActive, 'mod-shadow-grey': _vm.isActive && !_vm.isSelected, 'mod-shadow is-selected': _vm.isSelected}],attrs:{"title":_vm.isActive ? _vm.title : '',"disabled":_vm.isDisabled,"aria-pressed":_vm.isSelected ? 'true': 'false'},on:{"click":_vm.select}},[_c('span',[_vm._v(_vm._s(_vm.status.code))])])}
var PresenceStatusButtonvue_type_template_id_52f9435b_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/PresenceStatusButton.vue?vue&type=template&id=52f9435b&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/PresenceStatusButton.vue?vue&type=script&lang=ts&








var PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton =
/*#__PURE__*/
function (_Vue) {
  _inherits(PresenceStatusButton, _Vue);

  function PresenceStatusButton() {
    _classCallCheck(this, PresenceStatusButton);

    return _possibleConstructorReturn(this, _getPrototypeOf(PresenceStatusButton).apply(this, arguments));
  }

  _createClass(PresenceStatusButton, [{
    key: "select",
    value: function select() {
      if (!this.isSelected) {
        this.$emit('select');
      }
    }
  }, {
    key: "isActive",
    get: function get() {
      return this.isSelected || !this.isDisabled;
    }
  }]);

  return PresenceStatusButton;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean
})], PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton.prototype, "isSelected", void 0);

__decorate([Prop({
  type: Boolean
})], PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton.prototype, "isDisabled", void 0);

__decorate([Prop({
  type: Object,
  required: true
})], PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton.prototype, "status", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton.prototype, "title", void 0);

PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton = __decorate([vue_class_component_esm({
  name: 'presence-status-button'
})], PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton);
/* harmony default export */ var PresenceStatusButtonvue_type_script_lang_ts_ = (PresenceStatusButtonvue_type_script_lang_ts_PresenceStatusButton);
// CONCATENATED MODULE: ./src/components/entry/PresenceStatusButton.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_PresenceStatusButtonvue_type_script_lang_ts_ = (PresenceStatusButtonvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/entry/PresenceStatusButton.vue





/* normalize component */

var PresenceStatusButton_component = normalizeComponent(
  entry_PresenceStatusButtonvue_type_script_lang_ts_,
  PresenceStatusButtonvue_type_template_id_52f9435b_render,
  PresenceStatusButtonvue_type_template_id_52f9435b_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var entry_PresenceStatusButton = (PresenceStatusButton_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=template&id=fec6cd52&
var PresenceStatusDisplayvue_type_template_id_fec6cd52_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"result-wrap",class:{'u-flex': _vm.showCheckout, 'u-align-items-center': _vm.showCheckout}},[_c('div',{staticClass:"color-code u-cursor-default",class:[_vm.color || 'mod-none'],attrs:{"title":_vm.title}},[_c('span',[_vm._v(_vm._s(_vm.label))])]),(_vm.showCheckout)?[_c('i',{staticClass:"fa fa-sign-out checkout-indicator",class:{'is-checked-out': _vm.isCheckedOut },attrs:{"aria-hidden":"true","title":_vm.$t(_vm.isCheckedOut ? 'checked-out' : 'not-checked-out')}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t(_vm.isCheckedOut ? 'checked-out' : 'not-checked-out')))])]:_vm._e()],2)}
var PresenceStatusDisplayvue_type_template_id_fec6cd52_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=template&id=fec6cd52&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/PresenceStatusDisplay.vue?vue&type=script&lang=ts&









var PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay =
/*#__PURE__*/
function (_Vue) {
  _inherits(PresenceStatusDisplay, _Vue);

  function PresenceStatusDisplay() {
    _classCallCheck(this, PresenceStatusDisplay);

    return _possibleConstructorReturn(this, _getPrototypeOf(PresenceStatusDisplay).apply(this, arguments));
  }

  _createClass(PresenceStatusDisplay, [{
    key: "showCheckout",
    get: function get() {
      return this.hasCheckout && !!this.checkInDate;
    }
  }, {
    key: "isCheckedOut",
    get: function get() {
      if (typeof this.checkOutDate !== 'number' || typeof this.checkInDate !== 'number') {
        return false;
      }

      return this.checkOutDate > this.checkInDate;
    }
  }]);

  return PresenceStatusDisplay;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean,
  "default": false
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay.prototype, "hasCheckout", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay.prototype, "title", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay.prototype, "label", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay.prototype, "color", void 0);

__decorate([Prop({
  type: Number,
  "default": 0
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay.prototype, "checkInDate", void 0);

__decorate([Prop({
  type: Number,
  "default": 0
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay.prototype, "checkOutDate", void 0);

PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay = __decorate([vue_class_component_esm({
  name: 'presence-status-display'
})], PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay);
/* harmony default export */ var PresenceStatusDisplayvue_type_script_lang_ts_ = (PresenceStatusDisplayvue_type_script_lang_ts_PresenceStatusDisplay);
// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_PresenceStatusDisplayvue_type_script_lang_ts_ = (PresenceStatusDisplayvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/entry/PresenceStatusDisplay.vue?vue&type=custom&index=0&blockType=i18n
var PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n = __webpack_require__("efe6");

// CONCATENATED MODULE: ./src/components/entry/PresenceStatusDisplay.vue





/* normalize component */

var PresenceStatusDisplay_component = normalizeComponent(
  entry_PresenceStatusDisplayvue_type_script_lang_ts_,
  PresenceStatusDisplayvue_type_template_id_fec6cd52_render,
  PresenceStatusDisplayvue_type_template_id_fec6cd52_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* custom blocks */

if (typeof PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(PresenceStatusDisplayvue_type_custom_index_0_blockType_i18n["default"])(PresenceStatusDisplay_component)

/* harmony default export */ var entry_PresenceStatusDisplay = (PresenceStatusDisplay_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/EntryTable.vue?vue&type=script&lang=ts&


















var EntryTablevue_type_script_lang_ts_EntryTable =
/*#__PURE__*/
function (_Vue) {
  _inherits(EntryTable, _Vue);

  function EntryTable() {
    var _this;

    _classCallCheck(this, EntryTable);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(EntryTable).apply(this, arguments));
    _this.sortBy = 'lastname';
    _this.sortDesc = false;
    _this.isBusy = false;
    return _this;
  }

  _createClass(EntryTable, [{
    key: "getSortStatus",
    value: function getSortStatus(name) {
      if (this.sortBy !== name) {
        return 'none';
      }

      return this.sortDesc ? 'descending' : 'ascending';
    }
  }, {
    key: "sortByNameField",
    value: function sortByNameField(namefield) {
      if (this.sortBy === namefield) {
        this.sortDesc = !this.sortDesc;
        return;
      }

      this.sortBy = namefield;
      this.sortDesc = false;
    }
  }, {
    key: "createPeriod",
    value: function createPeriod() {
      var _this2 = this;

      this.$emit('create-period', function () {
        _this2.$refs.table.refresh();
      });
    }
  }, {
    key: "getPlaceHolder",
    value: function getPlaceHolder(periodId) {
      return "P".concat(this.periods.findIndex(function (p) {
        return p.id === periodId;
      }) + 1);
    }
  }, {
    key: "getStudentStatusForPeriod",
    value: function getStudentStatusForPeriod(student, periodId) {
      return student["period#".concat(periodId, "-status")];
    }
  }, {
    key: "getPresenceStatusTitle",
    value: function getPresenceStatusTitle(status) {
      if (status.type !== 'custom') {
        var _this$statusDefaults$;

        return ((_this$statusDefaults$ = this.statusDefaults.find(function (statusDefault) {
          return statusDefault.id === status.id;
        })) === null || _this$statusDefaults$ === void 0 ? void 0 : _this$statusDefaults$.title) || '';
      }

      return status.title || '';
    }
  }, {
    key: "getPresenceStatus",
    value: function getPresenceStatus(statusId) {
      return this.presenceStatuses.find(function (status) {
        return status.id === statusId;
      });
    }
  }, {
    key: "hasSelectedStudentStatus",
    value: function hasSelectedStudentStatus(student, status) {
      if (!this.selectedPeriod) {
        return false;
      }

      return this.getStudentStatusForPeriod(student, this.selectedPeriod.id) === status;
    }
  }, {
    key: "getStatusCodeForStudent",
    value: function getStatusCodeForStudent(student) {
      var _this$getPresenceStat;

      var periodId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;

      if (periodId === undefined) {
        if (!this.selectedPeriod) {
          return '';
        }

        periodId = this.selectedPeriod.id;
      }

      return ((_this$getPresenceStat = this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId))) === null || _this$getPresenceStat === void 0 ? void 0 : _this$getPresenceStat.code) || '';
    }
  }, {
    key: "getStatusColorForStudent",
    value: function getStatusColorForStudent(student) {
      var _this$getPresenceStat2;

      var periodId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;

      if (periodId === undefined) {
        if (!this.selectedPeriod) {
          return '';
        }

        periodId = this.selectedPeriod.id;
      }

      return ((_this$getPresenceStat2 = this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId))) === null || _this$getPresenceStat2 === void 0 ? void 0 : _this$getPresenceStat2.color) || '';
    }
  }, {
    key: "getStatusTitleForStudent",
    value: function getStatusTitleForStudent(student) {
      var periodId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;

      if (periodId === undefined) {
        if (!this.selectedPeriod) {
          return '';
        }

        periodId = this.selectedPeriod.id;
      }

      var status = this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId));
      return status ? this.getPresenceStatusTitle(status) : '';
    }
  }, {
    key: "created",
    value: function created() {
      var _this3 = this;

      this.$parent.$on('refresh', function () {
        if (_this3.isFullyEditable) {
          _this3.$refs.table.refresh();
        }
      });
    }
  }, {
    key: "hasResults",
    get: function get() {
      return this.pagination.total !== 0;
    }
  }, {
    key: "isFullyEditable",
    get: function get() {
      return this.canEditPresence && !this.hasNonCourseStudents;
    }
  }, {
    key: "periodsReversed",
    get: function get() {
      var periods = _toConsumableArray(this.periods);

      periods.reverse();
      return periods;
    }
  }, {
    key: "dynamicFieldKeys",
    get: function get() {
      return this.periods.map(function (period) {
        return {
          key: "period#".concat(period.id),
          id: period.id
        };
      });
    }
  }, {
    key: "selectedPeriodLabel",
    get: function get() {
      var _this$selectedPeriod;

      return ((_this$selectedPeriod = this.selectedPeriod) === null || _this$selectedPeriod === void 0 ? void 0 : _this$selectedPeriod.label) || '';
    },
    set: function set(label) {
      if (!this.selectedPeriod) {
        return;
      }

      this.$emit('period-label-changed', this.selectedPeriod, label);
    }
  }, {
    key: "presenceStatuses",
    get: function get() {
      var _this$presence;

      return ((_this$presence = this.presence) === null || _this$presence === void 0 ? void 0 : _this$presence.statuses) || [];
    }
  }, {
    key: "userFields",
    get: function get() {
      return [this.canEditPresence ? {
        key: 'photo',
        sortable: false,
        label: '',
        variant: 'photo'
      } : null, {
        key: 'fullname',
        sortable: false,
        label: 'Student'
      }, {
        key: 'official_code',
        sortable: false
      }];
    }
  }, {
    key: "periodFields",
    get: function get() {
      if (this.isCreatingNewPeriod) {
        return [];
      }

      if (this.canEditPresence && !!this.selectedPeriod) {
        return [{
          key: 'period-entry',
          sortable: false,
          label: this.selectedPeriod.label,
          variant: 'period'
        }, this.presence && this.presence.has_checkout && (this.isFullyEditable || this.checkoutMode) ? {
          key: 'period-checkout',
          sortable: false,
          variant: 'checkout'
        } : null];
      }

      var periodFields = this.periods.map(function (period) {
        return {
          key: "period#".concat(period.id),
          sortable: false,
          label: period.label || '',
          variant: 'result'
        };
      });
      periodFields.reverse();
      return periodFields;
    }
  }, {
    key: "fields",
    get: function get() {
      return [].concat(_toConsumableArray(this.userFields), [this.canEditPresence && !this.selectedPeriod && !this.hasNonCourseStudents && !this.isCreatingNewPeriod ? {
        key: 'new_period',
        sortable: false,
        label: ''
      } : null, this.isCreatingNewPeriod ? {
        key: 'period-entry-plh',
        sortable: false,
        variant: 'period'
      } : null], _toConsumableArray(this.periodFields));
    }
  }]);

  return EntryTable;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  "default": ''
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "id", void 0);

__decorate([Prop()], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "items", void 0);

__decorate([Prop({
  type: Object,
  "default": null
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "selectedPeriod", void 0);

__decorate([Prop({
  type: Object,
  "default": function _default() {
    return {
      perPage: 0,
      currentPage: 0,
      total: 0
    };
  }
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "pagination", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "isSaving", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "footClone", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "checkoutMode", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "globalSearchQuery", void 0);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "statusDefaults", void 0);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "periods", void 0);

__decorate([Prop({
  type: Object,
  "default": null
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "presence", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "canEditPresence", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "hasNonCourseStudents", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], EntryTablevue_type_script_lang_ts_EntryTable.prototype, "isCreatingNewPeriod", void 0);

EntryTablevue_type_script_lang_ts_EntryTable = __decorate([vue_class_component_esm({
  name: 'entry-table',
  components: {
    PresenceStatusDisplay: entry_PresenceStatusDisplay,
    PresenceStatusButton: entry_PresenceStatusButton,
    OnOffSwitch: components_OnOffSwitch,
    DynamicFieldKey: DynamicFieldKey
  }
})], EntryTablevue_type_script_lang_ts_EntryTable);
/* harmony default export */ var EntryTablevue_type_script_lang_ts_ = (EntryTablevue_type_script_lang_ts_EntryTable);
// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_EntryTablevue_type_script_lang_ts_ = (EntryTablevue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/entry/EntryTable.vue?vue&type=style&index=0&id=76b91b7e&scoped=true&lang=css&
var EntryTablevue_type_style_index_0_id_76b91b7e_scoped_true_lang_css_ = __webpack_require__("8bce");

// EXTERNAL MODULE: ./src/components/entry/EntryTable.vue?vue&type=custom&index=0&blockType=i18n
var EntryTablevue_type_custom_index_0_blockType_i18n = __webpack_require__("5091");

// CONCATENATED MODULE: ./src/components/entry/EntryTable.vue






/* normalize component */

var EntryTable_component = normalizeComponent(
  entry_EntryTablevue_type_script_lang_ts_,
  EntryTablevue_type_template_id_76b91b7e_scoped_true_render,
  EntryTablevue_type_template_id_76b91b7e_scoped_true_staticRenderFns,
  false,
  null,
  "76b91b7e",
  null
  
)

/* custom blocks */

if (typeof EntryTablevue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(EntryTablevue_type_custom_index_0_blockType_i18n["default"])(EntryTable_component)

/* harmony default export */ var entry_EntryTable = (EntryTable_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/FilterStatusButton.vue?vue&type=template&id=2cf36b0e&
var FilterStatusButtonvue_type_template_id_2cf36b0e_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('button',{staticClass:"color-code mod-selectable",class:[_vm.color, {'mod-off': !_vm.isSelected, 'is-selected': _vm.isSelected}],attrs:{"title":_vm.title,"aria-pressed":_vm.isSelected ? 'true': 'false'},on:{"click":function($event){return _vm.$emit('toggle-filter')}}},[_c('span',[_vm._v(_vm._s(_vm.label))])])}
var FilterStatusButtonvue_type_template_id_2cf36b0e_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/FilterStatusButton.vue?vue&type=template&id=2cf36b0e&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/FilterStatusButton.vue?vue&type=script&lang=ts&







var FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton =
/*#__PURE__*/
function (_Vue) {
  _inherits(FilterStatusButton, _Vue);

  function FilterStatusButton() {
    _classCallCheck(this, FilterStatusButton);

    return _possibleConstructorReturn(this, _getPrototypeOf(FilterStatusButton).apply(this, arguments));
  }

  return FilterStatusButton;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean
})], FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton.prototype, "isSelected", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton.prototype, "title", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton.prototype, "label", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton.prototype, "color", void 0);

FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton = __decorate([vue_class_component_esm({
  name: 'filter-status-button'
})], FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton);
/* harmony default export */ var FilterStatusButtonvue_type_script_lang_ts_ = (FilterStatusButtonvue_type_script_lang_ts_FilterStatusButton);
// CONCATENATED MODULE: ./src/components/entry/FilterStatusButton.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_FilterStatusButtonvue_type_script_lang_ts_ = (FilterStatusButtonvue_type_script_lang_ts_); 
// CONCATENATED MODULE: ./src/components/entry/FilterStatusButton.vue





/* normalize component */

var FilterStatusButton_component = normalizeComponent(
  entry_FilterStatusButtonvue_type_script_lang_ts_,
  FilterStatusButtonvue_type_template_id_2cf36b0e_render,
  FilterStatusButtonvue_type_template_id_2cf36b0e_staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var entry_FilterStatusButton = (FilterStatusButton_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"3905bc8e-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/BulkStatusPopup.vue?vue&type=template&id=61728bf2&scoped=true&
var BulkStatusPopupvue_type_template_id_61728bf2_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('b-popover',{attrs:{"target":"show-more","show":_vm.isVisible,"triggers":"click","placement":"bottom","custom-class":"bulk-status"},on:{"update:show":function($event){_vm.isVisible=$event}}},[_c('div',{staticClass:"p-8"},[_c('div',{staticClass:"u-flex u-justify-content-center msg-text mb-6"},[_vm._v(_vm._s(_vm.$t('set-students-without-status'))+":")]),_c('div',{staticClass:"u-flex u-gap-small u-flex-wrap mb-12"},_vm._l((_vm.presenceStatuses),function(status,index){return _c('button',{key:("status-" + index),staticClass:"color-code mod-selectable",class:[status.color, _vm.selectedStatus === status ? 'mod-shadow is-selected' : 'mod-shadow-grey'],on:{"click":function($event){_vm.selectedStatus = status}}},[_c('span',[_vm._v(_vm._s(status.code))])])}),0),_c('div',{staticClass:"u-flex u-gap-small u-justify-content-end"},[_c('button',{staticClass:"btn btn-primary btn-sm px-8 py-2",attrs:{"disabled":_vm.selectedStatus === null},on:{"click":_vm.apply}},[_vm._v(_vm._s(_vm.$t('apply')))]),_c('button',{staticClass:"btn btn-default btn-sm px-8 py-2",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])])}
var BulkStatusPopupvue_type_template_id_61728bf2_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/entry/BulkStatusPopup.vue?vue&type=template&id=61728bf2&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/entry/BulkStatusPopup.vue?vue&type=script&lang=ts&








var BulkStatusPopupvue_type_script_lang_ts_BulkStatusPopup =
/*#__PURE__*/
function (_Vue) {
  _inherits(BulkStatusPopup, _Vue);

  function BulkStatusPopup() {
    var _this;

    _classCallCheck(this, BulkStatusPopup);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(BulkStatusPopup).apply(this, arguments));
    _this.selectedStatus = null;
    return _this;
  }

  _createClass(BulkStatusPopup, [{
    key: "apply",
    value: function apply() {
      this.$emit('apply', this.selectedStatus);
      this.selectedStatus = null;
    }
  }, {
    key: "cancel",
    value: function cancel() {
      this.$emit('cancel');
      this.selectedStatus = null;
    }
  }]);

  return BulkStatusPopup;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Boolean,
  "default": false
})], BulkStatusPopupvue_type_script_lang_ts_BulkStatusPopup.prototype, "isVisible", void 0);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], BulkStatusPopupvue_type_script_lang_ts_BulkStatusPopup.prototype, "presenceStatuses", void 0);

BulkStatusPopupvue_type_script_lang_ts_BulkStatusPopup = __decorate([vue_class_component_esm({
  name: 'bulk-status-popup'
})], BulkStatusPopupvue_type_script_lang_ts_BulkStatusPopup);
/* harmony default export */ var BulkStatusPopupvue_type_script_lang_ts_ = (BulkStatusPopupvue_type_script_lang_ts_BulkStatusPopup);
// CONCATENATED MODULE: ./src/components/entry/BulkStatusPopup.vue?vue&type=script&lang=ts&
 /* harmony default export */ var entry_BulkStatusPopupvue_type_script_lang_ts_ = (BulkStatusPopupvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/entry/BulkStatusPopup.vue?vue&type=style&index=0&lang=css&
var BulkStatusPopupvue_type_style_index_0_lang_css_ = __webpack_require__("6098");

// EXTERNAL MODULE: ./src/components/entry/BulkStatusPopup.vue?vue&type=style&index=1&id=61728bf2&scoped=true&lang=css&
var BulkStatusPopupvue_type_style_index_1_id_61728bf2_scoped_true_lang_css_ = __webpack_require__("d24f");

// EXTERNAL MODULE: ./src/components/entry/BulkStatusPopup.vue?vue&type=custom&index=0&blockType=i18n
var BulkStatusPopupvue_type_custom_index_0_blockType_i18n = __webpack_require__("a9f2");

// CONCATENATED MODULE: ./src/components/entry/BulkStatusPopup.vue







/* normalize component */

var BulkStatusPopup_component = normalizeComponent(
  entry_BulkStatusPopupvue_type_script_lang_ts_,
  BulkStatusPopupvue_type_template_id_61728bf2_scoped_true_render,
  BulkStatusPopupvue_type_template_id_61728bf2_scoped_true_staticRenderFns,
  false,
  null,
  "61728bf2",
  null
  
)

/* custom blocks */

if (typeof BulkStatusPopupvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(BulkStatusPopupvue_type_custom_index_0_blockType_i18n["default"])(BulkStatusPopup_component)

/* harmony default export */ var entry_BulkStatusPopup = (BulkStatusPopup_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/Entry.vue?vue&type=script&lang=ts&



























var Entryvue_type_script_lang_ts_Entry =
/*#__PURE__*/
function (_Vue) {
  _inherits(Entry, _Vue);

  function Entry() {
    var _this;

    _classCallCheck(this, Entry);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Entry).apply(this, arguments));
    _this.statusDefaults = [];
    _this.presence = null;
    _this.connector = null;
    _this.connectorNonCourse = null;
    _this.periods = [];
    _this.toRemovePeriod = null;
    _this.students = [];
    _this.nonCourseStudents = [];
    _this.creatingNew = false;
    _this.createdId = null;
    _this.pageLoaded = false;
    _this.errorData = null;
    _this.statusFilters = [];
    _this.withoutStatusSelected = false;
    _this.checkoutMode = false;
    _this.showMore = false;
    _this.pagination = {
      currentPage: 1,
      perPage: 15,
      total: 0
    };
    _this.searchOptions = {
      globalSearchQuery: ''
    };
    _this.requestCount = true;
    _this.requestNonCourseStudents = true;
    _this.selectedPeriod = null;
    _this.changeAfterStatusFilters = false;
    return _this;
  }

  _createClass(Entry, [{
    key: "toggleStatusFilters",
    value: function toggleStatusFilters(status) {
      var statusFilters = this.statusFilters;
      var index = statusFilters.indexOf(status);

      if (index === -1) {
        this.statusFilters.push(status);
      } else {
        this.statusFilters = statusFilters.slice(0, index).concat(statusFilters.slice(index + 1));
      }

      this.requestCount = true;
      this.$emit('refresh');
    }
  }, {
    key: "refreshFilters",
    value: function refreshFilters() {
      this.requestCount = true;
      this.$emit('refresh');
    }
  }, {
    key: "toggleWithoutStatus",
    value: function toggleWithoutStatus() {
      this.withoutStatusSelected = !this.withoutStatusSelected;
      this.requestCount = true;
      this.$emit('refresh');
    }
  }, {
    key: "onFilterChanged",
    value: function onFilterChanged() {
      this.requestCount = true;
    }
  }, {
    key: "onFilterCleared",
    value: function onFilterCleared() {
      if (this.globalSearchQuery !== '') {
        this.globalSearchQuery = '';
        this.requestCount = true;
      }
    }
  }, {
    key: "getPresenceStatusTitle",
    value: function getPresenceStatusTitle(status) {
      if (status.type !== 'custom') {
        var _this$statusDefaults$;

        return ((_this$statusDefaults$ = this.statusDefaults.find(function (statusDefault) {
          return statusDefault.id === status.id;
        })) === null || _this$statusDefaults$ === void 0 ? void 0 : _this$statusDefaults$.title) || '';
      }

      return status.title || '';
    }
  }, {
    key: "applyBulkStatus",
    value: function applyBulkStatus(status) {
      var _this$connector,
          _this2 = this;

      this.showMore = false;

      if (!this.canEditPresence || !this.selectedPeriod) {
        return;
      }

      this.errorData = null;
      (_this$connector = this.connector) === null || _this$connector === void 0 ? void 0 : _this$connector.bulkSavePresenceEntries(this.selectedPeriod.id, status.id, function (data) {
        if ((data === null || data === void 0 ? void 0 : data.status) === 'ok') {
          _this2.$emit('refresh');
        }
      });
    }
  }, {
    key: "cancelBulkStatus",
    value: function cancelBulkStatus() {
      this.showMore = false;
    }
  }, {
    key: "load",
    value: function () {
      var _load = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        var _this$connector2;

        var presenceData;
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return (_this$connector2 = this.connector) === null || _this$connector2 === void 0 ? void 0 : _this$connector2.loadPresence();

              case 2:
                presenceData = _context.sent;

                if (presenceData) {
                  this.statusDefaults = presenceData['status-defaults'];
                  this.presence = presenceData.presence;
                }

              case 4:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function load() {
        return _load.apply(this, arguments);
      }

      return load;
    }()
  }, {
    key: "itemsProvider",
    value: function () {
      var _itemsProvider = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2(ctx) {
        var _this$connector3;

        var parameters, data, periods, students, selectedPeriod;
        return regeneratorRuntime.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                parameters = {
                  global_search_query: ctx.filter,
                  sort_field: ctx.sortBy,
                  sort_direction: ctx.sortDesc ? 'desc' : 'asc',
                  items_per_page: ctx.perPage,
                  page_number: ctx.currentPage,
                  request_count: this.requestCount,
                  request_non_course_students: this.requestNonCourseStudents
                };

                if (!!this.selectedPeriod && (this.statusFilters.length || this.withoutStatusSelected)) {
                  parameters['period_id'] = this.selectedPeriod.id;
                  parameters['status_filters'] = this.statusFilters.map(function (status) {
                    return status.id;
                  });
                  parameters['without_status'] = this.withoutStatusSelected;
                }

                _context2.next = 4;
                return (_this$connector3 = this.connector) === null || _this$connector3 === void 0 ? void 0 : _this$connector3.loadPresenceEntries(parameters);

              case 4:
                data = _context2.sent;
                this.changeAfterStatusFilters = false;
                periods = data.periods, students = data.students;
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

                selectedPeriod = this.selectedPeriod;

                if (!this.pageLoaded && this.periods.length) {
                  this.setSelectedPeriod(this.periods[this.periods.length - 1].id); //this.$emit('selected-period', this.periods[this.periods.length - 1].id);

                  this.pageLoaded = true;
                } else if (this.createdId !== null) {
                  this.setSelectedPeriod(this.createdId); //this.$emit('selected-period', this.createdId);

                  this.createdId = null;
                  this.creatingNew = false;
                } else if (selectedPeriod) {
                  this.setSelectedPeriod(selectedPeriod.id); //this.$emit('selected-period-maybe');
                }

                if (students.length) {
                  _context2.next = 15;
                  break;
                }

                return _context2.abrupt("return", [{
                  tableEmpty: true
                }]);

              case 15:
                return _context2.abrupt("return", students);

              case 16:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function itemsProvider(_x) {
        return _itemsProvider.apply(this, arguments);
      }

      return itemsProvider;
    }()
  }, {
    key: "setError",
    value: function setError(data) {
      this.errorData = data;
    }
  }, {
    key: "createResultPeriod",
    value: function () {
      var _createResultPeriod = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee3() {
        var _this$connector4,
            _this3 = this;

        var callback,
            _args3 = arguments;
        return regeneratorRuntime.wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                callback = _args3.length > 0 && _args3[0] !== undefined ? _args3[0] : undefined;

                if (this.canEditPresence) {
                  _context3.next = 3;
                  break;
                }

                return _context3.abrupt("return");

              case 3:
                this.selectedPeriod = null;
                this.creatingNew = true;
                this.errorData = null;
                this.checkoutMode = false;
                _context3.next = 9;
                return (_this$connector4 = this.connector) === null || _this$connector4 === void 0 ? void 0 : _this$connector4.createResultPeriod(function (data) {
                  if ((data === null || data === void 0 ? void 0 : data.status) === 'ok') {
                    _this3.createdId = data.id;

                    if (callback) {
                      callback(_this3.createdId);
                    }
                  }
                });

              case 9:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, this);
      }));

      function createResultPeriod() {
        return _createResultPeriod.apply(this, arguments);
      }

      return createResultPeriod;
    }()
  }, {
    key: "removeSelectedPeriod",
    value: function removeSelectedPeriod() {
      var _this$connector5,
          _this4 = this;

      if (!this.canEditPresence || !this.selectedPeriod) {
        return;
      }

      this.errorData = null;
      var selectedPeriod = this.selectedPeriod;
      this.toRemovePeriod = selectedPeriod;
      var index = this.periods.indexOf(selectedPeriod);
      (_this$connector5 = this.connector) === null || _this$connector5 === void 0 ? void 0 : _this$connector5.deletePresencePeriod(selectedPeriod.id, function (data) {
        _this4.toRemovePeriod = null;

        if ((data === null || data === void 0 ? void 0 : data.status) === 'ok') {
          _this4.selectedPeriod = null;

          _this4.periods.splice(index, 1);
        }
      });
    }
  }, {
    key: "setSelectedPeriod",
    value: function setSelectedPeriod(periodId) {
      if (!this.canEditPresence) {
        return;
      }

      if (periodId === null) {
        this.selectedPeriod = null;
      } else {
        this.selectedPeriod = this.periods.find(function (p) {
          return p.id === periodId;
        }) || null;
      }

      var hasFiltersSet = this.statusFilters.length || this.withoutStatusSelected;

      if (this.selectedPeriod === null && hasFiltersSet) {
        this.statusFilters = [];
        this.withoutStatusSelected = false;
        this.requestCount = true;
        this.$emit('refresh');
      } else if (hasFiltersSet) {
        this.requestCount = true;
        this.$emit('refresh');
      } //this.checkoutMode = false;

    }
  }, {
    key: "setSelectedPeriodLabel",
    value: function setSelectedPeriodLabel(selectedPeriod, label) {
      var _this$connector6;

      if (!this.canEditPresence) {
        return;
      }

      this.errorData = null;
      selectedPeriod.label = label;
      (_this$connector6 = this.connector) === null || _this$connector6 === void 0 ? void 0 : _this$connector6.updatePresencePeriod(selectedPeriod.id, label);
    }
  }, {
    key: "setSelectedStudentStatus",
    value: function () {
      var _setSelectedStudentStatus = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee4(student, selectedPeriod, status) {
        var hasNonCourseStudents,
            periodId,
            connector,
            _args4 = arguments;
        return regeneratorRuntime.wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                hasNonCourseStudents = _args4.length > 3 && _args4[3] !== undefined ? _args4[3] : false;

                if (this.canEditPresence) {
                  _context4.next = 3;
                  break;
                }

                return _context4.abrupt("return");

              case 3:
                this.errorData = null;
                periodId = selectedPeriod.id;
                student["period#".concat(periodId, "-status")] = status;

                if (!hasNonCourseStudents && (this.statusFilters.length || this.withoutStatusSelected)) {
                  this.changeAfterStatusFilters = true;
                }

                connector = hasNonCourseStudents ? this.connectorNonCourse : this.connector;
                connector === null || connector === void 0 ? void 0 : connector.savePresenceEntry(periodId, student.id, status, function (data) {
                  if ((data === null || data === void 0 ? void 0 : data.status) === 'ok') {
                    student["period#".concat(periodId, "-checked_in_date")] = data.checked_in_date;
                    student["period#".concat(periodId, "-checked_out_date")] = data.checked_out_date;
                  }
                });

              case 9:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4, this);
      }));

      function setSelectedStudentStatus(_x2, _x3, _x4) {
        return _setSelectedStudentStatus.apply(this, arguments);
      }

      return setSelectedStudentStatus;
    }()
  }, {
    key: "toggleCheckout",
    value: function toggleCheckout(student, selectedPeriod) {
      var hasNonCourseStudents = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

      if (!this.canEditPresence) {
        return;
      }

      var periodId = selectedPeriod.id;

      if (!student["period#".concat(periodId, "-checked_in_date")]) {
        return;
      }

      var connector = hasNonCourseStudents ? this.connectorNonCourse : this.connector;
      connector === null || connector === void 0 ? void 0 : connector.togglePresenceEntryCheckout(periodId, student.id, function (data) {
        if ((data === null || data === void 0 ? void 0 : data.status) === 'ok') {
          student["period#".concat(periodId, "-checked_in_date")] = data.checked_in_date;
          student["period#".concat(periodId, "-checked_out_date")] = data.checked_out_date;
        }
      });
    }
  }, {
    key: "mounted",
    value: function mounted() {
      this.connector = new Connector_Connector(this.apiConfig);
      this.connector.addErrorListener(this);
      this.connectorNonCourse = new Connector_Connector(this.apiConfig);
      this.connectorNonCourse.addErrorListener(this);
      this.load();
    }
  }, {
    key: "_loadIndex",
    value: function _loadIndex() {
      this.load();
    }
  }, {
    key: "globalSearchQuery",
    get: function get() {
      return this.searchOptions.globalSearchQuery;
    },
    set: function set(query) {
      this.searchOptions.globalSearchQuery = query;
    }
  }, {
    key: "presenceStatuses",
    get: function get() {
      var _this$presence;

      return ((_this$presence = this.presence) === null || _this$presence === void 0 ? void 0 : _this$presence.statuses) || [];
    }
  }, {
    key: "isSaving",
    get: function get() {
      var _this$connector7;

      return ((_this$connector7 = this.connector) === null || _this$connector7 === void 0 ? void 0 : _this$connector7.isSaving) || false;
    }
  }, {
    key: "isSavingNonCourse",
    get: function get() {
      var _this$connectorNonCou;

      return ((_this$connectorNonCou = this.connectorNonCourse) === null || _this$connectorNonCou === void 0 ? void 0 : _this$connectorNonCou.isSaving) || false;
    }
  }]);

  return Entry;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: APIConfig_APIConfig,
  required: true
})], Entryvue_type_script_lang_ts_Entry.prototype, "apiConfig", void 0);

__decorate([Prop({
  type: Number,
  "default": 0
})], Entryvue_type_script_lang_ts_Entry.prototype, "loadIndex", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], Entryvue_type_script_lang_ts_Entry.prototype, "canEditPresence", void 0);

__decorate([Watch('loadIndex')], Entryvue_type_script_lang_ts_Entry.prototype, "_loadIndex", null);

Entryvue_type_script_lang_ts_Entry = __decorate([vue_class_component_esm({
  name: 'entry',
  components: {
    EntryTable: entry_EntryTable,
    OnOffSwitch: components_OnOffSwitch,
    FilterStatusButton: entry_FilterStatusButton,
    SearchBar: entry_SearchBar,
    LegendItem: entry_LegendItem,
    DynamicFieldKey: DynamicFieldKey,
    BulkStatusPopup: entry_BulkStatusPopup
  }
})], Entryvue_type_script_lang_ts_Entry);
/* harmony default export */ var Entryvue_type_script_lang_ts_ = (Entryvue_type_script_lang_ts_Entry);
// CONCATENATED MODULE: ./src/components/Entry.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_Entryvue_type_script_lang_ts_ = (Entryvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/Entry.vue?vue&type=style&index=0&id=b35f4f70&scoped=true&lang=css&
var Entryvue_type_style_index_0_id_b35f4f70_scoped_true_lang_css_ = __webpack_require__("3cbf");

// EXTERNAL MODULE: ./src/components/Entry.vue?vue&type=custom&index=0&blockType=i18n
var Entryvue_type_custom_index_0_blockType_i18n = __webpack_require__("cd9d");

// CONCATENATED MODULE: ./src/components/Entry.vue






/* normalize component */

var Entry_component = normalizeComponent(
  components_Entryvue_type_script_lang_ts_,
  Entryvue_type_template_id_b35f4f70_scoped_true_render,
  Entryvue_type_template_id_b35f4f70_scoped_true_staticRenderFns,
  false,
  null,
  "b35f4f70",
  null
  
)

/* custom blocks */

if (typeof Entryvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(Entryvue_type_custom_index_0_blockType_i18n["default"])(Entry_component)

/* harmony default export */ var components_Entry = (Entry_component.exports);
// CONCATENATED MODULE: ./src/plugin.ts


/* harmony default export */ var src_plugin = ({
  install: function install(Vue) {
    Vue.component('PresenceBuilder', components_Builder);
    Vue.component('PresenceEntry', components_Entry);
  }
});
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ var entry_lib = __webpack_exports__["default"] = (src_plugin);



/***/ }),

/***/ "cd9d":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("1169");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Entry_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "ce1a":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// `Symbol.prototype.description` getter
// https://tc39.github.io/ecma262/#sec-symbol.prototype.description

var $ = __webpack_require__("4a1c");
var DESCRIPTORS = __webpack_require__("70b9");
var global = __webpack_require__("b5f1");
var has = __webpack_require__("e414");
var isObject = __webpack_require__("2f69");
var defineProperty = __webpack_require__("e6a8").f;
var copyConstructorProperties = __webpack_require__("6d94");

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

/***/ "ce57":
/***/ (function(module, exports, __webpack_require__) {

var internalObjectKeys = __webpack_require__("3b5d");
var enumBugKeys = __webpack_require__("b337");

// `Object.keys` method
// https://tc39.github.io/ecma262/#sec-object.keys
module.exports = Object.keys || function keys(O) {
  return internalObjectKeys(O, enumBugKeys);
};


/***/ }),

/***/ "d0d3":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("4ff6");
var requireObjectCoercible = __webpack_require__("b2c6");

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

/***/ "d24f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_1_id_61728bf2_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("73f7");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_1_id_61728bf2_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_1_id_61728bf2_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_BulkStatusPopup_vue_vue_type_style_index_1_id_61728bf2_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "d612":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "d656":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var forEach = __webpack_require__("8239");

// `Array.prototype.forEach` method
// https://tc39.github.io/ecma262/#sec-array.prototype.foreach
$({ target: 'Array', proto: true, forced: [].forEach != forEach }, {
  forEach: forEach
});


/***/ }),

/***/ "d667":
/***/ (function(module, exports, __webpack_require__) {

var redefine = __webpack_require__("6a8a");

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

/***/ "d821":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),

/***/ "dd93":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("4ff6");

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

/***/ "df7c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("fbe2");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "e163":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {

var utils = __webpack_require__("0e29");
var normalizeHeaderName = __webpack_require__("e178");

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__("04be");
  } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
    // For node use HTTP adapter
    adapter = __webpack_require__("04be");
  }
  return adapter;
}

var defaults = {
  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Accept');
    normalizeHeaderName(headers, 'Content-Type');
    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
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
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data)) {
      setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
      return JSON.stringify(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    /*eslint no-param-reassign:0*/
    if (typeof data === 'string') {
      try {
        data = JSON.parse(data);
      } catch (e) { /* Ignore */ }
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

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  }
};

defaults.headers = {
  common: {
    'Accept': 'application/json, text/plain, */*'
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("9e7e")))

/***/ }),

/***/ "e178":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),

/***/ "e1f8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");
var bind = __webpack_require__("c097");
var Axios = __webpack_require__("8796");
var mergeConfig = __webpack_require__("eb13");
var defaults = __webpack_require__("e163");

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Factory for creating new instances
axios.create = function create(instanceConfig) {
  return createInstance(mergeConfig(axios.defaults, instanceConfig));
};

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__("97f0");
axios.CancelToken = __webpack_require__("9ebe");
axios.isCancel = __webpack_require__("fe2c");

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__("33a3");

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports.default = axios;


/***/ }),

/***/ "e34b":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_3_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("3558");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_3_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_3_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_3_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "e414":
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;

module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ "e4e7":
/***/ (function(module, exports, __webpack_require__) {

var getBuiltIn = __webpack_require__("f914");

module.exports = getBuiltIn('navigator', 'userAgent') || '';


/***/ }),

/***/ "e57b":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $filter = __webpack_require__("ec68").filter;
var arrayMethodHasSpeciesSupport = __webpack_require__("7aeb");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');
// Edge 14- issue
var USES_TO_LENGTH = arrayMethodUsesToLength('filter');

// `Array.prototype.filter` method
// https://tc39.github.io/ecma262/#sec-array.prototype.filter
// with adding support of @@species
$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT || !USES_TO_LENGTH }, {
  filter: function filter(callbackfn /* , thisArg */) {
    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "e681":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "e6a8":
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__("70b9");
var IE8_DOM_DEFINE = __webpack_require__("4bec");
var anObject = __webpack_require__("6161");
var toPrimitive = __webpack_require__("370b");

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

/***/ "e789":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var fails = __webpack_require__("7104");
var toObject = __webpack_require__("64f1");
var nativeGetPrototypeOf = __webpack_require__("1b63");
var CORRECT_PROTOTYPE_GETTER = __webpack_require__("efa9");

var FAILS_ON_PRIMITIVES = fails(function () { nativeGetPrototypeOf(1); });

// `Object.getPrototypeOf` method
// https://tc39.github.io/ecma262/#sec-object.getprototypeof
$({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES, sham: !CORRECT_PROTOTYPE_GETTER }, {
  getPrototypeOf: function getPrototypeOf(it) {
    return nativeGetPrototypeOf(toObject(it));
  }
});



/***/ }),

/***/ "eb13":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 * @returns {Object} New object resulting from merging config2 to config1
 */
module.exports = function mergeConfig(config1, config2) {
  // eslint-disable-next-line no-param-reassign
  config2 = config2 || {};
  var config = {};

  var valueFromConfig2Keys = ['url', 'method', 'params', 'data'];
  var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy'];
  var defaultToConfig2Keys = [
    'baseURL', 'url', 'transformRequest', 'transformResponse', 'paramsSerializer',
    'timeout', 'withCredentials', 'adapter', 'responseType', 'xsrfCookieName',
    'xsrfHeaderName', 'onUploadProgress', 'onDownloadProgress',
    'maxContentLength', 'validateStatus', 'maxRedirects', 'httpAgent',
    'httpsAgent', 'cancelToken', 'socketPath'
  ];

  utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
    if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    }
  });

  utils.forEach(mergeDeepPropertiesKeys, function mergeDeepProperties(prop) {
    if (utils.isObject(config2[prop])) {
      config[prop] = utils.deepMerge(config1[prop], config2[prop]);
    } else if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    } else if (utils.isObject(config1[prop])) {
      config[prop] = utils.deepMerge(config1[prop]);
    } else if (typeof config1[prop] !== 'undefined') {
      config[prop] = config1[prop];
    }
  });

  utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
    if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    } else if (typeof config1[prop] !== 'undefined') {
      config[prop] = config1[prop];
    }
  });

  var axiosKeys = valueFromConfig2Keys
    .concat(mergeDeepPropertiesKeys)
    .concat(defaultToConfig2Keys);

  var otherKeys = Object
    .keys(config2)
    .filter(function filterAxiosKeys(key) {
      return axiosKeys.indexOf(key) === -1;
    });

  utils.forEach(otherKeys, function otherKeysDefaultToConfig2(prop) {
    if (typeof config2[prop] !== 'undefined') {
      config[prop] = config2[prop];
    } else if (typeof config1[prop] !== 'undefined') {
      config[prop] = config1[prop];
    }
  });

  return config;
};


/***/ }),

/***/ "ec68":
/***/ (function(module, exports, __webpack_require__) {

var bind = __webpack_require__("326d");
var IndexedObject = __webpack_require__("2be1");
var toObject = __webpack_require__("64f1");
var toLength = __webpack_require__("7cf1");
var arraySpeciesCreate = __webpack_require__("62c9");

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

/***/ "edef":
/***/ (function(module, exports, __webpack_require__) {

var userAgent = __webpack_require__("e4e7");

module.exports = /(iphone|ipod|ipad).*applewebkit/i.test(userAgent);


/***/ }),

/***/ "eed1":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "efa9":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");

module.exports = !fails(function () {
  function F() { /* empty */ }
  F.prototype.constructor = null;
  return Object.getPrototypeOf(new F()) !== F.prototype;
});


/***/ }),

/***/ "efe6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PresenceStatusDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a976");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PresenceStatusDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PresenceStatusDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PresenceStatusDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "f01e":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_4_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("5810");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_4_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_4_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Builder_vue_vue_type_style_index_4_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "f072":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

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
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),

/***/ "f23c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__("0e29");
var transformData = __webpack_require__("000a");
var isCancel = __webpack_require__("fe2c");
var defaults = __webpack_require__("e163");

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData(
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData(
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData(
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),

/***/ "f7ad":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $map = __webpack_require__("ec68").map;
var arrayMethodHasSpeciesSupport = __webpack_require__("7aeb");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('map');
// FF49- issue
var USES_TO_LENGTH = arrayMethodUsesToLength('map');

// `Array.prototype.map` method
// https://tc39.github.io/ecma262/#sec-array.prototype.map
// with adding support of @@species
$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT || !USES_TO_LENGTH }, {
  map: function map(callbackfn /* , thisArg */) {
    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "f7f3":
/***/ (function(module, exports, __webpack_require__) {

var toIndexedObject = __webpack_require__("2060");
var toLength = __webpack_require__("7cf1");
var toAbsoluteIndex = __webpack_require__("dd93");

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

/***/ "f914":
/***/ (function(module, exports, __webpack_require__) {

var path = __webpack_require__("0949");
var global = __webpack_require__("b5f1");

var aFunction = function (variable) {
  return typeof variable == 'function' ? variable : undefined;
};

module.exports = function (namespace, method) {
  return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(global[namespace])
    : path[namespace] && path[namespace][method] || global[namespace] && global[namespace][method];
};


/***/ }),

/***/ "f9d2":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");

module.exports = global.Promise;


/***/ }),

/***/ "fabe":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }

  error.request = request;
  error.response = response;
  error.isAxiosError = true;

  error.toJSON = function() {
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
      config: this.config,
      code: this.code
    };
  };
  return error;
};


/***/ }),

/***/ "fba6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var global = __webpack_require__("b5f1");
var getBuiltIn = __webpack_require__("f914");
var IS_PURE = __webpack_require__("764f");
var DESCRIPTORS = __webpack_require__("70b9");
var NATIVE_SYMBOL = __webpack_require__("49cf");
var USE_SYMBOL_AS_UID = __webpack_require__("7f0c");
var fails = __webpack_require__("7104");
var has = __webpack_require__("e414");
var isArray = __webpack_require__("8d52");
var isObject = __webpack_require__("2f69");
var anObject = __webpack_require__("6161");
var toObject = __webpack_require__("64f1");
var toIndexedObject = __webpack_require__("2060");
var toPrimitive = __webpack_require__("370b");
var createPropertyDescriptor = __webpack_require__("62ca");
var nativeObjectCreate = __webpack_require__("2c24");
var objectKeys = __webpack_require__("ce57");
var getOwnPropertyNamesModule = __webpack_require__("9161");
var getOwnPropertyNamesExternal = __webpack_require__("0ffa");
var getOwnPropertySymbolsModule = __webpack_require__("5dc3");
var getOwnPropertyDescriptorModule = __webpack_require__("05dc");
var definePropertyModule = __webpack_require__("e6a8");
var propertyIsEnumerableModule = __webpack_require__("0ffc");
var createNonEnumerableProperty = __webpack_require__("0209");
var redefine = __webpack_require__("6a8a");
var shared = __webpack_require__("943e");
var sharedKey = __webpack_require__("691f");
var hiddenKeys = __webpack_require__("4427");
var uid = __webpack_require__("5be7");
var wellKnownSymbol = __webpack_require__("4736");
var wrappedWellKnownSymbolModule = __webpack_require__("49ae");
var defineWellKnownSymbol = __webpack_require__("2afe");
var setToStringTag = __webpack_require__("5c65");
var InternalStateModule = __webpack_require__("0876");
var $forEach = __webpack_require__("ec68").forEach;

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

var isSymbol = USE_SYMBOL_AS_UID ? function (it) {
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

  redefine($Symbol, 'withoutSetter', function (description) {
    return wrap(uid(description), description);
  });

  propertyIsEnumerableModule.f = $propertyIsEnumerable;
  definePropertyModule.f = $defineProperty;
  getOwnPropertyDescriptorModule.f = $getOwnPropertyDescriptor;
  getOwnPropertyNamesModule.f = getOwnPropertyNamesExternal.f = $getOwnPropertyNames;
  getOwnPropertySymbolsModule.f = $getOwnPropertySymbols;

  wrappedWellKnownSymbolModule.f = function (name) {
    return wrap(wellKnownSymbol(name), name);
  };

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

/***/ "fbe2":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "fde1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


const pFinally = __webpack_require__("c790");

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
module.exports.default = pTimeout;

module.exports.TimeoutError = TimeoutError;


/***/ }),

/***/ "fe2c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),

/***/ "ffbc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var DESCRIPTORS = __webpack_require__("70b9");
var fails = __webpack_require__("7104");
var objectKeys = __webpack_require__("ce57");
var getOwnPropertySymbolsModule = __webpack_require__("5dc3");
var propertyIsEnumerableModule = __webpack_require__("0ffc");
var toObject = __webpack_require__("64f1");
var IndexedObject = __webpack_require__("2be1");

var nativeAssign = Object.assign;
var defineProperty = Object.defineProperty;

// `Object.assign` method
// https://tc39.github.io/ecma262/#sec-object.assign
module.exports = !nativeAssign || fails(function () {
  // should have correct order of operations (Edge bug)
  if (DESCRIPTORS && nativeAssign({ b: 1 }, nativeAssign(defineProperty({}, 'a', {
    enumerable: true,
    get: function () {
      defineProperty(this, 'b', {
        value: 3,
        enumerable: false
      });
    }
  }), { b: 2 })).b !== 1) return true;
  // should work with symbols and should have deterministic property order (V8 bug)
  var A = {};
  var B = {};
  // eslint-disable-next-line no-undef
  var symbol = Symbol();
  var alphabet = 'abcdefghijklmnopqrst';
  A[symbol] = 7;
  alphabet.split('').forEach(function (chr) { B[chr] = chr; });
  return nativeAssign({}, A)[symbol] != 7 || objectKeys(nativeAssign({}, B)).join('') != alphabet;
}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
  var T = toObject(target);
  var argumentsLength = arguments.length;
  var index = 1;
  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
  var propertyIsEnumerable = propertyIsEnumerableModule.f;
  while (argumentsLength > index) {
    var S = IndexedObject(arguments[index++]);
    var keys = getOwnPropertySymbols ? objectKeys(S).concat(getOwnPropertySymbols(S)) : objectKeys(S);
    var length = keys.length;
    var j = 0;
    var key;
    while (length > j) {
      key = keys[j++];
      if (!DESCRIPTORS || propertyIsEnumerable.call(S, key)) T[key] = S[key];
    }
  } return T;
} : nativeAssign;


/***/ })

/******/ });
});
//# sourceMappingURL=cosnics-presence.umd.js.map