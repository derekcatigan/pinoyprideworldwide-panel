<?php

namespace App\Enum;

enum BoxType: string
{
    case Large = 'large';

    case Jumbo = 'jumbo';

    case ODD = 'odd';

    public function displayBoxType(): string
    {
        return match ($this) {
            self::Large => 'Large',
            self::Jumbo => 'Jumbo',
            self::ODD => 'ODD',
        };
    }

    public static function options(array $exclude = []): array
    {
        $cases = array_filter(self::cases(), fn(self $c) => !in_array($c, $exclude, true));

        return array_map(fn(self $type) => [
            'name'  => $type->displayBoxType(),
            'value' => $type->value,
        ], $cases);
    }
}
