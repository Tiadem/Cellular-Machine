<?php

include_once 'autoload.php';

use CellularMachine\CellularMachinePatternTransformer;
use View\Builder\CellularMachineDataViewBuilder;
use View\Strategy\CreateStepViewStrategy;
use View\Decorator\CreateStepViewDecorator;

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
$strategy = new CreateStepViewStrategy();

$builder = new CellularMachineDataViewBuilder($transformer,$strategy);
$strategyDecorator = new CreateStepViewDecorator($strategy);

// Build Body

$defaultStrategies = [$strategy];
$decoratedStrategies = array_fill(
        0,
        count(CELLULAR_MACHINE_TRANSFORM_STEPS) - count($defaultStrategies),
        $strategyDecorator
);
$stepStrategies = array_merge($defaultStrategies,$decoratedStrategies);

$stepViewParameters = array_combine(CELLULAR_MACHINE_TRANSFORM_STEPS,$stepStrategies);

array_walk($stepViewParameters,function (&$strategy, $transformStep)
{
    $strategy = [
        'transformStep' => $transformStep,
        'strategy' => $strategy
    ];
});

$stepViewParameters = array_values($stepViewParameters);

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
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    
    <body>
        <div class="placeholder"></div>
        <div class="container">
            <div class="row">
                <div class="col-6">

                <?php echo $builder->createNavigation($navigationParameters);?>

                <form method="POST">
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <button type="submit" class="input-group-text" id="inputGroup-sizing-sm">Wzór</button>
                        </div>
                        <input type="text" class="form-control" name="pattern" value="<?php echo $pattern; ?>" placeholder="np. 1x^2+1%37 " aria-label="Wzór" aria-describedby="inputGroup-sizing-sm">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <button type="submit" class="input-group-text" id="inputGroup-sizing-sm">Przedział czasowy</button>
                        </div>
                        <input type="text" class="form-control" name="timeLimit" value="<?php echo $timeLimit; ?>" placeholder="np. 30 " aria-label="Przedział czasowy" aria-describedby="inputGroup-sizing-sm">
                    </div>
                </form>

                <?php echo $builder->createBody($stepViewParameters);?>

                </div>
                <div class="col-6">
                    <canvas id="myChart" height="300">
                    </canvas>
                </div>
            </div>

        </div>
        <script>

            const keys = [<?php echo $keys;?>];
            const values = [<?php echo $values;?>];
            const maxValue = <?php echo $valueBorders['max'] + 0.2;?>;
            const roundValues =  [<?php echo $roundValues;?>];

            createEntropyChart(keys,values,maxValue,roundValues);

        </script>
    </body>
</html>
