<?php

return [
    'version' => env('BEAUTY_CRM_VERSION', '1.0.0'),
    'company_name' => env('BEAUTY_CRM_COMPANY', 'Beauty Studio'),

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

    'ticket_statuses' => [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],

    'ticket_priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    'ticket_categories' => [
        'complaint' => 'Complaint',
        'question' => 'Question',
        'request' => 'Request',
        'feedback' => 'Feedback',
        'technical' => 'Technical Issue',
    ],

    'roles' => [
        'admin' => 'Admin',
        'sales' => 'Sales',
        'cs' => 'Customer Service',
        'manager' => 'Manager',
    ],
];