<?php

namespace Drupal\eds\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * @ViewsField("eds_header")
 * 
 * Display a single header value from EDS Header's array
 */
class EDSHeader extends FieldPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();
        $options['item_name'] = ['default' => 'DbLabel'];
        // Add this to prevent the warning
        $options['element_class_enable'] = ['default' => FALSE];
        $options['element_class'] = ['default' => ''];

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function adminSummary()
    {
        return $this->options['item_name'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {
        $form['item_name'] = [
            '#type' => 'select',
            '#title' => $this->t('Item to display'),
            '#options' => [
                'DbId' => $this->t('Database ID'),
                'DbLabel' => $this->t('Database Label'),
                'An' => $this->t('An'),
                'RelevancyScore' => $this->t('Relevancy Score'),
                'AccessLevel' => $this->t('Access Level'),
                'PubType' => $this->t('Publication Type'),
                'PubTypeId' => $this->t('Publication Type ID'),
            ],
            '#default_value' => $this->options['item_name'],
            '#description' => $this->t('Pick which item from the EDS record to display.'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function render(ResultRow $values)
    {
        $item_name = $this->options['item_name'];
        $data = '';

        if (!empty($values->header) && is_array($values->header)) {
            foreach ($values->header as $item => $value) {
                if ($item === $item_name) {
                    $data = $value ?? '';
                    break;
                }
            }
        }

        $data = html_entity_decode($data);
        $data = preg_replace('#<searchLink.*?>(.*?)</searchLink>#', '$1', $data);

        return $data;
    }
}
