﻿var oEditor=window.opener,FCKEquation=null,eSelected=null;
function LoadSelected(){if(oEditor&&"undefined"!=typeof oEditor.FCKEquation)if((FCKEquation=oEditor.FCKEquation)&&(eSelected=oEditor.FCKSelection.GetSelectedElement()),eSelected&&"IMG"==eSelected.tagName&&eSelected._fckequation){var a=unescape(eSelected._fckequation).match(/\\f([\[\$])(.*?)\\f[\]\$]/);document.getElementById("latex_formula").value=a[2];"["==a[1]?document.getElementById("eqstyle2").checked=!0:document.getElementById("eqstyle1").checked=!0;renderEqn(null)}else document.getElementById("latex_formula").value=
"",null==eSelected};