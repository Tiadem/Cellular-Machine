<?php

namespace View\Helpers;

class ViewDataParametersMapperFactory{

    public static function createHeaderParametersMapper(): ViewDataParametersMapperInterface{
        return new ViewDataHeaderParametersMapper();
    }
    public static function createBodyParametersMapper(): ViewDataParametersMapperInterface{
        return new ViewDataBodyParametersMapper();
    }
}
