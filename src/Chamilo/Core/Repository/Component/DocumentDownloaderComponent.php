<?php

namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package repository.lib.repository_manager.component
 */
class DocumentDownloaderComponent extends Manager implements NoAuthenticationSupport
{
    const PARAM_REDIRECTED_TO_DOWNLOAD_HOST = 'DownloadHost';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $object_id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $object_id);

        if (!$object_id)
        {
            throw new \Exception(
                Translation::get(
                    'NoObjectSelected',
                    array('OBJECT' => Translation::get('ContentObject')),
                    Utilities::COMMON_LIBRARIES
                )
            );
        }

        $object = DataManager::retrieve_by_id(ContentObject::class_name(), $object_id);
        $valid_types = array(
            'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File',
            'Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage',
            'Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass\ExternalCalendar'
        );

        if (!$object || !in_array($object->get_type(), $valid_types))
        {
            throw new UserException(Translation::get('ContentObjectMustBeDocument'));
        }

        $security_code = Request::get(ContentObject::PARAM_SECURITY_CODE);
        if ($security_code != $object->calculate_security_code())
        {
            throw new UserException(Translation::get('SecurityCodeNotValid', null, Utilities::COMMON_LIBRARIES));
        }

        $downloadHostRedirect = $this->redirectToDownloadHost($object_id, $security_code);
        if ($downloadHostRedirect instanceof RedirectResponse)
        {
            return $downloadHostRedirect;
        }

        if (Request::get('display') == 1)
        {
            $object->open_in_browser();
        }
        else
        {
            $object->send_as_download();
        }

        return null;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_document_downloader');
    }

    /**
     * @param $object_id
     * @param $security_code
     *
     * @return RedirectResponse
     */
    protected function redirectToDownloadHost($object_id, $security_code)
    {
        $fileDownloadHost =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'file_download_host']);

        if (empty($fileDownloadHost))
        {
            $this->getExceptionLogger()->logException(
                new \Exception('Please configure the file_download_host setting in your administrator settings')
            );

            return null;
        }

        $redirectedUrl = null;

        $isRedirected = $this->getRequest()->getFromUrl(self::PARAM_REDIRECTED_TO_DOWNLOAD_HOST);
        if (strpos($this->get_url(), $fileDownloadHost) === false && !$isRedirected)
        {
            $redirectedUrl = $this->get_url(
                [
                    self::PARAM_CONTENT_OBJECT_ID => $object_id, self::PARAM_REDIRECTED_TO_DOWNLOAD_HOST => 1,
                    ContentObject::PARAM_SECURITY_CODE => $security_code,
                    'display' => $this->getRequest()->getFromUrl('display')
                ]
        );

            $webPath = $this->getPathBuilder()->getBasePath(true);
            $lastChar = substr($fileDownloadHost, - 1);
            if ($lastChar != '/')
            {
                $fileDownloadHost .= '/';
            }

            $redirectedUrl = str_replace($webPath, $fileDownloadHost, $redirectedUrl);

            return new RedirectResponse($redirectedUrl);
        }

        return null;
    }
}
