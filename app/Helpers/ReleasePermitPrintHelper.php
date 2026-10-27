<?php

/*
|--------------------------------------------------------------------------
| تنسيق وإخراج بيانات إذن الإفراج بأمان
|--------------------------------------------------------------------------
*/

function permitEscape($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function permitFormatDate($value): string
{
    if (empty($value)) {
        return '—';
    }

    $timestamp = strtotime((string) $value);

    if ($timestamp === false) {
        return '—';
    }

    return date('Y-m-d H:i', $timestamp);
}

function permitFormatNumber($value, int $decimals = 2): string
{
    if ($value === null || $value === '') {
        return '—';
    }

    return number_format(
        (float) $value,
        $decimals
    );
}