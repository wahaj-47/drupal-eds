<?php

/**
 * Implements hook_views_data().
 */
function eds_views_data()
{
    $data = [];

    // Base data.
    $data['eds_node']['table']['group'] = t('EDS');
    $data['eds_node']['table']['base'] = [
        'title' => t('EDS Node'),
        'help' => t('EDS node data provided by the EDS API.'),
        'query_id' => 'eds_query',
    ];

    $data['eds_node']['libguides_search'] = [
        'group' => t('Search'),
        'title' => t('LibGuides Search'),
        'help' => t('Filter by searching guide name and description. When sort_by=relevance is specified, the full-text index is searched.'),
        'filter' => [
            'id' => 'libguides_search_terms',
        ],
    ];

    $data['eds_node']['libguides_sort_by'] = [
        'title' => t('LibGuides Sort By'),
        'help' => t('Sorting of returned guides.'),
        'sort' => [
            'id' => 'libguides_sort_by',
        ],
    ];

    // Fields
    $data['eds_node']['id'] = [
        'title' => t('ID'),
        'help' => t('Guide ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['type_id'] = [
        'title' => t('Type ID'),
        'help' => t('Guide type ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['site_id'] = [
        'title' => t('Site ID'),
        'help' => t('Guide site ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    // Owner
    $data['eds_node']['owner_id'] = [
        'title' => t('Owner ID'),
        'help' => t('Owner ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['owner_first_name'] = [
        'title' => t('Owner First Name'),
        'help' => t('Owner First Name'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['owner_last_name'] = [
        'title' => t('Owner Last Name'),
        'help' => t('Owner Last Name'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['owner_email'] = [
        'title' => t('Owner Email'),
        'help' => t('Owner Email'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    $data['eds_node']['name'] = [
        'title' => t('Name'),
        'help' => t('Guide name.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['description'] = [
        'title' => t('Description'),
        'help' => t('Guide description.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['status'] = [
        'title' => t('Status'),
        'help' => t('Guide status'),
        'field' => [
            'id' => 'standard',
            'value' => 1
        ],
        'filter' => [
            'id' => 'libguides_status',
        ]
    ];
    $data['eds_node']['published'] = [
        'title' => t('Published'),
        'help' => t('Guide published date.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['created'] = [
        'title' => t('Created'),
        'help' => t('Guide created date.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['updated'] = [
        'title' => t('Updated'),
        'help' => t('Guide updated date.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['count_hit'] = [
        'title' => t('Hits'),
        'help' => t('Guide hit count'),
        'field' => [
            'id' => 'numeric',
        ],
    ];
    $data['eds_node']['group_id'] = [
        'title' => t('Group ID'),
        'help' => t('Guide group ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['url'] = [
        'title' => t('URL'),
        'help' => t('Guide URL'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['friendly_url'] = [
        'title' => t('Friendly URL'),
        'help' => t('Guide friendly URL'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    // Metadata
    $data['eds_node']['tags'] = [
        'title' => t('Tags'),
        'help' => t('Tags'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['eds_node']['subjects'] = [
        'title' => t('Subjects'),
        'help' => t('Subjects'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    return $data;
}
