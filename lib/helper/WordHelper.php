<?php

if (!function_exists('get_reduced_raw_text')) {
    /**
     * Reduce given raw text to defined number of words or chars. If the text is
     * reduced, the additional text is added as a marker that there is a more of
     * text
     * 
     * @param string $text Original text to reduce
     * @param int $maxWords Max number of words without spacing, or null if this should not be limit
     * @param int $maxChars Max number of chars with spacing, or null if this should not be limit
     * @param string $moreText Text to append to result if original text is reduced
     * @return string Reduced text
     */
    function get_reduced_raw_text($text, $maxWords, $maxChars, $moreText = '...') {
        $words = explode(' ', strip_tags($text));
        
        $totalChars = 0;
        $result = array();
        $isReduced = false;
        foreach ($words as $word) {
            $result[] = $word;
            $totalChars += strlen($word);
            
            // Break conditions
            // First we have to do with max chars
            // Then with word count
            if (!is_null($maxChars) && ($totalChars + (count($result)-1)) > $maxChars) {
                // Remove last word
                array_pop($result);
                $isReduced = true;
                break;
            } elseif (!is_null($maxChars) && count($result) == $maxWords) {
                $isReduced = true;
                break;
            }            
        }
        if ($isReduced) {
            return implode(' ', $result) . $moreText;
        } else {
            return implode(' ', $result);
        }
    }
}