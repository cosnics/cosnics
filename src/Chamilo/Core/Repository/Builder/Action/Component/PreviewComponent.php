<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;

/**
 * This component previews the complex content object
 *
 * @author Sven Vanpoucke
 */
class PreviewComponent extends Manager
{

    /**
     * Runs the component and displays it's output
     */
    public function run()
    {
        return $this->getPreview()->run();
    }

    /**
     * Modification of the display header to show the preview mode warning
     *
     * @return string
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = '<br /><div style="border: 1px solid #FFD89B;" class="row warning-banner bg-warning text-warning">';
        $html[] = Translation :: get('PreviewModeWarning');
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getPreview()
    {
        $contentObjectClassname = $this->get_root_content_object()->get_type();
        $contentObjectNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
            $contentObjectClassname);
        $contentObjectNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($contentObjectNamespace, 2);
        $namespace = $contentObjectNamespace . '\Display\Preview';

        return new ApplicationFactory($namespace, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
    }
}
