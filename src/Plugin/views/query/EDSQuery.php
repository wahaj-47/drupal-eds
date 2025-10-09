<?php

namespace Drupal\eds\Plugin\views\query;

use Drupal\Core\Url;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;

/**
 * EDS views query plugin which wraps calls to the EDS API in order to
 * expose the results to views.
 *
 * @ViewsQuery(
 *   id = "eds_query",
 *   title = @Translation("EDS Query"),
 *   help = @Translation("Query against the EDS API.")
 * )
 */
class EDSQuery extends QueryPluginBase
{
    use LoggerTrait;

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * The logging channel to use.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * To silence the warning: Creation of dynamic property $where is deprecated
     */
    protected $where;

    /**
     * Search terms
     * 
     * @var string|null
     */
    public $search_terms;

    /**
     * Search mode
     * 
     * @var string|null
     */
    public $search_mode;

    /**
     * Sort by
     * 
     * @var string|null
     */
    public $sort_by;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $client, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $client);
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('http_client'),
            $container->get('logger.channel.eds')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ViewExecutable $view)
    {
        $view->initPager();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ViewExecutable $view)
    {
        try {
            // Pager config
            $items_per_page = $view->getItemsPerPage();
            $current_page = $view->getCurrentPage();

            $url = Url::fromUri('internal:/api-proxy/eds_api_proxy', [
                'query' => [
                    '_api_proxy_uri' => '/edsapi/rest/search' .
                        '?query-1=' . str_replace(",", "\,", $this->search_terms) .
                        '&sort=' . 'relevance' . // @TODO: Replace with sort plugin
                        '&includefacets=' . 'y' .
                        '&searchmode=' . $this->search_mode . // enum: any, bool, all, smart 
                        '&view=' . 'detailed' . // enum: title, brief, detailed
                        '&resultsperpage=' . $items_per_page .
                        '&pagenumber=' . $current_page + 1 .
                        '&includeimagequickview=' . 'y' .
                        '&highlight=' . 'n'
                ],
                'absolute' => TRUE,
            ])->toString(TRUE)->getGeneratedUrl();

            $response = $this->client->request(
                'GET',
                $url,
            );

            $json = json_decode($response->getBody()->getContents(), TRUE);

            $view->pager->total_items = $json['SearchResult']['Statistics']['TotalHits'];

            $data = $json['SearchResult']['Data']['Records'];

            $index = 0;
            foreach ($data as $publication) {
                $row['id'] = $publication['ResultId'] ?? "";
                $row['header'] = $publication['Header'] ?? [];
                $row['p_link'] = $publication['PLink'] ?? "#";
                $row['image_info'] = $publication['ImageInfo'] ?? [];
                $row['items'] = $publication['Items'] ?? [];
                $row['custom_links'] = $publication['CustomLinks'] ?? [];
                $row['search_term'] = $this->search_terms;

                $row['index'] = $index++;

                $view->result[] = new ResultRow($row);
            }

            $view->pager->updatePageInfo();
            $view->pager->postExecute($view->result);
            $view->total_rows = $json['SearchResult']['Statistics']['TotalHits'];
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    /**
     * Views core assume an SQL-query backend. 
     * To mitigate that, we need to implement two methods which will, in a sense, ignore core Views as a way to work around this limitation.
     */
    public function ensureTable($table, $relationship = NULL)
    {
        return '';
    }

    public function addField($table, $field, $alias = '', $params = array())
    {
        return $field;
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
