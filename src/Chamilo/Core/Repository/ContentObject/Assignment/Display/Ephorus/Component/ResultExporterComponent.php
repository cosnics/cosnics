<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\ReportExporter;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultExporterComponent extends Manager
{

    public function run()
    {
        $entryId = $this->getRequest()->getFromRequestOrQuery(self::PARAM_ENTRY_ID);
        $requests = $this->getDataProvider()->findEphorusRequestsForAssignmentEntries([$entryId]);

        if (empty($requests))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('RequestNotFound', null, Manager::context())
            );
        }

        $request = $requests[0];
        $this->getReportExporter()->exportRequestReport($request);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\ReportExporter
     */
    protected function getReportExporter()
    {
        return $this->getService(ReportExporter::class);
    }
}