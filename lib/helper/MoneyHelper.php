<?php

if (!function_exists('format_amount')) {
    /*
    * Author: TheCelavi
    *
    * @param mixed $amount The amount to format
    * @param string $format The name of format from the configuration
    * 
    * @return number Formatted amount to display
    */
    function format_amount($amount, $format = 'default')
    {
        $settings = sfConfig::get('dm_dmCommonHelpersPlugin_money_helper_settings');
        $formatSettings = $settings['formats'][$format];

        if (is_string($amount)) $amount = floatval($amount);

        $amount = number_format($amount, $formatSettings['decimals'], $formatSettings['decimals_point'], $formatSettings['thousands_point']);

        if ($formatSettings['currency_symbol_position'] == 'after') {
            return $amount . $formatSettings['currency_separator'] . $formatSettings['currency_sign'];
        } else {
            return $formatSettings['currency_sign'] . $formatSettings['currency_separator'] .  $amount;
        }
    }
}