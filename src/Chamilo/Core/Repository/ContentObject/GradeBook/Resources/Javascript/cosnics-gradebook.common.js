module.exports =
/******/ (function(modules) { // webpackBootstrap
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

/***/ "034f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_App_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("89ea");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_App_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_App_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_App_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "0434":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"add-remove-scores":"Add/Remove scores","cancel":"Cancel","remove":"Remove","remove-from-overview":"Remove score \u0027{title}\u0027 from overview?"},"nl":{"add-remove-scores":"Scores toevoegen/verwijderen","cancel":"Annuleren","remove":"Verwijderen","remove-from-overview":"Score \u0027{title}\u0027 verwijderen uit overzicht?"}}')
  delete Component.options._Ctor
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

/***/ "072a":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "073e":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var toObject = __webpack_require__("64f1");
var nativeKeys = __webpack_require__("ce57");
var fails = __webpack_require__("7104");

var FAILS_ON_PRIMITIVES = fails(function () { nativeKeys(1); });

// `Object.keys` method
// https://tc39.github.io/ecma262/#sec-object.keys
$({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES }, {
  keys: function keys(it) {
    return nativeKeys(toObject(it));
  }
});


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

/***/ "0c9e":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_style_index_0_id_2ad3ace1_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("faa7");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_style_index_0_id_2ad3ace1_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_style_index_0_id_2ad3ace1_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_style_index_0_id_2ad3ace1_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "0ccc":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"not-synchronized":"Not synchronized","not-yet-updated":"Final score not yet updated"},"nl":{"not-synchronized":"Niet gesynchronizeerd","not-yet-updated":"Eindcijfer nog niet geüpdated"}}')
  delete Component.options._Ctor
}


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

/***/ "1161":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var fails = __webpack_require__("7104");

// babel-minify transpiles RegExp('a', 'y') -> /a/y and it causes SyntaxError,
// so we use an intermediate function.
function RE(s, f) {
  return RegExp(s, f);
}

exports.UNSUPPORTED_Y = fails(function () {
  // babel-minify transpiles RegExp('a', 'y') -> /a/y and it causes SyntaxError
  var re = RE('a', 'y');
  re.lastIndex = 2;
  return re.exec('abcd') != null;
});

exports.BROKEN_CARET = fails(function () {
  // https://bugzilla.mozilla.org/show_bug.cgi?id=773687
  var re = RE('^r', 'gy');
  re.lastIndex = 2;
  return re.exec('str') != null;
});


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

/***/ "1880":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "218a":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"aabs":"aabs","auth-absent":"Authorized absent","edit-comment":"Edit comments","no-score":"No score","no-score-abbr":"n/a","no-score-found":"No score found"},"nl":{"aabs":"gafw","auth-absent":"Gewettigd afwezig","edit-comment":"Wijzig opmerkingen","no-score":"Geen score","no-score-abbr":"n.b.","no-score-found":"Geen score gevonden"}}')
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

/***/ "2378":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("0ccc");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "25d2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $some = __webpack_require__("ec68").some;
var arrayMethodIsStrict = __webpack_require__("9fac");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var STRICT_METHOD = arrayMethodIsStrict('some');
var USES_TO_LENGTH = arrayMethodUsesToLength('some');

// `Array.prototype.some` method
// https://tc39.github.io/ecma262/#sec-array.prototype.some
$({ target: 'Array', proto: true, forced: !STRICT_METHOD || !USES_TO_LENGTH }, {
  some: function some(callbackfn /* , thisArg */) {
    return $some(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "2690":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var charAt = __webpack_require__("d0d3").charAt;

// `AdvanceStringIndex` abstract operation
// https://tc39.github.io/ecma262/#sec-advancestringindex
module.exports = function (S, index, unicode) {
  return index + (unicode ? charAt(S, index).length : 1);
};


/***/ }),

/***/ "284a":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"choose-file":"Choose file","choose-type":"Choose type","correct-mistakes":"Please correct any errors and","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-Unknown":"An unknown error occurred. Your changes have possibly not been saved. You can try again later.","file-with":"File with","go-to-gradebook":"Go to gradebook","import":"Import","import-complete":"Import complete","import-preview":"Preview","import-results-overview":"You can find an overview of the results from the CSV file below. Click the button below to import. Only results that are valid will be imported.","import-steps":"Import steps","import-successful":"The results have been successfully imported.","no-results-some-students":"Careful! For some subscribed students no matching results have been found. See below.<br>You can still make manual adjustments.","question-upload":"What kind of file do you want to upload?","reupload-results":"reupload","select-file":"Select a file...","type-scores":"1 or more score columns","type-scores-comments":"1 score column and 1 feedback column","upload":"Upload","user-not-in-course":"Student is not subscribed to this course","without-results":"No results"},"nl":{"choose-file":"Kies bestand","choose-type":"Kies type","correct-mistakes":"Gelieve de fout(en) te verbeteren en de resultaten","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","file-with":"Bestand met","go-to-gradebook":"Ga naar puntenboekje","import":"Importeer","import-complete":"Importeren voltooid","import-preview":"Voorbeeldweergave","import-results-overview":"Hieronder vind je een overzicht van de resultaten uit het CSV-bestand. Klik op de knop hieronder om te importeren. Enkel de geldige resultaten zullen worden geïmporteerd.","import-steps":"Import-stappen","import-successful":"De resultaten werden succesvol geïmporteerd.","no-results-some-students":"Let op! Voor sommige ingeschreven studenten werden geen resultaten gevonden. Zie hieronder.<br>Gelieve deze handmatig aan te passen.","question-upload":"Wat voor bestand wil je opladen?","reupload-results":"opnieuw op te laden","select-file":"Kies een bestand...","type-scores":"1 of meerdere scorekolommen","type-scores-comments":"1 scorekolom en 1 feedbackkolom","upload":"Upload","user-not-in-course":"Student maakt geen deel uit van deze cursus","without-results":"Zonder resultaat"}}')
  delete Component.options._Ctor
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

/***/ "289f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MultiDrag", function() { return MultiDragPlugin; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Sortable", function() { return Sortable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Swap", function() { return SwapPlugin; });
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

/* harmony default export */ __webpack_exports__["default"] = (Sortable);



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

/***/ "2c8b":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a0e5");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "2f23":
/***/ (function(module, exports) {

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

/***/ "2ffb":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("9a88");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CSVImportInfo_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "30fb":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"weight":"Weight"},"nl":{"weight":"Gewicht"}}')
  delete Component.options._Ctor
}


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

/***/ "3336":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var parseIntImplementation = __webpack_require__("cc07");

// `parseInt` method
// https://tc39.github.io/ecma262/#sec-parseint-string-radix
$({ global: true, forced: parseInt != parseIntImplementation }, {
  parseInt: parseIntImplementation
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

/***/ "33d1":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8fe0");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "3aa4":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "3c2e":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("284a");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "4411":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_style_index_0_id_0b7f26b5_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("55ba");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_style_index_0_id_0b7f26b5_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_style_index_0_id_0b7f26b5_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_style_index_0_id_0b7f26b5_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "4427":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "4581":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "473a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("30fb");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "49ae":
/***/ (function(module, exports, __webpack_require__) {

var wellKnownSymbol = __webpack_require__("4736");

exports.f = wellKnownSymbol;


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

/***/ "4bc5":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("3ab7");
var regexpExec = __webpack_require__("9a1c");

// `RegExpExec` abstract operation
// https://tc39.github.io/ecma262/#sec-regexpexec
module.exports = function (R, S) {
  var exec = R.exec;
  if (typeof exec === 'function') {
    var result = exec.call(R, S);
    if (typeof result !== 'object') {
      throw TypeError('RegExp exec method returned something other than an Object or null');
    }
    return result;
  }

  if (classof(R) !== 'RegExp') {
    throw TypeError('RegExp#exec called on incompatible receiver');
  }

  return regexpExec.call(R, S);
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

/***/ "53da":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("6af0");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "54a8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_style_index_0_id_a97afd16_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("f52b");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_style_index_0_id_a97afd16_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_style_index_0_id_a97afd16_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImporterApp_vue_vue_type_style_index_0_id_a97afd16_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "54ff":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var toInteger = __webpack_require__("4ff6");
var requireObjectCoercible = __webpack_require__("b2c6");

// `String.prototype.repeat` method implementation
// https://tc39.github.io/ecma262/#sec-string.prototype.repeat
module.exports = ''.repeat || function repeat(count) {
  var str = String(requireObjectCoercible(this));
  var result = '';
  var n = toInteger(count);
  if (n < 0 || n == Infinity) throw RangeError('Wrong number of repetitions');
  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
  return result;
};


/***/ }),

/***/ "55ba":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "5849":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "59b0":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("5acc");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_2_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "59c0":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var toInteger = __webpack_require__("4ff6");
var thisNumberValue = __webpack_require__("9596");
var repeat = __webpack_require__("54ff");
var fails = __webpack_require__("7104");

var nativeToFixed = 1.0.toFixed;
var floor = Math.floor;

var pow = function (x, n, acc) {
  return n === 0 ? acc : n % 2 === 1 ? pow(x, n - 1, acc * x) : pow(x * x, n / 2, acc);
};

var log = function (x) {
  var n = 0;
  var x2 = x;
  while (x2 >= 4096) {
    n += 12;
    x2 /= 4096;
  }
  while (x2 >= 2) {
    n += 1;
    x2 /= 2;
  } return n;
};

var FORCED = nativeToFixed && (
  0.00008.toFixed(3) !== '0.000' ||
  0.9.toFixed(0) !== '1' ||
  1.255.toFixed(2) !== '1.25' ||
  1000000000000000128.0.toFixed(0) !== '1000000000000000128'
) || !fails(function () {
  // V8 ~ Android 4.3-
  nativeToFixed.call({});
});

// `Number.prototype.toFixed` method
// https://tc39.github.io/ecma262/#sec-number.prototype.tofixed
$({ target: 'Number', proto: true, forced: FORCED }, {
  // eslint-disable-next-line max-statements
  toFixed: function toFixed(fractionDigits) {
    var number = thisNumberValue(this);
    var fractDigits = toInteger(fractionDigits);
    var data = [0, 0, 0, 0, 0, 0];
    var sign = '';
    var result = '0';
    var e, z, j, k;

    var multiply = function (n, c) {
      var index = -1;
      var c2 = c;
      while (++index < 6) {
        c2 += n * data[index];
        data[index] = c2 % 1e7;
        c2 = floor(c2 / 1e7);
      }
    };

    var divide = function (n) {
      var index = 6;
      var c = 0;
      while (--index >= 0) {
        c += data[index];
        data[index] = floor(c / n);
        c = (c % n) * 1e7;
      }
    };

    var dataToString = function () {
      var index = 6;
      var s = '';
      while (--index >= 0) {
        if (s !== '' || index === 0 || data[index] !== 0) {
          var t = String(data[index]);
          s = s === '' ? t : s + repeat.call('0', 7 - t.length) + t;
        }
      } return s;
    };

    if (fractDigits < 0 || fractDigits > 20) throw RangeError('Incorrect fraction digits');
    // eslint-disable-next-line no-self-compare
    if (number != number) return 'NaN';
    if (number <= -1e21 || number >= 1e21) return String(number);
    if (number < 0) {
      sign = '-';
      number = -number;
    }
    if (number > 1e-21) {
      e = log(number * pow(2, 69, 1)) - 69;
      z = e < 0 ? number * pow(2, -e, 1) : number / pow(2, e, 1);
      z *= 0x10000000000000;
      e = 52 - e;
      if (e > 0) {
        multiply(0, z);
        j = fractDigits;
        while (j >= 7) {
          multiply(1e7, 0);
          j -= 7;
        }
        multiply(pow(10, j, 1), 0);
        j = e - 1;
        while (j >= 23) {
          divide(1 << 23);
          j -= 23;
        }
        divide(1 << j);
        multiply(1, 1);
        divide(2);
        result = dataToString();
      } else {
        multiply(0, z);
        multiply(1 << -e, 0);
        result = dataToString() + repeat.call('0', fractDigits);
      }
    }
    if (fractDigits > 0) {
      k = result.length;
      result = sign + (k <= fractDigits
        ? '0.' + repeat.call('0', fractDigits - k) + result
        : result.slice(0, k - fractDigits) + '.' + result.slice(k - fractDigits));
    } else {
      result = sign + result;
    } return result;
  }
});


/***/ }),

/***/ "5acc":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "5f8f":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "6431":
/***/ (function(module, exports, __webpack_require__) {

module.exports =
/******/ (function(modules) { // webpackBootstrap
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
/******/ 	return __webpack_require__(__webpack_require__.s = "fb15");
/******/ })
/************************************************************************/
/******/ ({

/***/ "02f4":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("4588");
var defined = __webpack_require__("be13");
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
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var at = __webpack_require__("02f4")(true);

 // `AdvanceStringIndex` abstract operation
// https://tc39.github.io/ecma262/#sec-advancestringindex
module.exports = function (S, index, unicode) {
  return index + (unicode ? at(S, index).length : 1);
};


/***/ }),

/***/ "07e3":
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ "0bfb":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 21.2.5.3 get RegExp.prototype.flags
var anObject = __webpack_require__("cb7c");
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

/***/ "0fc9":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("3a38");
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),

/***/ "1654":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $at = __webpack_require__("71c1")(true);

// 21.1.3.27 String.prototype[@@iterator]()
__webpack_require__("30f1")(String, 'String', function (iterated) {
  this._t = String(iterated); // target
  this._i = 0;                // next index
// 21.1.5.2.1 %StringIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var index = this._i;
  var point;
  if (index >= O.length) return { value: undefined, done: true };
  point = $at(O, index);
  this._i += point.length;
  return { value: point, done: false };
});


/***/ }),

/***/ "1691":
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),

/***/ "1af6":
/***/ (function(module, exports, __webpack_require__) {

// 22.1.2.2 / 15.4.3.2 Array.isArray(arg)
var $export = __webpack_require__("63b6");

$export($export.S, 'Array', { isArray: __webpack_require__("9003") });


/***/ }),

/***/ "1bc3":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__("f772");
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

/***/ "1ec9":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("f772");
var document = __webpack_require__("e53d").document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),

/***/ "20fd":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $defineProperty = __webpack_require__("d9f6");
var createDesc = __webpack_require__("aebd");

module.exports = function (object, index, value) {
  if (index in object) $defineProperty.f(object, index, createDesc(0, value));
  else object[index] = value;
};


/***/ }),

/***/ "214f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

__webpack_require__("b0c5");
var redefine = __webpack_require__("2aba");
var hide = __webpack_require__("32e9");
var fails = __webpack_require__("79e5");
var defined = __webpack_require__("be13");
var wks = __webpack_require__("2b4c");
var regexpExec = __webpack_require__("520a");

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
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("d3f4");
var document = __webpack_require__("7726").document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),

/***/ "23c6":
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__("2d95");
var TAG = __webpack_require__("2b4c")('toStringTag');
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

/***/ "241e":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__("25eb");
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),

/***/ "25eb":
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),

/***/ "294c":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),

/***/ "2aba":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("7726");
var hide = __webpack_require__("32e9");
var has = __webpack_require__("69a8");
var SRC = __webpack_require__("ca5a")('src');
var $toString = __webpack_require__("fa5b");
var TO_STRING = 'toString';
var TPL = ('' + $toString).split(TO_STRING);

__webpack_require__("8378").inspectSource = function (it) {
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

/***/ "2b4c":
/***/ (function(module, exports, __webpack_require__) {

var store = __webpack_require__("5537")('wks');
var uid = __webpack_require__("ca5a");
var Symbol = __webpack_require__("7726").Symbol;
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
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// 21.1.3.7 String.prototype.includes(searchString, position = 0)

var $export = __webpack_require__("5ca1");
var context = __webpack_require__("d2c8");
var INCLUDES = 'includes';

$export($export.P + $export.F * __webpack_require__("5147")(INCLUDES), 'String', {
  includes: function includes(searchString /* , position = 0 */) {
    return !!~context(this, searchString, INCLUDES)
      .indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "30f1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__("b8e3");
var $export = __webpack_require__("63b6");
var redefine = __webpack_require__("9138");
var hide = __webpack_require__("35e8");
var Iterators = __webpack_require__("481b");
var $iterCreate = __webpack_require__("8f60");
var setToStringTag = __webpack_require__("45f2");
var getPrototypeOf = __webpack_require__("53e2");
var ITERATOR = __webpack_require__("5168")('iterator');
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

/***/ "32a6":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__("241e");
var $keys = __webpack_require__("c3a1");

__webpack_require__("ce7e")('keys', function () {
  return function keys(it) {
    return $keys(toObject(it));
  };
});


/***/ }),

/***/ "32e9":
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__("86cc");
var createDesc = __webpack_require__("4630");
module.exports = __webpack_require__("9e1e") ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "32fc":
/***/ (function(module, exports, __webpack_require__) {

var document = __webpack_require__("e53d").document;
module.exports = document && document.documentElement;


/***/ }),

/***/ "335c":
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__("6b4c");
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),

/***/ "355d":
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),

/***/ "35e8":
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__("d9f6");
var createDesc = __webpack_require__("aebd");
module.exports = __webpack_require__("8e60") ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "36c3":
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__("335c");
var defined = __webpack_require__("25eb");
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),

/***/ "3702":
/***/ (function(module, exports, __webpack_require__) {

// check on default Array iterator
var Iterators = __webpack_require__("481b");
var ITERATOR = __webpack_require__("5168")('iterator');
var ArrayProto = Array.prototype;

module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};


/***/ }),

/***/ "3a38":
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),

/***/ "40c3":
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__("6b4c");
var TAG = __webpack_require__("5168")('toStringTag');
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

/***/ "4588":
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),

/***/ "45f2":
/***/ (function(module, exports, __webpack_require__) {

var def = __webpack_require__("d9f6").f;
var has = __webpack_require__("07e3");
var TAG = __webpack_require__("5168")('toStringTag');

module.exports = function (it, tag, stat) {
  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
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

/***/ "469f":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("6c1c");
__webpack_require__("1654");
module.exports = __webpack_require__("7d7b");


/***/ }),

/***/ "481b":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "4aa6":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("dc62");

/***/ }),

/***/ "4bf8":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__("be13");
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),

/***/ "4ee1":
/***/ (function(module, exports, __webpack_require__) {

var ITERATOR = __webpack_require__("5168")('iterator');
var SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function () { SAFE_CLOSING = true; };
  // eslint-disable-next-line no-throw-literal
  Array.from(riter, function () { throw 2; });
} catch (e) { /* empty */ }

module.exports = function (exec, skipClosing) {
  if (!skipClosing && !SAFE_CLOSING) return false;
  var safe = false;
  try {
    var arr = [7];
    var iter = arr[ITERATOR]();
    iter.next = function () { return { done: safe = true }; };
    arr[ITERATOR] = function () { return iter; };
    exec(arr);
  } catch (e) { /* empty */ }
  return safe;
};


/***/ }),

/***/ "50ed":
/***/ (function(module, exports) {

module.exports = function (done, value) {
  return { value: value, done: !!done };
};


/***/ }),

/***/ "5147":
/***/ (function(module, exports, __webpack_require__) {

var MATCH = __webpack_require__("2b4c")('match');
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

/***/ "5168":
/***/ (function(module, exports, __webpack_require__) {

var store = __webpack_require__("dbdb")('wks');
var uid = __webpack_require__("62a0");
var Symbol = __webpack_require__("e53d").Symbol;
var USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function (name) {
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;


/***/ }),

/***/ "5176":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("51b6");

/***/ }),

/***/ "51b6":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("a3c3");
module.exports = __webpack_require__("584a").Object.assign;


/***/ }),

/***/ "520a":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var regexpFlags = __webpack_require__("0bfb");

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

/***/ "53e2":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has = __webpack_require__("07e3");
var toObject = __webpack_require__("241e");
var IE_PROTO = __webpack_require__("5559")('IE_PROTO');
var ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};


/***/ }),

/***/ "549b":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var ctx = __webpack_require__("d864");
var $export = __webpack_require__("63b6");
var toObject = __webpack_require__("241e");
var call = __webpack_require__("b0dc");
var isArrayIter = __webpack_require__("3702");
var toLength = __webpack_require__("b447");
var createProperty = __webpack_require__("20fd");
var getIterFn = __webpack_require__("7cd6");

$export($export.S + $export.F * !__webpack_require__("4ee1")(function (iter) { Array.from(iter); }), 'Array', {
  // 22.1.2.1 Array.from(arrayLike, mapfn = undefined, thisArg = undefined)
  from: function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
    var O = toObject(arrayLike);
    var C = typeof this == 'function' ? this : Array;
    var aLen = arguments.length;
    var mapfn = aLen > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    var index = 0;
    var iterFn = getIterFn(O);
    var length, result, step, iterator;
    if (mapping) mapfn = ctx(mapfn, aLen > 2 ? arguments[2] : undefined, 2);
    // if object isn't iterable or it's array with default iterator - use simple case
    if (iterFn != undefined && !(C == Array && isArrayIter(iterFn))) {
      for (iterator = iterFn.call(O), result = new C(); !(step = iterator.next()).done; index++) {
        createProperty(result, index, mapping ? call(iterator, mapfn, [step.value, index], true) : step.value);
      }
    } else {
      length = toLength(O.length);
      for (result = new C(length); length > index; index++) {
        createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
      }
    }
    result.length = index;
    return result;
  }
});


/***/ }),

/***/ "54a1":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("6c1c");
__webpack_require__("1654");
module.exports = __webpack_require__("95d5");


/***/ }),

/***/ "5537":
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__("8378");
var global = __webpack_require__("7726");
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__("2d00") ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ "5559":
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__("dbdb")('keys');
var uid = __webpack_require__("62a0");
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),

/***/ "584a":
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.5' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),

/***/ "5b4e":
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__("36c3");
var toLength = __webpack_require__("b447");
var toAbsoluteIndex = __webpack_require__("0fc9");
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

/***/ "5ca1":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("7726");
var core = __webpack_require__("8378");
var hide = __webpack_require__("32e9");
var redefine = __webpack_require__("2aba");
var ctx = __webpack_require__("9b43");
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

/***/ "5d73":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("469f");

/***/ }),

/***/ "5f1b":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var classof = __webpack_require__("23c6");
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

/***/ "626a":
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__("2d95");
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),

/***/ "62a0":
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),

/***/ "63b6":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("e53d");
var core = __webpack_require__("584a");
var ctx = __webpack_require__("d864");
var hide = __webpack_require__("35e8");
var has = __webpack_require__("07e3");
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var IS_WRAP = type & $export.W;
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE];
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE];
  var key, own, out;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if (own && has(exports, key)) continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function (C) {
      var F = function (a, b, c) {
        if (this instanceof C) {
          switch (arguments.length) {
            case 0: return new C();
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if (IS_PROTO) {
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if (type & $export.R && expProto && !expProto[key]) hide(expProto, key, out);
    }
  }
};
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

/***/ "6762":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/Array.prototype.includes
var $export = __webpack_require__("5ca1");
var $includes = __webpack_require__("c366")(true);

$export($export.P, 'Array', {
  includes: function includes(el /* , fromIndex = 0 */) {
    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
  }
});

__webpack_require__("9c6c")('includes');


/***/ }),

/***/ "6821":
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__("626a");
var defined = __webpack_require__("be13");
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
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__("d3f4");
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

/***/ "6b4c":
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),

/***/ "6c1c":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("c367");
var global = __webpack_require__("e53d");
var hide = __webpack_require__("35e8");
var Iterators = __webpack_require__("481b");
var TO_STRING_TAG = __webpack_require__("5168")('toStringTag');

var DOMIterables = ('CSSRuleList,CSSStyleDeclaration,CSSValueList,ClientRectList,DOMRectList,DOMStringList,' +
  'DOMTokenList,DataTransferItemList,FileList,HTMLAllCollection,HTMLCollection,HTMLFormElement,HTMLSelectElement,' +
  'MediaList,MimeTypeArray,NamedNodeMap,NodeList,PaintRequestList,Plugin,PluginArray,SVGLengthList,SVGNumberList,' +
  'SVGPathSegList,SVGPointList,SVGStringList,SVGTransformList,SourceBufferList,StyleSheetList,TextTrackCueList,' +
  'TextTrackList,TouchList').split(',');

for (var i = 0; i < DOMIterables.length; i++) {
  var NAME = DOMIterables[i];
  var Collection = global[NAME];
  var proto = Collection && Collection.prototype;
  if (proto && !proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
  Iterators[NAME] = Iterators.Array;
}


/***/ }),

/***/ "71c1":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("3a38");
var defined = __webpack_require__("25eb");
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

/***/ "7726":
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),

/***/ "774e":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("d2d5");

/***/ }),

/***/ "77f1":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("4588");
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),

/***/ "794b":
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__("8e60") && !__webpack_require__("294c")(function () {
  return Object.defineProperty(__webpack_require__("1ec9")('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "79aa":
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
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

/***/ "7cd6":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("40c3");
var ITERATOR = __webpack_require__("5168")('iterator');
var Iterators = __webpack_require__("481b");
module.exports = __webpack_require__("584a").getIteratorMethod = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),

/***/ "7d7b":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("e4ae");
var get = __webpack_require__("7cd6");
module.exports = __webpack_require__("584a").getIterator = function (it) {
  var iterFn = get(it);
  if (typeof iterFn != 'function') throw TypeError(it + ' is not iterable!');
  return anObject(iterFn.call(it));
};


/***/ }),

/***/ "7e90":
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__("d9f6");
var anObject = __webpack_require__("e4ae");
var getKeys = __webpack_require__("c3a1");

module.exports = __webpack_require__("8e60") ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = getKeys(Properties);
  var length = keys.length;
  var i = 0;
  var P;
  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
  return O;
};


/***/ }),

/***/ "8378":
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.5' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),

/***/ "8436":
/***/ (function(module, exports) {

module.exports = function () { /* empty */ };


/***/ }),

/***/ "86cc":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("cb7c");
var IE8_DOM_DEFINE = __webpack_require__("c69a");
var toPrimitive = __webpack_require__("6a99");
var dP = Object.defineProperty;

exports.f = __webpack_require__("9e1e") ? Object.defineProperty : function defineProperty(O, P, Attributes) {
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

/***/ "8aae":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("32a6");
module.exports = __webpack_require__("584a").Object.keys;


/***/ }),

/***/ "8e60":
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__("294c")(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "8f60":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var create = __webpack_require__("a159");
var descriptor = __webpack_require__("aebd");
var setToStringTag = __webpack_require__("45f2");
var IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__("35e8")(IteratorPrototype, __webpack_require__("5168")('iterator'), function () { return this; });

module.exports = function (Constructor, NAME, next) {
  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
  setToStringTag(Constructor, NAME + ' Iterator');
};


/***/ }),

/***/ "9003":
/***/ (function(module, exports, __webpack_require__) {

// 7.2.2 IsArray(argument)
var cof = __webpack_require__("6b4c");
module.exports = Array.isArray || function isArray(arg) {
  return cof(arg) == 'Array';
};


/***/ }),

/***/ "9138":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("35e8");


/***/ }),

/***/ "9306":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var getKeys = __webpack_require__("c3a1");
var gOPS = __webpack_require__("9aa9");
var pIE = __webpack_require__("355d");
var toObject = __webpack_require__("241e");
var IObject = __webpack_require__("335c");
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__("294c")(function () {
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

/***/ "9427":
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__("63b6");
// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
$export($export.S, 'Object', { create: __webpack_require__("a159") });


/***/ }),

/***/ "95d5":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("40c3");
var ITERATOR = __webpack_require__("5168")('iterator');
var Iterators = __webpack_require__("481b");
module.exports = __webpack_require__("584a").isIterable = function (it) {
  var O = Object(it);
  return O[ITERATOR] !== undefined
    || '@@iterator' in O
    // eslint-disable-next-line no-prototype-builtins
    || Iterators.hasOwnProperty(classof(O));
};


/***/ }),

/***/ "9aa9":
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ "9b43":
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__("d8e8");
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
/***/ (function(module, exports, __webpack_require__) {

// 22.1.3.31 Array.prototype[@@unscopables]
var UNSCOPABLES = __webpack_require__("2b4c")('unscopables');
var ArrayProto = Array.prototype;
if (ArrayProto[UNSCOPABLES] == undefined) __webpack_require__("32e9")(ArrayProto, UNSCOPABLES, {});
module.exports = function (key) {
  ArrayProto[UNSCOPABLES][key] = true;
};


/***/ }),

/***/ "9def":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__("4588");
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),

/***/ "9e1e":
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__("79e5")(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "a159":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject = __webpack_require__("e4ae");
var dPs = __webpack_require__("7e90");
var enumBugKeys = __webpack_require__("1691");
var IE_PROTO = __webpack_require__("5559")('IE_PROTO');
var Empty = function () { /* empty */ };
var PROTOTYPE = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __webpack_require__("1ec9")('iframe');
  var i = enumBugKeys.length;
  var lt = '<';
  var gt = '>';
  var iframeDocument;
  iframe.style.display = 'none';
  __webpack_require__("32fc").appendChild(iframe);
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

/***/ "a352":
/***/ (function(module, exports) {

module.exports = __webpack_require__("289f");

/***/ }),

/***/ "a3c3":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__("63b6");

$export($export.S + $export.F, 'Object', { assign: __webpack_require__("9306") });


/***/ }),

/***/ "a481":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var anObject = __webpack_require__("cb7c");
var toObject = __webpack_require__("4bf8");
var toLength = __webpack_require__("9def");
var toInteger = __webpack_require__("4588");
var advanceStringIndex = __webpack_require__("0390");
var regExpExec = __webpack_require__("5f1b");
var max = Math.max;
var min = Math.min;
var floor = Math.floor;
var SUBSTITUTION_SYMBOLS = /\$([$&`']|\d\d?|<[^>]*>)/g;
var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&`']|\d\d?)/g;

var maybeToString = function (it) {
  return it === undefined ? it : String(it);
};

// @@replace logic
__webpack_require__("214f")('replace', 2, function (defined, REPLACE, $replace, maybeCallNative) {
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

/***/ "a4bb":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("8aae");

/***/ }),

/***/ "a745":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("f410");

/***/ }),

/***/ "aae3":
/***/ (function(module, exports, __webpack_require__) {

// 7.2.8 IsRegExp(argument)
var isObject = __webpack_require__("d3f4");
var cof = __webpack_require__("2d95");
var MATCH = __webpack_require__("2b4c")('match');
module.exports = function (it) {
  var isRegExp;
  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
};


/***/ }),

/***/ "aebd":
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

/***/ "b0c5":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var regexpExec = __webpack_require__("520a");
__webpack_require__("5ca1")({
  target: 'RegExp',
  proto: true,
  forced: regexpExec !== /./.exec
}, {
  exec: regexpExec
});


/***/ }),

/***/ "b0dc":
/***/ (function(module, exports, __webpack_require__) {

// call something on iterator step with safe closing on error
var anObject = __webpack_require__("e4ae");
module.exports = function (iterator, fn, value, entries) {
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (e) {
    var ret = iterator['return'];
    if (ret !== undefined) anObject(ret.call(iterator));
    throw e;
  }
};


/***/ }),

/***/ "b447":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__("3a38");
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),

/***/ "b8e3":
/***/ (function(module, exports) {

module.exports = true;


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
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__("6821");
var toLength = __webpack_require__("9def");
var toAbsoluteIndex = __webpack_require__("77f1");
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

/***/ "c367":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var addToUnscopables = __webpack_require__("8436");
var step = __webpack_require__("50ed");
var Iterators = __webpack_require__("481b");
var toIObject = __webpack_require__("36c3");

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __webpack_require__("30f1")(Array, 'Array', function (iterated, kind) {
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

/***/ "c3a1":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __webpack_require__("e6f3");
var enumBugKeys = __webpack_require__("1691");

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),

/***/ "c649":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return insertNodeAt; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return camelize; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return console; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return removeNode; });
/* harmony import */ var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a481");
/* harmony import */ var core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es6_regexp_replace__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var F_source_Vue_Draggable_node_modules_babel_runtime_corejs2_core_js_object_create__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("4aa6");
/* harmony import */ var F_source_Vue_Draggable_node_modules_babel_runtime_corejs2_core_js_object_create__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(F_source_Vue_Draggable_node_modules_babel_runtime_corejs2_core_js_object_create__WEBPACK_IMPORTED_MODULE_1__);



function getConsole() {
  if (typeof window !== "undefined") {
    return window.console;
  }

  return global.console;
}

var console = getConsole();

function cached(fn) {
  var cache = F_source_Vue_Draggable_node_modules_babel_runtime_corejs2_core_js_object_create__WEBPACK_IMPORTED_MODULE_1___default()(null);

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


/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("c8ba")))

