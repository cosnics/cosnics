<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Form\PublicationForm;
use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Publisher;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\Architecture\Traits\ViewerTrait;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Admin\Announcement\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends Manager implements ViewerInterface
{
    use ViewerTrait;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        if (!$this->isAnyObjectSelectedInViewer())
        {
            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }
        else
        {
            $contentObjectIdentifiers = $this->getObjectsSelectedInviewer();

            if (!is_array($contentObjectIdentifiers))
            {
                $contentObjectIdentifiers = [$contentObjectIdentifiers];
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
                    ($success ? 'ObjectPublished' : 'ObjectNotPublished'), ['OBJECT' => Translation::get('Object')],
                    StringUtilities::LIBRARIES
                );

                $parameters = [self::PARAM_ACTION => self::ACTION_BROWSE];

                $this->redirectWithMessage($message, !$success, $parameters);
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
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;

        return parent::getAdditionalParameters($additionalParameters);
    }

    protected function getWorkspaceRightsService(): RightsService
    {
        return this->getService(RightsService::class);
    }

    /**
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        return [SystemAnnouncement::class];
    }

    /**
     * @param int $contentObjectIdentifiers
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
                    $contentObjectIdentifiers
                )
            );

            $contentObjects = DataManager::retrieve_active_content_objects(
                ContentObject::class, $parameters
            );

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . Translation::get(
                    'SelectedContentObjects', null, StringUtilities::LIBRARIES
                ) . '</h3>';
            $html[] = '</div>';
            $html[] = '<ul class="list-group">';

            foreach ($contentObjects as $contentObject)
            {
                $namespace = ContentObject::get_content_object_type_namespace($contentObject->getType());
                $glyph = $contentObject->getGlyph(IdentGlyph::SIZE_MINI);

                if ($this->getWorkspaceRightsService()->canUseContentObject($this->getUser(), $contentObject))
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
