<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;

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
class ReportingViewerComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $classname = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME, $classname);
        
        $factory = new ApplicationFactory(
            \Chamilo\Core\Reporting\Viewer\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component = $factory->getComponent();
        $component->set_template_by_name($classname);
        return $component->run();
    }

    private function add_pcattree_breadcrumbs($pcattree, &$trail)
    {
        $cat = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $pcattree);
        
        $categories[] = $cat;
        while ($cat->get_parent() != 0)
        {
            $cat = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(), 
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
        if (Request::get('pcattree') != null && Request::get('pcattree') > 0)
        {
            $this->add_pcattree_breadcrumbs(Request::get('pcattree'), $breadcrumbtrail);
        }
        
        if (Request::get('cid') != null)
        {
            $cloi = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(), 
                Request::get('cid'));
            $wp = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $cloi->get_ref());
            
            $url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Request::get('tool') == 'learning_path' ? 'view_clo' : 'view', 
                    'display_action' => 'view_item', 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID), 
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID => Request::get('cid')));
            
            $breadcrumbtrail->add(new Breadcrumb($url, $wp->get_title()));
        }
    }
}
