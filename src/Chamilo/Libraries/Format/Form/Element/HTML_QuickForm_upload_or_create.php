<?php
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
/**
 * Form element to upload or create a document This element contains 2 radio- buttons.
 * One with label 'upload document'
 * and one with label 'create document'. Only if the second radio-button is selected, a HTML-editor appears to allow the
 * user to create a HTML document
 *
 * @package Chamilo\Libraries\Format\Form\Element
 */
class HTML_QuickForm_upload_or_create extends HTML_QuickForm_group
{
    const ELEMENT_CHOICE = 'choice';
    const ELEMENT_FILE = 'file';
    const ELEMENT_EDITOR = 'html_content';
    const ELEMENT_UNCOMPRESS = 'uncompress';

    /**
     * Constructor
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string[] $attributes This should contain the keys 'receivers' and 'receivers_selected'
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null)
    {
        HTML_QuickForm_element::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->_type = 'upload_or_create';
    }

    /**
     *
     * @see HTML_QuickForm_group::_createElements()
     */
    public function _createElements()
    {
        $this->_elements[0] = new HTML_QuickForm_Radio(
            self::ELEMENT_CHOICE,
            '',
            Translation::get('Upload', null, Utilities::COMMON_LIBRARIES),
            '0',
            array(
                'onclick' => 'javascript:editor_hide(\'editor_html_content\'); javascript:uncompress_show(\'' .
                     self::ELEMENT_UNCOMPRESS . '\')'));
        $this->_elements[0]->setChecked(true);
        $this->_elements[1] = new HTML_QuickForm_file(self::ELEMENT_FILE, '');
        $this->_elements[2] = new HTML_QuickForm_Radio(
            self::ELEMENT_CHOICE,
            '',
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES),
            '1',
            array(
                'onclick' => 'javascript:editor_show(\'editor_html_content\'); javascript:editor_hide(\'' .
                     self::ELEMENT_UNCOMPRESS . '\')'));
        $this->_elements[3] = new HTML_QuickForm_textarea(self::ELEMENT_EDITOR, '');
        // $this->_elements[4] = new HTML_QuickForm_checkbox(self ::
        // ELEMENT_UNCOMPRESS, '', Translation :: get('Uncompress', null, Utilities
        // :: COMMON_LIBRARIES), array(
        // 'id' => self :: ELEMENT_UNCOMPRESS));
    }

    /**
     *
     * @see HTML_QuickForm_group::toHtml()
     */
    public function toHtml()
    {
        $html[] = $this->_elements[0]->toHtml();
        $html[] = '<div style="display: inline;" id="' . self::ELEMENT_UNCOMPRESS . '">';
        $html[] = $this->_elements[1]->toHtml();
        // $html[] = $this->_elements[4]->toHtml();
        $html[] = '</div>';
        $html[] = '<br />';
        $html[] = $this->_elements[2]->toHtml();
        $html[] = '<div style="margin-left:20px;display:block;" id="editor_html_content">';
        // $html[] = $this->_elements[3]->toHtml();

        $html_editor = new FormValidatorHtmlEditor(
            self::ELEMENT_EDITOR,
            '',
            false,
            array(FormValidatorHtmlEditorOptions::OPTION_HEIGHT => '500'));

        $html[] = $html_editor->render();
        $html[] = '</div>';
        $html[] = $this->getElementJS();
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getElementJS()
    {
        $js = "<script language=\"JavaScript\" type=\"text/javascript\">
					editor_hide('editor_html_content');
					function editor_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function uncompress_show(item) {
						el = document.getElementById(item);
						el.style.display='inline';
					}
					function editor_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					</script>
				";
        return $js;
    }

    /**
     *
     * @see HTML_QuickForm_group::accept()
     */
    public function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }
}
