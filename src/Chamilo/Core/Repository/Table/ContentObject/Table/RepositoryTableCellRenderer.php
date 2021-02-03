<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Table;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class RepositoryTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case RepositoryTableColumnModel::PROPERTY_TYPE :
                $image = $content_object->get_icon_image(Theme::ICON_MINI);
                return '<a href="' . Utilities::htmlentities(
                    $this->get_component()->get_type_filter_url($content_object->get_template_registration_id())) .
                     '" title="' . htmlentities($content_object->get_type_string()) . '">' . $image . '</a>';
            case ContentObject::PROPERTY_TITLE :
                $title = parent::render_cell($column, $content_object);
                $title_short = StringUtilities::getInstance()->truncate($title, 50, true);
                return '<a href="' .
                     Utilities::htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) .
                     '" title="' . htmlentities($title) . '">' . $title_short . '</a>';
            case ContentObject::PROPERTY_DESCRIPTION :
                return StringUtilities::getInstance()->truncate(
                    html_entity_decode($content_object->get_description()), 
                    50);
            case ContentObject::PROPERTY_OWNER_ID :
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    User::class_name(), 
                    $content_object->get_owner_id());
                if (! $user)
                {
                    return Translation::get('UserUnknown', null, 'Chamilo\Core\User');
                }
                
                return $user->get_fullname();
            case ContentObject::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
                    $content_object->get_creation_date());
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
                    $content_object->get_modification_date());
            case RepositoryTableColumnModel::PROPERTY_VERSION :
                if ($content_object instanceof Versionable)
                {
                    if ($content_object->has_versions())
                    {
                        $number = $content_object->get_version_count();
                        return '<img src="' .
                             Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'VersionsMultiple') .
                             '" alt="' . Translation::get('VersionsAvailable', array('NUMBER' => $number)) . '" title="' .
                             Translation::get('VersionsAvailable', array('NUMBER' => $number)) . '" />';
                    }
                    else
                    {
                        return '<img src="' .
                             Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'VersionsNone') . '" alt="' .
                             Translation::get('NoVersionsAvailable') . '" title="' .
                             Translation::get('NoVersionsAvailable') . '" />';
                    }
                }
                else
                {
                    return '<img src="' . Theme::getInstance()->getImagePath('Chamilo\Core\Repository', 'VersionsNone') .
                         '" alt="' . Translation::get('NotVersionable') . '" title="' .
                         Translation::get('NotVersionable') . '" />';
                }
        }
        
        return parent::render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        return $this->get_component()->get_content_object_actions($content_object);
    }
}
