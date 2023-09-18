<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Admin\Service\SettingsConnectorInterface;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Schema\Service\SchemaService;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Metadata
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SettingsConnector implements SettingsConnectorInterface
{

    protected SchemaService $schemaService;

    protected Translator $translator;

    public function __construct(Translator $translator, SchemaService $schemaService)
    {
        $this->translator = $translator;
        $this->schemaService = $schemaService;
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }

    public function getSchemaService(): SchemaService
    {
        return $this->schemaService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveNamespaces(): array
    {
        $translator = $this->getTranslator();
        $namespaces = $this->getSchemaService()->findSchemasForCondition();

        if ($namespaces->count())
        {
            $spaces[0] = $translator->trans('SelectNamespace', [], Manager::CONTEXT);

            foreach ($namespaces as $namespace)
            {
                $spaces[$namespace->getId()] = $namespace->get_name();
            }
        }
        else
        {
            $spaces[0] = $translator->trans('NoNamespaceDefined', [], Manager::CONTEXT);
        }

        return $spaces;
    }
}
