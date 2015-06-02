<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Architecture\Kernel;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AuthenticationValidator
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Kernel
     */
    private $kernel;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->getKernel()->getConfiguration();
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Kernel $kernel
     */
    public function setKernel(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function validate()
    {
        if (! $this->isAuthenticated())
        {
            return $this->tryExternalAuthentication();
        }
        else
        {
            // TODO: Re-invent this in a durable way ...
            // $preventDoubleLogin = (boolean) $this->getKernel()->getConfiguration()->get_setting(
            // \Chamilo\Core\User\Manager :: context(),
            // 'prevent_double_login');

            // if ($preventDoubleLogin)
            // {
            // \Chamilo\Core\User\Storage\DataClass\UserLoginSession :: check_single_login(false);
            // }

            return true;
        }
    }

    /**
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        $user_id = Session :: get_user_id();
        return ! empty($user_id);
    }

    public function tryExternalAuthentication()
    {
        $externalAuthenticationEnabled = $this->getKernel()->getConfiguration()->get_setting(
            array('Chamilo\Core\Admin', 'enableExternalAuthentication'));
        $bypassExternalAuthentication = (boolean) Request :: get('noExtAuth', false);

        if ($externalAuthenticationEnabled && ! $bypassExternalAuthentication)
        {
            return $this->performExternalAuthentication();
        }
        else
        {
            return false;
        }
    }

    public function performExternalAuthentication()
    {
        $externalAuthenticationTypes = Authentication :: getExternalTypes();
        // $preventDoubleLogin = (boolean) $this->getKernel()->getConfiguration()->get_setting(
        // \Chamilo\Core\User\Manager :: context(),
        // 'prevent_double_login');

        foreach ($externalAuthenticationTypes as $externalAuthenticationType)
        {
            $typeAuthenticationEnabled = $this->getKernel()->getConfiguration()->get_setting(
                array('Chamilo\Core\Admin',
                'enable' . $externalAuthenticationType . 'Authentication'));
            $bypassTypeAuthentication = Request :: get('no' . $externalAuthenticationType . 'Auth');

            if ($typeAuthenticationEnabled && ! $bypassTypeAuthentication)
            {
                $authentication = Authentication :: factory($externalAuthenticationType);

                if ($authentication->check_login())
                {
                    // TODO: Re-invent this in a durable way ...
                    // if ($preventDoubleLogin)
                    // {
                    // \Chamilo\Core\User\Storage\DataClass\UserLoginSession :: check_single_login();
                    // }

                    return true;
                }
            }
        }

        return false;
    }
}