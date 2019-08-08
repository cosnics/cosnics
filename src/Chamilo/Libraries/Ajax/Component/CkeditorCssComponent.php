<?php
namespace Chamilo\Libraries\Ajax\Component;

use Assetic\Asset\AssetCollection;
use Assetic\Filter\CssImportFilter;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Utilities\CssFileAsset;
use Chamilo\Libraries\Platform\Session\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CkeditorCssComponent extends \Chamilo\Libraries\Ajax\Manager implements NoAuthenticationSupport, NoVisitTraceComponentInterface
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $theme = Request::get('theme');
        $assets = array();

        $contentObjectTypes = \Chamilo\Core\Repository\Storage\DataManager::get_registered_types();

        /** @var PathBuilder $pathUtilities */
        $pathUtilities = $this->getService('chamilo.libraries.file.path_builder');
        $classNameUtilities = $this->getService('chamilo.libraries.architecture.classname_utilities');
        $themeUtilities = $this->getService('chamilo.libraries.format.theme');

        foreach ($contentObjectTypes as $contentObjectType)
        {

            $relativeEditorPath = '/HtmlEditor/Ckeditor/Stylesheet.css';
            $namespace = $classNameUtilities->getNamespaceFromClassname($contentObjectType);
            $namespace = $classNameUtilities->getNamespaceParent($namespace, 2);

            $stylesheetPath = $themeUtilities->getCssPath($namespace, false) . $relativeEditorPath;

            if (file_exists($stylesheetPath))
            {
                $asset = new CssFileAsset($pathUtilities, $stylesheetPath);
                $assets[] = $asset;
            }
        }

        $asset_collection = new AssetCollection($assets, array(new CssImportFilter()));

        $response = new Response();
        $response->setContent($asset_collection->dump());
        $response->headers->set('Content-Type', 'text/css');
        $response->send();
    }
}