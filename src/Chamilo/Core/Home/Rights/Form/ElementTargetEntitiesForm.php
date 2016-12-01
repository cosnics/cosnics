<?php
namespace Chamilo\Core\Home\Rights\Form;

use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Target Entities Form
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementTargetEntitiesForm extends TargetEntitiesForm
{

    /**
     *
     * @var ElementRightsService
     */
    protected $elementRightsService;

    /**
     * The current element
     * 
     * @var Element
     */
    protected $element;

    /**
     * Constructor
     * TargetEntitiesForm constructor.
     * 
     * @param Element $element
     * @param string $action
     * @param ElementRightsService $elementRightsService
     */
    public function __construct(Element $element, $action, ElementRightsService $elementRightsService)
    {
        $this->element = $element;
        $this->elementRightsService = $elementRightsService;
        
        parent::__construct(sprintf('home_block_%s_target_entities_form', $element->getId()), $action);
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        parent::buildForm();
        
        $buttonGroup = $this->getElement('buttons');
        $buttons = $buttonGroup->getElements();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'cancel', 
            Translation::get('Cancel', null, Utilities::COMMON_LIBRARIES), 
            array('class' => 'btn-danger'), 
            null, 
            'remove');
        
        $buttonGroup->setElements($buttons);
    }

    /**
     * Returns the selected entities
     * 
     * @return HomeTargetEntity[]
     */
    protected function getSelectedEntities()
    {
        return $this->elementRightsService->getTargetEntitiesForElement($this->element);
    }
}