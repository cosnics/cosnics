<?php
namespace Chamilo\Libraries\Architecture\Application;

/**
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationConfiguration implements ApplicationConfigurationInterface
{

    /**
     *
     * @var \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    private $request;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @var mixed[]
     */
    private $configurationParameters;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct(\Chamilo\Libraries\Platform\ChamiloRequest $request, $user = null, $application = null,
        $configurationParameters = array())
    {
        $this->request = $request;
        $this->user = $user;
        $this->application = $application;
        $this->configurationParameters = $configurationParameters;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function setUser(\Chamilo\Core\User\Storage\DataClass\User $user)
    {
        $this->user = $user;
    }

    /**
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        return isset($this->configurationParameters[$key]) ? $this->configurationParameters[$key] : $defaultValue;
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->configurationParameters[$key] = $value;
    }
}
