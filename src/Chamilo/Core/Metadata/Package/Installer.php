<?php
namespace Chamilo\Core\Metadata\Package;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;

/**
 * @package Chamilo\Core\Metadata\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @throws \Exception
     */
    public function extra(array $formValues): bool
    {
        $translator = $this->getTranslator();

        if (!$this->installDefaultSchemas())
        {
            return false;
        }

        $relation = new Relation();
        $relation->set_name('isAvailableFor');

        if ($relation->create())
        {
            $entityTranslations = [];
            $entityTranslations[$translator->getLocale()] = $translator->trans('IsAvailableFor', [], Manager::CONTEXT);

            $entity = DataClassEntityFactory::getInstance()->getEntityFromDataClass($relation);

            $entityTranslationService = new EntityTranslationService();
            if (!$entityTranslationService->createEntityTranslations($entity, $entityTranslations))
            {
                return false;
            }
        }

        return true;
    }

    public function installDefaultSchemas(): bool
    {
        $schemaDefinition = [];
        $schemaDefinition[Schema::class] = [
            Schema::PROPERTY_NAMESPACE => 'dc',
            Schema::PROPERTY_NAME => 'Dublin Core',
            Schema::PROPERTY_DESCRIPTION => '',
            Schema::PROPERTY_URL => 'http://purl.org/dc/elements/1.1/',
            Schema::PROPERTY_FIXED => '1'
        ];

        $schemaDefinition[Element::class] = [
            'contributor' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'coverage' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'creator' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'date' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'description' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_FREE,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'format' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'identifier' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'language' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_PREDEFINED,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'publisher' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'relation' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'rights' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'source' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'subject' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ],
            'title' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_FREE,
                Element::PROPERTY_VALUE_LIMIT => 1,
                Element::PROPERTY_FIXED => 1
            ],
            'type' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ]
        ];

        $schemaInstaller = new \Chamilo\Core\Metadata\Schema\Action\Installer($schemaDefinition);
        if (!$schemaInstaller->run())
        {
            return false;
        }

        $schemaDefinition = [];
        $schemaDefinition[Schema::class] = [
            Schema::PROPERTY_NAMESPACE => 'ct',
            Schema::PROPERTY_NAME => 'Tags',
            Schema::PROPERTY_DESCRIPTION => '',
            Schema::PROPERTY_URL => 'http://www.chamilo.org/',
            Schema::PROPERTY_FIXED => '1'
        ];

        $schemaDefinition[Element::class] = [
            'tags' => [
                Element::PROPERTY_VALUE_TYPE => Element::VALUE_TYPE_VOCABULARY_USER,
                Element::PROPERTY_VALUE_LIMIT => 0,
                Element::PROPERTY_FIXED => 1
            ]
        ];

        $schemaInstaller = new \Chamilo\Core\Metadata\Schema\Action\Installer($schemaDefinition);

        if (!$schemaInstaller->run())
        {
            return false;
        }

        return true;
    }
}