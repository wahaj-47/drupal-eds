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

    $data['eds_node']['eds_search'] = [
        'group' => t('Search'),
        'title' => t('EDS Search'),
        'help' => t('Search EDS'),
        'filter' => [
            'id' => 'eds_search_terms',
        ],
    ];

    $data['eds_node']['eds_sort_by'] = [
        'title' => t('EDS Sort By'),
        'help' => t('Sorting of returned records.'),
        'sort' => [
            'id' => 'eds_sort_by',
        ],
    ];

    $data['eds_node']['eds_facets'] = [
        'group' => t('Search'),
        'title' => t('EDS Facets'),
        'help' => t('Filter by facet.'),
        'filter' => [
            'id' => 'eds_facets',
        ],
    ];

    // Fields
    $data['eds_node']['id'] = [
        'title' => t('Result ID'),
        'help' => t('Result ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    $data['eds_node']['header'] = [
        'title' => t('Header'),
        'help' => t('Header'),
        'field' => [
            'id' => 'eds_header',
        ],
    ];

    $data['eds_node']['p_link'] = [
        'title' => t('Publication Link'),
        'help' => t('Publication Link'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    $data['eds_node']['image_info'] = [
        'title' => t('Image Info'),
        'help' => t('Image options available'),
        'field' => [
            'id' => 'eds_image',
        ],
    ];

    $data['eds_node']['items'] = [
        'title' => t('Item'),
        'help' => t("Item data from Item's array"),
        'field' => [
            'id' => 'eds_item',
        ],
    ];

    $data['eds_node']['custom_links'] = [
        'title' => t('Custom Links'),
        'help' => t('Custom Links'),
        'field' => [
            'id' => 'eds_custom_link',
        ],
    ];

    return $data;
}
