<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Weblcms\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeComponent extends Manager implements DelegateComponent
{
    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        $component = $this->isAuthorized(Manager::context(), 'ViewPersonalCourses') ? 'CourseList' :
            'OpenCoursesBrowser';

        $redirect = new Redirect(
            array(
                self::PARAM_CONTEXT => Manager::context(),
                self::PARAM_ACTION => $component
            )
        );

        $redirect->toUrl();
    }
}
