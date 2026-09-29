<?php

function escape_html($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function request_positive_int($name)
{
    $value = $_GET[$name] ?? null;
    $validated = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    return $validated === false ? null : $validated;
}

function youtube_video_id($url)
{
    $parts = parse_url((string)$url);
    $host = strtolower($parts['host'] ?? '');

    if ($host === 'youtu.be') {
        $id = trim($parts['path'] ?? '', '/');
    } elseif ($host === 'youtube.com' || $host === 'www.youtube.com') {
        parse_str($parts['query'] ?? '', $query);
        $id = $query['v'] ?? '';
    } else {
        return null;
    }

    return preg_match('/^[A-Za-z0-9_-]{6,20}$/', (string)$id) === 1 ? $id : null;
}

function format_score($value)
{
    return number_format((float)$value, 3, '.', '');
}
