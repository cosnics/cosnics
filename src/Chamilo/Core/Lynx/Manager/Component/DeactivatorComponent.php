<?php
namespace Chamilo\Core\Lynx\Manager\Component;

use Chamilo\Configuration\Package\NotAllowed;
use Chamilo\Core\Lynx\Manager\Action\PackageDeactivator;
use Chamilo\Core\Lynx\Manager\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class DeactivatorComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $context = Request::get(self::PARAM_CONTEXT);
        $deactivator = new PackageDeactivator($context);
        $deactivator->run();
        
        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                null, 
                Translation::get(
                    'DeactivatingPackage', 
                    array('PACKAGE' => Translation::get('TypeName', null, $this->context)))));
        
        if ($deactivator instanceof NotAllowed)
        {
            throw new NotAllowedException();
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $deactivator->get_result(true);
        
        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('BackToPackageOVerview'), 
                Theme::getInstance()->getCommonImagePath('Action/Back'), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE))));
        
        $html[] = $toolbar->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }
}
