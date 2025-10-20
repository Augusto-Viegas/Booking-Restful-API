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
    'booking_show' => [
        'prefix' => 'booking:id:',
        'ttl' => 120,
    ],

    #CUSTOMER
    'customer_index' => [
        'prefix' => 'customers:page',
        'ttl' => 60,
    ],
    'customer_show' => [
        'prefix' => 'customer:id:',
        'ttl' => 120,
    ],

    #SERVICES
    'service_index' => [
        'prefix' => 'services:page',
        'ttl' => 60,
    ],
    'service_show' => [
        'prefix' => 'service:id:',
        'ttl' => 120,
    ],

    #SERVICE SLOTS
    'service_slot_index' => [
        'prefix' => 'service_slots:page',
        'ttl' => 60,
    ],
    'service_slot_show' => [
        'prefix' => 'service_slot:id:',
        'ttl' => 120,
    ],
];
