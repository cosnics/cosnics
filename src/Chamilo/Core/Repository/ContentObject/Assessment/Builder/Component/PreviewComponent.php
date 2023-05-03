<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Translation\Translation;

/**
 * This component previews the complex content object
 *
 * @author Sven Vanpoucke
 */
class PreviewComponent extends Manager implements DelegateComponent
{

    /**
     * Runs the component and displays it's output
     */
    public function run()
    {
        return $this->getPreview()->run();
    }

    public function getPreview()
    {
        $contentObjectClassname = $this->get_root_content_object()->getType();
        $contentObjectNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($contentObjectClassname);
        $contentObjectNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($contentObjectNamespace, 2);
        $namespace = $contentObjectNamespace . '\Display\Preview';

        return $this->getApplicationFactory()->getApplication(
            $namespace, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );
    }

    /**
     * Modification of the display header to show the preview mode warning
     *
     * @return string
     */
    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);

        $html[] = '<div class="alert alert-warning">';
        $html[] = Translation::get('PreviewModeWarning');
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
