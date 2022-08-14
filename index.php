<?php

include_once 'autoload.php';

const CELLULAR_MACHINE_TRANSFORM_STEPS = [
    'sumRowValuesWithLog',
    'divideAndFillWithBlankValues',
    'countDifferentValues',
    'inverseRows',
    'mapRecursivePolynomialValuesByModulo'
];
const NAVIGATION_TITLES = [
    'Suma logarytmów',
    'Wypełnienie zerami',
    'Sumy wystepujących wartości',
    'Obrócenie kolumn',
    'Rekurencja'
];

$pattern = $_POST['pattern'] ?? '1x^2+1%37';
$timeLimit = $_POST['timeLimit'] ?? 30;

// Init

$transformer = new CellularMachinePatternTransformer($timeLimit,$pattern);
$strategy = new CreateViewBodyStrategy();

$builder = new CellularMachineDataViewBuilder($transformer,$strategy);
$strategyDecorator = new CreateViewBodyDecorator($strategy);


// Build Navigation

$navigationParameters = array_combine(CELLULAR_MACHINE_TRANSFORM_STEPS,NAVIGATION_TITLES);

array_walk($navigationParameters,function (&$title, $transformStep)
{
    $title = [
        'transformStep' => $transformStep,
        'title' => $title
    ];
});
$navigationParameters = array_values($navigationParameters);

// Prepare entropy chart values

[$labelBorders,$valueBorders] = $transformer->provideMinAndMaxValues();
[$keys,$values]= $transformer->provideLabelsAndValues();

$roundValues = implode(', ',array_map(
    fn($value) => number_format($value,2,'.',''),
    $values
));

$keys = implode(', ',$keys);
$values = implode(', ',$values);


?>

<!DOCTYPE html>
<html>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    
    <body>
        <div class="placeholder"></div>
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <?php echo $builder->createNavigation($navigationParameters,'');?>
<!--                <ul class="nav nav-pills" id="myTab" role="tablist">-->
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button-->
<!--                                class="nav-link "-->
<!--                                data-bs-toggle="tab"-->
<!--                                data-bs-target="#sumRowValuesWithLog"-->
<!--                                type="button"-->
<!--                                role="tab"-->
<!--                                aria-controls="sumRowValuesWithLog"-->
<!--                                aria-selected="true"-->
<!--                        >-->
<!--                            Suma logarytmów-->
<!--                        </button>-->
<!--                    </li>-->
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button-->
<!--                                class="nav-link active"-->
<!--                                data-bs-toggle="tab"-->
<!--                                data-bs-target="#divideAndFillWithBlankValues"-->
<!--                                type="button"-->
<!--                                role="tab"-->
<!--                                aria-controls="divideAndFillWithBlankValues"-->
<!--                                aria-selected="false"-->
<!--                        >-->
<!--                            Wypełnienie zerami-->
<!--                        </button>-->
<!--                    </li>-->
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button-->
<!--                                class="nav-link"-->
<!--                                data-bs-toggle="tab"-->
<!--                                data-bs-target="#countDifferentValues"-->
<!--                                type="button"-->
<!--                                role="tab"-->
<!--                                aria-controls="countDifferentValues"-->
<!--                                aria-selected="false"-->
<!--                        >-->
<!--                            Sumy wystepujących wartości-->
<!--                        </button>-->
<!--                    </li>-->
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button-->
<!--                                class="nav-link"-->
<!--                                data-bs-toggle="tab"-->
<!--                                data-bs-target="#inverseRows"-->
<!--                                type="button"-->
<!--                                role="tab"-->
<!--                                aria-controls="inverseRows"-->
<!--                                aria-selected="false"-->
<!--                        >-->
<!--                            Obrócenie kolumn-->
<!--                        </button>-->
<!--                    </li>-->
<!--                    <li class="nav-item" role="presentation">-->
<!--                        <button-->
<!--                                class="nav-link"-->
<!--                                data-bs-toggle="tab"-->
<!--                                data-bs-target="#mapRecursivePolynomialValuesByModulo"-->
<!--                                type="button"-->
<!--                                role="tab"-->
<!--                                aria-controls="mapRecursivePolynomialValuesByModulo"-->
<!--                                aria-selected="false"-->
<!--                        >-->
<!--                            Rekurencja-->
<!--                        </button>-->
<!--                    </li>-->
<!--                </ul>-->
                <form method="POST">
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <button type="submit" class="input-group-text" id="inputGroup-sizing-sm">Wzór</button>
                        </div>
                        <input type="text" class="form-control" name="pattern" value="<?php echo $pattern ?>" placeholder="np. 1x^2+1%37 " aria-label="Wzór" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </form>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="sumRowValuesWithLog" role="tabpanel" aria-labelledby="sumRowValuesWithLog">
                        <?php echo $builder->createView('sumRowValuesWithLog')?>
                    </div>
                    <div class="tab-pane fade" id="divideAndFillWithBlankValues" role="tabpanel" aria-labelledby="divideAndFillWithBlankValues">
                        <?php
                            echo $builder
                                ->setStrategy($strategyDecorator)
                                ->createView('divideAndFillWithBlankValues')
                        ?>
                    </div>
                    <div class="tab-pane fade" id="countDifferentValues" role="tabpanel" aria-labelledby="countDifferentValues">
                        <?php
                            echo $builder
                                ->setStrategy($strategyDecorator)
                                ->createView('countDifferentValues')
                        ?>
                    </div>
                    <div class="tab-pane fade" id="inverseRows" role="tabpanel" aria-labelledby="inverseRows">
                        <?php
                            echo $builder
                                ->setStrategy($strategyDecorator)
                                ->createView('inverseRows')
                        ?>
                    </div>
                    <div class="tab-pane fade" id="mapRecursivePolynomialValuesByModulo" role="tabpanel" aria-labelledby="mapRecursivePolynomialValuesByModulo">
                        <?php
                            echo $builder
                                ->setStrategy($strategyDecorator)
                                ->createView('mapRecursivePolynomialValuesByModulo')
                        ?>
                    </div>
                </div>
                </div>
                <div class="col-6">
                    <canvas id="myChart" class="max-width:500px;" width="400" height="400">
                    </canvas>
                </div>
            </div>

        </div>
        <script>
            var ctx = document.getElementById("myChart");
            var data = {
                labels: [<?php echo $keys;?>],
                datasets: [{
                    label: "Wykres entropii Shannona dla automatu komórkowego 0-wymiarowego",
                    function: function(x) { return x },
                    borderColor: "rgba(75, 192, 192, 1)",
                    data: [<?php echo $values;?>],
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

            var myBarChart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    bezierCurve : true,
                    scales: {
                        y: {
                            max: <?php echo $valueBorders['max'] + 0.2;?>,
                            min: 0,
                            ticks: {
                                autoSkip: false,
                                stepSize: 0.001,
                                callback: (value, index, ticks) => {
                                    const values = [<?php echo $roundValues;?>];
                                     if(values.includes(value)){
                                         return value;
                                     }
                                }
                            }
                        }
                    }
                }
            });
        </script>
    </body>
</html>
