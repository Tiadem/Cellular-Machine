
function createEntropyChart(keys,values,maxValue,roundedValues){

    const ctx = document.getElementById("myChart");
    const data = {
        labels: keys,
        datasets: [{
            label: "Wykres entropii Shannona dla automatu komórkowego 0-wymiarowego",
            function: function(x) { return x },
            borderColor: "rgba(75, 192, 192, 1)",
            data: values,
        fill: false
        }]
    };

    Chart.register({
        id: 'entrophy',
        beforeInit: function(chart) {
            var data = chart.config.data;
            for (var i = 0; i < data.datasets.length; i++) {
                for (var j = 0; j < data.labels.length; j++) {
                    var fct = data.datasets[i].function,
                        x = data.labels[j],
                        y = fct(x);
                    data.datasets[i].data.push(y);
                }
            }
        }
    });

    new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            bezierCurve : true,
            scales: {
                y: {
                    max: maxValue,
                min: 0,
                ticks: {
                autoSkip: false,
                    stepSize: 0.001,
                    callback: (value) => {
                            if(roundedValues.includes(value)){
                                return value;
                            }
                        }
                    }
                }
            }
        }
    });
}
