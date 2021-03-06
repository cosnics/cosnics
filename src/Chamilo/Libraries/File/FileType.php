<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\File
 */
class FileType
{
    // Filetype groups
    const TYPE_AUDIO = 1;
    const TYPE_VIDEO = 2;
    const TYPE_IMAGE = 3;
    const TYPE_PDF = 4;
    const TYPE_SPREADSHEET = 5;
    const TYPE_TEXT = 6;
    const TYPE_PRESENTATION = 7;
    const TYPE_DATABASE = 8;
    const TYPE_WEB = 9;
    const TYPE_ARCHIVE = 10;
    const TYPE_APPLICATION = 11;
    const TYPE_FLASH = 12;
    const TYPE_CODE = 13;

    // Map of all file extensions and their respective MIME-type
    /**
     *
     * @var string[]
     */
    private static $extensions = array(
        'aac' => 'audio/aac',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asc' => 'text/plain',
        'asf' => 'video/x-ms-asf',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'csv' => 'text/csv',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'djv' => 'image/vnd.djvu',
        'djvu' => 'image/vnd.djvu',
        'dll' => 'application/octet-stream',
        'dmg' => 'application/x-diskcopy',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dvi' => 'application/x-dvi',
        'dwg' => 'application/vnd.dwg',
        'dxf' => 'application/vnd.dxf',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'etx' => 'text/x-setext',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'gif' => 'image/gif',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'hdf' => 'application/x-hdf',
        'hqx' => 'application/mac-binhex40',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ice' => 'x-conference-xcooltalk',
        'ief' => 'image/ief',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'jar' => 'application/java-archiver',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'kar' => 'audio/midi',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'log' => 'text/plain',
        'lzh' => 'application/octet-stream',
        'm1a' => 'audio/mpeg',
        'm2a' => 'audio/mpeg',
        'm3u' => 'audio/x-mpegurl',
        'm4a' => 'audio/x-m4a',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'mesh' => 'model/mesh',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mkv' => 'video/x-matroska',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'mpa' => 'audio/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'ms' => 'application/x-troff-ms',
        'msh' => 'model/mesh',
        'mxu' => 'video/vnd.mpegurl',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'ogg' => 'audio/ogg',
        'ogv' => 'video/ogg',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oxt' => 'application/vnd.openofficeorg.extension',
        'pbm' => 'image/x-portable-bitmap',
        'pct' => 'image/pict',
        'pdb' => 'chemical/x-pdb',
        'pdf' => 'application/pdf',
        'pgm' => 'image/x-portable-graymap',
        'pgn' => 'application/x-chess-pgn',
        'pict' => 'image/pict',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ps' => 'application/postscript',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rar' => 'image/x-rar-compressed',
        'ras' => 'image/x-cmu-raster',
        'rdf' => 'application/rdf+xml',
        'rgb' => 'image/x-rgb',
        'rm' => 'audio/x-pn-realaudio',
        'roff' => 'application/x-troff',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'rtf' => 'application/rtf',
        'rtx' => 'text/richtext',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sib' => 'application/X-Sibelius-Score',
        'silo' => 'model/mesh',
        'sit' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'snd' => 'audio/basic',
        'so' => 'application/octet-stream',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svf' => 'application/vnd.svf',
        'swf' => 'application/x-shockwave-flash',
        'sxc' => 'application/vnd.sun.xml.calc',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sxw' => 'application/vnd.sun.xml.writer',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tga' => 'image/x-targa',
        'tif' => 'image/tif',
        'tiff' => 'image/tiff',
        'tr' => 'application/x-troff',
        'tsv' => 'text/tab-seperated-values',
        'ttml' => 'application/ttml+xml',
        'txt' => 'text/plain',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vrml' => 'model/vrml',
        'vtt' => 'text/vtt',
        'wav' => 'audio/x-wav',
        'wbmp' => 'image/vnd.wap.wbmp',
        'wbxml' => 'application/vnd.wap.wbxml',
        'webm' => 'video/webm',
        'wma' => 'video/x-ms-wma',
        'wml' => 'text/vnd.wap.wml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'wmv' => 'audio/x-ms-wmv',
        'wrl' => 'model/vrml',
        'xbm' => 'image/x-xbitmap',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xml' => 'text/xml',
        'xpm' => 'image/x-xpixmap',
        'xsl' => 'text/xml',
        'xwd' => 'image/x-windowdump',
        'xyz' => 'chemical/x-xyz',
        'zip' => 'application/zip');

