(function () {
    function setupFancyTree(context) {
        $('#tree').fancytree({
            aria: true,
            keyboard: true,
            source: context.treeData,
            glyph: {
                map: {
                    checkbox: 'glyphicon glyphicon-unchecked',
                    checkboxSelected: 'glyphicon glyphicon-check',
                    checkboxUnknown: 'glyphicon glyphicon-share',
                    dragHelper: 'glyphicon glyphicon-play',
                    dropMarker: 'glyphicon glyphicon-arrow-right',
                    error: 'glyphicon glyphicon-warning-sign',
                    expanderClosed: 'glyphicon glyphicon-chevron-right',
                    expanderLazy: 'glyphicon glyphicon-plus-sign',
                    expanderOpen: 'glyphicon glyphicon-chevron-down',
                    loading: 'glyphicon glyphicon-refresh glyphicon-spin',
                    nodata: 'glyphicon glyphicon-info-sign',
                    // Default node icons.
                    // (Use tree.options.icon callback to define custom icons based on node data)
                    doc: 'glyphicon glyphicon-file',
                    docOpen: 'glyphicon glyphicon-file',
                    folder: 'glyphicon glyphicon-folder-close',
                    folderOpen: 'glyphicon glyphicon-folder-open'
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
                dragStart: function (node) {
                    if (!context.isEditable) {
                        return false;
                    }
                    // fancytree has an invisible root
                    // do not drag the learning path root
                    return !node.getParent().isRoot();
                },
                initHelper: null,     // Callback(sourceNode, data)
                updateHelper: null,   // Callback(sourceNode, data)

                // Events that make tree nodes accept draggables
                dragEnter: function (node, data) {
                    if (node.getParent().isRoot()) {
                        return [];
                    }
                    if (!node.isFolder()) {
                        return ['before', 'after'];
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
                    data.otherNode.moveTo(node, data.hitMode);
                    context.moveTreeNode(data.otherNode);
                }
            },
            minExpandLevel: 2,
            selectMode: 1,
            checkbox: false,
            extensions: ['dnd', 'edit', 'glyph', 'persist'],
            toggleEffect: false,
            persist: {
                store: 'local', // 'cookie', 'local': use localStore, 'session': sessionStore
                types: 'expanded'
            },
            enhanceTitle: function (event, data) {
                data.$title.addClass('ws-wrap');
            },
            click: function (event, data) {
                if ((data.targetType === 'title' && !event.originalEvent.target.classList.contains('fancytree-title')) || data.targetType === undefined) {
                    return false; //if the click is on the left or on the icon, leave the node alone...
                }

                const node = data.node;

                if (!context.isEditable && event.button !== 2 && event.originalEvent.target.classList.contains('fancytree-title')) {
                    if (node.data.href) {
                        window.location.href = node.data.href;
                    }
                }
                if (event.button === 2) {
                    return false;
                }
            },
            renderTitle: function (event, data) {
                return `<span class="fancytree-title">${ context.renderTitle(data.node) }</span>`;
            },
            edit: {
                save: function(event, data) {
                    context.updateTreeNodeTitle(data.node, data.input.val(), data.orgTitle);
                }
            }
        });
        return $.ui.fancytree.getTree("#tree");
    }

    function createFancyTreeContext(vueInstance) {
        return {
            get treeData() {
                return vueInstance.treeData;
            },
            get isEditable() {
                return vueInstance.quickEditStructureEnabled;
            },
            renderTitle: vueInstance.renderTitle.bind(vueInstance),
            moveTreeNode: vueInstance.moveTreeNode.bind(vueInstance),
            updateTreeNodeTitle: vueInstance.updateTreeNodeTitle.bind(vueInstance)
        };
    }

    function createContextMenu(context) {
        $.contextMenu({
            selector: '.fancytree-title',
            zIndex: 10001,
            minWidth: '202px',
            build: $triggerElement => {
                const node = $.ui.fancytree.getNode($triggerElement);
                if (node) {
                    node.setFocus(true);
                }
                return {
                    items: context.getContextMenuItems(node)
                };
            }
        });
    }

    const extractContextMenuItemFromNodeAction = function (node, action) {
        return extractContextMenuItemFromActionsArray(node.data.actions, action);
    };

    const extractContextMenuItemFromActionsArray = function (actionsArray, action) {
        if (actionsArray[action] === undefined) { return null; }

        return createContextMenuItemFromAction(actionsArray[action]);
    };

    const createContextMenuItemFromAction = function (actionObject) {
        return {
            name: actionObject.title,
            icon: actionObject.image,
            callback: function () {
                if (actionObject.confirm && !confirm(actionObject.confirmation_message)) { return; }
                window.location.href = actionObject.url;
            }
        }
    };

    Vue.component('learning-path-html-tree', {
        template: '<div id="tree"></div>',
        props: ['appData', 'quickEditStructureEnabled'],
        data: function () {
            return {
                canEditTree: false,
                canViewReporting: false,
                inReportingMode: false,
                treeData: [],
                addingNewNode: false,
                apiConfig: {},
                translations: {},
                tree: null
            };
        },
        mounted() {
            this.setupData();
        },
        methods: {
            setupData: function () {
                const {canEditTree, canViewReporting, inReportingMode, treeData, apiConfig, translations} = this.appData;
                this.canEditTree = canEditTree;
                this.canViewReporting = canViewReporting;
                this.inReportingMode = inReportingMode;
                this.treeData = treeData;
                this.apiConfig = apiConfig;
                this.translations = translations;
                this.tree = setupFancyTree(createFancyTreeContext(this));
                createContextMenu({
                    getContextMenuItems: this.getContextMenuItems.bind(this)
                });
            },
            beginLoading() {
                this.$emit('is-loading', true);
            },
            endLoading() {
                this.$emit('is-loading', false);
            },
            setError(error) {
                this.$emit('error', true);
            },
            renderTitle: function (node) {
                let title = node.title;
                if (this.canEditTree && !this.inReportingMode && node.data.step_blocked) {
                    title = `${title}<span class="tree-additional-icon step-blocked"></span>`;
                }
                if (node.data.number && !this.quickEditStructureEnabled) {
                    return `${node.data.number} ${title}`;
                }
                return title;
            },
            enableQuickEditStructure: function () {
                this.$emit('quick-edit-enabled', true);
                this.reloadTreeWithFreshData();
            },
            disableQuickEditStructure: function () {
                this.$emit('quick-edit-enabled', false);
                this.reloadTreeWithFreshData();
            },
            moveTreeNode: async function (node) {
                this.beginLoading();
                try {
                    //persist
                    await axios.post(
                        this.apiConfig.moveTreeNodeAjaxUrl,
                        {'child_id': node.key, 'parent_id': node.getParent().key, 'display_order': node.getIndex() + 1},
                        {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                    );
                    this.endLoading();
                } catch (error) {
                    this.endLoading();
                    this.setError(error);
                    await this.reloadTreeWithFreshData(); //reset tree
                }
            },
            updateTreeNodeTitle: async function (node, newTitle, orgTitle) {
                this.beginLoading();
                try {
                    await axios.post(
                        this.apiConfig.updateTreeNodeTitleAjaxUrl,
                        { 'new_title': newTitle, 'child_id': node.key },
                        {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                    );
                    if (this.addingNewNode && !this.quickEditStructureEnabled) { //when adding a new node and editing the title, we want a redirect to the edit page
                        window.location.href = node.data.actions.edit.url;
                        this.addingNewNode = false;
                    } else {
                        this.endLoading();
                    }
                } catch (error) {
                    node.setTitle(orgTitle);
                    this.endLoading();
                    this.setError(error);
                }
            },
            reloadTree: async function (treeData) {
                this.treeData = treeData;
                return this.tree.reload(this.treeData);
            },
            reloadTreeWithFreshData: async function () {
                this.beginLoading();
                const activeNode = this.tree.getActiveNode();
                const activeNodeId = activeNode ? activeNode.key : 0;
                try {
                    const {data} = await axios.post(
                        this.apiConfig.fetchTreeNodesAjaxUrl,
                        { 'active_child_id': activeNodeId },
                        {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                    );
                    this.endLoading();
                    this.treeData = data;
                    this.tree.reload(this.treeData);
                } catch (error) {
                    this.endLoading();
                    this.setError(error);
                }
            },
            /**
             * Helper function: add new node server side and refresh tree
             * @param node
             * @param node_type
             */
            addNodeToTreeAndStartTitleEdit: async function (node, node_type) {
                this.beginLoading();
                const params = {
                    'parent_node_id': node.key,
                    'node_type': node_type
                };

                if (!node.folder) {
                    params['display_order'] = node.getIndex() + 2;
                }

                try {
                    const {data} = await axios.post(
                        this.apiConfig.addTreeNodeAjaxUrl,
                        params,
                        {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                    );
                    node.setExpanded();
                    await this.reloadTree(data.treeData);
                    this.endLoading();
                    this.addingNewNode = true;
                    this.tree.getNodeByKey(data.nodeId).editStart();
                    this.selectInputField();
                } catch (error) {
                    this.endLoading();
                    this.setError(error);
                }
            },
            selectInputField: function () {
                document.querySelector('.fancytree-edit-input').select();
            },
            getContextMenuItems: function (node) {
                const items = {};
                let actions = [];

                if (this.canEditTree && !this.inReportingMode) {
                    items['addSection'] = {
                        name: this.translations['AddNewSection'],
                        icon: 'fa-plus',
                        callback: () => {
                            this.addNodeToTreeAndStartTitleEdit(node, 'Chamilo\\Core\\Repository\\ContentObject\\Section\\Storage\\DataClass\\Section');
                        }
                    };
                    items['addItem'] = {
                        name: this.translations['AddNewPage'],
                        icon: 'fa-plus',
                        callback: () => {
                            this.addNodeToTreeAndStartTitleEdit(node, 'Chamilo\\Core\\Repository\\ContentObject\\Page\\Storage\\DataClass\\Page');
                        }
                    };
                    items['sep0'] = '-';

                    const createActions = [];

                    if (node.data.actions['create'] !== undefined) {
                        node.data.actions['create'].forEach(action => {
                            createActions.push(createContextMenuItemFromAction(action));
                        });
                    }

                    items['create'] = {
                        name: this.translations['Create'],
                        icon: 'fa-plus',
                        items: createActions
                    };

                    const addFromItems = [];
                    addFromItems.push(extractContextMenuItemFromNodeAction(node, 'browse_repository'));
                    addFromItems.push(extractContextMenuItemFromNodeAction(node, 'browse_workspaces'));

                    items['addFrom'] = {
                        name: this.translations['AddFrom'],
                        icon: 'fa-refresh',
                        items: addFromItems
                    };

                    const importActions = [];

                    if (node.data.actions['import'] !== undefined) {
                        node.data.actions['import'].forEach(action => {
                            importActions.push(createContextMenuItemFromAction(action));
                        });
                    }

                    items['import'] = {
                        name: this.translations['Import'],
                        icon: 'fa-upload',
                        items: importActions
                    };

                    items['sep1'] = '-';

                    if (this.quickEditStructureEnabled) {
                        items['editStructure'] = {
                            name: this.translations['StopStructureQuickEditMode'],
                            icon: 'fa-random',
                            callback: () => this.disableQuickEditStructure()
                        }
                    } else {
                        items['editStructure'] = {
                            name: this.translations['StartStructureQuickEditMode'],
                            icon: 'fa-random',
                            callback: () => this.enableQuickEditStructure()
                        }
                    }

                    items['sep2'] = '-';

                    items['editTitle'] = {
                        name: this.translations['EditTitle'],
                        icon: 'fa-edit',
                        callback: () => {
                            this.addingNewNode = false;
                            node.editStart();
                            this.selectInputField();
                        }
                    };

                    if (this.quickEditStructureEnabled) {
                        items['quickDelete'] = {
                            name: this.translations['Remove'],
                            icon: 'fa-remove',
                            callback: async () => {
                                if (confirm(this.translations['Confirm'])) {
                                    this.beginLoading();
                                    try {
                                        await axios.post(
                                            this.apiConfig.deleteTreeNodeAjaxUrl,
                                            { 'node_id': node.key },
                                            {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
                                        );
                                        node.remove();
                                        this.endLoading();
                                    } catch (error) {
                                        this.endLoading();
                                        this.setError(error);
                                    }
                                }
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
                else if (!this.inReportingMode && this.canViewReporting) {
                    actions = ['reporting', 'progress', 'activity'];
                }
                else if (!this.inReportingMode) {
                    actions = ['progress', 'activity']
                }
                else if (this.canViewReporting && this.inReportingMode) {
                    actions = ['view', '-', 'reporting', 'progress'];
                }
                else if (this.inReportingMode) {
                    actions = ['view', 'progress'];
                }

                let separatorCounter = 3;
                let previousIsSeparator = true;

                actions.forEach(function (action) {
                    if (action === '-' && !previousIsSeparator) {
                        items['sep' + separatorCounter] = '-';
                        separatorCounter++;
                        previousIsSeparator = true;
                    }
                    else {
                        const item = extractContextMenuItemFromNodeAction(node, action);
                        if (item) {
                            items[action] = item;
                            previousIsSeparator = false;
                        }
                    }
                });

                return items;
            }
        }
    });
})();
