<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_file;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_stylefile extends HTML_QuickForm_file
{
    use DependencyInjectionContainerTrait;

    public function toHtml(): string
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }
        else {
            $glyph = new FontAwesomeGlyph('upload', [], null, 'fas');

            $html = [];

            $html[] = $this->_getTabs();

            $html[] = '<div class="input-group">';

            $html[] = '<span class="input-group-btn">';
            $html[] = '<label class="btn btn-default">';
            $html[] = $glyph->render();
            $html[] = ' ';
            $html[] = $this->getTranslator()->trans(
                'ChooseFileInputLabel', [], StringUtilities::LIBRARIES
            );
            $html[] = ' ';

            $this->setAttribute('style', 'display: none !important;');
            $this->setAttribute('onchange', '$(\'#' . $this->getName() . '-info\').val(this.files[0].name)');

            $html[] = '<input' . $this->_getAttrString($this->_attributes) . ' />';

            $html[] = '</label>';
            $html[] = '</span>';

            $html[] = '<input type="text" id="' . $this->getName() . '-info" class="form-control" disabled />';

            $html[] = '</div>';

            return implode('', $html);
        }
    }
}
