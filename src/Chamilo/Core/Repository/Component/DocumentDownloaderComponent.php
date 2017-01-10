<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
        $object_id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $object_id);
        
        if (! $object_id)
        {
            throw new \Exception(
                Translation::get(
                    'NoObjectSelected', 
                    array('OBJECT' => Translation::get('ContentObject')), 
                    Utilities::COMMON_LIBRARIES));
        }
        
        $object = DataManager::retrieve_by_id(ContentObject::class_name(), $object_id);
        $valid_types = array(
            'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File', 
            'Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage', 
            'Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass\ExternalCalendar'
        );
        
        if (! $object || ! in_array($object->get_type(), $valid_types))
        {
            throw new UserException(Translation::get('ContentObjectMustBeDocument'));
        }
        
        $security_code = Request::get(ContentObject::PARAM_SECURITY_CODE);
        if ($security_code != $object->calculate_security_code())
        {
            throw new UserException(Translation::get('SecurityCodeNotValid', null, Utilities::COMMON_LIBRARIES));
        }
        
        if (Request::get('display') == 1)
        {
            $object->open_in_browser();
        }
        else
        {
            $object->send_as_download();
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_document_downloader');
    }
}
