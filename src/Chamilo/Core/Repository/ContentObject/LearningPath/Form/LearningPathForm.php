<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Form;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: learning_path_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.learning_path
 */
class LearningPathForm extends ContentObjectForm
{
    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject|null
     */
    public function create_content_object()
    {
        $object = new LearningPath();
        $this->set_content_object($object);

        $this->setLearningPathProperties($object);

        return parent::create_content_object();
    }

    /**
     * @return bool
     */
    public function update_content_object()
    {
        $object = $this->get_content_object();

        $this->setLearningPathProperties($object);

        return parent::update_content_object();
    }

    public function setDefaults($defaults = array())
    {
        /** @var LearningPath $object */
        $object = $this->get_content_object();
        if ($object->is_identified())
        {
            $defaults[LearningPath::PROPERTY_AUTOMATIC_NUMBERING] = $object->getAutomaticNumbering();

            $defaults[LearningPath::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER] =
                $object->enforcesDefaultTraversingOrder();
        }

        parent::setDefaults($defaults);
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     */
    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($this->getHtmlEditorOptions(), $in_tab);
        $this->buildLearningPathForm();
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     */
    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($this->getHtmlEditorOptions(), $in_tab);
        $this->buildLearningPathForm();
    }

    /**
     * Builds the form for the additional learning path properties
     */
    protected function buildLearningPathForm()
    {
        $translator = Translation::getInstance();

        $this->addElement('category', $translator->getTranslation('Properties'));

        $this->addElement(
            'checkbox', LearningPath::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER,
            $translator->getTranslation('EnforceDefaultTraversingOrder')
        );

        $translatedOptions = array();

        $options = LearningPath::getAutomaticNumberingOptions();
        foreach ($options as $option)
        {
            $translatedOptions[$option] = $translator->getTranslation('AutomaticNumberingOption' . ucfirst($option));
        }

        $this->addElement(
            'select', LearningPath::PROPERTY_AUTOMATIC_NUMBERING, $translator->getTranslation('AutomaticNumbering'),
            $translatedOptions
        );
    }

    /**
     * Sets the specific properties for the given learning path
     *
     * @param LearningPath $object
     */
    protected function setLearningPathProperties(LearningPath $object)
    {
        $object->setAutomaticNumbering($this->exportValue(LearningPath::PROPERTY_AUTOMATIC_NUMBERING));

        $object->setEnforceDefaultTraversingOrder(
            (bool) $this->exportValue(LearningPath::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER)
        );
    }

    /**
     * @return string[]
     */
    protected function getHtmlEditorOptions()
    {
        $htmleditor_options = array();

        $htmleditor_options[FormValidatorHtmlEditorOptions::OPTION_HEIGHT] = '500';

        return $htmleditor_options;
    }
}
