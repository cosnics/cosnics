/**
* jQuery jPassword plugin
* @licenses	Creative Commons BY-SA [ http://creativecommons.org/licenses/by-sa/2.0/deed.fr ]
*
* @name		jPassword
* @desc		Plugin jQuery that provides you to detect strength of password. It gives help messages to write a strong password and generates on demand personnalized password. Help can be localized and shown in a tooltip or directly after input.
* @author	Hervé GOUCHET [ contact(at)rvdevsign(dot)net ]
* @version	1.0
* @date		2009/01/24
* @doc		http://www.rvdevsign.net/ressources/javascript/jpassword-plugin-jquery.html
* @requires jQuery v1.2.6+
* 
* Example:
* $("input").jpassword();
*/
(function($){
	$.fn.jpassword = function(settings){
		var jElements			= this;
		var settings			= $.extend({}, $.fn.jpassword.defaults, settings);
		var template			= '<div class="jpassword"><div style="padding: 0px; margin: 0px;"><p class="jpassword-meter">&nbsp;</p><p class="jpassword-info">&nbsp;</p></div></div>';
		
		return jElements.each(function(){
			// Manage all inputs type password
			if($(jElements).is("input")){ jPassword( $(jElements) ); }
		});
		
		// Construct password meter
		function jPassword(jInput){
			// Create tooltip
			var unikId			= "jpassword_" + parseInt(Math.random()*1000);
			var jTooltip		= $(template).attr("id", unikId);
			if(settings.flat == false){
				// Define position of the tooltip
				var pos			= jInput.offset();
				var win			= getWindow();
				var dir			= "right";
				var top			= pos.top;
				var left		= (pos.left + jInput.width());
				jTooltip.appendTo(document.body);
				if((left + jTooltip.width()) > (win.left + win.width)){ left -= (jTooltip.width() + jInput.width()); dir = "left"; }
				if((top + jTooltip.height()) > (win.top + win.height)){ top -= (jTooltip.height() - (jInput.height()*1.5)); dir += "bottom"; }else{ dir += "top"; }
				jTooltip.css({ left: left + "px", top: top + "px", display: "none" });
				jTooltip.addClass("jpassword-" + dir);
			}else{
				// Insert after the input
				jTooltip.insertAfter(jInput);
				jTooltip.css({ position: "relative", display: "none" });
				jTooltip.addClass("jpassword-flat");
			}
			// Event handler
			jInput.bind("keyup", function(e){ verifPsw(jInput, jTooltip); });
			jInput.bind("focus", function(e){
				verifPsw(jInput, jTooltip);
				// Show tooltip
				if(settings.flat == false){ tooltip(jTooltip, "show"); }
				// Function called when the tooltip is shown
				if($.isFunction(settings.onShow)){ settings.onShow(jInput, jTooltip); }	
			});
			jInput.bind("blur", function(e){
				// Hide tooltip
				if(settings.flat == false){ tooltip(jTooltip, "hide"); }
				// Function called when the tooltip is hided
				if($.isFunction(settings.onHide)){ settings.onHide(jInput, jTooltip); }	
			});
			// Generate a new password
			var jGenerate		= $("#" + settings.generate);
			if(jGenerate){ jGenerate.bind("click", function(e){ jInput.val(newPsw()); verifPsw(jInput, jTooltip); return false; }); }
			// Function called when process is completed
			if($.isFunction(settings.onComplete)){ settings.onComplete(jInput, jTooltip); }
		}
		
		// Verified password and update the tolerance meter
		function verifPsw(jInput, jTooltip){
			var val				= jInput.val();
			var meter			= jTooltip.find(".jpassword-meter");
			var info			= jTooltip.find(".jpassword-info");
			var psw				= securPsw(val);
			
			// Advises for write a strong password
			var msg				= "";
			if(psw.lowercase < 2){
				msg				= settings.lang.lowercase;
			}else if(psw.uppercase < 2){
				msg				= settings.lang.uppercase;
			}else if(psw.number < 2){
				msg				= settings.lang.number;
			}else if(psw.punctuation < 2){
				msg				= settings.lang.punctuation;
			}else if(psw.special < 2){
				msg				= settings.lang.special;
			}
			// Correct length ?
			if(val.length < settings.length && psw.level < 10 && msg == ""){ msg = settings.lang.length.replace(/-X-/g, settings.length); }
			// Display of level
			if(psw.val == ""){
				meter.css("background-position", "0 0");
				info.html(settings.lang.please);	
			}else if(psw.level < 5){
				meter.css("background-position", "0 -10px");
				info.html(settings.lang.low + " " + msg);
			}else if(psw.level < 10){
				meter.css("background-position", "0 -20px");
				info.html(settings.lang.correct + " " + msg);
			}else{
				meter.css("background-position", "0 -30px");
				info.html(settings.lang.high);
			}
			// Replace value of password
			jInput.val(psw.val);
			// Function called when writing the password 
			if($.isFunction(settings.onKeyup)){ settings.onKeyup(jInput); }
		}
		
		// Verified degree of security of password
		function securPsw(val){
			val					= val.replace(/(^\s+)|(\s+$)/g, "");
			var cNbr = cCap = cMin = cPct = cSpe = 1;
			var len				= val.length;
			for(var c = 0; c < len; c++){
				var char		= val.charCodeAt(c);
				if(char < 128){ if(char > 47 && char < 58){ cNbr += 1; }else if(char > 64 && char < 91){ cCap += 1; }else if(char > 96 && char < 123){ cMin += 1; }else{ cPct += 2;} }else{ cSpe += 3; }
			}
			var lPsw			= (cNbr * cCap * cMin * cPct * cSpe);
			lPsw				= Math.round(Math.log((lPsw * lPsw)));
			
			return { val: val, level: lPsw, number: cNbr, uppercase: cCap, lowercase: cMin, punctuation: cPct, special: cSpe };
		}
		
		// Generate a password
		function newPsw(){
			var val				= "";
			for(c = 0; c < settings.length; c++){
				var char		= Math.round(32+Math.random()*222);
				var ok			= 0;
				// Number
				if((char > 47 && char < 58) || (char > 64 && char < 91) || (char > 96 && char < 123)){ ok = 1; }
				// Upper or lower case
				if(settings.type == 1 && char < 127){ ok = 1; }
				// Puntuations
				if(settings.type == 2){ ok = 1; }
				// Special
				if(settings.special && (char == 48 || char == 49 || char == 50 || char == 53 || char == 54 || char == 56 || char == 57 || char == 66 || char == 67 || char == 68 || char == 71 || char == 73 || char == 75 || char == 79 || char == 80 || char == 81 || char == 83 || char == 85 || char == 86 || char == 87 || char == 88 || char == 90 || char == 99 || char == 104 || char == 105 || char == 107 || char == 108 || char == 111 || char == 112 || char == 113 || char == 115 || char == 117 || char == 118 || char == 119 || char == 120 || char == 122)){ ok = 0; }
				if(ok == 1){ val += String.fromCharCode(char); }else{ c--; }
			}
			return val;
		}
		
		// Show or hide tooltip 
		function tooltip(jTooltip, effect){
			if(effect == "show"){ jTooltip.fadeIn(); }else{ jTooltip.fadeOut(); }
		}
		
		// Get window size
		function getWindow(){
			var m				= document.compatMode == "CSS1Compat";
			return {
				left : (window.pageXOffset || (m ? document.documentElement.scrollLeft : document.body.scrollLeft)),
				top : (window.pageYOffset || (m ? document.documentElement.scrollTop : document.body.scrollTop)),
				width : (window.innerWidth || (m ? document.documentElement.clientWidth : document.body.clientWidth)),
				height : (window.innerHeight || (m ? document.documentElement.clientHeight : document.body.clientHeight))
			};
		}
	};

	// Default settings
	$.fn.jpassword.defaults = {
		lang: { please: "A strong password...", low: "Low security.", correct: "Correct security.", high: "High security.", length: "-X- characters would be a plus.", number: "Why not numbers?", uppercase: "And caps?", lowercase: "Some tiny?", punctuation: "Punctuations?", special: "Best, special characters?" },
		length: 8,														// Length minimal of good password
		flat: false,													// Add jPassword after input or show it on demand
		type: 1,														// 0: low, 1: correct, 2: high. Defined level of security
		special: 0,														// 0 or 1. If 1, used the special chars when generating password
		generate: null,													// ID of the element whose on click generates a password (without #)
		onShow: function(){},											// Function called when the tooltip is shown (return: jQuery of input and tooltip)
		onHide: function(){},											// Function called when the tooltip is hided (return: jQuery of input and tooltip)
		onKeyup: function(){},											// Function called when writing the password (return: jQuery of input)
		onComplete: function(){}										// Function called when the process is done (return: jQuery of input and tooltip)
	};
})(jQuery);