<?php

namespace Drupal\eds\Plugin\api_proxy;

use Drupal\api_proxy\Plugin\api_proxy\HttpApiCommonConfigs;
use Drupal\api_proxy\Plugin\HttpApiPluginBase;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Form\SubformStateInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * The LibGuides API.
 *
 * @HttpApi(
 *   id = "eds_api_proxy",
 *   label = @Translation("EDS API"),
 *   description = @Translation("Proxies requests to the EBSCO EDS API."),
 *   serviceUrl = "https://eds-api.ebscohost.com",
 * )
 */
final class EDSApiProxy extends HttpApiPluginBase
{

    use HttpApiCommonConfigs;

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Translates between Symfony and PRS objects.
     *
     * @var \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface
     */
    private $foundationFactory;

    /**
     * Cache
     * 
     * @var \Drupal\Core\Cache\CacheBackendInterface
     */
    private $cache;

    /**
     * Session
     * 
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $client, HttpFoundationFactoryInterface $foundation_factory, CacheBackendInterface $cache, SessionInterface $session)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $client, $foundation_factory);
        $this->client = $client;
        $this->foundationFactory = $foundation_factory;
        $this->cache = $cache;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self
    {
        $settings = $container->get('config.factory')
            ->get('api_proxy.settings')
            ->get('api_proxies');
        $plugin_settings = empty($settings[$plugin_id]) ? [] : $settings[$plugin_id];
        $configuration = array_merge($plugin_settings, $configuration);

        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('http_client'),
            $container->get('psr7.http_foundation_factory'),
            $container->get('cache.api_proxy'),
            $container->get('session')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addMoreConfigurationFormElements(array $form, SubformStateInterface $form_state): array
    {
        $form['uid'] = [
            '#type' => 'textfield',
            '#title' => $this->t('User ID'),
            '#default_value' => $this->configuration['uid'] ?? "",
        ];
        $form['password'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Password'),
            '#default_value' => $this->configuration['password'] ?? "",
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function calculateHeaders(array $headers): array
    {
        $default_headers = parent::calculateHeaders($headers);
        $auth_token = $this->getAuthenticationToken();
        $session_token = $this->getSessionToken($auth_token);

        return array_merge(
            $default_headers,
            [
                'accept' => ['application/json'],
                'x-authenticationToken' => [$auth_token],
                'x-sessionToken' => [$session_token],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postprocessOutgoing(Response $response): Response
    {
        // Modify the response from the API.
        // A common problem is to remove the Transfer-Encoding header.
        $response->headers->remove('transfer-encoding');
        return $response;
    }

    /**
     * Fetch authentication token
     */
    private function getAuthenticationToken(): string
    {
        $cid = 'eds.auth_token';
        if ($cache = $this->cache->get($cid)) {
            return $cache->data;
        }

        $endpoint = rtrim($this->getBaseUrl(), '/') . '/authservice/rest/uidauth';
        $user_id = $this->configuration['uid'];
        $password = $this->configuration['password'];

        $psr7_response = $this->client->request(
            'post',
            $endpoint,
            [
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ],
                'body' => '{"UserId": "' . $user_id . '", "Password": "' . $password . '"}'
            ]
        );

        $response = $this->foundationFactory->createResponse($psr7_response);
        $data = json_decode($response->getContent(), true);

        \Drupal::logger('eds_api_proxy')->debug('Response: @resp', [
            '@resp' => print_r($data, true),
        ]);

        if (!isset($data['AuthToken'])) {
            \Drupal::logger('eds_api_proxy')->error('Authentication token missing in response: @resp', [
                '@resp' => print_r($data, true),
            ]);
            throw new \RuntimeException('Failed to retrieve authentication token');
        }

        $auth_token = $data['AuthToken'];
        $expires_in = $data['AuthTimeout'] ?? 3600;

        $this->cache->set($cid, $auth_token, time() + $expires_in);

        return $auth_token;
    }

    /**
     * Fetch session token
     */
    private function getSessionToken($auth_token): string
    {
        $session_id = $this->session->getId();
        $cid = 'eds.session_token.' . $session_id;
        if ($cache = $this->cache->get($cid)) {
            return $cache->data;
        }

        $endpoint = rtrim($this->getBaseUrl(), '/') . '/edsapi/rest/createsession?profile=edsapi&guest=n';

        $psr7_response = $this->client->request(
            'get',
            $endpoint,
            [
                'headers' => [
                    'accept' => 'application/json',
                    'x-authenticationToken' => $auth_token
                ],
            ]
        );

        $response = $this->foundationFactory->createResponse($psr7_response);
        $data = json_decode($response->getContent(), true);

        \Drupal::logger('eds_api_proxy')->debug('Response: @resp', [
            '@resp' => print_r($data, true),
        ]);

        if (!isset($data['SessionToken'])) {
            \Drupal::logger('eds_api_proxy')->error('Session token missing in response: @resp', [
                '@resp' => print_r($data, true),
            ]);
            throw new \RuntimeException('Failed to retrieve session token');
        }

        $session_token = $data['SessionToken'];

        $this->cache->set($cid, $session_token, tags: ['session:' . $session_id]); // Session ID invalidates the token

        return $auth_token;
    }
}
