$(function() {
	function disable_checkbox(elem) {
		if (!elem.is(':checkbox'))
			return;
		elem.prop('checked', false);
		elem.prop('disabled', true);
	}

	function enable_checkbox(elem) {
		if (!elem.is(':checkbox'))
			return;
		elem.removeAttr('disabled');
	}

	function toggle_others(elem) {
		var code_elem = $(".code");
		var request_elem = $(".request");
		var value = -1;
		if (elem.length == 2)
			value = elem.siblings('input:checked').val();
		else
			value = elem.val();

		var id = -1;
		if (elem.length == 2)
			id = elem.siblings('input').attr('name');
		else
			id = elem.attr('id');

		if (value == 0) {
			switch (id) {
			case 'direct_target_groups_option':
				if (!$(".direct").is(':checked'))
					return false;
				disable_checkbox(request_elem);
				$('#requestBlock').css('display', 'none');
			case 'request_target_groups_option':
				if (elem.siblings('input:checked').attr('id') == 'receiver_request_target_groups'
						&& !$(".request").is(':checked'))
					return false;
				disable_checkbox(code_elem);
				$('#codeBlock').css('display', 'none');
				break;
			case 'creation_groups_option':
				if (!$(".creation").is(':checked'))
					return false;
				disable_checkbox($(".creation_on_request"));
				$('#creation_on_requestBlock').css('display', 'none');
				break;
			}
		} else {
			switch (id) {
			case 'direct_target_groups_option':
			case 'request_target_groups_option':
				if ($("input[name=code_fixed]").length == 0
						&& ($(
								'input[name=request_target_groups_option]:checked')
								.attr('id').toString() != 'receiver_request_target_groups' || !$(
								".request").is(':checked')))
					enable_checkbox(code_elem);
				if ($("input[name=request_fixed]").length == 0)
					enable_checkbox(request_elem);
				break;
			case 'creation_groups_option':
				enable_checkbox($(".creation_on_request"));
				break;
			}
		}
	}

	function change_block(elem) {
		var type = elem.attr('class').split(' ').slice(-1);
		block = $('#' + type + 'Block');

		if (elem.prop('checked')) {
			block.css('display', 'block');
			switch (type.toString()) {
			case 'direct':
			case 'request':
				toggle_others($('input[name=' + type + '_target_groups_option]'));
				break;
			case 'creation':
				toggle_others($('input[name=' + type + '_groups_option]'));
				break;
			}
		} else {
			block.css('display', 'none');
			switch (type.toString()) {
			case 'direct':
			case 'request':
				if ($("input[name=code_fixed]").length == 0
						&& ($(
								'input[name=request_target_groups_option]:checked')
								.attr('id').toString() != 'receiver_request_target_groups' || !$(
								".request").is(':checked')))
					enable_checkbox($(".code"));
				if ($("input[name=request_fixed]").length == 0)
					enable_checkbox($(".request"));
				break;
			case 'creation':
				enable_checkbox($(".creation_on_request"));
				break;
			}
		}
	}

	function disable_element_style(theElement) {
		if (theElement.css("background-image")) {
			if (!theElement.hasClass('disabled')) {
				theElement.addClass('disabled');
				theElement.css("background-image", theElement.css(
						"background-image").replace(".png", "Na.png"));
			}
		}
	}

	function enable_element_style(theElement) {
		if (typeof theElement.css("background-image") !== 'undefined') {
			theElement.removeClass('disabled');
			theElement.css("background-image", theElement.css(
					"background-image").replace("Na.png", ".png"));
		}
	}

	function disable_element(currentElement) {
		var currentElementParent = currentElement.parent().parent();

		disable_element_style(currentElement);

		var subElements = $('ul:first div a', currentElementParent);

		$.each(subElements, function(i, subElement) {
			subElementObject = $(subElement);
			disable_element_style(subElementObject);
		});
	}

	function enable_element(currentElement) {
		var currentElementParent = currentElement.parent().parent();

		enable_element_style(currentElement);

		var subElements = $('ul:first div a', currentElementParent);
		$.each(subElements, function(i, subElement) {
			enable_element_style($(subElement));
		});
	}

	function add_events(elem) {
		var parent = elem.parent();
		while (parent.attr("class") != "inactive_elements"
				&& parent.attr("class") != "active_elements") {
			parent = parent.parent();
		}
		var name_array = parent.attr("id").split('_').slice(0, -1);
		var name = "";
		$.each(name_array, function(index, name_array_elem) {
			if (index != 0)
				name += "_";
			name += name_array_elem;
		});
		var parent_element = $("#" + name + "_active");
		var sub_elements = $("a", parent_element);
		$.each(sub_elements, function(i, sub_element) {
			var sub_elem = $(sub_element);
			sub_elem.unbind("click");
			sub_elem.click(function() {
				toggle_other_groups(sub_elem);
			});
		});
	}

	function toggle_other_groups_by_id(id, elem_type_id, parent_class,
			orig_elem) {
		$("[id|=" + id + "]")
				.each(
						function() {
							var elem_type = $(this);
							var skip_elem = orig_elem;
							if (orig_elem !== false)
								skip_elem = elem_type[0] == orig_elem[0];
							if (elem_type.attr("id") == id && !skip_elem) {
								var parent_type = elem_type.parent();
								while (parent_type.attr("class") != "inactive_elements"
										&& parent_type.attr("class") != "active_elements") {
									parent_type = parent_type.parent();
								}
								var type_id = parent_type.attr("id").split('_')[1];
								if (((elem_type_id == "request"
										|| elem_type_id == "direct" || elem_type_id == "code") && (type_id == "request"
										|| type_id == "direct" || type_id == "code"))
										|| ((elem_type_id == "creation" || elem_type_id == "creationrequest") && (type_id == "creation" || type_id == "creationrequest"))) {
									if (parent_type.attr("class") == "inactive_elements"
											&& parent_class == "inactive_elements")
										disable_element(elem_type);
									else if (parent_type.attr("class") == "inactive_elements"
											&& parent_class == "active_elements")
										enable_element(elem_type);
								}
							}
						});
	}

	function toggle_other_groups(elem) {
		var id = elem.attr("id"), parent = elem.parent();
		while (parent.attr("class") != "inactive_elements"
				&& parent.attr("class") != "active_elements") {
			parent = parent.parent();
		}
		var elem_type_id = parent.attr("id").split('_')[1];
		toggle_other_groups_by_id(id, elem_type_id, parent.attr("class"), elem);
		if (parent.attr("class") == "inactive_elements")
			setTimeout(function() {
				add_events(elem);
			}, 50);
	}

	function disable_locked_groups() {
		if (typeof (fixed_groups) != "undefined") {
			var locked_groups = unserialize(fixed_groups);
			$.each(locked_groups, function(i, group) {
				toggle_other_groups_by_id("group_" + group, "direct",
						"inactive_elements", false);
			});
		}
	}

	function check_disabled_before_toggle() {
		var elem = $(this);
		if (elem.attr("class").split(' ').slice(-1) != "disabled")
			toggle_other_groups(elem);
		else
			return false;
	}

	$.fn.init_everybody = function() {
		var elem = $(this);
		toggle_others(elem);
	}

	$.fn.bind_everybody = function() {
		var elem = $(this);
		elem.click(function() {
			toggle_others(elem);
		});
	}

	$.fn.init_available_checkbox = function() {
		return this.each(function() {
			var elem = $(this);
			change_block(elem);
		});
	}

	$.fn.init_disable_other_groups = function() {
		disable_locked_groups();
		return this.each(function() {
			var elem = $(this);
			if (elem.attr("class").split(' ').slice(-1) == "disabled")
				toggle_other_groups(elem);
		});
	}

	function reset(evt, ui) {
		setTimeout(function() {
			$('.available').init_available_checkbox();
			$("input[name=direct_target_groups_option]").init_everybody();
			$("input[name=request_target_groups_option]").init_everybody()
			$("a.type").init_disable_other_groups();
		}, 30);
	}

	$(document).ready(function() {
		$(document).on('click', '.available', function() {
			change_block($(this))
		});
		$('.available').init_available_checkbox();
		$("input[name=direct_target_groups_option]").bind_everybody()
		$("input[name=request_target_groups_option]").bind_everybody()
		$("input[name=direct_target_groups_option]").init_everybody();
		$("input[name=request_target_groups_option]").init_everybody();
		$("a.type").click(check_disabled_before_toggle);
		$("a.type").init_disable_other_groups();
		$(document).on('click', ':reset', reset);
	});
});