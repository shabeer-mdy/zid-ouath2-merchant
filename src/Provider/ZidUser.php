<?php

namespace Zid\OAuth2\Client\Provider;

use Exception;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class ZidUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->getResponseValue('user.id');
    }

    /**
     * Get preferred name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getResponseValue('user.name');
    }

    /**
     * Get email address.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getResponseValue('user.email');
    }

    /**
     * Get mobile number.
     *
     * @return string|null
     */
    public function getMobile()
    {
        return $this->getResponseValue('user.mobile');
    }

    /**
     * Get store id.
     *
     * @return string|null
     */
    public function getBusinessId()
    {
        return $this->getResponseValue('user.store.id');
    }

    /**
     * Get store id.
     *
     * @return string|null
     */
    public function getBusinessReference()
    {
        return $this->getResponseValue('user.store.uuid');
    }

    /**
     * Get store name.
     *
     * @return string|null
     */
    public function getBusinessName() 
    {
        return $this->getResponseValue('user.store.title');
    }

    /**
     * Get store mobile.
     *
     * @return string|null
     */
    public function getBusinessMobile() {
        return $this->getResponseValue('user.store.phone');
    }

    /**
     * Get store email.
     *
     * @return string|null
     */
    public function getBusinessEmail() {
        return $this->getResponseValue('user.store.email');
    }

    /**
     * Get store ulr.
     *
     * @return string|null
     */
    public function getBusinessUrl() {
        return $this->getResponseValue('user.store.url');
    }


    /**
     * Get user data as an array.
     *
     * @return array
     * @throws Exception
     */
    public function toArray()
    {
        try {
            return $this->response['user'];
        }catch (Exception $exception){
            throw new Exception('User data not found');
        }
    }


    private function getResponseValue($key)
    {
        $context = $this->response;
        $pieces = explode('.', $key);
        foreach ($pieces as $piece) {
            if (!is_array($context) || !array_key_exists($piece, $context)) {
                return null;
            }
            $context = &$context[$piece];
        }
        return $context;
    }
}
