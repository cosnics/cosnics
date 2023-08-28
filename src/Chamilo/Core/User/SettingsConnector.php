<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Picture\UserPictureProviderFactory;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use function Strftime;

/**
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
    use DependencyInjectionContainerTrait;

    /**
     * Returns the available user picture providers
     *
     * @return array
     */
    public function getUserPictureProviders()
    {
        return $this->getService(UserPictureProviderFactory::class)->getAvailablePictureProviderTypes();
    }

    public function get_date_terms_and_conditions_update()
    {
        $date_format = '%e-%m-%Y';

        return [Strftime($date_format, Manager::get_date_terms_and_conditions_last_modified())];
    }

    public function get_fullname_formats()
    {
        return User::get_fullname_format_options();
    }
}
