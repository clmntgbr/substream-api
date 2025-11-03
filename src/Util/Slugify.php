<?php

declare(strict_types=1);

namespace App\Util;

class Slugify
{
    private static string $separator = '-';
    private static bool $lowercase = true;
    private static ?int $maxLength = null;

    public function __construct(
        string $separator = '-',
        bool $lowercase = true,
        ?int $maxLength = null,
    ) {
        self::$separator = $separator;
        self::$lowercase = $lowercase;
        self::$maxLength = $maxLength;
    }

    public static function slug(string $text): string
    {
        $text = trim($text);

        $transliterated = self::transliterate($text);

        if (self::$lowercase) {
            if (function_exists('mb_strtolower')) {
                $transliterated = mb_strtolower($transliterated, 'UTF-8');
            } else {
                $transliterated = strtolower($transliterated);
            }
        }

        $sep = preg_quote(self::$separator, '/');
        $slug = preg_replace('/[^A-Za-z0-9]+/u', self::$separator, $transliterated);
        $slug = is_string($slug) ? $slug : '';
        $slug = preg_replace("/^{$sep}+|{$sep}+$/u", '', $slug);
        $slug = is_string($slug) ? $slug : '';
        $slug = preg_replace("/{$sep}{2,}/u", self::$separator, $slug);
        $slug = is_string($slug) ? $slug : '';

        if (null !== self::$maxLength && self::$maxLength > 0 && '' !== $slug) {
            if (function_exists('mb_substr')) {
                $slug = mb_substr($slug, 0, self::$maxLength, 'UTF-8');
            } else {
                $slug = substr($slug, 0, self::$maxLength);
            }
            $slug = preg_replace("/{$sep}+$/u", '', $slug);
            $slug = is_string($slug) ? $slug : '';
        }

        return '' === $slug ? 'n-a' : $slug;
    }

    private static function transliterate(string $text): string
    {
        if (class_exists(\Transliterator::class)) {
            try {
                $t = \Transliterator::create('Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC');
                if (null !== $t) {
                    $result = $t->transliterate($text);
                    if (false !== $result && '' !== $result) {
                        return $result;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if (false !== $converted && '' !== $converted) {
                return $converted;
            }
        }

        $map = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a', 'ā' => 'a',
            'ç' => 'c', 'č' => 'c', 'ć' => 'c',
            'đ' => 'd', 'ď' => 'd',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ė' => 'e', 'ę' => 'e',
            'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ī' => 'i', 'į' => 'i', 'ì' => 'i',
            'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n',
            'ô' => 'o', 'ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'õ' => 'o', 'ő' => 'o', 'ō' => 'o',
            'ř' => 'r', 'ŕ' => 'r',
            'š' => 's', 'ś' => 's', 'ș' => 's', 'ş' => 's',
            'ť' => 't', 'ț' => 't', 'ţ' => 't',
            'û' => 'u', 'ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'ū' => 'u', 'ů' => 'u',
            'ÿ' => 'y', 'ý' => 'y',
            'ž' => 'z', 'ż' => 'z', 'ź' => 'z',
            'Æ' => 'AE', 'æ' => 'ae', 'Ø' => 'O', 'ø' => 'o', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe',
        ];
        $lower = $text;
        $lower = strtr($lower, $map);
        $filtered = preg_replace('/[^\x00-\x7F]/', '', $lower);

        return is_string($filtered) ? $filtered : $text;
    }
}
