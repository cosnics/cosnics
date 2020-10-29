<?php
namespace Chamilo\Core\Metadata\Schema\Action;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

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

    public function run()
    {
        $schema = $this->installSchema();

        if (!$schema instanceof Schema)
        {
            return false;
        }

        if (!$this->installElements($schema))
        {
            return false;
        }

        return true;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
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

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * Installs the schema elements
     *
     * @param Schema $schema
     *
     * @return boolean
     */
    protected function installElements(Schema $schema)
    {
        $elementService = new ElementService();
        $schemaDefinition = $this->getSchemaDefinition();

        $elementDefinitions = $schemaDefinition[Element::class];

        foreach ($elementDefinitions as $elementName => $elementProperties)
        {
            $element = $elementService->getElementBySchemaIdAndName($schema->get_id(), $elementName);

            if (!$element)
            {
                $element = new Element();
                $element->set_name($elementName);
                $element->set_schema_id($schema->get_id());
            }

            $element->set_display_name(
                Translation::get((string) StringUtilities::getInstance()->createString($elementName)->upperCamelize())
            );
            $element->set_fixed($elementProperties[Element::PROPERTY_FIXED]);
            $element->set_value_type($elementProperties[Element::PROPERTY_VALUE_TYPE]);
            $element->set_value_limit($elementProperties[Element::PROPERTY_VALUE_LIMIT]);

            $succes = $element->is_identified() ? $element->update() : $element->create();
            $this->getDataClassRepositoryCache()->reset();

            if (!$succes)
            {
                return false;
            }
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
        $this->getDataClassRepositoryCache()->reset();
        $schemaDefinition = $this->getSchemaDefinition();

        try
        {
            $schema = DataManager::retrieveSchemaByNamespace(
                $schemaDefinition[Schema::class][Schema::PROPERTY_NAMESPACE]
            );
        }
        catch (Exception $ex)
        {
            $schema = null;
        }

        if (!$schema)
        {
            $schema = new Schema();
            $schema->set_namespace($schemaDefinition[Schema::class][Schema::PROPERTY_NAMESPACE]);
        }

        $schema->set_name($schemaDefinition[Schema::class][Schema::PROPERTY_NAME]);
        $schema->set_description($schemaDefinition[Schema::class][Schema::PROPERTY_DESCRIPTION]);
        $schema->set_url($schemaDefinition[Schema::class][Schema::PROPERTY_URL]);
        $schema->set_fixed($schemaDefinition[Schema::class][Schema::PROPERTY_FIXED]);

        $succes = $schema->is_identified() ? $schema->update() : $schema->create();

        if (!$succes)
        {
            return false;
        }

        return $schema;
    }
}