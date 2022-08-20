<?php
namespace View\Helpers;

class ChartValuesFormatter
{
    const SEPARATOR = ', ';

    public function roundValues(array $values,int $decimals): string{
        return self::implode(array_map(
            fn($value) => number_format($value,$decimals,'.',''),
            $values
        ));
    }
    public function implode(array $data): string{
        return implode(', ',$data);
    }
}
