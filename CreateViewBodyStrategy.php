<?php

class CreateViewBodyStrategy implements CreatViewBodyStrategyInterface
{

    public function createBody(array $bodyData): string{
        return array_reduce($bodyData,function($carry, $rowValue){
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
