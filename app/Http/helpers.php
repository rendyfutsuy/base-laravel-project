<?php

use Illuminate\Support\Str;

if (! function_exists('base64url_encode')) {
    /**
     * @param  mixed  $data
     * @return string
     */
    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (! function_exists('base64url_decode')) {
    /**
     * @param  mixed  $data
     * @return string
     */
    function base64url_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/').str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}

if (! function_exists('round_up')) {
    /**
     * @param  float  $value
     * @param  int  $precision
     * @return float
     */
    function round_up($value, $precision = 2)
    {
        return round($value, $precision);
    }
}

if (! function_exists('random_str')) {
    function random_str(int $length, string $keyspace = ''): string
    {
        $keyspace = $keyspace ?: '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; $i++) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }
}

if (! function_exists('number_abbr')) {
    function number_abbr(int $number, int $precision = 1): string
    {
        if ($number < 900) {
            // 0 - 900
            $n_format = number_format($number, $precision, ',', '.');
            $suffix = '';
        } elseif ($number < 900000) {
            // 0.9rb-850rb
            $n_format = number_format($number / 1000, $precision, ',', '.');
            $suffix = 'rb';
        } elseif ($number < 900000000) {
            // 0.9 juta-850 juta
            $n_format = number_format($number / 1000000, $precision, ',', '.');
            $suffix = 'jt';
        } elseif ($number < 900000000000) {
            // 0.9 milyar-850 milyar
            $n_format = number_format($number / 1000000000, $precision, ',', '.');
            $suffix = 'm';
        } else {
            // 0.9t+
            $n_format = number_format($number / 1000000000000, $precision, ',', '.');
            $suffix = 't';
        }

        // Remove unnecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotZero = ','.str_repeat('0', $precision);
            $n_format = str_replace($dotZero, '', $n_format);
        }

        return $n_format.$suffix;
    }
}

if (! function_exists('price_abbr')) {
    function price_abbr(float $number): string
    {
        return number_format($number, 0, '.', ',');
    }
}

if (! function_exists('price_formatted')) {
    function price_formatted(int $number, string $currencyCode = 'Rp.'): string
    {
        return $currencyCode.number_format($number, 0, ',', '.');
    }
}

if (! function_exists('phone')) {
    function phone(?string $phoneNumber, $countryCode = 62): ?array
    {
        $value = normalize_string($phoneNumber ?? '_');

        if (! is_numeric($value)) {
            return null;
        }

        $phones = [];
        if (substr($value, 0, 2) == '08') {
            $phones['international'] = '0'.substr($value, 1, strlen($value));
            $phones['local'] = $countryCode.substr($value, 1, strlen($value));
            $phones['without_code'] = substr($value, 1, strlen($value));

            return $phones;
        }

        if (substr($value, 0, 2) == $countryCode) {
            $phones['international'] = '0'.substr($value, 2, strlen($value));
            $phones['local'] = $countryCode.substr($value, 2, strlen($value));
            $phones['without_code'] = substr($value, 2, strlen($value));

            return $phones;
        }

        if (substr($value, 0, 3) == '+'.$countryCode) {
            $phones['international'] = '0'.substr($value, 3, strlen($value));
            $phones['local'] = $countryCode.substr($value, 3, strlen($value));
            $phones['without_code'] = substr($value, 3, strlen($value));

            return $phones;
        }

        return [
            'international' => '0'.$value,
            'local' => $countryCode.$value,
            'without_code' => $value,
        ];
    }
}

if (! function_exists('mask_email')) {
    function mask_email(string $email): string
    {
        $chunks = explode('@', $email);
        $strLength1 = strlen($chunks['0']);
        $firstChar1 = substr($chunks['0'], 0, (int) floor($strLength1 * 0.3));
        $stars1 = str_repeat('*', $strLength1 - 1);

        if (count($chunks) == 1) {
            return $firstChar1.$stars1.substr($chunks['0'], -2);
        }

        $strLength2 = strlen($chunks['1']);
        $firstChar2 = substr($chunks['1'], 0, -1 * ($strLength2 - 3));
        $stars2 = str_repeat('*', $strLength2 - 3);

        $email = $firstChar1.$stars1.'@'.$firstChar2.$stars2;

        return $email;
    }
}

if (! function_exists('normalize_words')) {
    /**
     * @param  string  $value
     * @return int
     */
    function normalize_words($value)
    {
        $value = str_replace('> ', '>', $value);
        $value = str_replace('/> ', '/>', $value);
        $value = str_replace(' <', '<', $value);
        $value = str_replace(' </', '</', $value);

        $decoded_text = strip_tags($value);

        return preg_replace("/\r\n|\r|\n/", ' ', preg_replace('/(\&nbsp\;|\s)+/', ' ', $decoded_text));
    }
}

if (! function_exists('word_counter')) {
    /**
     * @param  string  $value
     * @return int
     */
    function word_counter($value)
    {
        $words = normalize_words($value);
        $words = Str::of($words)->replace('_', ' ');
        $words = Str::of($words)->replace('-', ' ');

        return count(explode(' ', $words));
    }
}

if (! function_exists('normalize_string')) {
    /**
     * @param  string  $value
     * @return string|null
     */
    function normalize_string($value)
    {
        $value = str_replace('-', '', $value);

        return preg_replace('/[^A-Za-z0-9\-]/', '', $value);
    }
}

if (! function_exists('title')) {
    /**
     * @param  string  $value
     * @return string|null
     */
    function title($value)
    {
        return Str::remove(' ', ucwords(Str::of($value)->replace('_', ' ')));
    }
}
