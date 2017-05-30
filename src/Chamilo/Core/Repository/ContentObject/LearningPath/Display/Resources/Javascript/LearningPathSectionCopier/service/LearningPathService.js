(function () {
    var learningPathSectionCopierApp = angular.module('learningPathSectionCopierApp');

    learningPathSectionCopierApp.service('LearningPathService', ['$http', function($http) {

        this.fetchLearningPaths = function (workspaceId, categoryId, searchQuery, successCallback, errorCallback) {
            var parameters = {'search_query': searchQuery};

            if(workspaceId) {
                parameters.workspace_id = workspaceId;
            }

            if(categoryId !== null) {
                parameters.category_id = categoryId;
            }

            $http.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetLearningPaths',
                $.param(parameters),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(
                function(response) {
                    successCallback(response.data);
                },
                errorCallback
            );
        };

        this.fetchLearningPathTree = function(learningPathId, successCallback, errorCallback) {
            $http.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetLearningPathTree',
                $.param({ 'learning_path_id': learningPathId }),
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