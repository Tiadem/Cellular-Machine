<?php

class CreateViewBodyDecorator implements CreatViewBodyStrategyInterface
{
    public function __construct(private CreatViewBodyStrategyInterface $strategy){}

    public function createBody(array $bodyData): string{

        $newBodyData = array_map(function($row){
            array_walk($row, function(&$value,$index){
                $value = sprintf(
                    '<span class="concatinated-value"><b>(%d) </b> %s </span>',
                    $index,
                    is_float ($value) ?
                        number_format($value,8,'.','') : (string) $value
                );
            });

            return '<div class="concatinated-values-wrapper">'.implode(
                '',
                $row
            ).'</div>';
        },$bodyData);

        return $this->strategy->createBody($newBodyData);
    }
}
