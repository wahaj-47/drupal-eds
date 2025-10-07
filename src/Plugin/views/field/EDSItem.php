<?php

namespace Drupal\eds\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * @ViewsField("eds_item")
 * 
 * Display a single item from EDS Item's array
 */
class EDSItem extends FieldPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();
        $options['item_name'] = ['default' => 'Title'];
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
                'Title' => $this->t('Title'),
                'TitleAlt' => $this->t('Alternate Title'),
                'TitleSource' => $this->t('Title Source'),
                'Author' => $this->t('Author'),
                'TypePub' => $this->t('Publication Type'),
                'Subject' => $this->t('Subject Terms'),
                'Abstract' => $this->t('Abstract'),
                'URL' => $this->t('URL'),
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

        if (!empty($values->items) && is_array($values->items)) {
            foreach ($values->items as $item) {
                if (($item['Name'] ?? '') === $item_name) {
                    $data = $item['Data'] ?? '';
                    break;
                }
            }
        }

        $data = html_entity_decode($data);
        $data = preg_replace('#<searchLink.*?>(.*?)</searchLink>#', '$1', $data);

        return $data;
    }
}
