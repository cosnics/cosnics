<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * $Id: document_downloader.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class DocumentDownloaderComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $object_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        if (! $object_id)
        {
            $this->handle_error(
                Translation :: get(
                    'NoObjectSelected',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES));
        }

        $object = DataManager :: retrieve_content_object($object_id);
        $valid_types = array(
            'core\repository\content_object\document\Document',
            'core\repository\content_object\file\File',
            'core\repository\content_object\webpage\Webpage',
            'core\repository\content_object\page\Page',
            'core\repository\content_object\external_calendar\ExternalCalendar');

        if (! $object || ! in_array($object->get_type(), $valid_types))
        {
            $this->handle_error(Translation :: get('ContentObjectMustBeDocument'));
        }

        $security_code = Request :: get(ContentObject :: PARAM_SECURITY_CODE);
        if ($security_code != $object->calculate_security_code())
        {
            $this->handle_error('SecurityCodeNotValid', null, Utilities :: COMMON_LIBRARIES);
        }

        if (Request :: get('display') == 1)
        {
            $object->open_in_browser();
        }
        else
        {
            $object->send_as_download();
        }
    }

    public function handle_error($error_message)
    {
        return $this->display_error_page($error_message);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_document_downloader');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_ID);
    }
}
