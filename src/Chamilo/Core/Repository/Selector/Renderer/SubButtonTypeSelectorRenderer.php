<?php
namespace Chamilo\Core\Repository\Selector\Renderer;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonTypeSelectorRenderer extends TypeSelectorRenderer
{

    /**
     *
     * @var string[]
     */
    private $parameters;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $parent
     * @param \Chamilo\Core\Repository\Selector\TypeSelector $typeSelector
     * @param string[] $parameters
     */
    public function __construct(
        Application $parent, TypeSelector $typeSelector, $parameters = []
    )
    {
        parent::__construct($parent, $typeSelector);

        $this->parameters = $parameters;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function render()
    {
        $subButtons = [];

        $firstItemExcluded = false;

        foreach ($this->get_type_selector()->getAllTypeSelectorOptions() as $option)
        {
            // If multiple categories add category header?
            $subButtons[] = new SubButton(
                $option->get_label(), $option->get_image_path(IdentGlyph::SIZE_MINI),
                $this->getContentObjectTypeUrl($option->get_template_registration_id())
            );
        }

        return $subButtons;
    }

    /**
     *
     * @param integer $templateRegistrationIdentifier
     *
     * @return string
     */
    public function getContentObjectTypeUrl($templateRegistrationIdentifier)
    {
        $objectTypeParameters = $this->getParameters();
        $objectTypeParameters[TypeSelector::PARAM_SELECTION] = $templateRegistrationIdentifier;

        $url = new Redirect($objectTypeParameters);

        return $url->getUrl();
    }

    /**
     *
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param string[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
