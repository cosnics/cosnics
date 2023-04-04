<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Form;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.peer_assessment
 * @author Renaat De Muynck
 * @author Sven Vanpoucke
 */
/**
 * This class represents a form to create or update peer_assessments
 */
class PeerAssessmentForm extends ContentObjectForm
{

    public function setDefaults($defaults = array(), $filter = null)
    {
        $defaults[PeerAssessment::PROPERTY_SCALE] = $this->get_content_object()->get_scale();
        $defaults[PeerAssessment::PROPERTY_ASSESSMENT_TYPE] = $this->get_content_object()->get_assessment_type();

        parent::setDefaults($defaults);
    }

    private function build_default_form()
    {
        $locked = null;
        if (! is_null($this->get_content_object()->get_id()))
            $locked = array('disabled' => 'disabled');

        $this->addElement('category', Translation::get('Properties'));
        $this->addElement(
            'select',
            PeerAssessment::PROPERTY_ASSESSMENT_TYPE,
            Translation::get('Type'),
            $this->get_types(),
            $locked);
        $this->addElement(
            'select',
            PeerAssessment::PROPERTY_SCALE,
            Translation::get('Scale'),
            $this->get_scales(),
            $locked);
        $this->addElement('category');
    }

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
        $this->build_default_form();
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($htmleditor_options, $in_tab);
        $this->build_default_form();
    }

    private function get_types()
    {
        return array(
            PeerAssessment::TYPE_SCORES => Translation::get('TypeScores'),
            PeerAssessment::TYPE_FEEDBACK => Translation::get('TypeFeedback'),
            PeerAssessment::TYPE_BOTH => Translation::get('TypeBoth'));
    }

    private function get_scales()
    {
        $scales = array();
        foreach ($this->get_content_object()->get_scale_types() as $scale_type)
        {
            $scales[$scale_type] = Translation::get($scale_type);
        }
        return $scales;
    }

    /*
     * (non-PHPdoc) @see repository.ContentObjectForm::create_content_object()
     */
    public function create_content_object()
    {
        $object = new PeerAssessment();
        $values = $this->exportValues();

        $object->set_assessment_type($values[PeerAssessment::PROPERTY_ASSESSMENT_TYPE]);
        $object->set_scale($values[PeerAssessment::PROPERTY_SCALE]);

        $this->set_content_object($object);
        return parent::create_content_object();
    }

    /*
     * (non-PHPdoc) @see repository.ContentObjectForm::update_content_object()
     */
    public function update_content_object()
    {
        $object = $this->get_content_object();

        $values = $this->exportValues();

        $object->set_assessment_type($values[PeerAssessment::PROPERTY_ASSESSMENT_TYPE]);
        $object->set_scale($values[PeerAssessment::PROPERTY_SCALE]);

        $this->set_content_object($object);
        return parent::update_content_object();
    }
}
