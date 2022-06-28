<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
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
    public const PARAM_POPUP = 'popup';

    private $content_object;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->set_parameter(self::PARAM_POPUP, Request::get(self::PARAM_POPUP));
        $content_object_id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        try
        {
            $this->content_object = DataManager::retrieve_by_id(ContentObject::class, $content_object_id);

            if (!RightsService::getInstance()->canEditContentObject(
                $this->get_user(), $this->content_object, $this->getWorkspace()
            ))
            {
                throw new NotAllowedException();
            }

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BUILD_COMPLEX_CONTENT_OBJECT)),
                    Translation::get(
                        'BuildContentObject', array('CONTENT_OBJECT' => $this->content_object->get_title())
                    )
                )
            );

            $context =
                ClassnameUtilities::getInstance()->getNamespaceParent($this->content_object->getType(), 3) . '\Builder';

            return $this->getApplicationFactory()->getApplication(
                $context, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }
        catch (Exception $exception)
        {
            return $this->display_error_page(
                Translation::get(
                    'NoObjectSelected', array('OBJECT' => Translation::get('ContentObject')), StringUtilities::LIBRARIES
                )
            );
        }
    }

    public function get_root_content_object()
    {
        return $this->content_object;
    }

    public function redirect_away_from_complex_builder($message, $error_message)
    {
        $this->redirectWithMessage(
            $message, $error_message, array(self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS)
        );
    }

    public function render_header(string $pageTitle = ''): string
    {
        $is_popup = Request::get(self::PARAM_POPUP);

        if ($is_popup)
        {
            $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);
        }

        return parent::render_header($pageTitle);
    }
}
