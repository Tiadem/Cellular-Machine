<?php

namespace CellularMachine\EquationPattern;

interface EquationPatternResolverInterface
{
    function provideModulo(): int;
    function resolvePolynomialValue(int $unknownValue): int;
}
