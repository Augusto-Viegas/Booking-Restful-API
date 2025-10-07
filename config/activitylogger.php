<?php
/**
 * Configuration for activity logger
 */
return [
    //Key used for fallback when there is no user (optional)
    'fallback_system_key' => env('ACTIVITYLOGGER_FALLBACK_SYSTEM_KEY', null),

    //If wants to ignore logs on CLI
    'log_in_console' => true,
];