/***/ }),

/***/ "c69a":
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__("9e1e") && !__webpack_require__("79e5")(function () {
  return Object.defineProperty(__webpack_require__("230e")('div'), 'a', { get: function () { return 7; } }).a != 7;
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

/***/ "c8bb":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("54a1");

/***/ }),

/***/ "ca5a":
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),

/***/ "cb7c":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("d3f4");
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),

/***/ "ce7e":
/***/ (function(module, exports, __webpack_require__) {

// most Object methods by ES6 should accept primitives
var $export = __webpack_require__("63b6");
var core = __webpack_require__("584a");
var fails = __webpack_require__("294c");
module.exports = function (KEY, exec) {
  var fn = (core.Object || {})[KEY] || Object[KEY];
  var exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function () { fn(1); }), 'Object', exp);
};


/***/ }),

/***/ "d2c8":
/***/ (function(module, exports, __webpack_require__) {

// helper for String#{startsWith, endsWith, includes}
var isRegExp = __webpack_require__("aae3");
var defined = __webpack_require__("be13");

module.exports = function (that, searchString, NAME) {
  if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
  return String(defined(that));
};


/***/ }),

/***/ "d2d5":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("1654");
__webpack_require__("549b");
module.exports = __webpack_require__("584a").Array.from;


/***/ }),

/***/ "d3f4":
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ "d864":
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__("79aa");
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

/***/ "d8e8":
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),

/***/ "d9f6":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("e4ae");
var IE8_DOM_DEFINE = __webpack_require__("794b");
var toPrimitive = __webpack_require__("1bc3");
var dP = Object.defineProperty;

exports.f = __webpack_require__("8e60") ? Object.defineProperty : function defineProperty(O, P, Attributes) {
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

/***/ "dbdb":
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__("584a");
var global = __webpack_require__("e53d");
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__("b8e3") ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ "dc62":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("9427");
var $Object = __webpack_require__("584a").Object;
module.exports = function create(P, D) {
  return $Object.create(P, D);
};


/***/ }),

/***/ "e4ae":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("f772");
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),

/***/ "e53d":
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),

/***/ "e6f3":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("07e3");
var toIObject = __webpack_require__("36c3");
var arrayIndexOf = __webpack_require__("5b4e")(false);
var IE_PROTO = __webpack_require__("5559")('IE_PROTO');

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

/***/ "f410":
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("1af6");
module.exports = __webpack_require__("584a").Array.isArray;


/***/ }),

/***/ "f559":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// 21.1.3.18 String.prototype.startsWith(searchString [, position ])

var $export = __webpack_require__("5ca1");
var toLength = __webpack_require__("9def");
var context = __webpack_require__("d2c8");
var STARTS_WITH = 'startsWith';
var $startsWith = ''[STARTS_WITH];

$export($export.P + $export.F * __webpack_require__("5147")(STARTS_WITH), 'String', {
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

/***/ "f772":
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ "fa5b":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("5537")('native-function-to-string', Function.toString);


/***/ }),

/***/ "fb15":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var setPublicPath_i
  if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __webpack_require__.p = setPublicPath_i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// EXTERNAL MODULE: ./node_modules/@babel/runtime-corejs2/core-js/object/assign.js
var object_assign = __webpack_require__("5176");
var assign_default = /*#__PURE__*/__webpack_require__.n(object_assign);

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.starts-with.js
var es6_string_starts_with = __webpack_require__("f559");

// EXTERNAL MODULE: ./node_modules/@babel/runtime-corejs2/core-js/object/keys.js
var keys = __webpack_require__("a4bb");
var keys_default = /*#__PURE__*/__webpack_require__.n(keys);

// EXTERNAL MODULE: ./node_modules/@babel/runtime-corejs2/core-js/array/is-array.js
var is_array = __webpack_require__("a745");
var is_array_default = /*#__PURE__*/__webpack_require__.n(is_array);

// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/arrayWithHoles.js

function _arrayWithHoles(arr) {
  if (is_array_default()(arr)) return arr;
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime-corejs2/core-js/get-iterator.js
var get_iterator = __webpack_require__("5d73");
var get_iterator_default = /*#__PURE__*/__webpack_require__.n(get_iterator);

// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/iterableToArrayLimit.js

function _iterableToArrayLimit(arr, i) {
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = get_iterator_default()(arr), _s; !(_n = (_s = _i.next()).done); _n = true) {
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
// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/nonIterableRest.js
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/slicedToArray.js



function _slicedToArray(arr, i) {
  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
}
// EXTERNAL MODULE: ./node_modules/core-js/modules/es7.array.includes.js
var es7_array_includes = __webpack_require__("6762");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.includes.js
var es6_string_includes = __webpack_require__("2fdb");

// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (is_array_default()(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime-corejs2/core-js/array/from.js
var from = __webpack_require__("774e");
var from_default = /*#__PURE__*/__webpack_require__.n(from);

// EXTERNAL MODULE: ./node_modules/@babel/runtime-corejs2/core-js/is-iterable.js
var is_iterable = __webpack_require__("c8bb");
var is_iterable_default = /*#__PURE__*/__webpack_require__.n(is_iterable);

// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/iterableToArray.js


function _iterableToArray(iter) {
  if (is_iterable_default()(Object(iter)) || Object.prototype.toString.call(iter) === "[object Arguments]") return from_default()(iter);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime-corejs2/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}
// EXTERNAL MODULE: external {"commonjs":"sortablejs","commonjs2":"sortablejs","amd":"sortablejs","root":"Sortable"}
var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_ = __webpack_require__("a352");
var external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_default = /*#__PURE__*/__webpack_require__.n(external_commonjs_sortablejs_commonjs2_sortablejs_amd_sortablejs_root_Sortable_);

// EXTERNAL MODULE: ./src/util/helper.js
var helper = __webpack_require__("c649");

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

  var attrs = keys_default()($attrs).filter(function (key) {
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

  assign_default()(attributes.attrs, componentDataAttrs);

  return attributes;
}

var eventsListened = ["Start", "Add", "Remove", "Update", "End"];
var eventsToEmit = ["Choose", "Unchoose", "Sort", "Filter", "Clone"];
var readonlyProperties = ["Move"].concat(eventsListened, eventsToEmit).map(function (evt) {
  return "on" + evt;
});
var draggingElement = null;
var vuedraggable_props = {
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
  props: vuedraggable_props,
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

    var attributes = keys_default()(this.$attrs).reduce(function (res, key) {
      res[Object(helper["a" /* camelize */])(key)] = _this3.$attrs[key];
      return res;
    }, {});

    var options = assign_default()({}, this.options, attributes, optionsAdded, {
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
          return assign_default()(destination, context);
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

      assign_default()(draggedContext, {
        futureIndex: futureIndex
      });

      var sendEvt = assign_default()({}, evt, {
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


/* harmony default export */ var entry_lib = __webpack_exports__["default"] = (vuedraggable);



/***/ })

/******/ })["default"];
//# sourceMappingURL=vuedraggable.common.js.map

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

/***/ "6673":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var global = __webpack_require__("b5f1");
var userAgent = __webpack_require__("e4e7");

var slice = [].slice;
var MSIE = /MSIE .\./.test(userAgent); // <- dirty ie9- check

var wrap = function (scheduler) {
  return function (handler, timeout /* , ...arguments */) {
    var boundArgs = arguments.length > 2;
    var args = boundArgs ? slice.call(arguments, 2) : undefined;
    return scheduler(boundArgs ? function () {
      // eslint-disable-next-line no-new-func
      (typeof handler == 'function' ? handler : Function(handler)).apply(this, args);
    } : handler, timeout);
  };
};

// ie9- setTimeout & setInterval additional parameters fix
// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#timers
$({ global: true, bind: true, forced: MSIE }, {
  // `setTimeout` method
  // https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
  setTimeout: wrap(global.setTimeout),
  // `setInterval` method
  // https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
  setInterval: wrap(global.setInterval)
});


/***/ }),

/***/ "682f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_0_id_692ab328_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("d303");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_0_id_692ab328_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_0_id_692ab328_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_style_index_0_id_692ab328_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "6870":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $reduce = __webpack_require__("a3a9").left;
var arrayMethodIsStrict = __webpack_require__("9fac");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var STRICT_METHOD = arrayMethodIsStrict('reduce');
var USES_TO_LENGTH = arrayMethodUsesToLength('reduce', { 1: 0 });

// `Array.prototype.reduce` method
// https://tc39.github.io/ecma262/#sec-array.prototype.reduce
$({ target: 'Array', proto: true, forced: !STRICT_METHOD || !USES_TO_LENGTH }, {
  reduce: function reduce(callbackfn /* , initialValue */) {
    return $reduce(this, callbackfn, arguments.length, arguments.length > 1 ? arguments[1] : undefined);
  }
});


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

/***/ "68be":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var exec = __webpack_require__("9a1c");

$({ target: 'RegExp', proto: true, forced: /./.exec !== exec }, {
  exec: exec
});


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

/***/ "6af0":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "6b9e":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"first-name":"First name","last-name":"Last name","official-code":"Official code"},"nl":{"first-name":"Voornaam","last-name":"Achternaam","official-code":"Officiële code"}}')
  delete Component.options._Ctor
}


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

/***/ "6e08":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_style_index_0_id_e1de9e6a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8fb4");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_style_index_0_id_e1de9e6a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_style_index_0_id_e1de9e6a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResultRow_vue_vue_type_style_index_0_id_e1de9e6a_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "6e26":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("0434");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "717c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_style_index_0_id_00a31407_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("fe61");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_style_index_0_id_00a31407_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_style_index_0_id_00a31407_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_style_index_0_id_00a31407_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "725c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_style_index_0_id_0ed11da2_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("3aa4");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_style_index_0_id_0ed11da2_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_style_index_0_id_0ed11da2_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_style_index_0_id_0ed11da2_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "7449":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_style_index_0_id_557783c1_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("bd2f");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_style_index_0_id_557783c1_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_style_index_0_id_557783c1_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_style_index_0_id_557783c1_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "764f":
/***/ (function(module, exports) {

module.exports = false;


/***/ }),

/***/ "76cd":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"adjust-title":"Adjust title","adjust-weight":"Adjust weight","category-settings":"Category Settings","count-towards-endresult-not":"Score does not count towards final result","final-score":"Final score","first-name":"First name","grouped-score":"Grouped score","invisible":"Final score is hidden","item-settings":"Score Settings","last-name":"NAME","make-invisible":"Score is shown. Click to hide.","make-visible":"Score is hidden. Click to show.","saving":"Saving","source-results-warning":"The results of this column refers to source data that no longer exists. You can keep on using this data but synchronizing will have no effect on this column. If you remove the column its results will be gone forever.","total":"Total","uncounted":"Not counted","visible":"Final score is shown","without-category":"Without category"},"nl":{"adjust-title":"Pas titel aan","adjust-weight":"Pas gewicht aan","category-settings":"Categorie-instellingen","count-towards-endresult-not":"Score wordt niet meegeteld voor het eindresultaat","final-score":"Eindcijfer","first-name":"Voornaam","grouped-score":"Gegroepeerde score","invisible":"Eindscore is verborgen","item-settings":"Score-instellingen","last-name":"FAMILIENAAM","make-invisible":"Score wordt weergegeven. Klik om te verbergen.","make-visible":"Score is verborgen. Klik om te tonen.","saving":"Aan het opslaan","source-results-warning":"De resultaten in deze kolom verwijzen naar brondata die niet meer bestaat. Je kan de data verder blijven gebruiken maar synchronizeren zal op deze kolom geen effect hebben. Als je de kolom verwijdert zijn de resultaten ervan voorgoed weg.","total":"Totaal","uncounted":"Niet meegeteld","visible":"Eindscore wordt weergegeven","without-category":"Zonder categorie"}}')
  delete Component.options._Ctor
}


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

/***/ "798b":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_1_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("7d47");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_1_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_1_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_1_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "7d47":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "7d5a":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"cancel":"Cancel","edit":"Edit"},"nl":{"cancel":"Annuleren","edit":"Wijzigen"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "7e05":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"all-imports":"All imports","auth-absent":"Authorized absent","no-score-found":"No score found","not-subscribed":"Not subscribed to course","show":"Show","total":"Total","user-not-in-course":"Student is not subscribed to this course","valid-imports":"Valid imports"},"nl":{"all-imports":"Alle imports","auth-absent":"Gewettigd afwezig","no-score-found":"Geen score gevonden","not-subscribed":"Niet ingeschreven in cursus","show":"Toon","total":"Totaal","user-not-in-course":"Student maakt geen deel uit van deze cursus","valid-imports":"Geldige imports"}}')
  delete Component.options._Ctor
}


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

/***/ "801a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("c6b7");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemSettings_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "80a6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_style_index_0_id_6f48efb4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8f3f");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_style_index_0_id_6f48efb4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_style_index_0_id_6f48efb4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesDropdown_vue_vue_type_style_index_0_id_6f48efb4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "829a":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "8324":
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("e1f8");

/***/ }),

/***/ "832b":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"count-towards-endresult-not":"Score does not count towards final result","final-score":"Final score","not-yet-released":"Not yet released","title":"Title","score":"Score","weight":"Weight"},"nl":{"count-towards-endresult-not":"Score telt niet mee voor het eindresultaat","final-score":"Eindcijfer","not-yet-released":"Nog niet vrijgegeven","title":"Titel","score":"Score","weight":"Gewicht"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "835c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("7d5a");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TableCellInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "84a1":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_style_index_0_id_200a99cc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("5f8f");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_style_index_0_id_200a99cc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_style_index_0_id_200a99cc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_WeightInput_vue_vue_type_style_index_0_id_200a99cc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "89ea":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "8bba":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = require("vue");

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

/***/ "8f3f":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "8fb4":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "8fe0":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"cancel":"Cancel","category-settings":"Category settings","close":"Close","remove":"Remove","remove-category":"Remove category?"},"nl":{"cancel":"Annuleren","category-settings":"Categorie-instellingen","close":"Sluiten","remove":"Verwijderen","remove-category":"Categorie verwijderen?"}}')
  delete Component.options._Ctor
}


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

/***/ "9596":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("3ab7");

// `thisNumberValue` abstract operation
// https://tc39.github.io/ecma262/#sec-thisnumbervalue
module.exports = function (value) {
  if (typeof value != 'number' && classof(value) != 'Number') {
    throw TypeError('Incorrect invocation');
  }
  return +value;
};


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

/***/ "993b":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("832b");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScores_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "9a1c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var regexpFlags = __webpack_require__("89bd");
var stickyHelpers = __webpack_require__("1161");

var nativeExec = RegExp.prototype.exec;
// This always refers to the native implementation, because the
// String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
// which loads this file before patching the method.
var nativeReplace = String.prototype.replace;

var patchedExec = nativeExec;

var UPDATES_LAST_INDEX_WRONG = (function () {
  var re1 = /a/;
  var re2 = /b*/g;
  nativeExec.call(re1, 'a');
  nativeExec.call(re2, 'a');
  return re1.lastIndex !== 0 || re2.lastIndex !== 0;
})();

var UNSUPPORTED_Y = stickyHelpers.UNSUPPORTED_Y || stickyHelpers.BROKEN_CARET;

// nonparticipating capturing group, copied from es5-shim's String#split patch.
var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;

var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED || UNSUPPORTED_Y;

if (PATCH) {
  patchedExec = function exec(str) {
    var re = this;
    var lastIndex, reCopy, match, i;
    var sticky = UNSUPPORTED_Y && re.sticky;
    var flags = regexpFlags.call(re);
    var source = re.source;
    var charsAdded = 0;
    var strCopy = str;

    if (sticky) {
      flags = flags.replace('y', '');
      if (flags.indexOf('g') === -1) {
        flags += 'g';
      }

      strCopy = String(str).slice(re.lastIndex);
      // Support anchored sticky behavior.
      if (re.lastIndex > 0 && (!re.multiline || re.multiline && str[re.lastIndex - 1] !== '\n')) {
        source = '(?: ' + source + ')';
        strCopy = ' ' + strCopy;
        charsAdded++;
      }
      // ^(? + rx + ) is needed, in combination with some str slicing, to
      // simulate the 'y' flag.
      reCopy = new RegExp('^(?:' + source + ')', flags);
    }

    if (NPCG_INCLUDED) {
      reCopy = new RegExp('^' + source + '$(?!\\s)', flags);
    }
    if (UPDATES_LAST_INDEX_WRONG) lastIndex = re.lastIndex;

    match = nativeExec.call(sticky ? reCopy : re, strCopy);

    if (sticky) {
      if (match) {
        match.input = match.input.slice(charsAdded);
        match[0] = match[0].slice(charsAdded);
        match.index = re.lastIndex;
        re.lastIndex += match[0].length;
      } else re.lastIndex = 0;
    } else if (UPDATES_LAST_INDEX_WRONG && match) {
      re.lastIndex = re.global ? match.index + match[0].length : lastIndex;
    }
    if (NPCG_INCLUDED && match && match.length > 1) {
      // Fix browsers whose `exec` methods don't consistently return `undefined`
      // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
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

/***/ "9a88":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"comment":"comment","csv-must-look-like":"The CSV file must look like this","import-comment-title":"Choose a title for the column of feedback you wish to import (can be left empty).","import-id":"One of the following: <ul><li>email address</li><li>username</li><li>official code</li></ul>","import-score":"One of the following: <ul><li>number</li><li>aabs (authorized absent)</li></ul>","import-score-title":"Choose a title for the column of scores you wish to import.","mandatory-fields":"mandatory fields are marked in bold","title":"title"},"nl":{"comment":"commentaar","csv-must-look-like":"Het CSV bestand moet er als volgt uit zien","import-comment-title":"Kies hier een titel voor de feedbackkolom die je wenst te importeren (mag ook leeggelaten worden).","import-id":"Een van de volgende: <ul><li>e-mailadres</li><li>gebruikersnaam</li><li>officiële code (stamboeknummer)</li></ul>","import-score":"Een van de volgende: <ul><li>cijfer</li><li>gafw (gewettigd afwezig)</li></ul>","import-score-title":"Kies hier een titel voor de scorekolom die je wenst te importeren.","mandatory-fields":"verplichte velden zijn in het vet aangeduid","title":"titel"}}')
  delete Component.options._Ctor
}


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

/***/ "9b89":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScoresApp_vue_vue_type_style_index_0_id_022d871c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8bba");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScoresApp_vue_vue_type_style_index_0_id_022d871c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScoresApp_vue_vue_type_style_index_0_id_022d871c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserScoresApp_vue_vue_type_style_index_0_id_022d871c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "a07a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_0_id_2ea6a10f_scoped_true_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("5849");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_0_id_2ea6a10f_scoped_true_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_0_id_2ea6a10f_scoped_true_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_0_id_2ea6a10f_scoped_true_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "a0e5":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"category":"Category","error-Conflict":"The server responded with an error due to a conflict. Probably someone else is working on the same gradebook at this time. Please refresh the page and try again.","error-Forbidden":"The server responded with an error. Possibly your last change(s) haven\u0027t been saved correctly. Please refresh the page and try again.","error-LoggedOut":"It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.","error-NotFound":"The server responded with an error. Possibly your last change(s) haven\u0027t been saved correctly. Please refresh the page and try again.","error-Timeout":"The server took too long to respond. Your changes have possibly not been saved. You can try again later.","error-Unknown":"An unknown error happened. Possibly your last change(s) haven\u0027t been saved. Please refresh the page and try again.","find-student":"Find student","import":"Import","new":"New","new-category":"New category","new-score":"New score","show":"Show","synchronize-scores":"Synchronize","update-final-scores":"Update final scores"},"nl":{"category":"Categorie","error-Conflict":"Serverfout vanwege een conflict. Misschien werkt iemand anders ook nog aan dit puntenboekje op dit moment. Gelieve de pagina te herladen en opnieuw te proberen.","error-Forbidden":"Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.","error-LoggedOut":"Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.","error-NotFound":"Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.","error-Timeout":"De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.","error-Unknown":"Je laatste wijzigingen werden mogelijk niet opgeslagen vanwege een onbekende fout. Gelieve de pagina te herladen en opnieuw te proberen.","find-student":"Zoek student","import":"Importeer","new":"Nieuw","new-category":"Nieuwe categorie","new-score":"Nieuwe score","show":"Toon","synchronize-scores":"Synchronizeer","update-final-scores":"Update eindcijfers"}}')
  delete Component.options._Ctor
}


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

/***/ "a3a9":
/***/ (function(module, exports, __webpack_require__) {

var aFunction = __webpack_require__("6373");
var toObject = __webpack_require__("64f1");
var IndexedObject = __webpack_require__("2be1");
var toLength = __webpack_require__("7cf1");

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

/***/ "a4c2":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var $every = __webpack_require__("ec68").every;
var arrayMethodIsStrict = __webpack_require__("9fac");
var arrayMethodUsesToLength = __webpack_require__("3bd5");

var STRICT_METHOD = arrayMethodIsStrict('every');
var USES_TO_LENGTH = arrayMethodUsesToLength('every');

// `Array.prototype.every` method
// https://tc39.github.io/ecma262/#sec-array.prototype.every
$({ target: 'Array', proto: true, forced: !STRICT_METHOD || !USES_TO_LENGTH }, {
  every: function every(callbackfn /* , thisArg */) {
    return $every(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "a549":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "a66a":
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

/***/ "b03a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_0_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("4581");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_0_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_0_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_style_index_0_id_990d6710_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "b072":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var IndexedObject = __webpack_require__("2be1");
var toIndexedObject = __webpack_require__("2060");
var arrayMethodIsStrict = __webpack_require__("9fac");

var nativeJoin = [].join;

var ES3_STRINGS = IndexedObject != Object;
var STRICT_METHOD = arrayMethodIsStrict('join', ',');

// `Array.prototype.join` method
// https://tc39.github.io/ecma262/#sec-array.prototype.join
$({ target: 'Array', proto: true, forced: ES3_STRINGS || !STRICT_METHOD }, {
  join: function join(separator) {
    return nativeJoin.call(toIndexedObject(this), separator === undefined ? ',' : separator);
  }
});


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

/***/ "b1c9":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var DESCRIPTORS = __webpack_require__("70b9");
var defineProperties = __webpack_require__("2dc3");

// `Object.defineProperties` method
// https://tc39.github.io/ecma262/#sec-object.defineproperties
$({ target: 'Object', stat: true, forced: !DESCRIPTORS, sham: !DESCRIPTORS }, {
  defineProperties: defineProperties
});


/***/ }),

/***/ "b1cc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__("4a1c");
var aFunction = __webpack_require__("6373");
var toObject = __webpack_require__("64f1");
var fails = __webpack_require__("7104");
var arrayMethodIsStrict = __webpack_require__("9fac");

var test = [];
var nativeSort = test.sort;

// IE8-
var FAILS_ON_UNDEFINED = fails(function () {
  test.sort(undefined);
});
// V8 bug
var FAILS_ON_NULL = fails(function () {
  test.sort(null);
});
// Old WebKit
var STRICT_METHOD = arrayMethodIsStrict('sort');

var FORCED = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD;

// `Array.prototype.sort` method
// https://tc39.github.io/ecma262/#sec-array.prototype.sort
$({ target: 'Array', proto: true, forced: FORCED }, {
  sort: function sort(comparefn) {
    return comparefn === undefined
      ? nativeSort.call(toObject(this))
      : nativeSort.call(toObject(this), aFunction(comparefn));
  }
});


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

/***/ "b67c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_1_id_03563a68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("829a");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_1_id_03563a68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_1_id_03563a68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_1_id_03563a68_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "b6bc":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// TODO: Remove from `core-js@4` since it's moved to entry points
__webpack_require__("68be");
var redefine = __webpack_require__("6a8a");
var fails = __webpack_require__("7104");
var wellKnownSymbol = __webpack_require__("4736");
var regexpExec = __webpack_require__("9a1c");
var createNonEnumerableProperty = __webpack_require__("0209");

var SPECIES = wellKnownSymbol('species');

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

// IE <= 11 replaces $0 with the whole match, as if it was $&
// https://stackoverflow.com/questions/6024666/getting-ie-to-replace-a-regex-with-the-literal-string-0
var REPLACE_KEEPS_$0 = (function () {
  return 'a'.replace(/./, '$0') === '$0';
})();

var REPLACE = wellKnownSymbol('replace');
// Safari <= 13.0.3(?) substitutes nth capture where n>m with an empty string
var REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE = (function () {
  if (/./[REPLACE]) {
    return /./[REPLACE]('a', '$0') === '';
  }
  return false;
})();

// Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
// Weex JS has frozen built-in prototypes, so use try / catch wrapper
var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = !fails(function () {
  var re = /(?:)/;
  var originalExec = re.exec;
  re.exec = function () { return originalExec.apply(this, arguments); };
  var result = 'ab'.split(re);
  return result.length !== 2 || result[0] !== 'a' || result[1] !== 'b';
});

module.exports = function (KEY, length, exec, sham) {
  var SYMBOL = wellKnownSymbol(KEY);

  var DELEGATES_TO_SYMBOL = !fails(function () {
    // String methods call symbol-named RegEp methods
    var O = {};
    O[SYMBOL] = function () { return 7; };
    return ''[KEY](O) != 7;
  });

  var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL && !fails(function () {
    // Symbol-named RegExp methods call .exec
    var execCalled = false;
    var re = /a/;

    if (KEY === 'split') {
      // We can't use real regex here since it causes deoptimization
      // and serious performance degradation in V8
      // https://github.com/zloirock/core-js/issues/306
      re = {};
      // RegExp[@@split] doesn't call the regex's exec method, but first creates
      // a new one. We need to return the patched regex when creating the new one.
      re.constructor = {};
      re.constructor[SPECIES] = function () { return re; };
      re.flags = '';
      re[SYMBOL] = /./[SYMBOL];
    }

    re.exec = function () { execCalled = true; return null; };

    re[SYMBOL]('');
    return !execCalled;
  });

  if (
    !DELEGATES_TO_SYMBOL ||
    !DELEGATES_TO_EXEC ||
    (KEY === 'replace' && !(
      REPLACE_SUPPORTS_NAMED_GROUPS &&
      REPLACE_KEEPS_$0 &&
      !REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE
    )) ||
    (KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC)
  ) {
    var nativeRegExpMethod = /./[SYMBOL];
    var methods = exec(SYMBOL, ''[KEY], function (nativeMethod, regexp, str, arg2, forceStringMethod) {
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
    }, {
      REPLACE_KEEPS_$0: REPLACE_KEEPS_$0,
      REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE: REGEXP_REPLACE_SUBSTITUTES_UNDEFINED_CAPTURE
    });
    var stringMethod = methods[0];
    var regexMethod = methods[1];

    redefine(String.prototype, KEY, stringMethod);
    redefine(RegExp.prototype, SYMBOL, length == 2
      // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
      // 21.2.5.11 RegExp.prototype[@@split](string, limit)
      ? function (string, arg) { return regexMethod.call(string, this, arg); }
      // 21.2.5.6 RegExp.prototype[@@match](string)
      // 21.2.5.9 RegExp.prototype[@@search](string)
      : function (string) { return regexMethod.call(string, this); }
    );
  }

  if (sham) createNonEnumerableProperty(RegExp.prototype[SYMBOL], 'sham', true);
};


/***/ }),

/***/ "b7cb":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var trim = __webpack_require__("7c64").trim;
var whitespaces = __webpack_require__("9538");

var $parseFloat = global.parseFloat;
var FORCED = 1 / $parseFloat(whitespaces + '-0') !== -Infinity;

// `parseFloat` method
// https://tc39.github.io/ecma262/#sec-parsefloat-string
module.exports = FORCED ? function parseFloat(string) {
  var trimmedString = trim(String(string));
  var result = $parseFloat(trimmedString);
  return result === 0 && trimmedString.charAt(0) == '-' ? -0 : result;
} : $parseFloat;


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

/***/ "b7e4":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var DESCRIPTORS = __webpack_require__("70b9");
var ownKeys = __webpack_require__("17bb");
var toIndexedObject = __webpack_require__("2060");
var getOwnPropertyDescriptorModule = __webpack_require__("05dc");
var createProperty = __webpack_require__("c46f");

// `Object.getOwnPropertyDescriptors` method
// https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptors
$({ target: 'Object', stat: true, sham: !DESCRIPTORS }, {
  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
    var O = toIndexedObject(object);
    var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
    var keys = ownKeys(O);
    var result = {};
    var index = 0;
    var key, descriptor;
    while (keys.length > index) {
      descriptor = getOwnPropertyDescriptor(O, key = keys[index++]);
      if (descriptor !== undefined) createProperty(result, key, descriptor);
    }
    return result;
  }
});


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

/***/ "b974":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("1880");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_style_index_1_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "ba2e":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"aabs":"AABS","absent":"Absent","auth-absent":"Authorized absent","comments":"Comments","score":"Score","use-source-result":"Use source result"},"nl":{"aabs":"GAFW","absent":"Afwezig","auth-absent":"Gewettigd afwezig","comments":"Opmerkingen","score":"Score","use-source-result":"Gebruik bronresultaat"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "bab4":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var fails = __webpack_require__("7104");
var toIndexedObject = __webpack_require__("2060");
var nativeGetOwnPropertyDescriptor = __webpack_require__("05dc").f;
var DESCRIPTORS = __webpack_require__("70b9");

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

/***/ "bced":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("bf70");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ErrorDisplay_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "bd2f":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "bf70":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"errors":"Error(s)"},"nl":{"errors":"Fout(en)"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "c085":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("7e05");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "c22f":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_style_index_0_id_7e31a3f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a549");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_style_index_0_id_7e31a3f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_style_index_0_id_7e31a3f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ImportsTable_vue_vue_type_style_index_0_id_7e31a3f6_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "c266":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a66a");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Main_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "c2d2":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "c6b7":
/***/ (function(module, exports) {

module.exports = function (Component) {
  Component.options.__i18n = Component.options.__i18n || []
  Component.options.__i18n.push('{"en":{"aabs":"aabs","auth-absent":"Authorized absent","authorized-absence":"In the event of authorized absence","cancel":"Cancel","close":"Close","column-settings":"Column settings","count-towards-endresult":"Count towards end result","count-towards-endresult-not":"Score does not count towards final result","group-scores":"Group scores","grouped-scores":"Grouped scores","make-visible":"Show score to student","maximum-towards-endresult":"Maximum score (100%) counts towards final result","minimum-towards-endresult":"Minimum score (0%) counts towards final result","no-score-found":"No score found","remove":"Remove","remove-from-overview":"Remove score \u0027{title}\u0027 from overview?","settings":"Settings","unauthorized-absence":"In the absence of a score (without authorized absence)","weight":"Weight"},"nl":{"aabs":"gafw","auth-absent":"Gewettigd afwezig","authorized-absence":"Bij gewettigde afwezigheid","cancel":"Annuleren","close":"Sluiten","column-settings":"Kolominstellingen","count-towards-endresult":"Meetellen voor eindresultaat","count-towards-endresult-not":"Score niet meetellen voor het eindresultaat","group-scores":"Scores groeperen","grouped-scores":"Gegroepeerde scores","make-visible":"Score weergeven voor student","maximum-towards-endresult":"Maximale score (100%) meetellen voor het eindresultaat","minimum-towards-endresult":"Minimale score (0%) meetellen voor het eindresultaat","no-score-found":"Geen score gevonden","remove":"Verwijderen","remove-from-overview":"Score \u0027{title}\u0027 verwijderen uit overzicht?","settings":"Instellingen","unauthorized-absence":"Bij ontbreken van score (zonder gewettigde afwezigheid)","weight":"Gewicht"}}')
  delete Component.options._Ctor
}


/***/ }),

/***/ "c77a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("6b9e");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "c9b7":
/***/ (function(module, exports, __webpack_require__) {

var $ = __webpack_require__("4a1c");
var parseFloatImplementation = __webpack_require__("b7cb");

// `parseFloat` method
// https://tc39.github.io/ecma262/#sec-parsefloat-string
$({ global: true, forced: parseFloat != parseFloatImplementation }, {
  parseFloat: parseFloatImplementation
});


/***/ }),

