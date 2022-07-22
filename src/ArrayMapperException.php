<?php


namespace Smoren\ArrayMapper;


use Smoren\ExtendedExceptions\BaseException;

class ArrayMapperException extends BaseException
{
    const STATUS_FIELD_NOT_EXIST = 1;
    const STATUS_SCALAR_SOURCE_ITEM = 2;
    const STATUS_NON_SCALAR_FIELD_VALUE = 3;
}
