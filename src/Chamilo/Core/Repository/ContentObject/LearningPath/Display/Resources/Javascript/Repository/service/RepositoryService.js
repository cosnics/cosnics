(function () {
    var repositoryApp = angular.module('repositoryApp');

    repositoryApp.service('RepositoryService', ['$http', function($http) {

        this.fetchWorkspaces = function(successCallback, errorCallback) {
            $http.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetWorkspacesWithCopyRight',
                null,
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(
                function(response) {
                    successCallback(response.data);
                },
                errorCallback
            );
        };

        this.fetchCategories = function (workspaceId, successCallback, errorCallback) {
            var parameters = {};
            if(workspaceId) {
                parameters = { 'workspace_id': workspaceId};
            }

            $http.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetCategories',
                $.param(parameters),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(
                function(response) {
                    var categories = [];

                    response.data.forEach(function (category) {
                        categories.push({
                            "id": category.id,
                            "name": category.name.replace(/&mdash;/g, '-').replace(/&nbsp;&nbsp;&nbsp;/g, '-')
                        });
                    });

                    successCallback(categories);
                },
                errorCallback
            );
        };

        this.fetchContentObjects = function (categoryId, searchQuery, successCallback, errorCallback) {
            $http.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetContentObjects',
                $.param({'category_id': categoryId, 'search_query': searchQuery}),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(
                function(response) {
                    successCallback(response.data);
                },
                errorCallback
            );
        };

    }]);

})();