/***/ "cc07":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("b5f1");
var trim = __webpack_require__("7c64").trim;
var whitespaces = __webpack_require__("9538");

var $parseInt = global.parseInt;
var hex = /^[+-]?0[Xx]/;
var FORCED = $parseInt(whitespaces + '08') !== 8 || $parseInt(whitespaces + '0x16') !== 22;

// `parseInt` method
// https://tc39.github.io/ecma262/#sec-parseint-string-radix
module.exports = FORCED ? function parseInt(string, radix) {
  var S = trim(String(string));
  return $parseInt(S, (radix >>> 0) || (hex.test(S) ? 16 : 10));
} : $parseInt;


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

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/App.vue?vue&type=template&id=09bcfe23&
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{attrs:{"id":"app"}},[_c('Main',{attrs:{"api-config":_vm.apiConfig}}),(_vm.debugServerResponse)?_c('div',{attrs:{"id":"server-response"}}):_vm._e()],1)}
var staticRenderFns = []


// CONCATENATED MODULE: ./src/App.vue?vue&type=template&id=09bcfe23&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/classCallCheck.js
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.symbol.js
var es_symbol = __webpack_require__("fba6");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.symbol.description.js
var es_symbol_description = __webpack_require__("ce1a");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.symbol.iterator.js
var es_symbol_iterator = __webpack_require__("05e6");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.iterator.js
var es_array_iterator = __webpack_require__("5301");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.to-string.js
var es_object_to_string = __webpack_require__("0379");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.string.iterator.js
var es_string_iterator = __webpack_require__("96cd");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/web.dom-collections.iterator.js
var web_dom_collections_iterator = __webpack_require__("80fa");

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
  $internalHooks.push.apply($internalHooks, _toConsumableArray(keys));
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

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/Main.vue?vue&type=template&id=03563a68&scoped=true&
var Mainvue_type_template_id_03563a68_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"u-contents"},[(_vm.gradeBook)?_c('div',{attrs:{"aria-hidden":(_vm.itemSettings !== null || !!_vm.selectedCategory || !!_vm.errorData)}},[_c('div',{staticClass:"u-flex u-flex-wrap gradebook-toolbar"},[_c('div',{staticClass:"input-group"},[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.searchTerm),expression:"searchTerm"}],staticClass:"form-control",attrs:{"type":"text","placeholder":_vm.$t('find-student')},domProps:{"value":(_vm.searchTerm)},on:{"input":function($event){if($event.target.composing){ return; }_vm.searchTerm=$event.target.value}}}),_c('div',{staticClass:"input-group-btn"},[_c('button',{staticClass:"btn btn-default",attrs:{"name":"clear","value":"clear"},on:{"click":function($event){_vm.searchTerm = ''}}},[_c('span',{staticClass:"glyphicon glyphicon-remove",attrs:{"aria-hidden":"true"}})])])]),_c('grades-dropdown',{attrs:{"id":"dropdown-main","graded-items":_vm.gradeBook.statusGradedItems},on:{"toggle":_vm.toggleGradeItem}}),_c('div',{staticClass:"u-flex u-justify-content-end u-gap-small u-ml-auto gradebook-create-actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.synchronizeGradeBook}},[_c('i',{staticClass:"fa fa-refresh",attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('synchronize-scores')))]),_c('div',{staticClass:"btn-group"},[_c('a',{staticClass:"btn btn-default btn-sm dropdown-toggle",attrs:{"data-toggle":"dropdown","aria-haspopup":"true"}},[_c('i',{staticClass:"fa fa-plus",attrs:{"aria-hidden":"true"}}),_c('span',[_vm._v(_vm._s(_vm.$t('new')))]),_vm._v(" "),_c('span',{staticClass:"caret",attrs:{"aria-hidden":"true"}})]),_c('ul',{staticClass:"dropdown-menu",attrs:{"role":"menu"}},[_c('li',{staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{attrs:{"role":"menuitem"},on:{"click":function($event){$event.preventDefault();return _vm.createNewScore($event)}}},[_vm._v(_vm._s(_vm.$t('new-score')))])]),_c('li',{staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{attrs:{"role":"menuitem"},on:{"click":function($event){$event.preventDefault();return _vm.createNewCategory($event)}}},[_vm._v(_vm._s(_vm.$t('new-category')))])]),_c('li',{staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{attrs:{"role":"menuitem","href":_vm.apiConfig.gradeBookImportCsvURL}},[_vm._v(_vm._s(_vm.$t('import'))+"…")])])])]),(_vm.gradeBook.totalsNeedUpdating)?_c('button',{staticClass:"btn btn-update-totals btn-primary btn-sm u-font-medium u-text-upper",on:{"click":_vm.updateTotalScores}},[_c('i',{staticClass:"fa fa-exclamation-circle",attrs:{"aria-hidden":"true"}}),_vm._v(_vm._s(_vm.$t('update-final-scores'))+" ")]):_vm._e(),_c('div',{staticClass:"btn-group"},[_c('a',{staticClass:"btn btn-default btn-sm dropdown-toggle",attrs:{"data-toggle":"dropdown","aria-haspopup":"true","title":((_vm.$t('show')) + " " + _vm.itemsPerPage + " items")}},[_c('span',[_vm._v(_vm._s(_vm.$t('show'))+" "+_vm._s(_vm.itemsPerPage)+" items")]),_vm._v(" "),_c('span',{staticClass:"caret",attrs:{"aria-hidden":"true"}})]),_c('ul',{staticClass:"dropdown-menu dropdown-menu-right",attrs:{"role":"listbox"}},_vm._l(([5, 10, 15, 20, 50]),function(count){return _c('li',{key:'per-page-' + count,staticClass:"u-cursor-pointer",attrs:{"role":"presentation"}},[_c('a',{class:_vm.itemsPerPage === count ? 'selected' : 'not-selected',attrs:{"role":"option","aria-selected":_vm.itemsPerPage === count ? 'true' : 'false'},on:{"click":function($event){return _vm.setItemsPerPage(count)}}},[_c('span',[_vm._v(_vm._s(_vm.$t('show'))+" "+_vm._s(count)+" items")])])])}),0)])])],1),_c('div',{staticClass:"gradebook-table-container"},[_c('grades-table',{attrs:{"grade-book":_vm.gradeBook,"search-terms":_vm.studentSearchTerms,"busy":_vm.tableBusy,"add-column-id":_vm.addColumnId,"save-column-id":_vm.saveColumnId,"save-category-id":_vm.saveCategoryId,"items-per-page":_vm.itemsPerPage,"grade-book-root-url":_vm.apiConfig.gradeBookRootURL},on:{"item-settings":function($event){_vm.itemSettings = $event},"category-settings":function($event){_vm.categorySettings = $event},"update-score-comment":_vm.onUpdateScoreComment,"overwrite-result":_vm.onOverwriteResult,"revert-overwritten-result":_vm.onRevertOverwrittenResult,"change-category":_vm.onChangeCategory,"move-category":_vm.onMoveCategory,"change-gradecolumn":_vm.onChangeGradeColumn,"change-gradecolumn-category":_vm.onChangeGradeColumnCategory,"move-gradecolumn":_vm.onMoveGradeColumn}})],1)]):_c('div',{staticClass:"lds-ellipsis",attrs:{"aria-hidden":"true"}},[_c('div'),_c('div'),_c('div'),_c('div')]),(_vm.itemSettings !== null)?_c('item-settings',{attrs:{"grade-book":_vm.gradeBook,"column-id":_vm.itemSettings},on:{"close":function($event){_vm.itemSettings = null},"item-settings":function($event){_vm.itemSettings = $event},"change-gradecolumn":_vm.onChangeGradeColumn,"add-subitem":_vm.onAddSubItem,"remove-subitem":_vm.onRemoveSubItem,"remove-column":_vm.onRemoveColumn}}):_vm._e(),(_vm.selectedCategory)?_c('category-settings',{attrs:{"grade-book":_vm.gradeBook,"category":_vm.selectedCategory},on:{"close":_vm.closeSelectedCategory,"change-category":_vm.onChangeCategory,"remove-category":_vm.onRemoveCategory}}):_vm._e(),(_vm.errorData)?_c('error-display',{on:{"close":_vm.closeErrorDisplay}},[_vm._v(_vm._s(_vm.$t(("error-" + (_vm.errorData.type)))))]):_vm._e()],1)}
var Mainvue_type_template_id_03563a68_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=template&id=03563a68&scoped=true&

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.filter.js
var es_array_filter = __webpack_require__("e57b");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.find.js
var es_array_find = __webpack_require__("529c");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.for-each.js
var es_array_for_each = __webpack_require__("d656");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.index-of.js
var es_array_index_of = __webpack_require__("62c8");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.function.bind.js
var es_function_bind = __webpack_require__("6bd9");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.parse-int.js
var es_parse_int = __webpack_require__("3336");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.regexp.exec.js
var es_regexp_exec = __webpack_require__("68be");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.string.split.js
var es_string_split = __webpack_require__("f347");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/web.dom-collections.for-each.js
var web_dom_collections_for_each = __webpack_require__("5270");

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
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/GradesDropdown.vue?vue&type=template&id=6f48efb4&scoped=true&
var GradesDropdownvue_type_template_id_6f48efb4_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{directives:[{name:"clickoutside",rawName:"v-clickoutside",value:(_vm.close),expression:"close"}],staticClass:"btn-group",class:{'open': _vm.isOpen},attrs:{"id":_vm.id}},[_c('button',{staticClass:"u-flex u-align-items-center u-justify-content-between btn dropdown-toggle",attrs:{"aria-haspopup":"true","aria-expanded":_vm.isOpen,"title":_vm.$t('add-remove-scores')},on:{"click":function($event){_vm.isOpen = !_vm.isOpen}}},[_c('span',[_vm._v(_vm._s(_vm.$t('add-remove-scores')))]),_vm._v(" "),_c('span',{staticClass:"caret",attrs:{"aria-hidden":"true"}})]),_c('ul',{staticClass:"dropdown-menu"},_vm._l((_vm.gradedItems),function(item,index){return _c('li',{key:("item-" + index),attrs:{"role":"presentation"},on:{"click":function($event){$event.stopPropagation();}}},[_c('a',{staticClass:"dropdown-item",class:{'mod-removed': item.removed, 'mod-checked': item.checked},attrs:{"role":"menuitem","href":"#","target":"_self"}},[_c('b-form-checkbox',{class:{'is-disabled': item.disabled},attrs:{"id":(_vm.id + "-item-" + index),"checked":item.checked,"disabled":item.disabled || (item.removed && !item.checked)},on:{"change":function($event){return _vm.toggleItem(item, index)}}},[_vm._v(" "+_vm._s(item.title)+" "),_c('div',{staticClass:"score-breadcrumb-trail"},[_vm._v(_vm._s(_vm._f("breadcrumb")(item)))])])],1)])}),0),(_vm.gradeItemToRemove)?_c('div',{staticClass:"modal-wrapper",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center modal-content"},[_c('div',{staticClass:"modal-content-title"},[_vm._v(_vm._s(_vm.$t('remove-from-overview', {title: _vm.gradeItemToRemove.title})))]),_c('div',{staticClass:"u-flex actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.remove}},[_vm._v(_vm._s(_vm.$t('remove')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])]):_vm._e()])}
var GradesDropdownvue_type_template_id_6f48efb4_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/GradesDropdown.vue?vue&type=template&id=6f48efb4&scoped=true&

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.concat.js
var es_array_concat = __webpack_require__("236c");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.join.js
var es_array_join = __webpack_require__("b072");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/GradesDropdown.vue?vue&type=script&lang=ts&










external_commonjs_vue_commonjs2_vue_root_Vue_default.a.directive('clickoutside', {
  inserted: function inserted(el, binding, vnode) {
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
  unbind: function unbind(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent);
    document.body.removeEventListener('touchstart', el.clickOutsideEvent);
  }
});

var GradesDropdownvue_type_script_lang_ts_GradesDropdown =
/*#__PURE__*/
function (_Vue) {
  _inherits(GradesDropdown, _Vue);

  function GradesDropdown() {
    var _this;

    _classCallCheck(this, GradesDropdown);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(GradesDropdown).apply(this, arguments));
    _this.isOpen = false;
    _this.gradeItemToRemove = null;
    return _this;
  } // eslint-disable-next-line no-unused-vars


  _createClass(GradesDropdown, [{
    key: "toggleItem",
    value: function toggleItem(item, index) {
      if (item.checked) {
        this.gradeItemToRemove = item;
        return;
      }

      this.$emit('toggle', item, !item.checked);
    }
  }, {
    key: "open",
    value: function open() {
      this.isOpen = true;
    }
  }, {
    key: "close",
    value: function close() {
      this.isOpen = false;
    }
  }, {
    key: "cancel",
    value: function cancel() {
      var _this2 = this;

      if (this.gradeItemToRemove) {
        var index = this.gradedItems.indexOf(this.gradeItemToRemove);

        if (index !== -1) {
          this.$nextTick(function () {
            return document.querySelector("#".concat(_this2.id, "-item-").concat(index)).checked = true;
          });
        }
      }

      this.gradeItemToRemove = null;
    }
  }, {
    key: "remove",
    value: function remove() {
      if (this.gradeItemToRemove) {
        this.$emit('toggle', this.gradeItemToRemove, false);
      }

      this.gradeItemToRemove = null;
    }
  }]);

  return GradesDropdown;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  "default": ''
})], GradesDropdownvue_type_script_lang_ts_GradesDropdown.prototype, "id", void 0);

__decorate([Prop({
  type: Array,
  required: true
})], GradesDropdownvue_type_script_lang_ts_GradesDropdown.prototype, "gradedItems", void 0);

GradesDropdownvue_type_script_lang_ts_GradesDropdown = __decorate([vue_class_component_esm({
  filters: {
    breadcrumb: function breadcrumb(gradedItem) {
      return gradedItem.breadcrumb.join(' » ');
    }
  }
})], GradesDropdownvue_type_script_lang_ts_GradesDropdown);
/* harmony default export */ var GradesDropdownvue_type_script_lang_ts_ = (GradesDropdownvue_type_script_lang_ts_GradesDropdown);
// CONCATENATED MODULE: ./src/components/GradesDropdown.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_GradesDropdownvue_type_script_lang_ts_ = (GradesDropdownvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/GradesDropdown.vue?vue&type=style&index=0&id=6f48efb4&scoped=true&lang=css&
var GradesDropdownvue_type_style_index_0_id_6f48efb4_scoped_true_lang_css_ = __webpack_require__("80a6");

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

// EXTERNAL MODULE: ./src/components/GradesDropdown.vue?vue&type=custom&index=0&blockType=i18n
var GradesDropdownvue_type_custom_index_0_blockType_i18n = __webpack_require__("6e26");

// CONCATENATED MODULE: ./src/components/GradesDropdown.vue






/* normalize component */

var component = normalizeComponent(
  components_GradesDropdownvue_type_script_lang_ts_,
  GradesDropdownvue_type_template_id_6f48efb4_scoped_true_render,
  GradesDropdownvue_type_template_id_6f48efb4_scoped_true_staticRenderFns,
  false,
  null,
  "6f48efb4",
  null
  
)

/* custom blocks */

if (typeof GradesDropdownvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(GradesDropdownvue_type_custom_index_0_blockType_i18n["default"])(component)

/* harmony default export */ var components_GradesDropdown = (component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/GradesTable.vue?vue&type=template&id=990d6710&scoped=true&
var GradesTablevue_type_template_id_990d6710_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('div',{staticClass:"table-wrap u-relative"},[_c('table',{staticClass:"gradebook-table",class:{'is-dragging': _vm.isDraggingColumn, 'is-category-drop': _vm.categoryDropArea !== null },attrs:{"id":"gradebook-table","aria-busy":_vm.busy}},[_c('thead',[(_vm.gradeBook.categories.length)?_c('tr',{staticClass:"table-row table-head-row table-categories-row"},[_c('th',{staticClass:"col-sticky table-student"}),_c('draggable',{staticClass:"u-contents",attrs:{"list":_vm.gradeBook.categories,"tag":"div","disabled":_vm.catEditItemId !== null},on:{"end":_vm.onDragEnd}},_vm._l((_vm.gradeBook.categories),function(ref){
var id = ref.id;
var title = ref.title;
var color = ref.color;
var columnIds = ref.columnIds;
return _c('th',{key:("category-" + id),staticClass:"category u-relative u-font-medium",class:{'is-droppable': _vm.categoryDropArea === id},style:(("--color: " + color + ";")),attrs:{"draggable":"","colspan":Math.max(columnIds.length, 1)},on:{"dragstart":function($event){return _vm.startDragCategory($event, id)},"dragover":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, id)},"dragenter":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, id)},"dragleave":function($event){_vm.categoryDropArea = null},"drop":function($event){(_vm.isDraggingColumn || _vm.isDraggingCategory) && _vm.onDrop($event, id)}}},[(_vm.catEditItemId === id)?_c('item-title-input',{staticClass:"item-title-input",attrs:{"item-title":title},on:{"cancel":function($event){_vm.catEditItemId = null},"ok":function($event){return _vm.setCategoryTitle(id, $event)}}}):(id !== 0)?_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-between u-cursor-pointer",attrs:{"title":_vm.$t('adjust-title')},on:{"dblclick":function($event){return _vm.showCategoryTitleDialog(id)}}},[_vm._v(_vm._s(title)+" "),(_vm.isSavingCategoryWithId(id))?_c('div',{staticClass:"spin",attrs:{"role":"status","aria-busy":"true","aria-label":_vm.$t('saving')}},[_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin",attrs:{"aria-hidden":"true"}})]):_vm._e(),_c('button',{staticClass:"btn-settings",attrs:{"title":_vm.$t('category-settings')},on:{"click":function($event){return _vm.showCategorySettings(id)}}},[_c('i',{staticClass:"fa fa-gear u-inline-block",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('category-settings')))])])]):_vm._e()],1)}),0),(_vm.showNullCategory)?_c('th',{staticClass:"mod-no-category-assigned",class:{'is-droppable': _vm.categoryDropArea === 0},attrs:{"colspan":Math.max(_vm.gradeBook.nullCategory.columnIds.length, 1),"title":_vm.$t('without-category')},on:{"dragover":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, 0)},"dragenter":function($event){$event.preventDefault();return _vm.onDropAreaOverEnter($event, 0)},"dragleave":function($event){_vm.categoryDropArea = null},"drop":function($event){(_vm.isDraggingColumn || _vm.isDraggingCategory) && _vm.onDrop($event, 0)}}}):_vm._e(),_c('th',{staticClass:"col-sticky table-student-total"})],1):_vm._e(),_c('tr',{staticClass:"table-row table-head-row table-scores-row"},[_c('th',{staticClass:"col-sticky table-student"},[_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('lastname')},on:{"click":function($event){return _vm.sortByNameField('lastname')}}},[_vm._v(_vm._s(_vm.$t('last-name')))]),_vm._v(" "),_c('a',{staticClass:"tbl-sort-option",attrs:{"aria-sort":_vm.getSortStatus('firstname')},on:{"click":function($event){return _vm.sortByNameField('firstname')}}},[_vm._v(_vm._s(_vm.$t('first-name')))])]),_vm._l((_vm.displayedCategories),function(category){return _c('draggable',{key:("category-score-" + (category.id)),staticClass:"u-contents",attrs:{"list":category.columnIds,"tag":"div","ghost-class":"ghost","disabled":_vm.editItemId !== null || _vm.weightEditItemId !== null},on:{"end":_vm.onDragEnd}},[(category.columnIds.length === 0)?_c('th',{key:("item-id-" + (category.id))}):_vm._l((_vm.getColumns(category)),function(column){return _c('th',{key:("item-id-" + (category.id) + "--" + (column.id) + "-name"),class:{'unreleased-score-cell': !column.released, 'uncounted-score-cell': !column.countsForEndResult, 'u-relative': column.isEditing},attrs:{"draggable":""},on:{"dragstart":function($event){return _vm.startDragColumn($event, column.id)},"drop":function($event){(_vm.isDraggingColumn || _vm.isDraggingCategory) && _vm.onDrop($event, -1)}}},[(column.isEditingTitle)?_c('item-title-input',{staticClass:"item-title-input",attrs:{"item-title":column.title},on:{"cancel":function($event){_vm.editItemId = null},"ok":function($event){return _vm.setTitle(column.id, $event)}}}):(column.isEditingWeight)?[_c('span',{staticClass:"column-title"},[(column.isGrouped)?_c('i',{staticClass:"fa fa-group",attrs:{"aria-hidden":"true"}}):_vm._e(),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('grouped-score')))]),_vm._v(_vm._s(column.title))]),_c('weight-input',{staticClass:"weight-input",attrs:{"item-weight":column.weight},on:{"cancel":function($event){_vm.weightEditItemId = null},"ok":function($event){return _vm.setWeight(column.id, $event)}}})]:[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-between u-cursor-pointer",attrs:{"title":_vm.$t('adjust-title')},on:{"dblclick":function($event){return _vm.showColumnTitleDialog(column.id)}}},[_c('span',{staticClass:"column-title",attrs:{"id":((column.id) + "-title")}},[(column.isGrouped)?_c('i',{staticClass:"fa fa-group",attrs:{"aria-hidden":"true"}}):_vm._e(),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('grouped-score')))]),_vm._v(_vm._s(column.title)+" "),(column.hasRemovedSourceData)?_c('i',{staticClass:"fa fa-exclamation-circle",attrs:{"aria-hidden":"true"}}):_vm._e()]),(column.hasRemovedSourceData)?_c('b-popover',{attrs:{"target":((column.id) + "-title"),"triggers":"hover","placement":"bottom"}},[_c('p',{staticClass:"source-results-warning"},[_vm._v(_vm._s(_vm.$t('source-results-warning')))])]):_vm._e(),_c('button',{staticClass:"btn-settings",attrs:{"title":_vm.$t('item-settings')},on:{"click":function($event){return _vm.showColumnSettings(column.id)}}},[_c('i',{staticClass:"fa fa-gear u-inline-block",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('item-settings')))])])],1),_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-between"},[(column.countsForEndResult)?_c('div',{staticClass:"weight u-font-normal u-cursor-pointer",class:{'mod-custom': column.hasWeightSet , 'is-error': _vm.gradeBook.eqRestWeight < 0},attrs:{"title":_vm.$t('adjust-weight')},on:{"dblclick":function($event){return _vm.showColumnWeightDialog(column.id)}}},[_vm._v(_vm._s(_vm._f("formatNum")(column.weight))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]):_c('div',{staticClass:"weight u-font-normal u-font-italic",attrs:{"title":_vm.$t('count-towards-endresult-not')}},[_c('span',{attrs:{"aria-hidden":"true"}},[_vm._v(_vm._s(_vm.$t('uncounted')))]),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),(!column.isSaving)?_c('button',{staticClass:"btn-released u-ml-auto",attrs:{"title":column.released ? _vm.$t('make-invisible') : _vm.$t('make-visible')},on:{"click":function($event){return _vm.toggleVisibility(column.id)}}},[_c('i',{staticClass:"fa",class:{'fa-eye': column.released, 'fa-eye-slash': !column.released},attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(column.released ? _vm.$t('make-invisible') : _vm.$t('make-visible')))])]):_vm._e(),_c('div',{staticClass:"spin",attrs:{"role":"status","aria-busy":column.isSaving,"aria-label":_vm.$t('saving')}},[(column.isSaving)?_c('div',{staticClass:"glyphicon glyphicon-repeat glyphicon-spin",attrs:{"aria-hidden":"true"}}):_vm._e()])])]],2)})],2)}),_c('th',{staticClass:"col-sticky table-student-total u-text-end",class:{'unreleased-score-cell': _vm.gradeBook.hasUnreleasedScores}},[_c('div',[_vm._v(_vm._s(_vm.$t('final-score')))]),_c('div',{staticClass:"final-score-released",attrs:{"title":_vm.gradeBook.hasUnreleasedScores ? _vm.$t('invisible') : _vm.$t('visible')}},[_c('i',{staticClass:"fa",class:{'fa-eye': !_vm.gradeBook.hasUnreleasedScores, 'fa-eye-slash': _vm.gradeBook.hasUnreleasedScores},attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.gradeBook.hasUnreleasedScores ? _vm.$t('invisible') : _vm.$t('visible')))])])])],2)]),_c('tbody',_vm._l((_vm.displayedUsers),function(user){return _c('student-result-row',{key:'user-' + user.id,attrs:{"grade-book":_vm.gradeBook,"user":user,"grade-book-root-url":_vm.gradeBookRootUrl,"exclude-column-id":_vm.addColumnId,"show-null-category":_vm.showNullCategory,"edit-score-id":_vm.editScoreId,"edit-student-score-id":_vm.editStudentScoreId,"score-menu-tab":_vm.scoreMenuTab},on:{"edit-score":function($event){return _vm.showStudentScoreDialog(user.id, $event)},"edit-canceled":_vm.hideStudentScoreDialog,"edit-comment":function($event){return _vm.showStudentScoreDialog(user.id, $event, 'comment')},"menu-tab-changed":function($event){_vm.scoreMenuTab = $event},"result-updated":function($event){return _vm.overwriteResult(user.id, $event)},"result-reverted":function($event){return _vm.revertOverwrittenResult(user.id, $event)},"comment-updated":function($event){return _vm.updateResultComment(user.id, $event)}}})}),1)]),_vm._m(0)]),_c('div',{staticClass:"pagination-container u-flex u-justify-content-end"},[_c('b-pagination',{attrs:{"total-rows":_vm.sortedUsers.length,"per-page":_vm.itemsPerPage,"aria-controls":"gradebook-table"},model:{value:(_vm.pagination.currentPage),callback:function ($$v) {_vm.$set(_vm.pagination, "currentPage", $$v)},expression:"pagination.currentPage"}}),_c('ul',{staticClass:"pagination"},[_c('li',{staticClass:"page-item active"},[_c('a',{staticClass:"page-link"},[_vm._v(_vm._s(_vm.$t('total'))+" "+_vm._s(_vm.sortedUsers.length))])])])],1)])}
var GradesTablevue_type_template_id_990d6710_scoped_true_staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"lds-ellipsis",attrs:{"aria-hidden":"true"}},[_c('div'),_c('div'),_c('div'),_c('div')])}]


// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=template&id=990d6710&scoped=true&

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.every.js
var es_array_every = __webpack_require__("a4c2");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.map.js
var es_array_map = __webpack_require__("f7ad");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.slice.js
var es_array_slice = __webpack_require__("9df3");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.sort.js
var es_array_sort = __webpack_require__("b1cc");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.number.constructor.js
var es_number_constructor = __webpack_require__("250b");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/web.timers.js
var web_timers = __webpack_require__("6673");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.is-array.js
var es_array_is_array = __webpack_require__("badb");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function arrayWithoutHoles_arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.from.js
var es_array_from = __webpack_require__("b537");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.date.to-string.js
var es_date_to_string = __webpack_require__("d667");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.regexp.to-string.js
var es_regexp_to_string = __webpack_require__("21be");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/iterableToArray.js










function iterableToArray_iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function nonIterableSpread_nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function toConsumableArray_toConsumableArray(arr) {
  return arrayWithoutHoles_arrayWithoutHoles(arr) || iterableToArray_iterableToArray(arr) || nonIterableSpread_nonIterableSpread();
}
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.reduce.js
var es_array_reduce = __webpack_require__("6870");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.some.js
var es_array_some = __webpack_require__("25d2");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.splice.js
var es_array_splice = __webpack_require__("1ce3");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.number.to-fixed.js
var es_number_to_fixed = __webpack_require__("59c0");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.define-properties.js
var es_object_define_properties = __webpack_require__("b1c9");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.get-own-property-descriptor.js
var es_object_get_own_property_descriptor = __webpack_require__("bab4");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.get-own-property-descriptors.js
var es_object_get_own_property_descriptors = __webpack_require__("b7e4");

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.object.keys.js
var es_object_keys = __webpack_require__("073e");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/defineProperty.js

function defineProperty_defineProperty(obj, key, value) {
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
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@babel/runtime/helpers/esm/objectSpread2.js











function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    if (enumerableOnly) symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    });
    keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};

    if (i % 2) {
      ownKeys(Object(source), true).forEach(function (key) {
        defineProperty_defineProperty(target, key, source[key]);
      });
    } else if (Object.getOwnPropertyDescriptors) {
      Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
    } else {
      ownKeys(Object(source)).forEach(function (key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }
  }

  return target;
}
// CONCATENATED MODULE: ./src/domain/GradeBook.ts
















