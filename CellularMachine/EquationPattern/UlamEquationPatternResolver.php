<?php

namespace CellularMachine\EquationPattern;

class UlamEquationPatternResolver extends AbstractEquationPatternResolver
{
    private const EVEN = 'even';
    private const ODD = 'odd';

    private const FULL_PATTERN_TEMPLATE = '%s%%%d';
    private const ULAM_FUNCTION = [
        self::EVEN => '1x^1/2',
        self::ODD => '3x^1+1'
    ];
    private array $mappedPatterns = [
        self::EVEN => [],
        self::ODD => []
    ];
    public function __construct(private int $modulo){

        $this->setMappedPattern(self::EVEN,$modulo);
        $this->setMappedPattern(self::ODD,$modulo);

    }

    private function setMappedPattern(string $parity,int $modulo): void{
        $this->mappedPatterns[$parity] =  $this->preparePolynomialValues(
            sprintf(self::FULL_PATTERN_TEMPLATE,self::ULAM_FUNCTION[$parity],$modulo)
        );
    }

    public function provideModulo(): int
    {
        return $this->modulo;
    }

    public function resolvePolynomialValue(int $unknownValue): int{

        $mappedPattern = $unknownValue % 2 == 0 ?
            $this->mappedPatterns[self::EVEN] :
            $this->mappedPatterns[self::ODD];
        $polynomialPositionsValue = $this->sumPolynomialPositions($unknownValue,$mappedPattern['polynomialPositions']);

        return ($polynomialPositionsValue + $mappedPattern['additional']) % $mappedPattern['modulo'];
    }
}
