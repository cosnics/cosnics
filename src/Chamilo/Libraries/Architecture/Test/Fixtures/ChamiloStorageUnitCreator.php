<?php
namespace Chamilo\Libraries\Architecture\Test\Fixtures;

use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ChamiloStorageUnitCreator
{

    /**
     * @var StorageUnitRepository
     */
    protected $storageUnitRepository;

    protected SystemPathBuilder $systemPathBuilder;

    /**
     * ChamiloStorageUnitCreator constructor.
     *
     * @param StorageUnitRepository $storageUnitRepository
     */
    public function __construct(SystemPathBuilder $systemPathBuilder, StorageUnitRepository $storageUnitRepository)
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->storageUnitRepository = $storageUnitRepository;
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used
     * as the PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database. mdb2.datatypes.php
     *
     * @param $file string The complete path to the XML-file from which the storage unit definition should be read.
     */
    public function createStorageUnitFromXMLFile(string $file)
    {
        $properties = [];
        $indexes = [];

        $doc = new DOMDocument();
        $doc->load($file);

        $object = $doc->getElementsByTagName('object')->item(0);

        if (!$object instanceof DOMElement)
        {
            throw new RuntimeException($file . 'does not contain a valid storage unit description');
        }

        $name = $object->getAttribute('name');
        $attributes = ['type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed'];

        $xmlPropertiesElement = $doc->getElementsByTagName('properties')->item(0);

        if (!$xmlPropertiesElement instanceof DOMElement)
        {
            throw new RuntimeException($file . 'does not contain a valid storage unit description');
        }

        $xmlProperties = $xmlPropertiesElement->getElementsByTagName('property');

        if (count($xmlProperties) == 0)
        {
            throw new RuntimeException($file . 'does not contain a valid storage unit description');
        }

        foreach ($xmlProperties as $property)
        {
            $propertyInfo = [];

            foreach ($attributes as $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $propertyInfo[$attribute] = $property->getAttribute($attribute);
                }
            }

            $properties[$property->getAttribute('name')] = $propertyInfo;
        }

        $xmlIndexesElement = $doc->getElementsByTagName('indexes')->item(0);

        if ($xmlIndexesElement instanceof DOMElement)
        {
            $xmlIndexes = $xmlIndexesElement->getElementsByTagName('index');

            foreach ($xmlIndexes as $index)
            {
                $indexInfo = [];
                $indexInfo['type'] = $index->getAttribute('type');

                $indexProperties = $index->getElementsByTagName('property');

                foreach ($indexProperties as $indexProperty)
                {
                    $indexInfo['fields'][$indexProperty->getAttribute('name')] = [
                        'length' => $indexProperty->getAttribute('length')
                    ];
                }

                $indexes[$index->getAttribute('name')] = $indexInfo;
            }
        }

        if (!$this->storageUnitRepository->create($name, $properties, $indexes))
        {
            throw new RuntimeException('Could not create the storage unit with name ' . $name);
        }
    }

    /**
     * Creates all the defined storage units for a given context identified by his namespace
     *
     * @param string $contextNamespace
     * @param string[] $storageUnitNames
     */
    public function createStorageUnitsForContext($contextNamespace, $storageUnitNames = [])
    {
        $storagePath = $this->systemPathBuilder->namespaceToFullPath($contextNamespace) . 'Resources/Storage';
        if (!is_dir($storagePath) || !file_exists($storagePath))
        {
            throw new InvalidArgumentException(
                sprintf('The given context %s does not have a valid storage path', $contextNamespace)
            );
        }

        $finder = new Finder();

        /** @var SplFileInfo[] | Finder $files */
        $files = $finder->in($storagePath)->files();

        if (!empty($storageUnitNames))
        {
            foreach ($storageUnitNames as $storageUnitName)
            {
                $files->name($storageUnitName . '.xml');
            }
        }
        else
        {
            $files->name('*.xml');
        }

        foreach ($files as $file)
        {
            $this->createStorageUnitFromXMLFile($file->getPathname());
        }
    }

}