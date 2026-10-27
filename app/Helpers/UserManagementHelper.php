<?php

declare(strict_types=1);

/**
 * تحويل النصوص إلى صيغة آمنة للعرض في HTML.
 */
function userManagementEscape(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}