<?php

namespace Drupal\eds\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * @ViewsField("eds_custom_link")
 * 
 * Renders EDS custom link
 */
class EDSCustomLink extends FieldPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function render(ResultRow $values)
    {
        $data = [];

        if (!empty($values->custom_links) && is_array($values->custom_links)) {
            foreach ($values->custom_links as $link) {
                $url = $link['Url'] ?? '';
                $text = $link['Text'] ?? $link['Name'] ?? 'Link';

                if ($url) {
                    $data[] = [
                        '#type' => 'markup',
                        '#markup' => '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($text, ENT_QUOTES) . '</a>',
                    ];
                }
            }
        }

        return [
            '#type' => 'markup',
            '#markup' => implode('<br>', array_map(function ($item) {
                return $item['#markup'];
            }, $data)),
        ];
    }
}
