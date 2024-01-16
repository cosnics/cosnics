(function () {
    var learningPathSectionCopierApp = angular.module('learningPathSectionCopierApp');

    learningPathSectionCopierApp.controller(
        'MainController',
        ['$scope', 'RepositoryService', 'LearningPathService', function ($scope, RepositoryService, LearningPathService) {

            var mainController = this;

            this.isLoading = false;
            this.treeData = [];

            this.workspaces = [];
            this.selectedWorkspace = 0;
            this.originalLearningPaths = this.learningPaths = [];
            this.selectedLearningPath = null;
            this.selectedLearningPathNodeIds = null;
            this.categories = [];
            this.selectedCategory = null;
            this.searchQuery = null;

            this.userCategories = [];
            this.selectedCopyCategory = null;

            this.pager = {
                itemsPerPage: 5,
                totalItems: this.learningPaths.length,
                currentPage: 1,
                totalPages: 0,
                getFirstItemOffset: function() { return this.itemsPerPage * (this.currentPage - 1); },
                getLastItemOffset: function() { return this.getFirstItemOffset() + this.itemsPerPage },
                reset: function(learningPaths) {
                    this.currentPage = 1;
                    this.totalItems = learningPaths !== null ? learningPaths.length : 0;
                    this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                }
            };

            this.selectLearningPath = function (learningPath) {
                if (this.selectedLearningPath !== null) {
                    this.selectedLearningPath.selected = false;
                }

                this.selectedLearningPath = learningPath;
                learningPath.selected = true;

                this.fetchTree(this.selectedLearningPath.id);
            };

            this.resetSelectedLearningPath = function() {
                this.selectedLearningPath.selected = false;
                this.selectedLearningPath = null;
                this.selectedLearningPathNodeIds = null;
                this.treeData = [];
                tree.reload(this.treeData);
            };

            this.selectPage = function() {
                this.visibleLearningPaths = this.learningPaths.slice(
                    this.pager.getFirstItemOffset(), this.pager.getLastItemOffset()
                );
            };

            this.searchQuery = null;

            $scope.$watch(
                function() { return mainController.selectedWorkspace; },
                function() {
                    mainController.fetchCategories();
                }
            );

            $scope.$watch(
                function() { return mainController.selectedCategory; },
                function() {
                    mainController.searchQuery = null;

                    if(mainController.selectedCategory !== null) {
                        mainController.fetchLearningPaths(mainController.selectedCategory.id);
                    }
                }
            );

            /** Don't work with scope.watch because the search query can be reset programmatically by the category **/
            this.search = function() {
                if (mainController.searchQuery === null) {
                    if (mainController.selectedCategory !== null) {
                        mainController.fetchLearningPaths(mainController.selectedCategory.id);
                    }
                }
                else {
                    mainController.selectedCategory = null;
                    mainController.fetchLearningPaths(null, mainController.searchQuery);
                }
            };

            this.fetchWorkspaces = function() {
                Repo.fetchWorkspaces(function(data) {
                    mainController.workspaces = data;
                });
            };

            this.fetchLearningPaths = function (categoryId, searchQuery) {
                LearningPathService.fetchLearningPaths(this.selectedWorkspace, categoryId, searchQuery, function(data) {
                    mainController.originalLearningPaths = mainController.learningPaths = data;
                    mainController.pager.reset(mainController.learningPaths);
                    mainController.selectPage();
                });
            };

            this.fetchCategories = function () {
                RepoService.fetchCategories(this.selectedWorkspace, function(categories) {
                    mainController.categories = categories;
                    mainController.selectedCategory = mainController.categories[0];
                    $scope.$apply();
                });
            };

            this.fetchTree = function(learningPathId) {
                LearningPathService.fetchTree(learningPathId, function(data) {
                    mainController.treeData = data;
                    tree.reload(mainController.treeData);
                });
            };

            this.fetchWorkspaces();

            this.canChangeCopyMode = function() {
                var workspace = this.getSelectedWorkspace();
                return workspace === undefined || (workspace !== undefined && workspace.copy_right === true && workspace.use_right === true);
            };

            this.isCopyModeSelectedByDefault = function() {
                var workspace = this.getSelectedWorkspace();
                return workspace !== undefined && workspace.copy_right === true && workspace.use_right === false;
            };

            this.getSelectedWorkspace = function() {
                var main = this;
                var selectedWorkspace = undefined;

                this.workspaces.forEach(function(workspace) {
                    if(workspace.id == main.selectedWorkspace) {
                        selectedWorkspace = workspace;
                    }
                });

                return selectedWorkspace;
            };

            $("#sectionSelectorTree").fancytree({
                keyboard: false,
                source: this.treeData,
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
                        doc: "glyphicon glyphicon-file",
                        docOpen: "glyphicon glyphicon-file",
                        folder: "glyphicon glyphicon-folder-close",
                        folderOpen: "glyphicon glyphicon-folder-open"
                    }
                },
                minExpandLevel: 10,
                selectMode: 3,
                checkbox: true,
                extensions: ["glyph", "persist"],
                toggleEffect: false,
                persist: {
                    store: "local",
                    types: 'expanded'
                },
                renderTitle: function (event, data) {
                    return '<span class="fancytree-title">' + data.node.data.number + ' ' + data.node.title +
                        '</span>';

                },
                select: function(event, data) {
                    var selKeys = $.map(data.tree.getSelectedNodes(true), function(node){
                        return node.key;
                    });

                    mainController.selectedLearningPathNodeIds =
                        selKeys.length === 0 ? null : JSON.stringify(selKeys);

                    $scope.$digest();
                }

            });

            var tree = $.ui.fancytree.getTree("#tree");

            RepoService.fetchCategories(0, function(categories) {
                mainController.userCategories = categories;
                mainController.selectedCopyCategory = categories[0];
            });
        }]
    );
})();
