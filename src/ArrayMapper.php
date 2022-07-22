<?php


namespace Smoren\ArrayMapper;


use Smoren\ExtendedExceptions\BaseException;

/**
 * Mapper helper
 */
class ArrayMapper
{
    /**
     * @param array $input
     * @param array $mapFields
     * @param bool $multiple
     * @param bool $ignoreNulls
     * @param callable|null $valueGetter
     * @return array
     * @throws ArrayMapperException
     */
    public static function map(
        array $input, array $mapFields, bool $multiple, bool $ignoreNulls = true, ?callable $valueGetter = null
    ): array
    {
        $result = [];

        foreach($input as $item) {
            if($ignoreNulls && !static::isFieldsNotNull($item, $mapFields)) {
                continue;
            }
            $resultPointer = &$result;
            foreach($mapFields as $fieldName) {
                $fieldValue = static::getFieldValue($item, $fieldName);
                if(!is_scalar($fieldValue)) {
                    throw new ArrayMapperException(
                        "field value of '{$fieldName}' is not scalar",
                        ArrayMapperException::STATUS_NON_SCALAR_FIELD_VALUE,
                        null,
                        [
                            'fieldName' => $fieldName,
                            'fieldValue' => $fieldValue,
                            'source' => $item,
                        ]
                    );
                }
                if(!isset($resultPointer[$fieldValue])) {
                    $resultPointer[$fieldValue] = [];
                }
                $resultPointer = &$resultPointer[$fieldValue];
            }

            $value = is_callable($valueGetter) ? $valueGetter($item) : $item;

            if($multiple) {
                $resultPointer[] = $value;
            } else {
                $resultPointer = $value;
            }
        }

        return $result;
    }

    protected static function isFieldsNotNull($source, array $fieldNames): bool
    {
        foreach($fieldNames as $fieldName) {
            try {
                if(static::getFieldValue($source, $fieldName) === null) {
                    return false;
                }
            } catch(ArrayMapperException $e) {
                return false;
            }

        }

        return true;
    }

    /**
     * @param $source
     * @param $fieldName
     * @return mixed
     * @throws ArrayMapperException
     */
    protected static function getFieldValue($source, $fieldName)
    {
        if(
            is_array($source) && !array_key_exists($fieldName, $source)
            || is_object($source) && !isset($source->{$fieldName}) && !property_exists($source, $fieldName)
        ) {
            throw new ArrayMapperException(
                "field '{$fieldName}' not exist",
                ArrayMapperException::STATUS_FIELD_NOT_EXIST,
                null,
                [
                    'fieldName' => $fieldName,
                    'source' => $source,
                ]
            );
        }

        if(is_array($source)) {
            return $source[$fieldName];
        } elseif(is_object($source)) {
            return $source->{$fieldName};
        } else {
            throw new ArrayMapperException(
                "source item is scalar",
                ArrayMapperException::STATUS_SCALAR_SOURCE_ITEM,
                null,
                [
                    'source' => $source,
                ]
            );
        }
    }
}
