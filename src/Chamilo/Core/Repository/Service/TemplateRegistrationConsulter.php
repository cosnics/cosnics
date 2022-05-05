<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Configuration\Interfaces\DataLoaderInterface;
use Chamilo\Configuration\Service\DataConsulter;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationConsulter extends DataConsulter
{

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Configuration\Interfaces\DataLoaderInterface $dataLoader
     */
    public function __construct(StringUtilities $stringUtilities, DataLoaderInterface $dataLoader)
    {
        parent::__construct($dataLoader);
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @param int $identifier
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration
     */
    public function getTemplateRegistrationByIdentifier($identifier)
    {
        return $this->getTemplateRegistrations()[TemplateRegistrationLoader::REGISTRATION_ID][$identifier];
    }

    /**
     *
     * @param string $type
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration
     */
    public function getTemplateRegistrationDefaultByType($type)
    {
        return $this->getTemplateRegistrations()[TemplateRegistrationLoader::REGISTRATION_DEFAULT][$type];
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     */
    public function getTemplateRegistrations()
    {
        return $this->getData();
    }

    /**
     * Get the template registrations for a specific content object type and/or user_id
     *
     * @param string[] $types
     * @param int $user_id
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     */
    public function getTemplateRegistrationsByTypesAndUserIdentifier($types, $user_id = null)
    {
        $templateRegistrations = $this->getTemplateRegistrations();

        $filteredTemplateRegistrations = [];

        if (!is_array($types))
        {
            $types = array($types);
        }

        foreach ($types as $type)
        {
            $commonTemplateRegistrations = (array)
                $templateRegistrations[TemplateRegistrationLoader::REGISTRATION_USER_ID][0][$type];

            if (count($commonTemplateRegistrations) > 0)
            {
                $filteredTemplateRegistrations =
                    array_merge($filteredTemplateRegistrations, $commonTemplateRegistrations);
            }

            if ($user_id)
            {
                $userTemplateRegistrations = (array)
                    $templateRegistrations[TemplateRegistrationLoader::REGISTRATION_USER_ID][$user_id][$type];

                if (count($userTemplateRegistrations) > 0)
                {
                    $filteredTemplateRegistrations =
                        array_merge($filteredTemplateRegistrations, $userTemplateRegistrations);
                }
            }
        }

        return $filteredTemplateRegistrations;
    }
}
