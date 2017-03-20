<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Gallery;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\RecordGalleryTable\RecordGalleryTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

/**
 * Gallery table for the content object publications
 *
 * @author Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record gallery table
 */
class ObjectPublicationGalleryTable extends RecordGalleryTable implements TableFormActionsSupport
{
    /**
     * The identifier for the table (used for table actions)
     */
    const TABLE_IDENTIFIER = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        return $this->get_component()->get_actions();
    }
}
