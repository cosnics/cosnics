<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: reporting_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.component
 */

/**
 * Description of reporting_template_viewerclass
 *
 * @author Soliber
 */
class ReportingViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $classname = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_TEMPLATE_NAME);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_TEMPLATE_NAME, $classname);

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ReportingViewerComponent')));

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Reporting\Viewer\Manager :: context(),
            $this->get_user(),
            $this);
        $component = $factory->getComponent();
        $component->set_template_by_name($classname);
        return $component->run();
    }

    private function add_pcattree_breadcrumbs($pcattree, &$trail)
    {
        $cat = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $pcattree);

        $categories[] = $cat;
        while ($cat->get_parent() != 0)
        {
            $cat = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                ContentObjectPublication :: class_name(),
                $cat->get_parent());

            $categories[] = $cat;
        }
        $categories = array_reverse($categories);
        foreach ($categories as $categorie)
        {
            $trail->add(
                new Breadcrumb($this->get_url(array('pcattree' => $categorie->get_id())), $categorie->get_name()));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if (Request :: get('pcattree') != null && Request :: get('pcattree') > 0)
        {
            $this->add_pcattree_breadcrumbs(Request :: get('pcattree'), $breadcrumbtrail);
        }

        if (Request :: get('cid') != null)
        {
            $cloi = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
                Request :: get('cid'));
            $wp = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($cloi->get_ref());

            $url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Request :: get('tool') ==
                         'learning_path' ? 'view_clo' : 'view',
                        'display_action' => 'view_item',
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => Request :: get(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_COMPLEX_ID => Request :: get('cid')));

            $breadcrumbtrail->add(new Breadcrumb($url, $wp->get_title()));
        }
    }
}
