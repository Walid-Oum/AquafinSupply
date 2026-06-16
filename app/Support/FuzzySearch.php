<?php

namespace App\Support;

class FuzzySearch
{
    public static function matches(?string $query, string $text): bool
    {
        $query = self::normalize($query);
        $text = self::normalize($text);

        if ($query === '') {
            return true;
        }

        if (str_contains($text, $query)) {
            return true;
        }

        $queryWords = self::words($query);
        $textWords = self::words($text);

        foreach ($queryWords as $queryWord) {
            if (! self::wordMatches($queryWord, $textWords)) {
                return false;
            }
        }

        return true;
    }

    private static function wordMatches(string $queryWord, array $textWords): bool
    {
        foreach ($textWords as $textWord) {
            if ($queryWord === $textWord) {
                return true;
            }

            if (str_contains($textWord, $queryWord)) {
                return true;
            }

            if (self::isSimilar($queryWord, $textWord)) {
                return true;
            }
        }

        return false;
    }

    private static function isSimilar(string $queryWord, string $textWord): bool
    {
        $queryLength = strlen($queryWord);
        $textLength = strlen($textWord);

        if ($queryLength < 3) {
            return false;
        }

        $allowedDistance = self::allowedDistance($queryWord);

        if (levenshtein($queryWord, $textWord) <= $allowedDistance) {
            return true;
        }

        $minWindowLength = max(3, $queryLength - 2);
        $maxWindowLength = min($textLength, $queryLength + 2);

        for ($windowLength = $minWindowLength; $windowLength <= $maxWindowLength; $windowLength++) {
            for ($start = 0; $start <= $textLength - $windowLength; $start++) {
                $part = substr($textWord, $start, $windowLength);

                if (levenshtein($queryWord, $part) <= $allowedDistance) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function allowedDistance(string $word): int
    {
        $length = strlen($word);

        if ($length <= 3) {
            return 1;
        }

        if ($length <= 7) {
            return 2;
        }

        if ($length <= 12) {
            return 3;
        }

        return 4;
    }

    private static function words(string $value): array
    {
        return array_values(array_filter(explode(' ', $value)));
    }

    private static function normalize(?string $value): string
    {
        $value = mb_strtolower($value ?? '');

        $value = strtr($value, [
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'ö' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
        ]);

        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim($value ?? '');
    }
}
