<?php

namespace tecnocen\roa\openApi;

interface DataType
{
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY = 'array';
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';

    const FORMAT_INTEGER = 'int32';
    const FORMAT_LONG = 'int64';
    const FORMAT_FLOAT = 'float';
    const FORMAT_DOUBLE = 'double';
    const FORMAT_BYTE = 'byte';
    const FORMAT_BINARY = 'binary';
    const FORMAT_DATE = 'date';
    const FORMAT_DATETIME = 'datetime';
    const FORMAT_PASSWORD = 'password';
}
