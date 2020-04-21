<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use RuntimeException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusWebserviceRepositoryFactory
{
    const TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus';

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * EphorusWebserviceRepositoryFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\EphorusWebserviceRepository
     */
    public function createEphorusWebserviceRepository()
    {
        $handInWsdl = $this->configurationConsulter->getSetting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'handin_service_wsdl')
        );

        if (empty($handInWsdl))
        {
            throw new RuntimeException(
                $this->translator->trans('HandInWsdlNotConfigured', [], self::TRANSLATION_CONTEXT)
            );
        }

        $handInCode = $this->configurationConsulter->getSetting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'hand_in_code')
        );

        if (empty($handInWsdl))
        {
            throw new RuntimeException(
                $this->translator->trans('HandInCodeNotConfigured', [], self::TRANSLATION_CONTEXT)
            );
        }

        $indexWsdl = $this->configurationConsulter->getSetting(
            array('Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'index_document_service_wsdl')
        );

        if (empty($handInWsdl))
        {
            throw new RuntimeException(
                $this->translator->trans('IndexDocumentWsdlNotConfigured', [], self::TRANSLATION_CONTEXT)
            );
        }

        return new EphorusWebserviceRepository($handInWsdl, $handInCode, $indexWsdl);
    }

}