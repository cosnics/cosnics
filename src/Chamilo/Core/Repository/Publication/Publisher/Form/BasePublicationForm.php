<?php
namespace Chamilo\Core\Repository\Publication\Publisher\Form;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A base form which can be extended by publication forms
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class BasePublicationForm extends FormValidator
{

    /**
     * The selected content objects
     *
     * @var ContentObject[]
     */
    protected $selectedContentObjects;

    /**
     * Adds the selected content objects to the form
     *
     * @param User $user
     */
    protected function addSelectedContentObjects(User $user)
    {
        if (count($this->selectedContentObjects) == 0)
        {
            return;
        }

        $html[] = '<ul class="attachments_list">';

        foreach ($this->selectedContentObjects as $contentObject)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname(
                ContentObject::get_content_object_type_namespace($contentObject->get_type())
            );
            $glyph = $contentObject->getGlyph(IdentGlyph::SIZE_MINI);

            if (RightsService::getInstance()->canUseContentObject($user, $contentObject))
            {
                $html[] = '<li>' . $glyph->render() . ' ' . $contentObject->get_title() . '</li>';
            }
            else
            {
                $html[] = '<li>' . $glyph->render() . ' ' . $contentObject->get_title() . '<em class="text-danger">' .
                    Translation::get('NotAllowed') . '</em>' . '</li>';
            }
        }

        $html[] = '</ul>';

        $this->addElement(
            'static', '', Translation::get('SelectedContentObjects', null, Utilities::COMMON_LIBRARIES),
            implode(PHP_EOL, $html)
        );
    }

    /**
     *
     * @param ContentObject[] $selectedContentObjects
     */
    public function setSelectedContentObjects($selectedContentObjects = array())
    {
        $this->selectedContentObjects = $selectedContentObjects;
    }
}