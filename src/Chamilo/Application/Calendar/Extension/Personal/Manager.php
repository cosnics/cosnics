<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService;
use Chamilo\Application\Calendar\Extension\Personal\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_CREATE = 'Publisher';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_EDIT = 'Editor';
    public const ACTION_EXPORT = 'Exporter';
    public const ACTION_VIEW = 'Viewer';
    public const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_VIEW;

    public const PARAM_ACTION = 'personal_action';
    public const PARAM_OBJECT = 'object';
    public const PARAM_PUBLICATION_ID = 'publication_id';

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->getService(PublicationService::class);
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService(RightsService::class);
    }
}
