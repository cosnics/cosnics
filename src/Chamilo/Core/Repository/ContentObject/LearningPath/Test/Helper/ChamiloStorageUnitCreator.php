<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper;

use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ChamiloStorageUnitCreator
{
    /**
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     * @var StorageUnitRepository
     */
    protected $storageUnitRepository;

    /**
     * ChamiloStorageUnitCreator constructor.
     *
     * @param PathBuilder $pathBuilder
     * @param StorageUnitRepository $storageUnitRepository
     */
    public function __construct(PathBuilder $pathBuilder, StorageUnitRepository $storageUnitRepository)
    {
        $this->pathBuilder = $pathBuilder;
        $this->storageUnitRepository = $storageUnitRepository;
    }

    /**
     * Creates all the defined storage units for a given context identified by his namespace
     *
     * @param string $contextNamespace
     * @param string[] $storageUnitNames
     */
    public function createStorageUnitsForContext($contextNamespace, $storageUnitNames = array())
    {
        $storagePath = $this->pathBuilder->namespaceToFullPath($contextNamespace) . 'Resources/Storage';
        if (!is_dir($storagePath) || !file_exists($storagePath))
        {
            throw new \InvalidArgumentException(
                sprintf('The given context %s does not have a valid storage path', $contextNamespace)
            );
        }

        $finder = new Finder();

        /** @var SplFileInfo[] | Finder $files */
        $files = $finder->in($storagePath)
            ->files()
            ->name('*.xml');

        if(!empty($storageUnitNames))
        {
            foreach($storageUnitNames as $storageUnitName)
            {
                $files->name($storageUnitName . '.xml');
            }
        }

        foreach ($files as $file)
        {
            $this->createStorageUnitFromXMLFile($file);
        }
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used
     * as the PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database. mdb2.datatypes.php
     *
     * @param $file string The complete path to the XML-file from which the storage unit definition should be read.
     */
    public function createStorageUnitFromXMLFile($file)
    {
        $properties = array();
        $indexes = array();

        $doc = new \DOMDocument();
        $doc->load($file);

        $object = $doc->getElementsByTagname('object')->item(0);
        $name = $object->getAttribute('name');
        $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');

        $xmlProperties = $doc->getElementsByTagname('property');
        foreach ($xmlProperties as $index => $property)
        {
            /** @var \DOMElement $property */

            $propertyInfo = array();
            foreach ($attributes as $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $propertyInfo[$attribute] = $property->getAttribute($attribute);
                }
            }
            $properties[$property->getAttribute('name')] = $propertyInfo;
        }

        $xmlIndexes = $doc->getElementsByTagname('index');
        foreach ($xmlIndexes as $key => $index)
        {
            /** @var \DOMElement $index */

            $indexInfo = array();
            $indexInfo['type'] = $index->getAttribute('type');

            $indexProperties = $index->getElementsByTagname('indexproperty');
            foreach ($indexProperties as $subkey => $indexProperty)
            {
                /** @var \DOMElement $indexProperty */

                $indexInfo['fields'][$indexProperty->getAttribute('name')] = array(
                    'length' => $indexProperty->getAttribute('length')
                );
            }
            $indexes[$index->getAttribute('name')] = $indexInfo;
        }

        if (!$this->storageUnitRepository->create($name, $properties, $indexes))
        {
            throw new \RuntimeException('Could not create the storage unit with name ' . $name);
        }
    }

}