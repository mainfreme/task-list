<?php

declare(strict_types=1);

namespace App\Settings\Domain\ValueObject;

enum SettingFieldType: string
{
    case String = 'string';
    case Int = 'int';
    case Bool = 'bool';
    case Json = 'json';

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
