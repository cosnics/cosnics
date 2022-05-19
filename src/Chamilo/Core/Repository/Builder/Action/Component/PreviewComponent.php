<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;

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
    public function render_header($pageTitle = '')
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);

        $html[] = '<div class="alert alert-warning">';
        $html[] = Translation::get('PreviewModeWarning');
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getPreview()
    {
        $contentObjectClassname = $this->get_root_content_object()->getType();
        $contentObjectNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($contentObjectClassname);
        $contentObjectNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($contentObjectNamespace, 2);
        $namespace = $contentObjectNamespace . '\Display\Preview';

        return $this->getApplicationFactory()->getApplication(
            $namespace,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
    }
}
