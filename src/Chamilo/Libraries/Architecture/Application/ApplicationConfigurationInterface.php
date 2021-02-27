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
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function setUser(\Chamilo\Core\User\Storage\DataClass\User $user);

    /**
     *
     * @param string $key
     * @param string $defaultValue
     * @return string
     */
    public function get($key, $defaultValue = null);

    /**
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value);

    /**
     * @return bool
     */
    public function isEmbeddedApplication();
}
