<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'personal_action';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_OBJECT = 'object';

    // Properties
    const ACTION_VIEW = 'Viewer';
    const ACTION_CREATE = 'Publisher';
    const ACTION_DELETE = 'Deleter';
    const ACTION_EDIT = 'Editor';
    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    const ACTION_EXPORT = 'Exporter';
    const ACTION_IMPORT = 'Importer';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_VIEW;

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer :: PARAM_TIME,
            \Chamilo\Libraries\Calendar\Renderer\Type\ViewRenderer :: PARAM_TYPE);
    }
}
