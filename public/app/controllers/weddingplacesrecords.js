app.controller('WeddingPlacesController', function($scope, $http, API_URL) {
    $scope.screen_type = 0;
    $scope.place = {};
    $scope.old_place = {};
    $scope.search = '';
    $scope.places = {};

    $scope.dataReload = function() {
        $scope.places = {};
        $http({
            method: 'GET',
            url: API_URL + "places"
        }).then(function successCallback(response) {
            if (response.data.success==false)
                alert(response.data.exception);
            else
                $scope.places = response.data;
        }, function errorCallback(response) {
            alert(response.data.message);
        });
    }

    $scope.dataReload();

    $scope.toggle = function(type, param) {

        switch (type) {

            case 'cancel':
                $scope.screen_type = 0;
                break;

            case 'new':
                $scope.place = {};
                $scope.old_place = null;
                $scope.screen_type = 1;
                break;

            case 'save':
                if ( $scope.old_place == null )
                {
                    // save
                    $http({
                        method: 'POST',
                        url: API_URL + "places",
                        data: angular.toJson($scope.place)
                    }).then(function successCallback(response) {
                        //$scope.places.push($scope.place);
                        $scope.dataReload();
                        $scope.screen_type = 0;
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }
                else
                {
                    //update
                    var data = {
                        'old_city': $scope.old_place.city,
                        'old_place': $scope.old_place.place,
                        'new_city': $scope.place.city,
                        'new_place': $scope.place.place
                    };

                    $http({
                        method: 'PUT',
                        url: API_URL + "places",
                        data: data
                    }).then(function successCallback(response) {
                        $scope.dataReload();
                        $scope.screen_type = 0;
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }

                break;

            case 'search':

                var url = ($scope.search=="") ? API_URL + "places" : API_URL + "places/" + $scope.search;

                $http({
                    method: 'GET',
                    url: url
                }).then(function successCallback(response) {
                    if (response.data.success==false)
                        alert(response.data.exception);
                    else
                        $scope.places = response.data;
                }, function errorCallback(response) {
                    console.log(response);
                });
                break;

            case 'edit':
                $scope.place = param;

                $scope.old_place = {};
                $scope.old_place.city = param.city;
                $scope.old_place.place = param.place;

                $scope.screen_type = 1;
                break;

            case 'delete':
                // delete
                console.log(param.city + "," + param.place);
                $http({
                    method: 'delete',
                    url: API_URL + "places/" + param.city + "," + param.place
                }).then(function successCallback(response) {
                    $scope.dataReload();
                    $scope.screen_type = 0;
                }, function errorCallback(response) {
                    console.log('error');
                    console.log(response);
                });
                break;

            case 'refresh':
                $scope.dataReload();
                break;

            default:
                break;
        }
    }
});