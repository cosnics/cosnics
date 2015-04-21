<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Shared;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTableColumnModel;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class SharedTableCellRenderer extends DataClassTableCellRenderer
{

    // the entities a content object is shared with
    private $target_entities;

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case SharedTableColumnModel :: GIVENRIGHTS :
                return $this->get_sharing_links($content_object);
            case SharedTableColumnModel :: MANAGERIGHTS :
                return $this->get_rights_links($content_object);
            case RepositoryTableColumnModel :: PROPERTY_TYPE :
                $image = $content_object->get_icon_image(Theme :: ICON_MINI);
                return '<a href="' . Utilities :: htmlentities(
                    $this->get_component()->get_type_filter_url($content_object->get_template_registration_id())) .
                     '" title="' . htmlentities($content_object->get_type_string()) . '">' . $image . '</a>';
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = StringUtilities :: getInstance()->truncate($title, 53, false);
                return $title_short;
            case ContentObject :: PROPERTY_DESCRIPTION :
                return StringUtilities :: getInstance()->truncate($content_object->get_description(), 50);
            case RepositoryTableColumnModel :: PROPERTY_VERSION :
                if ($content_object instanceof Versionable)
                {
                    if ($content_object->has_versions())
                    {
                        $number = $content_object->get_version_count();
                        return '<img src="' .
                             Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'VersionsMultiple') .
                             '" alt="' . Translation :: get('VersionsAvailable', array('NUMBER' => $number)) .
                             '" title="' . Translation :: get('VersionsAvailable', array('NUMBER' => $number)) . '" />';
                    }
                    else
                    {
                        return '<img src="' .
                             Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'VersionsNone') . '" alt="' .
                             Translation :: get('NoVersionsAvailable') . '" title="' .
                             Translation :: get('NoVersionsAvailable') . '" />';
                    }
                }
                else
                {
                    return '<img src="' . Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository',
                        'VersionsNone') . '" alt="' . Translation :: get('NotVersionable') . '" title="' .
                         Translation :: get('NotVersionable') . '" />';
                }
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' .
                         Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES),
                        $content_object->get_modification_date());
            case ContentObject :: PROPERTY_OWNER_ID :
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    User :: class_name(),
                    (int) $content_object->get_owner_id());
                if ($user)
                {
                    return $user->get_fullname();
                }
                else
                {
                    return Translation :: get('UserDeleted');
                }
            case Translation :: get(SharedTableColumnModel :: SHAREWITH) :
                return $this->get_shared_users_groups($content_object);
        }
    }

    private function get_sharing_links($content_object)
    {
        $user = $this->get_component()->get_user();

        $toolbar = new Toolbar();

        $copy_right = RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
            RepositoryRights :: COPY_RIGHT,
            $content_object->get_id(),
            RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
            $content_object->get_owner_id());

        $collaborate_right = RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
            RepositoryRights :: COLLABORATE_RIGHT,
            $content_object->get_id(),
            RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
            $content_object->get_owner_id());

        $use_right = RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
            RepositoryRights :: USE_RIGHT,
            $content_object->get_id(),
            RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
            $content_object->get_owner_id());

        $view_right = RepositoryRights :: get_instance()->is_allowed_in_user_subtree(
            RepositoryRights :: VIEW_RIGHT,
            $content_object->get_id(),
            RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
            $content_object->get_owner_id());

        if ($view_right)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('View', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_component()->get_content_object_viewing_url($content_object),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/BrowserNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($use_right)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                    $this->get_component()->get_publish_content_object_url($content_object),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('PublishNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/PublishNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($collaborate_right)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_content_object_editing_url($content_object),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/EditNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($copy_right)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Copy', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Reuse'),
                    $this->get_component()->get_copy_content_object_url($content_object->get_id()),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('CopyNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/ReuseNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Move'),
                $this->get_component()->get_shared_content_object_moving_url($content_object),
                ToolbarItem :: DISPLAY_ICON));

        // only user shares can be deleted
        $target_entities = $this->get_target_entities($content_object);
        if (in_array($this->get_component()->get_user_id(), $target_entities[UserEntity :: ENTITY_TYPE]))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_shared_content_object_deletion_url($content_object),
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }

    private function get_rights_links($content_object)
    {
        $user = $this->get_component()->get_user();

        if ($user->get_id() == $content_object->get_owner_id())
        {
            $toolbar = new Toolbar();
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditShareRights'),
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_component()->get_share_content_objects_url($content_object->get_id()),
                    ToolbarItem :: DISPLAY_ICON));
            return $toolbar->as_html();
        }
        else
        {
            return null;
        }
    }

    private function get_shared_users_groups($content_object)
    {
        $target_entities = $this->get_target_entities($content_object);

        $shared_users = array_unique($target_entities[UserEntity :: ENTITY_TYPE]);
        $shared_groups = array_unique($target_entities[PlatformGroupEntity :: ENTITY_TYPE]);

        $html = array();
        $html[] = '<select>';

        foreach ($shared_users as $user_id)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(User :: class_name(), (int) $user_id);
            if ($user)
            {
                $user_name = $user->get_fullname();
            }
            else
            {
                $user_name = Translation :: get('UserDeleted');
            }
            $html[] = '<option value="u_' . $user_id . '">' . $user_name . '</option>';
        }

        if (count($shared_users) > 0 && count($shared_groups) > 0)
        {
            $html[] = '<option disabled="disabled">---------------------</option>';
        }

        foreach ($shared_groups as $group_id)
        {
            $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $group_id);

            $group_name = $group ? $group->get_name() : Translation :: get('GroupUnknown');

            $html[] = '<option value="g_' . $group_id . '">[' .
                 strtoupper(Translation :: get('GroupShort', null, \Chamilo\Core\Group\Manager :: context())) . '] ' .
                 $group_name . '</option>';
        }

        $html[] = '</select>';
        return implode(PHP_EOL, $html);
    }

    private function get_target_entities($content_object)
    {
        if (! $this->target_entities[$content_object->get_id()])
        {
            $this->target_entities[$content_object->get_id()] = RepositoryRights :: get_instance()->get_share_target_entities_overview(
                $content_object->get_id(),
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                $content_object->get_owner_id());
        }
        return $this->target_entities[$content_object->get_id()];
    }
}
