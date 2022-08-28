<?php

namespace CellularMachine;

use CellularMachine\EquationPattern\EquationPatternResolverInterface;

final class CellularMachinePatternTransformer{

    private int $modulo;

    public function __construct(private int $timeLimit,private EquationPatternResolverInterface $patternResolver){

        $this->modulo = $this->patternResolver->provideModulo();

    }

    public function setEquationPatternResolver(EquationPatternResolverInterface $patternResolver): self{

        $this->patternResolver = $patternResolver;
        $this->modulo = $patternResolver->provideModulo();

        return $this;
    }

    public function mapRecursivePolynomialValuesInTime(int &$iteration,int $previousValue,array &$values): void{
	
        $values[] = $previousValue;

        $currentValue = $this->patternResolver->resolvePolynomialValue($previousValue);

        if($iteration >= $this->timeLimit) return;
        $iteration++;

        $this->mapRecursivePolynomialValuesInTime($iteration,$currentValue,$values);

    }
    
    public function mapRecursivePolynomialValuesByModulo(): array{

        $mappedValues = array_fill(0,$this->modulo,0);

        array_walk($mappedValues,function(int &$iteration,int $moduloKey){
            $values = [];
            $this->mapRecursivePolynomialValuesInTime($iteration,$moduloKey,$values);
            $iteration = $values;
        });

        return $mappedValues;

    }
    
    public function inverseRows(): array{
    
        $twoDimensionalArray = $this->mapRecursivePolynomialValuesByModulo();
        $inverseArray = [];

        for($i = 0;$i < $this->timeLimit;$i++){
            $inverseArray[] = array_column($twoDimensionalArray,$i);	
        }
    
      return $inverseArray;	
    
    }
    
    public function countDifferentValues(): array{
        return array_map(fn (array $row) =>  array_count_values($row),$this->inverseRows());
    }

    public function divideAndFillWithBlankValues(): array{

        return array_map(function(array $row){
            $dividedValuesRow = [];
            ksort($row);
            for($i = 0;$i < $this->modulo;$i++){
                $dividedValuesRow[] = array_key_exists($i,$row) ? $row[$i]/$this->modulo : 0;
            }
            return $dividedValuesRow;
        },$this->countDifferentValues());

    }
    
    public function sumRowValuesWithLog(): array{
    
        $dividedValuesArray = $this->divideAndFillWithBlankValues();

        return array_map(function(array $row){
            return abs(array_reduce($row,function($carry, $rowValue){
                $carry += $rowValue != 0 ? $rowValue*log($rowValue,2) : 0;
                return $carry;
            },0));
        
        },$dividedValuesArray);
        
    }
    
    public function provideLabelsAndValues(): array{
    
        $sumRowValuesWithLogArray = $this->sumRowValuesWithLog();

        return [array_keys($sumRowValuesWithLogArray),array_values($sumRowValuesWithLogArray)];
    
    }
    public function provideMinAndMaxValues(): array{

        $sumRowValuesWithLogArray = $this->provideLabelsAndValues();

        $getBorders = function(array $borderSource){
            return [
                'min' => array_shift($borderSource),
                'max'=> end($borderSource)
            ];
        };

        sort($sumRowValuesWithLogArray[1]);

        $labelBorders = $getBorders($sumRowValuesWithLogArray[0]);
        $valueBorders = $getBorders($sumRowValuesWithLogArray[1]);

        return [$labelBorders,$valueBorders];

    }
}
