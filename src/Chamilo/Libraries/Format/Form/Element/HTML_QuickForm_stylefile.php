<?php
namespace Chamilo\Libraries\Format\Form\Element;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_QuickForm_file;

/**
 * @package Chamilo\Libraries\Format\Form\Element
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_stylefile extends HTML_QuickForm_file
{

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function toHtml(): string
    {
        if ($this->_flagFrozen)
        {
            return $this->getFrozenHtml();
        }
        else
        {
            $glyph = new FontAwesomeGlyph('upload', [], null, 'fas');

            $html = [];

            $html[] = $this->_getTabs();

            $html[] = '<div class="input-group">';

            $html[] = '<span class="input-group-btn">';
            $html[] = '<label class="btn btn-default">';
            $html[] = $glyph->render();
            $html[] = ' ';
            $html[] = Translation::getInstance()->getTranslation(
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
