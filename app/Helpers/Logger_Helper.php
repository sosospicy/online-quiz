<?php

if ( !function_exists('exception_logger')) {
    function exception_logger($e) {
        log_message('error', $e->getMessage() . "\r\n" . $e->getTraceAsString());
    }
}