(function ($) {
    "use strict";

    $(window).on('load', function() {
        setTimeout(function() {
            //Cashflow Chart
            if (document.getElementById("transactionAnalysis")) {
                var chartCurrency = _currency_symbol;
                const transactionAnalysis = document
                    .getElementById("transactionAnalysis")
                    .getContext("2d");
                var transactionAnalysisChart = new Chart(transactionAnalysis, {
                    type: "bar",
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: $lang_income,
                                data: [],
                                backgroundColor: ["rgba(72, 52, 212, 1.0)"],
                                borderColor: ["rgba(72, 52, 212, 1.0)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                            {
                                label: $lang_expense,
                                data: [],
                                backgroundColor: ["rgba(231, 76, 60, 1.0)"],
                                borderColor: ["rgba(231, 76, 60, 1.0)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                        ],
                    },
                    options: {
                        interaction: {
                            mode: "index",
                            intersect: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        stacked: true,
                        scales: {
                            y: {
                                type: "linear",
                                display: true,
                                position: "left",
                                ticks: {
                                    callback: function (value, index, values) {
                                        return chartCurrency + " " + value;
                                    },
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: "rectRounded",
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var label = context.dataset.label || "";

                                        if (
                                            context.parsed.y !== null &&
                                            context.dataset.yAxisID == "y"
                                        ) {
                                            label +=
                                                ": " +
                                                chartCurrency +
                                                " " +
                                                context.parsed.y;
                                        } else {
                                            label += ": " + context.parsed.y;
                                        }

                                        return label;
                                    },
                                },
                            },
                        },
                    },
                });
            }

            if (document.getElementById("transactionAnalysis")) {
                $.ajax({
                    url: _url + "/dashboard/json_profit_and_loss",
                    success: function (data) {
                        var json = JSON.parse(data);

                        transactionAnalysisChart.data.labels = json["month"];
                        transactionAnalysisChart.data.datasets[0].data = [
                            typeof json["income"][1] !== "undefined"
                                ? json["income"][1]
                                : 0,
                            typeof json["income"][2] !== "undefined"
                                ? json["income"][2]
                                : 0,
                            typeof json["income"][3] !== "undefined"
                                ? json["income"][3]
                                : 0,
                            typeof json["income"][4] !== "undefined"
                                ? json["income"][4]
                                : 0,
                            typeof json["income"][5] !== "undefined"
                                ? json["income"][5]
                                : 0,
                            typeof json["income"][6] !== "undefined"
                                ? json["income"][6]
                                : 0,
                            typeof json["income"][7] !== "undefined"
                                ? json["income"][7]
                                : 0,
                            typeof json["income"][8] !== "undefined"
                                ? json["income"][8]
                                : 0,
                            typeof json["income"][9] !== "undefined"
                                ? json["income"][9]
                                : 0,
                            typeof json["income"][10] !== "undefined"
                                ? json["income"][10]
                                : 0,
                            typeof json["income"][11] !== "undefined"
                                ? json["income"][11]
                                : 0,
                            typeof json["income"][12] !== "undefined"
                                ? json["income"][12]
                                : 0,
                        ];
                        transactionAnalysisChart.data.datasets[1].data = [
                            typeof json["expense"][1] !== "undefined"
                                ? json["expense"][1]
                                : 0,
                            typeof json["expense"][2] !== "undefined"
                                ? json["expense"][2]
                                : 0,
                            typeof json["expense"][3] !== "undefined"
                                ? json["expense"][3]
                                : 0,
                            typeof json["expense"][4] !== "undefined"
                                ? json["expense"][4]
                                : 0,
                            typeof json["expense"][5] !== "undefined"
                                ? json["expense"][5]
                                : 0,
                            typeof json["expense"][6] !== "undefined"
                                ? json["expense"][6]
                                : 0,
                            typeof json["expense"][7] !== "undefined"
                                ? json["expense"][7]
                                : 0,
                            typeof json["expense"][8] !== "undefined"
                                ? json["expense"][8]
                                : 0,
                            typeof json["expense"][9] !== "undefined"
                                ? json["expense"][9]
                                : 0,
                            typeof json["expense"][10] !== "undefined"
                                ? json["expense"][10]
                                : 0,
                            typeof json["expense"][11] !== "undefined"
                                ? json["expense"][11]
                                : 0,
                            typeof json["expense"][12] !== "undefined"
                                ? json["expense"][12]
                                : 0,
                        ];
                        transactionAnalysisChart.update();
                    },
                });
            }
            $(".loading-chart").remove();
      }, 2000);
    });
})(jQuery);
