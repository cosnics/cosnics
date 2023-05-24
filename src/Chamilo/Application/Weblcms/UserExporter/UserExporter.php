<?php
namespace Chamilo\Application\Weblcms\UserExporter;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use InvalidArgumentException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\UserExporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserExporter
{
    public const PROPERTY_SORT_NAME = 'SortName';

    protected Translator $translator;

    /**
     * The list of user export extenders
     *
     * @var \Chamilo\Application\Weblcms\UserExporter\UserExportExtender[]
     */
    private array $userExportExtenders;

    private UserExportRenderer $userExportRenderer;

    /**
     * @param \Chamilo\Application\Weblcms\UserExporter\UserExportExtender[] $userExportExtenders
     */
    public function __construct(
        Translator $translator, UserExportRenderer $userExportRenderer, array $userExportExtenders = []
    )
    {
        $this->translator = $translator;
        $this->userExportRenderer = $userExportRenderer;
        $this->userExportExtenders = $userExportExtenders;
    }

    protected function add_additional_headers(string $courseIdentifier, array &$headers_export_data)
    {
        foreach ($this->getUserExportExtenders() as $user_export_extender)
        {
            $headers_export_data =
                array_merge($headers_export_data, $user_export_extender->export_headers($courseIdentifier));
        }
    }

    protected function add_additional_user_data(string $courseIdentifier, array &$user_export_data, User $user)
    {
        foreach ($this->getUserExportExtenders() as $user_export_extender)
        {
            $user_export_data =
                array_merge($user_export_data, $user_export_extender->export_user($courseIdentifier, $user));
        }
    }

    /**
     * Exports the given users
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User[] $users
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function export(string $courseIdentifier, array $users)
    {
        $translator = $this->getTranslator();

        $user_export_headers = [];

        $user_export_headers[User::PROPERTY_OFFICIAL_CODE] = $translator->trans(
            'OfficialCode', [], \Chamilo\Core\User\Manager::CONTEXT
        );

        $user_export_headers[User::PROPERTY_USERNAME] = $translator->trans(
            'Username', [], \Chamilo\Core\User\Manager::CONTEXT
        );

        $user_export_headers[User::PROPERTY_LASTNAME] = $translator->trans(
            'Lastname', [], \Chamilo\Core\User\Manager::CONTEXT
        );

        $user_export_headers[User::PROPERTY_FIRSTNAME] = $translator->trans(
            'Firstname', [], \Chamilo\Core\User\Manager::CONTEXT
        );

        $user_export_headers[self::PROPERTY_SORT_NAME] = $translator->trans(
            'SortName', [], Manager::CONTEXT
        );

        $user_export_headers[User::PROPERTY_EMAIL] = $translator->trans(
            'Email', [], \Chamilo\Core\User\Manager::CONTEXT
        );

        $this->add_additional_headers($courseIdentifier, $user_export_headers);

        $exported_users = [];

        foreach ($users as $user)
        {
            if (!$user instanceof User)
            {
                throw new InvalidArgumentException('The given user must be an instance of User');
            }

            $user_export_data = [];

            $user_export_data[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
            $user_export_data[User::PROPERTY_USERNAME] = $user->get_username();
            $user_export_data[User::PROPERTY_LASTNAME] = $user->get_lastname();
            $user_export_data[User::PROPERTY_FIRSTNAME] = $user->get_firstname();

            $user_export_data[self::PROPERTY_SORT_NAME] =
                strtoupper(str_replace(' ', '', $user->get_lastname() . ',' . $user->get_firstname()));

            $user_export_data[User::PROPERTY_EMAIL] = $user->get_email();

            $this->add_additional_user_data($courseIdentifier, $user_export_data, $user);

            $exported_users[] = $user_export_data;
        }

        return $this->render_exported_users($user_export_headers, $exported_users);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserExportExtenders(): array
    {
        return $this->userExportExtenders;
    }

    public function getUserExportRenderer(): UserExportRenderer
    {
        return $this->userExportRenderer;
    }

    protected function render_exported_users(array $user_export_headers, array $exported_users)
    {
        return $this->getUserExportRenderer()->render($user_export_headers, $exported_users);
    }
}