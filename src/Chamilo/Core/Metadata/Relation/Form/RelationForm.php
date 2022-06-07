<?php
namespace Chamilo\Core\Metadata\Relation\Form;

use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Form for the element
 */
class RelationForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Relation
     */
    private $relation;

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Relation $relation
     * @param string $formUrl
     *
     * @throws \Exception
     */
    public function __construct(Relation $relation, $formUrl)
    {
        parent::__construct('relation', self::FORM_METHOD_POST, $formUrl);

        $this->relation = $relation;

        $this->buildForm();
        $this->setFormDefaults();
    }

    /**
     * Builds this form
     *
     * @throws \Exception
     */
    protected function buildForm()
    {
        $this->addElement('category', Translation::get('General'));

        $this->addElement(
            'text', Relation::PROPERTY_NAME, Translation::get('Name', null, StringUtilities::LIBRARIES)
        );
        $this->addRule(
            Relation::PROPERTY_NAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->getEntityTranslationFormService()->addFieldsToForm($this);
        $this->addSaveResetButtons();
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityTranslationFormService
     */
    protected function getEntityTranslationFormService()
    {
        return $this->getService(EntityTranslationFormService::class);
    }

    /**
     * @throws \Exception
     */
    protected function setFormDefaults()
    {
        $this->setDefaults(array(Relation::PROPERTY_NAME => $this->relation->get_name()));

        $this->getEntityTranslationFormService()->setFormDefaults($this, $this->relation);
    }
}