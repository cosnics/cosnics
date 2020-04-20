<?php

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HTML_QuickForm_stylefile extends HTML_QuickForm_file
{
    /**
     * @return string
     */
    function toHtml()
    {
        if ($this->_flagFrozen)
        {
            return $this->getFrozenHtml();
        }
        else
        {
            $glyph = new FontAwesomeGlyph('upload', array(), null, 'fas');

            $html = array();

            $html[] = $this->_getTabs();

            $html[] = '<div class="input-group">';

            $html[] = '<span class="input-group-btn">';
            $html[] = '<label class="btn btn-default">';
            $html[] = $glyph->render();
            $html[] = ' ';
            $html[] = Chamilo\Libraries\Translation\Translation::getInstance()->getTranslation(
                'ChooseFileInputLabel', array(), Utilities::COMMON_LIBRARIES
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
