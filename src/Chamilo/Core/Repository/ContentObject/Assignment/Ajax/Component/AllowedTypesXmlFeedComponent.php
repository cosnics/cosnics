<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Ajax\Component;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;

/**
 *
 * @package Chamilo\Core\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AllowedTypesXmlFeedComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    function run()
    {
        $allowedTypes = $this->getAllowedContentObjectTypes();
        $search = $this->getRequest()->getFromPost(self::PARAM_SEARCH_QUERY);

        $elements = new AdvancedElementFinderElements();

        foreach ($allowedTypes as $allowedType)
        {
            $allowedTypeTranslation = $allowedType['translation'];

            if (!empty($search) && strpos(strtolower($allowedTypeTranslation), strtolower($search)) === false)
            {
                continue;
            }

            $typeClass = strtolower(
                $this->getClassnameUtilities()->getPackageNameFromNamespace(
                    $this->getClassnameUtilities()->getNamespaceParent($allowedType['type'])
                )
            );

            $elements->add_element(
                new AdvancedElementFinderElement(
                    $allowedType['id'],
                    'type type_' . $typeClass,
                    $allowedTypeTranslation,
                    $allowedTypeTranslation
                )
            );
        }

        $result = new JsonAjaxResult();
        $result->set_property(self::PROPERTY_ELEMENTS, $elements->as_array());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, count($elements->as_array()));

        $result->display();
    }

    /**
     * @return string[][]
     */
    protected function getAllowedContentObjectTypes()
    {
        $registrationConsulter = $this->getRegistrationConsulter();
        $translator = $this->getTranslator();

        $integrationPackages = $registrationConsulter->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Assignment'
        );

        $types = array();

        foreach ($integrationPackages as $basePackage => $integrationPackageData)
        {
            if ($integrationPackageData['status'] != Registration::STATUS_ACTIVE)
            {
                continue;
            }

            $integrationPackageData['translation'] = $translator->trans('TypeName', array(), $basePackage);
            $types[] = $integrationPackageData;
        }

        usort(
            $types, function ($typeA, $typeB) {
            return strcmp($typeA['translation'], $typeB['translation']);
        }
        );

        return $types;
    }

}