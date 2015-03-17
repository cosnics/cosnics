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
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_OBJECT = 'object';

    // Properties
    const ACTION_VIEW = 'viewer';
    const ACTION_CREATE = 'publisher';
    const ACTION_DELETE = 'deleter';
    const ACTION_EDIT = 'editor';
    const ACTION_VIEW_ATTACHMENT = 'attachment_viewer';
    const ACTION_EXPORT = 'exporter';
    const ACTION_IMPORT = 'importer';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_VIEW;

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Libraries\Calendar\Renderer\Renderer :: PARAM_TIME,
            \Chamilo\Libraries\Calendar\Renderer\Renderer :: PARAM_TYPE);
    }
}
