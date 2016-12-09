<?php
namespace Chamilo\Core\Metadata\Schema\Action;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Utilities\StringUtilities;

class Installer
{

    /**
     *
     * @var string[]
     */
    private $schemaDefinition;

    /**
     *
     * @param string[] $schemaDefinition
     */
    public function __construct($schemaDefinition)
    {
        $this->schemaDefinition = $schemaDefinition;
    }

    /**
     *
     * @return string[]
     */
    public function getSchemaDefinition()
    {
        return $this->schemaDefinition;
    }

    /**
     *
     * @param string[] $schemaDefinition
     */
    public function setSchemaDefinition($schemaDefinition)
    {
        $this->schemaDefinition = $schemaDefinition;
    }

    public function run()
    {
        $schema = $this->installSchema();

        if (! $schema instanceof Schema)
        {
            return false;
        }

        if (! $this->installElements($schema))
        {
            return false;
        }

        return true;
    }

    /**
     * Installs the schema
     *
     * @return bool
     */
    protected function installSchema()
    {
        DataClassCache :: reset();
        $schemaDefinition = $this->getSchemaDefinition();

        try
        {
            $schema = \Chamilo\Core\Metadata\Schema\Storage\DataManager :: retrieveSchemaByNamespace(
                $schemaDefinition[Schema :: class_name()][Schema :: PROPERTY_NAMESPACE]);
        }
        catch (\Exception $ex)
        {
            $schema = null;
        }

        if (! $schema)
        {
            $schema = new Schema();
            $schema->set_namespace($schemaDefinition[Schema :: class_name()][Schema :: PROPERTY_NAMESPACE]);
        }

        $schema->set_name($schemaDefinition[Schema :: class_name()][Schema :: PROPERTY_NAME]);
        $schema->set_description($schemaDefinition[Schema :: class_name()][Schema :: PROPERTY_DESCRIPTION]);
        $schema->set_url($schemaDefinition[Schema :: class_name()][Schema :: PROPERTY_URL]);
        $schema->set_fixed($schemaDefinition[Schema :: class_name()][Schema :: PROPERTY_FIXED]);

        $succes = $schema->is_identified() ? $schema->update() : $schema->create();

        if (! $succes)
        {
            return false;
        }

        return $schema;
    }

    /**
     * Installs the schema elements
     *
     * @param Schema $schema
     * @return boolean
     */
    protected function installElements(Schema $schema)
    {
        $elementService = new ElementService();
        $schemaDefinition = $this->getSchemaDefinition();

        $elementDefinitions = $schemaDefinition[Element :: class_name()];

        foreach ($elementDefinitions as $elementName => $elementProperties)
        {
            $element = $elementService->getElementBySchemaIdAndName($schema->get_id(), $elementName);

            if (! $element)
            {
                $element = new Element();
                $element->set_name($elementName);
                $element->set_schema_id($schema->get_id());
            }

            $element->set_display_name(
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($elementName)->upperCamelize()));
            $element->set_fixed($elementProperties[Element :: PROPERTY_FIXED]);
            $element->set_value_type($elementProperties[Element :: PROPERTY_VALUE_TYPE]);
            $element->set_value_limit($elementProperties[Element :: PROPERTY_VALUE_LIMIT]);

            $succes = $element->is_identified() ? $element->update() : $element->create();
            DataClassCache :: reset();

            if (! $succes)
            {
                return false;
            }
        }

        return true;
    }
}