<?php

namespace Smoren\ArrayMapper;

use phpDocumentor\Reflection\Types\Scalar;

/**
 * Mapper helper
 */
class ArrayMapper
{
    /**
     * Maps array with map fields
     * @param array<array<mixed>>|array<object> $input array of arrays or objects you want to map
     * @param array<scalar|callable> $mapFields fields for mapping (scalar or callable)
     * @param bool $multiple support multiple results on mapping
     * @param bool $ignoreNulls ignore items with nullable mapping fields values
     * @param callable|null $valueGetter callable value getter
     * @return array<mixed> mapped array
     * @throws ArrayMapperException
     */
    public static function map(
        array $input,
        array $mapFields,
        bool $multiple,
        bool $ignoreNulls = true,
        ?callable $valueGetter = null
    ): array {
        $result = [];

        foreach($input as $item) {
            if($ignoreNulls && !static::isFieldsNotNull($item, $mapFields)) {
                continue;
            }
            $resultPointer = &$result;
            foreach($mapFields as $field) {
                $fieldValue = static::getFieldValue($item, $field);
                if(!is_scalar($fieldValue)) {
                    $field = strval($field);
                    throw new ArrayMapperException(
                        "field value of '{$field}' is not scalar",
                        ArrayMapperException::STATUS_NON_SCALAR_FIELD_VALUE,
                        null,
                        [
                            'fieldName' => $field,
                            'fieldValue' => $fieldValue,
                            'source' => $item,
                        ]
                    );
                }
                /** @var array<string|int|bool|null, mixed> $resultPointer */
                if(!isset($resultPointer[$fieldValue])) {
                    $resultPointer[$fieldValue] = [];
                }
                $resultPointer = &$resultPointer[$fieldValue];
            }

            $value = is_callable($valueGetter) ? $valueGetter($item) : $item;

            if($multiple) {
                /** @var array<mixed> $resultPointer */
                $resultPointer[] = $value;
            } else {
                $resultPointer = $value;
            }
        }

        return $result;
    }

    /**
     * Checks that field values are not null for source item
     * @param array<mixed>|object $source source item
     * @param array<scalar|callable> $fieldNames field names
     * @return bool
     */
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
     * Returns field value for source item and field name
     * @param array<mixed>|object $source source item
     * @param string|callable $fieldName field name
     * @return mixed field value
     * @throws ArrayMapperException
     */
    protected static function getFieldValue($source, $fieldName)
    {
        if(is_callable($fieldName)) {
            return $fieldName($source);
        }

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
