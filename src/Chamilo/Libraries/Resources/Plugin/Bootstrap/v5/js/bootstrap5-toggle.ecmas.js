/* Copyright Notice
 * bootstrap5-toggle v5.2.0
 * https://palcarazm.github.io/bootstrap5-toggle/
 * @author 2011-2014 Min Hur (https://github.com/minhur)
 * @author 2018-2019 Brent Ely (https://github.com/gitbrent)
 * @author 2022 Pablo Alcaraz Martínez (https://github.com/palcarazm)
 * @funding GitHub Sponsors
 * @see https://github.com/sponsors/palcarazm
 * @license MIT
 * @see https://github.com/palcarazm/bootstrap5-toggle/blob/master/LICENSE
 */

(function (factory) {
    typeof define === 'function' && define.amd ? define(factory) :
    factory();
})((function () { 'use strict';

    var ToggleStateValue;
    (function (ToggleStateValue) {
        ToggleStateValue["ON"] = "on";
        ToggleStateValue["OFF"] = "off";
        ToggleStateValue["INDETERMINATE"] = "indeterminate";
    })(ToggleStateValue || (ToggleStateValue = {}));
    var ToggleStateStatus;
    (function (ToggleStateStatus) {
        ToggleStateStatus["ENABLED"] = "enabled";
        ToggleStateStatus["DISABLED"] = "disabled";
        ToggleStateStatus["READONLY"] = "readonly";
    })(ToggleStateStatus || (ToggleStateStatus = {}));
    var ToggleActionType;
    (function (ToggleActionType) {
        ToggleActionType["NEXT"] = "next";
        ToggleActionType["ON"] = "on";
        ToggleActionType["OFF"] = "off";
        ToggleActionType["TOGGLE"] = "toggle";
        ToggleActionType["DETERMINATE"] = "determinate";
        ToggleActionType["INDETERMINATE"] = "indeterminate";
        ToggleActionType["READONLY"] = "readonly";
        ToggleActionType["DISABLE"] = "disable";
        ToggleActionType["ENABLE"] = "enable";
    })(ToggleActionType || (ToggleActionType = {}));

    var DOMBuilder = /** @class */ (function () {
        /**
       * Initializes a new instance of the DOMBuilder class.
       * This renders the toggle if the parent element is visible, otherwise defers rendering until it becomes visible.
       * @param checkbox HTMLInputElement element representing the toggle.
       * @param options ToggleOptions object containing options for the toggle.
       * @param state ToggleState object containing the initial state of the toggle.
       */
        function DOMBuilder(checkbox, options, state) {
            this.isBuilt = false;
            this.lastState = state;
            this.onStyle = "btn-".concat(options.onstyle);
            this.offStyle = "btn-".concat(options.offstyle);
            this.name = options.name;
            this.checkbox = checkbox;
            if (options.onvalue)
                this.checkbox.value = options.onvalue;
            this.invCheckbox = options.offvalue
                ? this.createInvCheckbox(options.offvalue)
                : null;
            this.sizeClass = DOMBuilder.sizeResolver(options.size);
            this.toggleOn = this.createToggleSpan(options.onlabel, this.onStyle, options.ontitle);
            this.toggleOff = this.createToggleSpan(options.offlabel, this.offStyle, options.offtitle);
            this.toggleHandle = this.createToggleHandle();
            this.toggleGroup = this.createToggleGroup();
            this.toggle = document.createElement("div");
            if (this.isVisible()) {
                this.renderToggle(options);
                this.render(state);
            }
            else {
                this.deferRender(options);
            }
        }
        /**
       * Checks if the parent element of the checkbox is visible.
       * A parent element is considered visible if its `offsetWidth` and `offsetHeight` are greater than `0`.
       * @returns boolean indicating whether the parent element is visible or not.
       */
        DOMBuilder.prototype.isVisible = function () {
            var parent = this.checkbox.parentElement;
            return !!parent && parent.offsetWidth > 0 && parent.offsetHeight > 0;
        };
        /**
       * Defer rendering the toggle until the parent element is visible.
       * It does this by observing the parent element's bounding rectangle and only rendering the toggle once the width and height of the bounding rectangle are greater than 0.
       * @param options ToggleOptions object containing options for the toggle.
       */
        DOMBuilder.prototype.deferRender = function (options) {
            var _this = this;
            this.resizeObserver = new ResizeObserver(function (entries) {
                if (_this.isBuilt) {
                    _this.resizeObserver.disconnect();
                    return;
                }
                for (var _i = 0, entries_1 = entries; _i < entries_1.length; _i++) {
                    var entry = entries_1[_i];
                    if (entry.contentRect.width > 0 && entry.contentRect.height > 0) {
                        _this.renderToggle(options);
                        _this.render(_this.lastState);
                        _this.isBuilt = true;
                        _this.resizeObserver.disconnect();
                        return;
                    }
                }
            });
            this.resizeObserver.observe(this.checkbox.parentElement);
        };
        /**
       * Resolves the size class for the toggle based on the provided size.
       * If size is not provided or is invalid, returns an empty string.
       * @param size ToggleSize value representing the size of the toggle.
       * @returns string representing the size class for the toggle.
       */
        DOMBuilder.sizeResolver = function (size) {
            var _a;
            var sizeMap = {
                large: "btn-lg",
                lg: "btn-lg",
                small: "btn-sm",
                sm: "btn-sm",
                mini: "btn-xs",
                xs: "btn-xs",
            };
            return (_a = sizeMap[size]) !== null && _a !== void 0 ? _a : "";
        };
        /**
       * Creates an inverted checkbox element that is used in the toggle.
       * This checkbox is used to create the toggle's "off" state.
       * @param offValue The value of the checkbox when the toggle is in the "off" state.
       * @returns An HTMLInputElement representing the inverted checkbox element.
       */
        DOMBuilder.prototype.createInvCheckbox = function (offValue) {
            var invCheckbox = this.checkbox.cloneNode(true);
            invCheckbox.value = offValue;
            invCheckbox.dataset.toggle = "invert-toggle";
            invCheckbox.removeAttribute("id");
            return invCheckbox;
        };
        /**
       * Renders the toggle element and its children.
       * Sets the class attribute of the toggle with the provided style and size class.
       * Sets the tabindex attribute of the toggle with the provided tabindex.
       * Inserts the toggle element before the original checkbox element.
       * Appends the checkbox, inverted checkbox (if exists) and toggle group elements to the toggle element.
       * Handles the toggle size by setting the width and height attributes of the toggle element.
       * @param options - ToggleOptions object containing the style, width, height and tabindex for the toggle.
       */
        DOMBuilder.prototype.renderToggle = function (_a) {
            var _b;
            var style = _a.style, width = _a.width, height = _a.height, tabindex = _a.tabindex;
            this.toggle.className = "toggle btn ".concat(this.sizeClass, " ").concat(style);
            this.toggle.dataset.toggle = "toggle";
            this.toggle.tabIndex = tabindex;
            this.toggle.role = "button";
            (_b = this.checkbox.parentElement) === null || _b === void 0 ? void 0 : _b.insertBefore(this.toggle, this.checkbox);
            this.toggle.appendChild(this.checkbox);
            if (this.invCheckbox)
                this.toggle.appendChild(this.invCheckbox);
            this.toggle.appendChild(this.toggleGroup);
            this.handleToggleSize(width, height);
            this.isBuilt = true;
        };
        /**
       * Creates a div element representing the toggle group.
       * The toggle group contains the on, off, and handle elements of the toggle.
       * @returns An HTMLElement representing the toggle group element.
       */
        DOMBuilder.prototype.createToggleGroup = function () {
            var toggleGroup = document.createElement("div");
            toggleGroup.className = "toggle-group";
            toggleGroup.appendChild(this.toggleOn);
            toggleGroup.appendChild(this.toggleOff);
            toggleGroup.appendChild(this.toggleHandle);
            return toggleGroup;
        };
        /**
       * Creates a span element representing a toggle option (on/off).
       * The span element is given a class attribute with the provided style and size class.
       * The innerHTML of the span element is set to the provided label.
       * If a title is provided, the span element is given a title attribute with the provided title.
       * @param label The text to be displayed in the toggle option.
       * @param style The style of the toggle option (primary, secondary, etc.).
       * @param title The title of the toggle option.
       * @returns An HTMLElement representing the toggle option element.
       */
        DOMBuilder.prototype.createToggleSpan = function (label, style, title) {
            var toggleSpan = document.createElement("span");
            toggleSpan.className = "btn ".concat(this.sizeClass, " ").concat(style);
            toggleSpan.innerHTML = label;
            if (title)
                toggleSpan.title = title;
            return toggleSpan;
        };
        /**
       * Creates a span element representing the toggle handle.
       * The span element is given a class attribute with the provided size class.
       * @returns An HTMLElement representing the toggle handle element.
       */
        DOMBuilder.prototype.createToggleHandle = function () {
            var toggleHandle = document.createElement("span");
            toggleHandle.className = "toggle-handle btn ".concat(this.sizeClass);
            return toggleHandle;
        };
        /**
       * Sets the width and height of the toggle element.
       * If a width or height is not provided, the toggle element will be given a minimum width and height
       * that is calculated based on the size of the toggle on and off options.
       * @param width The width of the toggle element.
       * @param height The height of the toggle element.
       */
        DOMBuilder.prototype.handleToggleSize = function (width, height) {
            if (width) {
                this.toggle.style.width = width;
            }
            else {
                this.toggle.style.minWidth = "100px"; // First approach for better calculation
                this.toggle.style.minWidth = "".concat(Math.max(this.toggleOn.getBoundingClientRect().width, this.toggleOff.getBoundingClientRect().width) +
                    this.toggleHandle.getBoundingClientRect().width / 2, "px");
            }
            if (height) {
                this.toggle.style.height = height;
            }
            else {
                this.toggle.style.minHeight = "36px"; // First approach for better calculation
                this.toggle.style.minHeight = "".concat(Math.max(this.toggleOn.getBoundingClientRect().height, this.toggleOff.getBoundingClientRect().height), "px");
            }
            // B: Apply on/off class
            this.toggleOn.classList.add("toggle-on");
            this.toggleOff.classList.add("toggle-off");
            // C: Finally, set lineHeight if needed
            if (height) {
                this.toggleOn.style.lineHeight = DOMBuilder.calcH(this.toggleOn) + "px";
                this.toggleOff.style.lineHeight = DOMBuilder.calcH(this.toggleOff) + "px";
            }
        };
        /**
         * Calculates the height of the toggle element that should be used for the line-height property.
         * This calculation is used when the toggle element is given a height that is not explicitly set.
         * The calculation takes into account the height of the toggle element, the border-top and border-bottom widths,
         * and the padding-top and padding-bottom of the toggle element.
         * @param toggleSpan The HTMLElement that represents the toggle element.
         * @returns The height of the toggle element that should be used for the line-height property.
         */
        DOMBuilder.calcH = function (toggleSpan) {
            var styles = globalThis.window.getComputedStyle(toggleSpan);
            var height = toggleSpan.offsetHeight;
            var borderTopWidth = Number.parseFloat(styles.borderTopWidth);
            var borderBottomWidth = Number.parseFloat(styles.borderBottomWidth);
            var paddingTop = Number.parseFloat(styles.paddingTop);
            var paddingBottom = Number.parseFloat(styles.paddingBottom);
            return (height - borderBottomWidth - borderTopWidth - paddingTop - paddingBottom);
        };
        /**
       * Renders the toggle element based on the provided state if the toggle is already built.
       * This method should be called whenever the state of the toggle changes.
       * @param {ToggleState} state The state of the toggle element.
       */
        DOMBuilder.prototype.render = function (state) {
            this.lastState = state;
            if (!this.isBuilt)
                return;
            this.updateToggleByValue(state);
            this.updateToggleByChecked(state);
            this.updateToggleByState(state);
        };
        /*************  ✨ Windsurf Command ⭐  *************/
        /**
         * Updates the class of the toggle element based on the provided state.
         * Removes any existing on/off/indeterminate classes and adds the appropriate class based on the state.
         * If the state is indeterminate, adds the 'indeterminate' class and either the on or off class based on the checked attribute.
         * @param {ToggleState} state The state of the toggle element.
         */
        /*******  9e620de0-7e60-44a0-b26d-be36099794af  *******/
        DOMBuilder.prototype.updateToggleByValue = function (state) {
            this.toggle.classList.remove(this.onStyle, this.offStyle, "off", "indeterminate");
            switch (state.value) {
                case ToggleStateValue.ON:
                    this.toggle.classList.add(this.onStyle);
                    break;
                case ToggleStateValue.OFF:
                    this.toggle.classList.add(this.offStyle, "off");
                    break;
                case ToggleStateValue.INDETERMINATE:
                    this.toggle.classList.add("indeterminate");
                    if (state.checked) {
                        this.toggle.classList.add(this.onStyle);
                    }
                    else {
                        this.toggle.classList.add(this.offStyle, "off");
                    }
                    break;
            }
        };
        /**
         * Updates the toggle element based on the provided state.
         * Calls {@link DOMBuilder.updateCheckboxByChecked} and {@link DOMBuilder.updateInvCheckboxByChecked} to update the checkbox and inverted checkbox elements respectively.
         * @param {ToggleState} state The state of the toggle element.
         */
        DOMBuilder.prototype.updateToggleByChecked = function (state) {
            this.updateCheckboxByChecked(state);
            this.updateInvCheckboxByChecked(state);
        };
        /**
         * Updates the checkbox element based on the provided state.
         * Sets the checked attribute of the checkbox based on the state's checked attribute.
         * Sets the disabled and readonly attributes of the checkbox based on the state's status.
         * Adds or removes the 'disabled' class from the toggle element based on the state's status.
         * @param {ToggleState} state The state of the toggle element.
         */
        DOMBuilder.prototype.updateCheckboxByChecked = function (state) {
            this.checkbox.checked = state.checked;
            switch (state.status) {
                case ToggleStateStatus.ENABLED:
                    this.checkbox.disabled = false;
                    this.checkbox.readOnly = false;
                    this.toggle.classList.remove("disabled");
                    this.toggle.removeAttribute("disabled");
                    break;
                case ToggleStateStatus.DISABLED:
                    this.checkbox.disabled = true;
                    this.checkbox.readOnly = false;
                    this.toggle.classList.add("disabled");
                    this.toggle.setAttribute("disabled", "");
                    break;
                case ToggleStateStatus.READONLY:
                    this.checkbox.disabled = false;
                    this.checkbox.readOnly = true;
                    this.toggle.classList.add("disabled");
                    this.toggle.setAttribute("disabled", "");
                    break;
            }
        };
        /**
         * Updates the inverted checkbox element based on the provided state.
         * Sets the checked attribute of the inverted checkbox to the opposite of the state's checked attribute.
         * Sets the disabled and readonly attributes of the inverted checkbox based on the state's status.
         * @param {ToggleState} state The state of the toggle element.
         */
        DOMBuilder.prototype.updateInvCheckboxByChecked = function (state) {
            if (!this.invCheckbox)
                return;
            this.invCheckbox.checked = !state.checked;
            switch (state.status) {
                case ToggleStateStatus.ENABLED:
                    this.invCheckbox.disabled = false;
                    this.invCheckbox.readOnly = false;
                    break;
                case ToggleStateStatus.DISABLED:
                    this.invCheckbox.disabled = true;
                    this.invCheckbox.readOnly = false;
                    break;
                case ToggleStateStatus.READONLY:
                    this.invCheckbox.disabled = false;
                    this.invCheckbox.readOnly = true;
                    break;
            }
        };
        /**
         * Updates the indeterminate attribute of the checkbox and inverted checkbox elements based on the provided state.
         * If the state is indeterminate, sets the indeterminate attribute of the checkbox and inverted checkbox to true and removes the name attribute.
         * If the state is not indeterminate, sets the indeterminate attribute of the checkbox and inverted checkbox to false and sets the name attribute to the provided name.
         * @param {ToggleState} state The state of the toggle element.
         */
        DOMBuilder.prototype.updateToggleByState = function (state) {
            if (state.indeterminate) {
                this.checkbox.indeterminate = true;
                this.checkbox.removeAttribute("name");
                if (this.invCheckbox)
                    this.invCheckbox.indeterminate = true;
                if (this.invCheckbox)
                    this.invCheckbox.removeAttribute("name");
            }
            else {
                this.checkbox.indeterminate = false;
                if (this.name)
                    this.checkbox.name = this.name;
                if (this.invCheckbox)
                    this.invCheckbox.indeterminate = false;
                if (this.invCheckbox && this.name)
                    this.invCheckbox.name = this.name;
            }
        };
        Object.defineProperty(DOMBuilder.prototype, "root", {
            /**
           * Returns the root element of the toggle, which is the container of all toggle elements.
           * @returns {HTMLElement} The root element of the toggle.
           */
            get: function () {
                return this.toggle;
            },
            enumerable: false,
            configurable: true
        });
        /**
       * Destroys the toggle by removing the toggle element from the DOM and
       * inserting the original checkbox element back into its original position.
       * Also disconnects the ResizeObserver if it was used.
       */
        DOMBuilder.prototype.destroy = function () {
            var _a, _b;
            (_a = this.toggle.parentNode) === null || _a === void 0 ? void 0 : _a.insertBefore(this.checkbox, this.toggle);
            this.toggle.remove();
            (_b = this.resizeObserver) === null || _b === void 0 ? void 0 : _b.disconnect();
            this.resizeObserver = undefined;
            this.isBuilt = false;
        };
        return DOMBuilder;
    }());

    function sanitize(text) {
        if (!text)
            return text;
        var map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;",
            "/": "&#x2F;"
        };
        // Using replace with regex for single-pass character mapping compatible with ES5
        return text.replace(/[&<>"'/]/g, function (m) { return map[m]; });
    }
    /**
     * Checks if the given string is a valid numeric value.
     *
     * A valid numeric value is a `string` that starts with an optional plus or minus sign,
     * followed by one or more digits, optionally followed by a decimal point and
     * one or more digits.
     *
     * Examples of valid numeric values include "123", "-123", "+123.45", "-123.45", etc.
     * Examples of invalid numeric values include "abc", "123abc", "123.abc", etc.
     * @param {string | number} value The string or number to check for being a valid numeric value.
     * @returns {boolean} `true` if the string contains a valid numeric value, `false` otherwise.
     */
    function isNumeric(value) {
        return /^[+-]?\d+(\.\d+)?$/.test(value.toString().trim());
    }

    /**
     * OptionResolver is responsible for reading HTML attributes and user options
     * to build a complete ToggleOptions object.
     * It also handles deprecated options.
     */
    var OptionResolver = /** @class */ (function () {
        function OptionResolver() {
        }
        /**
       * Gets a sanitized attribute value from an HTML element
       * @param element HTMLInputElement to read
       * @param attrName Attribute name
       * @param options method options
       * @param options.sanitized Flag to indicate if the attribute value needs to be sanitized (default: `true`)
       * @returns Sanitized attribute value or null
       */
        OptionResolver.getAttr = function (element, attrName, opts) {
            var _a = (opts !== null && opts !== void 0 ? opts : {}).sanitized, sanitized = _a === void 0 ? true : _a;
            var value = element.getAttribute(attrName);
            return sanitized ? sanitize(value) : value;
        };
        /**
       * Returns the value of an attribute, user-provided value, or default value
       * @param element HTMLInputElement to read
       * @param attrName Attribute name
       * @param userValue Value provided by the user
       * @param defaultValue Default value if neither attribute nor user value exists
       * @param sanitized Flag to indicate if the attribute value needs to be sanitized (default: {@code true})
       * @returns Final attribute value
       */
        OptionResolver.getAttrOrDefault = function (element, attrName, userValue, defaultValue, sanitized) {
            if (sanitized === void 0) { sanitized = true; }
            return OptionResolver.getAttr(element, attrName, { sanitized: sanitized }) || userValue || defaultValue;
        };
        /**
       * Returns the value of an attribute, user-provided value, or marks as deprecated
       * @param element HTMLInputElement to read
       * @param attrName Attribute name
       * @param userValue Value provided by the user
       * @param sanitized Flag to indicate if the attribute value needs to be sanitized (default: {@code true})
       * @returns Final attribute value or DeprecationConfig.value if not found
       */
        OptionResolver.getAttrOrDeprecation = function (element, attrName, userValue, sanitized) {
            if (sanitized === void 0) { sanitized = true; }
            return OptionResolver.getAttr(element, attrName, { sanitized: sanitized }) ||
                userValue ||
                DeprecationConfig.value;
        };
        /**
       * Resolves all toggle options from the element and user options
       * @param element HTMLInputElement representing the toggle
       * @param userOptions Options provided by the user
       * @returns Complete ToggleOptions object
       */
        OptionResolver.resolve = function (element, userOptions) {
            if (userOptions === void 0) { userOptions = {}; }
            var options = {
                onlabel: this.getAttrOrDeprecation(element, "data-onlabel", userOptions.onlabel, false),
                offlabel: this.getAttrOrDeprecation(element, "data-offlabel", userOptions.offlabel, false),
                onstyle: this.getAttrOrDefault(element, "data-onstyle", userOptions.onstyle, OptionResolver.DEFAULT.onstyle),
                offstyle: this.getAttrOrDefault(element, "data-offstyle", userOptions.offstyle, OptionResolver.DEFAULT.offstyle),
                onvalue: this.getAttr(element, "value") || this.getAttrOrDefault(element, "data-onvalue", userOptions.onvalue, OptionResolver.DEFAULT.onvalue),
                offvalue: this.getAttrOrDefault(element, "data-offvalue", userOptions.offvalue, OptionResolver.DEFAULT.offvalue),
                ontitle: this.getAttrOrDefault(element, "data-ontitle", userOptions.ontitle, OptionResolver.getAttr(element, "title") ||
                    OptionResolver.DEFAULT.ontitle),
                offtitle: this.getAttrOrDefault(element, "data-offtitle", userOptions.offtitle, OptionResolver.getAttr(element, "title") ||
                    OptionResolver.DEFAULT.offtitle),
                size: this.getAttrOrDefault(element, "data-size", userOptions.size, this.DEFAULT.size),
                style: this.getAttrOrDefault(element, "data-style", userOptions.style, this.DEFAULT.style),
                width: this.getAttrOrDefault(element, "data-width", userOptions.width, this.DEFAULT.width),
                height: this.getAttrOrDefault(element, "data-height", userOptions.height, this.DEFAULT.height),
                tabindex: Number(this.getAttrOrDefault(element, "tabindex", userOptions.tabindex, this.DEFAULT.tabindex)),
                tristate: element.hasAttribute("tristate") ||
                    userOptions.tristate ||
                    OptionResolver.DEFAULT.tristate,
                name: this.getAttrOrDefault(element, "name", userOptions.name, this.DEFAULT.name),
            };
            if (options.width && isNumeric(options.width))
                options.width = "".concat(options.width, "px");
            if (options.height && isNumeric(options.height))
                options.height = "".concat(options.height, "px");
            DeprecationConfig.handle(options, element, userOptions);
            return options;
        };
        /** Default values for all toggle options */
        OptionResolver.DEFAULT = {
            onlabel: "On",
            onstyle: "primary",
            onvalue: null,
            ontitle: null,
            offlabel: "Off",
            offstyle: "secondary",
            offvalue: null,
            offtitle: null,
            size: "",
            style: "",
            width: null,
            height: null,
            tabindex: 0,
            tristate: false,
            name: null,
        };
        return OptionResolver;
    }());
    /** Types of deprecation source */
    var OptionType;
    (function (OptionType) {
        OptionType["ATTRIBUTE"] = "attribute";
        OptionType["OPTION"] = "option";
    })(OptionType || (OptionType = {}));
    /**
     * Handles deprecated attributes and options for Bootstrap Toggle.
     */
    var DeprecationConfig = /** @class */ (function () {
        function DeprecationConfig() {
        }
        /**
       * Processes deprecated options and attributes and logs warnings
       * @param options ToggleOptions object to update
       * @param element HTMLInputElement to read deprecated attributes from
       * @param userOptions UserOptions provided by the user
       */
        DeprecationConfig.handle = function (options, element, userOptions) {
            var _this = this;
            this.deprecatedOptions.forEach(function (_a) {
                var currentOpt = _a.currentOpt, deprecatedAttr = _a.deprecatedAttr, deprecatedOpt = _a.deprecatedOpt;
                if (options[currentOpt] === DeprecationConfig.value) {
                    var deprecatedAttrSanitized = sanitize(element.getAttribute(deprecatedAttr));
                    if (deprecatedAttrSanitized) {
                        _this.log(OptionType.ATTRIBUTE, deprecatedAttr, "data-".concat(currentOpt));
                        options[currentOpt] = deprecatedAttrSanitized;
                    }
                    else if (userOptions[deprecatedOpt]) {
                        _this.log(OptionType.OPTION, deprecatedOpt, currentOpt);
                        options[currentOpt] = userOptions[deprecatedOpt];
                    }
                    else {
                        options[currentOpt] = OptionResolver.DEFAULT[currentOpt];
                    }
                }
            });
        };
        /**
       * Logs a deprecation warning to the console
       * @param type Source of the deprecated option (ATTRIBUTE | OPTION)
       * @param oldLabel Deprecated attribute or option name
       * @param newLabel Recommended replacement option name
       */
        DeprecationConfig.log = function (type, oldLabel, newLabel) {
            console.warn("Bootstrap Toggle deprecation warning: Using ".concat(oldLabel, " ").concat(type, " is deprecated. Use ").concat(newLabel, " instead."));
        };
        /** Unique string used to detect deprecated placeholders */
        DeprecationConfig.value = "BOOTSTRAP TOGGLE DEPRECATION CHECK -- a0Jhux0QySypjjs4tLtEo8xT2kx0AbYaq9K6mgNjWSs0HF0L8T8J0M0o3Kr7zkm7 --";
        /** Mapping of current option, deprecated attribute, and deprecated user option */
        DeprecationConfig.deprecatedOptions = [
            {
                currentOpt: "onlabel",
                deprecatedAttr: "data-on",
                deprecatedOpt: "on",
            },
            {
                currentOpt: "offlabel",
                deprecatedAttr: "data-off",
                deprecatedOpt: "off",
            },
        ];
        return DeprecationConfig;
    }());

    var __assign = (undefined && undefined.__assign) || function () {
        __assign = Object.assign || function(t) {
            for (var s, i = 1, n = arguments.length; i < n; i++) {
                s = arguments[i];
                for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                    t[p] = s[p];
            }
            return t;
        };
        return __assign.apply(this, arguments);
    };
    var StateReducer = /** @class */ (function () {
        /**
       * Constructor for the StateReducer class.
       * @param element The HTMLInputElement which represents the toggle.
       * @param isTristate A boolean indicating whether the toggle is tristate.
       * Initializes the toggle state with the given element and tristate value.
       */
        function StateReducer(element, isTristate) {
            this.isTristate = isTristate;
            this.state = this.getElementState(element);
        }
        /**
       * Retrieves the current state of the toggle based on the HTMLInputElement.
       * The state is determined by the following:
       * - The checked property of the input element
       * - The disabled property of the input element
       * - The readonly property of the input element
       * - The indeterminate property of the input element if the toggle is tristate
       * @returns An object containing the state of the toggle.
       */
        StateReducer.prototype.getElementState = function (element) {
            var checked = element.checked;
            var status;
            if (element.disabled) {
                status = ToggleStateStatus.DISABLED;
            }
            else if (element.readOnly) {
                status = ToggleStateStatus.READONLY;
            }
            else {
                status = ToggleStateStatus.ENABLED;
            }
            var indeterminate = this.isTristate && element.indeterminate;
            var value;
            if (indeterminate) {
                value = ToggleStateValue.INDETERMINATE;
            }
            else if (checked) {
                value = ToggleStateValue.ON;
            }
            else {
                value = ToggleStateValue.OFF;
            }
            return {
                value: value,
                checked: checked,
                status: status,
                indeterminate: indeterminate,
            };
        };
        /**
       * Get the current toggle state.
       * @returns An immutable copy of the current toggle state.
       */
        StateReducer.prototype.get = function () {
            return Object.freeze(__assign({}, this.state));
        };
        /**
       * Determines whether the toggle is enabled and can be interacted with.
       * @returns True if the toggle is enabled and can be interacted with, false otherwise.
       */
        StateReducer.prototype.canInteract = function () {
            return this.state.status === ToggleStateStatus.ENABLED;
        };
        /**
       * Synchronizes the internal state of the toggle with the provided HTMLInputElement.
       * This method is useful when you need to update the internal state of the toggle
       * manually, such as when the toggle is updated programmatically.
       * @param element The HTMLInputElement to synchronize the toggle state with.
       */
        StateReducer.prototype.sync = function (element) {
            this.state = this.getElementState(element);
        };
        /**
       * Apply a toggle action to the toggle state.
       * @param action The toggle action to apply.
       * @returns A boolean indicating whether the action was successful.
       * If the toggle is disabled, any action execpect {@code ToggleActionType.ENABLE} will return {@code false}.
       * If the toggle is currently in the target state of the action, the action will return {@code false}.
       * If the toggle is in the indeterminate state and the action is {@code ToggleActionType.DETERMINATE},
       * the toggle will be set to the checked state.
       * If the action is {@code ToggleActionType.NEXT} :
       *  - For a tristate toggle, the toggle will do ON -> INDETERMINATE -> OFF -> INDETERMINATE -> ON.
       *  - For a non-tristate toggle, the toggle will do ON -> OFF -> ON.
       */
        StateReducer.prototype.do = function (action) {
            var actionsRequiringInteract = [
                ToggleActionType.ON,
                ToggleActionType.OFF,
                ToggleActionType.TOGGLE,
                ToggleActionType.INDETERMINATE,
                ToggleActionType.DETERMINATE,
                ToggleActionType.NEXT,
                ToggleActionType.READONLY,
            ];
            if (actionsRequiringInteract.includes(action) && !this.canInteract())
                return false;
            switch (action) {
                case ToggleActionType.ON:
                    return this.setValueIfChanged(ToggleStateValue.ON, true, false);
                case ToggleActionType.OFF:
                    return this.setValueIfChanged(ToggleStateValue.OFF, false, false);
                case ToggleActionType.TOGGLE:
                    if (this.state.value === ToggleStateValue.ON)
                        return this.do(ToggleActionType.OFF);
                    if (this.state.value === ToggleStateValue.OFF)
                        return this.do(ToggleActionType.ON);
                    return false;
                case ToggleActionType.INDETERMINATE:
                    return this.setValueIfChanged(ToggleStateValue.INDETERMINATE, undefined, true);
                case ToggleActionType.DETERMINATE:
                    if (this.state.value != ToggleStateValue.INDETERMINATE)
                        return false;
                    return this.setValue(this.state.checked ? ToggleStateValue.ON : ToggleStateValue.OFF, this.state.checked, false);
                case ToggleActionType.NEXT:
                    return this.doNext();
                case ToggleActionType.DISABLE:
                    return this.setStatusIfChanged(ToggleStateStatus.DISABLED);
                case ToggleActionType.ENABLE:
                    return this.setStatusIfChanged(ToggleStateStatus.ENABLED);
                case ToggleActionType.READONLY:
                    return this.setStatus(ToggleStateStatus.READONLY);
            }
        };
        /**
         * Sets the state of the toggle to the provided value.
         * If checked or indeterminate is provided, sets the corresponding property of the state to the provided value.
         * Otherwise, leaves the property unchanged.
         * @param value The value of the toggle to set.
         * @param checked The checked state of the toggle to set. If not provided, the property is left unchanged.
         * @param indeterminate The indeterminate state of the toggle to set. If not provided, the property is left unchanged.
         * @returns A boolean indicating whether the state was updated.
         */
        StateReducer.prototype.setValue = function (value, checked, indeterminate) {
            this.state = __assign(__assign({}, this.state), { value: value, checked: checked !== null && checked !== void 0 ? checked : this.state.checked, indeterminate: indeterminate !== null && indeterminate !== void 0 ? indeterminate : this.state.indeterminate });
            return true;
        };
        /**
         * Sets the state of the toggle to the provided value if the value is different from the current state.
         * If checked or indeterminate is provided, sets the corresponding property of the state to the provided value.
         * Otherwise, leaves the property unchanged.
         * @returns A boolean indicating whether the state was updated.
         */
        StateReducer.prototype.setValueIfChanged = function (value, checked, indeterminate) {
            if (this.state.value === value)
                return false;
            return this.setValue(value, checked, indeterminate);
        };
        /**
         * Sets the status of the toggle to the provided value.
         * @param status The new status of the toggle.
         * @returns A boolean indicating whether the state was updated.
         */
        StateReducer.prototype.setStatus = function (status) {
            this.state = __assign(__assign({}, this.state), { status: status });
            return true;
        };
        /**
         * Sets the status of the toggle to the provided value if the value is different from the current status.
         * @param status The new status of the toggle.
         * @returns A boolean indicating whether the state was updated.
         */
        StateReducer.prototype.setStatusIfChanged = function (status) {
            if (this.state.status === status)
                return false;
            return this.setStatus(status);
        };
        /**
         * Applies the next action based on the current state of the toggle.
         * If the toggle is tristate, cycles through the on, off, and indeterminate states.
         * If the toggle is not tristate, cycles through the on and off states.
         * @returns A boolean indicating whether the state was updated.
         */
        StateReducer.prototype.doNext = function () {
            if (this.isTristate) {
                if (this.state.value === ToggleStateValue.ON || this.state.value === ToggleStateValue.OFF) {
                    return this.do(ToggleActionType.INDETERMINATE);
                }
                if (this.state.value === ToggleStateValue.INDETERMINATE) {
                    return this.state.checked
                        ? this.do(ToggleActionType.OFF)
                        : this.do(ToggleActionType.ON);
                }
            }
            else {
                return this.state.value === ToggleStateValue.ON
                    ? this.do(ToggleActionType.OFF)
                    : this.do(ToggleActionType.ON);
            }
            return false;
        };
        return StateReducer;
    }());

    var Toggle = /** @class */ (function () {
        /**
       * Initializes a new instance of the BootstrapToggle class.
       * @param element The HTMLInputElement element which represents the toggle.
       * @param options The options for the toggle.
       * @returns The constructed BootstrapToggle instance.
       */
        function Toggle(element, options) {
            var _this = this;
            this.pointer = null;
            this.SCROLL_THRESHOLD = 10;
            this.eventsBound = false;
            this.suppressExternalSync = false;
            this.originalDescriptors = new Map();
            /**
             * Handles the change event of the input element of the toggle.
             * This event listener is responsible for detecting when the input element
             * of the toggle changes its state and triggering the update method to keep the toggle in sync.
             */
            this.onExternalChange = function () {
                _this.update(true);
            };
            this.onFormReset = function () {
                setTimeout(function () { return _this.onExternalChange(); }, 0);
            };
            /**
           * Handles pointer down events by initiating the toggle action and setting up
           * listeners for pointer movement, release, and cancellation.
           *
           * The method early exits if:
           * - the pointer event is not a primary mouse button click
           * - the toggle cannot be interacted with (`disabled` or `readonly`)
           * @param e The PointerEvent object representing the pointer down event.
           */
            this.onPointerDown = function (e) {
                if (e.pointerType === "mouse" && e.button !== 0)
                    return;
                if (!_this.stateReducer.canInteract())
                    return;
                _this.pointer = { x: e.clientX, y: e.clientY };
                _this.domBuilder.root.addEventListener("pointermove", _this.onPointerMove, {
                    passive: true,
                });
                _this.domBuilder.root.addEventListener("pointerup", _this.onPointerUp, {
                    passive: true,
                });
                _this.domBuilder.root.addEventListener("pointercancel", _this.onPointerCancel, { passive: true });
            };
            /**
           * Handles pointer move events by checking the distance moved from the initial pointer down position.
           * If the pointer has moved beyond a certain threshold, the pointer interaction is cancelled.
           *
           * Allows dragging within the width of the toggle but cancels if vertical movement exceeds the scroll threshold.
           * @param e The PointerEvent object representing the pointer move event.
           */
            this.onPointerMove = function (e) {
                var dx = Math.abs(e.clientX - _this.pointer.x);
                var dy = Math.abs(e.clientY - _this.pointer.y);
                if (dy > _this.SCROLL_THRESHOLD || dx > _this.domBuilder.root.offsetWidth) {
                    _this.onPointerCancel();
                }
            };
            /**
           * Handles pointer up events by determining if the pointer interaction
           * should trigger a toggle action based on the distance moved.
           *
           * If the pointer has moved beyond a certain threshold, the pointer interaction is cancelled.
           * Allows dragging within the width of the toggle but cancels if vertical movement exceeds the scroll threshold.
           * Finally, it cleans up by calling the pointer cancel handler.
           *
           * If the pointer event is not a primary mouse button click, the interaction is cancelled.
           * @param e The PointerEvent object representing the pointer up event.
           */
            this.onPointerUp = function (e) {
                if (e.pointerType === "mouse" && e.button !== 0) {
                    _this.onPointerCancel();
                    return;
                }
                var dx = Math.abs(e.clientX - _this.pointer.x);
                var dy = Math.abs(e.clientY - _this.pointer.y);
                if (dy <= _this.SCROLL_THRESHOLD && dx <= _this.domBuilder.root.offsetWidth) {
                    _this.apply(ToggleActionType.NEXT);
                }
                _this.onPointerCancel();
            };
            /**
           * Cleans up pointer event listeners after a pointer interaction is completed or cancelled.
           *
           * This method removes the `pointermove`, `pointerup`, and `pointercancel` event listeners
           * from the root element of the toggle.
           * However, `pointerdown` listener remains active for future interactions.
           */
            this.onPointerCancel = function () {
                _this.domBuilder.root.removeEventListener("pointermove", _this.onPointerMove);
                _this.domBuilder.root.removeEventListener("pointerup", _this.onPointerUp);
                _this.domBuilder.root.removeEventListener("pointercancel", _this.onPointerCancel);
            };
            this.handlerKeyboardEvent = function (e) {
                if (e.key == " ") {
                    _this.apply(ToggleActionType.NEXT);
                }
            };
            this.handlerLabelEvent = function (e) {
                e.preventDefault();
                _this.apply(ToggleActionType.NEXT);
                _this.domBuilder.root.focus();
            };
            this.element = element;
            this.userOptions = options;
            this.options = OptionResolver.resolve(element, options);
            this.stateReducer = new StateReducer(element, this.options.tristate);
            this.domBuilder = new DOMBuilder(element, this.options, this.stateReducer.get());
            this.bindEventListeners();
            this.interceptInputProperties();
            this.element.bsToggle = this;
        }
        /**
         * Intercepts the following input properties to detect external changes:
         * - checked
         * - disabled
         * - readonly
         * - indeterminate
         * This method is used to detect changes made to the input element directly,
         * rather than through the BootstrapToggle API. It is used to maintain the
         * state of the toggle in cases where the user changes the input element
         * directly, rather than through the API.
         * @returns void
         */
        Toggle.prototype.interceptInputProperties = function () {
            var _this = this;
            var props = ["checked", "disabled", "readOnly", "indeterminate"];
            props.forEach(function (prop) {
                var descriptor = Object.getOwnPropertyDescriptor(Object.getPrototypeOf(_this.element), prop);
                if (!(descriptor === null || descriptor === void 0 ? void 0 : descriptor.set))
                    return;
                _this.originalDescriptors.set(prop, descriptor);
                Object.defineProperty(_this.element, prop, {
                    configurable: true,
                    get: function () { return descriptor.get.call(_this.element); },
                    set: function (value) {
                        descriptor.set.call(_this.element, value);
                        if (_this.suppressExternalSync)
                            return;
                        _this.onExternalChange();
                    },
                });
            });
        };
        /**
         * Restores the original input properties of the toggle element.
         * This method is used to restore the original descriptors of the input properties
         * which were intercepted by the BootstrapToggle to detect external changes.
         * @returns void
         */
        Toggle.prototype.restoreInputProperties = function () {
            var _this = this;
            this.originalDescriptors.forEach(function (descriptor, prop) {
                Object.defineProperty(_this.element, prop, descriptor);
            });
            this.originalDescriptors.clear();
        };
        /**
       * Binds event listeners to the toggle element.
       * This method is called by the constructor and is responsible for
       * binding the following event listeners:
       * - Pointer events (click, touchstart, touchend)
       * - Keyboard events (keydown, keyup)
       * - Label events (click)
       * If the event listeners are already bound (i.e. this.eventsBound is true),
       * this method does nothing.
       * @returns void
       */
        Toggle.prototype.bindEventListeners = function () {
            if (this.eventsBound)
                return;
            this.bindFormResetListener();
            this.bindPointerEventListener();
            this.bindKeyboardEventListener();
            this.bindLabelEventListener();
            this.eventsBound = true;
        };
        /**
       * Unbinds all event listeners from the toggle element.
       * This method is called by the destructor and is responsible for
       * unbinding the following event listeners:
       * - Pointer events (click, touchstart, touchend)
       * - Keyboard events (keydown, keyup)
       * - Label events (click)
       * If the event listeners are not bound (i.e. this.eventsBound is false),
       * this method does nothing.
       * @returns void
       */
        Toggle.prototype.unbindEventListeners = function () {
            if (!this.eventsBound)
                return;
            this.unbindFormResetListener();
            this.unbindPointerEventListener();
            this.unbindKeyboardEventListener();
            this.unbindLabelEventListener();
            this.eventsBound = false;
        };
        Toggle.prototype.bindFormResetListener = function () {
            var form = this.element.form;
            if (!form)
                return;
            form.addEventListener("reset", this.onFormReset);
        };
        Toggle.prototype.unbindFormResetListener = function () {
            var form = this.element.form;
            if (!form)
                return;
            form.removeEventListener("reset", this.onFormReset);
        };
        /**
       * Binds a pointerdown event listener to the root element of the toggle.
       * The event listener is responsible for handling pointer events (e.g. mouse clicks, touch events)
       * and triggering the toggle's state change when a pointer event occurs.
       * The event listener is bound with the passive option, which means that it will not block
       * other event listeners from being triggered.
       */
        Toggle.prototype.bindPointerEventListener = function () {
            this.domBuilder.root.addEventListener("pointerdown", this.onPointerDown, {
                passive: true,
            });
        };
        /**
       * Unbinds the pointerdown event listener from the root element of the toggle.
       * This method is responsible for unbinding the pointerdown event listener that was
       * previously bound by the bindPointerEventListener method.
       * If the event listener is not bound (i.e. this.eventsBound is false), this method does nothing.
       * @returns void
       */
        Toggle.prototype.unbindPointerEventListener = function () {
            this.domBuilder.root.removeEventListener("pointerdown", this.onPointerDown);
        };
        /**
       * Binds a keypress event listener to the root element of the toggle.
       * The event listener is responsible for handling keypress events
       * and triggering the toggle's state change when a keypress event occurs.
       * The event listener is bound with the passive option, which means that it will not block
       * other event listeners from being triggered.
       */
        Toggle.prototype.bindKeyboardEventListener = function () {
            this.domBuilder.root.addEventListener("keypress", this.handlerKeyboardEvent, { passive: true });
        };
        /**
       * Unbinds the keypress event listener from the root element of the toggle.
       * This method is responsible for unbinding the keypress event listener that was
       * previously bound by the bindKeyboardEventListener method.
       * If the event listener is not bound (i.e. this.eventsBound is false), this method does nothing.
       * @returns void
       */
        Toggle.prototype.unbindKeyboardEventListener = function () {
            this.domBuilder.root.removeEventListener("keypress", this.handlerKeyboardEvent);
        };
        /**
       * Binds a click event listener to all labels that are associated with the toggle's input element.
       * The event listener is responsible for handling click events and triggering the toggle's state change when a click event occurs.
       * The event listener is bound with the passive option set to false, which means that it will block other event listeners from being triggered until it has finished its execution.
       * This method is called by the constructor and is responsible for binding the event listener to the toggle's labels.
       * If the toggle's input element does not have an id (i.e. this.element.id is null or undefined), this method does nothing.
       * @returns void
       */
        Toggle.prototype.bindLabelEventListener = function () {
            var _this = this;
            if (this.element.id) {
                document
                    .querySelectorAll('label[for="' + this.element.id + '"]')
                    .forEach(function (label) {
                    label.addEventListener("click", _this.handlerLabelEvent, {
                        passive: false,
                    });
                });
            }
        };
        /**
       * Unbinds the click event listener from all labels that are associated with the toggle's input element.
       * This method is responsible for unbinding the event listener that was previously bound by the bindLabelEventListener method.
       * If the toggle's input element does not have an id (i.e. this.element.id is null or undefined), this method does nothing.
       * @returns void
       */
        Toggle.prototype.unbindLabelEventListener = function () {
            var _this = this;
            if (this.element.id) {
                document
                    .querySelectorAll('label[for="' + this.element.id + '"]')
                    .forEach(function (label) {
                    label.removeEventListener("click", _this.handlerLabelEvent);
                });
            }
        };
        /**
       * Applies a toggle action to the toggle state and renders the toggle element.
       * If the action is successful, this method will render the toggle element with the new state.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param action The toggle action to apply.
       * @param silent A boolean indicating whether to trigger the change event after applying the action.
       */
        Toggle.prototype.apply = function (action, silent) {
            if (silent === void 0) { silent = false; }
            if (!this.stateReducer.do(action))
                return;
            this.suppressExternalSync = true;
            try {
                this.domBuilder.render(this.stateReducer.get());
                if (!silent)
                    this.trigger();
            }
            finally {
                this.suppressExternalSync = false;
            }
        };
        /**
       * Toggles the state of the toggle.
       * If the toggle is currently in the on state, it will be set to the off state.
       * If the toggle is currently in the off state, it will be set to the on state.
       * If the toggle is currently in the indeterminate state, it will be set to the on state.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param silent A boolean indicating whether to trigger the change event after applying the action.
       */
        Toggle.prototype.toggle = function (silent) {
            if (silent === void 0) { silent = false; }
            this.apply(ToggleActionType.TOGGLE, silent);
        };
        /**
       * Sets the toggle state to on.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param silent A boolean indicating whether to trigger the change event after applying the action.
       */
        Toggle.prototype.on = function (silent) {
            if (silent === void 0) { silent = false; }
            this.apply(ToggleActionType.ON, silent);
        };
        /**
       * Sets the toggle state to off.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param silent A boolean indicating whether to trigger the change event after applying the action.
       */
        Toggle.prototype.off = function (silent) {
            if (silent === void 0) { silent = false; }
            this.apply(ToggleActionType.OFF, silent);
        };
        /**
       * Sets the toggle state to indeterminate.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param {boolean} silent A boolean indicating whether to trigger the change event after applying the action.
       */
        Toggle.prototype.indeterminate = function (silent) {
            if (silent === void 0) { silent = false; }
            this.apply(ToggleActionType.INDETERMINATE, silent);
        };
        /**
       * Sets the toggle state to determinate.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param {boolean} silent A boolean indicating whether to trigger the change event after applying the action.
       */
        Toggle.prototype.determinate = function (silent) {
            if (silent === void 0) { silent = false; }
            this.apply(ToggleActionType.DETERMINATE, silent);
        };
        /**
       * Enables the toggle.
       * If the toggle is currently disabled, this method will set the toggle state to enabled.
       * If the silent parameter is false, this method will also trigger the change event.
       * @returns void
       */
        Toggle.prototype.enable = function () {
            this.apply(ToggleActionType.ENABLE);
        };
        /**
       * Disables the toggle.
       * If the toggle is currently enabled, this method will set the toggle state to disabled.
       * If the silent parameter is false, this method will also trigger the change event.
       */
        Toggle.prototype.disable = function () {
            this.apply(ToggleActionType.DISABLE);
        };
        /**
       * Sets the toggle state to readonly.
       * If the toggle is currently disabled or enabled, this method will set the toggle state to readonly.
       * If the silent parameter is false, this method will also trigger the change event.
       * @returns void
       */
        Toggle.prototype.readonly = function () {
            this.apply(ToggleActionType.READONLY);
        };
        /**
       * Synchronizes the toggle state with the input element and renders the toggle.
       * If the silent parameter is false, this method will also trigger the change event.
       * @param {boolean} silent A boolean indicating whether to trigger the change event after synchronizing the toggle state.
       */
        Toggle.prototype.update = function (silent) {
            this.suppressExternalSync = true;
            try {
                this.stateReducer.sync(this.element);
                this.domBuilder.render(this.stateReducer.get());
                if (!silent)
                    this.trigger();
            }
            finally {
                this.suppressExternalSync = false;
            }
        };
        /**
       * Triggers the change event on the toggle's input element.
       * @param {boolean} silent A boolean indicating whether to trigger the change event.
       * If the silent parameter is false, this method will trigger the change event.
       */
        Toggle.prototype.trigger = function (silent) {
            if (silent === void 0) { silent = false; }
            if (!silent)
                this.element.dispatchEvent(new Event("change", { bubbles: true }));
        };
        /**
       * Destroys the toggle element and unbinds all event listeners.
       *This method is useful when you need to remove the toggle element from the DOM.
       *After calling this method, the toggle element will be removed from the DOM and all event listeners will be unbound.
       */
        Toggle.prototype.destroy = function () {
            this.restoreInputProperties();
            this.unbindEventListeners();
            this.domBuilder.destroy();
            delete this.element.bsToggle;
        };
        /**
       * Destroys the toggle element and reinitializes it with the same options.
       *This method is useful when you need to reinitialize the toggle element with the same options.
       */
        Toggle.prototype.rerender = function () {
            this.destroy();
            new Toggle(this.element, this.userOptions);
        };
        return Toggle;
    }());

    var ToggleMethods;
    (function (ToggleMethods) {
        ToggleMethods["on"] = "on";
        ToggleMethods["ON"] = "ON";
        ToggleMethods["off"] = "off";
        ToggleMethods["OFF"] = "OFF";
        ToggleMethods["toggle"] = "toggle";
        ToggleMethods["TOGGLE"] = "TOGGLE";
        ToggleMethods["determinate"] = "determinate";
        ToggleMethods["DETERMINATE"] = "DETERMINATE";
        ToggleMethods["indeterminate"] = "indeterminate";
        ToggleMethods["INDETERMINATE"] = "INDETERMINATE";
        ToggleMethods["enable"] = "enable";
        ToggleMethods["ENABLE"] = "ENABLE";
        ToggleMethods["disable"] = "disable";
        ToggleMethods["DISABLE"] = "DISABLE";
        ToggleMethods["readonly"] = "readonly";
        ToggleMethods["READONLY"] = "READONLY";
        ToggleMethods["destroy"] = "destroy";
        ToggleMethods["DESTROY"] = "DESTROY";
        ToggleMethods["rerender"] = "rerender";
        ToggleMethods["RENDERER"] = "RENDERER";
    })(ToggleMethods || (ToggleMethods = {}));

    (function () {
        /**
       * Add `bootstrapToggle` prototype function to HTML Elements
       * Enables execution when used with HTML - ex: `document.getElementById('toggle').bootstrapToggle('on')`
       */
        HTMLInputElement.prototype.bootstrapToggle = function (options, silent) {
            var _bsToggle = this.bsToggle || new Toggle(this, (options && typeof options !== "string") ? options : {});
            // Execute method calls
            if (options && typeof options === "string") {
                switch (options) {
                    case ToggleMethods.TOGGLE:
                    case ToggleMethods.toggle:
                        return _bsToggle.toggle(silent);
                    case ToggleMethods.ON:
                    case ToggleMethods.on:
                        return _bsToggle.on(silent);
                    case ToggleMethods.OFF:
                    case ToggleMethods.off:
                        return _bsToggle.off(silent);
                    case ToggleMethods.INDETERMINATE:
                    case ToggleMethods.indeterminate:
                        return _bsToggle.indeterminate(silent);
                    case ToggleMethods.DETERMINATE:
                    case ToggleMethods.determinate:
                        return _bsToggle.determinate(silent);
                    case ToggleMethods.ENABLE:
                    case ToggleMethods.enable:
                        return _bsToggle.enable();
                    case ToggleMethods.DISABLE:
                    case ToggleMethods.disable:
                        return _bsToggle.disable();
                    case ToggleMethods.READONLY:
                    case ToggleMethods.readonly:
                        return _bsToggle.readonly();
                    case ToggleMethods.DESTROY:
                    case ToggleMethods.destroy:
                        return _bsToggle.destroy();
                    case ToggleMethods.RENDERER:
                    case ToggleMethods.rerender:
                        return _bsToggle.rerender();
                }
            }
        };
        /**
       * Replace all `input[type=checkbox][data-toggle="toggle"]` inputs with "Bootstrap-Toggle"
       * Executes once page elements have rendered enabling script to be placed in `<head>`
       */
        if (globalThis.window !== undefined)
            globalThis.window.onload = function () {
                document
                    .querySelectorAll('input[type=checkbox][data-toggle="toggle"]')
                    .forEach(function (ele) {
                    ele.bootstrapToggle();
                });
            };
        // Export library if possible
        if (typeof module !== "undefined" && module.exports) {
            module.exports = Toggle;
        }
    })();

}));
//# sourceMappingURL=bootstrap5-toggle.ecmas.js.map
