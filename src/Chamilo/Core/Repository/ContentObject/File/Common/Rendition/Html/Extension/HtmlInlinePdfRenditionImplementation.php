<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlinePdfRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters = [])
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code()) . '&display=1&saveName=' . urlencode($object->get_filename());
        
        $viewerPath = Path::getInstance()->getResourcesPath(Utilities::COMMON_LIBRARIES, true) .
             'Javascript/Plugin/PDFJS/web/viewer.html';
        $viewerPath = $this->addDownloadHostToViewerPath($viewerPath);
        $url = $viewerPath . '?file=' . urlencode($url);
        
        $html = array();

        $html[] = '<div class="pull-right">';
        $html[] = '<a href="' . $url . '" target="_blank">';
        $html[] = '<input type="button" class="btn btn-default" value="' . Translation::getInstance()->getTranslation(
            'OpenInFullScreen', 
            null, 
            'Chamilo\Core\Repository\ContentObject\File') . '" />';
        $html[] = '</div>';
        $html[] = '</a>';
        $html[] = '<div class="clearfix"></div>';
        
        $html[] = '<div style="margin-top: 20px; border: 1px solid grey;"><iframe border="0" style="border: 0;"
                width="100%" height="600"  src="' . $url . '"></iframe></div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * @param string $viewerPath
     *
     * @return string
     */
    protected function addDownloadHostToViewerPath(string $viewerPath)
    {
        $this->initializeContainer();

        $fileDownloadHost =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'file_download_host']);

        if (empty($fileDownloadHost))
        {
            return $viewerPath;
        }

        $webPath = $this->getPathBuilder()->getBasePath(true);
        $lastChar = substr($fileDownloadHost, - 1);
        if ($lastChar != '/')
        {
            $fileDownloadHost .= '/';
        }

        $viewerPath = str_replace($webPath, $fileDownloadHost, $viewerPath);

        return $viewerPath;
    }
}
