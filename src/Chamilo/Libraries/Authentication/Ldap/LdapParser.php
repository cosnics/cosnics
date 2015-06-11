<?php
namespace Chamilo\Libraries\Authentication\Ldap;

/**
 * $Id: ldap_parser.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.authentication.ldap
 */
/**
 * This class is used to parse the information from the ldap to a user.
 * This class is an example used at hogent and must
 * be adapted for your company to work. Connection information can be added in the administrator settings of chamilo
 */
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Hashing\Hashing;

class LdapParser
{

    public function parse($info = array(), $username)
    {
        $user_properties[User :: PROPERTY_LASTNAME] = $info[0]['sn'][0];
        $user_properties[User :: PROPERTY_FIRSTNAME] = $info[0]['givenname'][0];
        $user_properties[User :: PROPERTY_USERNAME] = $username;
        $user_properties[User :: PROPERTY_PASSWORD] = Hashing :: hash('PLACEHOLDER');
        $user_properties[User :: PROPERTY_AUTH_SOURCE] = 'ldap';
        $user_properties[User :: PROPERTY_EMAIL] = $info[0]['mail'][0];
        ;
        
        for ($j = 0; $j < $info[0]['objectclass']['count']; $j ++)
        {
            if ($info[0]['objectclass'][$j] == 'hgStudent')
            {
                $student = true;
            }
            if ($info[0]['objectclass'][$j] == 'hgEmployee')
            {
                $personeel = true;
            }
        }
        if ($student)
        {
            $result['hgOfficialCode'] = $info[0]['hgstamnummer'][0];
            $status = User :: STATUS_STUDENT;
        }
        if ($personeel)
        {
            $result['hgOfficialCode'] = $info[0]['hgpersoneelsnummer'][0];
            $status = User :: STATUS_TEACHER;
        }
        
        $user_properties[User :: PROPERTY_OFFICIAL_CODE] = $result["hgOfficialCode"];
        $user_properties[User :: PROPERTY_STATUS] = $status;
        
        $user_properties[User :: PROPERTY_PLATFORMADMIN] = '0';
        $user_properties[User :: PROPERTY_ACTIVE] = '1';
        $user_properties[User :: PROPERTY_PHONE] = '';
        $user_properties[User :: PROPERTY_PICTURE_URI] = '';
        $user_properties[User :: PROPERTY_CREATOR_ID] = '';
        
        $user = new User(0);
        $user->set_default_properties($user_properties);
        return $user->create();
    }
}
