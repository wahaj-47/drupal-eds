<?php

namespace Drupal\eds\Plugin\views\sort;

use Drupal\eds\Plugin\views\EDSHandlerTrait;
use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Provides a sort plugin for EDS views.
 *
 * @ViewsSort("eds_sort_by")
 */
class EDSSortBy extends SortPluginBase
{

    use EDSHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['order']['default'] = 'relevance';

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function sortOptions()
    {
        return [
            'relevance' => $this->t('Sort by relevance'),
            'date' => $this->t('Date Newest'),
            'date2' => $this->t('Date Oldest'),
        ];
    }

    /**
     * Display whether or not the sort order is ascending or descending.
     */
    public function adminSummary()
    {
        if (!empty($this->options['exposed'])) {
            return $this->t('Exposed');
        }
        switch ($this->options['order']) {
            case 'date';
                return $this->t('Date Newest');

            case 'date2';
                return $this->t('Date Oldest');

            case 'relevance':
            default:
                return $this->t('Relevance');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $this->getQuery()->sort_by = $this->options['order'];
    }
}
