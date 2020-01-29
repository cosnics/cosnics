<?php

namespace Chamilo\Application\Weblcms\UserExporter;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class to export users
 *
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserExporter
{
    const PROPERTY_SORT_NAME = 'SortName';

    /**
     * The user export renderer
     *
     * @var UserExportRenderer
     */
    private $user_export_renderer;

    /**
     * The list of user export extenders
     *
     * @var UserExportExtender[]
     */
    private $user_export_extenders;

    /**
     * Constructor
     *
     * @param UserExportRenderer $user_export_renderer
     * @param UserExportExtender[] $user_export_extenders
     */
    public function __construct(UserExportRenderer $user_export_renderer = null, array $user_export_extenders = array())
    {
        $this->set_user_export_renderer($user_export_renderer);
        $this->set_user_export_extenders($user_export_extenders);
    }

    /**
     * Sets the user export extenders
     *
     * @param $user_export_extenders
     *
     * @throws \InvalidArgumentException
     */
    public function set_user_export_extenders($user_export_extenders)
    {
        foreach ($user_export_extenders as $user_export_extender)
        {
            if (!$user_export_extender instanceof UserExportExtender)
            {
                throw new \InvalidArgumentException(
                    'The given user export extenders must be an instance of UserExportExtender'
                );
            }
        }
        $this->user_export_extenders = $user_export_extenders;
    }

    /**
     * Sets the user export renderer
     *
     * @param \application\weblcms\UserExportRenderer $user_export_renderer
     *
     * @throws \InvalidArgumentException
     */
    public function set_user_export_renderer(UserExportRenderer $user_export_renderer)
    {
        if (!$user_export_renderer instanceof UserExportRenderer)
        {
            throw new \InvalidArgumentException(
                'The given user export renderer must be an instance of UserExportRenderer'
            );
        }

        $this->user_export_renderer = $user_export_renderer;
    }

    /**
     * Exports the given users
     *
     * @param User[] $users
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function export(array $users)
    {
        $user_export_headers = array();

        $user_export_headers[User::PROPERTY_OFFICIAL_CODE] = Translation::get(
            'OfficialCode',
            null,
            \Chamilo\Core\User\Manager::context()
        );

        $user_export_headers[User::PROPERTY_USERNAME] = Translation::get(
            'Username',
            null,
            \Chamilo\Core\User\Manager::context()
        );

        $user_export_headers[User::PROPERTY_LASTNAME] = Translation::get(
            'Lastname',
            null,
            \Chamilo\Core\User\Manager::context()
        );

        $user_export_headers[User::PROPERTY_FIRSTNAME] = Translation::get(
            'Firstname',
            null,
            \Chamilo\Core\User\Manager::context()
        );

        $user_export_headers[self::PROPERTY_SORT_NAME] = Translation::get(
            'SortName',
            null,
            Manager::context()
        );

        $user_export_headers[User::PROPERTY_EMAIL] = Translation::get(
            'Email',
            null,
            \Chamilo\Core\User\Manager::context()
        );

        $this->add_additional_headers($user_export_headers);

        $exported_users = array();

        foreach ($users as $user)
        {
            if (!$user instanceof User)
            {
                throw new \InvalidArgumentException('The given user must be an instance of User');
            }

            $user_export_data = array();

            $user_export_data[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
            $user_export_data[User::PROPERTY_USERNAME] = $user->get_username();
            $user_export_data[User::PROPERTY_LASTNAME] = $user->get_lastname();
            $user_export_data[User::PROPERTY_FIRSTNAME] = $user->get_firstname();

            $safeLastName = StringUtilities::getInstance()->createString($user->get_lastname())->toAscii();
            $safeFirstName = StringUtilities::getInstance()->createString($user->get_firstname())->toAscii();

            $user_export_data[self::PROPERTY_SORT_NAME] =
                strtoupper(str_replace(' ', '', $safeLastName . ',' . $safeFirstName));

            $user_export_data[User::PROPERTY_EMAIL] = $user->get_email();

            $this->add_additional_user_data($user_export_data, $user);

            $exported_users[] = $user_export_data;
        }

        return $this->render_exported_users($user_export_headers, $exported_users);
    }

    /**
     * Renders the exported users
     *
     * @param array $user_export_headers
     * @param array $exported_users
     *
     * @return mixed
     */
    protected function render_exported_users(array $user_export_headers, array $exported_users)
    {
        if ($this->user_export_renderer)
        {
            return $this->user_export_renderer->render($user_export_headers, $exported_users);
        }
    }

    /**
     * Adds additional headers
     */
    protected function add_additional_headers(array &$headers_export_data)
    {
        foreach ($this->user_export_extenders as $user_export_extender)
        {
            $headers_export_data = array_merge($headers_export_data, $user_export_extender->export_headers());
        }
    }

    /**
     * Adds additional user data
     *
     * @param array $user_export_data
     * @param User $user
     */
    protected function add_additional_user_data(array &$user_export_data, User $user)
    {
        foreach ($this->user_export_extenders as $user_export_extender)
        {
            $user_export_data = array_merge($user_export_data, $user_export_extender->export_user($user));
        }
    }
}
