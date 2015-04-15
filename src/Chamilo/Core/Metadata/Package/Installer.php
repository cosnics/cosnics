<?php
namespace Chamilo\Core\Metadata\Package;

use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;

/**
 *
 * @package Chamilo\Core\Metadata\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function extra()
    {
        if (! $this->installDefaultSchemas())
        {
            return false;
        }

        $relation = new \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation();
        $relation->set_name('isAvailableFor');
        if ($relation->create())
        {
            $entityTranslations = array();
            $entityTranslations[Translation :: get_language()] = Translation :: get('IsAvailableFor');

            $entityTranslationService = new EntityTranslationService($relation);
            if (! $entityTranslationService->createEntityTranslations($entityTranslations))
            {
                return false;
            }
        }

        return true;
    }

    public function installDefaultSchemas()
    {
        $schemaDefinition = array();
        $schemaDefinition[Schema :: class_name()] = array(
            Schema :: PROPERTY_NAMESPACE => 'dc',
            Schema :: PROPERTY_NAME => 'Dublin Core',
            Schema :: PROPERTY_DESCRIPTION => '',
            Schema :: PROPERTY_URL => 'http://purl.org/dc/elements/1.1/',
            Schema :: PROPERTY_FIXED => '1');

        $schemaDefinition[Element :: class_name()] = array(
            'contributor' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'coverage' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'creator' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'date' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'description' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_FREE,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'format' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'identifier' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'language' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_PREDEFINED,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'publisher' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'relation' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'rights' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'source' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'subject' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1),
            'title' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 1,
                Element :: PROPERTY_FIXED => 1),
            'type' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1));

        $schemaInstaller = new \Chamilo\Core\Metadata\Schema\Action\Installer($schemaDefinition);
        if (! $schemaInstaller->run())
        {
            return false;
        }

        $schemaDefinition = array();
        $schemaDefinition[Schema :: class_name()] = array(
            Schema :: PROPERTY_NAMESPACE => 'ct',
            Schema :: PROPERTY_NAME => 'Tags',
            Schema :: PROPERTY_DESCRIPTION => '',
            Schema :: PROPERTY_URL => 'http://www.chamilo.org/',
            Schema :: PROPERTY_FIXED => '1');

        $schemaDefinition[Element :: class_name()] = array(
            'tags' => array(
                Element :: PROPERTY_VALUE_TYPE => Element :: VALUE_TYPE_VOCABULARY_USER,
                Element :: PROPERTY_VALUE_LIMIT => 0,
                Element :: PROPERTY_FIXED => 1));

        $schemaInstaller = new \Chamilo\Core\Metadata\Schema\Action\Installer($schemaDefinition);
        if (! $schemaInstaller->run())
        {
            return false;
        }

        return true;
    }
}