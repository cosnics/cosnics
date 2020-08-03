/**
 * Created by Minas on 12/03/14.
 */
$(function(){
    var webPath = getPath('WEB_PATH');

    //Boolean to see if the parents of publications and subcategories should be checked
    //With CourseCopier should be True, with CourseTruncater should be false
    var checkParents = true;
    var addedTools = [];
    var categories;
    var publications;
    var translations;

    var getHiddenCategoriesCheckboxes = function() {
        if(typeof this.hiddenCategoriesCheckboxes == "undefined")
            this.hiddenCategoriesCheckboxes = $('#categories input');

        return this.hiddenCategoriesCheckboxes;
    };

    var getHiddenCategoryCheckbox = function(id) {
        var hiddenCheckboxes = getHiddenCategoriesCheckboxes();

        var foundCheckbox;
        for(var i=0; i< hiddenCheckboxes.length; i++)
        {
            var checkbox = hiddenCheckboxes[i];
            if(checkbox.getAttribute('name') == "categories[" + id + "]")
            {
                foundCheckbox = checkbox;
                break;
            }
        }

        return foundCheckbox;
    };

    var getHiddenPublicationsCheckboxes = function() {
        if(typeof this.hiddenPublicationsCheckboxes == "undefined")
            this.hiddenPublicationsCheckboxes = $('#publications input');

        return this.hiddenPublicationsCheckboxes;
    }

    var getHiddenPublicationCheckbox = function(id) {
        var hiddenCheckboxes = getHiddenPublicationsCheckboxes();

        var foundCheckbox;
        for(var i=0; i< hiddenCheckboxes.length; i++)
        {
            var checkbox = hiddenCheckboxes[i];
            if(checkbox.getAttribute('name') == "publications[" + id + "]")
            {
                foundCheckbox = checkbox;
                break;
            }
        }

        return foundCheckbox;
    };

    var getData = function() {
        categories = JSON.parse($('#categoriesJSON').text());
        publications = JSON.parse($('#publicationsJSON').text());

        var list = [
            {id:"course_node", parent:"#", text:$('#courseName').text(), state:{"opened":true},
                icon: webPath + "/Chamilo/Application/Weblcms/Resources/Images/Aqua/Logo/22.png"
            }
        ];

        for(var i = 0; i < categories.length; i++)
        {
            var category = categories[i];
                addCategory({id : category.id, name : category.name, tool : category.tool,
                    parent_id : category.parent_id}, list);
        }

        for(var i = 0; i< publications.length; i++)
        {
            var publication = publications[i];
            if(publication.category_id == (null || 0))
            {
                publication.category_id = 0;
            }

            addPublication(publication, list);
        }

        return list;
    };

    var addTool = function(toolName, list) {
        var node = {};
        var iconUrl = '';

        if(toolName === 'LectureCapture')
        {
            iconUrl = webPath + "/Hogent/Application/Weblcms/Tool/Implementation/" + toolName +
                "/Resources/Images/Aqua/Logo/22.png";
        }
        else {
            iconUrl = webPath + "/Chamilo/Application/Weblcms/Tool/Implementation/" + toolName +
                "/Resources/Images/Aqua/Logo/22.png";
        }

        node.id = getToolNodeId(toolName);
        node.icon = iconUrl;
        node.text = getTranslation(toolName);
        node.parent = "course_node";
        node.state = {'opened' : false};
        node.li_attr = {class : "tool_node"};

        addedTools.push(toolName);
        list.push(node);
    };

    var getToolNodeId = function(toolName)
    {
        return toolName + "_node";
    };

    var checkIfToolWasAdded = function (toolName)
    {
        var toolFound = false;
        for(var i = 0; i < addedTools.length; i++)
        {
            if(addedTools[i] == toolName)
            {
                toolFound = true;
                break;
            }
        }
        return toolFound;
    }

    var addCategory = function(category, list) {
        var node = {};
        node.id = getCategoryNodeId(category.id);
        //node.icon = webPath +
        node.text = category.name;
        node.state = {'opened' : false, selected : getHiddenCategoryCheckbox(category.id).checked};
        node.category_id = category.id;
        node.li_attr = {class : "category_node"};

        if(category.parent_id == (null || 0))
        {
            if(!checkIfToolWasAdded(category.tool))
                addTool(category.tool, list);

            node.parent = getToolNodeId(category.tool);
        }
        else
            node.parent = getCategoryNodeId(category.parent_id);

        list.push(node);
    };

    var getCategoryNodeId = function(id) {
        return "category_" + id;
    };

    var addPublication = function(publication, list) {
        var node = {};

        node.id = getPublicationNodeId(publication.id);
        node.state = {selected : getHiddenPublicationCheckbox(publication.id).checked};
        node.text = publication.title;
        node.icon = webPath + "Chamilo/Configuration/Resources/Images/Aqua/Action/Item.png";
        node.publication_id = publication.id;
        node.li_attr = {class : "publication_node"};

        if(publication.category_id == 0)
        {
            if(!checkIfToolWasAdded(publication.tool))
                addTool(publication.tool, list);

            node.parent = getToolNodeId(publication.tool);
        }
        else
            node.parent = getCategoryNodeId(publication.category_id);

        list.push(node);
    };

    var getPublicationNodeId = function(id){
        return "node_" + id;
    };

    /**
     *
     * @param object model: contains the modelData
     * @param string selectedCheckbox
     *
     * Will check if the parent of the selected checkbox is a category.
     * If the parent is a category, it will set it the parent's checkbox on true and check its parents.
     */
    var checkAndSelectParent = function(model, selectedCheckbox) {
        if(! checkParents)
            return;

        var node = model[selectedCheckbox];
        var parent = model[node.parent];

        if(parent.li_attr.class == "category_node")
        {
            var hiddenCheckbox = getHiddenCategoryCheckbox(parent.original.category_id);
            if(!hiddenCheckbox.checked)
            {
                hiddenCheckbox.checked = true;
                checkAndSelectParent(model, parent.id);
            }
        }
    };

    var initializeTree = function() {
        //$('#categories').hide();
        //$('#publications').hide();
        checkParents = JSON.parse($('#checkParentsBoolean').text());

        var publications_tree = $('#publications_tree');
        publications_tree.jstree({
            "checkbox" : {
                "real_checkboxes": true,
                "keep_selected_style": false
            },
            "core" : {
                'data' : getData()
            },
            "plugins": ["checkbox"]
        });

        /**
         * Will listen to any selection(checkbox checked/unchecked) changes.
         * @param e contains the event
         * @param data contains the action, instance, model and a selected-array
         *
         * On change, it will loop the hidden checkboxes and set them on false.
         * Then it will loop through the selected jstree-checkboxes and set the hidden checkboxes on true.
         * The reason the hidden checkboxes are put on false first is to prevent faulty synchronisation.
         */
        publications_tree.on("changed.jstree", function (e, data) {
            var model = data.instance._model.data;
            var hiddenCategories = getHiddenCategoriesCheckboxes();
            var hiddenPublications = getHiddenPublicationsCheckboxes();
            var hiddenCheckboxes = hiddenCategories;
            hiddenCheckboxes.push.apply(hiddenCategories, hiddenPublications);

            for(var i=0; i < hiddenCheckboxes.length; i++)
            {
                var hiddenCheckbox = hiddenCheckboxes[i];
                if(data.action == "select_all")
                    hiddenCheckbox.checked = true;
                else
                    hiddenCheckbox.checked = false;
            }
            if(data.action == ("select_all" || "deselect_all"))
                return;

            for(var j = 0; j < data.selected.length; j++)
            {
                var selectedCheckbox = data.selected[j];
                if(model[selectedCheckbox].li_attr == null)
                    continue;

                if(model[selectedCheckbox].li_attr.class == "category_node")
                {
                    var hiddenCheckbox = getHiddenCategoryCheckbox(model[selectedCheckbox].original.category_id);
                    hiddenCheckbox.checked = true;
                    checkAndSelectParent(model, selectedCheckbox);
                }

                if(model[selectedCheckbox].li_attr.class=="publication_node")
                {
                    var hiddenCheckbox = getHiddenPublicationCheckbox(model[selectedCheckbox].original.publication_id);
                    hiddenCheckbox.checked = true;
                    checkAndSelectParent(model, selectedCheckbox);
                }
            }
        });

        $('#selectAll').click(function(evt) {
            publications_tree.jstree('select_all');
            //Prevents auto-scrolling to the top of the page
            evt.preventDefault();
        });

        $('#deselectAll').click(function(evt) {
            publications_tree.jstree('deselect_all');
            evt.preventDefault();
        });

        //Even though the state of "opened" is false, it will leave the nodes open
        //Has to do with the fact that they are checked on init
        publications_tree.jstree('close_all');
        publications_tree.jstree('open_node', 'course_node');
    }

    var getTranslation = function(key)
    {
        if(typeof translations == "undefined")
            translations = JSON.parse($("#translations").text());

        if(translations !== null) {
            var value = translations[key];
            if(typeof value == "undefined")
                return key;
            else
                return value;
        }
        else
            return key;
    }

    $(document).ready(initializeTree());
});
