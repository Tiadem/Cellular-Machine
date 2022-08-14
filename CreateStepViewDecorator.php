<?php

class CreateStepViewDecorator implements CreateStepViewStrategyInterface
{
    public function __construct(private CreateStepViewStrategyInterface $strategy){}

    public function createStepViewBody(array $bodyData): string{

        $newBodyData = array_map(function(array $row){
            array_walk($row, function(mixed &$value,int $index){
                $value = sprintf(
                    '<span class="concatenated-value"><b>(%d) </b> %s </span>',
                    $index,
                    is_float ($value) ?
                        number_format($value,8,'.','') : (string) $value
                );
            });

            return '<div class="concatenated-values-wrapper">'.implode(
                '',
                $row
            ).'</div>';
        },$bodyData);

        return $this->strategy->createStepViewBody($newBodyData);
    }
}
