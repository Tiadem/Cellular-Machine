<?php

namespace View\Helpers;

class ViewDataHeaderParametersMapper implements ViewDataParametersMapperInterface
{
    public function mapParameters(array $parameters): array
    {
        $parameters = array_combine(CELLULAR_MACHINE_TRANSFORM_STEPS,$parameters);

        array_walk($parameters,function (&$title, $transformStep)
        {
            $title = [
                'transformStep' => $transformStep,
                'title' => $title
            ];
        });
        return array_values($parameters);
    }
}
