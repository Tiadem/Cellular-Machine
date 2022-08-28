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
