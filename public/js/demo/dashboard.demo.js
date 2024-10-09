$(document).ready(function () {

    wo();
    // document.addEventListener('theme-reload', function () {
    //     $('[data-render="apexchart"], #chart-server').empty();
    //     handleRenderChart();
    // });
})

setInterval(function () { wo(); }, 30000);


function wo() {
    "use strict";
    $('#todayWO').html('<div class="col-lg-12">'
        + '<div class="card mb-3">'
        + ' <div class="card-body">'
        + '      <div class="d-flex fw-bold small mb-3">'
        + '           <span class="flex-grow-1">Loading...</span>'
        + '           <a href="#" data-toggle="card-expand"'
        + '                class="text-white text-opacity-50 text-decoration-none"><i class="bi bi-fullscreen"></i></a>'
        + '        </div>'
        + '        <div class="row align-items-center mb-2">'
        + '            <div class="col-12 p-3">'
        + '                <h1 class="mb-0 text-warning text-center">'
        + '                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>'
        + '                   <span class="sr-only">Loading...</span>'
        + '                </h1> '
        + '            </div>'
        + '        </div>'
        + '    </div>'
        + '    <div class="card-arrow">'
        + '        <div class="card-arrow-top-left"></div>'
        + '        <div class="card-arrow-top-right"></div>'
        + '        <div class="card-arrow-bottom-left"></div>'
        + '        <div class="card-arrow-bottom-right"></div>'
        + '    </div>'
        + '    </div>'
        + '</div>')

     

    $.ajax({
        type: "GET",
        url: "api/wo.php",
        dataType: 'json',
        success: function (d) {
            $('#todayWO').html(d.dataone)
            console.log(app.color.themeRgb)
        },
        complete: function (d) {
                   var chartColors = ['rgba(' + app.color.themeRgb + ', .15)', 'rgba(' + app.color.themeRgb + ', .35)', 'rgba(' + app.color.themeRgb + ', .55)', 'rgba(' + app.color.themeRgb + ', .75)', 'rgba(' + app.color.themeRgb + ', .95)'];
        var chartData = [10, 15, 20, 25, 30];
        var chartStroke = { show: false, curve: 'smooth', lineCap: 'butt', colors: 'rgba(' + app.color.blackRgb + ', .25)', width: 2, dashArray: 0, };
        var chartPlotOptions = { pie: { donut: { background: 'transparent', } } };

        var chartOptions = {
                chart: {
                    height: 45,
                    type: 'donut',
                    toolbar: { show: false },
                    sparkline: { enabled: true },
                },
                dataLabels: { enabled: false },
                colors: chartColors,
                stroke: chartStroke,
                plotOptions: chartPlotOptions,
                series: chartData,
                grid: { show: false },
                tooltip: {
                    theme: 'dark',
                    x: { show: false },
                    y: {
                        title: { formatter: function (seriesName) { return '' } },
                        formatter: (value) => { return '' + value },
                    }
                },
                xaxis: { labels: { show: false } },
                yaxis: { labels: { show: false } }
            };

                var chart = new ApexCharts(document.querySelector("#chart"), chartOptions);
                chart.render();
        },
        error: function () {
            $('#todayWO').html('<div class="col-lg-12">'
                + '<div class="card mb-3">'
                + ' <div class="card-body">'
                + '      <div class="d-flex fw-bold small mb-3">'
                + '           <span class="flex-grow-1">Internal Connection Failed : Error 105</span>'
                + '           <a href="#" data-toggle="card-expand"'
                + '                class="text-white text-opacity-50 text-decoration-none"><i class="bi bi-fullscreen"></i></a>'
                + '        </div>'
                + '        <div class="row align-items-center mb-2">'
                + '            <div class="col-12 p-3">'
                + '                <h1 class="mb-0 text-warning text-center">'
                + '                   Internal Connection Failed! '
                + '                </h1> '
                + '            </div>'
                + '        </div>'
                + '    </div>'
                + '    <div class="card-arrow">'
                + '        <div class="card-arrow-top-left"></div>'
                + '        <div class="card-arrow-top-right"></div>'
                + '        <div class="card-arrow-bottom-left"></div>'
                + '        <div class="card-arrow-bottom-right"></div>'
                + '    </div>'
                + '    </div>'
                + '</div>')
        }
    });
}