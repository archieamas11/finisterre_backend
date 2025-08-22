<?php
/**
 * Format array data for consistent casing and custom rules.
 *
 * @param array $data Input associative array
 * @param array $skipKeys Keys to skip formatting (default: [])
 * @param array $lowercaseKeys Keys to force lowercase (default: [])
 * @param callable|null $customFormat Optional custom formatter for values
 * @return array Formatted array
 */
function formatData($data, $skipKeys = [], $lowercaseKeys = [], $customFormat = null) {
    if (!is_array($data)) return $data;
    foreach ($data as $key => $value) {
        if (in_array($key, $skipKeys)) {
            continue;
        } elseif (in_array($key, $lowercaseKeys)) {
            // ⚠️ Only apply string functions to string values
            $data[$key] = is_string($value) ? strtolower($value) : $value;
        } elseif ($customFormat && is_callable($customFormat)) {
            $data[$key] = $customFormat($key, $value);
        } else {
            // ⚠️ Only apply string functions to string values
            $data[$key] = is_string($value) ? ucwords(strtolower($value)) : $value;
        }
    }
    return $data;
}