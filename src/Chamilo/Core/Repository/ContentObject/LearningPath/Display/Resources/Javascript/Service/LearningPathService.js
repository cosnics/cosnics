(function () {
    const LearningPathService = {
        fetchLearningPaths: function (workspaceId, categoryId, searchQuery, successCallback, errorCallback) {
            const formData = new FormData();
            formData.set('search_query', searchQuery || '');
            if (workspaceId) {
                formData.set('workspace_id', workspaceId);
            }
            if (categoryId !== null) {
                formData.set('category_id', categoryId);
            }

            axios.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetLearningPaths',
                formData,
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(function(response) {
                successCallback(response.data);
            }).catch(function(error) {
                if (errorCallback) {
                    errorCallback(error);
                }
            });
        },
        fetchTree: function (learningPathId, successCallback, errorCallback) {
            const formData = new FormData();
            formData.set('learning_path_id', learningPathId);

            axios.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetTree',
                formData,
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(function(response) {
                successCallback(response.data);
            }).catch(function(error) {
                if (errorCallback) {
                    errorCallback(error);
                }
            });
        }
    };

    window.VueLearningPathService = {
        install: function (Vue, options) {
            Object.defineProperties(Vue.prototype, {
                $learningPathService: {
                    get: function get() {
                        return LearningPathService;
                    }
                }
            });
        }
    }
})();