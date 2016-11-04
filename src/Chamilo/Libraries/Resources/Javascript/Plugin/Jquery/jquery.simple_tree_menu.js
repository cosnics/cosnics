/**
 * Copyright (c) 2010, Sven Vanpoucke, Chamilo tree menu in jQuery plugin 
 */

(function($){
	$.fn.extend({ 
		tree_menu: function(options) {

			//Settings list and the default values
			var defaults = {
					search: ''
			};
			
			var settings = $.extend(defaults, options), self = $(this);
			
			function collapseItem(e) 
			{
				$("ul:first", $(this).parent()).hide();
				if ($(this).hasClass("lastCollapse"))
				{
					$(this).removeClass("lastCollapse");
					$(this).addClass("lastExpand");
				}
				else if ($(this).hasClass("collapse"))
				{
					$(this).removeClass("collapse");
					$(this).addClass("expand");
				}
			}
			
			function expandItem(e) 
			{
				$("ul:first", $(this).parent()).show();
				changeExpandItemIcon($(this));
			}
			
			function changeExpandItemIcon(item)
			{
				if (item.hasClass("lastExpand"))
				{
					item.removeClass("lastExpand");
					item.addClass("lastCollapse");
				}
				else if (item.hasClass("expand"))
				{
					item.removeClass("expand");
					item.addClass("collapse");
				}
			}
			
			function expandItemAndLoadChildren(e)
			{
				var id = $('a', $(this)).attr("id");
				var parent = $(this).parent();
				var children = getChildren(id);
				
				if(children)
				{
					parent.append(children);
					changeExpandItemIcon($(this));
					$(this).unbind('click');
					processTree($(this).parent().parent().parent());
				}
			}
			
			function getChildren(parent_id)
			{
				if(settings.search == '')
				{
					return '';
				}
				
				var ul = $('<ul></ul>');
				var response = loadChildren(parent_id);
				var tree = $.xml2json(response, true);
				
				if((tree.leaf && $(tree.leaf).size() > 0))
				{
					$.each(tree.leaf, function(i, the_leaf)
					{
						var expand = '';
						if(the_leaf.has_children == '1')
						{
							expand = ' class="expand"';
						}
						var li = $('<li><div' + expand + '><a href="#" id="' + the_leaf.id + '" class="' + the_leaf.classes + '">' + the_leaf.title + '</a></div></li>');
						$(ul).append(li);
					});
					
					return ul;
				}
			}
			
			function loadChildren(parent_id)
			{
				var response = $.ajax({
					type: "GET",
					dataType: "xml",
					url: settings.search,
					data: { parent_id: parent_id },
					async: false
				}).responseText;
				
				return response;
			}
			
			function processTree(parent)
			{
				$("ul li:last-child > div", parent).addClass("last"); 
				$("ul li:last-child > div.expand", parent).addClass("lastExpand");
				$("ul li:last-child > div.expand", parent).removeClass("expand");
				$("ul li:last-child > ul", parent).css("background-image", "none");

				$("ul li:not(:last-child):has(ul) > div", parent).addClass("expand");
				$("ul li:not(:last-child):has(ul) > div", parent).removeClass("collapse");
				$("ul li:not(:last-child):has(ul) > ul", parent).hide();
				
				$("ul li:last-child:has(ul) > div", parent).addClass("lastExpand");
				$("ul li:last-child:has(ul) > div", parent).removeClass("lastCollapse");
				$("ul li:last-child:has(ul) > ul", parent).hide();

				$("ul li:has(ul) > div", parent).toggle(expandItem, collapseItem);

				$("div.lastExpand, div.expand", $("ul li:not(:has(ul))", parent)).bind('click', expandItemAndLoadChildren);

				$("ul li:has(ul) > div > a", parent).click(function(e){e.stopPropagation();});
			}
			
			function init()
			{
				processTree(self.parent());
			}
			
			return this.each(init);
    	}
	});
})(jQuery);
