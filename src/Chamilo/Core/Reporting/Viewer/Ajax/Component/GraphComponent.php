<?php
namespace Chamilo\Core\Reporting\Viewer\Ajax\Component;

use Chamilo\Core\Reporting\Viewer\Ajax\Manager;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GraphComponent extends Manager
{

    public function run()
    {
        $graphMd5 = $this->getRequest()->query->get(self::PARAM_GRAPHMD5);

        $rootPath = $this->getConfigurablePathBuilder()->getTemporaryPath();
        $base_path = $graphMd5 . '.png';
        $file = $rootPath . $base_path;

        $type = exif_imagetype($file);
        $mime = image_type_to_mime_type($type);
        $size = filesize($file);

        $response = new StreamedResponse();
        $response->headers->add(array('Content-Type' => $mime, 'Content-Length' => $size));
        $response->setCallback(
            function () use ($file) {
                readfile($file);
            }
        );

        $response->send();
    }
}