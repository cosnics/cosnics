<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Manager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
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
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $publication_id
        );

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            $this->redirectWithMessage(
                Translation::get('NotAllowed', null, StringUtilities::LIBRARIES), true, [], [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                ]
            );
        }

        $this->getCategoryBreadcrumbsGenerator()->generateBreadcrumbsForContentObjectPublication(
            BreadcrumbTrail::getInstance(), $this, $this->publication
        );
        
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    public function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_delete_feedback($feedback)
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node)
    {
        $hasWorkspaceRight = $this->getRightsService()->canEditContentObject(
            $this->get_user(), $this->publication->get_content_object()
        );

        $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();

        $hasPublicationContentRight = $weblcmsRightsService->canUserEditPublication(
            $this->get_user(), $this->publication, $this->get_application()->get_course()
        );

        $hasPublictionRight = $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication) &&
            $this->publication->get_allow_collaboration();

        return $hasWorkspaceRight || $hasPublicationContentRight || $hasPublictionRight;
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node)
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }
}
