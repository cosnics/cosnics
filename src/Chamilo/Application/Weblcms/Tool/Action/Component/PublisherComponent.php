<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Publication\ContentObjectPublicationHandler;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormHandler;
use Chamilo\Application\Weblcms\Tool\Interfaces\PublisherCustomPublicationFormInterface;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublisherSupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: announcement_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.announcement.component
 */
class PublisherComponent extends Manager implements PublisherSupport, DelegateComponent
{

    /**
     * The publication form
     *
     * @var ContentObjectPublicationForm
     */
    protected $publicationForm;

    /**
     * Runs the component
     *
     * @return string
     * @throws NotAllowedException
     */
    public function run()
    {
        if (!($this->get_course()->is_course_admin($this->getUser()) || $this->is_allowed(WeblcmsRights :: ADD_RIGHT)))
        {
            throw new NotAllowedException();
        }

        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
        $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager :: SETTING_TABS_DISABLED, true);

        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\Publication\Publisher\Manager:: context(),
            $applicationConfiguration
        );

        return $factory->run();
    }

    /**
     * Returns the publication form
     *
     * @param ContentObject[] $selectedContentObjects
     *
     * @return FormValidator
     */
    public function getPublicationForm($selectedContentObjects = array())
    {
        $mode = Request:: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLISH_MODE);

        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLISH_MODE, $mode);
        $publish_type = PlatformSetting:: get('display_publication_screen', 'Chamilo\Application\Weblcms');

        $show_form =
            (($publish_type == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_FORM) || ($publish_type ==
                    \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_BOTH &&
                    $mode != \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_MODE_QUICK));

        if (!$show_form)
        {
            return null;
        }

        $course = $this->get_course();

        $publications = array();

        foreach ($selectedContentObjects as $contentObject)
        {
            $publication = new ContentObjectPublication();
            $publication->set_content_object_id($contentObject->getId());
            $publication->set_course_id($this->get_course_id());
            $publication->set_tool($this->get_tool_id());
            $publication->set_publisher_id($this->getUser()->getId());
            $publication->set_publication_publisher($this->getUser());
            $publications[] = $publication;
        }

        $this->publicationForm = $this->constructPublicationForm($publications, $selectedContentObjects);

        return $this->publicationForm;
    }

    /**
     * Constructs the publication form
     *
     * @param ContentObjectPublication[]   $publications
     * @param ContentObject[] $selectedContentObjects
     *
     * @return ContentObjectPublicationForm
     */
    protected function constructPublicationForm($publications, $selectedContentObjects)
    {
        $parentApplication = $this->get_application();
        if ($parentApplication instanceof PublisherCustomPublicationFormInterface)
        {
            return $parentApplication->constructPublicationForm($publications, $selectedContentObjects);
        }

        $course = $this->get_course();
        $is_course_admin = $course->is_course_admin($this->getUser());

        return new ContentObjectPublicationForm(
            $this->get_application()->context(),
            $this->getUser(),
            ContentObjectPublicationForm :: TYPE_CREATE,
            $publications,
            $course,
            $this->get_url(),
            $is_course_admin,
            $selectedContentObjects
        );
    }

    /**
     * Returns the publication handler
     *
     * @return PublicationHandlerInterface
     */
    public function getPublicationHandler()
    {
        $parentApplication = $this->get_application();
        if ($parentApplication instanceof PublisherCustomPublicationFormHandler)
        {
            return $parentApplication->getPublicationHandler($this->publicationForm);
        }

        return new ContentObjectPublicationHandler(
            $this->get_course_id(),
            $this->get_tool_id(),
            $this->getUser(),
            $this,
            $this->publicationForm
        );
    }
}
