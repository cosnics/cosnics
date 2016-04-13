$(function() {
	
	$(document).ready(function() {
		$("input").autocomplete({minLength:3, source:"https://bitbucket.org/xhr/users/"});
	});

});