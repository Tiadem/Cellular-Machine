<?php

namespace View\Helpers;

class ViewDataBodyParametersMapper implements ViewDataParametersMapperInterface
{
    public function mapParameters(array $parameters): array{
        $parameters = array_combine(self::CELLULAR_MACHINE_TRANSFORM_STEPS,$parameters);

        array_walk($parameters,function (&$strategy, $transformStep)
        {
            $strategy = [
                'transformStep' => $transformStep,
                'strategy' => $strategy
            ];
        });

        return array_values($parameters);
    }
}
