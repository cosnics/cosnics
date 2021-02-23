<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chamilo\Libraries\Platform\Security\Csrf;

use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;

/**
 * Token storage that uses chamilo session utilities
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class SessionUtilitiesTokenStorage implements ClearableTokenStorageInterface
{
    /**
     * The namespace used to store values in the session.
     */
    const SESSION_NAMESPACE = '_csrf';

    /**
     * @var string
     */
    protected $namespace = self::SESSION_NAMESPACE;

    /**
     * @var SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * Initializes the storage with a Session object and a session namespace.
     *
     * @param SessionUtilities $sessionUtilities
     */
    public function __construct(SessionUtilities $sessionUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId)
    {
        $this->startSessionIfNotStarted();

        if (!$this->hasToken($tokenId))
        {
            throw new TokenNotFoundException('The CSRF token with ID ' . $tokenId . ' does not exist.');
        }

        $csrfData = $this->getCsrfData();
        return (string) $csrfData[$tokenId];
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($tokenId, $token)
    {
        $this->startSessionIfNotStarted();

        $csrfData = $this->getCsrfData();
        $csrfData[$tokenId] = (string) $token;

        $this->sessionUtilities->register($this->namespace, $csrfData);
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken($tokenId)
    {
        $this->startSessionIfNotStarted();

        if (!$this->sessionUtilities->has($this->namespace))
        {
            return false;
        }

        $csrfData = $this->getCsrfData();
        return array_key_exists($tokenId, $csrfData);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken($tokenId)
    {
        $this->startSessionIfNotStarted();

        $csrfData = $this->getCsrfData();
        unset($csrfData[$tokenId]);

        $this->sessionUtilities->register($this->namespace, $csrfData);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->sessionUtilities->unregister($this->namespace);
    }

    /**
     * @return array|mixed
     */
    protected function getCsrfData()
    {
        $csrfData = $this->sessionUtilities->get($this->namespace);
        if(!is_array($csrfData))
        {
            $csrfData = [];
        }

        return $csrfData;
    }

    /**
     * Starts the session
     */
    protected function startSessionIfNotStarted()
    {
        if (!$this->sessionUtilities->isStarted())
        {
            $this->sessionUtilities->start();
        }
    }
}
