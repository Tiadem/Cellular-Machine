<?php

namespace View\Strategy;

interface CreateStepViewStrategyInterface
{
    function createStepViewBody(array $bodyData): string;
}
