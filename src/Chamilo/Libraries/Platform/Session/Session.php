<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 *
 * @deprecated Use SessionUtilities now
 */
class Session
{

    protected static ?SessionUtilities $sessionUtilities = null;

    public static function clear()
    {
        self::getSessionUtilities()->clear();
    }

    public static function destroy()
    {
        self::getSessionUtilities()->destroy();
    }

    public static function get(string $variable, $default = null)
    {
        return self::getSessionUtilities()->get($variable, $default);
    }

    protected static function getSessionUtilities(): SessionUtilities
    {
        if (self::$sessionUtilities === null)
        {
            self::$sessionUtilities =
                DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionUtilities::class);
        }

        return self::$sessionUtilities;
    }

    public static function getUserId(): ?int
    {
        return self::getSessionUtilities()->getUserId();
    }

    public static function get_user_id(): ?int
    {
        return self::getSessionUtilities()->getUserId();
    }

    public static function register(string $variable, $value)
    {
        self::getSessionUtilities()->register($variable, $value);
    }

    public static function registerIfNotSet(string $variable, $value)
    {
        self::getSessionUtilities()->registerIfNotSet($variable, $value);
    }

    public static function retrieve(string $variable)
    {
        return self::getSessionUtilities()->retrieve($variable);
    }

    /**
     * @throws \Exception
     */
    public static function start()
    {
        self::getSessionUtilities()->start();
    }

    public static function unregister(string $variable)
    {
        self::getSessionUtilities()->unregister($variable);
    }
}
