<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default VAT Rate
    |--------------------------------------------------------------------------
    |
    | This is the default VAT rate used when no specific rate is configured
    | for a locale. The rate should be expressed as a decimal (e.g., 0.20 for 20%).
    |
    */
    'rate' => env('DEFAULT_VAT_RATE', 0.20),

    /*
    |--------------------------------------------------------------------------
    | Locale-specific VAT Rates
    |--------------------------------------------------------------------------
    |
    | Configure different VAT rates for different locales.
    | If a locale is not specified here, the default rate will be used.
    |
    */
    'rates' => [
        'fr' => env('FR_VAT_RATE', 0.20),
        'en' => env('EN_VAT_RATE', 0.20),
    ],
]; 
