(function () {
    var learningPathHtmlTreeApp = angular.module('learningPathHtmlTreeApp');

    learningPathHtmlTreeApp.controller(
        'learningPathHtmlTreeController',
        ['$scope', '$http', 'canEditTree', 'inReportingMode', 'treeData',
            'addTreeNodeAjaxUrl', 'deleteTreeNodeAjaxUrl', 'fetchTreeNodesAjaxUrl', 'moveTreeNodeAjaxUrl', 'updateTreeNodeTitleAjaxUrl', 'translationsJSON',
            function ($scope, $http, canEditTree, inReportingMode, treeData, addTreeNodeAjaxUrl, deleteTreeNodeAjaxUrl, fetchTreeNodesAjaxUrl, moveTreeNodeAjaxUrl, updateTreeNodeTitleAjaxUrl, translationsJSON) {


            var translations = JSON.parse(translationsJSON);

            $scope.canEditTree = canEditTree;
            $scope.inReportingMode = inReportingMode;
            $scope.isLoading = false;
            $scope.showError = false;
            $scope.treeData = treeData;

            var extractContextMenuItemFromNodeAction = function(node, action) {
                return extractContextMenuItemFromActionsArray(node.data.actions, action);
            };

            var extractContextMenuItemFromActionsArray = function(actionsArray, action) {
                if(actionsArray[action] === undefined) return null;

                return createContextMenuItemFromAction(actionsArray[action]);
            };

            var createContextMenuItemFromAction = function(actionObject) {
                return {
                    name: actionObject.title,
                    icon: actionObject.image,
                    callback: function () {
                        if(actionObject.confirm)
                        {
                            var result = confirm(actionObject.confirmation_message);
                            if(result === false)
                            {
                                return;
                            }
                        }

                        window.location.href = actionObject.url;
                    }
                }
            };

            /**
             * Helper function: add new node server side and refresh tree
             * @param node
             * @param node_type
             */
            var addNodeToTreeAndStartTitleEdit = function(node, node_type) {
                $scope.isLoading = true;
                var params = {
                    'parent_node_id': node.key,
                    'node_type': node_type
                };

                if(!node.folder) {
                    params['display_order'] = node.getIndex() + 2;
                }

                $http.post(
                    addTreeNodeAjaxUrl,
                    $.param(params),
                    {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                ).then(function successCallback(response) {
                        node.setExpanded();
                        reloadTree(response.data.treeData).then(function () {
                                $scope.isLoading = false;
                                $scope.addingNewNode = true;
                                tree.getNodeByKey(response.data.nodeId).editStart();
                                selectInputField();
                            }
                        );
                    }, function errorCallback(response) {
                        $scope.isLoading = false;
                        $scope.showError = true;
                    }
                );
            };

            var getContextMenuItems = function(node) {
                var items = {};
                var actions = [];

                if ($scope.canEditTree && !$scope.inReportingMode) {
                    items["addSection"] =
                        {
                            name: translations['AddNewSection'],
                            icon: "fa-plus",
                            callback: function () {
                                addNodeToTreeAndStartTitleEdit(node, 'Chamilo\\Core\\Repository\\ContentObject\\Section\\Storage\\DataClass\\Section');
                            }
                        };

                    items["addItem"] = {
                        name: translations['AddNewPage'],
                        icon: "fa-plus",
                        callback: function () {
                            addNodeToTreeAndStartTitleEdit(node, 'Chamilo\\Core\\Repository\\ContentObject\\Page\\Storage\\DataClass\\Page');
                        }
                    };
                    items['sep0'] = '-';

                    var createActions = [];

                    if(node.data.actions['create'] !== undefined) {
                        node.data.actions['create'].forEach(function(action) {
                            createActions.push(createContextMenuItemFromAction(action));
                        });
                    }

                    items["create"] = {
                      name: translations['Create'],
                      icon: 'fa-plus',
                      items: createActions
                    };

                    var addFromItems = [];
                    addFromItems.push(extractContextMenuItemFromNodeAction(node, 'browse_repository'));
                    addFromItems.push(extractContextMenuItemFromNodeAction(node, 'browse_workspaces'));

                    items['addFrom'] = {
                      name: translations['AddFrom'],
                      icon: 'fa-refresh',
                      items: addFromItems
                    };

                    var importActions = [];

                    if(node.data.actions['import'] !== undefined) {
                        node.data.actions['import'].forEach(function(action) {
                            importActions.push(createContextMenuItemFromAction(action));
                        });
                    }

                    console.log(importActions);

                    items['import'] = {
                        name: translations['Import'],
                        icon: 'fa-upload',
                        items: importActions
                    };

                    items['sep1'] = '-';

                    if($scope.quickEditStructureEnabled) {
                        items["editStructure"] = {
                            name: translations['StopStructureQuickEditMode'],
                            icon: "fa-random",
                            callback: function () {
                                $scope.disableQuickEditStructure();
                            }
                        }
                    } else {
                        items["editStructure"] = {
                            name: translations['StartStructureQuickEditMode'],
                            icon: "fa-random",
                            callback: function () {
                                $scope.enableQuickEditStructure();
                            }
                        }
                    }

                    items['sep2'] = '-';

                    items["editTitle"] = {
                        name: translations['EditTitle'], icon: "fa-edit", callback: function () {
                            $scope.addingNewNode = false;
                            node.editStart();
                            selectInputField();
                        }
                    };

                    if($scope.quickEditStructureEnabled) {
                        items["quickDelete"] = {
                            name: translations['Remove'], icon: "fa-remove", callback: function () {
                                confirm(translations['Confirm']);
                                var params = {
                                    'node_id': node.key
                                };
                                $http.post(
                                    deleteTreeNodeAjaxUrl,
                                    $.param(params),
                                    {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                                ).then(function successCallback(response) {
                                        node.remove();
                                        $scope.isLoading = false;
                                    }, function errorCallback(response) {
                                        $scope.isLoading = false;
                                        $scope.showError = true;
                                    }
                                );
                            }
                        };
                        actions = ['edit'];
                    } else {
                        actions = [
                            'edit', 'delete', 'move', 'manage', '-',
                            'buildAssessment', 'setAssessmentMasteryScore', 'configureAssessment',
                            'forumSubscribe', '-',
                            'block', 'default_traversing_order', '-', 'reporting', 'progress', 'activity'
                        ];
                    }
                }
                else if (!$scope.inReportingMode) {
                    actions = ['progress', 'activity'];
                }
                else if($scope.canEditTree && $scope.inReportingMode)
                {
                    actions = ['view', '-', 'reporting', 'progress'];
                }
                else if($scope.inReportingMode)
                {
                    actions = ['view', 'progress'];
                }

                var separatorCounter = 3;
                var previousIsSeperator = true;

                actions.forEach(function (action) {
                    if (action === '-' && !previousIsSeperator) {
                        items['sep' + separatorCounter] = '-';
                        separatorCounter++;
                        previousIsSeperator = true;
                    }
                    else {
                        var item = extractContextMenuItemFromNodeAction(node, action);
                        if (item !== null) {
                            items[action] = item;
                            previousIsSeperator = false;
                        }
                    }
                });

                return items;
            };

            var buildContextMenu = function () {
                $.contextMenu({
                    selector: ".fancytree-title",
                    zIndex: 10001,
                    minWidth: '202px',
                    build: function ($triggerElement, e) {
                        node = $.ui.fancytree.getNode($triggerElement);
                        if (node) {
                            node.setFocus(true);
                        }
                        return {
                            items: getContextMenuItems(node)
                        };
                    }
                });
            };

            buildContextMenu(); //@todo move to init code

            $scope.quickEditStructureEnabled = false;

            var reloadTree = function(treeData) {
                $scope.treeData = treeData;
                return tree.reload($scope.treeData);
            };

            var reloadTreeWithFreshData = function() {
                $scope.isLoading = true;
                var activeNode =  tree.getActiveNode();
                if(activeNode) {
                    activeNodeId = activeNode.key;
                }
                else {
                    activeNodeId = 0;
                }
                var params = {
                    'active_child_id': activeNodeId
                };
                $http.post(
                    fetchTreeNodesAjaxUrl,
                    $.param(params),
                    {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                ).then(function successCallback(response) {
                        $scope.isLoading = false;
                        $scope.treeData = response.data;
                        return tree.reload($scope.treeData);
                    }, function errorCallback(response) {
                        $scope.isLoading = false;
                        $scope.showError = true;
                    }
                );
            };

            var selectInputField = function() {
                $('.fancytree-edit-input').select();
            };

            var treeSelector = $('#tree');

            treeSelector.fancytree({
                keyboard: false,
                source: $scope.treeData,
                glyph: {
                    map: {
                        checkbox: "glyphicon glyphicon-unchecked",
                        checkboxSelected: "glyphicon glyphicon-check",
                        checkboxUnknown: "glyphicon glyphicon-share",
                        dragHelper: "glyphicon glyphicon-play",
                        dropMarker: "glyphicon glyphicon-arrow-right",
                        error: "glyphicon glyphicon-warning-sign",
                        expanderClosed: "glyphicon glyphicon-chevron-right",
                        expanderLazy: "glyphicon glyphicon-plus-sign",
                        expanderOpen: "glyphicon glyphicon-chevron-down",
                        loading: "glyphicon glyphicon-refresh glyphicon-spin",
                        nodata: "glyphicon glyphicon-info-sign",
                        // Default node icons.
                        // (Use tree.options.icon callback to define custom icons based on node data)
                        doc: "glyphicon glyphicon-file",
                        docOpen: "glyphicon glyphicon-file",
                        folder: "glyphicon glyphicon-folder-close",
                        folderOpen: "glyphicon glyphicon-folder-open"
                    }
                },
                dnd: {
                    // Available options with their default:
                    autoExpandMS: 250,   // Expand nodes after n milliseconds of hovering
                    draggable: null,      // Additional options passed to jQuery UI draggable
                    droppable: null,      // Additional options passed to jQuery UI droppable
                    dropMarkerOffsetX: -24,  // absolute position offset for .fancytree-drop-marker
                    // relatively to ..fancytree-title (icon/img near a node accepting drop)
                    dropMarkerInsertOffsetX: -16, // additional offset for drop-marker with hitMode = "before"/"after"
                    focusOnClick: false,  // Focus, although draggable cancels mousedown event (#270)
                    preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
                    preventVoidMoves: true,      // Prevent dropping nodes 'before self', etc.
                    smartRevert: true,    // set draggable.revert = true if drop was rejected

                    // Events that make tree nodes draggable
                    dragStart: function (node, data) {
                        if(!$scope.quickEditStructureEnabled) {
                            return false;
                        }

                        if(node.getParent().isRoot()) { //fancytree has an invisible root
                            return false; // do not drag the learning path root
                        }
                        return true;
                    },
                    initHelper: null,     // Callback(sourceNode, data)
                    updateHelper: null,   // Callback(sourceNode, data)

                    // Events that make tree nodes accept draggables
                    dragEnter: function (node, data) {
                        if(node.getParent().isRoot()) {
                            return [];
                        }
                        if (!node.isFolder()) {
                            return ["before", "after"];
                        }
                        return true;
                    },      // Callback(targetNode, data)
                    dragExpand: function(node, data) {
                        // return false to prevent auto-expanding data.node on hover
                    },
                    dragOver: function(node, data) {
                    },
                    dragLeave: function(node, data) {
                    },
                    dragStop: function(node, data) {
                    },
                    dragDrop: function(node, data) {
                        $scope.isLoading = true;
                        data.otherNode.moveTo(node, data.hitMode);

                        var displayOrder = data.otherNode.getIndex() + 1;
                        var parentId = data.otherNode.getParent().key;
                        var childId = data.otherNode.key;

                        //persist
                        $http.post(
                            moveTreeNodeAjaxUrl,
                            $.param({'child_id': childId, 'parent_id': parentId, 'display_order': displayOrder}),
                            {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                        ).then(function successCallback(response) {
                                $scope.isLoading = false;
                            }, function errorCallback(response) {
                                $scope.isLoading = false;
                                $scope.showError = true;
                                reloadTreeWithFreshData(); //reset tree
                            }
                        );
                    }
                },
                minExpandLevel: 2,
                selectMode: 1,
                checkbox: false,
                extensions: ["dnd", "edit", "glyph", "persist"],
                toggleEffect: false,
                persist: {
                    store: "local", // 'cookie', 'local': use localStore, 'session': sessionStore
                    types: 'expanded'
                },
                enhanceTitle: function (event, data) {
                    data.$title.addClass("ws-wrap");
                },
                click: function (event, data) {
                    if((data.targetType === 'title' && !$(event.originalEvent.target).hasClass('fancytree-title')) || data.targetType === undefined) {
                        return false; //if the click is on the left or on the icon, leave the node alone...
                    }

                    var node = data.node;

                    if (!$scope.quickEditStructureEnabled && event.button !== 2 && $(event.originalEvent.target).hasClass('fancytree-title')) {
                        if (node.data.href) {
                            window.location.href = node.data.href;
                        }
                    }
                    if (event.button === 2) {
                        return false;
                    }
                },
                renderTitle: function (event, data) {
                    var title = data.node.title;

                    if($scope.canEditTree && !$scope.inReportingMode) {
                        var stepBlocked = data.node.data.step_blocked;

                        if(stepBlocked) {
                            title +=  '<span class="inline-glyph fas fa-lightbulb fa-fw text-primary"></span>';
                        }
                    }

                    if(data.node.data.number && !$scope.quickEditStructureEnabled) {
                        return '<span class="fancytree-title">' + data.node.data.number + ' ' + title + '</span>';
                    } else {
                        return '<span class="fancytree-title">' + title + '</span>';
                    }

                },
                edit: {
                    save: function(event, data) {
                        $scope.isLoading = true;
                        var node = data.node;
                        var params = {
                            'new_title': data.input.val(),
                            'child_id': node.key
                        };

                        $http.post(
                            updateTreeNodeTitleAjaxUrl,
                            $.param(params),
                            {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                        ).then(function successCallback(response) {
                                if($scope.addingNewNode && !$scope.quickEditStructureEnabled) { //when adding a new node and editing the title, we want a redirect to the edit page
                                    window.location.href = node.data.actions.edit.url;
                                    $scope.addingNewNode = false;
                                } else {
                                    $scope.isLoading = false;
                                }
                            }, function errorCallback(response) {
                                node.setTitle(data.orgTitle);
                                $scope.isLoading = false;
                                $scope.showError = true;
                            }
                        );
                    }
                }

            });

            var tree = treeSelector.fancytree('getTree');

            $scope.enableQuickEditStructure = function() {
                $scope.quickEditStructureEnabled = true;
                reloadTreeWithFreshData();
            };

            $scope.disableQuickEditStructure = function () {
                $scope.quickEditStructureEnabled = false;
                reloadTreeWithFreshData();
            }
        }]
    );
})();