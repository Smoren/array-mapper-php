<?php

namespace Smoren\ArrayMapper;

use Smoren\ExtendedExceptions\BaseException;

class ArrayMapperException extends BaseException
{
    public const STATUS_FIELD_NOT_EXIST = 1;
    public const STATUS_SCALAR_SOURCE_ITEM = 2;
    public const STATUS_NON_SCALAR_FIELD_VALUE = 3;
}
