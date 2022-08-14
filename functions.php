<?php

function recursive(int $limit,int &$iteration,$previousValue,array &$values){
	
	$values[] = $previousValue;
	$currentValue = (pow($previousValue,2)+1) % 37;

	if($iteration >= $limit){
		return;	
	}
	
	$iteration++;
	recursive($limit,$iteration,$currentValue,$values);
	
  
}

function countAll(int $modulo,int $timeLimit){

	$all = [];
	for($i=0;$i < $modulo;$i++){
		$values = [];
		$iteration = 0;
		recursive($timeLimit,$iteration,$i,$values);
		$all[] = $values;
	}
	return $all;
}

function inverseRows(int $modulo,int $timeLimit){

	$twoDimensionalArray = countAll($modulo,$timeLimit);
	$inversedArray = [];

  for($i = 0;$i < $timeLimit;$i++){
    $inversedArray[] = array_column($twoDimensionalArray,$i);	
  }

  return $inversedArray;	

}

function countDiffrentValues(int $modulo,int $timeLimit){

	$inversedRows = inverseRows($modulo,$timeLimit);

    $formattedArray = [];
    foreach($inversedRows as $row){
        $formattedArray[] = array_count_values($row);
    }
    
    return $formattedArray;

}
function divideAndFillWithBlankValues(int $modulo,int $timeLimit){

	$diffrentValues = countDiffrentValues($modulo,$timeLimit);

    $dividedValuesArray = [];

    foreach($diffrentValues as $row){
        $dividedValuesRow = [];
        ksort($row);
        for($i = 0;$i < $modulo;$i++){
            $dividedValuesRow[] = array_key_exists($i,$row) ? $row[$i]/$modulo : 0;
        }
        $dividedValuesArray[] = $dividedValuesRow;
  
    }

    return $dividedValuesArray;

}

function sumRowValuesWithLog(int $modulo,int $timeLimit){

	$dividedValuesArray = divideAndFillWithBlankValues($modulo,$timeLimit);

    $sumRowValuesArray = array_map(function($row){
        return abs(array_reduce($row,function($carry, $rowValue){
            $carry += $rowValue != 0 ? $rowValue*log($rowValue,2) : 0;
            return $carry;
        },0));
    
    },$dividedValuesArray);

    return $sumRowValuesArray;

}

function provideLabelsAndValues(int $modulo,int $timeLimit){

	$sumRowValuesWithLogArray = sumRowValuesWithLog($modulo,$timeLimit);

    return [array_keys($sumRowValuesWithLogArray),array_values($sumRowValuesWithLogArray)];

}