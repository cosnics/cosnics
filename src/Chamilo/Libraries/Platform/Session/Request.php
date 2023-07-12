<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package    Chamilo\Libraries\Platform\Session
 * @deprecated Use \Chamilo\Libraries\Platform\ChamiloRequest
 */
class Request
{

    public static ?ChamiloRequest $chamiloRequest = null;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @deprecated Use \Chamilo\Libraries\Platform\ChamiloRequest->query->get()
     */
    public static function get(string $variable, mixed $default = null): mixed
    {
        return self::getRequest()->query->get($variable, $default);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public static function getRequest(): ChamiloRequest
    {
        if (self::$chamiloRequest === null)
        {
            self::$chamiloRequest =
                DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ChamiloRequest::class);
        }

        return self::$chamiloRequest;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @deprecated Use \Chamilo\Libraries\Platform\ChamiloRequest->request->get()
     */
    public static function post(string $variable): mixed
    {
        return self::getRequest()->request->get($variable);
    }
}
