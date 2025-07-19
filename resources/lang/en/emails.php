<?php

return [

    /*
    |--------------------------------------------------------------------------
    | E-mails - English Translations
    |--------------------------------------------------------------------------
    |
    | Contains the labels for the welcome and order confirmation emails.
    |
    */

    'welcome' => [
        'subject'               => 'Welcome to :app_name',
        'title'                 => 'Welcome, :firstname !',
        'greeting'              => 'Hello :firstname,',
        'account_created'       => 'Your account has been created successfully. Here are your login credentials:',
        'email'                 => 'Email',
        'temp_password'         => 'Temporary password',
        'security_notice'       => 'For your security, please change this password upon first login.',
        'unauthorized_notice'   => 'If you did not request this account, simply ignore this email.',
        'regards'               => 'Best regards,',
        'team'                  => 'The :app_name Team',
        'rights_reserved'       => 'All rights reserved',
    ],

    'order' => [
        'subject'               => 'Order Confirmation #:reference',
        'confirmation_title'    => 'Your Order Confirmation',
        'thank_you'             => 'Thank you for your order!',
        'reference'             => 'Reference',
        'date'                  => 'Date',
        'products_count'        => 'Number of products',
        'shipping_address'      => 'Shipping Address',
        'subtotal'              => 'Subtotal',
        'vat'                   => 'VAT',
        'total'                 => 'Total (incl. VAT)',
        'contact_support'       => 'If you have any questions regarding your order, please feel free to contact our support team.',
        'rights_reserved'       => 'All rights reserved',
    ],

];
