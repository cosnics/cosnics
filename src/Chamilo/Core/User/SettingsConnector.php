<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Picture\UserPictureProviderFactory;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * $Id: settings_user_connector.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * 
 * @package user.settings
 */

/**
 * Simple connector class to facilitate rendering settings forms by preprocessing data from the datamanagers to a simple
 * array format.
 * 
 * @author Hans De Bisschop
 */
class SettingsConnector
{

    public function get_fullname_formats()
    {
        return User::get_fullname_format_options();
    }

    function get_date_terms_and_conditions_update()
    {
        $date_format = '%e-%m-%Y';
        return array(\Strftime($date_format, Manager::get_date_terms_and_conditions_last_modified()));
    }

    /**
     * Returns the available user picture providers
     * 
     * @return array
     */
    public function getUserPictureProviders()
    {
        $userPictureProviderFactory = new UserPictureProviderFactory();
        return $userPictureProviderFactory->getAvailablePictureProviders();
    }
}
