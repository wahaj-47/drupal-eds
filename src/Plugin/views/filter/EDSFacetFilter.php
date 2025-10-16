<?php

namespace Drupal\eds\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\eds\Plugin\views\EDSHandlerTrait;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;

/**
 * Filter by Facets. This filter does not work if "Exposed form in block" is set to 'Yes'
 * 
 * @ViewsFilter("eds_facets")
 */
class EDSFacetFilter extends InOperator
{

    use EDSHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL)
    {
        parent::init($view, $display, $options);
        $this->valueOptions = [$this->t('- Dummy Option -')];
    }

    /**
     * {@inheritdoc}
     * 
     * Exposing the filter by default. This filter is useless if not exposed
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();
        $options['exposed'] = ['default' => TRUE];

        return $options;
    }

    public function showExposeButton(&$form, FormStateInterface $form_state)
    {
        parent::showExposeButton($form, $form_state);
        $form['expose_button']['checkbox']['checkbox']['#title'] = $this->t('Expose this filter to visitors. This filter is useless if not exposed because it gets populated on the client');
    }

    /**
     * {@inheritdoc}
     */
    public function operators()
    {
        $operators = [
            'in' => [
                'title' => $this->t('Facets'),
                'short' => $this->t('in'),
                'short_single' => $this->t('='),
                'method' => 'opSimple',
                'values' => 1,
            ],
        ];
        return $operators;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $query = $this->getQuery();
        if (isset($this->value['all'])) unset($this->value['all']);
        $query->facets = $this->value;
    }

    /**
     * {@inheritdoc}
     * 
     */
    public function buildOptionsForm(&$form, \Drupal\Core\Form\FormStateInterface $form_state)
    {
        parent::buildOptionsForm($form, $form_state);

        // Add description for the value field
        $form['value']['#description'] = $this->t('No need to configure here. The options list will be populated via JS on client.');
    }

    /**
     * {@inheritdoc}
     */
    public function buildExposedForm(&$form, FormStateInterface $form_state)
    {
        parent::buildExposedForm($form, $form_state);
        $identifier = $this->options['expose']['identifier'];

        // Disabled form validation. @TODO: Figure out a way to do this with validation enabled
        $form[$identifier]['#validated'] = TRUE;
        $form[$identifier]['#limit_validation_errors'] = [];
    }

    /**
     * {@inheritdoc}
     * 
     * Disable validation.
     */
    public function validate()
    {
        return null;
    }
}
