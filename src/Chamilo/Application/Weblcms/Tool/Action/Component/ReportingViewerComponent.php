<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * @package application.lib.weblcms.tool.component
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Soliber
 */
class ReportingViewerComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $classname = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME, $classname);

        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Reporting\Viewer\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );
        $component->set_template_by_name($classname);

        return $component->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        if ($this->getRequest()->query->has('pcattree') && $this->getRequest()->query->get('pcattree') > 0)
        {
            $this->add_pcattree_breadcrumbs($this->getRequest()->query->get('pcattree'), $breadcrumbtrail);
        }

        if ($this->getRequest()->query->has('cid'))
        {
            $cloi = DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $this->getRequest()->query->get('cid')
            );
            $wp = DataManager::retrieve_by_id(
                ContentObject::class, $cloi->get_ref()
            );

            $url = $this->get_url(
                [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => $this->getRequest()->query->get(
                        'tool'
                    ) == 'learning_path' ? 'view_clo' : 'view',
                    'display_action' => 'view_item',
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->getRequest()->query->get(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    ),
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID => $this->getRequest()->query->get(
                        'cid'
                    )
                ]
            );

            $breadcrumbtrail->add(new Breadcrumb($url, $wp->get_title()));
        }
    }

    private function add_pcattree_breadcrumbs($pcattree, $trail)
    {
        $cat = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pcattree
        );

        $categories[] = $cat;
        while ($cat->get_parent() != 0)
        {
            $cat = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class, $cat->get_parent()
            );

            $categories[] = $cat;
        }
        $categories = array_reverse($categories);
        foreach ($categories as $categorie)
        {
            $trail->add(
                new Breadcrumb($this->get_url(['pcattree' => $categorie->get_id()]), $categorie->get_name())
            );
        }
    }
}
