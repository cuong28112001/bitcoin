<?php

namespace VendorDuplicator\Google\AuthHandler;

use VendorDuplicator\Google\Auth\CredentialsLoader;
use VendorDuplicator\Google\Auth\FetchAuthTokenCache;
use VendorDuplicator\Google\Auth\HttpHandler\HttpHandlerFactory;
use VendorDuplicator\Google\Auth\Subscriber\AuthTokenSubscriber;
use VendorDuplicator\Google\Auth\Subscriber\ScopedAccessTokenSubscriber;
use VendorDuplicator\Google\Auth\Subscriber\SimpleSubscriber;
use VendorDuplicator\GuzzleHttp\Client;
use VendorDuplicator\GuzzleHttp\ClientInterface;
use VendorDuplicator\Psr\Cache\CacheItemPoolInterface;
/**
 * This supports Guzzle 5
 */
class Guzzle5AuthHandler
{
    protected $cache;
    protected $cacheConfig;
    public function __construct($cache = null, array $cacheConfig = [])
    {
        $this->cache = $cache;
        $this->cacheConfig = $cacheConfig;
    }
    public function attachCredentials(ClientInterface $http, CredentialsLoader $credentials, $tokenCallback = null)
    {
        // use the provided cache
        if ($this->cache) {
            $credentials = new FetchAuthTokenCache($credentials, $this->cacheConfig, $this->cache);
        }
        return $this->attachCredentialsCache($http, $credentials, $tokenCallback);
    }
    public function attachCredentialsCache(ClientInterface $http, FetchAuthTokenCache $credentials, $tokenCallback = null)
    {
        // if we end up needing to make an HTTP request to retrieve credentials, we
        // can use our existing one, but we need to throw exceptions so the error
        // bubbles up.
        $authHttp = $this->createAuthHttp($http);
        $authHttpHandler = HttpHandlerFactory::build($authHttp);
        $subscriber = new AuthTokenSubscriber($credentials, $authHttpHandler, $tokenCallback);
        $http->setDefaultOption('auth', 'google_auth');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    public function attachToken(ClientInterface $http, array $token, array $scopes)
    {
        $tokenFunc = function ($scopes) use ($token) {
            return $token['access_token'];
        };
        $subscriber = new ScopedAccessTokenSubscriber($tokenFunc, $scopes, $this->cacheConfig, $this->cache);
        $http->setDefaultOption('auth', 'scoped');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    public function attachKey(ClientInterface $http, $key)
    {
        $subscriber = new SimpleSubscriber(['key' => $key]);
        $http->setDefaultOption('auth', 'simple');
        $http->getEmitter()->attach($subscriber);
        return $http;
    }
    private function createAuthHttp(ClientInterface $http)
    {
        return new Client(['base_url' => $http->getBaseUrl(), 'defaults' => ['exceptions' => \true, 'verify' => $http->getDefaultOption('verify'), 'proxy' => $http->getDefaultOption('proxy')]]);
    }
}
