<?php

namespace Drupal\eds\Plugin\views\row;

use Drupal\views\Plugin\views\row\RowPluginBase;

/**
 * Renders API data in a custom Twig template.
 *
 * @ViewsRow(
 *   id = "eds",
 *   title = @Translation("EDS Node"),
 *   help = @Translation("Render EDS record using a Twig template.")
 * )
 */
class EDSRow extends RowPluginBase
{
    private function snake(string $in)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $in));
    }

    private function process(array $data, string $key, string $value): array
    {
        $result = [];

        foreach ($data as $item) {
            $v = $item[$value];
            $v = strip_tags(html_entity_decode($v));
            $v = preg_replace('#<searchLink.*?>(.*?)</searchLink>#', '$1', $v);
            $k = $this->snake($item[$key]);
            $result[$k][] = $v;
        }

        return $result;
    }

    private function snake_r(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $snake_key = $this->snake($key);
            $result[$snake_key] = $value;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function render($row)
    {
        $output = [
            '#theme' => 'eds',
            '#id' => $row->id,
            '#header' => $this->snake_r($row->header),
            '#p_link' => $row->p_link,
            '#image_info' => $this->process($row->image_info, 'Size', 'Target'),    // [[Size] => [Target]...]
            '#items' => $this->process($row->items, 'Name', 'Data'),                // [[Name] => [Data]...]
            '#custom_links' => $this->process($row->custom_links, 'Text', 'Url'),   // [[Text] => [Data]...]
            '#search_term' => $row->search_term,
            '#view' => $this->view,
        ];
        return $output;
    }
}