var GradeBook_GradeBook =
/*#__PURE__*/
function () {
  function GradeBook(dataId, currentVersion, title) {
    _classCallCheck(this, GradeBook);

    this.gradeItems = [];
    this.gradeColumns = [];
    this.categories = [];
    this.nullCategory = {
      id: 0,
      title: '',
      color: '',
      columnIds: []
    };
    this.users = [];
    this.resultsData = {};
    this.dataId = dataId;
    this.title = title;
    this.currentVersion = currentVersion;
  }

  _createClass(GradeBook, [{
    key: "getGradeItem",
    value: function getGradeItem(itemId) {
      return this.gradeItems.find(function (item) {
        return item.id === itemId;
      });
    }
  }, {
    key: "getGradeColumn",
    value: function getGradeColumn(columnId) {
      return this.gradeColumns.find(function (column) {
        return column.id === columnId;
      });
    }
  }, {
    key: "getCategory",
    value: function getCategory(categoryId) {
      return this.allCategories.find(function (category) {
        return category.id === categoryId;
      });
    }
  }, {
    key: "getStatusGradedItemsByColumn",
    value: function getStatusGradedItemsByColumn(columnId) {
      var _this = this;

      var column = this.getGradeColumn(columnId);

      if (!column) {
        return [];
      }

      return this.gradeItems.map(function (item) {
        var checked = column.subItemIds.indexOf(item.id) !== -1;
        var disabled = false;

        if (checked && column.type !== 'group') {
          disabled = true;
        } else {
          var col = _this.findGradeColumnWithGradeItem(item.id);

          if (col && col.type === 'group' && col !== column) {
            disabled = true;
          }
        }

        return _objectSpread2({}, item, {
          checked: checked,
          disabled: disabled
        });
      });
    }
  }, {
    key: "getWeight",
    value: function getWeight(column) {
      if (column.weight === null) {
        return this.eqRestWeight;
      }

      return column.weight;
    }
  }, {
    key: "setWeight",
    value: function setWeight(columnId, weight) {
      var column = this.getGradeColumn(columnId);

      if (column) {
        column.weight = weight;
      }
    }
  }, {
    key: "getTitle",
    value: function getTitle(column) {
      if (column.title) {
        return column.title;
      }

      if (column.type === 'item' || column.type === 'group') {
        var _this$getGradeItem;

        return ((_this$getGradeItem = this.getGradeItem(column.subItemIds[0])) === null || _this$getGradeItem === void 0 ? void 0 : _this$getGradeItem.title) || '';
      }

      return '';
    }
  }, {
    key: "setTitle",
    value: function setTitle(columnId, title) {
      var column = this.getGradeColumn(columnId);

      if (column) {
        column.title = title || null;
      }
    }
  }, {
    key: "hasRemovedSourceData",
    value: function hasRemovedSourceData(column) {
      var subItems = this.getColumnSubItems(column);
      return subItems.some(function (item) {
        return item.removed;
      });
    }
  }, {
    key: "getColumnSubItems",
    value: function getColumnSubItems(column) {
      var _this2 = this;

      return column.subItemIds.map(function (itemId) {
        return _this2.getGradeItem(itemId);
      });
    }
  }, {
    key: "hasResult",
    value: function hasResult(columnId, userId) {
      if (!this.resultsData[columnId]) {
        return false;
      }

      var score = this.resultsData[columnId][userId];
      return !!score;
    }
  }, {
    key: "getResult",
    value: function getResult(columnId, userId) {
      if (!this.resultsData[columnId]) {
        return null;
      }

      var score = this.resultsData[columnId][userId];

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
  }, {
    key: "getEndResult",
    value: function getEndResult(userId) {
      var _this3 = this;

      var endResult = 0;
      var maxWeight = 0;
      this.gradeColumns.filter(function (column) {
        return column.countForEndResult;
      }).forEach(function (column) {
        var result = _this3.getResult(column.id, userId);

        var weight = _this3.getWeight(column);

        if (typeof result === 'number') {
          maxWeight += weight;
        } else if (result === 'aabs') {
          if (column.authPresenceEndResult !== GradeBook.NO_SCORE) {
            maxWeight += weight;

            if (column.authPresenceEndResult === GradeBook.MAX_SCORE) {
              endResult += weight;
            }
          }
        } else if (result === null) {
          if (column.unauthPresenceEndResult !== GradeBook.NO_SCORE) {
            maxWeight += weight;

            if (column.unauthPresenceEndResult === GradeBook.MAX_SCORE) {
              endResult += weight;
            }
          }
        }

        if (typeof result === 'number') {
          endResult += result * weight * 0.01;
        }
      });

      if (maxWeight === 0) {
        return 0;
      }

      return endResult / maxWeight * 100;
    }
  }, {
    key: "isOverwrittenResult",
    value: function isOverwrittenResult(columnId, userId) {
      if (!this.resultsData[columnId]) {
        return false;
      }

      var score = this.resultsData[columnId][userId];

      if (!score) {
        return false;
      }

      return score.overwritten;
    }
  }, {
    key: "overwriteResult",
    value: function overwriteResult(columnId, userId, value) {
      if (!this.resultsData[columnId]) {
        return false;
      }

      var score = this.resultsData[columnId][userId];

      if (!score) {
        return false;
      }

      score.overwritten = true;

      if (value === 'aabs') {
        score.newScoreAuthAbsent = true;
        score.newScore = null;
      } else {
        score.newScoreAuthAbsent = false;
        score.newScore = value;
      }

      return score;
    }
  }, {
    key: "revertOverwrittenResult",
    value: function revertOverwrittenResult(columnId, userId) {
      if (!this.resultsData[columnId]) {
        return false;
      }

      var score = this.resultsData[columnId][userId];

      if (!score) {
        return false;
      }

      score.overwritten = false;
      score.newScoreAuthAbsent = false;
      score.newScore = null;
      return score;
    }
  }, {
    key: "userTotalNeedsUpdating",
    value: function userTotalNeedsUpdating(user) {
      var total = this.getResult('totals', user.id);

      if (total === null) {
        return false;
      } // unsynchronized user, cannot update


      if (typeof total !== 'number') {
        return true;
      }

      return total.toFixed(2) !== this.getEndResult(user.id).toFixed(2);
    }
  }, {
    key: "getResultComment",
    value: function getResultComment(columnId, userId) {
      if (!this.resultsData[columnId]) {
        return null;
      }

      var score = this.resultsData[columnId][userId];

      if (!score) {
        return null;
      }

      return score.comment;
    }
  }, {
    key: "updateResultComment",
    value: function updateResultComment(columnId, userId, comment) {
      if (!this.resultsData[columnId]) {
        return false;
      }

      var score = this.resultsData[columnId][userId];

      if (!score) {
        return false;
      }

      score.comment = comment;
      return score;
    }
  }, {
    key: "addItemToCategory",
    value: function addItemToCategory(categoryId, columnId) {
      var category = categoryId === 0 ? this.nullCategory : this.getCategory(categoryId);

      if ((category === null || category === void 0 ? void 0 : category.columnIds.indexOf(columnId)) === -1) {
        this.allCategories.forEach(function (cat) {
          if (cat.columnIds.indexOf(columnId) !== -1) {
            cat.columnIds = cat.columnIds.filter(function (id) {
              return id !== columnId;
            });
          }
        });
        category.columnIds.push(columnId);
      }
    }
  }, {
    key: "removeCategory",
    value: function removeCategory(category) {
      if (category === this.nullCategory) {
        return;
      }

      var columnIds = category.columnIds;
      var index = this.categories.indexOf(category);

      if (index < 0) {
        return;
      }

      this.categories.splice(index, 1);

      if (columnIds.length) {
        this.nullCategory.columnIds = [].concat(toConsumableArray_toConsumableArray(this.nullCategory.columnIds), toConsumableArray_toConsumableArray(columnIds));
      }
    }
  }, {
    key: "updateGradeColumnId",
    value: function updateGradeColumnId(column, newId) {
      var oldId = column.id;
      column.id = newId;
      this.allCategories.forEach(function (cat) {
        var index = cat.columnIds.indexOf(oldId);

        if (index !== -1) {
          cat.columnIds[index] = newId;
        }
      });
    }
  }, {
    key: "addGradeColumnFromItem",
    value: function addGradeColumnFromItem(item) {
      var newId = this.createNewColumnId();
      var column = {
        id: newId,
        type: 'item',
        title: null,
        subItemIds: [item.id],
        weight: null,
        countForEndResult: true,
        released: true,
        authPresenceEndResult: GradeBook.NO_SCORE,
        unauthPresenceEndResult: GradeBook.MIN_SCORE
      };
      this.gradeColumns.push(column);
      this.addItemToCategory(0, newId);
      return column;
    }
  }, {
    key: "findGradeColumnWithGradeItem",
    value: function findGradeColumnWithGradeItem(itemId) {
      var column = this.gradeColumns.find(function (column) {
        return column.subItemIds.indexOf(itemId) !== -1;
      });
      return column || null;
    }
  }, {
    key: "removeSubItem",
    value: function removeSubItem(item) {
      this.gradeColumns.forEach(function (column) {
        if (column.subItemIds.length) {
          column.subItemIds = column.subItemIds.filter(function (id) {
            return id !== item.id;
          });
        }
      });

      if (item.removed) {
        this.gradeItems = this.gradeItems.filter(function (gradeItem) {
          return gradeItem !== item;
        });
      }
    }
  }, {
    key: "createNewIdWithPrefix",
    value: function createNewIdWithPrefix(prefix) {
      var itemIds = this.gradeColumns.map(function (column) {
        return column.id;
      });
      var i = 1;

      while (itemIds.indexOf(prefix + i) !== -1) {
        i += 1;
      }

      return prefix + i;
    }
  }, {
    key: "createNewColumnId",
    value: function createNewColumnId() {
      return this.createNewIdWithPrefix('col');
    }
  }, {
    key: "createNewStandaloneScoreId",
    value: function createNewStandaloneScoreId() {
      return this.createNewIdWithPrefix('sc');
    }
  }, {
    key: "createNewScore",
    value: function createNewScore() {
      var id = this.createNewStandaloneScoreId();
      var newScore = {
        id: id,
        title: 'Score',
        type: 'standalone',
        subItemIds: [],
        weight: null,
        countForEndResult: true,
        released: true,
        authPresenceEndResult: GradeBook.NO_SCORE,
        unauthPresenceEndResult: GradeBook.MIN_SCORE
      };
      this.gradeColumns.push(newScore);
      this.nullCategory.columnIds.push(id);
      return newScore;
    }
  }, {
    key: "createNewCategory",
    value: function createNewCategory() {
      var id = this.categories.length ? Math.max.apply(null, this.categories.map(function (cat) {
        return cat.id;
      })) + 1 : 1;
      var newCategory = {
        id: id,
        title: 'Categorie',
        color: '#92eded',
        columnIds: []
      };
      this.categories.push(newCategory);
      return newCategory;
    }
  }, {
    key: "addSubItem",
    value: function addSubItem(item, columnId) {
      var column = this.getGradeColumn(columnId);

      if (!column) {
        return;
      }

      var srcColumn = this.findGradeColumnWithGradeItem(item.id);
      column.title = this.getTitle(column);
      column.type = 'group';
      column.subItemIds.push(item.id);

      if (srcColumn) {
        this.gradeColumns = this.gradeColumns.filter(function (column) {
          return column !== srcColumn;
        });
        this.allCategories.forEach(function (cat) {
          cat.columnIds = cat.columnIds.filter(function (id) {
            return id !== srcColumn.id;
          });
        });
        delete this.resultsData[srcColumn.id];
      }
    }
  }, {
    key: "removeColumn",
    value: function removeColumn(column) {
      var _this4 = this;

      column.subItemIds.forEach(function (itemId) {
        _this4.removeSubItem(_this4.getGradeItem(itemId));
      });
      delete this.resultsData[column.id];
      this.gradeColumns = this.gradeColumns.filter(function (col) {
        return col !== column;
      });
      this.allCategories.forEach(function (cat) {
        cat.columnIds = cat.columnIds.filter(function (id) {
          return id !== column.id;
        });
      });
    }
  }, {
    key: "allCategories",
    get: function get() {
      return [].concat(toConsumableArray_toConsumableArray(this.categories), [this.nullCategory]);
    }
  }, {
    key: "statusGradedItems",
    get: function get() {
      var itemIds = this.gradeColumns.reduce(function (ids, column) {
        return ids.concat(column.subItemIds);
      }, []);
      return this.gradeItems.map(function (item) {
        return _objectSpread2({}, item, {
          checked: itemIds.indexOf(item.id) !== -1,
          disabled: false
        });
      });
    }
  }, {
    key: "hasUnreleasedScores",
    get: function get() {
      return this.gradeColumns.some(function (column) {
        return column.countForEndResult && !column.released;
      });
    }
  }, {
    key: "eqRestWeight",
    get: function get() {
      var rest = 100;
      var noRest = 0;
      this.gradeColumns.filter(function (column) {
        return column.countForEndResult;
      }).forEach(function (column) {
        if (column.weight !== null) {
          rest -= column.weight;
        } else {
          noRest += 1;
        }
      });
      return rest / noRest;
    }
  }, {
    key: "totalsNeedUpdating",
    get: function get() {
      var _this5 = this;

      return this.users.some(function (user) {
        return _this5.userTotalNeedsUpdating(user);
      });
    }
  }], [{
    key: "from",
    value: function from(gradeBookObject) {
      var gradeBook = new GradeBook(gradeBookObject.dataId, gradeBookObject.version, gradeBookObject.title);
      gradeBook.gradeItems = gradeBookObject.gradeItems;
      gradeBook.gradeColumns = gradeBookObject.gradeColumns;
      gradeBook.categories = gradeBookObject.categories;
      gradeBook.nullCategory = gradeBookObject.nullCategory;
      return gradeBook;
    }
  }]);

  return GradeBook;
}();


GradeBook_GradeBook.NO_SCORE = 0;
GradeBook_GradeBook.MAX_SCORE = 1;
GradeBook_GradeBook.MIN_SCORE = 2;
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/StudentResultRow.vue?vue&type=template&id=e1de9e6a&scoped=true&
var StudentResultRowvue_type_template_id_e1de9e6a_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('tr',{staticClass:"table-row table-body-row"},[_c('td',{staticClass:"col-sticky table-student"},[(_vm.gradeBookRootUrl)?_c('a',{attrs:{"href":(_vm.gradeBookRootUrl + "&gradebook_display_action=UserScores&user_id=" + _vm.userId)}},[_vm._v(_vm._s(_vm.lastName)+", "+_vm._s(_vm.firstName))]):[_vm._v(_vm._s(_vm.lastName)+", "+_vm._s(_vm.firstName))]],2),(_vm.isSynchronized)?[_vm._l((_vm.columns),function(column,index){return [(column.isScoreColumn)?_c('td',{key:("col-" + index),class:{'unreleased-score-cell': !column.released, 'uncounted-score-cell': !column.countsForEndResult, 'u-relative': column.isEditing}},[(column.hasResult && !column.isEditing)?_c('student-result',{staticClass:"u-flex u-align-items-center u-justify-content-end u-cursor-pointer",class:{'uncounted-score': !column.countsForEndResult},attrs:{"id":("result-" + (column.id) + "-" + _vm.userId),"result":column.result,"comment":column.comment,"is-standalone-score":column.isStandaloneScore,"use-overwritten-flag":true,"is-overwritten":column.isOverwrittenResult},on:{"edit":function($event){return _vm.$emit('edit-score', column.id)},"edit-comment":function($event){return _vm.$emit('edit-comment', column.id)}}}):_vm._e(),(column.isEditing)?_c('score-input',{attrs:{"menu-tab":_vm.scoreMenuTab,"score":column.result,"comment":column.comment,"use-revert":column.isOverwrittenResult && !column.isStandaloneScore},on:{"menu-tab-changed":function($event){return _vm.$emit('menu-tab-changed', $event)},"cancel":function($event){return _vm.$emit('edit-canceled')},"comment-updated":function($event){return _vm.$emit('comment-updated', {columnId: column.id, comment: $event})},"ok":function($event){return _vm.$emit('result-updated', {columnId: column.id, value: $event})},"revert":function($event){return _vm.$emit('result-reverted', column.id)}}}):_vm._e()],1):_c('td',{key:("col-" + index)})]}),_c('td',{staticClass:"col-sticky table-student-total u-text-end",class:{'unreleased-score-cell': _vm.gradeBook.hasUnreleasedScores, 'mod-needs-update': _vm.totalNeedsUpdate}},[(_vm.totalNeedsUpdate)?_c('i',{staticClass:"fa fa-exclamation-circle",attrs:{"title":_vm.$t('not-yet-updated'),"aria-hidden":"true"}}):_vm._e(),(_vm.totalNeedsUpdate)?_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('not-yet-updated')))]):_vm._e(),_vm._v(_vm._s(_vm._f("formatNum2")(_vm.endResult))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])]:_c('td',{staticClass:"table-student-unsychronized",attrs:{"colspan":_vm.gradeBook.gradeColumns.length + 1}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center"},[_vm._v(_vm._s(_vm.$t('not-synchronized')))])])],2)}
var StudentResultRowvue_type_template_id_e1de9e6a_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/StudentResultRow.vue?vue&type=template&id=e1de9e6a&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ScoreInput.vue?vue&type=template&id=0ed11da2&scoped=true&
var ScoreInputvue_type_template_id_0ed11da2_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"menu",fn:function(){return [_c('div',{staticClass:"cell-content content-tabs"},[_c('div',{staticClass:"u-flex u-justify-content-end u-text-end",attrs:{"role":"tablist"}},[_c('div',{staticClass:"menu-tab u-cursor-pointer",class:{'mod-active': _vm.menuTab === 'score'},attrs:{"role":"tab","aria-selected":_vm.menuTab === 'score' ? 'true' : 'false',"aria-controls":"score-panel"},on:{"click":function($event){return _vm.$emit('menu-tab-changed', 'score')}}},[_vm._v(_vm._s(_vm.$t('score')))]),_c('div',{staticClass:"menu-tab u-cursor-pointer",class:{'mod-active': _vm.menuTab === 'comment'},attrs:{"role":"tab","aria-selected":_vm.menuTab === 'comment' ? 'true' : 'false',"aria-controls":"score-panel"},on:{"click":function($event){return _vm.$emit('menu-tab-changed', 'comment')}}},[_vm._v(_vm._s(_vm.$t('comments')))])])])]},proxy:true},{key:"content",fn:function(){return [(_vm.menuTab === 'score')?_c('div',{staticClass:"u-flex u-gap-small",attrs:{"role":"tabpanel","id":"score-panel"}},[_c('div',{staticClass:"number-input u-relative",class:{'is-selected': _vm.type === 'number'}},[_c('input',{ref:"score-input",staticClass:"percent-input u-font-normal",attrs:{"id":"score","type":"number","min":"0","max":"100","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.numValue)},on:{"input":function($event){_vm.type = 'number'},"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter")){ return null; }return _vm.onEdit($event)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"])){ return null; }return _vm.$emit('cancel')}],"focus":function($event){_vm.type = 'number'}}}),_c('div',{staticClass:"percent"},[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])]),_c('button',{staticClass:"color-code amber-700",class:{'is-selected': _vm.type === 'aabs'},attrs:{"title":_vm.$t('auth-absent')},on:{"click":_vm.setAuthAbsent}},[_c('span',[_vm._v(_vm._s(_vm.$t('aabs')))])]),(_vm.useRevert)?_c('button',{staticClass:"btn btn-secundary btn-sm btn-revert",attrs:{"title":_vm.$t('use-source-result')},on:{"click":_vm.setRevert}},[_c('i',{staticClass:"fa fa-undo",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('use-source-result')))])]):_vm._e()]):_vm._e(),(_vm.menuTab === 'comment')?_c('div',{attrs:{"role":"tabpanel","id":"score-panel"}},[_c('textarea',{directives:[{name:"model",rawName:"v-model",value:(_vm.commentValue),expression:"commentValue"}],ref:"comment-input",staticClass:"comment-field",domProps:{"value":(_vm.commentValue)},on:{"input":function($event){if($event.target.composing){ return; }_vm.commentValue=$event.target.value}}})]):_vm._e()]},proxy:true}])})}
var ScoreInputvue_type_template_id_0ed11da2_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/ScoreInput.vue?vue&type=template&id=0ed11da2&scoped=true&

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.parse-float.js
var es_parse_float = __webpack_require__("c9b7");

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/TableCellInput.vue?vue&type=template&id=557783c1&scoped=true&
var TableCellInputvue_type_template_id_557783c1_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_vm._t("menu"),_c('div',{staticClass:"cell-content"},[_vm._t("content"),_c('div',{staticClass:"u-flex name-input-actions"},[_c('button',{staticClass:"btn btn-primary btn-sm",on:{"click":function($event){return _vm.$emit('edit')}}},[_vm._v(_vm._s(_vm.$t('edit')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":function($event){return _vm.$emit('cancel')}}},[_vm._v(_vm._s(_vm.$t('cancel')))])])],2)],2)}
var TableCellInputvue_type_template_id_557783c1_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/TableCellInput.vue?vue&type=template&id=557783c1&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/TableCellInput.vue?vue&type=script&lang=ts&







var TableCellInputvue_type_script_lang_ts_TableCellInput =
/*#__PURE__*/
function (_Vue) {
  _inherits(TableCellInput, _Vue);

  function TableCellInput() {
    _classCallCheck(this, TableCellInput);

    return _possibleConstructorReturn(this, _getPrototypeOf(TableCellInput).apply(this, arguments));
  }

  return TableCellInput;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

TableCellInputvue_type_script_lang_ts_TableCellInput = __decorate([vue_class_component_esm({
  name: 'table-cell-input'
})], TableCellInputvue_type_script_lang_ts_TableCellInput);
/* harmony default export */ var TableCellInputvue_type_script_lang_ts_ = (TableCellInputvue_type_script_lang_ts_TableCellInput);
// CONCATENATED MODULE: ./src/components/TableCellInput.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_TableCellInputvue_type_script_lang_ts_ = (TableCellInputvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/TableCellInput.vue?vue&type=style&index=0&id=557783c1&lang=scss&scoped=true&
var TableCellInputvue_type_style_index_0_id_557783c1_lang_scss_scoped_true_ = __webpack_require__("7449");

// EXTERNAL MODULE: ./src/components/TableCellInput.vue?vue&type=custom&index=0&blockType=i18n
var TableCellInputvue_type_custom_index_0_blockType_i18n = __webpack_require__("835c");

// CONCATENATED MODULE: ./src/components/TableCellInput.vue






/* normalize component */

var TableCellInput_component = normalizeComponent(
  components_TableCellInputvue_type_script_lang_ts_,
  TableCellInputvue_type_template_id_557783c1_scoped_true_render,
  TableCellInputvue_type_template_id_557783c1_scoped_true_staticRenderFns,
  false,
  null,
  "557783c1",
  null
  
)

/* custom blocks */

if (typeof TableCellInputvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(TableCellInputvue_type_custom_index_0_blockType_i18n["default"])(TableCellInput_component)

/* harmony default export */ var components_TableCellInput = (TableCellInput_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ScoreInput.vue?vue&type=script&lang=ts&











var ScoreInputvue_type_script_lang_ts_ScoreInput =
/*#__PURE__*/
function (_Vue) {
  _inherits(ScoreInput, _Vue);

  function ScoreInput() {
    var _this;

    _classCallCheck(this, ScoreInput);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ScoreInput).apply(this, arguments));
    _this.type = 'number';
    _this.numValue = '';
    _this.commentValue = '';
    return _this;
  }

  _createClass(ScoreInput, [{
    key: "onEdit",
    value: function onEdit() {
      if (this.menuTab === 'comment') {
        this.$emit('comment-updated', this.commentValue || null);
        return;
      }

      if (this.type === 'number') {
        var el = this.scoreInput;

        if (!el.checkValidity()) {
          el.reportValidity();
          return;
        }

        var value = parseFloat(this.scoreInput.value);
        this.$emit('ok', isNaN(value) ? null : value);
      } else if (this.type === 'aabs') {
        this.$emit('ok', 'aabs');
      } else if (this.type === 'revert') {
        this.$emit('revert');
      }
    }
  }, {
    key: "setAuthAbsent",
    value: function setAuthAbsent() {
      var _this2 = this;

      this.type = 'aabs';
      this.$nextTick(function () {
        return _this2.numValue = '';
      });
    }
  }, {
    key: "setRevert",
    value: function setRevert() {
      var _this3 = this;

      this.type = 'revert';
      this.$nextTick(function () {
        return _this3.numValue = '';
      });
    }
  }, {
    key: "mounted",
    value: function mounted() {
      var _this4 = this;

      if (this.score === 'aabs') {
        this.type = 'aabs';
        return;
      }

      this.type = 'number';
      this.numValue = String(this.score);
      this.commentValue = this.comment || '';

      if (this.menuTab === 'comment') {
        this.$nextTick(function () {
          return _this4.commentInput.focus();
        });
      } else {
        this.$nextTick(function () {
          return _this4.scoreInput.focus();
        });
      }
    }
  }, {
    key: "scoreInput",
    get: function get() {
      return this.$refs['score-input'];
    }
  }, {
    key: "commentInput",
    get: function get() {
      return this.$refs['comment-input'];
    }
  }]);

  return ScoreInput;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: [Number, String],
  "default": null
})], ScoreInputvue_type_script_lang_ts_ScoreInput.prototype, "score", void 0);

__decorate([Prop({
  type: String,
  "default": null
})], ScoreInputvue_type_script_lang_ts_ScoreInput.prototype, "comment", void 0);

__decorate([Prop({
  type: String,
  "default": 'score'
})], ScoreInputvue_type_script_lang_ts_ScoreInput.prototype, "menuTab", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], ScoreInputvue_type_script_lang_ts_ScoreInput.prototype, "useRevert", void 0);

ScoreInputvue_type_script_lang_ts_ScoreInput = __decorate([vue_class_component_esm({
  name: 'score-input',
  components: {
    TableCellInput: components_TableCellInput
  },
  filters: {
    formatNum: function formatNum(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        maximumFractionDigits: 2
      });
    }
  }
})], ScoreInputvue_type_script_lang_ts_ScoreInput);
/* harmony default export */ var ScoreInputvue_type_script_lang_ts_ = (ScoreInputvue_type_script_lang_ts_ScoreInput);
// CONCATENATED MODULE: ./src/components/ScoreInput.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_ScoreInputvue_type_script_lang_ts_ = (ScoreInputvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/ScoreInput.vue?vue&type=style&index=0&id=0ed11da2&lang=scss&scoped=true&
var ScoreInputvue_type_style_index_0_id_0ed11da2_lang_scss_scoped_true_ = __webpack_require__("725c");

// EXTERNAL MODULE: ./src/components/ScoreInput.vue?vue&type=custom&index=0&blockType=i18n
var ScoreInputvue_type_custom_index_0_blockType_i18n = __webpack_require__("eade");

// CONCATENATED MODULE: ./src/components/ScoreInput.vue






/* normalize component */

var ScoreInput_component = normalizeComponent(
  components_ScoreInputvue_type_script_lang_ts_,
  ScoreInputvue_type_template_id_0ed11da2_scoped_true_render,
  ScoreInputvue_type_template_id_0ed11da2_scoped_true_staticRenderFns,
  false,
  null,
  "0ed11da2",
  null
  
)

/* custom blocks */

if (typeof ScoreInputvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(ScoreInputvue_type_custom_index_0_blockType_i18n["default"])(ScoreInput_component)

/* harmony default export */ var components_ScoreInput = (ScoreInput_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/StudentResult.vue?vue&type=template&id=2ea6a10f&scoped=true&
var StudentResultvue_type_template_id_2ea6a10f_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{on:{"dblclick":function($event){return _vm.$emit('edit')}}},[_c('div',{staticClass:"u-flex u-align-items-center u-gap-small"},[(_vm.comment)?[_c('a',{staticClass:"fa fa-comment-o",attrs:{"id":("result-comment-" + _vm.id),"title":_vm.$t('edit-comment')},on:{"click":function($event){$event.stopPropagation();return _vm.$emit('edit-comment')}}},[_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('edit-comment')))])]),_c('b-popover',{attrs:{"custom-class":"gradebook-comment-popover","target":("result-comment-" + _vm.id),"triggers":"hover","placement":"top"}},[_c('div',{staticClass:"comment"},[_c('div',{staticClass:"comment-header"},[_vm._v("Feedback:")]),_vm._v(" "+_vm._s(_vm.comment)+" ")])])]:_vm._e(),_c('div',{staticClass:"result u-flex u-align-items-center u-justify-content-end",class:{'overwritten-score': !_vm.isStandaloneScore && _vm.useOverwrittenFlag && _vm.isOverwritten, 'mod-aabs': _vm.result === 'aabs'}},[(_vm.result === 'aabs')?_c('div',{staticClass:"color-code amber-700",attrs:{"title":_vm.$t('auth-absent')}},[_c('span',[_vm._v(_vm._s(_vm.$t('aabs')))])]):(_vm.result === null)?_c('div',{staticClass:"color-code mod-none",attrs:{"title":_vm.$t('no-score-found')}},[_c('i',{staticClass:"fa fa-question",class:{'mod-none': _vm.isStandaloneScore || !_vm.useOverwrittenFlag || !_vm.isOverwritten},attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('no-score-found')))])]):_c('div',[_vm._v(_vm._s(_vm.result)),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])])],2)])}
var StudentResultvue_type_template_id_2ea6a10f_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/StudentResult.vue?vue&type=template&id=2ea6a10f&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/StudentResult.vue?vue&type=script&lang=ts&








var StudentResultvue_type_script_lang_ts_StudentResult =
/*#__PURE__*/
function (_Vue) {
  _inherits(StudentResult, _Vue);

  function StudentResult() {
    _classCallCheck(this, StudentResult);

    return _possibleConstructorReturn(this, _getPrototypeOf(StudentResult).apply(this, arguments));
  }

  return StudentResult;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  "default": ''
})], StudentResultvue_type_script_lang_ts_StudentResult.prototype, "id", void 0);

__decorate([Prop({
  type: [Number, String],
  "default": null
})], StudentResultvue_type_script_lang_ts_StudentResult.prototype, "result", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], StudentResultvue_type_script_lang_ts_StudentResult.prototype, "useOverwrittenFlag", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], StudentResultvue_type_script_lang_ts_StudentResult.prototype, "isOverwritten", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], StudentResultvue_type_script_lang_ts_StudentResult.prototype, "isStandaloneScore", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], StudentResultvue_type_script_lang_ts_StudentResult.prototype, "comment", void 0);

StudentResultvue_type_script_lang_ts_StudentResult = __decorate([vue_class_component_esm({
  name: 'student-result'
})], StudentResultvue_type_script_lang_ts_StudentResult);
/* harmony default export */ var StudentResultvue_type_script_lang_ts_ = (StudentResultvue_type_script_lang_ts_StudentResult);
// CONCATENATED MODULE: ./src/components/StudentResult.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_StudentResultvue_type_script_lang_ts_ = (StudentResultvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/StudentResult.vue?vue&type=style&index=0&id=2ea6a10f&scoped=true&lang=scss&
var StudentResultvue_type_style_index_0_id_2ea6a10f_scoped_true_lang_scss_ = __webpack_require__("a07a");

// EXTERNAL MODULE: ./src/components/StudentResult.vue?vue&type=style&index=1&lang=css&
var StudentResultvue_type_style_index_1_lang_css_ = __webpack_require__("b974");

// EXTERNAL MODULE: ./src/components/StudentResult.vue?vue&type=custom&index=0&blockType=i18n
var StudentResultvue_type_custom_index_0_blockType_i18n = __webpack_require__("d44e");

// CONCATENATED MODULE: ./src/components/StudentResult.vue







/* normalize component */

var StudentResult_component = normalizeComponent(
  components_StudentResultvue_type_script_lang_ts_,
  StudentResultvue_type_template_id_2ea6a10f_scoped_true_render,
  StudentResultvue_type_template_id_2ea6a10f_scoped_true_staticRenderFns,
  false,
  null,
  "2ea6a10f",
  null
  
)

/* custom blocks */

