<?php
namespace Chamilo\Core\Repository\Publication\Publisher\Form;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
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
     *
     * @param ContentObject[] $selectedContentObjects
     */
    public function setSelectedContentObjects($selectedContentObjects = array())
    {
        $this->selectedContentObjects = $selectedContentObjects;
    }

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
        
        foreach ($this->selectedContentObjects as $content_object)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname(
                ContentObject::get_content_object_type_namespace($content_object->get_type()));
            
            if (RightsService::getInstance()->canUseContentObject($user, $content_object))
            {
                $html[] = '<li><img src="' . $content_object->get_icon_path(Theme::ICON_MINI) . '" alt="' .
                     htmlentities(Translation::get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() .
                     '</li>';
            }
            else
            {
                $html[] = '<li><img src="' . $content_object->get_icon_path(Theme::ICON_MINI) . '" alt="' .
                     htmlentities(Translation::get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() .
                     '<span style="color: red; font-style: italic;">' . Translation::get('NotAllowed') . '</span>' .
                     '</li>';
            }
        }
        
        $html[] = '</ul>';
        
        $this->addElement(
            'static', 
            '', 
            Translation::get('SelectedContentObjects', null, Utilities::COMMON_LIBRARIES), 
            implode(PHP_EOL, $html));
    }
}