<?php
namespace Chamilo\Libraries\Service\Utilities;

use Stringy\Stringy;

/**
 * @package Chamilo\Libraries\Service\Utilities
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class StringUtilities
{
    public const LIBRARIES = 'Chamilo\Libraries';

    private string $encoding;

    public function __construct(string $encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
    }

    /**
     * Create a \Stringy\Stringy instance with the given string and return it
     *
     * @param string $string
     *
     * @return \Stringy\Stringy
     */
    public function createString(string $string): Stringy
    {
        return Stringy::create($string, $this->getEncoding());
    }

    public function encryptMailLink(string $email, ?string $clickableText = null): string
    {
        if (is_null($clickableText)) {
            $clickableText = $email;
        }
        // mailto already present?
        if (!str_starts_with($email, 'mailto:')) {
            $email = 'mailto:' . $email;
        }

        // encrypt email
        $hmail = '';

        for ($i = 0; $i < strlen($email); $i ++) {
            $hmail .= '&#' . ord($email[$i]) . ';';
        }

        // encrypt clickable text if @ is present
        $hclickableText = '';

        if (strpos($clickableText, '@')) {
            for ($i = 0; $i < strlen($clickableText); $i ++) {
                $hclickableText .= '&#' . ord($clickableText[$i]) . ';';
            }
        }
        else {
            $hclickableText = htmlspecialchars($clickableText);
        }

        // return encrypted mailto hyperlink
        return '<a href="' . $hmail . '">' . $hclickableText . '</a>';
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function hasValue(mixed $string, bool $forHumans = false): bool
    {
        return !$this->isNullOrEmpty($string, $forHumans);
    }

    public function highlight(string $haystack, string $needle): string
    {
        if (strlen($haystack) < 1 || strlen($needle) < 1) {
            return $haystack;
        }

        $matches = [];
        $matchesDone = [];

        preg_match_all("/$needle+/i", $haystack, $matches);

        if (is_array($matches[0]) && count($matches[0]) >= 1) {
            foreach ($matches[0] as $match) {
                if (in_array($match, $matchesDone)) {
                    continue;
                }

                $matchesDone[] = $match;
                $haystack = str_replace($match, '<mark>' . $match . '</mark>', $haystack);
            }
        }

        return $haystack;
    }

    public function isNullOrEmpty(mixed $string, bool $forHumans = false): bool
    {
        if (is_null($string)) {
            return true;
        }

        if (!is_string($string)) {
            return false;
        }

        if ($forHumans) {
            $string = trim(str_replace('&nbsp;', '', strip_tags($string)));
        }

        if (strlen($string) === 0) {
            return true;
        }

        return false;
    }

    public function isValidPath(string $path): bool
    {
        $path = str_replace(' ', '', $path);
        $path = preg_replace('/[^a-zA-Z0-9\s]/', '', $path);

        $filteredPath = $this->createString($path)->underscored()->toString();

        if (!$path || !$filteredPath) {
            return false;
        }

        return true;
    }

    public function truncate(
        string $string, int $length = 200, bool $stripTags = true, string $character = "\xE2\x80\xA6"
    ): string
    {
        if ($stripTags) {
            $string = strip_tags($string);
        }

        return (string) $this->createString($string)->truncate($length, $character);
    }
}
