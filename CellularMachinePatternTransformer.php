<?php


class CellularMachinePatternTransformer{

    private const REGEXP = ['/([\-\+]?\d+x\^\d*)/','/\%(\d+)/','/([\+\-]\d+)\%/'];
    private const SUPPORTED_POLYNOMIAL_OPERATORS = ['-','+'];

    private const POWER_OPERATOR_PLACEHOLDER = '^';
    private const SUPPORTED_UNKNOWN_PLACEHOLDER = 'x';

    private const EMPTY_VALUE = '';

    private int $modulo;

    public function __construct(private int $timeLimit,private string $pattern){

        ['modulo' => $modulo] = $this->preparePolynomialValues($pattern);
        $this->modulo = $modulo;

    }
    
    public function preparePolynomialValues(string $pattern): array{

        [$polynomialPositions,$modulo,$additional] = array_map(
            fn(string $regexp) =>$this->extractPolynomialParts($regexp,$pattern)
        ,self::REGEXP);

        $polynomialPositions = $polynomialPositions[0];
        $modulo = $modulo[0][0];
        $additional = $additional[0][0];


        $polynomialPositions = array_map(function(string $position){

            [$rest,$power] = explode(self::POWER_OPERATOR_PLACEHOLDER,$position);
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

    private function extractPolynomialParts(string $regexp,string $pattern): array{

        preg_match_all($regexp, $pattern, $matches);
        array_shift($matches);

        return array_values($matches);
    }

    private function resolvePolynomialValue(int $unknownValue,array $polynomialParts): int{

        $polynomialPositionsValue = array_reduce($polynomialParts['polynomialPositions'],function($carry, $position) use ($unknownValue){
            [
                'countUnknown' =>  $countUnknown,
                'power' => $power,
                'sign' => $sign
            ] = $position;
            $positionValue = $countUnknown*pow($unknownValue,$power);
            $sign ? $carry+= $positionValue : $carry-=$positionValue;
      
            return $carry;
        },0);

        return ($polynomialPositionsValue + $polynomialParts['additional']) % $polynomialParts['modulo'];
    }

    public function mapRecursivePolynomialValuesInTime(int &$iteration,int $previousValue,array &$values): void{
	
        $values[] = $previousValue;

        $pattern = $this->preparePolynomialValues($this->pattern);
        $currentValue = $this->resolvePolynomialValue($previousValue,$pattern);

        if($iteration >= $this->timeLimit) return;
        $iteration++;

        $this->mapRecursivePolynomialValuesInTime($iteration,$currentValue,$values);
        
      
    }
    
    public function mapRecursivePolynomialValuesByModulo(): array{
    
        $all = [];
        for($i=0;$i < $this->modulo;$i++){
            $values = [];
            $iteration = 0;
            $this->mapRecursivePolynomialValuesInTime($iteration,$i,$values);
            $all[] = $values;
        }
        return $all;
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

        $inverseRows = $this->inverseRows();
    
        $formattedArray = [];
        foreach($inverseRows as $row){
            $formattedArray[] = array_count_values($row);
        }

        return $formattedArray;
    
    }
    public function divideAndFillWithBlankValues(): array{
    
        $differentValues = $this->countDifferentValues();
    
        $dividedValuesArray = [];
    
        foreach($differentValues as $row){
            $dividedValuesRow = [];
            ksort($row);
            for($i = 0;$i < $this->modulo;$i++){
                $dividedValuesRow[] = array_key_exists($i,$row) ? $row[$i]/$this->modulo : 0;
            }
            $dividedValuesArray[] = $dividedValuesRow;
      
        }

        return $dividedValuesArray;
    
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
