<?php
/**
 * Vercel PHP entry point
 */

// Load environment variables
require_once __DIR__ . '/../config/bootstrap.php';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the main application
require_once __DIR__ . '/../web/index.php';
