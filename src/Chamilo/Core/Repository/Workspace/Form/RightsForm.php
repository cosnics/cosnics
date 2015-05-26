<?php
namespace Chamilo\Core\Repository\Workspace\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsForm extends FormValidator
{
    const PROPERTY_ACCESS = 'targets';
    const PROPERTY_COPY = 'right_copy';
    const PROPERTY_USE = 'right_use';
    const PROPERTY_VIEW = 'right_view';

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    private $entityRelation;

    /**
     *
     * @param string $formUrl
     */
    public function __construct($formUrl, WorkspaceEntityRelation $entityRelation)
    {
        parent :: __construct('rights', 'post', $formUrl);

        $this->entityRelation = $entityRelation;

        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(UserEntity :: get_element_finder_type());
        $types->add_element_type(PlatformGroupEntity :: get_element_finder_type());
        $this->addElement('advanced_element_finder', self :: PROPERTY_ACCESS, Translation :: get('Users'), $types);

        $this->addElement(
            'radio',
            self :: PROPERTY_VIEW,
            Translation :: get('ContentRight'),
            Translation :: get('ViewRight'),
            RightsService :: RIGHT_VIEW);

        $this->addElement(
            'radio',
            self :: PROPERTY_VIEW,
            null,
            Translation :: get('AddRight'),
            RightsService :: RIGHT_VIEW | RightsService :: RIGHT_ADD);

        $this->addElement(
            'radio',
            self :: PROPERTY_VIEW,
            null,
            Translation :: get('EditRight'),
            RightsService :: RIGHT_VIEW | RightsService :: RIGHT_ADD | RightsService :: RIGHT_EDIT);

        $this->addElement(
            'radio',
            self :: PROPERTY_VIEW,
            null,
            Translation :: get('DeleteRight'),
            RightsService :: RIGHT_VIEW | RightsService :: RIGHT_ADD | RightsService :: RIGHT_EDIT |
                 RightsService :: RIGHT_DELETE);

        $this->addElement(
            'checkbox',
            self :: PROPERTY_USE,
            Translation :: get('UseRight'),
            null,
            null,
            RightsService :: RIGHT_USE);

        $this->addElement(
            'checkbox',
            self :: PROPERTY_COPY,
            Translation :: get('CopyRight'),
            null,
            null,
            RightsService :: RIGHT_COPY);

        $this->addSaveResetButtons();
    }

    public function setDefaults($defaults = array())
    {
        if ($this->entityRelation instanceof WorkspaceEntityRelation)
        {
            // TODO: Add logic to determine the right default value
            $defaults[self :: PROPERTY_VIEW] = RightsService :: RIGHT_VIEW;

            $defaults[self :: PROPERTY_USE] = $this->entityRelation->get_rights() & RightsService :: RIGHT_USE;
            $defaults[self :: PROPERTY_COPY] = $this->entityRelation->get_rights() & RightsService :: RIGHT_COPY;
        }
        else
        {
            $defaults[self :: PROPERTY_VIEW] = RightsService :: RIGHT_VIEW;
        }

        parent :: setDefaults($defaults);
    }
}
