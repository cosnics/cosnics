<?php
namespace Chamilo\Core\Metadata\Interfaces;

/**
 *
 * @package Chamilo\Core\Metadata\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface EntityTranslationInterface
{

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation[]
     */
    public function getTranslations();

    /**
     *
     * @param string $isocode
     * @return string
     */
    public function getTranslationByIsocode($isocode);

    /**
     *
     * @return string
     */
    public function getTranslationFallback();
}