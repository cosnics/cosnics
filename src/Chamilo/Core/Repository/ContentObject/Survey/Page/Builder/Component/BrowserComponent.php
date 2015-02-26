<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\Selector\Option\LinkTypeSelectorOption;
use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Builder\Action\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    function render_footer()
    {
        $html = array();

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->namespaceToFullPath(__NAMESPACE__, true) . 'resources/javascript/display_order.js');
        $html[] = parent :: render_footer();

        return implode("\n", $html);
    }

    function get_additional_links()
    {
        $links = array();

        $links[] = new LinkTypeSelectorOption(
            __NAMESPACE__,
            'merge',
            $this->get_url(
                array(\Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => self :: ACTION_MERGE_SURVEY_PAGE)));

        $links[] = new LinkTypeSelectorOption(
            __NAMESPACE__,
            'select_questions',
            $this->get_url(
                array(
                    \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager :: ACTION_BROWSER)));

        $links[] = new LinkTypeSelectorOption(
            __NAMESPACE__,
            'config',
            $this->get_url(
                array(
                    \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE,
                    ViewerComponent :: PARAM_ACTION => ViewerComponent :: ACTION_BROWSER)));

        return $links;
    }
}
?>