(function () {
    const RepositoryService = {
        fetchWorkspaces: function (successCallback, errorCallback) {
            axios.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetWorkspacesWithCopyRight',
                new FormData(),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(function(response) {
                successCallback(response.data);
            }).catch(function(error) {
                if (errorCallback) {
                    errorCallback(error);
                }
            });
        },
        fetchCategories: function (workspaceId, successCallback, errorCallback) {
            const formData = new FormData();
            if (workspaceId) {
                formData.set('workspace_id', workspaceId);
            }
            axios.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetCategories',
                formData,
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(function(response) {
                const categories = response.data.map(category => ({
                    'id': category.id,
                    'name': category.name.replace(/&mdash;/g, '-').replace(/&nbsp;&nbsp;&nbsp;/g, '-')
                }));
                successCallback(categories);
            }).catch(function(error) {
                if (errorCallback) {
                    errorCallback(error);
                }
            });
        },
        fetchContentObjects: function (categoryId, searchQuery, successCallback, errorCallback) {
            const formData = new FormData();
            formData.set('category_id', categoryId);
            formData.set('search_query', searchQuery || '');

            axios.post(
                'index.php?application=Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Ajax&go=GetContentObjects',
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

    window.VueRepositoryService = {
        install: function (Vue, options) {
            Object.defineProperties(Vue.prototype, {
                $repositoryService: {
                    get: function get() {
                        return RepositoryService;
                    }
                }
            });
        }
    }
})();
