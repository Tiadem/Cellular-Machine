<?php

include_once 'autoload.php';

use CellularMachine\CellularMachinePatternTransformer;
use View\Builder\CellularMachineDataViewBuilder;
use View\Strategy\CreateStepViewStrategy;
use View\Decorator\CreateStepViewDecorator;
use CellularMachine\EquationPattern\CustomEquationPatternResolver;
use CellularMachine\EquationPattern\UlamEquationPatternResolver;
use View\Helpers\ViewDataParametersMapperFactory;
use View\Helpers\ChartValuesFormatter;

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

// Init

$pattern = $_POST['pattern'] ?? '1x^2+1%37';
$timeLimit = $_POST['timeLimit'] ?? 30;
$modulo = $_POST['modulo'] ?? 37;

$patternResolver = new CustomEquationPatternResolver($pattern);
$ulamResolver = new UlamEquationPatternResolver($modulo);

$transformer = new CellularMachinePatternTransformer($timeLimit,$patternResolver);

$strategy = new CreateStepViewStrategy();

$builder = new CellularMachineDataViewBuilder($transformer,$strategy);
$strategyDecorator = new CreateStepViewDecorator($strategy);

$defaultStrategies = [$strategy];
$decoratedStrategies = array_fill(
        0,
        count(CELLULAR_MACHINE_TRANSFORM_STEPS) - count($defaultStrategies),
        $strategyDecorator
);
$stepStrategies = array_merge($defaultStrategies,$decoratedStrategies);

$stepViewParameters =  (ViewDataParametersMapperFactory::createBodyParametersMapper())->mapParameters($stepStrategies);
$navigationParameters = (ViewDataParametersMapperFactory::createHeaderParametersMapper())->mapParameters(NAVIGATION_TITLES);

// Prepare entropy chart values

$chartValuesFormatter = new ChartValuesFormatter();

[$labelBorders,$valueBorders] = $transformer->provideMinAndMaxValues();
[$keys,$values]= $transformer->provideLabelsAndValues();

$roundValues = $chartValuesFormatter->roundValues($values,2);
$keys = $chartValuesFormatter->implode($keys);
$values = $chartValuesFormatter->implode($values);


?>

<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    
    <body>
        <nav id="pattern-nav">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                            class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#custom-pattern"
                            type="button"
                            role="tab"
                            aria-controls="custom-pattern"
                            aria-selected="false"
                    >
                        Dowolny wzór
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                            class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#ulam-function"
                            type="button"
                            role="tab"
                            aria-controls="ulam-function"
                            aria-selected="false"
                    >
                        Funkcja Ulama
                    </button>
                </li>
            </ul>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-6">
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
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <button type="submit" class="input-group-text" id="inputGroup-sizing-sm">Modulo dla funkcji Ulama</button>
                            </div>
                            <input type="text" class="form-control" name="modulo" value="<?php echo $modulo; ?>" placeholder="np. 37 " aria-label="Modulo dla funkcji Ulama" aria-describedby="inputGroup-sizing-sm">
                        </div>
                    </form>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="custom-pattern" role="tabpanel" aria-labelledby="custom-pattern">

                            <?php
                                echo $builder->createNavigation($navigationParameters);
                                echo $builder->createBody($stepViewParameters);
                            ?>

                        </div>

                        <div class="tab-pane fade" id="ulam-function" role="tabpanel" aria-labelledby="ulam-function">

                            <?php
                                echo $builder->generateRandomViewID()->
                                changeTransformer(
                                        $transformer->setEquationPatternResolver($ulamResolver)
                                )
                                ->createNavigation($navigationParameters);
                                echo $builder->createBody($stepViewParameters);
                            ?>

                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <canvas id="default-pattern-chart" height="280" style="padding-bottom: 20px;">
                    </canvas>
                    <canvas id="ulam-chart" height="280">
                    </canvas>
                </div>
            </div>

        </div>
        <script>

            createEntropyChart(
                'default-pattern-chart',
                'Wykres entropii Shannona dla automatu komórkowego 0-wymiarowego określonego przez funkcję wielomianową',
                [<?php echo $keys;?>],
                [<?php echo $values;?>],
                <?php echo $valueBorders['max'] + 0.2;?>,
                [<?php echo $roundValues;?>]
            );

            <?php
                [$ulamLabelBorders,$ulamValueBorders] = $transformer->provideMinAndMaxValues();
                [$ulamKeys,$ulamValues]= $transformer->provideLabelsAndValues();

                $ulamRoundValues = $chartValuesFormatter->roundValues($ulamValues,2);
                $ulamKeys = $chartValuesFormatter->implode($ulamKeys);
                $ulamValues = $chartValuesFormatter->implode($ulamValues);
            ?>

            createEntropyChart(
                'ulam-chart',
                'Wykres entropii Shannona dla automatu komórkowego 0-wymiarowego określonego przez funkcję Ulama',
                [<?php echo $ulamKeys;?>],
                [<?php echo $ulamValues;?>],
                <?php echo $ulamValueBorders['max'] + 0.2;?>,
                [<?php echo $ulamRoundValues;?>]
            );

        </script>
    </body>
</html>
