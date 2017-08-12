App.controller('demoCtrl', ['$scope', '$localStorage', '$window',
    function ($scope, $localStorage, $window) {
        $scope.showCountdown = false;
        $scope.showAlarm = false;
        $scope.msgSent = false;
        $scope.countdown = 0;

        $scope.testPubNub = _publish;
        $scope.initChartsFlot = initChartsFlot;
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

        function initChartsFlot() {
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
    }
]);



App.controller('reportCtrl', ['$scope', '$localStorage', '$window', '$http',
    function ($scope, $localStorage, $window, $http) {
        $scope.initChartsFlot = function() {
            $http({
                method: 'GET',
                url: 'https://6db159b0.ngrok.io/api/quantities'
            }).then(function successCallback(response) {
                var data = response.data;
                var date = [];
                var qty = [];
                for (var i = 0; i < data.length; i++) {
                    var item = data[i];

                    date.push(item.date);
                    qty.push(item.count);
                }

                var chartLinesCon  = jQuery('.js-chartjs-lines')[0].getContext('2d');
                var chartLinesBarsRadarData = {
                    labels: date,
                    datasets: [
                        {
                            label: 'Last Week',
                            fillColor: 'rgba(102,0,51,.3)',
                            strokeColor: 'rgba(102,0,51,1)',
                            pointColor: 'rgba(102,0,51,1)',
                            pointStrokeColor: '#fff',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(102,0,51,1)',
                            data: qty
                        }
                    ]
                };
                var globalOptions = {
                    scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                    scaleFontColor: '#999',
                    scaleFontStyle: '600',
                    tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                    tooltipCornerRadius: 3,
                    maintainAspectRatio: false,
                    responsive: true,
                    scaleOverride: true,
                    scaleSteps: 5,
                    scaleStepWidth: 1,
                    scaleStartValue: 0 
                };
                chartLines = new Chart(chartLinesCon).Line(chartLinesBarsRadarData, globalOptions);
            }, function errorCallback(response) {
                console.errr(response);
            });

            // ----------------------
            $http({
                method: 'GET',
                url: 'https://6db159b0.ngrok.io/api/times'
            }).then(function successCallback(response) {
                var data = response.data;
                var newData = [];
                var date = [];
                var counter = -1;
                for (var i = 0; i < data.length; i++) {
                    var item = data[i];

                    if (counter < 0 || item.date !== data[i-1].date) {
                        counter += 1;
                    }
                    newData.push([counter, item.date], [counter, item.hour]);
                    date.push(item.date);
                }

                var flotLines      = jQuery('.js-flot-lines');

                jQuery.plot(flotLines,
                    [{
                        data: newData,
                        lines: {
                            show: true,
                            // fill: true,
                            fillColor: {
                                colors: [{opacity: .7}, {opacity: .7}]
                            }
                        },
                        points: {
                            show: true,
                            radius: 6
                        }
                    }],
                    {
                        colors: ['#660033', '#333333'],
                        legend: {
                            show: true,
                            position: 'nw',
                            backgroundOpacity: 0
                        },
                        grid: {
                            borderWidth: 0,
                            hoverable: true,
                            clickable: true
                        },
                        yaxis: {
                            tickColor: '#ffffff',
                            ticks: 3
                        },
                        xaxis: {
                            ticks: date,
                            tickColor: '#f5f5f5'
                        }
                    }
                );

                var previousPoint = null, ttlabel = null;
                flotLines.bind('plothover', function(event, pos, item) {
                    if (item) {
                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            jQuery('.js-flot-tooltip').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            if (item.seriesIndex === 0) {
                                ttlabel = '$ <strong>' + y + '</strong>';
                            } else if (item.seriesIndex === 1) {
                                ttlabel = '<strong>' + y + '</strong> sales';
                            } else {
                                ttlabel = '<strong>' + y + '</strong> tickets';
                            }

                            jQuery('<div class="js-flot-tooltip flot-tooltip">' + ttlabel + '</div>')
                                .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                        }
                    }
                    else {
                        jQuery('.js-flot-tooltip').remove();
                        previousPoint = null;
                    }
                });
            }, function errorCallback(response) {
                console.errr(response);
            });

            // ------
            $http({
                method: 'GET',
                url: 'https://6db159b0.ngrok.io/api/weights'
            }).then(function successCallback(response) {
                var data = response.data;
                var date = [];
                var last_weight_value = [];
                for (var i = 0; i < data.length; i++) {
                    var item = data[i];

                    date.push(item.date);
                    last_weight_value.push(item.last_weight_value);
                }


                var weightChart  = jQuery('.js-weight-chart-lines')[0].getContext('2d');
                var weightChartData = {
                    labels: date,
                    datasets: [
                        {
                            label: 'Weight',
                            fillColor: 'rgba(171, 227, 125, .3)',
                            strokeColor: 'rgba(171, 227, 125, 1)',
                            pointColor: 'rgba(171, 227, 125, 1)',
                            pointStrokeColor: '#fff',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(171, 227, 125, 1)',
                            data: last_weight_value
                        }
                    ]
                };
                var globalOptions = {
                    scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                    scaleFontColor: '#999',
                    scaleFontStyle: '600',
                    tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                    tooltipCornerRadius: 3,
                    maintainAspectRatio: false,
                    responsive: true,
                    scaleOverride: true,
                    scaleSteps: 100,
                    scaleStepWidth: 10,
                    scaleStartValue: 0 
                };
                weightChartLines = new Chart(weightChart).Line(weightChartData, globalOptions);
            }, function errorCallback(response) {
                console.errr(response);
            });
        };
    }
]);