var app = angular.module('myApp', ['ngMap', 'angular-loading-bar']);
app.controller('mapCtrl', function($scope, $http) {
    $scope.paths = [];
    $scope.polyline_container = [];
    $scope.formData = {
        'type' : 'number',
        'date' : '2014-10-6',
        'number' : '62811100261',
        'limit' : 2,
        'offset': 0
    };

    $scope.getRandomSpan = function(){
        return Math.floor((Math.random()*100)+1);
    }

    // $http.get(api+"/phones")
    //     .success(function(response) {
    //         console.log('phone retrieved')
            $scope.phones = phones;
            var tags = $scope.phones;
            $( "#autocomplete" ).autocomplete({
              source: function( request, response ) {
                      var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
                      response( $.grep( tags, function( item ){
                          return matcher.test( item );
                      }) );
                  },
                select: function(event, ui) {
                    $scope.formData.number = ui.item.value;
                    return false;
                  }
            });
        // })
        // .error(function(err) {
        //     console.log(err);
        // });


    /* Custom setting  */
    $scope.filter = function(formData) {
        if ($scope.polyline_container.length > 0) {
            angular.forEach($scope.polyline_container, function(value, key) {
                value.setMap(null);
            });
        }

        if (formData.type == 'number') {
            $http.get(api+"/filter/" + formData.date + "/"+formData.number )
                .success(function(response) {
                    $scope.datas = response;
                    $scope.generatePath();
                });
        } else {
            $http.get(api+"/filter/" + formData.date + "/"+formData.limit + "/"+ formData.offset )
                .success(function(response) {
                    $scope.datas = response;
                    $scope.generatePath();
                });
        }
    };

    $scope.checkFilterNumber = function() {
        if ($scope.formData.type == 'number') {
            return true;
        }
        return false;
    }

    $scope.generatePath = function() {
        var response = $scope.datas;
        console.log(response);
        $scope.paths.length = 0;
        for (var number in response) {
            var path = [];
            var obj = response[number];
            for (var p in obj) {
                path.push(new google.maps.LatLng(obj[p].Lat, obj[p].Long));
            }
            $scope.paths.push(path);
        }
           console.log($scope.paths);
    };

    $scope.getRandomColor = function() {
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++ ) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    };

    $scope.getMarker = function(item, index) {
        var count = item.length;
        if (index == 0) {
            return 'img/marker-green.png';
        }

        if (index == (count-1)) {

            return 'img/marker-orange.png';
        }

        return 'img/marker-grey.png';
    };

    $scope.logPath = function() {
        var lineSymbol = {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
          };
        $scope.polyline_container.length = 0;
        for (var i = 0; i < $scope.paths.length; i++) {
            var p = new google.maps.Polyline({
                path: $scope.paths[i],
                geodesic: true,
                strokeColor: $scope.getRandomColor(),
                strokeOpacity: 1.0,
                strokeWeight: 2,
                icons: [{
                  icon: lineSymbol,
                  offset: '100%'
                }],
            });
            $scope.polyline_container.push(p);
        }
        if ($scope.polyline_container.length > 0) {
            angular.forEach($scope.polyline_container, function(value, key) {
                value.setMap($scope.map);
            });
        }
    };

    $scope.showInfoWindow = function (event, data) {
        var infowindow = new google.maps.InfoWindow();
        var center = new google.maps.LatLng(data.Lat,data.Long);

        infowindow.setContent(
            '<p>MSISDN : ' + data.MSISDN + '<br>' +
            '<p>Latitude : ' + data.Lat + '<br>' +
            '<p>Longitude : ' + data.Long + '</p>');

        infowindow.setPosition(center);
        infowindow.open($scope.map);
     };

});
