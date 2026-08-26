<?php

return [
    'version' => env('BEAUTY_CRM_VERSION', '1.0.0'),
    'company_name' => env('COMPANY_NAME', 'Beauty Studio'),
    
    // Notification Settings
    'notify_new_lead' => env('NOTIFY_NEW_LEAD', true),
    'notify_won_deal' => env('NOTIFY_WON_DEAL', true),

    'lead_statuses' => [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'converted' => 'Converted',
        'closed' => 'Closed',
    ],

    'lead_qualifications' => [
        'qualified' => 'Potensial',
        'unqualified' => 'Tidak Potensial',
        'not_fit' => 'Tidak Cocok',
        'win' => 'Win',
        'lost' => 'Lost',
    ],

    'deal_statuses' => [
        'open' => 'Open',
        'won' => 'Won',
        'lost' => 'Lost',
    ],

    'activity_types' => [
        'call' => 'Telepon',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'meeting' => 'Meeting',
        'note' => 'Catatan',
        'other' => 'Lainnya',
    ],

    'activity_results' => [
        'connected' => 'Connected',
        'no_answer' => 'No Answer',
        'voicemail' => 'Voicemail',
        'busy' => 'Busy',
        'wrong_number' => 'Wrong Number',
    ],


    'roles' => [
        'admin' => 'Admin',
        'sales' => 'Sales',
        'cs' => 'Customer Service',
        'manager' => 'Manager',
    ],
];