    /**
     *
     * @var string[][]
     */
    private static $types = array(
        self::TYPE_AUDIO => array(
            'aac',
            'ac3',
            'aif',
            'aifc',
            'aiff',
            'ape',
            'au',
            'kar',
            'm1a',
            'm2a',
            'm3u',
            'm4a',
            'mid',
            'midi',
            'mp2',
            'mp3',
            'mpa',
            'mpga',
            'ogg',
            'ra',
            'ram',
            'rm',
            'rpm',
            'snd',
            'wav',
            'wbmp',
            'wma'),
        self::TYPE_VIDEO => array(
            '3gp',
            'asf',
            'avi',
            'flv',
            'mkv',
            'mov',
            'movie',
            'mp4',
            'mpe',
            'mpeg',
            'mpg',
            'mxu',
            'ogv',
            'qt',
            'vob',
            'webm',
            'wmv'),
        self::TYPE_IMAGE => array(
            'ai',
            'bmp',
            'djv',
            'djvu',
            'gif',
            'ief',
            'jpe',
            'jpeg',
            'jpg',
            'pbm',
            'pct',
            'pgm',
            'png',
            'pnm',
            'ppm',
            'ps',
            'psd',
            'psp',
            'pspimage',
            'ras',
            'rgb',
            'svg',
            'tga',
            'tif',
            'tiff',
            'vsd',
            'vst',
            'xbm',
            'xpm'),
        self::TYPE_PDF => array('pdf'),
        self::TYPE_SPREADSHEET => array('ods', 'xlr', 'xls', 'xlsb', 'xlsm', 'xlsx', 'xlt', 'xltx'),
        self::TYPE_TEXT => array('asc', 'doc', 'docx', 'etx', 'log', 'odt', 'rtf', 'rtx', 'txt', 'wpd', 'wps', 'wpt'),
        self::TYPE_PRESENTATION => array('odp', 'potx', 'pps', 'ppt', 'pptm', 'pptx'),
        self::TYPE_DATABASE => array('accdb', 'dccdt', 'mdb', 'odb'),
        self::TYPE_WEB => array(
            'css',
            'htm',
            'html',
            'wbxml',
            'wml',
            'wmlc',
            'wmls',
            'wmlsc',
            'xht',
            'xhtml',
            'xml',
            'xsl'),
        self::TYPE_ARCHIVE => array(
            '7z',
            'cab',
            'dmg',
            'gtar',
            'gz',
            'jar',
            'lha',
            'lzh',
            'rar',
            'tar',
            'tgz',
            'zip',
            'zipx'),
        self::TYPE_APPLICATION => array('app', 'bat', 'exe', 'msi', 'sh'),
        self::TYPE_FLASH => array('fla', 'flv', 'swc', 'swf', 'swt'),
        self::TYPE_CODE => array('java')
    );

    /**
     *
     * @return string[]
     */
    public static function get_types()
    {
        return array(
            self::TYPE_AUDIO => Translation::get('FileTypeAudio'),
            self::TYPE_VIDEO => Translation::get('FileTypeVideo'),
            self::TYPE_IMAGE => Translation::get('FileTypeImage'),
            self::TYPE_PDF => Translation::get('FileTypePdf'),
            self::TYPE_SPREADSHEET => Translation::get('FileTypeSpreadsheet'),
            self::TYPE_TEXT => Translation::get('FileTypeText'),
            self::TYPE_PRESENTATION => Translation::get('FileTypePresentation'),
            self::TYPE_DATABASE => Translation::get('FileTypeDatabase'),
            self::TYPE_WEB => Translation::get('FileTypeWeb'),
            self::TYPE_ARCHIVE => Translation::get('FileTypeArchive'),
            self::TYPE_APPLICATION => Translation::get('FileTypeApplication'),
            self::TYPE_FLASH => Translation::get('FileTypeFlash'),
            self::TYPE_CODE => Translation::get('FileTypeCode'));
    }

