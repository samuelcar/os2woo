<?php

/**
 * Returns the percentage of a quantity given a base number
 *
 * @param      $base
 * @param      $quantity
 * @param bool $string_format
 * @return float|string
 */
function percentageOf($base, $quantity, $string_format = true)
{
    $value = ($quantity * 100) / $base;

    return $string_format ? number_format($value).'%' : $value;
}

/**
 * Wordpress function to generate random passwords
 *
 * @param int        $length
 * @param bool|true  $special_chars
 * @param bool|false $extra_special_chars
 * @return string
 */
function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ( $special_chars )
        $chars .= '!@#$%^&*()';
    if ( $extra_special_chars )
        $chars .= '-_ []{}<>~`+=,.;:/?|';

    $password = '';
    for ( $i = 0; $i < $length; $i++ ) {
        $password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
    }
    return $password;
}