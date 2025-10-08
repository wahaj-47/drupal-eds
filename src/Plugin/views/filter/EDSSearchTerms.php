<?php

namespace Drupal\eds\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\eds\Plugin\views\EDSHandlerTrait;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter by searching Scholarly Repository full-text index.
 * 
 * @ViewsFilter("eds_search_terms")
 */
class EDSSearchTerms extends FilterPluginBase
{
    use EDSHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function showOperatorForm(&$form, FormStateInterface $form_state)
    {
        parent::showOperatorForm($form, $form_state);

        if (!empty($form['operator'])) {
            $form['operator']['#description'] = $this->t('Search mode');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function operatorOptions($which = 'title')
    {
        $options = [];
        foreach ($this->operators() as $id => $info) {
            $options[$id] = $info[$which];
        }

        return $options;
    }

    /**
     * Returns information about the available operators for this filter.
     *
     * @return array[]
     *   An associative array mapping operator identifiers to their information.
     *   The operator information itself is an associative array with the
     *   following keys:
     *   - title: The translated title for the operator.
     *   - short: The translated short title for the operator.
     *   - values: The number of values the operator requires as input.
     */
    public function operators()
    {
        return [
            'any' => [
                'title' => $this->t('Find any of my search terms'),
                'short' => $this->t('and'),
                'values' => 1,
            ],
            'all' => [
                'title' => $this->t('Find all my search terms'),
                'short' => $this->t('or'),
                'values' => 1,
            ],
            'bool' => [
                'title' => $this->t('Boolean/Phrase'),
                'short' => $this->t('or'),
                'values' => 1,
            ],
            'smart' => [
                'title' => $this->t('SmartText Searching'),
                'short' => $this->t('or'),
                'values' => 1,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['operator']['default'] = 'all';

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function valueForm(&$form, FormStateInterface $form_state)
    {
        parent::valueForm($form, $form_state);

        $form['value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Value'),
            '#default_value' => $this->value,
        ];
    }

    public function query()
    {
        $query = $this->getQuery();

        if (!$this->value) return;

        // Pass any data you need to your query plugin.
        $query->search_terms = reset($this->value);
        $query->search_mode = $this->operator;
    }
}