if (typeof StudentResultvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(StudentResultvue_type_custom_index_0_blockType_i18n["default"])(StudentResult_component)

/* harmony default export */ var components_StudentResult = (StudentResult_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/StudentResultRow.vue?vue&type=script&lang=ts&


















var StudentResultRowvue_type_script_lang_ts_StudentResultRow =
/*#__PURE__*/
function (_Vue) {
  _inherits(StudentResultRow, _Vue);

  function StudentResultRow() {
    _classCallCheck(this, StudentResultRow);

    return _possibleConstructorReturn(this, _getPrototypeOf(StudentResultRow).apply(this, arguments));
  }

  _createClass(StudentResultRow, [{
    key: "getColumnData",
    value: function getColumnData(columnId) {
      var gradeBook = this.gradeBook;
      var userId = this.userId;
      var column = gradeBook.getGradeColumn(columnId);

      if (!column) {
        throw new Error("GradeColumn with id ".concat(columnId, " not found."));
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
  }, {
    key: "userId",
    get: function get() {
      return this.user.id;
    }
  }, {
    key: "firstName",
    get: function get() {
      return this.user.firstName;
    }
  }, {
    key: "lastName",
    get: function get() {
      return this.user.lastName.toUpperCase();
    }
  }, {
    key: "isSynchronized",
    get: function get() {
      var _this = this;

      return !this.gradeBook.gradeColumns.filter(function (column) {
        return column.id !== _this.excludeColumnId;
      }).some(function (column) {
        return !_this.gradeBook.hasResult(column.id, _this.userId);
      });
    }
  }, {
    key: "totalNeedsUpdate",
    get: function get() {
      return this.gradeBook.userTotalNeedsUpdating(this.user);
    }
  }, {
    key: "endResult",
    get: function get() {
      return this.gradeBook.getEndResult(this.userId);
    }
  }, {
    key: "displayedCategories",
    get: function get() {
      if (this.showNullCategory) {
        return [].concat(toConsumableArray_toConsumableArray(this.gradeBook.categories), [this.gradeBook.nullCategory]);
      }

      return this.gradeBook.categories;
    }
  }, {
    key: "columns",
    get: function get() {
      var _this2 = this;

      return this.displayedCategories.reduce(function (columns, currentCategory) {
        if (currentCategory.columnIds.length) {
          return [].concat(toConsumableArray_toConsumableArray(columns), toConsumableArray_toConsumableArray(currentCategory.columnIds.map(function (columnId) {
            return _this2.getColumnData(columnId);
          })));
        }

        return [].concat(toConsumableArray_toConsumableArray(columns), [{
          isScoreColumn: false
        }]);
      }, []);
    }
  }]);

  return StudentResultRow;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: GradeBook_GradeBook,
  required: true
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "gradeBook", void 0);

__decorate([Prop({
  type: Object,
  required: true
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "user", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "gradeBookRootUrl", void 0);

__decorate([Prop({
  type: [String, Number],
  "default": null
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "excludeColumnId", void 0);

__decorate([Prop({
  type: Number,
  "default": null
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "editStudentScoreId", void 0);

__decorate([Prop({
  type: [String, Number],
  "default": null
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "editScoreId", void 0);

__decorate([Prop({
  type: String,
  "default": 'score'
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "scoreMenuTab", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow.prototype, "showNullCategory", void 0);

StudentResultRowvue_type_script_lang_ts_StudentResultRow = __decorate([vue_class_component_esm({
  name: 'student-result-row',
  components: {
    ScoreInput: components_ScoreInput,
    StudentResult: components_StudentResult
  },
  filters: {
    formatNum2: function formatNum2(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  }
})], StudentResultRowvue_type_script_lang_ts_StudentResultRow);
/* harmony default export */ var StudentResultRowvue_type_script_lang_ts_ = (StudentResultRowvue_type_script_lang_ts_StudentResultRow);
// CONCATENATED MODULE: ./src/components/StudentResultRow.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_StudentResultRowvue_type_script_lang_ts_ = (StudentResultRowvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/StudentResultRow.vue?vue&type=style&index=0&id=e1de9e6a&lang=scss&scoped=true&
var StudentResultRowvue_type_style_index_0_id_e1de9e6a_lang_scss_scoped_true_ = __webpack_require__("6e08");

// EXTERNAL MODULE: ./src/components/StudentResultRow.vue?vue&type=custom&index=0&blockType=i18n
var StudentResultRowvue_type_custom_index_0_blockType_i18n = __webpack_require__("2378");

// CONCATENATED MODULE: ./src/components/StudentResultRow.vue






/* normalize component */

var StudentResultRow_component = normalizeComponent(
  components_StudentResultRowvue_type_script_lang_ts_,
  StudentResultRowvue_type_template_id_e1de9e6a_scoped_true_render,
  StudentResultRowvue_type_template_id_e1de9e6a_scoped_true_staticRenderFns,
  false,
  null,
  "e1de9e6a",
  null
  
)

/* custom blocks */

if (typeof StudentResultRowvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(StudentResultRowvue_type_custom_index_0_blockType_i18n["default"])(StudentResultRow_component)

/* harmony default export */ var components_StudentResultRow = (StudentResultRow_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ItemTitleInput.vue?vue&type=template&id=97c5d59e&scoped=true&
var ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"content",fn:function(){return [_c('input',{ref:"title-input",staticClass:"u-font-normal",attrs:{"type":"text"},domProps:{"value":_vm.itemTitle},on:{"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter")){ return null; }return _vm.onEdit($event)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"])){ return null; }return _vm.$emit('cancel')}]}})]},proxy:true}])})}
var ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/ItemTitleInput.vue?vue&type=template&id=97c5d59e&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ItemTitleInput.vue?vue&type=script&lang=ts&









var ItemTitleInputvue_type_script_lang_ts_ItemTitleInput =
/*#__PURE__*/
function (_Vue) {
  _inherits(ItemTitleInput, _Vue);

  function ItemTitleInput() {
    _classCallCheck(this, ItemTitleInput);

    return _possibleConstructorReturn(this, _getPrototypeOf(ItemTitleInput).apply(this, arguments));
  }

  _createClass(ItemTitleInput, [{
    key: "onEdit",
    value: function onEdit() {
      this.$emit('ok', this.titleInput.value);
    }
  }, {
    key: "mounted",
    value: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        return _this.titleInput.focus();
      });
    }
  }, {
    key: "titleInput",
    get: function get() {
      return this.$refs['title-input'];
    }
  }]);

  return ItemTitleInput;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  "default": ''
})], ItemTitleInputvue_type_script_lang_ts_ItemTitleInput.prototype, "itemTitle", void 0);

ItemTitleInputvue_type_script_lang_ts_ItemTitleInput = __decorate([vue_class_component_esm({
  name: 'item-title-input',
  components: {
    TableCellInput: components_TableCellInput
  }
})], ItemTitleInputvue_type_script_lang_ts_ItemTitleInput);
/* harmony default export */ var ItemTitleInputvue_type_script_lang_ts_ = (ItemTitleInputvue_type_script_lang_ts_ItemTitleInput);
// CONCATENATED MODULE: ./src/components/ItemTitleInput.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_ItemTitleInputvue_type_script_lang_ts_ = (ItemTitleInputvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/ItemTitleInput.vue?vue&type=style&index=0&id=97c5d59e&scoped=true&lang=css&
var ItemTitleInputvue_type_style_index_0_id_97c5d59e_scoped_true_lang_css_ = __webpack_require__("da8c");

// CONCATENATED MODULE: ./src/components/ItemTitleInput.vue






/* normalize component */

var ItemTitleInput_component = normalizeComponent(
  components_ItemTitleInputvue_type_script_lang_ts_,
  ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_render,
  ItemTitleInputvue_type_template_id_97c5d59e_scoped_true_staticRenderFns,
  false,
  null,
  "97c5d59e",
  null
  
)

/* harmony default export */ var components_ItemTitleInput = (ItemTitleInput_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/WeightInput.vue?vue&type=template&id=200a99cc&scoped=true&
var WeightInputvue_type_template_id_200a99cc_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('table-cell-input',{on:{"edit":_vm.onEdit,"cancel":function($event){return _vm.$emit('cancel')}},scopedSlots:_vm._u([{key:"content",fn:function(){return [_c('label',{staticClass:"u-font-medium",attrs:{"for":"weight"}},[_vm._v(_vm._s(_vm.$t('weight'))+":")]),_c('div',{staticClass:"u-relative"},[_c('input',{ref:"weight-input",staticClass:"percent-input u-font-normal",attrs:{"id":"weight","type":"number","min":"0","max":"100","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.itemWeight)},on:{"keyup":[function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"enter",13,$event.key,"Enter")){ return null; }return _vm.onEdit($event)},function($event){if(!$event.type.indexOf('key')&&_vm._k($event.keyCode,"esc",27,$event.key,["Esc","Escape"])){ return null; }return _vm.$emit('cancel')}]}}),_c('div',{staticClass:"percent"},[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])])]},proxy:true}])})}
var WeightInputvue_type_template_id_200a99cc_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/WeightInput.vue?vue&type=template&id=200a99cc&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/WeightInput.vue?vue&type=script&lang=ts&











var WeightInputvue_type_script_lang_ts_WeightInput =
/*#__PURE__*/
function (_Vue) {
  _inherits(WeightInput, _Vue);

  function WeightInput() {
    _classCallCheck(this, WeightInput);

    return _possibleConstructorReturn(this, _getPrototypeOf(WeightInput).apply(this, arguments));
  }

  _createClass(WeightInput, [{
    key: "onEdit",
    value: function onEdit() {
      var el = this.weightInput;

      if (!el.checkValidity()) {
        el.reportValidity();
        return;
      }

      var value = parseFloat(this.weightInput.value);
      this.$emit('ok', isNaN(value) ? null : value);
    }
  }, {
    key: "mounted",
    value: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        return _this.weightInput.focus();
      });
    }
  }, {
    key: "weightInput",
    get: function get() {
      return this.$refs['weight-input'];
    }
  }]);

  return WeightInput;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Number,
  "default": ''
})], WeightInputvue_type_script_lang_ts_WeightInput.prototype, "itemWeight", void 0);

WeightInputvue_type_script_lang_ts_WeightInput = __decorate([vue_class_component_esm({
  name: 'weight-input',
  components: {
    TableCellInput: components_TableCellInput
  },
  filters: {
    formatNum: function formatNum(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        maximumFractionDigits: 2
      });
    }
  }
})], WeightInputvue_type_script_lang_ts_WeightInput);
/* harmony default export */ var WeightInputvue_type_script_lang_ts_ = (WeightInputvue_type_script_lang_ts_WeightInput);
// CONCATENATED MODULE: ./src/components/WeightInput.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_WeightInputvue_type_script_lang_ts_ = (WeightInputvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/WeightInput.vue?vue&type=style&index=0&id=200a99cc&lang=scss&scoped=true&
var WeightInputvue_type_style_index_0_id_200a99cc_lang_scss_scoped_true_ = __webpack_require__("84a1");

// EXTERNAL MODULE: ./src/components/WeightInput.vue?vue&type=custom&index=0&blockType=i18n
var WeightInputvue_type_custom_index_0_blockType_i18n = __webpack_require__("473a");

// CONCATENATED MODULE: ./src/components/WeightInput.vue






/* normalize component */

var WeightInput_component = normalizeComponent(
  components_WeightInputvue_type_script_lang_ts_,
  WeightInputvue_type_template_id_200a99cc_scoped_true_render,
  WeightInputvue_type_template_id_200a99cc_scoped_true_staticRenderFns,
  false,
  null,
  "200a99cc",
  null
  
)

/* custom blocks */

if (typeof WeightInputvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(WeightInputvue_type_custom_index_0_blockType_i18n["default"])(WeightInput_component)

/* harmony default export */ var components_WeightInput = (WeightInput_component.exports);
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vuedraggable/dist/vuedraggable.common.js
var vuedraggable_common = __webpack_require__("6431");
var vuedraggable_common_default = /*#__PURE__*/__webpack_require__.n(vuedraggable_common);

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/GradesTable.vue?vue&type=script&lang=ts&

























var GradesTablevue_type_script_lang_ts_GradesTable =
/*#__PURE__*/
function (_Vue) {
  _inherits(GradesTable, _Vue);

  function GradesTable() {
    var _this;

    _classCallCheck(this, GradesTable);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(GradesTable).apply(this, arguments));
    _this.isDraggingColumn = false;
    _this.isDraggingCategory = false;
    _this.categoryDropArea = null;
    _this.editItemId = null;
    _this.catEditItemId = null;
    _this.weightEditItemId = null;
    _this.editStudentScoreId = null;
    _this.editScoreId = null;
    _this.scoreMenuTab = 'score';
    _this.sortBy = 'lastname';
    _this.sortDesc = false;
    _this.pagination = {
      currentPage: 1
    };
    return _this;
  }

  _createClass(GradesTable, [{
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
    key: "resetDialogs",
    value: function resetDialogs() {
      this.editItemId = null;
      this.catEditItemId = null;
      this.weightEditItemId = null;
      this.editStudentScoreId = null;
      this.editScoreId = null;
    }
  }, {
    key: "showCategorySettings",
    value: function showCategorySettings(categoryId) {
      this.resetDialogs();
      this.$emit('category-settings', categoryId);
    }
  }, {
    key: "showColumnSettings",
    value: function showColumnSettings(columnId) {
      this.resetDialogs();
      this.$emit('item-settings', columnId);
    }
  }, {
    key: "showCategoryTitleDialog",
    value: function showCategoryTitleDialog(categoryId) {
      this.resetDialogs();
      this.catEditItemId = categoryId;
    }
  }, {
    key: "showColumnTitleDialog",
    value: function showColumnTitleDialog(columnId) {
      this.resetDialogs();
      this.editItemId = columnId;
    }
  }, {
    key: "showColumnWeightDialog",
    value: function showColumnWeightDialog(columnId) {
      this.resetDialogs();
      this.weightEditItemId = columnId;
    }
  }, {
    key: "showStudentScoreDialog",
    value: function showStudentScoreDialog(userId, itemId) {
      var menuTab = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'score';
      this.resetDialogs();
      this.scoreMenuTab = menuTab;
      this.editStudentScoreId = userId;
      this.editScoreId = itemId;
    }
  }, {
    key: "hideStudentScoreDialog",
    value: function hideStudentScoreDialog() {
      this.editStudentScoreId = null;
      this.editScoreId = null;
    }
  }, {
    key: "getColumnData",
    value: function getColumnData(columnId) {
      var gradeBook = this.gradeBook;
      var column = gradeBook.getGradeColumn(columnId);

      if (!column) {
        throw new Error("GradeColumn with id ".concat(columnId, " not found."));
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
  }, {
    key: "getColumns",
    value: function getColumns(category) {
      var _this2 = this;

      return category.columnIds.map(function (columnId) {
        return _this2.getColumnData(columnId);
      });
    }
  }, {
    key: "setCategoryTitle",
    value: function setCategoryTitle(id, title) {
      var category = this.gradeBook.getCategory(id);

      if (category) {
        category.title = title;
        this.$emit('change-category', category);
      }

      this.catEditItemId = null;
    }
  }, {
    key: "setTitle",
    value: function setTitle(columnId, title) {
      var gradeColumn = this.gradeBook.getGradeColumn(columnId);

      if (gradeColumn) {
        this.gradeBook.setTitle(columnId, title);
        this.$emit('change-gradecolumn', gradeColumn);
      }

      this.editItemId = null;
    }
  }, {
    key: "setWeight",
    value: function setWeight(columnId, weight) {
      var gradeColumn = this.gradeBook.getGradeColumn(columnId);

      if (gradeColumn) {
        this.gradeBook.setWeight(columnId, weight);
        this.$emit('change-gradecolumn', gradeColumn);
      }

      this.weightEditItemId = null;
    }
  }, {
    key: "toggleVisibility",
    value: function toggleVisibility(columnId) {
      var gradeColumn = this.gradeBook.getGradeColumn(columnId);

      if (gradeColumn) {
        gradeColumn.released = !gradeColumn.released;
        this.$emit('change-gradecolumn', gradeColumn);
      }
    }
  }, {
    key: "overwriteResult",
    value: function overwriteResult(userId, _ref) {
      var columnId = _ref.columnId,
          value = _ref.value;
      var score = this.gradeBook.overwriteResult(columnId, userId, value);

      if (!score) {
        return;
      }

      this.$emit('overwrite-result', score);
      this.hideStudentScoreDialog();
    }
  }, {
    key: "revertOverwrittenResult",
    value: function revertOverwrittenResult(userId, columnId) {
      var score = this.gradeBook.revertOverwrittenResult(columnId, userId);

      if (!score) {
        return;
      }

      this.$emit('revert-overwritten-result', score);
      this.hideStudentScoreDialog();
    }
  }, {
    key: "updateResultComment",
    value: function updateResultComment(userId, _ref2) {
      var columnId = _ref2.columnId,
          comment = _ref2.comment;
      var score = this.gradeBook.updateResultComment(columnId, userId, comment);

      if (!score) {
        return;
      }

      this.$emit('update-score-comment', score);
      this.hideStudentScoreDialog();
    }
  }, {
    key: "isSavingColumnWithId",
    value: function isSavingColumnWithId(columnId) {
      return this.saveColumnId === columnId;
    }
  }, {
    key: "isSavingCategoryWithId",
    value: function isSavingCategoryWithId(categoryId) {
      return this.saveCategoryId === categoryId;
    }
  }, {
    key: "startDragColumn",
    value: function startDragColumn(evt, id) {
      if (!evt.dataTransfer) {
        return;
      }

      evt.dataTransfer.setData('__COLUMN_ID', JSON.stringify({
        id: id
      }));
      this.isDraggingColumn = true;
    }
  }, {
    key: "startDragCategory",
    value: function startDragCategory(evt, id) {
      if (!evt.dataTransfer) {
        return;
      }

      evt.dataTransfer.setData('__CATEGORY_ID', JSON.stringify({
        id: id
      }));
      this.isDraggingCategory = true;
    }
  }, {
    key: "onDropAreaOverEnter",
    value: function onDropAreaOverEnter(evt, index) {
      if (!evt.dataTransfer) {
        return;
      }

      this.categoryDropArea = index;
      evt.dataTransfer.dropEffect = 'move';
      evt.dataTransfer.effectAllowed = 'copyMove';
    }
  }, {
    key: "onDragEnd",
    value: function onDragEnd() {
      this.categoryDropArea = null;
      this.isDraggingColumn = false;
      this.isDraggingColumn = false;
    }
  }, {
    key: "onDrop",
    value: function onDrop(evt, categoryId) {
      var _this3 = this;

      if (!evt.dataTransfer) {
        return;
      }

      if (this.isDraggingColumn) {
        var id = JSON.parse(evt.dataTransfer.getData('__COLUMN_ID')).id;

        if (categoryId === -1) {
          window.setTimeout(function () {
            _this3.$emit('move-gradecolumn', _this3.gradeBook.getGradeColumn(id));
          });
        } else {
          this.gradeBook.addItemToCategory(categoryId, id);
          this.$emit('change-gradecolumn-category', this.gradeBook.getGradeColumn(id), categoryId || null);
        }
      } else if (this.isDraggingCategory) {
        var _id = JSON.parse(evt.dataTransfer.getData('__CATEGORY_ID')).id;
        window.setTimeout(function () {
          _this3.$emit('move-category', _this3.gradeBook.getCategory(_id));
        }, 200);
      }
    }
  }, {
    key: "onShowNullCategoryChange",
    value: function onShowNullCategoryChange(showNullCategory) {
      if (showNullCategory) {
        window.setTimeout(function () {
          var _document$querySelect;

          (_document$querySelect = document.querySelector('.table-wrap')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.scrollBy(21, 0);
        }, 100);
      }
    }
  }, {
    key: "showNullCategory",
    get: function get() {
      return this.isDraggingColumn || this.gradeBook.nullCategory.columnIds.length > 0;
    }
  }, {
    key: "displayedCategories",
    get: function get() {
      if (this.showNullCategory) {
        return [].concat(toConsumableArray_toConsumableArray(this.gradeBook.categories), [this.gradeBook.nullCategory]);
      }

      return this.gradeBook.categories;
    }
  }, {
    key: "displayedUsers",
    get: function get() {
      var currentPage = this.pagination.currentPage;
      var perPage = this.itemsPerPage;
      return this.sortedUsers.slice((currentPage - 1) * perPage, currentPage * perPage);
    }
  }, {
    key: "filteredUsers",
    get: function get() {
      var _this4 = this;

      if (!this.searchTerms) {
        return this.gradeBook.users;
      }

      return this.gradeBook.users.filter(function (user) {
        var fullName = user.firstName.toLowerCase() + ' ' + user.lastName.toLowerCase();
        return _this4.searchTerms.every(function (term) {
          return fullName.indexOf(term) !== -1;
        });
      });
    }
  }, {
    key: "sortedUsers",
    get: function get() {
      var field;

      if (this.sortBy === 'lastname') {
        field = 'lastName';
      } else if (this.sortBy === 'firstname') {
        field = 'firstName';
      } else {
        return this.filteredUsers;
      }

      var users = toConsumableArray_toConsumableArray(this.filteredUsers);

      var mul = this.sortDesc ? -1 : 1;
      users.sort(function (u1, u2) {
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
  }]);

  return GradesTable;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: GradeBook_GradeBook,
  required: true
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "gradeBook", void 0);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "searchTerms", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "busy", void 0);

__decorate([Prop({
  type: [String, Number],
  "default": null
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "addColumnId", void 0);

__decorate([Prop({
  type: [String, Number],
  "default": null
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "saveColumnId", void 0);

__decorate([Prop({
  type: Number,
  "default": null
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "saveCategoryId", void 0);

__decorate([Prop({
  type: Number,
  "default": 5
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "itemsPerPage", void 0);

__decorate([Prop({
  type: String,
  "default": ''
})], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "gradeBookRootUrl", void 0);

__decorate([Watch('showNullCategory')], GradesTablevue_type_script_lang_ts_GradesTable.prototype, "onShowNullCategoryChange", null);

GradesTablevue_type_script_lang_ts_GradesTable = __decorate([vue_class_component_esm({
  name: 'grades-table',
  components: {
    StudentResultRow: components_StudentResultRow,
    ItemTitleInput: components_ItemTitleInput,
    WeightInput: components_WeightInput,
    ScoreInput: components_ScoreInput,
    StudentResult: components_StudentResult,
    draggable: vuedraggable_common_default.a
  },
  filters: {
    formatNum: function formatNum(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        maximumFractionDigits: 2
      });
    }
  }
})], GradesTablevue_type_script_lang_ts_GradesTable);
/* harmony default export */ var GradesTablevue_type_script_lang_ts_ = (GradesTablevue_type_script_lang_ts_GradesTable);
// CONCATENATED MODULE: ./src/components/GradesTable.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_GradesTablevue_type_script_lang_ts_ = (GradesTablevue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/GradesTable.vue?vue&type=style&index=0&id=990d6710&lang=scss&scoped=true&
var GradesTablevue_type_style_index_0_id_990d6710_lang_scss_scoped_true_ = __webpack_require__("b03a");

// EXTERNAL MODULE: ./src/components/GradesTable.vue?vue&type=style&index=1&id=990d6710&lang=scss&scoped=true&
var GradesTablevue_type_style_index_1_id_990d6710_lang_scss_scoped_true_ = __webpack_require__("798b");

// EXTERNAL MODULE: ./src/components/GradesTable.vue?vue&type=custom&index=0&blockType=i18n
var GradesTablevue_type_custom_index_0_blockType_i18n = __webpack_require__("f315");

// CONCATENATED MODULE: ./src/components/GradesTable.vue







/* normalize component */

var GradesTable_component = normalizeComponent(
  components_GradesTablevue_type_script_lang_ts_,
  GradesTablevue_type_template_id_990d6710_scoped_true_render,
  GradesTablevue_type_template_id_990d6710_scoped_true_staticRenderFns,
  false,
  null,
  "990d6710",
  null
  
)

/* custom blocks */

if (typeof GradesTablevue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(GradesTablevue_type_custom_index_0_blockType_i18n["default"])(GradesTable_component)

/* harmony default export */ var components_GradesTable = (GradesTable_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ItemSettings.vue?vue&type=template&id=0b7f26b5&scoped=true&
var ItemSettingsvue_type_template_id_0b7f26b5_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"modal-wrapper",attrs:{"role":"dialog","aria-modal":"true","aria-label":_vm.$t('column-settings')}},[_c('div',{staticClass:"modal-content"},[_c('div',{staticClass:"u-flex u-justify-content-between modal-header"},[_c('div',[_c('input',{ref:"column-title",attrs:{"type":"text","autocomplete":"off"},domProps:{"value":_vm.title},on:{"input":function($event){_vm.title = $event}}}),_c('button',{staticClass:"btn btn-link",on:{"click":function($event){_vm.showRemoveItemDialog = true}}},[_vm._v(_vm._s(_vm.$t('remove')))])]),_c('button',{staticClass:"btn-close u-ml-auto",attrs:{"title":_vm.$t('close')},on:{"click":function($event){return _vm.$emit('close')}}},[_c('i',{staticClass:"fa fa-times",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('close')))])])]),_c('div',{staticClass:"modal-body"},[(_vm.column.type !== 'standalone')?[(_vm.isGrouped)?_c('h5',[_vm._v(_vm._s(_vm.$t('grouped-scores')))]):_vm._e(),_c('ul',{staticClass:"grouped-scores"},_vm._l((_vm.subItems),function(item){return _c('li',{key:item.id},[_c('span',[_vm._v(_vm._s(item.title))]),_c('div',{staticClass:"score-breadcrumb-trail"},[_vm._v(_vm._s(_vm._f("breadcrumb")(item)))])])}),0),_c('div',{staticClass:"ml-20"},[(!(_vm.isGrouped || _vm.groupButtonPressed))?_c('button',{staticClass:"btn btn-default",on:{"click":_vm.openGradesDropdown}},[_vm._v(_vm._s(_vm.$t('group-scores')))]):_c('grades-dropdown',{ref:"dropdown",attrs:{"id":"dropdown-settings","graded-items":_vm.gradedItems},on:{"toggle":_vm.toggleSubItem}})],1)]:_vm._e(),_c('h5',{class:{'standalone': _vm.column.type === 'standalone'}},[_vm._v(_vm._s(_vm.$t('settings')))]),_c('div',{staticClass:"settings"},[_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.column.released),expression:"column.released"}],staticClass:"mr-5",attrs:{"type":"checkbox","id":"released"},domProps:{"checked":Array.isArray(_vm.column.released)?_vm._i(_vm.column.released,null)>-1:(_vm.column.released)},on:{"input":_vm.onGradeColumnChange,"change":function($event){var $$a=_vm.column.released,$$el=$event.target,$$c=$$el.checked?(true):(false);if(Array.isArray($$a)){var $$v=null,$$i=_vm._i($$a,$$v);if($$el.checked){$$i<0&&(_vm.$set(_vm.column, "released", $$a.concat([$$v])))}else{$$i>-1&&(_vm.$set(_vm.column, "released", $$a.slice(0,$$i).concat($$a.slice($$i+1))))}}else{_vm.$set(_vm.column, "released", $$c)}}}}),_c('label',{staticClass:"settings-label u-font-medium",attrs:{"for":"released"}},[_vm._v(_vm._s(_vm.$t('make-visible')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.column.countForEndResult),expression:"column.countForEndResult"}],staticClass:"mr-5",attrs:{"type":"checkbox","id":"countForEndResult"},domProps:{"checked":Array.isArray(_vm.column.countForEndResult)?_vm._i(_vm.column.countForEndResult,null)>-1:(_vm.column.countForEndResult)},on:{"input":_vm.onGradeColumnChange,"change":function($event){var $$a=_vm.column.countForEndResult,$$el=$event.target,$$c=$$el.checked?(true):(false);if(Array.isArray($$a)){var $$v=null,$$i=_vm._i($$a,$$v);if($$el.checked){$$i<0&&(_vm.$set(_vm.column, "countForEndResult", $$a.concat([$$v])))}else{$$i>-1&&(_vm.$set(_vm.column, "countForEndResult", $$a.slice(0,$$i).concat($$a.slice($$i+1))))}}else{_vm.$set(_vm.column, "countForEndResult", $$c)}}}}),_c('label',{staticClass:"settings-label u-font-medium",attrs:{"for":"countForEndResult"}},[_vm._v(_vm._s(_vm.$t('count-towards-endresult')))])]),(_vm.column.countForEndResult)?_c('div',[_c('div',{staticClass:"mt-10"},[_c('label',{staticClass:"settings-label u-block",attrs:{"for":"weight"}},[_vm._v(_vm._s(_vm.$t('weight'))+":")]),_c('div',{staticClass:"number-input u-relative"},[_c('input',{attrs:{"type":"number","id":"weight","autocomplete":"off"},domProps:{"value":_vm._f("formatNum")(_vm.gradeBook.getWeight(_vm.column))},on:{"input":_vm.setWeight}}),_vm._m(0)])]),_c('div',{staticClass:"mt-20",attrs:{"role":"radiogroup","aria-labelledby":"setting-aabs"}},[_c('label',{staticClass:"settings-label",attrs:{"id":"setting-aabs"}},[_vm._v(_vm._s(_vm.$t('authorized-absence'))+" "),_c('div',{staticClass:"color-code amber-700 mi-3",attrs:{"aria-hidden":"true","title":_vm.$t('auth-absent')}},[_c('span',[_vm._v(_vm._s(_vm.$t('aabs')))])]),_vm._v(":")]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.authPresenceEndResult),expression:"column.authPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-5",attrs:{"type":"radio","name":"gafw-option","id":"gafw-option1","value":"0"},domProps:{"checked":_vm._q(_vm.column.authPresenceEndResult,_vm._n("0"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "authPresenceEndResult", _vm._n("0"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"gafw-option1"}},[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.authPresenceEndResult),expression:"column.authPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-5",attrs:{"type":"radio","name":"gafw-option","id":"gafw-option2","value":"1"},domProps:{"checked":_vm._q(_vm.column.authPresenceEndResult,_vm._n("1"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "authPresenceEndResult", _vm._n("1"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"gafw-option2"}},[_vm._v(_vm._s(_vm.$t('maximum-towards-endresult')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.authPresenceEndResult),expression:"column.authPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-5",attrs:{"type":"radio","name":"gafw-option","id":"gafw-option3","value":"2"},domProps:{"checked":_vm._q(_vm.column.authPresenceEndResult,_vm._n("2"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "authPresenceEndResult", _vm._n("2"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"gafw-option3"}},[_vm._v(_vm._s(_vm.$t('minimum-towards-endresult')))])])]),_c('div',{staticClass:"mt-20",attrs:{"role":"radiogroup","aria-labelledby":"setting-uaabs"}},[_c('label',{staticClass:"settings-label",attrs:{"id":"setting-uaabs"}},[_vm._v(_vm._s(_vm.$t('unauthorized-absence'))+" "),_c('div',{staticClass:"color-code mod-none mi-3",attrs:{"title":_vm.$t('no-score-found')}},[_c('i',{staticClass:"fa fa-question",attrs:{"aria-hidden":"true"}})]),_vm._v(":")]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.unauthPresenceEndResult),expression:"column.unauthPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-5",attrs:{"type":"radio","name":"nogafw-option","id":"nogafw-option1","value":"0"},domProps:{"checked":_vm._q(_vm.column.unauthPresenceEndResult,_vm._n("0"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "unauthPresenceEndResult", _vm._n("0"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"nogafw-option1"}},[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.unauthPresenceEndResult),expression:"column.unauthPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-5",attrs:{"type":"radio","name":"nogafw-option","id":"nogafw-option2","value":"1"},domProps:{"checked":_vm._q(_vm.column.unauthPresenceEndResult,_vm._n("1"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "unauthPresenceEndResult", _vm._n("1"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"nogafw-option2"}},[_vm._v(_vm._s(_vm.$t('maximum-towards-endresult')))])]),_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model.number",value:(_vm.column.unauthPresenceEndResult),expression:"column.unauthPresenceEndResult",modifiers:{"number":true}}],staticClass:"mr-5",attrs:{"type":"radio","name":"nogafw-option","id":"nogafw-option3","value":"2"},domProps:{"checked":_vm._q(_vm.column.unauthPresenceEndResult,_vm._n("2"))},on:{"input":_vm.onGradeColumnChange,"change":function($event){_vm.$set(_vm.column, "unauthPresenceEndResult", _vm._n("2"))}}}),_c('label',{staticClass:"u-font-normal",attrs:{"for":"nogafw-option3"}},[_vm._v(_vm._s(_vm.$t('minimum-towards-endresult')))])])])]):_vm._e()])],2)]),_c('div',{staticClass:"modal-overlay",on:{"click":function($event){return _vm.$emit('close')}}}),(_vm.showRemoveItemDialog)?_c('div',{staticClass:"modal-remove",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center modal-remove-content"},[_c('div',[_vm._v(_vm._s(_vm.$t('remove-from-overview', {title: _vm.title})))]),_c('div',{staticClass:"u-flex modal-remove-actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.removeColumn}},[_vm._v(_vm._s(_vm.$t('remove')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])]):_vm._e()])}
var ItemSettingsvue_type_template_id_0b7f26b5_scoped_true_staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"percent"},[_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])])}]


// CONCATENATED MODULE: ./src/components/ItemSettings.vue?vue&type=template&id=0b7f26b5&scoped=true&

// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/debounce/index.js
var debounce = __webpack_require__("2f23");
var debounce_default = /*#__PURE__*/__webpack_require__.n(debounce);

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ItemSettings.vue?vue&type=script&lang=ts&
















var ItemSettingsvue_type_script_lang_ts_ItemSettings =
/*#__PURE__*/
function (_Vue) {
  _inherits(ItemSettings, _Vue);

  function ItemSettings() {
    var _this;

    _classCallCheck(this, ItemSettings);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ItemSettings).call(this));
    _this.groupButtonPressed = false;
    _this.showRemoveItemDialog = false;
    _this.onGradeColumnChange = debounce_default()(_this.onGradeColumnChange, 750);
    return _this;
  }

  _createClass(ItemSettings, [{
    key: "openGradesDropdown",
    value: function openGradesDropdown() {
      var _this2 = this;

      this.groupButtonPressed = true;
      window.setTimeout(function () {
        _this2.$refs['dropdown'].open();
      }, 100);
    }
  }, {
    key: "setWeight",
    value: function setWeight(event) {
      var weight = parseFloat(event.target.value);
      this.gradeBook.setWeight(this.columnId, isNaN(weight) ? null : weight);
      this.onGradeColumnChange();
    }
  }, {
    key: "toggleSubItem",
    value: function toggleSubItem(gradeItem, isAdding) {
      var item = this.gradeBook.gradeItems.find(function (item) {
        return item.id === gradeItem.id;
      });

      if (isAdding) {
        this.addSubItem(item);
      } else {
        this.removeSubItem(item);
      }
    }
  }, {
    key: "addSubItem",
    value: function addSubItem(item) {
      this.gradeBook.addSubItem(item, this.columnId);
      this.$emit('add-subitem', item, this.columnId);
    }
  }, {
    key: "removeSubItem",
    value: function removeSubItem(item) {
      this.gradeBook.removeSubItem(item);
      this.$emit('remove-subitem', item, this.columnId);

      if (item.id === this.columnId) {
        this.$emit('close');
      }
    }
  }, {
    key: "removeColumn",
    value: function removeColumn() {
      var column = this.column;

      if (column) {
        this.gradeBook.removeColumn(column);
        this.$emit('remove-column', column);
      }

      this.showRemoveItemDialog = false;
      this.$emit('close');
    }
  }, {
    key: "cancel",
    value: function cancel() {
      this.showRemoveItemDialog = false;
    }
  }, {
    key: "onGradeColumnChange",
    value: function onGradeColumnChange() {
      this.$emit('change-gradecolumn', this.column);
    }
  }, {
    key: "mounted",
    value: function mounted() {
      this.$refs['column-title'].focus();
    }
  }, {
    key: "column",
    get: function get() {
      return this.gradeBook.getGradeColumn(this.columnId);
    }
  }, {
    key: "isGrouped",
    get: function get() {
      var _this$column;

      return ((_this$column = this.column) === null || _this$column === void 0 ? void 0 : _this$column.type) === 'group';
    }
  }, {
    key: "subItems",
    get: function get() {
      return this.column ? this.gradeBook.getColumnSubItems(this.column) : [];
    }
  }, {
    key: "title",
    get: function get() {
      return this.column ? this.gradeBook.getTitle(this.column) : '';
    },
    set: function set(event) {
      this.gradeBook.setTitle(this.columnId, event.target.value);
      this.onGradeColumnChange();
    }
  }, {
    key: "gradedItems",
    get: function get() {
      return this.gradeBook.getStatusGradedItemsByColumn(this.columnId);
    }
  }]);

  return ItemSettings;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: GradeBook_GradeBook,
  required: true
})], ItemSettingsvue_type_script_lang_ts_ItemSettings.prototype, "gradeBook", void 0);

__decorate([Prop({
  type: [String, Number]
})], ItemSettingsvue_type_script_lang_ts_ItemSettings.prototype, "columnId", void 0);

ItemSettingsvue_type_script_lang_ts_ItemSettings = __decorate([vue_class_component_esm({
  components: {
    GradesDropdown: components_GradesDropdown
  },
  filters: {
    formatNum: function formatNum(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        maximumFractionDigits: 2
      });
    },
    breadcrumb: function breadcrumb(gradedItem) {
      return gradedItem.breadcrumb.join(' » ');
    }
  }
})], ItemSettingsvue_type_script_lang_ts_ItemSettings);
/* harmony default export */ var ItemSettingsvue_type_script_lang_ts_ = (ItemSettingsvue_type_script_lang_ts_ItemSettings);
// CONCATENATED MODULE: ./src/components/ItemSettings.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_ItemSettingsvue_type_script_lang_ts_ = (ItemSettingsvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/ItemSettings.vue?vue&type=style&index=0&id=0b7f26b5&lang=scss&scoped=true&
var ItemSettingsvue_type_style_index_0_id_0b7f26b5_lang_scss_scoped_true_ = __webpack_require__("4411");

// EXTERNAL MODULE: ./src/components/ItemSettings.vue?vue&type=custom&index=0&blockType=i18n
var ItemSettingsvue_type_custom_index_0_blockType_i18n = __webpack_require__("801a");

// CONCATENATED MODULE: ./src/components/ItemSettings.vue






/* normalize component */

var ItemSettings_component = normalizeComponent(
  components_ItemSettingsvue_type_script_lang_ts_,
  ItemSettingsvue_type_template_id_0b7f26b5_scoped_true_render,
  ItemSettingsvue_type_template_id_0b7f26b5_scoped_true_staticRenderFns,
  false,
  null,
  "0b7f26b5",
  null
  
)

/* custom blocks */

if (typeof ItemSettingsvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(ItemSettingsvue_type_custom_index_0_blockType_i18n["default"])(ItemSettings_component)

/* harmony default export */ var components_ItemSettings = (ItemSettings_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/CategorySettings.vue?vue&type=template&id=810f4a6c&scoped=true&
var CategorySettingsvue_type_template_id_810f4a6c_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"modal-wrapper",attrs:{"role":"dialog","aria-modal":"true","aria-label":_vm.$t('category-settings')}},[_c('div',{staticClass:"modal-content"},[_c('div',{staticClass:"u-flex u-justify-content-between modal-header"},[_c('div',[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.category.title),expression:"category.title"}],ref:"category-title",attrs:{"type":"text","autocomplete":"off"},domProps:{"value":(_vm.category.title)},on:{"input":[function($event){if($event.target.composing){ return; }_vm.$set(_vm.category, "title", $event.target.value)},_vm.onCategoryChange]}}),_c('button',{staticClass:"btn btn-link",on:{"click":function($event){_vm.showRemoveItemDialog = true}}},[_vm._v(_vm._s(_vm.$t('remove')))])]),_c('button',{staticClass:"btn-close u-ml-auto",attrs:{"title":_vm.$t('close')},on:{"click":function($event){return _vm.$emit('close')}}},[_c('i',{staticClass:"fa fa-times",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('close')))])])]),_c('div',{staticClass:"modal-body"},[_c('input',{directives:[{name:"model",rawName:"v-model",value:(_vm.category.color),expression:"category.color"}],attrs:{"type":"color"},domProps:{"value":(_vm.category.color)},on:{"input":[function($event){if($event.target.composing){ return; }_vm.$set(_vm.category, "color", $event.target.value)},_vm.onCategoryChange]}})])]),_c('div',{staticClass:"modal-overlay",on:{"click":function($event){return _vm.$emit('close')}}}),(_vm.showRemoveItemDialog)?_c('div',{staticClass:"modal-remove",on:{"click":function($event){$event.stopPropagation();}}},[_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-center modal-remove-content"},[_c('div',[_vm._v(_vm._s(_vm.$t('remove-category')))]),_c('div',{staticClass:"u-flex modal-remove-actions"},[_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.removeCategory}},[_vm._v(_vm._s(_vm.$t('remove')))]),_c('button',{staticClass:"btn btn-default btn-sm",on:{"click":_vm.cancel}},[_vm._v(_vm._s(_vm.$t('cancel')))])])])]):_vm._e()])}
var CategorySettingsvue_type_template_id_810f4a6c_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/CategorySettings.vue?vue&type=template&id=810f4a6c&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/CategorySettings.vue?vue&type=script&lang=ts&










var CategorySettingsvue_type_script_lang_ts_CategorySettings =
/*#__PURE__*/
function (_Vue) {
  _inherits(CategorySettings, _Vue);

  function CategorySettings() {
    var _this;

    _classCallCheck(this, CategorySettings);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(CategorySettings).call(this));
    _this.showRemoveItemDialog = false;
    _this.onCategoryChange = debounce_default()(_this.onCategoryChange, 750);
    return _this;
  }

  _createClass(CategorySettings, [{
    key: "onCategoryChange",
    value: function onCategoryChange() {
      this.$emit('change-category', this.category);
    }
  }, {
    key: "removeCategory",
    value: function removeCategory() {
      var category = this.category;
      this.gradeBook.removeCategory(category);
      this.$emit('remove-category', category);
      this.showRemoveItemDialog = false;
      this.$emit('close');
    }
  }, {
    key: "cancel",
    value: function cancel() {
      this.showRemoveItemDialog = false;
    }
  }, {
    key: "mounted",
    value: function mounted() {
      this.$refs['category-title'].focus();
    }
  }]);

  return CategorySettings;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: GradeBook_GradeBook,
  required: true
})], CategorySettingsvue_type_script_lang_ts_CategorySettings.prototype, "gradeBook", void 0);

__decorate([Prop({
  type: Object,
  required: true
})], CategorySettingsvue_type_script_lang_ts_CategorySettings.prototype, "category", void 0);

CategorySettingsvue_type_script_lang_ts_CategorySettings = __decorate([vue_class_component_esm({
  components: {}
})], CategorySettingsvue_type_script_lang_ts_CategorySettings);
/* harmony default export */ var CategorySettingsvue_type_script_lang_ts_ = (CategorySettingsvue_type_script_lang_ts_CategorySettings);
// CONCATENATED MODULE: ./src/components/CategorySettings.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_CategorySettingsvue_type_script_lang_ts_ = (CategorySettingsvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/CategorySettings.vue?vue&type=style&index=0&id=810f4a6c&lang=scss&scoped=true&
var CategorySettingsvue_type_style_index_0_id_810f4a6c_lang_scss_scoped_true_ = __webpack_require__("e613");

// EXTERNAL MODULE: ./src/components/CategorySettings.vue?vue&type=custom&index=0&blockType=i18n
var CategorySettingsvue_type_custom_index_0_blockType_i18n = __webpack_require__("33d1");

// CONCATENATED MODULE: ./src/components/CategorySettings.vue






/* normalize component */

var CategorySettings_component = normalizeComponent(
  components_CategorySettingsvue_type_script_lang_ts_,
  CategorySettingsvue_type_template_id_810f4a6c_scoped_true_render,
  CategorySettingsvue_type_template_id_810f4a6c_scoped_true_staticRenderFns,
  false,
  null,
  "810f4a6c",
  null
  
)

/* custom blocks */

if (typeof CategorySettingsvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(CategorySettingsvue_type_custom_index_0_blockType_i18n["default"])(CategorySettings_component)

/* harmony default export */ var components_CategorySettings = (CategorySettings_component.exports);
// EXTERNAL MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/core-js/modules/es.array.includes.js
var es_array_includes = __webpack_require__("7f6d");

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

// CONCATENATED MODULE: ./src/domain/Log.ts

function logResponse(data) {
  var responseEl = document.getElementById('server-response');

  if (!responseEl) {
    return;
  }

  responseEl.innerHTML = _typeof(data) === 'object' ? JSON.stringify(data, null, 4) : "<div>An error occurred:</div>".concat(data);
}
// CONCATENATED MODULE: ./src/connector/Connector.ts



















var HTTP_FORBIDDEN = 403;
var HTTP_NOT_FOUND = 404;
var HTTP_CONFLICT = 409;
var ERROR_UNKNOWN = 'UNKNOWN';
var TIMEOUT_SEC = 30;

function timeout(ms) {
  return new Promise(function (resolve) {
    return setTimeout(resolve, ms);
  });
}

var Connector_Connector =
/*#__PURE__*/
function () {
  function Connector(apiConfig, gradebookDataId, currentVersion) {
    _classCallCheck(this, Connector);

    this.queue = new dist_default.a({
      concurrency: 1
    });
    this._isSaving = false;
    this.errorListeners = [];
    this.apiConfig = apiConfig;
    this.gradebookDataId = gradebookDataId;
    this.currentVersion = currentVersion;
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
    key: "addCategory",
    value: function addCategory(category, callback) {
      var _this = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        var parameters, data;
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                parameters = {
                  'categoryData': JSON.stringify(category)
                };
                _context.next = 3;
                return _this.executeAPIRequest(_this.apiConfig.addCategoryURL, parameters);

              case 3:
                data = _context.sent;
                callback(data.category);

              case 5:
              case "end":
                return _context.stop();
            }
          }
        }, _callee);
      })));
    }
  }, {
    key: "updateCategory",
    value: function updateCategory(category) {
      var _this2 = this;

      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                parameters = {
                  'categoryData': JSON.stringify(category)
                };
                _context2.next = 3;
                return _this2.executeAPIRequest(_this2.apiConfig.updateCategoryURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2);
      })));
    }
  }, {
    key: "moveCategory",
    value: function moveCategory(category, newIndex) {
      var _this3 = this;

      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee3() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                parameters = {
                  'categoryData': JSON.stringify(category),
                  'newSort': newIndex + 1
                };
                _context3.next = 3;
                return _this3.executeAPIRequest(_this3.apiConfig.moveCategoryURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3);
      })));
    }
  }, {
    key: "removeCategory",
    value: function removeCategory(category) {
      var _this4 = this;

      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee4() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                parameters = {
                  'categoryData': JSON.stringify(category)
                };
                _context4.next = 3;
                return _this4.executeAPIRequest(_this4.apiConfig.removeCategoryURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4);
      })));
    }
  }, {
    key: "addGradeColumn",
    value: function addGradeColumn(gradeColumn, callback) {
      var _this5 = this;

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
                  'gradeColumnData': JSON.stringify(gradeColumn)
                };
                _context5.next = 3;
                return _this5.executeAPIRequest(_this5.apiConfig.addColumnURL, parameters);

              case 3:
                data = _context5.sent;
                callback(data.column, data.scores);

              case 5:
              case "end":
                return _context5.stop();
            }
          }
        }, _callee5);
      })));
    }
  }, {
    key: "addColumnSubItem",
    value: function addColumnSubItem(gradeColumnId, gradeItemId, callback) {
      var _this6 = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee6() {
        var parameters, data;
        return regeneratorRuntime.wrap(function _callee6$(_context6) {
          while (1) {
            switch (_context6.prev = _context6.next) {
              case 0:
                parameters = {
                  'gradeColumnId': gradeColumnId,
                  'gradeItemId': gradeItemId
                };
                _context6.next = 3;
                return _this6.executeAPIRequest(_this6.apiConfig.addColumnSubItemURL, parameters);

              case 3:
                data = _context6.sent;
                callback(data.column, data.scores);

              case 5:
              case "end":
                return _context6.stop();
            }
          }
        }, _callee6);
      })));
    }
  }, {
    key: "removeColumnSubItem",
    value: function removeColumnSubItem(gradeColumnId, gradeItemId, callback) {
      var _this7 = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee7() {
        var parameters, data;
        return regeneratorRuntime.wrap(function _callee7$(_context7) {
          while (1) {
            switch (_context7.prev = _context7.next) {
              case 0:
                parameters = {
                  'gradeColumnId': gradeColumnId,
                  'gradeItemId': gradeItemId
                };
                _context7.next = 3;
                return _this7.executeAPIRequest(_this7.apiConfig.removeColumnSubItemURL, parameters);

              case 3:
                data = _context7.sent;
                callback(data.column, data.scores);

              case 5:
              case "end":
                return _context7.stop();
            }
          }
        }, _callee7);
      })));
    }
  }, {
    key: "updateGradeColumn",
    value: function updateGradeColumn(gradeColumn) {
      var _this8 = this;

      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee8() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee8$(_context8) {
          while (1) {
            switch (_context8.prev = _context8.next) {
              case 0:
                parameters = {
                  'gradeColumnData': JSON.stringify(gradeColumn)
                };
                _context8.next = 3;
                return _this8.executeAPIRequest(_this8.apiConfig.updateColumnURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context8.stop();
            }
          }
        }, _callee8);
      })));
    }
  }, {
    key: "updateGradeColumnCategory",
    value: function updateGradeColumnCategory(gradeColumn, categoryId) {
      var _this9 = this;

      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee9() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee9$(_context9) {
          while (1) {
            switch (_context9.prev = _context9.next) {
              case 0:
                parameters = {
                  'gradeColumnId': gradeColumn.id,
                  'categoryId': categoryId
                };
                _context9.next = 3;
                return _this9.executeAPIRequest(_this9.apiConfig.updateColumnCategoryURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context9.stop();
            }
          }
        }, _callee9);
      })));
    }
  }, {
    key: "moveGradeColumn",
    value: function moveGradeColumn(gradeColumn, newIndex) {
      var _this10 = this;

      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee10() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee10$(_context10) {
          while (1) {
            switch (_context10.prev = _context10.next) {
              case 0:
                parameters = {
                  'gradeColumnId': gradeColumn.id,
                  'newSort': newIndex + 1
                };
                _context10.next = 3;
                return _this10.executeAPIRequest(_this10.apiConfig.moveColumnURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context10.stop();
            }
          }
        }, _callee10);
      })));
    }
  }, {
    key: "removeGradeColumn",
    value: function removeGradeColumn(gradeColumn) {
      var _this11 = this;

      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee11() {
        var parameters;
        return regeneratorRuntime.wrap(function _callee11$(_context11) {
          while (1) {
            switch (_context11.prev = _context11.next) {
              case 0:
                parameters = {
                  'gradeColumnId': gradeColumn.id
                };
                _context11.next = 3;
                return _this11.executeAPIRequest(_this11.apiConfig.removeColumnURL, parameters);

              case 3:
                if (callback) {
                  callback();
                }

              case 4:
              case "end":
                return _context11.stop();
            }
          }
        }, _callee11);
      })));
    }
  }, {
    key: "synchronizeGradeBook",
    value: function synchronizeGradeBook(callback) {
      var _this12 = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee12() {
        var data;
        return regeneratorRuntime.wrap(function _callee12$(_context12) {
          while (1) {
            switch (_context12.prev = _context12.next) {
              case 0:
                _context12.next = 2;
                return _this12.executeAPIRequest(_this12.apiConfig.synchronizeGradeBookURL);

              case 2:
                data = _context12.sent;
                callback(data.scores);

              case 4:
              case "end":
                return _context12.stop();
            }
          }
        }, _callee12);
      })));
      /*        return new Promise(resolve => {
                  this.addToQueue(async () => {
                      const data = await this.executeAPIRequest(this.apiConfig.synchronizeGradeBookURL);
                      resolve(data);
                  });
              })*/
    }
  }, {
    key: "overwriteGradeResult",
    value: function overwriteGradeResult(result, callback) {
      var _this13 = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee13() {
        var parameters, data;
        return regeneratorRuntime.wrap(function _callee13$(_context13) {
          while (1) {
            switch (_context13.prev = _context13.next) {
              case 0:
                parameters = {
                  'gradeScoreId': result.id,
                  'newScore': result.newScore,
                  'newScoreAuthAbsent': result.newScoreAuthAbsent
                };
                _context13.next = 3;
                return _this13.executeAPIRequest(_this13.apiConfig.overwriteScoreURL, parameters);

              case 3:
                data = _context13.sent;
                callback(data.score);

              case 5:
              case "end":
                return _context13.stop();
            }
          }
        }, _callee13);
      })));
    }
  }, {
    key: "revertOverwrittenGradeResult",
    value: function revertOverwrittenGradeResult(result, callback) {
      var _this14 = this;

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
                  'gradeScoreId': result.id
                };
                _context14.next = 3;
                return _this14.executeAPIRequest(_this14.apiConfig.revertOverwrittenScoreURL, parameters);

              case 3:
                data = _context14.sent;
                callback(data.score);

              case 5:
              case "end":
                return _context14.stop();
            }
          }
        }, _callee14);
      })));
    }
  }, {
    key: "updateGradeResultComment",
    value: function updateGradeResultComment(result, callback) {
      var _this15 = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee15() {
        var parameters, data;
        return regeneratorRuntime.wrap(function _callee15$(_context15) {
          while (1) {
            switch (_context15.prev = _context15.next) {
              case 0:
                parameters = {
                  'gradeScoreId': result.id,
                  'comment': result.comment
                };
                _context15.next = 3;
                return _this15.executeAPIRequest(_this15.apiConfig.updateScoreCommentURL, parameters);

              case 3:
                data = _context15.sent;
                callback(data.score);

              case 5:
              case "end":
                return _context15.stop();
            }
          }
        }, _callee15);
      })));
    }
  }, {
    key: "calculateTotalScores",
    value: function calculateTotalScores(callback) {
      var _this16 = this;

      this.addToQueue(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee16() {
        var data;
        return regeneratorRuntime.wrap(function _callee16$(_context16) {
          while (1) {
            switch (_context16.prev = _context16.next) {
              case 0:
                _context16.next = 2;
                return _this16.executeAPIRequest(_this16.apiConfig.calculateTotalScoresURL);

              case 2:
                data = _context16.sent;
                callback(data.totalScores);

              case 4:
              case "end":
                return _context16.stop();
            }
          }
        }, _callee16);
      })));
    }
  }, {
    key: "addToQueue",
    value: function addToQueue(callback) {
      this.queue.add(
      /*#__PURE__*/
      _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee17() {
        return regeneratorRuntime.wrap(function _callee17$(_context17) {
          while (1) {
            switch (_context17.prev = _context17.next) {
              case 0:
                _context17.next = 2;
                return callback();

              case 2:
              case "end":
                return _context17.stop();
            }
          }
        }, _callee17);
      })));
      this.queue.onIdle().then(this.finishSaving);
    }
  }, {
    key: "executeAPIRequest",
    value: function () {
      var _executeAPIRequest = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee18(apiURL) {
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
            _err$response2,
            _err$response2$data,
            error,
            status,
            _args18 = arguments;

        return regeneratorRuntime.wrap(function _callee18$(_context18) {
          while (1) {
            switch (_context18.prev = _context18.next) {
              case 0:
                parameters = _args18.length > 1 && _args18[1] !== undefined ? _args18[1] : {};
                this.beginSaving();
                parameters['gradebookDataId'] = this.gradebookDataId;
                parameters['version'] = this.currentVersion;
                formData = new FormData();

                if (this.apiConfig.csrfToken) {
                  formData.set('_csrf_token', this.apiConfig.csrfToken);
                }

                for (_i = 0, _Object$entries = Object.entries(parameters); _i < _Object$entries.length; _i++) {
                  _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2), key = _Object$entries$_i[0], value = _Object$entries$_i[1];
                  formData.set(key, value);
                }

                _context18.prev = 7;
                _context18.next = 10;
                return axios_default.a.post(apiURL, formData, {
                  timeout: TIMEOUT_SEC * 1000
                });

              case 10:
                res = _context18.sent;
                logResponse(res.data);

                if (!(_typeof(res.data) === 'object')) {
                  _context18.next = 18;
                  break;
                }

                this.gradebookDataId = res.data.gradebook.dataId;
                this.currentVersion = res.data.gradebook.version;
                return _context18.abrupt("return", res.data);

              case 18:
                if (!(typeof res.data === 'string' && res.data.indexOf('login') !== -1)) {
                  _context18.next = 22;
                  break;
                }

                throw {
                  'type': 'LoggedOut'
                };

              case 22:
                throw {
                  'type': 'Unknown'
                };

              case 23:
                _context18.next = 30;
                break;

              case 25:
                _context18.prev = 25;
                _context18.t0 = _context18["catch"](7);
                logResponse(_context18.t0);

                if ((_context18.t0 === null || _context18.t0 === void 0 ? void 0 : _context18.t0.isAxiosError) && ((_err$message = _context18.t0.message) === null || _err$message === void 0 ? void 0 : _err$message.toLowerCase().indexOf('timeout')) !== -1) {
                  error = {
                    'type': 'Timeout'
                  };
                } else if ([HTTP_FORBIDDEN, HTTP_NOT_FOUND, HTTP_CONFLICT, ERROR_UNKNOWN].includes(_context18.t0 === null || _context18.t0 === void 0 ? void 0 : (_err$response = _context18.t0.response) === null || _err$response === void 0 ? void 0 : _err$response.status)) {
                  status = _context18.t0.response.status;

                  if (status === HTTP_FORBIDDEN) {
                    error = {
                      'type': 'Forbidden'
                    };
                  } else if (status === HTTP_NOT_FOUND) {
                    error = {
                      'type': 'NotFound'
                    };
                  } else if (status === HTTP_CONFLICT) {
                    error = {
                      'type': 'Conflict'
                    };
                  } else {
                    error = {
                      'type': 'Unknown'
                    };
                  }
                } else if (_context18.t0 === null || _context18.t0 === void 0 ? void 0 : (_err$response2 = _context18.t0.response) === null || _err$response2 === void 0 ? void 0 : (_err$response2$data = _err$response2.data) === null || _err$response2$data === void 0 ? void 0 : _err$response2$data.error) {
                  error = _context18.t0.response.data.error;
                } else if (_context18.t0 === null || _context18.t0 === void 0 ? void 0 : _context18.t0.type) {
                  error = _context18.t0;
                } else {
                  error = {
                    'type': 'Unknown'
                  };
                }

                this.errorListeners.forEach(function (errorListener) {
                  return errorListener.setError(error);
                });

              case 30:
              case "end":
                return _context18.stop();
            }
          }
        }, _callee18, this, [[7, 25]]);
      }));

      function executeAPIRequest(_x) {
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
  }], [{
    key: "loadGradeBookData",
    value: function () {
      var _loadGradeBookData = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee19(loadAllURL, csrfToken) {
        var params, res;
        return regeneratorRuntime.wrap(function _callee19$(_context19) {
          while (1) {
            switch (_context19.prev = _context19.next) {
              case 0:
                params = csrfToken ? {
                  '_csrf_token': csrfToken
                } : {};
                _context19.next = 3;
                return axios_default.a.get(loadAllURL, {
                  params: params
                });

              case 3:
                res = _context19.sent;
                return _context19.abrupt("return", res.data);

              case 5:
              case "end":
                return _context19.stop();
            }
          }
        }, _callee19);
      }));

      function loadGradeBookData(_x2, _x3) {
        return _loadGradeBookData.apply(this, arguments);
      }

      return loadGradeBookData;
    }()
  }]);

  return Connector;
}();


// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=template&id=00a31407&scoped=true&
var ErrorDisplayvue_type_template_id_00a31407_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"u-contents",attrs:{"role":"alertdialog","aria-modal":"true","aria-label":_vm.$t('errors')}},[_c('div',{staticClass:"modal-overlay"}),_c('div',{staticClass:"save-error u-flex u-justify-content-center"},[_c('div',{staticClass:"save-error-inner"},[_c('div',{staticClass:"errors-important u-flex u-align-items-baseline"},[_c('i',{staticClass:"fa fa-exclamation-circle mod-icon",attrs:{"aria-hidden":"true"}}),_c('div',[_vm._t("default"),_c('div',{staticClass:"u-text-center mt-5"},[_c('button',{ref:"btn-ok",staticClass:"btn btn-success btn-sm",on:{"click":function($event){return _vm.$emit('close')}}},[_vm._v("OK")])])],2)])])])])}
var ErrorDisplayvue_type_template_id_00a31407_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=template&id=00a31407&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ErrorDisplay.vue?vue&type=script&lang=ts&








var ErrorDisplayvue_type_script_lang_ts_ErrorDisplay =
/*#__PURE__*/
function (_Vue) {
  _inherits(ErrorDisplay, _Vue);

  function ErrorDisplay() {
    _classCallCheck(this, ErrorDisplay);

    return _possibleConstructorReturn(this, _getPrototypeOf(ErrorDisplay).apply(this, arguments));
  }

  _createClass(ErrorDisplay, [{
    key: "mounted",
    value: function mounted() {
      this.$refs['btn-ok'].focus();
    }
  }]);

  return ErrorDisplay;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

ErrorDisplayvue_type_script_lang_ts_ErrorDisplay = __decorate([vue_class_component_esm({
  name: 'error-display'
})], ErrorDisplayvue_type_script_lang_ts_ErrorDisplay);
/* harmony default export */ var ErrorDisplayvue_type_script_lang_ts_ = (ErrorDisplayvue_type_script_lang_ts_ErrorDisplay);
// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_ErrorDisplayvue_type_script_lang_ts_ = (ErrorDisplayvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/ErrorDisplay.vue?vue&type=style&index=0&id=00a31407&scoped=true&lang=css&
var ErrorDisplayvue_type_style_index_0_id_00a31407_scoped_true_lang_css_ = __webpack_require__("717c");

// EXTERNAL MODULE: ./src/components/ErrorDisplay.vue?vue&type=custom&index=0&blockType=i18n
var ErrorDisplayvue_type_custom_index_0_blockType_i18n = __webpack_require__("bced");

// CONCATENATED MODULE: ./src/components/ErrorDisplay.vue






/* normalize component */

var ErrorDisplay_component = normalizeComponent(
  components_ErrorDisplayvue_type_script_lang_ts_,
  ErrorDisplayvue_type_template_id_00a31407_scoped_true_render,
  ErrorDisplayvue_type_template_id_00a31407_scoped_true_staticRenderFns,
  false,
  null,
  "00a31407",
  null
  
)

/* custom blocks */

if (typeof ErrorDisplayvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(ErrorDisplayvue_type_custom_index_0_blockType_i18n["default"])(ErrorDisplay_component)

/* harmony default export */ var components_ErrorDisplay = (ErrorDisplay_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/Main.vue?vue&type=script&lang=ts&


























var ITEMS_PER_PAGE_KEY = 'chamilo-gradebook.itemsPerPage';

var Mainvue_type_script_lang_ts_Main =
/*#__PURE__*/
function (_Vue) {
  _inherits(Main, _Vue);

  function Main() {
    var _this;

    _classCallCheck(this, Main);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Main).call(this));
    _this.gradeBook = null;
    _this.connector = null;
    _this.itemSettings = null;
    _this.categorySettings = null;
    _this.studentSearchTerm = '';
    _this.studentSearchTerms = [];
    _this.tableBusy = false;
    _this.saveColumnId = null;
    _this.saveCategoryId = null;
    _this.itemsPerPage = 5;
    _this.errorData = null;
    _this.addColumnId = null;
    _this.updateResult = _this.updateResult.bind(_assertThisInitialized(_this));
    return _this;
  }

  _createClass(Main, [{
    key: "updateGradeColumnWithScores",
    value: function updateGradeColumnWithScores(column, id, scores) {
      if (!this.gradeBook) {
        return;
      }

      this.gradeBook.updateGradeColumnId(column, id);
      var resultsData = this.gradeBook.resultsData;
      scores.forEach(function (score) {
        if (!resultsData[score.columnId]) {
          external_commonjs_vue_commonjs2_vue_root_Vue_default.a.set(resultsData, score.columnId, {});
        }

        resultsData[score.columnId][score.targetUserId] = score;
      });
    }
  }, {
    key: "addGradeItem",
    value: function addGradeItem(item) {
      var _this$connector,
          _this2 = this;

      if (!this.gradeBook) {
        return;
      }

      var column = this.gradeBook.addGradeColumnFromItem(item);
      this.addColumnId = column.id;
      this.tableBusy = true;
      (_this$connector = this.connector) === null || _this$connector === void 0 ? void 0 : _this$connector.addGradeColumn(column, function (_ref, scores) {
        var id = _ref.id;

        _this2.updateGradeColumnWithScores(column, id, scores);

        _this2.resetGradeBook();

        _this2.tableBusy = false;
        _this2.addColumnId = null;
      });
    }
  }, {
    key: "removeGradeItem",
    value: function removeGradeItem(item) {
      if (!this.gradeBook) {
        return;
      }

      var column = this.gradeBook.findGradeColumnWithGradeItem(item.id);

      if (!column) {
        return;
      }

      if (column.type === 'item') {
        this.gradeBook.removeColumn(column);
        this.onRemoveColumn(column);
      } else {
        this.gradeBook.removeSubItem(item);
        this.onRemoveSubItem(item, column.id);
      }
    }
  }, {
    key: "toggleGradeItem",
    value: function toggleGradeItem(item, isAdding) {
      if (isAdding) {
        this.addGradeItem(item);
      } else {
        this.removeGradeItem(item);
      }
    }
  }, {
    key: "resetGradeBook",
    value: function resetGradeBook() {
      var _this3 = this;

      var gradeBook = this.gradeBook;
      this.gradeBook = null;
      this.$nextTick(function () {
        _this3.gradeBook = gradeBook;
      });
    }
  }, {
    key: "createNewCategory",
    value: function createNewCategory() {
      var _this$connector2,
          _this4 = this;

      if (!this.gradeBook) {
        return;
      }

      var category = this.gradeBook.createNewCategory();
      this.tableBusy = true;
      (_this$connector2 = this.connector) === null || _this$connector2 === void 0 ? void 0 : _this$connector2.addCategory(category, function (cat) {
        category.id = cat.id;
        _this4.categorySettings = cat.id;

        _this4.resetGradeBook();

        _this4.tableBusy = false;
      });
    }
  }, {
    key: "synchronizeGradeBook",
    value: function () {
      var _synchronizeGradeBook = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        var _this$connector3,
            _this5 = this;

        var gradeBook;
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (this.gradeBook) {
                  _context.next = 2;
                  break;
                }

                return _context.abrupt("return");

              case 2:
                gradeBook = this.gradeBook;
                this.tableBusy = true;
                _context.next = 6;
                return (_this$connector3 = this.connector) === null || _this$connector3 === void 0 ? void 0 : _this$connector3.synchronizeGradeBook(function (scores) {
                  var resultsData = gradeBook.resultsData;

                  if (!resultsData['totals']) {
                    external_commonjs_vue_commonjs2_vue_root_Vue_default.a.set(resultsData, 'totals', {});
                  }

                  scores.forEach(function (score) {
                    if (score.isTotal) {
                      resultsData['totals'][score.targetUserId] = score;
                      return;
                    }

                    if (!resultsData[score.columnId]) {
                      external_commonjs_vue_commonjs2_vue_root_Vue_default.a.set(resultsData, score.columnId, {});
                    }

                    resultsData[score.columnId][score.targetUserId] = score;
                  });
                  _this5.tableBusy = false;
                });

              case 6:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function synchronizeGradeBook() {
        return _synchronizeGradeBook.apply(this, arguments);
      }

      return synchronizeGradeBook;
    }()
  }, {
    key: "updateTotalScores",
    value: function () {
      var _updateTotalScores = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2() {
        var _this$connector4,
            _this6 = this;

        var gradeBook;
        return regeneratorRuntime.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                if (this.gradeBook) {
                  _context2.next = 2;
                  break;
                }

                return _context2.abrupt("return");

              case 2:
                gradeBook = this.gradeBook;
                this.tableBusy = true;
                _context2.next = 6;
                return (_this$connector4 = this.connector) === null || _this$connector4 === void 0 ? void 0 : _this$connector4.calculateTotalScores(function (scores) {
                  var resultsData = gradeBook.resultsData;

                  if (!resultsData['totals']) {
                    external_commonjs_vue_commonjs2_vue_root_Vue_default.a.set(resultsData, 'totals', {});
                  }

                  scores.forEach(function (score) {
                    resultsData['totals'][score.targetUserId] = score;
                  });
                  _this6.tableBusy = false;
                });

              case 6:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function updateTotalScores() {
        return _updateTotalScores.apply(this, arguments);
      }

      return updateTotalScores;
    }()
  }, {
    key: "createNewScore",
    value: function createNewScore() {
      var _this$connector5,
          _this7 = this;

      if (!this.gradeBook) {
        return;
      }

      var column = this.gradeBook.createNewScore();
      this.addColumnId = column.id;
      this.tableBusy = true;
      (_this$connector5 = this.connector) === null || _this$connector5 === void 0 ? void 0 : _this$connector5.addGradeColumn(column, function (_ref2, scores) {
        var id = _ref2.id;

        _this7.updateGradeColumnWithScores(column, id, scores);

        _this7.resetGradeBook();

        _this7.tableBusy = false;
        _this7.addColumnId = null;
      });
    }
  }, {
    key: "closeSelectedCategory",
    value: function closeSelectedCategory() {
      this.categorySettings = null;
    }
  }, {
    key: "onChangeCategory",
    value: function onChangeCategory(category) {
      var _this$connector6,
          _this8 = this;

      this.saveCategoryId = category.id;
      (_this$connector6 = this.connector) === null || _this$connector6 === void 0 ? void 0 : _this$connector6.updateCategory(category, function () {
        _this8.saveCategoryId = null;
      });
    }
  }, {
    key: "onMoveCategory",
    value: function () {
      var _onMoveCategory = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee3(category) {
        var _this$connector7,
            _this9 = this;

        return regeneratorRuntime.wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                if (this.gradeBook) {
                  _context3.next = 2;
                  break;
                }

                return _context3.abrupt("return");

              case 2:
                this.tableBusy = true;
                _context3.next = 5;
                return (_this$connector7 = this.connector) === null || _this$connector7 === void 0 ? void 0 : _this$connector7.moveCategory(category, this.gradeBook.categories.indexOf(category), function () {
                  _this9.tableBusy = false;
                });

              case 5:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, this);
      }));

      function onMoveCategory(_x) {
        return _onMoveCategory.apply(this, arguments);
      }

      return onMoveCategory;
    }()
  }, {
    key: "onRemoveCategory",
    value: function onRemoveCategory(category) {
      var _this$connector8,
          _this10 = this;

      this.tableBusy = true;
      (_this$connector8 = this.connector) === null || _this$connector8 === void 0 ? void 0 : _this$connector8.removeCategory(category, function () {
        _this10.tableBusy = false;
      });
    }
  }, {
    key: "onChangeGradeColumn",
    value: function onChangeGradeColumn(gradeColumn) {
      var _this$connector9,
          _this11 = this;

      this.saveColumnId = gradeColumn.id;
      (_this$connector9 = this.connector) === null || _this$connector9 === void 0 ? void 0 : _this$connector9.updateGradeColumn(gradeColumn, function () {
        _this11.saveColumnId = null;
      });
    }
  }, {
    key: "onChangeGradeColumnCategory",
    value: function onChangeGradeColumnCategory(gradeColumn, categoryId) {
      var _this$connector10,
          _this12 = this;

      this.tableBusy = true;
      (_this$connector10 = this.connector) === null || _this$connector10 === void 0 ? void 0 : _this$connector10.updateGradeColumnCategory(gradeColumn, categoryId, function () {
        _this12.tableBusy = false;
      });
    }
  }, {
    key: "onMoveGradeColumn",
    value: function onMoveGradeColumn(column) {
      var _this13 = this;

      if (!this.gradeBook) {
        return;
      }

      var category = this.gradeBook.allCategories.find(function (category) {
        return category.columnIds.indexOf(column.id) !== -1;
      });

      if (category) {
        var _this$connector11;

        this.tableBusy = true;
        (_this$connector11 = this.connector) === null || _this$connector11 === void 0 ? void 0 : _this$connector11.moveGradeColumn(column, category.columnIds.indexOf(column.id), function () {
          _this13.tableBusy = false;
        });
      }
    }
  }, {
    key: "onAddSubItem",
    value: function onAddSubItem(item, columnId) {
      var _this$connector12,
          _this14 = this;

      if (!this.gradeBook) {
        return;
      }

      var gradeBook = this.gradeBook;
      this.tableBusy = true;
      (_this$connector12 = this.connector) === null || _this$connector12 === void 0 ? void 0 : _this$connector12.addColumnSubItem(columnId, item.id, function (column, scores) {
        //console.log('scores', scores);
        var resultsData = gradeBook.resultsData;
        delete resultsData[columnId];
        scores.forEach(function (score) {
          if (!resultsData[columnId]) {
            external_commonjs_vue_commonjs2_vue_root_Vue_default.a.set(resultsData, columnId, {});
          }

          resultsData[columnId][score.targetUserId] = score;
        });
        _this14.tableBusy = false;
      });
    }
  }, {
    key: "onRemoveSubItem",
    value: function onRemoveSubItem(item, columnId) {
      var _this$connector13,
          _this15 = this;

      if (!this.gradeBook) {
        return;
      }

      var gradeBook = this.gradeBook;
      this.tableBusy = true;
      (_this$connector13 = this.connector) === null || _this$connector13 === void 0 ? void 0 : _this$connector13.removeColumnSubItem(columnId, item.id, function (column, scores) {
        //console.log('scores', scores);
        var resultsData = gradeBook.resultsData;
        delete resultsData[columnId];
        scores.forEach(function (score) {
          if (!resultsData[columnId]) {
            external_commonjs_vue_commonjs2_vue_root_Vue_default.a.set(resultsData, columnId, {});
          }

          resultsData[columnId][score.targetUserId] = score;
        });
        _this15.tableBusy = false;
      });
    }
  }, {
    key: "onRemoveColumn",
    value: function onRemoveColumn(column) {
      var _this$connector14,
          _this16 = this;

      this.tableBusy = true;
      (_this$connector14 = this.connector) === null || _this$connector14 === void 0 ? void 0 : _this$connector14.removeGradeColumn(column, function () {
        _this16.tableBusy = false;
      });
    }
  }, {
    key: "updateResult",
    value: function updateResult(result) {
      if (!this.gradeBook) {
        return;
      }

      this.saveColumnId = null;
      var colScores = this.gradeBook.resultsData[result.columnId];

      if (!colScores) {
        return;
      }

      colScores[result.targetUserId] = result;
    }
  }, {
    key: "onOverwriteResult",
    value: function onOverwriteResult(result) {
      var _this$connector15;

      this.saveColumnId = result.columnId;
      (_this$connector15 = this.connector) === null || _this$connector15 === void 0 ? void 0 : _this$connector15.overwriteGradeResult(result, this.updateResult);
    }
  }, {
    key: "onRevertOverwrittenResult",
    value: function onRevertOverwrittenResult(result) {
      var _this$connector16;

      this.saveColumnId = result.columnId;
      (_this$connector16 = this.connector) === null || _this$connector16 === void 0 ? void 0 : _this$connector16.revertOverwrittenGradeResult(result, this.updateResult);
    }
  }, {
    key: "onUpdateScoreComment",
    value: function onUpdateScoreComment(result) {
      var _this$connector17;

      this.saveColumnId = result.columnId;
      (_this$connector17 = this.connector) === null || _this$connector17 === void 0 ? void 0 : _this$connector17.updateGradeResultComment(result, this.updateResult);
    }
  }, {
    key: "loadItemsPerPage",
    value: function loadItemsPerPage() {
      this.itemsPerPage = parseInt(localStorage.getItem(ITEMS_PER_PAGE_KEY) || '5');
    }
  }, {
    key: "setItemsPerPage",
    value: function setItemsPerPage(count) {
      this.itemsPerPage = count;
      localStorage.setItem(ITEMS_PER_PAGE_KEY, String(count));
    }
  }, {
    key: "setError",
    value: function setError(data) {
      this.errorData = data;
    }
  }, {
    key: "closeErrorDisplay",
    value: function closeErrorDisplay() {
      this.errorData = null;
      this.saveColumnId = null;
      this.saveCategoryId = null;
      this.tableBusy = false;
    }
  }, {
    key: "load",
    value: function () {
      var _load = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee4() {
        var allData, resultsData;
        return regeneratorRuntime.wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                _context4.next = 2;
                return Connector_Connector.loadGradeBookData(this.apiConfig.loadGradeBookDataURL, this.apiConfig.csrfToken);

              case 2:
                allData = _context4.sent;
                console.log(allData);

                if (allData) {
                  this.gradeBook = GradeBook_GradeBook.from(allData.gradebook);
                  this.gradeBook.users = allData.users;
                  this.connector = new Connector_Connector(this.apiConfig, this.gradeBook.dataId, this.gradeBook.currentVersion);
                  this.connector.addErrorListener(this);
                  resultsData = {
                    'totals': {}
                  };
                  allData.scores.forEach(function (score) {
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

                console.log(this.gradeBook);

              case 6:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4, this);
      }));

      function load() {
        return _load.apply(this, arguments);
      }

      return load;
    }()
  }, {
    key: "mounted",
    value: function mounted() {
      this.load();
      this.loadItemsPerPage(); //console.log(this);
    }
  }, {
    key: "searchTerm",
    get: function get() {
      return this.studentSearchTerm;
    },
    set: function set(term) {
      this.studentSearchTerm = term;
      this.studentSearchTerms = term.toLowerCase().split(' ').filter(function (s) {
        return s.length;
      });
    }
  }, {
    key: "selectedCategory",
    get: function get() {
      var _this$gradeBook,
          _this17 = this;

      return ((_this$gradeBook = this.gradeBook) === null || _this$gradeBook === void 0 ? void 0 : _this$gradeBook.categories.find(function (cat) {
        return cat.id === _this17.categorySettings;
      })) || null;
    }
  }]);

  return Main;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object,
  "default": function _default() {
    return null;
  }
})], Mainvue_type_script_lang_ts_Main.prototype, "apiConfig", void 0);

