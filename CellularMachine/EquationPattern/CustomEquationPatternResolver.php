<?php

namespace CellularMachine\EquationPattern;

class CustomEquationPatternResolver extends AbstractEquationPatternResolver
{
    private array $mappedPattern;

    public function __construct(private string $pattern){
        $this->mappedPattern = $this->preparePolynomialValues($this->pattern);
    }

    public function provideModulo(): int{
        return (int) $this->mappedPattern['modulo'];
    }

    public function resolvePolynomialValue(int $unknownValue): int{

        $polynomialPositionsValue = $this->sumPolynomialPositions($unknownValue,$this->mappedPattern['polynomialPositions']);

        return ($polynomialPositionsValue + $this->mappedPattern['additional']) % $this->mappedPattern['modulo'];
    }
}
