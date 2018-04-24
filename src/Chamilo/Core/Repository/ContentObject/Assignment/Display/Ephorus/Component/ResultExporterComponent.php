<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ResultRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\File\Path;
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
        $entryId = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTRY_ID);
        $requests = $this->getDataProvider()->findEphorusRequestsForAssignmentEntries([$entryId]);

        if (empty($requests))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('RequestNotFound', null, Manager::context())
            );
        }

        $request = $requests[0];

        $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class,
            $request->get_content_object_id()
        );

        $html = array();
        $html[] = '<html><head>';
        $html[] = '<style type="text/css">' . file_get_contents(
                Path::getInstance()->getBasePath(true) .
                'application/weblcms/tool/ephorus/ephorus_request/resources/css/report.css'
            ) . '</style>';
        $html[] = '</head><body>';

        $result_to_html_converter = new ResultRenderer();
        $html[] = $result_to_html_converter->convert_to_html($request->getId());

        $html[] = '</body></html>';

        $unique_file_name = \Chamilo\Libraries\File\Filesystem::create_unique_name(
            Path::getInstance()->getTemporaryPath(),
            $content_object->get_title() . '.html'
        );

        $full_file_name = Path::getInstance()->getTemporaryPath() . $unique_file_name;
        \Chamilo\Libraries\File\Filesystem::create_dir(dirname($full_file_name));
        \Chamilo\Libraries\File\Filesystem::write_to_file($full_file_name, implode(PHP_EOL, $html));
        \Chamilo\Libraries\File\Filesystem::file_send_for_download($full_file_name, true);
        \Chamilo\Libraries\File\Filesystem::remove($full_file_name);
    }
}