Mainvue_type_script_lang_ts_Main = __decorate([vue_class_component_esm({
  components: {
    ErrorDisplay: components_ErrorDisplay,
    GradesTable: components_GradesTable,
    GradesDropdown: components_GradesDropdown,
    ItemSettings: components_ItemSettings,
    CategorySettings: components_CategorySettings
  }
})], Mainvue_type_script_lang_ts_Main);
/* harmony default export */ var Mainvue_type_script_lang_ts_ = (Mainvue_type_script_lang_ts_Main);
// CONCATENATED MODULE: ./src/components/Main.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_Mainvue_type_script_lang_ts_ = (Mainvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/Main.vue?vue&type=style&index=0&lang=css&
var Mainvue_type_style_index_0_lang_css_ = __webpack_require__("c266");

// EXTERNAL MODULE: ./src/components/Main.vue?vue&type=style&index=1&id=03563a68&lang=scss&scoped=true&
var Mainvue_type_style_index_1_id_03563a68_lang_scss_scoped_true_ = __webpack_require__("b67c");

// EXTERNAL MODULE: ./src/components/Main.vue?vue&type=style&index=2&lang=css&
var Mainvue_type_style_index_2_lang_css_ = __webpack_require__("59b0");

// EXTERNAL MODULE: ./src/components/Main.vue?vue&type=custom&index=0&blockType=i18n
var Mainvue_type_custom_index_0_blockType_i18n = __webpack_require__("2c8b");

// CONCATENATED MODULE: ./src/components/Main.vue








/* normalize component */

var Main_component = normalizeComponent(
  components_Mainvue_type_script_lang_ts_,
  Mainvue_type_template_id_03563a68_scoped_true_render,
  Mainvue_type_template_id_03563a68_scoped_true_staticRenderFns,
  false,
  null,
  "03563a68",
  null
  
)

/* custom blocks */

if (typeof Mainvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(Mainvue_type_custom_index_0_blockType_i18n["default"])(Main_component)

/* harmony default export */ var components_Main = (Main_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/App.vue?vue&type=script&lang=ts&








var Appvue_type_script_lang_ts_App =
/*#__PURE__*/
function (_Vue) {
  _inherits(App, _Vue);

  function App() {
    _classCallCheck(this, App);

    return _possibleConstructorReturn(this, _getPrototypeOf(App).apply(this, arguments));
  }

  return App;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object,
  "default": function _default() {
    return null;
  }
})], Appvue_type_script_lang_ts_App.prototype, "apiConfig", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], Appvue_type_script_lang_ts_App.prototype, "debugServerResponse", void 0);

Appvue_type_script_lang_ts_App = __decorate([vue_class_component_esm({
  components: {
    Main: components_Main
  }
})], Appvue_type_script_lang_ts_App);
/* harmony default export */ var Appvue_type_script_lang_ts_ = (Appvue_type_script_lang_ts_App);
// CONCATENATED MODULE: ./src/App.vue?vue&type=script&lang=ts&
 /* harmony default export */ var src_Appvue_type_script_lang_ts_ = (Appvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/App.vue?vue&type=style&index=0&lang=css&
var Appvue_type_style_index_0_lang_css_ = __webpack_require__("034f");

// CONCATENATED MODULE: ./src/App.vue






/* normalize component */

var App_component = normalizeComponent(
  src_Appvue_type_script_lang_ts_,
  render,
  staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* harmony default export */ var src_App = (App_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/ImporterApp.vue?vue&type=template&id=a97afd16&scoped=true&
var ImporterAppvue_type_template_id_a97afd16_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{attrs:{"id":"app"}},[_c('div',{attrs:{"id":"gradebook-import"}},[_c('ol',{staticClass:"nav nav-tabs mod-steps",attrs:{"role":"navigation","aria-label":_vm.$t('import-steps')}},[_c('li',{staticClass:"nav-item u-cursor-pointer",class:{'active': _vm.chooseTypeActive, 'done': _vm.importType},attrs:{"aria-current":_vm.chooseTypeActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block",on:{"click":_vm.reload}},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("1")]),_vm._v(_vm._s(_vm.$t('choose-type')))])]),_c('li',{staticClass:"nav-item",class:{'active': _vm.chooseFileActive, 'done': _vm.imported || _vm.resultsLoaded},attrs:{"aria-current":_vm.chooseFileActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block"},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("2")]),_vm._v(_vm._s(_vm.$t('choose-file')))])]),_c('li',{staticClass:"nav-item",class:{'active': _vm.previewActive, 'done': _vm.resultsLoaded},attrs:{"aria-current":_vm.previewActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block"},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("3")]),_vm._v(_vm._s(_vm.$t('import-preview')))])]),_c('li',{staticClass:"nav-item",class:{'active': _vm.importCompleteActive},attrs:{"aria-current":_vm.importCompleteActive ? 'step' : null}},[_c('a',{staticClass:"nav-link u-block"},[_c('span',{staticClass:"step u-inline-block"},[_vm._v("4")]),_vm._v(_vm._s(_vm.$t('import-complete')))])])]),(_vm.chooseTypeActive)?_c('div',[_c('p',{staticClass:"gradebook-import-type u-font-medium"},[_vm._v(_vm._s(_vm.$t('question-upload')))]),_c('div',{staticClass:"u-flex u-gap-small-2x"},[_c('button',{staticClass:"btn btn-light fs-13",on:{"click":function($event){_vm.importType = 'scores'}}},[_vm._v(_vm._s(_vm.$t('type-scores')))]),_c('button',{staticClass:"btn btn-default fs-13",on:{"click":function($event){_vm.importType = 'scores_comments'}}},[_vm._v(_vm._s(_vm.$t('type-scores-comments')))])])]):_vm._e(),(_vm.chooseFileActive && !_vm.hasError)?_c('div',[_c('div',{staticClass:"gradebook-import-file u-font-medium"},[_vm._v(_vm._s(_vm.$t('file-with'))+" "+_vm._s(_vm.importType === 'scores' ? _vm.$t('type-scores') : _vm.$t('type-scores-comments')))]),_c('csv-import-info',{attrs:{"import-type":_vm.importType}}),_c('input',{ref:"inputfile",staticClass:"inputfile",attrs:{"type":"file","name":"file","id":"file"},on:{"change":function($event){_vm.filename=$event.target.value.split('\\').pop()}}}),_c('div',{staticClass:"u-flex"},[_c('label',{staticClass:"btn btn-default lbl-input-file u-font-normal",class:{'mod-selected': !!_vm.filename},attrs:{"for":"file","title":_vm.$t('select-file')}},[_c('svg',{attrs:{"xmlns":"http://www.w3.org/2000/svg","width":"20","height":"17","viewBox":"0 0 20 17"}},[_c('path',{attrs:{"d":"M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"}})]),_vm._v(" "),_c('span',[_vm._v(_vm._s(_vm.filename || _vm.$t('select-file')))])]),(_vm.filename)?_c('button',{staticClass:"btn btn-primary",attrs:{"id":"uploadbutton","type":"button","value":"Upload","disabled":_vm.hasError || !_vm.importType},on:{"click":_vm.uploadCSV}},[_vm._v(_vm._s(_vm.$t('upload')))]):_vm._e()])],1):_vm._e(),(_vm.chooseFileActive && _vm.hasError)?_c('div',{staticClass:"import-errors alert alert-danger"},[(_vm.has500Error)?_c('span',{staticClass:"error-filename u-inline-block u-font-medium"},[_vm._v(_vm._s(_vm.filename)+":")]):_vm._e(),_c('div',{staticClass:"errors",class:{'mb-20': _vm.has500Error},domProps:{"innerHTML":_vm._s(_vm.error)}}),(_vm.has500Error)?_c('div',{staticClass:"u-font-medium"},[_vm._v(_vm._s(_vm.$t('correct-mistakes'))+" "),_c('a',{attrs:{"href":"#"},on:{"click":function($event){$event.stopPropagation();return _vm.reload($event)}}},[_vm._v(_vm._s(_vm.$t('reupload-results')))]),_vm._v(".")]):_vm._e()]):_vm._e(),(_vm.previewActive && !_vm.hasError)?[_c('div',{staticClass:"csv-import-info u-flex u-align-items-start"},[_c('p',[_vm._v(_vm._s(_vm.$t('import-results-overview')))]),_c('div',[_c('button',{staticClass:"btn btn-primary",attrs:{"title":_vm.$t('import')},on:{"click":_vm.uploadResults}},[_c('span',{staticClass:"glyphicon glyphicon-arrow-right",attrs:{"aria-hidden":"true"}}),_vm._v(" "+_vm._s(_vm.$t('import')))])])]),_c('imports-table',{attrs:{"fields":_vm.fields,"results":_vm.results}})]:_vm._e(),(_vm.previewActive && _vm.hasError)?_c('div',{staticClass:"import-errors alert alert-danger"},[_c('div',{staticClass:"errors",domProps:{"innerHTML":_vm._s(_vm.error)}})]):_vm._e(),(_vm.importCompleteActive)?[_c('div',{staticClass:"alert alert-info mod-import-completed"},[_c('p',[_vm._v(_vm._s(_vm.$t('import-successful')))]),(_vm.missingUsers.length)?_c('p',{domProps:{"innerHTML":_vm._s(_vm.$t('no-results-some-students'))}}):_vm._e(),_c('p',[_c('a',{staticClass:"u-font-medium",attrs:{"href":_vm.apiConfig.gradeBookRootURL}},[_c('i',{staticClass:"fa fa-arrow-right",attrs:{"aria-hidden":"true"}}),_vm._v(" "+_vm._s(_vm.$t('go-to-gradebook')))])])]),(_vm.missingUsers.length)?_c('p',{staticClass:"gradebook-import-missing-users u-font-medium"},[_vm._v(_vm._s(_vm.$t('without-results'))+":")]):_vm._e(),(_vm.missingUsers.length)?_c('missing-users-table',{attrs:{"missing-users":_vm.missingUsers}}):_vm._e()]:_vm._e()],2),(_vm.debugServerResponse)?_c('div',{attrs:{"id":"server-response"}}):_vm._e()])}
var ImporterAppvue_type_template_id_a97afd16_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/ImporterApp.vue?vue&type=template&id=a97afd16&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ImportsTable.vue?vue&type=template&id=7e31a3f6&scoped=true&
var ImportsTablevue_type_template_id_7e31a3f6_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[(_vm.hasInvalidResults)?_c('ul',{staticClass:"nav mod-imports u-flex u-align-items-baseline",attrs:{"role":"tablist"}},[_c('li',{class:{active: _vm.tab === 'all'},attrs:{"role":"presentation"},on:{"click":function($event){_vm.tab = 'all'}}},[_c('a',{attrs:{"aria-selected":_vm.tab === 'all' ? 'true' : 'false',"aria-controls":"imports-table","role":"tab"}},[_vm._v(_vm._s(_vm.$t('all-imports')))])]),_c('li',{class:{active: _vm.tab === 'valid'},attrs:{"role":"presentation"},on:{"click":function($event){_vm.tab = 'valid'}}},[_c('a',{attrs:{"aria-selected":_vm.tab === 'valid' ? 'true' : 'false',"aria-controls":"imports-table","role":"tab"}},[_vm._v(_vm._s(_vm.$t('valid-imports')))])]),_c('li',{class:{active: _vm.tab === 'invalid'},attrs:{"role":"presentation"},on:{"click":function($event){_vm.tab = 'invalid'}}},[_c('a',{attrs:{"aria-selected":_vm.tab === 'invalid' ? 'true' : 'false',"aria-controls":"imports-table","role":"tab"}},[_vm._v(_vm._s(_vm.$t('not-subscribed'))),_c('span',{staticClass:"badge mod-invalid"},[_vm._v(_vm._s(_vm.invalidResultRows.length))])])])]):_vm._e(),_c('table',{staticClass:"imports-table",attrs:{"id":"imports-table"}},[_c('thead',[_c('tr',{staticClass:"table-row table-head-row"},_vm._l((_vm.fields),function(field){return _c('th',{key:("field-" + (field.key)),staticClass:"table-cell",class:{'mod-score': field.type === 'score'}},[_vm._v(" "+_vm._s(field.label)+" ")])}),0)]),_c('tbody',_vm._l((_vm.filteredResultRows),function(result,row_index){return _c('tr',{key:("result-row-" + row_index),staticClass:"table-row table-body-row",class:{ 'mod-invalid': (_vm.showAll || !_vm.hasInvalidResults) ? !result.valid : _vm.showInvalid}},_vm._l((_vm.fields),function(field,col_index){return _c('td',{key:("result-" + row_index + "-" + col_index),staticClass:"table-cell",class:{'mod-score': field.type === 'score', 'mod-comment': col_index === 4 && field.type === 'string'},attrs:{"title":(!result.valid && field.key === 'id') ? _vm.$t('user-not-in-course') : ((col_index === 4 && field.type === 'string') ? result[field.key] : '')}},[((_vm.showAll || !_vm.hasInvalidResults) && field.key === 'id')?_c('div',{staticClass:"u-flex u-justify-content-between u-align-items-center"},[_vm._v(" "+_vm._s(result[field.key])+" "),_c('i',{staticClass:"fa",class:result.valid ? 'fa-check-circle' : 'fa-exclamation-circle',attrs:{"aria-hidden":"true"}}),(!result.valid)?_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('user-not-in-course')))]):_vm._e()]):(field.type === 'score' && _vm.isNullScore(result[field.key]))?_c('div',{staticClass:"color-code mod-none",attrs:{"title":_vm.$t('no-score-found')}},[_c('span',{staticClass:"sr-only"},[_vm._v(_vm._s(_vm.$t('no-score-found')))])]):(field.type === 'score' && _vm.isAuthAbsentScore(result[field.key]))?_c('div',{staticClass:"color-code amber-700",attrs:{"title":_vm.$t('auth-absent')}},[_c('span',[_vm._v(_vm._s(result[field.key]))])]):[_c('span',[_vm._v(_vm._s(result[field.key]))])]],2)}),0)}),0)])])}
var ImportsTablevue_type_template_id_7e31a3f6_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/ImportsTable.vue?vue&type=template&id=7e31a3f6&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/ImportsTable.vue?vue&type=script&lang=ts&









var ImportsTablevue_type_script_lang_ts_ImportsTable =
/*#__PURE__*/
function (_Vue) {
  _inherits(ImportsTable, _Vue);

  function ImportsTable() {
    var _this;

    _classCallCheck(this, ImportsTable);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ImportsTable).apply(this, arguments));
    _this.tab = 'all';
    _this.pagination = {
      currentPage: 1,
      itemsPerPage: 5
    };
    _this.sortBy = 'lastname';
    _this.sortDesc = false;
    return _this;
  }

  _createClass(ImportsTable, [{
    key: "isNullScore",
    value: function isNullScore(score) {
      return score === null;
    }
  }, {
    key: "isAuthAbsentScore",
    value: function isAuthAbsentScore(score) {
      return typeof score === 'string' && (score.toLowerCase() === 'aabs' || score.toLowerCase() === 'gafw');
    }
  }, {
    key: "showValid",
    get: function get() {
      return this.tab === 'valid';
    }
  }, {
    key: "showInvalid",
    get: function get() {
      return this.tab === 'invalid';
    }
  }, {
    key: "showAll",
    get: function get() {
      return this.tab === 'all';
    }
  }, {
    key: "validResultRows",
    get: function get() {
      return this.results.filter(function (v) {
        return v.valid;
      });
    }
  }, {
    key: "invalidResultRows",
    get: function get() {
      return this.results.filter(function (v) {
        return !v.valid;
      });
    }
  }, {
    key: "hasInvalidResults",
    get: function get() {
      return this.invalidResultRows.length > 0;
    }
  }, {
    key: "filteredResultRows",
    get: function get() {
      if (this.showValid) {
        return this.validResultRows;
      }

      if (this.showInvalid) {
        return this.invalidResultRows;
      }

      return this.results;
    }
  }]);

  return ImportsTable;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], ImportsTablevue_type_script_lang_ts_ImportsTable.prototype, "fields", void 0);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], ImportsTablevue_type_script_lang_ts_ImportsTable.prototype, "results", void 0);

ImportsTablevue_type_script_lang_ts_ImportsTable = __decorate([vue_class_component_esm({})], ImportsTablevue_type_script_lang_ts_ImportsTable);
/* harmony default export */ var ImportsTablevue_type_script_lang_ts_ = (ImportsTablevue_type_script_lang_ts_ImportsTable);
// CONCATENATED MODULE: ./src/components/ImportsTable.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_ImportsTablevue_type_script_lang_ts_ = (ImportsTablevue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/ImportsTable.vue?vue&type=style&index=0&id=7e31a3f6&scoped=true&lang=css&
var ImportsTablevue_type_style_index_0_id_7e31a3f6_scoped_true_lang_css_ = __webpack_require__("c22f");

// EXTERNAL MODULE: ./src/components/ImportsTable.vue?vue&type=custom&index=0&blockType=i18n
var ImportsTablevue_type_custom_index_0_blockType_i18n = __webpack_require__("c085");

// CONCATENATED MODULE: ./src/components/ImportsTable.vue






/* normalize component */

var ImportsTable_component = normalizeComponent(
  components_ImportsTablevue_type_script_lang_ts_,
  ImportsTablevue_type_template_id_7e31a3f6_scoped_true_render,
  ImportsTablevue_type_template_id_7e31a3f6_scoped_true_staticRenderFns,
  false,
  null,
  "7e31a3f6",
  null
  
)

/* custom blocks */

if (typeof ImportsTablevue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(ImportsTablevue_type_custom_index_0_blockType_i18n["default"])(ImportsTable_component)

/* harmony default export */ var components_ImportsTable = (ImportsTable_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/MissingUsersTable.vue?vue&type=template&id=37c72fd4&scoped=true&
var MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('table',{staticClass:"users-table"},[_c('thead',[_c('tr',{staticClass:"table-row table-head-row"},[_c('th',{staticClass:"table-cell"},[_vm._v(_vm._s(_vm.$t('last-name')))]),_c('th',{staticClass:"table-cell"},[_vm._v(_vm._s(_vm.$t('first-name')))]),_c('th',{staticClass:"table-cell"},[_vm._v(_vm._s(_vm.$t('official-code')))])])]),_c('tbody',_vm._l((_vm.missingUsers),function(user,row_index){return _c('tr',{key:("result-row-" + row_index),staticClass:"table-row table-body-row"},[_c('td',{staticClass:"table-cell"},[_vm._v(_vm._s(user.lastname))]),_c('td',{staticClass:"table-cell"},[_vm._v(_vm._s(user.firstname))]),_c('td',{staticClass:"table-cell"},[_vm._v(_vm._s(user.official_code))])])}),0)])}
var MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue?vue&type=template&id=37c72fd4&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/MissingUsersTable.vue?vue&type=script&lang=ts&







var MissingUsersTablevue_type_script_lang_ts_MissingUsersTable =
/*#__PURE__*/
function (_Vue) {
  _inherits(MissingUsersTable, _Vue);

  function MissingUsersTable() {
    _classCallCheck(this, MissingUsersTable);

    return _possibleConstructorReturn(this, _getPrototypeOf(MissingUsersTable).apply(this, arguments));
  }

  return MissingUsersTable;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Array,
  "default": function _default() {
    return [];
  }
})], MissingUsersTablevue_type_script_lang_ts_MissingUsersTable.prototype, "missingUsers", void 0);

MissingUsersTablevue_type_script_lang_ts_MissingUsersTable = __decorate([vue_class_component_esm({})], MissingUsersTablevue_type_script_lang_ts_MissingUsersTable);
/* harmony default export */ var MissingUsersTablevue_type_script_lang_ts_ = (MissingUsersTablevue_type_script_lang_ts_MissingUsersTable);
// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_MissingUsersTablevue_type_script_lang_ts_ = (MissingUsersTablevue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/MissingUsersTable.vue?vue&type=style&index=0&id=37c72fd4&scoped=true&lang=css&
var MissingUsersTablevue_type_style_index_0_id_37c72fd4_scoped_true_lang_css_ = __webpack_require__("cce6");

// EXTERNAL MODULE: ./src/components/MissingUsersTable.vue?vue&type=custom&index=0&blockType=i18n
var MissingUsersTablevue_type_custom_index_0_blockType_i18n = __webpack_require__("c77a");

// CONCATENATED MODULE: ./src/components/MissingUsersTable.vue






/* normalize component */

var MissingUsersTable_component = normalizeComponent(
  components_MissingUsersTablevue_type_script_lang_ts_,
  MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_render,
  MissingUsersTablevue_type_template_id_37c72fd4_scoped_true_staticRenderFns,
  false,
  null,
  "37c72fd4",
  null
  
)

/* custom blocks */

