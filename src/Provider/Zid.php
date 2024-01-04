<?php

namespace Zid\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Zid extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $base_url = 'https://oauth.zid.sa';
    protected $base_api_url = 'https://api.zid.sa';

    protected $headers = [
        'Accept' => 'application/json',
    ];

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->base_url .'/oauth/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array  $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->base_url .'/oauth/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken  $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->base_api_url.'/app/v1/managers/account/profile';
    }

    /**
     * Set base url for the authentication server.
     *
     * @param  string  $base_url
     *
     * @return Zid
     */
    public function setBaseUrl(string $base_url): Zid
    {
        $this->base_url = $base_url;
        return $this;
    }

    /**
     * @param  array  $headers
     *
     * @return Zid
     */
    public function setHeaders(array $headers): Zid
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array
     *
     * @link https://docs.zid.sa/docs/authorization-migration/2cgihlrgqoz36-authorization
     *
     * The provided scope will be used if you don't give any scope
     * and this scope will be used to grab user accounts public information
     *
     * @var array List of scopes that will be used for authentication.
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator, defaults to ','
     */
    protected function getScopeSeparator()
    {
        return ',';
    }

    /**
     * Check a provider response for errors.
     *
     * @param  ResponseInterface  $response
     * @param  array|string  $data
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (empty($data['error'])) {
            return;
        }

        $error = $data['error']['message'] ?? $data['error_description'] ?? null;
        throw new IdentityProviderException(
            $error,
            $response->getStatusCode(),
            $data
        );
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param  array  $response
     * @param  AccessToken  $token
     *
     * @return ZidUser
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ZidUser($response);
    }

    /**
     * @param  string  $method
     * @param  string  $url
     * @param  string|AccessToken  $token
     * @param  array  $options
     *
     * @return array|mixed|string
     * @throws IdentityProviderException
     */
    public function fetchResource(string $method, string $url, $token, array $options = [])
    {
        if ($token instanceof AccessToken) {
            $token = $token->getToken();
        }

        $request = $this->getAuthenticatedRequest($method, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Returns the default headers used by this provider.
     *
     * Typically this is used to set 'Accept' or 'Content-Type' headers.
     *
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return $this->headers;
    }
}
