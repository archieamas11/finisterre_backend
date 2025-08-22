<?php
// Test file to check if our log_helper.php has syntax errors
include_once 'logs/log_helper.php';
echo "Log helper loaded successfully!\n";

// Test the function signature
if (function_exists('create_log')) {
    echo "create_log function exists!\n";
} else {
    echo "create_log function not found!\n";
}
?>