if (typeof MissingUsersTablevue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(MissingUsersTablevue_type_custom_index_0_blockType_i18n["default"])(MissingUsersTable_component)

/* harmony default export */ var components_MissingUsersTable = (MissingUsersTable_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/CSVImportInfo.vue?vue&type=template&id=2ad3ace1&scoped=true&
var CSVImportInfovue_type_template_id_2ad3ace1_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('p',[_vm._v(_vm._s(_vm.$t('csv-must-look-like'))+" ("+_vm._s(_vm.$t('mandatory-fields'))+"):")]),_c('div',{staticClass:"csv-example"},[(_vm.importType === 'scores')?[_c('div',[_c('b',[_vm._v("lastname")]),_vm._v(";"),_c('b',[_vm._v("firstname")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-id"},[_vm._v("id")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-title-1 u-cursor-help",attrs:{"id":"csv-header-title-1"}},[_vm._v(_vm._s(_vm.$t('title'))+" 1")]),_vm._v(";"),_c('span',{staticClass:"csv-field csv-header-title-2 u-cursor-help",attrs:{"id":"csv-header-title-2"}},[_vm._v(_vm._s(_vm.$t('title'))+" 2")]),_vm._v(";…")]),_vm._m(0)]:[_c('div',[_c('b',[_vm._v("lastname")]),_vm._v(";"),_c('b',[_vm._v("firstname")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-id"},[_vm._v("id")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-title-1 u-cursor-help",attrs:{"id":"csv-header-title-1"}},[_vm._v(_vm._s(_vm.$t('title')))]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-header-comment u-cursor-help",attrs:{"id":"csv-header-comment"}},[_vm._v(_vm._s(_vm.$t('comment')))])]),_vm._m(1)]],2),_c('b-popover',{attrs:{"target":"csv-expl-id","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help mod-list"},[_c('div',{staticClass:"u-font-medium",staticStyle:{"color":"#507e86"}},[_vm._v("id")]),_c('div',{domProps:{"innerHTML":_vm._s(_vm.$t('import-id'))}})])]),_c('b-popover',{attrs:{"target":"csv-header-title-1","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('import-score-title'))}})]),_c('b-popover',{attrs:{"target":"csv-expl-title-1","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help mod-list",domProps:{"innerHTML":_vm._s(_vm.$t('import-score'))}})]),_c('b-popover',{attrs:{"target":"csv-header-title-2","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('import-score-title'))}})]),_c('b-popover',{attrs:{"target":"csv-expl-title-2","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help mod-list",domProps:{"innerHTML":_vm._s(_vm.$t('import-score'))}})]),_c('b-popover',{attrs:{"target":"csv-header-comment","triggers":"hover","placement":"bottom"}},[_c('div',{staticClass:"csv-import-help",domProps:{"innerHTML":_vm._s(_vm.$t('import-comment-title'))}})])],1)}
var CSVImportInfovue_type_template_id_2ad3ace1_scoped_true_staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-id u-cursor-help",attrs:{"id":"csv-expl-id"}},[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-title-1 u-cursor-help",attrs:{"id":"csv-expl-title-1"}},[_vm._v("xxx")]),_vm._v(";"),_c('span',{staticClass:"csv-field csv-field-title-2 u-cursor-help",attrs:{"id":"csv-expl-title-2"}},[_vm._v("xxx")]),_vm._v(";…")])},function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-id u-cursor-help",attrs:{"id":"csv-expl-id"}},[_vm._v("xxx")]),_vm._v(";"),_c('b',{staticClass:"csv-field csv-field-title-1 u-cursor-help",attrs:{"id":"csv-expl-title-1"}},[_vm._v("xxx")]),_vm._v(";"),_c('b',[_vm._v("xxx")])])}]


// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue?vue&type=template&id=2ad3ace1&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/CSVImportInfo.vue?vue&type=script&lang=ts&







var CSVImportInfovue_type_script_lang_ts_CsvImportInfo =
/*#__PURE__*/
function (_Vue) {
  _inherits(CsvImportInfo, _Vue);

  function CsvImportInfo() {
    _classCallCheck(this, CsvImportInfo);

    return _possibleConstructorReturn(this, _getPrototypeOf(CsvImportInfo).apply(this, arguments));
  }

  return CsvImportInfo;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: String,
  "default": 'scores'
})], CSVImportInfovue_type_script_lang_ts_CsvImportInfo.prototype, "importType", void 0);

CSVImportInfovue_type_script_lang_ts_CsvImportInfo = __decorate([vue_class_component_esm({})], CSVImportInfovue_type_script_lang_ts_CsvImportInfo);
/* harmony default export */ var CSVImportInfovue_type_script_lang_ts_ = (CSVImportInfovue_type_script_lang_ts_CsvImportInfo);
// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_CSVImportInfovue_type_script_lang_ts_ = (CSVImportInfovue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/CSVImportInfo.vue?vue&type=style&index=0&id=2ad3ace1&scoped=true&lang=css&
var CSVImportInfovue_type_style_index_0_id_2ad3ace1_scoped_true_lang_css_ = __webpack_require__("0c9e");

// EXTERNAL MODULE: ./src/components/CSVImportInfo.vue?vue&type=custom&index=0&blockType=i18n
var CSVImportInfovue_type_custom_index_0_blockType_i18n = __webpack_require__("2ffb");

// CONCATENATED MODULE: ./src/components/CSVImportInfo.vue






/* normalize component */

var CSVImportInfo_component = normalizeComponent(
  components_CSVImportInfovue_type_script_lang_ts_,
  CSVImportInfovue_type_template_id_2ad3ace1_scoped_true_render,
  CSVImportInfovue_type_template_id_2ad3ace1_scoped_true_staticRenderFns,
  false,
  null,
  "2ad3ace1",
  null
  
)

/* custom blocks */

if (typeof CSVImportInfovue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(CSVImportInfovue_type_custom_index_0_blockType_i18n["default"])(CSVImportInfo_component)

/* harmony default export */ var CSVImportInfo = (CSVImportInfo_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/ImporterApp.vue?vue&type=script&lang=ts&



















var ImporterAppvue_type_script_lang_ts_TIMEOUT_SEC = 30;

var ImporterAppvue_type_script_lang_ts_ImporterApp =
/*#__PURE__*/
function (_Vue) {
  _inherits(ImporterApp, _Vue);

  function ImporterApp() {
    var _this;

    _classCallCheck(this, ImporterApp);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(ImporterApp).apply(this, arguments));
    _this.importType = null;
    _this.filename = '';
    _this.hasError = false;
    _this.has500Error = false;
    _this.error = '';
    _this.imported = false;
    _this.missingUsers = [];
    _this.results = [];
    _this.fields = [];
    return _this;
  }

  _createClass(ImporterApp, [{
    key: "setError",
    value: function setError(msg) {
      this.hasError = true;
      this.error = msg;
    }
  }, {
    key: "handleError",
    value: function handleError(err) {
      var _err$message, _err$response, _err$response$data;

      var error;

      if ((err === null || err === void 0 ? void 0 : err.isAxiosError) && ((_err$message = err.message) === null || _err$message === void 0 ? void 0 : _err$message.toLowerCase().indexOf('timeout')) !== -1) {
        error = {
          'type': 'Timeout'
        };
      } else if (err === null || err === void 0 ? void 0 : (_err$response = err.response) === null || _err$response === void 0 ? void 0 : (_err$response$data = _err$response.data) === null || _err$response$data === void 0 ? void 0 : _err$response$data.error) {
        error = err.response.data.error;
      } else if (err === null || err === void 0 ? void 0 : err.type) {
        error = err;
      }

      if (!error.type) {
        error = {
          'type': 'Unknown'
        };
      }

      this.setError("".concat(this.$t('error-' + error.type)));
    }
  }, {
    key: "uploadCSV",
    value: function () {
      var _uploadCSV = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        var fileData, formData, _res$data, _res$data2, _res$data4, res, _res$data3, fields, results;

        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (this.importType) {
                  _context.next = 2;
                  break;
                }

                return _context.abrupt("return");

              case 2:
                fileData = this.inputFile.files[0];
                formData = new FormData();

                if (this.apiConfig.csrfToken) {
                  formData.append('_csrf_token', this.apiConfig.csrfToken);
                }

                formData.append('importType', this.importType);
                formData.append('file', fileData);
                _context.prev = 7;
                _context.next = 10;
                return axios_default.a.post(this.apiConfig.processCsvURL, formData, {
                  timeout: ImporterAppvue_type_script_lang_ts_TIMEOUT_SEC * 1000
                });

              case 10:
                res = _context.sent;
                logResponse(res.data);

                if (!(((_res$data = res.data) === null || _res$data === void 0 ? void 0 : _res$data.fields) !== undefined && ((_res$data2 = res.data) === null || _res$data2 === void 0 ? void 0 : _res$data2.results) !== undefined)) {
                  _context.next = 18;
                  break;
                }

                _res$data3 = res.data, fields = _res$data3.fields, results = _res$data3.results;
                this.fields = fields;
                this.results = results;
                _context.next = 28;
                break;

              case 18:
                if (!(((_res$data4 = res.data) === null || _res$data4 === void 0 ? void 0 : _res$data4.result_code) === 500)) {
                  _context.next = 23;
                  break;
                }

                this.has500Error = true;
                this.setError(res.data.result_message);
                _context.next = 28;
                break;

              case 23:
                if (!(typeof res.data === 'string' && res.data.toLowerCase().indexOf('login') !== -1)) {
                  _context.next = 27;
                  break;
                }

                throw {
                  'type': 'LoggedOut'
                };

              case 27:
                throw {
                  'type': 'Unknown'
                };

              case 28:
                _context.next = 34;
                break;

              case 30:
                _context.prev = 30;
                _context.t0 = _context["catch"](7);
                logResponse(_context.t0);
                this.handleError(_context.t0);

              case 34:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[7, 30]]);
      }));

      function uploadCSV() {
        return _uploadCSV.apply(this, arguments);
      }

      return uploadCSV;
    }()
  }, {
    key: "uploadResults",
    value: function () {
      var _uploadResults = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2() {
        var _this2 = this;

        var formData, scores, _res$data5, _res$data6, res;

        return regeneratorRuntime.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                formData = new FormData();
                formData.set('gradebookDataId', String(this.gradebookDataId));
                formData.set('version', String(this.currentVersion));

                if (this.apiConfig.csrfToken) {
                  formData.append('_csrf_token', this.apiConfig.csrfToken);
                }

                formData.append('importType', this.importType);
                scores = this.importType === 'scores_comments' ? [this.getResultsForField(this.fields[3], this.fields[4])] : this.fields.slice(3).map(function (field) {
                  return _this2.getResultsForField(field);
                });
                formData.set('importScores', JSON.stringify(scores));
                _context2.prev = 7;
                _context2.next = 10;
                return axios_default.a.post(this.apiConfig.importCsvURL, formData, {
                  timeout: ImporterAppvue_type_script_lang_ts_TIMEOUT_SEC * 1000
                });

              case 10:
                res = _context2.sent;
                logResponse(res.data);

                if (!(((_res$data5 = res.data) === null || _res$data5 === void 0 ? void 0 : _res$data5.missing_users) !== undefined)) {
                  _context2.next = 17;
                  break;
                }

                this.missingUsers = res.data.missing_users;
                this.imported = true;
                _context2.next = 27;
                break;

              case 17:
                if (!(((_res$data6 = res.data) === null || _res$data6 === void 0 ? void 0 : _res$data6.result_code) === 500)) {
                  _context2.next = 22;
                  break;
                }

                this.has500Error = true;
                this.setError(res.data.result_message);
                _context2.next = 27;
                break;

              case 22:
                if (!(typeof res.data === 'string' && res.data.toLowerCase().indexOf('login') !== -1)) {
                  _context2.next = 26;
                  break;
                }

                throw {
                  'type': 'LoggedOut'
                };

              case 26:
                throw {
                  'type': 'Unknown'
                };

              case 27:
                _context2.next = 33;
                break;

              case 29:
                _context2.prev = 29;
                _context2.t0 = _context2["catch"](7);
                logResponse(_context2.t0);
                this.handleError(_context2.t0);

              case 33:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this, [[7, 29]]);
      }));

      function uploadResults() {
        return _uploadResults.apply(this, arguments);
      }

      return uploadResults;
    }()
  }, {
    key: "getResultsForField",
    value: function getResultsForField(scoreField) {
      var commentField = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      return {
        label: scoreField.label,
        results: this.results.filter(function (v) {
          return v.valid;
        }).map(function (v) {
          var score = v[scoreField.key];
          var comment = commentField === null ? null : v[commentField.key] || null;
          var authAbsent = typeof score === 'string' && (score.toLowerCase() === 'aabs' || score.toLowerCase() === 'gafw');
          return {
            id: v.user_id,
            score: authAbsent ? null : score,
            authAbsent: authAbsent,
            comment: comment
          };
        })
      };
    }
  }, {
    key: "reload",
    value: function reload() {
      window.location.reload();
    }
  }, {
    key: "chooseTypeActive",
    get: function get() {
      return !this.importType;
    }
  }, {
    key: "chooseFileActive",
    get: function get() {
      return this.importType && !(this.imported || this.resultsLoaded);
    }
  }, {
    key: "previewActive",
    get: function get() {
      return !this.imported && this.resultsLoaded;
    }
  }, {
    key: "importCompleteActive",
    get: function get() {
      return this.imported;
    }
  }, {
    key: "inputFile",
    get: function get() {
      return this.$refs['inputfile'];
    }
  }, {
    key: "resultsLoaded",
    get: function get() {
      return this.fields.length > 0;
    }
  }]);

  return ImporterApp;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object,
  "default": function _default() {
    return null;
  }
})], ImporterAppvue_type_script_lang_ts_ImporterApp.prototype, "apiConfig", void 0);

__decorate([Prop({
  type: Number,
  required: true
})], ImporterAppvue_type_script_lang_ts_ImporterApp.prototype, "gradebookDataId", void 0);

__decorate([Prop({
  type: Number,
  required: true
})], ImporterAppvue_type_script_lang_ts_ImporterApp.prototype, "currentVersion", void 0);

__decorate([Prop({
  type: Boolean,
  "default": false
})], ImporterAppvue_type_script_lang_ts_ImporterApp.prototype, "debugServerResponse", void 0);

ImporterAppvue_type_script_lang_ts_ImporterApp = __decorate([vue_class_component_esm({
  components: {
    ImportsTable: components_ImportsTable,
    MissingUsersTable: components_MissingUsersTable,
    CsvImportInfo: CSVImportInfo
  }
})], ImporterAppvue_type_script_lang_ts_ImporterApp);
/* harmony default export */ var ImporterAppvue_type_script_lang_ts_ = (ImporterAppvue_type_script_lang_ts_ImporterApp);
// CONCATENATED MODULE: ./src/ImporterApp.vue?vue&type=script&lang=ts&
 /* harmony default export */ var src_ImporterAppvue_type_script_lang_ts_ = (ImporterAppvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/ImporterApp.vue?vue&type=style&index=0&id=a97afd16&scoped=true&lang=css&
var ImporterAppvue_type_style_index_0_id_a97afd16_scoped_true_lang_css_ = __webpack_require__("54a8");

// EXTERNAL MODULE: ./src/ImporterApp.vue?vue&type=custom&index=0&blockType=i18n
var ImporterAppvue_type_custom_index_0_blockType_i18n = __webpack_require__("3c2e");

// CONCATENATED MODULE: ./src/ImporterApp.vue






/* normalize component */

var ImporterApp_component = normalizeComponent(
  src_ImporterAppvue_type_script_lang_ts_,
  ImporterAppvue_type_template_id_a97afd16_scoped_true_render,
  ImporterAppvue_type_template_id_a97afd16_scoped_true_staticRenderFns,
  false,
  null,
  "a97afd16",
  null
  
)

/* custom blocks */

if (typeof ImporterAppvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(ImporterAppvue_type_custom_index_0_blockType_i18n["default"])(ImporterApp_component)

/* harmony default export */ var src_ImporterApp = (ImporterApp_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/UserScoresApp.vue?vue&type=template&id=022d871c&scoped=true&
var UserScoresAppvue_type_template_id_022d871c_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return (_vm.gradeBook)?_c('user-scores',{staticClass:"gradebook-user-scores",attrs:{"grade-book":_vm.gradeBook}}):_vm._e()}
var UserScoresAppvue_type_template_id_022d871c_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/UserScoresApp.vue?vue&type=template&id=022d871c&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"0bbff2c8-vue-loader-template"}!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/UserScores.vue?vue&type=template&id=692ab328&scoped=true&
var UserScoresvue_type_template_id_692ab328_scoped_true_render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('b-table-simple',{staticClass:"gradebook-table"},[_c('b-thead',[_c('b-tr',{staticClass:"table-row table-head-row"},[_c('b-th',[_vm._v(_vm._s(_vm.$t('title')))]),_c('b-th',{staticClass:"u-text-end"},[_vm._v(_vm._s(_vm.$t('score')))])],1)],1),_c('b-tbody',[_vm._l((_vm.gradeBook.allCategories),function(category){return [(category.columnIds.length && _vm.gradeBook.allCategories.length && _vm.gradeBook.allCategories[0].id !== 0)?_c('b-tr',{key:("cat-" + (category.id)),staticClass:"table-row table-body-row"},[_c('b-td',{staticClass:"table-category u-font-medium",attrs:{"colspan":"2"}},[_vm._v(_vm._s(category.title))])],1):_vm._e(),_vm._l((_vm.getColumns(category)),function(column){return _c('b-tr',{key:("col-" + (category.id) + "-" + (column.id)),staticClass:"table-row table-body-row result-row",attrs:{"id":("col-" + (category.id) + "-" + (column.id))}},[_c('b-td',{staticClass:"category-color u-relative",style:(("--color: " + (category.color) + ";"))},[_vm._v(_vm._s(column.title))]),_c('b-td',[(column.released)?_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end"},[(column.comment)?_c('i',{staticClass:"fa fa-comment-o",attrs:{"aria-hidden":"true"}}):_vm._e(),_c('student-result',{staticClass:"u-flex u-align-items-center u-justify-content-end",class:{'uncounted-score': !column.countsForEndResult},attrs:{"id":("result-" + (column.id)),"result":column.result}})],1):_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end not-yet-released"},[_vm._v(_vm._s(_vm.$t('not-yet-released')))])]),_c('b-popover',{attrs:{"custom-class":"gradebook-score-popover","target":("col-" + (category.id) + "-" + (column.id)),"triggers":"hover","placement":"rightbottom"}},[_c('div',{staticClass:"score-info"},[(column.countsForEndResult)?_c('div',{staticClass:"u-flex u-align-items-center popover-weight-header"},[_vm._v(_vm._s(_vm.$t('weight'))+": "+_vm._s(_vm._f("formatNum2")(column.weight))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]):_c('div',{staticClass:"popover-count-endresult-not"},[_c('i',[_vm._v(_vm._s(_vm.$t('count-towards-endresult-not')))])]),(column.comment)?[_c('div',{staticClass:"popover-feedback-header"},[_vm._v("Feedback:")]),_vm._v(" "+_vm._s(column.comment)+" ")]:_vm._e()],2)])],1)})]}),(_vm.gradeBook.allCategories.length && _vm.gradeBook.allCategories[0].id !== 0)?_c('b-tr',{staticClass:"table-row table-body-row"},[_c('b-td',{staticClass:"table-empty-cell",attrs:{"colspan":"2"}})],1):_vm._e(),_c('b-tr',{staticClass:"table-row table-body-row"},[_c('b-td',{staticClass:"table-final-score-header"},[_vm._v(_vm._s(_vm.$t('final-score')))]),_c('b-td',{staticClass:"table-final-score u-font-medium"},[(!_vm.gradeBook.hasUnreleasedScores)?_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end"},[_vm._v(_vm._s(_vm._f("formatNum2")(_vm.gradeBook.getEndResult(_vm.userId)))),_c('i',{staticClass:"fa fa-percent",attrs:{"aria-hidden":"true"}}),_c('span',{staticClass:"sr-only"},[_vm._v("%")])]):_c('div',{staticClass:"u-flex u-align-items-center u-justify-content-end not-yet-released"},[_vm._v(_vm._s(_vm.$t('not-yet-released')))])])],1)],2)],1)],1)}
var UserScoresvue_type_template_id_692ab328_scoped_true_staticRenderFns = []


// CONCATENATED MODULE: ./src/components/UserScores.vue?vue&type=template&id=692ab328&scoped=true&

// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/components/UserScores.vue?vue&type=script&lang=ts&











var UserScoresvue_type_script_lang_ts_UserScores =
/*#__PURE__*/
function (_Vue) {
  _inherits(UserScores, _Vue);

  function UserScores() {
    _classCallCheck(this, UserScores);

    return _possibleConstructorReturn(this, _getPrototypeOf(UserScores).apply(this, arguments));
  }

  _createClass(UserScores, [{
    key: "getColumnData",
    value: function getColumnData(columnId) {
      var gradeBook = this.gradeBook;
      var column = gradeBook.getGradeColumn(columnId);

      if (!column) {
        throw new Error("GradeColumn with id ".concat(columnId, " not found."));
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
  }, {
    key: "getColumns",
    value: function getColumns(category) {
      var _this = this;

      return category.columnIds.map(function (columnId) {
        return _this.getColumnData(columnId);
      });
    }
  }, {
    key: "userId",
    get: function get() {
      return this.gradeBook.users[0].id;
    }
  }]);

  return UserScores;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: GradeBook_GradeBook,
  required: true
})], UserScoresvue_type_script_lang_ts_UserScores.prototype, "gradeBook", void 0);

UserScoresvue_type_script_lang_ts_UserScores = __decorate([vue_class_component_esm({
  components: {
    StudentResult: components_StudentResult
  },
  filters: {
    formatNum: function formatNum(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        maximumFractionDigits: 2
      });
    },
    formatNum2: function formatNum2(v) {
      if (v === null) {
        return '';
      }

      return v.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  }
})], UserScoresvue_type_script_lang_ts_UserScores);
/* harmony default export */ var UserScoresvue_type_script_lang_ts_ = (UserScoresvue_type_script_lang_ts_UserScores);
// CONCATENATED MODULE: ./src/components/UserScores.vue?vue&type=script&lang=ts&
 /* harmony default export */ var components_UserScoresvue_type_script_lang_ts_ = (UserScoresvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/components/UserScores.vue?vue&type=style&index=0&id=692ab328&lang=scss&scoped=true&
var UserScoresvue_type_style_index_0_id_692ab328_lang_scss_scoped_true_ = __webpack_require__("682f");

// EXTERNAL MODULE: ./src/components/UserScores.vue?vue&type=style&index=1&lang=css&
var UserScoresvue_type_style_index_1_lang_css_ = __webpack_require__("53da");

// EXTERNAL MODULE: ./src/components/UserScores.vue?vue&type=custom&index=0&blockType=i18n
var UserScoresvue_type_custom_index_0_blockType_i18n = __webpack_require__("993b");

// CONCATENATED MODULE: ./src/components/UserScores.vue







/* normalize component */

var UserScores_component = normalizeComponent(
  components_UserScoresvue_type_script_lang_ts_,
  UserScoresvue_type_template_id_692ab328_scoped_true_render,
  UserScoresvue_type_template_id_692ab328_scoped_true_staticRenderFns,
  false,
  null,
  "692ab328",
  null
  
)

/* custom blocks */

if (typeof UserScoresvue_type_custom_index_0_blockType_i18n["default"] === 'function') Object(UserScoresvue_type_custom_index_0_blockType_i18n["default"])(UserScores_component)

/* harmony default export */ var components_UserScores = (UserScores_component.exports);
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--13-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/thread-loader/dist/cjs.js!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/babel-loader/lib!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/ts-loader??ref--13-3!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/cache-loader/dist/cjs.js??ref--0-0!/Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/vue-loader/lib??vue-loader-options!./src/UserScoresApp.vue?vue&type=script&lang=ts&












var UserScoresAppvue_type_script_lang_ts_UserScoresApp =
/*#__PURE__*/
function (_Vue) {
  _inherits(UserScoresApp, _Vue);

  function UserScoresApp() {
    var _this;

    _classCallCheck(this, UserScoresApp);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(UserScoresApp).apply(this, arguments));
    _this.gradeBook = null;
    return _this;
  }

  _createClass(UserScoresApp, [{
    key: "mounted",
    value: function mounted() {
      this.gradeBook = GradeBook_GradeBook.from(this.gradeBookData);
      var resultsData = {
        'totals': {}
      };
      this.gradeBook.users = this.users;
      this.scores.forEach(function (score) {
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
  }]);

  return UserScoresApp;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

__decorate([Prop({
  type: Object,
  required: true
})], UserScoresAppvue_type_script_lang_ts_UserScoresApp.prototype, "gradeBookData", void 0);

__decorate([Prop({
  type: Array,
  required: true
})], UserScoresAppvue_type_script_lang_ts_UserScoresApp.prototype, "users", void 0);

__decorate([Prop({
  type: Array,
  required: true
})], UserScoresAppvue_type_script_lang_ts_UserScoresApp.prototype, "scores", void 0);

UserScoresAppvue_type_script_lang_ts_UserScoresApp = __decorate([vue_class_component_esm({
  components: {
    UserScores: components_UserScores
  }
})], UserScoresAppvue_type_script_lang_ts_UserScoresApp);
/* harmony default export */ var UserScoresAppvue_type_script_lang_ts_ = (UserScoresAppvue_type_script_lang_ts_UserScoresApp);
// CONCATENATED MODULE: ./src/UserScoresApp.vue?vue&type=script&lang=ts&
 /* harmony default export */ var src_UserScoresAppvue_type_script_lang_ts_ = (UserScoresAppvue_type_script_lang_ts_); 
// EXTERNAL MODULE: ./src/UserScoresApp.vue?vue&type=style&index=0&id=022d871c&scoped=true&lang=css&
var UserScoresAppvue_type_style_index_0_id_022d871c_scoped_true_lang_css_ = __webpack_require__("9b89");

// CONCATENATED MODULE: ./src/UserScoresApp.vue






/* normalize component */

var UserScoresApp_component = normalizeComponent(
  src_UserScoresAppvue_type_script_lang_ts_,
  UserScoresAppvue_type_template_id_022d871c_scoped_true_render,
  UserScoresAppvue_type_template_id_022d871c_scoped_true_staticRenderFns,
  false,
  null,
  "022d871c",
  null
  
)

/* harmony default export */ var src_UserScoresApp = (UserScoresApp_component.exports);
// CONCATENATED MODULE: ./src/plugin.ts



/* harmony default export */ var src_plugin = ({
  install: function install(Vue, options) {
    Vue.component('GradeBookApp', src_App);
    Vue.component('ImporterApp', src_ImporterApp);
    Vue.component('GradeBookUserScoresApp', src_UserScoresApp);
  }
});
// CONCATENATED MODULE: /Users/stefan/dev/vagrantbox/synced_folders/var_www/html/cosnics/node_modules/@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ var entry_lib = __webpack_exports__["default"] = (src_plugin);



/***/ }),

/***/ "cce6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_style_index_0_id_37c72fd4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("072a");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_style_index_0_id_37c72fd4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_style_index_0_id_37c72fd4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MissingUsersTable_vue_vue_type_style_index_0_id_37c72fd4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "d303":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "d44e":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("218a");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_StudentResult_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "da8c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemTitleInput_vue_vue_type_style_index_0_id_97c5d59e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("e2c7");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemTitleInput_vue_vue_type_style_index_0_id_97c5d59e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemTitleInput_vue_vue_type_style_index_0_id_97c5d59e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_6_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_6_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_oneOf_1_2_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemTitleInput_vue_vue_type_style_index_0_id_97c5d59e_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "e2c7":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "e613":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_style_index_0_id_810f4a6c_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("c2d2");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_style_index_0_id_810f4a6c_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_style_index_0_id_810f4a6c_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_dist_cjs_js_ref_8_oneOf_1_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_dist_cjs_js_ref_8_oneOf_1_3_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CategorySettings_vue_vue_type_style_index_0_id_810f4a6c_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "eade":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("ba2e");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ScoreInput_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

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

/***/ "efa9":
/***/ (function(module, exports, __webpack_require__) {

var fails = __webpack_require__("7104");

module.exports = !fails(function () {
  function F() { /* empty */ }
  F.prototype.constructor = null;
  return Object.getPrototypeOf(new F()) !== F.prototype;
});


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

/***/ "f315":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("76cd");
/* harmony import */ var _node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0__);
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_kazupon_vue_i18n_loader_lib_index_js_node_modules_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_lib_index_js_vue_loader_options_GradesTable_vue_vue_type_custom_index_0_blockType_i18n__WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "f347":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var fixRegExpWellKnownSymbolLogic = __webpack_require__("b6bc");
var isRegExp = __webpack_require__("7350");
var anObject = __webpack_require__("6161");
var requireObjectCoercible = __webpack_require__("b2c6");
var speciesConstructor = __webpack_require__("bbec");
var advanceStringIndex = __webpack_require__("2690");
var toLength = __webpack_require__("7cf1");
var callRegExpExec = __webpack_require__("4bc5");
var regexpExec = __webpack_require__("9a1c");
var fails = __webpack_require__("7104");

var arrayPush = [].push;
var min = Math.min;
var MAX_UINT32 = 0xFFFFFFFF;

// babel-minify transpiles RegExp('x', 'y') -> /x/y and it causes SyntaxError
var SUPPORTS_Y = !fails(function () { return !RegExp(MAX_UINT32, 'y'); });

// @@split logic
fixRegExpWellKnownSymbolLogic('split', 2, function (SPLIT, nativeSplit, maybeCallNative) {
  var internalSplit;
  if (
    'abbc'.split(/(b)*/)[1] == 'c' ||
    'test'.split(/(?:)/, -1).length != 4 ||
    'ab'.split(/(?:ab)*/).length != 2 ||
    '.'.split(/(.?)(.?)/).length != 4 ||
    '.'.split(/()()/).length > 1 ||
    ''.split(/.?/).length
  ) {
    // based on es5-shim implementation, need to rework it
    internalSplit = function (separator, limit) {
      var string = String(requireObjectCoercible(this));
      var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
      if (lim === 0) return [];
      if (separator === undefined) return [string];
      // If `separator` is not a regex, use native split
      if (!isRegExp(separator)) {
        return nativeSplit.call(string, separator, lim);
      }
      var output = [];
      var flags = (separator.ignoreCase ? 'i' : '') +
                  (separator.multiline ? 'm' : '') +
                  (separator.unicode ? 'u' : '') +
                  (separator.sticky ? 'y' : '');
      var lastLastIndex = 0;
      // Make `global` and avoid `lastIndex` issues by working with a copy
      var separatorCopy = new RegExp(separator.source, flags + 'g');
      var match, lastIndex, lastLength;
      while (match = regexpExec.call(separatorCopy, string)) {
        lastIndex = separatorCopy.lastIndex;
        if (lastIndex > lastLastIndex) {
          output.push(string.slice(lastLastIndex, match.index));
          if (match.length > 1 && match.index < string.length) arrayPush.apply(output, match.slice(1));
          lastLength = match[0].length;
          lastLastIndex = lastIndex;
          if (output.length >= lim) break;
        }
        if (separatorCopy.lastIndex === match.index) separatorCopy.lastIndex++; // Avoid an infinite loop
      }
      if (lastLastIndex === string.length) {
        if (lastLength || !separatorCopy.test('')) output.push('');
      } else output.push(string.slice(lastLastIndex));
      return output.length > lim ? output.slice(0, lim) : output;
    };
  // Chakra, V8
  } else if ('0'.split(undefined, 0).length) {
    internalSplit = function (separator, limit) {
      return separator === undefined && limit === 0 ? [] : nativeSplit.call(this, separator, limit);
    };
  } else internalSplit = nativeSplit;

  return [
    // `String.prototype.split` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.split
    function split(separator, limit) {
      var O = requireObjectCoercible(this);
      var splitter = separator == undefined ? undefined : separator[SPLIT];
      return splitter !== undefined
        ? splitter.call(separator, O, limit)
        : internalSplit.call(String(O), separator, limit);
    },
    // `RegExp.prototype[@@split]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@split
    //
    // NOTE: This cannot be properly polyfilled in engines that don't support
    // the 'y' flag.
    function (regexp, limit) {
      var res = maybeCallNative(internalSplit, regexp, this, limit, internalSplit !== nativeSplit);
      if (res.done) return res.value;

      var rx = anObject(regexp);
      var S = String(this);
      var C = speciesConstructor(rx, RegExp);

      var unicodeMatching = rx.unicode;
      var flags = (rx.ignoreCase ? 'i' : '') +
                  (rx.multiline ? 'm' : '') +
                  (rx.unicode ? 'u' : '') +
                  (SUPPORTS_Y ? 'y' : 'g');

      // ^(? + rx + ) is needed, in combination with some S slicing, to
      // simulate the 'y' flag.
      var splitter = new C(SUPPORTS_Y ? rx : '^(?:' + rx.source + ')', flags);
      var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
      if (lim === 0) return [];
      if (S.length === 0) return callRegExpExec(splitter, S) === null ? [S] : [];
      var p = 0;
      var q = 0;
      var A = [];
      while (q < S.length) {
        splitter.lastIndex = SUPPORTS_Y ? q : 0;
        var z = callRegExpExec(splitter, SUPPORTS_Y ? S : S.slice(q));
        var e;
        if (
          z === null ||
          (e = min(toLength(splitter.lastIndex + (SUPPORTS_Y ? 0 : q)), S.length)) === p
        ) {
          q = advanceStringIndex(S, q, unicodeMatching);
        } else {
          A.push(S.slice(p, q));
          if (A.length === lim) return A;
          for (var i = 1; i <= z.length - 1; i++) {
            A.push(z[i]);
            if (A.length === lim) return A;
          }
          q = p = e;
        }
      }
      A.push(S.slice(p));
      return A;
    }
  ];
}, !SUPPORTS_Y);


/***/ }),

/***/ "f52b":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "faa7":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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

/***/ "fe61":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })

/******/ });
//# sourceMappingURL=cosnics-gradebook.common.js.map