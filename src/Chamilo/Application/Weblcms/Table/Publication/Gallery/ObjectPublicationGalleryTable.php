<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Gallery;

use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\RecordGalleryTable\RecordGalleryTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;

/**
 * Gallery table for the content object publications
 *
 * @author Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record gallery table
 */
class ObjectPublicationGalleryTable extends RecordGalleryTable implements TableActionsSupport
{
    /**
     * The identifier for the table (used for table actions)
     */
    const TABLE_IDENTIFIER = Manager::PARAM_PUBLICATION_ID;

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function getTableActions(): TableFormActions
    {
        return $this->get_component()->get_actions();
    }
}
