<?php
function setDebugHeaders() {
    global $HAS_HEADER;

    if (!$HAS_HEADER) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        $HAS_HEADER = true;
    }
}


/*
 * dd() with headers
 */
$HAS_HEADER = false;
if (!function_exists('ddh')) {
    function ddh($var){
        if(\Illuminate\Support\Facades\Config::get('app.debug')) {
            setDebugHeaders();
            dd($var);
        }
    }
}

