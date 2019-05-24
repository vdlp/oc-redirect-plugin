$(document).ready(function () {
    $.request('onRedirectHitsPerDay', {
        data: {},
        success: function (data) {
            var items = eval(data.result);
            var container = document.getElementById('visualization');

            var groups = new vis.DataSet();
                groups.add({id: 0, content: "Crawlers"});
                groups.add({id: 1, content: "Users"});

            new vis.DataSet(items);

            var options = {
                style: 'bar',
                barChart: {
                    width: 30,
                    align: 'center',
                    sideBySide: false
                }, // align: left, center, right
                drawPoints: false,
                dataAxis: {
                    visible: true
                },
                legend: true,
                orientation: 'top',
                graphHeight: '230px',
                clickToUse: true
            };

            new vis.Graph2d(container, items, groups, options);
        }
    });
});
