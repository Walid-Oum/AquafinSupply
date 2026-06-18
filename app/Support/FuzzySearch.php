<?php

namespace App\Support;

/**
 * FuzzySearch Class
 * 
 * Biedt fouttolerante zoekfunctionaliteit voor tekst.
 * Gebruikt Levenshtein afstand, accent-normalisatie en 
 * substring matching om spellingsfouten te tolereren.
 *
 * @author 
 * @version 1.0
 */
class FuzzySearch
{
    /**
     * Controleert of een query overeenkomt met een tekst.
     *
     * @param string|null $query
     * @param string $text
     * @return bool
     */
    public static function matches(?string $query, string $text): bool
    {
        $query = self::normalize($query);
        $text = self::normalize($text);

        if ($query === '') {
            return true;
        }

        // 1. Directe match (inclusief hoofdletter- en accentcorrectie dankzij normalize)
        if (str_contains($text, $query)) {
            return true;
        }

        // 2. Match zonder spaties (zoekt "water pomp" op als "waterpomp" en vice versa)
        $flatQuery = str_replace(' ', '', $query);
        $flatText = str_replace(' ', '', $text);
        if (str_contains($flatText, $flatQuery)) {
            return true;
        }

        // 3. Globale Levenshtein check op de platte tekst (voor typfouten over de hele zin)
        if (strlen($flatQuery) >= 3) {
            $allowedDistance = self::allowedDistance($flatQuery);
            if (levenshtein($flatQuery, $flatText) <= $allowedDistance) {
                return true;
            }
        }

        // 4. Splitsen in woorden voor de fijnmazige fuzzy search
        $queryWords = self::words($query);
        $textWords = self::words($text);

        foreach ($queryWords as $queryWord) {
            if (! self::wordMatches($queryWord, $textWords)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Controleert of een woord overeenkomt met een van de tekstwoorden.
     *
     * @param string $queryWord
     * @param array $textWords
     * @return bool
     */
    private static function wordMatches(string $queryWord, array $textWords): bool
    {
        foreach ($textWords as $textWord) {
            if ($queryWord === $textWord) {
                return true;
            }

            if (str_contains($textWord, $queryWord) || str_contains($queryWord, $textWord)) {
                return true;
            }

            if (self::isSimilar($queryWord, $textWord)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bepaalt of twee woorden gelijkaardig zijn via Levenshtein afstand.
     *
     * @param string $queryWord
     * @param string $textWord
     * @return bool
     */
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

    /**
     * Berekent de maximaal toegestane Levenshtein afstand op basis van woordlengte.
     *
     * @param string $word
     * @return int
     */
    private static function allowedDistance(string $word): int
    {
        $length = strlen($word);

        if ($length <= 4) {
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

    /**
     * Splitst een tekst in woorden.
     *
     * @param string $value
     * @return array
     */
    private static function words(string $value): array
    {
        return array_values(array_filter(explode(' ', $value)));
    }

    /**
     * Normaliseert een tekst: kleine letters, accenten verwijderen,
     * enkel alfanumerieke tekens behouden.
     *
     * @param string|null $value
     * @return string
     */
    private static function normalize(?string $value): string
    {
        $value = mb_strtolower($value ?? '');

        $value = strtr($value, [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        ]);

        // Vervang alle non-alphanumerieke tekens door een spatie
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);
        
        // Zorg dat meerdere opeenvolgende spaties één enkele spatie worden
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }
}