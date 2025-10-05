<?php

if (!function_exists('uuid7Regex')) {
    function uuid7Regex(): string
    {
        return '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
    }
}
