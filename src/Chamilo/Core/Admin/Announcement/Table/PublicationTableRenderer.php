<?php
namespace Chamilo\Core\Admin\Announcement\Table;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Service\ContentObjectService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Announcement\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{
    public const COLUMN_PUBLISHED_FOR = 'published_for';
    public const COLUMN_STATUS = 'status';

    public const TABLE_IDENTIFIER = Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID;

    protected ContentObjectService $contentObjectService;

    protected DatetimeUtilities $datetimeUtilities;

    protected GroupService $groupService;

    protected RightsService $rightsService;

    protected User $user;

    protected UserService $userService;

    public function __construct(
        RightsService $rightsService, UserService $userService, GroupService $groupService,
        DatetimeUtilities $datetimeUtilities, ContentObjectService $contentObjectService, User $user,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->rightsService = $rightsService;
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->user = $user;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->contentObjectService = $contentObjectService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getContentObjectService(): ContentObjectService
    {
        return $this->contentObjectService;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_STATUS, $translator->trans('Status', [], 'Chamilo\Core\Admin\Announcement')
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Publication::class, Publication::PROPERTY_PUBLICATION_DATE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Publication::class, Publication::PROPERTY_PUBLISHER_ID
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_PUBLISHED_FOR, $translator->trans('PublishedFor', [], 'Chamilo\Core\Admin\Announcement')
            )
        );
    }

    /**
     * @param string[]|int[] $publication
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $publication): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $contentObject = $this->getContentObjectService()->retrieveContentObjectByIdentifier(
            $publication[Publication::PROPERTY_CONTENT_OBJECT_ID]
        );

        switch ($column->get_name())
        {
            case self::COLUMN_STATUS :
                return $contentObject->get_icon_image(
                    IdentGlyph::SIZE_MINI, !$publication[Publication::PROPERTY_HIDDEN]
                );
            case ContentObject::PROPERTY_TITLE :
                $title_short = $contentObject->get_title();
                $title_short = StringUtilities::getInstance()->truncate($title_short, 53, false);

                $style = $publication[Publication::PROPERTY_HIDDEN] ? ' style="color: gray;"' : '';

                return '<a' . $style . ' href="' . htmlentities(
                        $urlGenerator->fromParameters(
                            [
                                Application::PARAM_CONTEXT => Manager::CONTEXT,
                                Manager::PARAM_ACTION => Manager::ACTION_VIEW,
                                Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[DataClass::PROPERTY_ID]
                            ]
                        )
                    ) . '" title="' . htmlentities($contentObject->get_title()) . '">' . $title_short . '</a>';
            case Publication::PROPERTY_PUBLICATION_DATE :
                $date_format = $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES);
                $data = $this->getDatetimeUtilities()->formatLocaleDate(
                    $date_format, $publication[Publication::PROPERTY_PUBLICATION_DATE]
                );

                return $publication[Publication::PROPERTY_HIDDEN] ? '<span style="color: gray">' . $data . '</span>' :
                    $data;
            case Publication::PROPERTY_PUBLISHER_ID :
                $user = $this->getUserService()->findUserByIdentifier(
                    $publication[Publication::PROPERTY_PUBLISHER_ID]
                );

                if (!$user)
                {
                    $data = '<i>' . $translator->trans('UserUnknown', [], 'Chamilo\Core\User') . '</i>';
                }
                else
                {
                    $data = $user->get_fullname();
                }

                return $publication[Publication::PROPERTY_HIDDEN] ? '<span style="color: gray">' . $data . '</span>' :
                    $data;
            case self::COLUMN_PUBLISHED_FOR :
                $data = '<div style="float: left;">' . $this->renderPublicationTargets($publication) . '</div>';

                if ($publication[Publication::PROPERTY_EMAIL_SENT])
                {
                    $glyph = new FontAwesomeGlyph('envelope', [],
                        $translator->trans('SentByEmail', [], 'Chamilo\Core\Admin\Announcement'));
                    $email_icon = ' - ' . $glyph->render();

                    $data .= $email_icon;
                }

                return $publication[Publication::PROPERTY_HIDDEN] ? '<span style="color: gray">' . $data . '</span>' :
                    $data;
            case ContentObject::PROPERTY_DESCRIPTION :
                $data = $publication[ContentObject::PROPERTY_DESCRIPTION];
                $data = StringUtilities::getInstance()->truncate($data, 100);

                return $publication[Publication::PROPERTY_HIDDEN] ? '<span style="color: gray">' . $data . '</span>' :
                    $data;
        }

        return parent::renderCell($column, $resultPosition, $publication);
    }

    /**
     * @param string[]|int[] $publication
     *
     * @throws \Exception
     */
    public function renderPublicationTargets(array $publication): string
    {
        $translator = $this->getTranslator();

        $targetEntities = $this->getRightsService()->getViewTargetUsersAndGroupsIdentifiersForPublicationIdentifier(
            $publication[DataClass::PROPERTY_ID]
        );

        $target_list = [];

        if (array_key_exists(0, $targetEntities[0]))
        {
            $target_list[] = $translator->trans('Everybody', [], StringUtilities::LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';

            foreach ($targetEntities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case GroupEntityProvider::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = $this->getGroupService()->findGroupByIdentifier($group_id);

                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case UserEntityProvider::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $user = $this->getUserService()->findUserByIdentifier($user_id);

                            if ($user)
                            {
                                $target_list[] = '<option>' . $user->get_fullname() . '</option>';
                            }
                        }
                        break;
                    case 0 :
                        $target_list[] =
                            '<option>' . $translator->trans('Everybody', [], StringUtilities::LIBRARIES) . '</option>';
                        break;
                }
            }
            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }

    /**
     * @param string[]|int[] $publication
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $publication): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $toolbar = new Toolbar();

        if ($this->getUser()->isPlatformAdmin() ||
            $publication[Publication::PROPERTY_PUBLISHER_ID] == $this->getUser()->getId())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_EDIT,
                            Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[DataClass::PROPERTY_ID]
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[DataClass::PROPERTY_ID]
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );

            if ($publication[Publication::PROPERTY_HIDDEN])
            {
                $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
            }
            elseif ($publication[Publication::PROPERTY_FROM_DATE] == 0 &&
                $publication[Publication::PROPERTY_TO_DATE] == 0)
            {
                $glyph = new FontAwesomeGlyph('eye');
            }
            else
            {
                $glyph = new FontAwesomeGlyph('clock');
            }

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Hide', [], StringUtilities::LIBRARIES), $glyph, $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_HIDE,
                        Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[DataClass::PROPERTY_ID]
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
