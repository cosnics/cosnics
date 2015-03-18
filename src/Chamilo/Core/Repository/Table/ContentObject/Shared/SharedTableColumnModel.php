<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Shared;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class SharedTableColumnModel extends DataClassTableColumnModel
{
    const PROPERTY_TYPE = 'type';
    const PROPERTY_VERSION = 'version';
    const SHAREWITH = 'SharedWith';
    const GIVENRIGHTS = 'GivenRights';
    const MANAGERIGHTS = 'ManageRights';

    /**
     * The tables sharing column
     */
    private static $sharing_column;

    /**
     * The tables rights column
     */
    private static $rights_column;

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(Translation :: get(self :: SHAREWITH)));

        if ($this->get_component()->get_view() == Manager :: SHARED_VIEW_ALL_OBJECTS ||
             $this->get_component()->get_view() == Manager :: SHARED_VIEW_OWN_OBJECTS)
        {
            $this->add_column(self :: get_rights_column());
        }

        if ($this->get_component()->get_view() == Manager :: SHARED_VIEW_ALL_OBJECTS ||
             $this->get_component()->get_view() == Manager :: SHARED_VIEW_OTHERS_OBJECTS)
        {
            $this->add_column(self :: get_sharing_column());
        }

        $this->add_column(
            new StaticTableColumn(
                self :: PROPERTY_TYPE,
                Theme :: getInstance()->getCommonImage(
                    'Action/Category',
                    'png',
                    Translation :: get('Type'),
                    null,
                    ToolbarItem :: DISPLAY_ICON)));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_MODIFICATION_DATE));
        $this->add_column(new StaticTableColumn(self :: PROPERTY_VERSION, ContentObject :: get_version_header()));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID));
    }

    /**
     * Gets the sharing column
     *
     * @return StaticTableColumn
     */
    public static function get_sharing_column()
    {
        if (! isset(self :: $sharing_column))
        {
            self :: $sharing_column = new StaticTableColumn(Translation :: get(self :: GIVENRIGHTS));
        }
        return self :: $sharing_column;
    }

    /**
     * Gets the sharing column
     *
     * @return StaticTableColumn
     */
    public static function get_rights_column()
    {
        if (! isset(self :: $rights_column))
        {
            self :: $rights_column = new StaticTableColumn(Translation :: get(self :: MANAGERIGHTS));
        }
        return self :: $rights_column;
    }
}
