<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Rights;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class PublicationTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @param $table
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     *
     * @throws \Exception
     */
    public function __construct($table, UserService $userService, GroupService $groupService)
    {
        parent::__construct($table);

        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService): void
    {
        $this->groupService = $groupService;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function get_actions($publication)
    {
        $toolbar = new Toolbar();

        if ($this->get_component()->get_user()->is_platform_admin() ||
            $publication[Publication::PROPERTY_PUBLISHER_ID] == $this->get_component()->get_user()->get_id())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', array(), Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Edit'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_EDIT,
                        Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[Publication::PROPERTY_ID]
                    )
                ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', array(), Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[Publication::PROPERTY_ID]
                    )
                ), ToolbarItem::DISPLAY_ICON, true
                )
            );

            if ($publication[Publication::PROPERTY_HIDDEN])
            {
                $visibility_img = 'Action/Invisible';
            }
            elseif ($publication[Publication::PROPERTY_FROM_DATE] == 0 &&
                $publication[Publication::PROPERTY_TO_DATE] == 0)
            {
                $visibility_img = 'Action/Visible';
            }
            else
            {
                $visibility_img = 'Action/Period';
            }

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Hide', array(), Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath($visibility_img), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_HIDE,
                        Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[Publication::PROPERTY_ID]
                    )
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    public function render_cell($column, $publication)
    {
        $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), $publication[Publication::PROPERTY_CONTENT_OBJECT_ID]
        );

        switch ($column->get_name())
        {
            case PublicationTableColumnModel::COLUMN_STATUS :
                return $content_object->get_icon_image(
                    Theme::ICON_MINI, !(boolean) $publication[Publication::PROPERTY_HIDDEN]
                );
                break;
            case ContentObject::PROPERTY_TITLE :
                $title_short = $content_object->get_title();
                $title_short = StringUtilities::getInstance()->truncate($title_short, 53, false);

                $style = $publication[Publication::PROPERTY_HIDDEN] ? ' style="color: gray;"' : '';

                return '<a' . $style . ' href="' . htmlentities(
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_VIEW,
                                Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication[Publication::PROPERTY_ID]
                            )
                        )
                    ) . '" title="' . htmlentities($content_object->get_title()) . '">' . $title_short . '</a>';
                break;
            case Publication::PROPERTY_PUBLICATION_DATE :
                $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
                $data = DatetimeUtilities::format_locale_date(
                    $date_format, $publication[Publication::PROPERTY_PUBLICATION_DATE]
                );
                break;
            case Publication::PROPERTY_PUBLISHER_ID :
                $user = $this->getUserService()->findUserByIdentifier(
                    (int) $publication[Publication::PROPERTY_PUBLISHER_ID]
                );

                if (!$user)
                {
                    $data = '<i>' . Translation::get('UserUnknown') . '</i>';
                }
                else
                {
                    $data = $user->get_fullname();
                }
                break;
            case PublicationTableColumnModel::COLUMN_PUBLISHED_FOR :
                $data = '<div style="float: left;">' . $this->render_publication_targets($publication) . '</div>';

                if ($publication[Publication::PROPERTY_EMAIL_SENT])
                {
                    $email_icon = ' - <img src="' . Theme::getInstance()->getCommonImagePath('Action/Email') . '" alt=""
                        style="vertical-align: middle;" title="' . Translation::get('SentByEmail') . '"/>';

                    $data .= $email_icon;
                }
                break;
            case ContentObject::PROPERTY_DESCRIPTION :
                $data = $publication[ContentObject::PROPERTY_DESCRIPTION];
                $data = StringUtilities::getInstance()->truncate($data, 100);
        }

        if ($data)
        {
            if ($publication[Publication::PROPERTY_HIDDEN])
            {
                return '<span style="color: gray">' . $data . '</span>';
            }
            else
            {
                return $data;
            }
        }

        return parent::render_cell($column, $publication);
    }

    /**
     * Renders the publication targets
     *
     * @param Publication $publication
     *
     * @return string
     */
    public function render_publication_targets($publication)
    {
        $target_entities = Rights::getInstance()->get_target_entities(
            Rights::VIEW_RIGHT, Manager::context(), $publication[Publication::PROPERTY_ID], Rights::TYPE_PUBLICATION
        );

        $target_list = array();

        if (array_key_exists(0, $target_entities[0]))
        {
            $target_list[] = Translation::get('Everybody', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';

            foreach ($target_entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case PlatformGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = $this->getGroupService()->getGroupByIdentifier($group_id);

                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case UserEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $user = $this->getUserService()->findUserByIdentifier((int) $user_id);

                            if ($user)
                            {
                                $target_list[] = '<option>' . $user->get_fullname() . '</option>';
                            }
                        }
                        break;
                    case 0 :
                        $target_list[] = '<option>Everyone</option>';
                        break;
                }
            }
            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }
}
