<?php

namespace CellularMachine\EquationPattern;

abstract class AbstractEquationPatternResolver implements EquationPatternResolverInterface
{
    const POLYNOMIAL_PATTERN_REGEXP = [
        '/([\-\+]?\d+x\^\d*[\/]?\d*)/',
        '/\%(\d+)/',
        '/([\+\-]\d+)\%/'
    ];

    const SUPPORTED_POLYNOMIAL_OPERATORS = ['-','+','/'];

    const POWER_OPERATOR_PLACEHOLDER = '^';
    const SUPPORTED_UNKNOWN_PLACEHOLDER = 'x';

    const EMPTY_VALUE = '';

    protected function preparePolynomialValues(string $pattern): array{

        [$polynomialPositions,$modulo,$additional] = array_map(
            fn(string $regexp) =>$this->extractPolynomialParts($regexp,$pattern)
            ,self::POLYNOMIAL_PATTERN_REGEXP);

        $polynomialPositions = $polynomialPositions[0];
        $modulo = $modulo[0][0];
        $additional = $additional[0][0] ?? 0;

        $polynomialPositions = array_map(function(string $position){

            [$rest,$hasBeenDivided] = explode(self::POWER_OPERATOR_PLACEHOLDER,$position);

            $hasBeenDivided = explode(self::SUPPORTED_POLYNOMIAL_OPERATORS[2],$hasBeenDivided);
            $power = $hasBeenDivided[0];

            $divedBy  = $hasBeenDivided[1] ?? 1;

            $rest = str_replace(self::SUPPORTED_UNKNOWN_PLACEHOLDER,self::EMPTY_VALUE,$rest);
            $sign = !(str_starts_with($rest, self::SUPPORTED_POLYNOMIAL_OPERATORS[0]));

            $countUnknown = str_replace(
                self::SUPPORTED_POLYNOMIAL_OPERATORS,
                [
                    self::EMPTY_VALUE,
                    self::EMPTY_VALUE
                ],$rest);

            return [
                'countUnknown' => (int) $countUnknown,
                'divided' =>(int) $divedBy,
                'power' => (int) $power,
                'sign' => $sign
            ];

        },$polynomialPositions);

        return [
            'polynomialPositions' => $polynomialPositions,
            'additional' => (int) $additional,
            'modulo' => $modulo
        ];

    }

    protected function extractPolynomialParts(string $regex,string $pattern): array{

        preg_match_all($regex, $pattern, $matches);
        array_shift($matches);

        return array_values($matches);
    }

    protected function sumPolynomialPositions(int $unknownValue,array $polynomialPositions):float{

        return array_reduce($polynomialPositions,function($carry, $position) use ($unknownValue){
            [
                'countUnknown' =>  $countUnknown,
                'power' => $power,
                'sign' => $sign,
                'divided' => $dividedBy
            ] = $position;

            $positionValue = ($countUnknown*pow($unknownValue,$power))/$dividedBy;
            $sign ? $carry+= $positionValue : $carry-=$positionValue;

            return $carry;
        },0);
    }
}
