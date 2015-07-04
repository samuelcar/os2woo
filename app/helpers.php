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