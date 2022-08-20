<?php

namespace View\Helpers;

interface ViewDataParametersMapperInterface
{
    const CELLULAR_MACHINE_TRANSFORM_STEPS = [
        'sumRowValuesWithLog',
        'divideAndFillWithBlankValues',
        'countDifferentValues',
        'inverseRows',
        'mapRecursivePolynomialValuesByModulo'
    ];

    function mapParameters(array $parameters): array;
}
