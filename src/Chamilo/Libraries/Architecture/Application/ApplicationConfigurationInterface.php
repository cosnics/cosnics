<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Core\User\Storage\DataClass\User;

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
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    public function get($key, $defaultValue = null);

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication();

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest();

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser();

    /**
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function setUser(User $user);
}
