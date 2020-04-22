<?php
namespace Chamilo\Libraries\Authentication\Ldap;

/**
 * This class is used to parse the information from the ldap to a user.
 * This class is an example used at hogent and must
 * be adapted for your company to work. Connection information can be added in the administrator settings of chamilo
 */

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Authentication\Ldap
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LdapParser
{
    use DependencyInjectionContainerTrait;

    public function __construct()
    {
        $this->initializeContainer();
    }

    /**
     *
     * @return \Chamilo\Libraries\Hashing\HashingUtilities
     */
    public function getHashingUtilities()
    {
        return $this->getService(HashingUtilities::class);
    }

    /**
     *
     * @param string[] $info
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    public function parse($info = array(), $username)
    {
        $userProperties = array();

        $userProperties[User::PROPERTY_LASTNAME] = $info[0]['sn'][0];
        $userProperties[User::PROPERTY_FIRSTNAME] = $info[0]['givenname'][0];
        $userProperties[User::PROPERTY_USERNAME] = $username;
        $userProperties[User::PROPERTY_PASSWORD] = $this->getHashingUtilities()->hashString('PLACEHOLDER');
        $userProperties[User::PROPERTY_AUTH_SOURCE] = 'ldap';
        $userProperties[User::PROPERTY_EMAIL] = $info[0]['mail'][0];

        $student = false;
        $employee = false;

        for ($j = 0; $j < $info[0]['objectclass']['count']; $j ++)
        {
            if ($info[0]['objectclass'][$j] == 'hgStudent')
            {
                $student = true;
            }
            if ($info[0]['objectclass'][$j] == 'hgEmployee')
            {
                $employee = true;
            }
        }

        if ($student)
        {
            $userProperties[User::PROPERTY_OFFICIAL_CODE] = $info[0]['hgstamnummer'][0];
            $userProperties[User::PROPERTY_STATUS] = User::STATUS_STUDENT;
        }

        if ($employee)
        {
            $userProperties[User::PROPERTY_OFFICIAL_CODE] = $info[0]['hgpersoneelsnummer'][0];
            $userProperties[User::PROPERTY_STATUS] = User::STATUS_TEACHER;
        }

        $userProperties[User::PROPERTY_PLATFORMADMIN] = '0';
        $userProperties[User::PROPERTY_ACTIVE] = '1';
        $userProperties[User::PROPERTY_PHONE] = '';
        $userProperties[User::PROPERTY_PICTURE_URI] = '';
        $userProperties[User::PROPERTY_CREATOR_ID] = '';

        $user = new User($userProperties);

        if (!$user->create())
        {
            throw new AuthenticationException(Translation::get('UserAccountCreationFailed'));
        }

        return $user;
    }
}
