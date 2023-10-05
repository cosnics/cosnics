<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\EventDispatcher\Event\AfterUserExportEvent;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ExporterComponent extends Manager
{
    public const EXPORT_ACTION_ADD = 'A';
    public const EXPORT_ACTION_DEFAULT = self::EXPORT_ACTION_ADD;
    public const EXPORT_ACTION_DELETE = 'D';
    public const EXPORT_ACTION_UPDATE = 'U';

    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $fileType = $this->getRequest()->query->get(self::PARAM_EXPORT_TYPE);

        if ($fileType)
        {
            $users = $this->getUserService()->findUsers(null, null, 10);

            $data = [];

            foreach ($users as $user)
            {
                if ($fileType == 'Pdf')
                {
                    $userRecord = $this->prepareForPdfExport($user);
                }
                else
                {
                    $userRecord = $this->prepareForOtherExport($user);
                }

                $this->getEventDispatcher()->dispatch(new AfterUserExportEvent($this->getUser(), $user));

                $data[] = $userRecord;
            }

            $this->exportUsers($fileType, $data);
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->getButtonToolbarRenderer()->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function exportUsers(string $fileType, array $data): void
    {
        $filename = 'export_users_' . date('Y-m-d_H-i-s');

        if ($fileType == 'Pdf')
        {
            $data = [['key' => 'users', 'data' => $data]];
        }

        $this->getExporter($fileType)->sendtoBrowser($filename, $data);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolBarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            foreach ($this->getExportTypes() as $exportType)
            {
                $commonActions->addButton(
                    new Button(
                        $exportType, $this->getExportGlyph($exportType), $this->getExportUrl($exportType),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolBarRenderer;
    }

    public function getExportGlyph(string $exportFileType): InlineGlyph
    {
        $glyph = match ($exportFileType)
        {
            'Csv' => 'file-csv',
            'Excel' => 'file-excel',
            'Pdf' => 'file-pdf',
            'Xml' => 'file-lines',
            default => 'file',
        };

        return new FontAwesomeGlyph($glyph);
    }

    public function getExportTypes(): array
    {
        return ['Csv', 'Excel', 'Pdf', 'Xml'];
    }

    public function getExportUrl(string $exportFileType): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => self::ACTION_EXPORT_USERS,
                self::PARAM_EXPORT_TYPE => $exportFileType
            ]
        );
    }

    protected function getExporter(string $fileType): Export
    {
        return $this->getService('Chamilo\Libraries\File\Export\\' . $fileType . '\\' . $fileType . 'Export');
    }

    protected function getPlatformLanguageForUser(User $user): string
    {
        return $this->getUserSettingService()->getSettingForUser($user, 'Chamilo\Core\Admin', 'platform_language');
    }

    /**
     * @return string[]
     */
    public function prepareForOtherExport(User $user, string $action = self::EXPORT_ACTION_DEFAULT): array
    {
        // action => needed for import back into chamilo
        $user_array['action'] = $action;

        $user_array[User::PROPERTY_LASTNAME] = $user->get_lastname();
        $user_array[User::PROPERTY_FIRSTNAME] = $user->get_firstname();
        $user_array[User::PROPERTY_USERNAME] = $user->get_username();
        $user_array[User::PROPERTY_EMAIL] = $user->get_email();

        $user_array['language'] = $this->getPlatformLanguageForUser($user);
        $user_array[User::PROPERTY_STATUS] = $user->get_status();
        $user_array[User::PROPERTY_ACTIVE] = $user->get_active();
        $user_array[User::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $user_array[User::PROPERTY_PHONE] = $user->get_phone();
        $user_array[User::PROPERTY_ACTIVATION_DATE] = $user->get_activation_date();
        $user_array[User::PROPERTY_EXPIRATION_DATE] = $user->get_expiration_date();
        $user_array[User::PROPERTY_AUTH_SOURCE] = $user->getAuthenticationSource();

        return $user_array;
    }

    /**
     * @return string[]
     */
    public function prepareForPdfExport(User $user): array
    {
        $translator = $this->getTranslator();

        $lastnameTitle = $translator->trans('Lastname', [], Manager::CONTEXT);
        $firstnameTitle = $translator->trans('Firstname', [], Manager::CONTEXT);
        $usernameTitle = $translator->trans('Username', [], Manager::CONTEXT);
        $emailTitle = $translator->trans('Email', [], Manager::CONTEXT);
        $languageTitle = $translator->trans('Language', [], Manager::CONTEXT);
        $statusTitle = $translator->trans('Status', [], Manager::CONTEXT);
        $activeTitle = $translator->trans('Active', [], Manager::CONTEXT);
        $officialCodeTitle = $translator->trans('OfficalCode', [], Manager::CONTEXT);
        $phoneTitle = $translator->trans('Phone', [], Manager::CONTEXT);
        $activationDateTitle = $translator->trans('ActivationDate', [], Manager::CONTEXT);
        $expirationDateTitle = $translator->trans('ExpirationDate', [], Manager::CONTEXT);
        $authSourceTitle = $translator->trans('AuthSource', [], Manager::CONTEXT);

        $userProperties = [];

        $userProperties[$lastnameTitle] = $user->get_lastname();
        $userProperties[$firstnameTitle] = $user->get_firstname();
        $userProperties[$usernameTitle] = $user->get_username();
        $userProperties[$emailTitle] = $user->get_email();
        $userProperties[$languageTitle] = $this->getPlatformLanguageForUser($user);
        $userProperties[$statusTitle] = $user->get_status();
        $userProperties[$activeTitle] = $user->get_active();
        $userProperties[$officialCodeTitle] = $user->get_official_code();
        $userProperties[$phoneTitle] = $user->get_phone();
        $userProperties[$activationDateTitle] = $user->get_activation_date();
        $userProperties[$expirationDateTitle] = $user->get_expiration_date();
        $userProperties[$authSourceTitle] = $user->getAuthenticationSource();

        return $userProperties;
    }
}
