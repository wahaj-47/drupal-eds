<?php

namespace Drupal\eds\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * @ViewsField("eds_image")
 * 
 * Render image associated with a publication
 */
class EDSImage extends FieldPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();
        $options['img_size'] = ['default' => 'thumb'];
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
        return $this->options['img_size'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state)
    {
        $form['img_size'] = [
            '#type' => 'select',
            '#title' => $this->t('Image size'),
            '#options' => [
                'thumb' => $this->t('Thumb'),
                'medium' => $this->t('Medium'),
            ],
            '#default_value' => $this->options['img_size'],
            '#description' => $this->t('Pick which image size from the EDS record to display.'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function render(ResultRow $values)
    {
        $img_size = $this->options['img_size'];
        $data = [];

        if (!empty($values->image_info) && is_array($values->image_info)) {
            foreach ($values->image_info as $item) {
                if (($item['Size'] ?? '') === $img_size) {
                    $data = [
                        '#type' => 'markup',
                        '#markup' => '<img src="' . $item['Target'] . '"></img>',
                    ];
                    break;
                }
            }
        }

        return $data;
    }
}
