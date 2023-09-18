<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package application\calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EditorComponent extends Manager implements BreadcrumbLessComponentInterface
{
    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user = $this->getUser();

        $id = $this->getRequest()->query->get(self::PARAM_PUBLICATION_ID);
        if ($id)
        {
            $calendarEventPublication = $this->getPublicationService()->findPublicationByIdentifier($id);

            if (!$this->getRightsService()->isAllowedToEditPublication($calendarEventPublication, $user))
            {
                throw new NotAllowedException();
            }

            $contentObject = $calendarEventPublication->get_publication_object();
            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(null, Translation::get('Edit', ['TITLE' => $contentObject->get_title()]))
            );

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, $this->getCurrentWorkspace(), $contentObject, 'edit',
                FormValidator::FORM_METHOD_POST, $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_EDIT,
                    self::PARAM_PUBLICATION_ID => $calendarEventPublication->getId()
                ]
            )
            );

            if ($form->validate() || $this->getRequest()->query->get('validated'))
            {
                if ($this->getRequest()->query->has('validated'))
                {
                    $success = $form->update_content_object();
                }

                if ($form->is_version())
                {
                    $calendarEventPublication->setContentObject($contentObject->get_latest_version());
                    $this->getPublicationService()->updatePublication($calendarEventPublication);
                }

                $publicationForm = new PublicationForm(
                    $user, $this->get_url(
                    [Manager::PARAM_PUBLICATION_ID => $calendarEventPublication->getId(), 'validated' => 1]
                )
                );

                $publicationForm->setPublicationDefaults(
                    $calendarEventPublication,
                    $this->getRightsService()->getUsersForPublication($calendarEventPublication),
                    $this->getRightsService()->getGroupsForPublication($calendarEventPublication)
                );

                if ($publicationForm->validate())
                {
                    $values = $publicationForm->exportValues();

                    $selectedUserIdentifiers =
                        (array) $values[PublicationForm::PARAM_SHARE][UserEntityProvider::ENTITY_TYPE];
                    $selectedGroupIdentifiers =
                        (array) $values[PublicationForm::PARAM_SHARE][GroupEntityProvider::ENTITY_TYPE];

                    $success = $this->getPublicationService()->updatePublicationWithRightsFromParameters(
                        $calendarEventPublication, $selectedUserIdentifiers, $selectedGroupIdentifiers
                    );

                    $message = $success ? Translation::get(
                        'ObjectUpdated', ['OBJECT' => Translation::get('PersonalCalendar')], StringUtilities::LIBRARIES
                    ) : Translation::get(
                        'ObjectNotUpdated', ['OBJECT' => Translation::get('PersonalCalendar')],
                        StringUtilities::LIBRARIES
                    );

                    $this->redirectWithMessage(
                        $message, !$success, [
                            self::PARAM_ACTION => Manager::ACTION_VIEW,
                            self::PARAM_PUBLICATION_ID => $calendarEventPublication->getId()
                        ]
                    );
                }
                else
                {
                    $html = [];

                    $html[] = $this->render_header();
                    $html[] = $publicationForm->render();
                    $html[] = $this->render_footer();

                    return implode(PHP_EOL, $html);
                }
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectsSelected', null, StringUtilities::LIBRARIES))
            );
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }
}
