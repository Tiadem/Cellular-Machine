<?php

class CreateStepViewStrategy implements CreateStepViewStrategyInterface
{

    public function createStepViewBody(array $bodyData): string{
        return array_reduce($bodyData,function(array $carry, mixed $rowValue){
            $index = $carry['index'];

            $carry['body'].= "
                <tr>
                    <td>
                        $index
                    </td>
                    <td>
                        $rowValue
                    </td>
                </tr>
            ";
            $carry['index']++;
            return $carry;
        },['body' => '', 'index' => 0])['body'];

    }
}
