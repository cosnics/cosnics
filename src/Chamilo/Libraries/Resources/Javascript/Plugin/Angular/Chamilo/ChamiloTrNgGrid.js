(function(){
    var chamiloTrNgGrid = angular.module('chamiloTrNgGrid', ['trNgGrid']);

    chamiloTrNgGrid.run(['gridTranslations', function (gridTranslations) {
        TrNgGrid.tableCssClass = "tr-ng-grid table table-bordered table-hover table-striped";
        TrNgGrid.translations['en'] = gridTranslations;
    }]);

})();
