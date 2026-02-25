<?php

/**
 * Fallback department/section structure (from nextcloud-like / gba_file_manager).
 * Used when gba_file_manager DB is unavailable or has no user data.
 */
return [
    'departments' => [
        'Corporate Services' => ['Administration', 'Human Resource', 'ICT'],
        'Executive' => ['Internal Audit', 'Planning', 'PRO'],
        'Finance and Investment' => ['Accounts', 'Finance'],
        'Irrigation and Operations' => [
            'Agriculture Production',
            'Infrastructure & Development',
            'Land Administration',
        ],
        'Irrigation Operations' => ['Infrastructure & Development', 'Land Administration'],
        'General Management' => ['Planning', 'PRO'],
    ],
];
