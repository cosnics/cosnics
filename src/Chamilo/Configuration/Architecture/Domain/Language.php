<?php
namespace Chamilo\Configuration\Architecture\Domain;

/**
 * @package Chamilo\Configuration\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Language
{
    public const CODE_TYPE_GLOTTOLOG = 'glottolog';
    public const CODE_TYPE_ISO_639_1 = 'iso_639_1';
    public const CODE_TYPE_ISO_639_2 = 'iso_639_2';
    public const CODE_TYPE_ISO_639_3 = 'iso_639_3';
    public const CODE_TYPE_LINGUASPHERE = 'linguasphere';

    /**
     * @string[] array
     */
    private array $codes;

    /**
     * @string[][] array
     */
    private array $families;

    private string $name;

    /**
     * @string[] array
     */
    private array $translations;

    /**
     * @param string[] $codes
     * @param string[][] $families
     * @param string $name
     * @param string[] $translations
     */
    public function __construct(array $codes, array $families, string $name, array $translations)
    {
        $this->codes = $codes;
        $this->families = $families;
        $this->name = $name;
        $this->translations = $translations;
    }

    public function getCode(LanguageCodeEnum $codeType): ?string
    {
        return $this->codes[$codeType->value] ?? null;
    }

    /**
     * @return string[]
     */
    public function getCodes(): array
    {
        return $this->codes;
    }

    /**
     * @param string[] $codes
     */
    public function setCodes(array $codes): Language
    {
        $this->codes = $codes;

        return $this;
    }

    /**
     * @return string[][]
     */
    public function getFamilies(): array
    {
        return $this->families;
    }

    /**
     * @param string[][] $families
     */
    public function setFamilies(array $families): Language
    {
        $this->families = $families;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Language
    {
        $this->name = $name;

        return $this;
    }

    public function getTranslation(string $isoCode): ?string
    {
        return $this->translations[$isoCode] ?? null;
    }

    /**
     * @return string[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @param string[] $translations
     */
    public function setTranslations(array $translations): Language
    {
        $this->translations = $translations;

        return $this;
    }

}
