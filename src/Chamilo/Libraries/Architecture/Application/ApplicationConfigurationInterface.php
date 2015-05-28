<?php
namespace Chamilo\Libraries\Architecture\Application;

/**
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ApplicationConfigurationInterface
{

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest();

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication();

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser();

    /**
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null);

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);
}
