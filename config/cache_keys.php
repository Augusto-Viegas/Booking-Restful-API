<?php

/**
 * Cache keys configuration file.
 * ttl = time to live in seconds
 */
return [
    #BOOKING
    'booking_index' => [
        'prefix' => 'bookings:page',
        'ttl' => 120, 
    ],
    'booking_show' => [
        'prefix' => 'booking:id:',
        'ttl' => 300,
    ],

    #CUSTOMER
    'customer_index' => [
        'prefix' => 'customers:page',
        'ttl' => 120,
    ],
    'customer_show' => [
        'prefix' => 'customer:id:',
        'ttl' => 300,
    ],

    #SERVICES
    'service_index' => [
        'prefix' => 'services:page',
        'ttl' => 120,
    ],
    'service_show' => [
        'prefix' => 'service:id:',
        'ttl' => 300,
    ],

    #SERVICE SLOTS
    'service_slot_index' => [
        'prefix' => 'service_slots:page',
        'ttl' => 120,
    ],
    'service_slot_show' => [
        'prefix' => 'service_slot:id:',
        'ttl' => 300,
    ],
];
