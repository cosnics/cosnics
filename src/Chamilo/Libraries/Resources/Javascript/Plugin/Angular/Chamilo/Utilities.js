(function(){
    var chamilo = angular.module('chamilo', []);

    chamilo.service('chamiloUtilities', ['$http', function($http) {

        this.callChamiloAjax = function(context, action, parameters, successCallback, errorCallback)
        {
            var ajaxURI = 'index.php?application={{ application }}&go={{ action }}';
            ajaxURI = ajaxURI.replace('{{ application }}', context);
            ajaxURI = ajaxURI.replace('{{ action }}', action);

            if(errorCallback == undefined)
            {
                errorCallback = function() {};
            }

            $http.post(ajaxURI,
                $.param(parameters),
                {
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                }
            ).success(successCallback).error(errorCallback);
        };

        this.callChamiloAjaxPromise = function(context, action, parameters)
        {
            var ajaxURI = 'index.php?application={{ application }}&go={{ action }}';
            ajaxURI = ajaxURI.replace('{{ application }}', context);
            ajaxURI = ajaxURI.replace('{{ action }}', action);

            return $http.post(ajaxURI,
                $.param(parameters),
                {
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
                }
            );
        }

    }]);

})();