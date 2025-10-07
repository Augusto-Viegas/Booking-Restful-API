<?php

/**
 * Cache keys configuration file.
 * ttl = time to live in seconds
 */
return [
    #BOOKING
    'booking_index' => [
        'prefix' => 'bookings:page',
        'ttl' => 60, 
    ],

    #CUSTOMER
    'customer_index' => [
        'prefix' => 'customers:page',
        'ttl' => 60,
    ],
    'customer_show' => [
        'prefix' => 'customer:id:',
        'ttl' => 300,
    ],
];
