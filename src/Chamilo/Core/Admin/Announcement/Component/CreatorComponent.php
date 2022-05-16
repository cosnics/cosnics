<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Form\PublicationForm;
use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Publisher;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Admin\Announcement\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends Manager implements ViewerInterface
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }
        else
        {
            $contentObjectIdentifiers = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();

            if (!is_array($contentObjectIdentifiers))
            {
                $contentObjectIdentifiers = array($contentObjectIdentifiers);
            }

            $publicationForm = new PublicationForm(
                PublicationForm::TYPE_CREATE, $this->get_url(), $this->getRightsService()->getEntities()
            );

            if ($publicationForm->validate())
            {
                $success = $this->getPublicationService()
                    ->createPublicationsForUserIdentifierAndContentObjectIdentifiersFromValues(
                        $this->getUser()->getId(), $contentObjectIdentifiers, $publicationForm->exportValues()
                    );

                $message = Translation::get(
                    ($success ? 'ObjectPublished' : 'ObjectNotPublished'),
                    array('OBJECT' => Translation::get('Object')), Utilities::COMMON_LIBRARIES
                );

                $parameters = array(self::PARAM_ACTION => self::ACTION_BROWSE);

                $this->redirect($message, !$success, $parameters);
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $this->renderContentObjects($contentObjectIdentifiers);
                $html[] = $publicationForm->render();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     * @return string[]
     */
    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;

        return $additionalParameters;
    }

    /**
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        return array(SystemAnnouncement::class);
    }

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return string
     */
    private function renderContentObjects(array $contentObjectIdentifiers)
    {
        $html = [];

        $items_to_publish = count($contentObjectIdentifiers);
        $publications = [];

        if ($items_to_publish > 0)
        {
            $parameters = new DataClassRetrievesParameters(
                new InCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    $contentObjectIdentifiers, ContentObject::get_table_name()
                )
            );

            $contentObjects = DataManager::retrieve_active_content_objects(
                ContentObject::class, $parameters
            );

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . Translation::get(
                    'SelectedContentObjects', null, Utilities::COMMON_LIBRARIES
                ) . '</h3>';
            $html[] = '</div>';
            $html[] = '<ul class="list-group">';

            foreach($contentObjects as $contentObject)
            {
                $namespace = ContentObject::get_content_object_type_namespace($contentObject->get_type());
                $glyph = $contentObject->getGlyph(IdentGlyph::SIZE_MINI);

                if (RightsService::getInstance()->canUseContentObject($this->getUser(), $contentObject))
                {
                    $html[] =
                        '<li class="list-group-item">' . $glyph->render() . ' ' . $contentObject->get_title() . '</li>';
                }
                else
                {
                    $html[] = '<li class="list-group-item">' . $glyph->render() . ' ' . $contentObject->get_title() .
                        '<em class="text-danger">' . Translation::get('NotAllowed') . '</em>' . '</li>';
                }
            }

            $html[] = '</ul>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
