<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * $Id: complex_builder.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */

/**
 * Component to build complex content object items
 *
 * @author vanpouckesven
 */
class BuilderComponent extends Manager implements ApplicationSupport
{
    const PARAM_POPUP = 'popup';

    private $content_object;

    public function render_header()
    {
        $is_popup = Request :: get(self :: PARAM_POPUP);

        if ($is_popup)
        {
            Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);
        }

        return parent :: render_header();
    }

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->set_parameter(self :: PARAM_POPUP, Request :: get(self :: PARAM_POPUP));
        $content_object_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        try
        {
            $this->content_object = $this->retrieve_content_object($content_object_id);

            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb(
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT)),
                    Translation :: get(
                        'BuildContentObject',
                        array('CONTENT_OBJECT' => $this->content_object->get_title()))));

            $context = ClassnameUtilities :: getInstance()->getNamespaceParent($this->content_object->get_type(), 3) .
                 '\Builder';
            $application_factory = new ApplicationFactory($this->getRequest(), $context, $this->get_user(), $this);
            return $application_factory->run();
        }
        catch (\Exception $exception)
        {
            return $this->display_error_page(
                Translation :: get(
                    'NoObjectSelected',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES));
        }
    }

    public function get_root_content_object()
    {
        return $this->content_object;
    }

    public function redirect_away_from_complex_builder($message, $error_message)
    {
        $this->redirect($message, $error_message, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_complex_builder');
    }
}