    /**
     *
     * @param integer $type
     * @return string
     */
    public static function get_type_string($type)
    {
        switch ($type)
        {
            case self::TYPE_AUDIO :
                return Translation::get('FileTypeAudio');
                break;
            case self::TYPE_VIDEO :
                return Translation::get('FileTypeVideo');
                break;
            case self::TYPE_IMAGE :
                return Translation::get('FileTypeImage');
                break;
            case self::TYPE_PDF :
                return Translation::get('FileTypePdf');
                break;
            case self::TYPE_SPREADSHEET :
                return Translation::get('FileTypeSpreadsheet');
                break;
            case self::TYPE_TEXT :
                return Translation::get('FileTypeText');
                break;
            case self::TYPE_PRESENTATION :
                return Translation::get('FileTypePresentation');
                break;
            case self::TYPE_DATABASE :
                return Translation::get('FileTypeDatabase');
                break;
            case self::TYPE_WEB :
                return Translation::get('FileTypeWeb');
                break;
            case self::TYPE_ARCHIVE :
                return Translation::get('FileTypeArchive');
                break;
            case self::TYPE_APPLICATION :
                return Translation::get('FileTypeApplication');
                break;
            case self::TYPE_FLASH :
                return Translation::get('FileTypeFlash');
                break;
            case self::TYPE_CODE :
                return Translation::get('FileTypeCode');
                break;
        }
    }

    /**
     *
     * @param integer $type
     * @return string[]
     */
    public static function get_type_extensions($type)
    {
        return self::$types[$type];
    }

    /**
     *
     * @param string $extension
     * @param string $type
     * @return boolean
     */
    public static function is_extension_from_type($extension, $type)
    {
        return in_array($extension, self::$types[$type]);
    }

    /**
     *
     * @param string $mimetype
     * @param integer $type
     */
    public static function is_mimetype_from_type($mimetype, $type)
    {
        $extensions = self::get_extensions($mimetype);

        foreach ($extensions as $extension)
        {
            if (self::is_extension_from_type($extension, $type))
            {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param string $extension
     * @return string[]
     */
    public static function determine_types_from_extension($extension)
    {
        $types = array();

        foreach (self::$types as $type => $extensions)
        {
            if (in_array($extension, $extensions))
            {
                $types[] = $type;
            }
        }

        return $types;
    }

    /**
     *
     * @param string $mimetype
     * @return string[]
     */
    public static function determine_type_from_mimetype($mimetype)
    {
        $extensions = self::get_extensions($mimetype);

        $types = array();

        foreach ($extensions as $extension)
        {
            $types = array_merge($types, self::determine_types_from_extension($extension));
        }

        return array_unique($types);
    }

    /**
     *
     * @param string $extension
     * @return string
     */
    public static function get_mimetype($extension)
    {
        if (isset(self::$extensions[$extension]))
        {
            return self::$extensions[$extension];
        }
        else
        {
            return 'application/octet-stream';
        }
    }

    /**
     *
     * @param string $mimetype
     * @return string[]
     */
    public static function get_extensions($mimetype)
    {
        return array_keys(self::$extensions, $mimetype);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_audio($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_AUDIO);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_video($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_VIDEO);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_image($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_IMAGE);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_pdf($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_PDF);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_flash($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_FLASH);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_spreadsheet($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_SPREADSHEET);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_text($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_TEXT);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_presentation($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_PRESENTATION);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_database($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_DATABASE);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_web($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_WEB);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_archive($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_ARCHIVE);
    }

    /**
     *
     * @param string $extension
     * @return boolean
     */
    public static function is_code($extension)
    {
        return self::is_extension_from_type($extension, self::TYPE_CODE);
    }
}