<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Table\ContentObjectPropertyRelMetadataElement;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table for the ContentObjectPropertyRelMetadataElement data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectPropertyRelMetadataElementTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID;

    /**
     * Returns the implemented form actions
     * 
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE), 
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));
        
        return $actions;
    }
}