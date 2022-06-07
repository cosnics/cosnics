<?php
namespace Chamilo\Core\Metadata\Schema\Form;

use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Form for the schema
 *
 * @package Chamilo\Core\Metadata\Schema\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    private $schema;

    /**
     * Constructor
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param string $formUrl
     *
     * @throws \Exception
     */
    public function __construct(Schema $schema, $formUrl)
    {
        parent::__construct('schema', self::FORM_METHOD_POST, $formUrl);

        $this->schema = $schema;

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
        $this->addElement('text', Schema::PROPERTY_NAMESPACE, Translation::get('Namespace'), array("size" => "50"));

        $this->addRule(
            Schema::PROPERTY_NAMESPACE, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->addElement('text', Schema::PROPERTY_NAME, Translation::get('Name'), array("size" => "50"));

        $this->addRule(
            Schema::PROPERTY_NAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->addElement('text', Schema::PROPERTY_URL, Translation::get('Url'), array("size" => "50"));

        $this->addRule(
            Schema::PROPERTY_URL, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
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
     * Sets the default values
     * @throws \Exception
     */
    protected function setFormDefaults()
    {
        $defaults = [];

        $defaults[Schema::PROPERTY_NAMESPACE] = $this->schema->get_namespace();
        $defaults[Schema::PROPERTY_NAME] = $this->schema->get_name();
        $defaults[Schema::PROPERTY_URL] = $this->schema->get_url();

        $this->setDefaults($defaults);

        $this->getEntityTranslationFormService()->setFormDefaults($this, $this->schema);
    }
}