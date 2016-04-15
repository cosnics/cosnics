<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Manager;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: glossary_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.glossary.component
 */

/**
 * Represents the view component for the assessment tool.
 */
class ComplexDisplayComponent extends Manager implements DelegateComponent, GlossaryDisplaySupport
{

    private $publication;

    public function run()
    {
        $publication_id = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $publication_id);

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication))
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }

        $context = $this->publication->get_content_object()->package() . '\Display';

        $factory = new ApplicationFactory(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        $hasWorkspaceRight = RightsService :: getInstance()->canEditContentObject(
            $this->get_user(),
            $this->publication->get_content_object());

        $weblcmsRightsService = \Chamilo\Application\Weblcms\Service\RightsService :: getInstance();

        $hasPublicationContentRight = $weblcmsRightsService->canEditPublicationContentObject(
            $this->get_user(),
            $this->get_application()->get_course(),
            $this->publication);

        $hasPublictionRight = $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);

        return $hasWorkspaceRight || $hasPublicationContentRight || $hasPublictionRight;
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }
}
