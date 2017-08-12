App.controller('demoCtrl', ['$scope', '$localStorage', '$window',
    function ($scope, $localStorage, $window) {
        $scope.showCountdown = false;
        $scope.showAlarm = false;
        $scope.msgSent = false;
        $scope.countdown = 0;

        $scope.testPubNub = _publish;
        $scope.updateCountdown = function() {
            if ($scope.countdown > 0) {
                $scope.countdown -= 1;
                setTimeout($scope.updateCountdown, 1000);
            } else {
                if ($scope.showCountdown) {
                    $scope.showCountdown = false;
                    $scope.showAlarm = true;
                    $scope.countdown = 10;
                    setTimeout($scope.updateCountdown, 1000);
                } else {
                    $scope.showAlarm = false;
                    $scope.msgSent = true;
                }
            }
            $scope.$apply();
        }

        var pubnub = new PubNub({
            publishKey: "pub-c-61f8db65-a012-490c-b4b3-293f2e475f8f",
            subscribeKey: "sub-c-8001e388-7eeb-11e7-a405-4e5e8b8b77ee"
        });

        function _publish(message, callback) {
            pubnub.publish({
                channel: 'libdzkmxx_channel',
                message: message
            }, function(status, response) {
                // console.log(status, response);
                if (callback) {
                    callback(status, response);
                }
            });
        }

        pubnub.addListener({
            status: function(statusEvent) {
                if (statusEvent.category === "PNConnectedCategory") {}
            },
            message: function(response) {
                if (response.message == 'Start counting') {
                    $scope.showCountdown = true;
                    $scope.countdown = 5;
                    $scope.updateCountdown();
                } else {
                    alert(response.message);
                }
            },
            presence: function(presenceEvent) {
                // handle presence
            }
        })      

        pubnub.subscribe({
            channels: ['libdzkmxx_channel']
        });

        var initChartsFlot = function() {
            var dataLive = [];
            var flotLive       = jQuery('.js-flot-live');
            function getRandomData() {
                if (dataLive.length > 0) {
                    dataLive = dataLive.slice(1);
                }

                while (dataLive.length < 300) {
                    var prev = dataLive.length > 0 ? dataLive[dataLive.length - 1] : 50;
                    var y = prev + Math.random() * 10 - 5;
                    if (y < 0)
                        y = 0;
                    if (y > 100)
                        y = 100;
                    dataLive.push(y);
                }

                var res = [];
                for (var i = 0; i < dataLive.length; ++i)
                    res.push([i, dataLive[i]]);

                // jQuery('.js-flot-live-info').html(y.toFixed(0) + '%');

                return res;
            }

            function updateChartLive() { // Update live chart
                chartLive.setData([getRandomData()]);
                chartLive.draw();
                setTimeout(updateChartLive, 70);
            }

            var chartLive = jQuery.plot(flotLive, // Init live chart
                [{ data: getRandomData() }],
                {
                    series: {
                        shadowSize: 0
                    },
                    lines: {
                        show: true,
                        lineWidth: 2,
                        fill: true,
                        fillColor: {
                            colors: [{opacity: .2}, {opacity: .2}]
                        }
                    },
                    colors: ['#75b0eb'],
                    grid: {
                        borderWidth: 0,
                        color: '#aaaaaa'
                    },
                    yaxis: {
                        show: true,
                        min: 0,
                        max: 110
                    },
                    xaxis: {
                        show: false
                    }
                }
            );

            // updateChartLive(); // Start getting new data
        };
        
        initChartsFlot();
    }
]);