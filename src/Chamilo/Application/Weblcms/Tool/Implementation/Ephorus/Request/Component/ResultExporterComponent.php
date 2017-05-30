<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;

/**
 * User: Pieterjan Broekaert Date: 30/07/12 Time: 12:41
 *
 * @author Pieterjan Broekaert Hogent
 */
class ResultExporterComponent extends Manager
{

    public function run()
    {
        if ($this->can_execute_component())
        {
            $request_id = \Chamilo\Libraries\Platform\Session\Request::get(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager::PARAM_CONTENT_OBJECT_IDS
            );
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager::PARAM_CONTENT_OBJECT_IDS,
                $request_id
            );

            $request = DataManager::retrieve_by_id(Request::class_name(), $request_id);

            if (!$request instanceof Request)
            {
                throw new UserException(
                    Translation::getInstance()->getTranslation('RequestNotFound', null, Manager::context())
                );
            }

            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $request->get_content_object_id()
            );

            $html = array();
            $html[] = '<html><head>';
            $html[] = '<style type="text/css">' . file_get_contents(
                    Path::getInstance()->getBasePath(true) .
                    'application/weblcms/tool/ephorus/ephorus_request/resources/css/report.css'
                ) . '</style>';
            $html[] = '</head><body>';

            $result_to_html_converter = new ResultToHtmlConverter();
            $html[] = $result_to_html_converter->convert_to_html($request_id);

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
        else
        {
            throw new NotAllowedException();
        }
    }

    protected function can_execute_component()
    {
        return $this->get_parent()->is_allowed(WeblcmsRights::EDIT_RIGHT);
    }
}