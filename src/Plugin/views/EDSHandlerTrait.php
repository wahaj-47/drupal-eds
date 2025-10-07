<?php

namespace Drupal\eds\Plugin\views;

use Drupal\eds\Plugin\views\query\EDSQuery;

trait EDSHandlerTrait
{

    /**
     * Retrieves the query plugin.
     *
     * @return \Drupal\libguides\Plugin\views\query\EDSQuery|null
     *   The query plugin, or NULL if there is no query or it is not LibguidesQuery
     *   query.
     */
    public function getQuery()
    {
        $query = $this->query ?? $this->view->query ?? NULL;
        return $query instanceof EDSQuery ? $query : NULL;
    }
}
