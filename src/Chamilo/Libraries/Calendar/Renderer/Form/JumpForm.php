<?php
namespace Chamilo\Libraries\Calendar\Renderer\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package libraries\calendar\renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class JumpForm extends FormValidator
{
    const JUMP_DAY = 'day';
    const JUMP_MONTH = 'month';
    const JUMP_YEAR = 'year';

    /**
     *
     * @var \HTML_QuickForm_Renderer
     */
    private $renderer;

    /**
     *
     * @var int
     */
    private $time;

    /**
     *
     * @param int $time
     * @param string $url
     */
    public function __construct($url, $time = null)
    {
        parent :: __construct('calendar_jump_form', 'post', $url);

        $this->renderer = $this->defaultRenderer();
        $this->time = is_null($time) ? intval($time) : $time;

        $this->buildForm();
        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function buildForm()
    {
        $this->renderer->setFormTemplate(
            '<form {attributes}><div class="jump_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{element}</div></div>');

        $this->addElement('static', null, null, Translation :: get('JumpTo', null, Utilities :: COMMON_LIBRARIES));
        $this->addElement('select', self :: JUMP_DAY, null, $this->getDays(), array('class' => 'postback'));
        $this->addElement('select', self :: JUMP_MONTH, null, $this->getMonths(), array('class' => 'postback'));
        $this->addElement('select', self :: JUMP_YEAR, null, $this->getYears(), array('class' => 'postback'));
        $this->addElement('style_submit_button', 'submit', Translation :: get('Jump'), array('class' => 'normal'));
        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Postback.js'));

        $this->setDefaults(
            array(
                self :: JUMP_DAY => date('j', $this->time),
                self :: JUMP_MONTH => date('n', $this->time),
                self :: JUMP_YEAR => date('Y', $this->time)));
    }

    /**
     * Display the form
     */
    public function render()
    {
        $html = array();
        $html[] = '<div class="content_object" style="margin-top:10px;padding:10px;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    /**
     *
     * @return int
     */
    public function getTime()
    {
        $values = $this->exportValues();
        return mktime(0, 0, 0, $values[self :: JUMP_MONTH], $values[self :: JUMP_DAY], $values[self :: JUMP_YEAR]);
    }

    /**
     *
     * @return int[]
     */
    public function getDays()
    {
        $numberDays = date('t', $this->time);
        $days = array();

        for ($i = 1; $i <= $numberDays; $i ++)
        {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     *
     * @return string[]
     */
    public function getMonths()
    {
        $monthNames = array(
            Translation :: get("JanuaryLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("FebruaryLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("MarchLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("AprilLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("MayLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("JuneLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("JulyLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("AugustLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("SeptemberLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("OctoberLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("NovemberLong", null, Utilities :: COMMON_LIBRARIES),
            Translation :: get("DecemberLong", null, Utilities :: COMMON_LIBRARIES));
        $months = array();

        foreach ($monthNames as $key => $month)
        {
            $months[$key + 1] = $month;
        }

        return $months;
    }

    /**
     *
     * @return int[]
     */
    public function getYears()
    {
        $year = date('Y', $this->time);
        $years = array();

        for ($i = $year - 5; $i <= $year + 5; $i ++)
        {
            $years[$i] = $i;
        }

        return $years;
    }
}
