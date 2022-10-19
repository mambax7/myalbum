<?php declare(strict_types=1);

namespace XoopsModules\Myalbum;

if (!\class_exists('TextSanitizer')) {
    require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';

    /**
     * Class MyAlbumTextSanitizer
     */
    class TextSanitizer extends \MyTextSanitizer
    {
        public int $nbsp = 0;
        /*
        * MyAlbumTextSanitizer constructor.
        *
        * Gets allowed html tags from admin config settings
        * <br> should not be allowed since nl2br will be used
        * when storing data.
        *
        * @access   private
        *
        * @todo Sofar, this does nuttin' ;-)
        */

        /**
         * MyAlbumTextSanitizer constructor.
         */
        public function __construct()
        {
        }

        /**
         * Access the only instance of this class
         *
         * @return \XoopsModules\Myalbum\TextSanitizer
         */
        public static function getInstance(): TextSanitizer
        {
            static $instance;
            if (null === $instance) {
                $instance = new static();
            }

            return $instance;
        }

        /**
         * Filters textarea form data in DB for display
         *
         * @param string   $text
         * @param bool|int $html   allow html?
         * @param bool|int $smiley allow smileys?
         * @param bool|int $xcode  allow xoopscode?
         * @param bool|int $image  allow inline images?
         * @param bool|int $br     convert linebreaks?
         *
         * @param int      $nbsp
         *
         * @return string
         */
        public function &displayTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, int $nbsp = 0): string
        {
            $this->nbsp = $nbsp;
            $text       = parent::displayTarea($text, $html, $smiley, $xcode, $image, $br);
            $ret        = $this->postCodeDecode($text);
            return $ret;
            /*      if ($html != 1) {
                        // html not allowed
                        $text =& $this->htmlSpecialChars($text);
                    }
                    $text =& $this->makeClickable($text);
                    if ($smiley != 0) {
                        // process smiley
                        $text =& $this->smiley($text);
                    }
                    if ($xcode != 0) {
                        // decode xcode
                        if ($image != 0) {
                            // image allowed
                            $text =& $this->xoopsCodeDecode($text);
                                } else {
                                    // image not allowed
                                    $text =& $this->xoopsCodeDecode($text, 0);
                        }
                    }
                    if ($br != 0) {
                        $text =& $this->nl2Br($text);
                    }

                    return $text; */
        }

        /**
         * Replace some appendix codes with their equivalent HTML formatting
         *
         * @param string $text
         *
         * @return string
         **/
        public function postCodeDecode(string $text): string
        {
            $removal_tags = ['[summary]', '[/summary]', '[pagebreak]'];
            $text         = \str_replace($removal_tags, '', $text);

            $patterns     = [];
            $replacements = [];

            $patterns[] = "/\[siteimg align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";
            $patterns[] = "/\[siteimg]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";
            if ($image) {
                $replacements[] = '<img src="' . XOOPS_URL . '/\\3" align="\\2" alt="" >';

                $replacements[] = '<img src="' . XOOPS_URL . '/\\1" alt="" >';
            } else {
                $replacements[] = '<a href"' . XOOPS_URL . '/\\3" target="_blank">' . XOOPS_URL . '/\\3</a>';
                $replacements[] = '<a href"' . XOOPS_URL . '/\\1" target="_blank">' . XOOPS_URL . '/\\1</a>';
            }

            return \preg_replace($patterns, $replacements, $text);
        }

        /**
         * get inside of tags [summary] and [/summary]
         *
         * @param string $text
         *
         * @return string
         **/
        public function extractSummary(string $text): string
        {
            $patterns[]     = '/^(.*)\[summary\](.*)\[\/summary\](.*)$/sU';
            $replacements[] = '$2';

            return \preg_replace($patterns, $replacements, $text);
        }

        /**
         * Convert linebreaks to <br> tags
         *
         * @param string $text
         *
         * @return string
         */
        public function nl2Br($text): string
        {
            $text = \preg_replace("/(\015\012)|(\015)|(\012)/", '<br>', $text);
            if ($this->nbsp) {
                $patterns = ['  ', '\"'];
                $replaces = [' &nbsp;', '"'];
                //              $text     = substr(preg_replace('/\>.*\</esU', "str_replace(\$patterns,\$replaces,'\\0')", ">$text<"), 1, -1);
                $text = mb_substr(
                    \preg_replace_callback(
                        '/\>.*\</sU',
                        static function ($m) {
                            return \str_replace($patterns, $replaces, $m[0]);
                        },
                        ">$text<"
                    ),
                    1,
                    -1
                );
            }

            return $text;
        }

        /*
        * if magic_quotes_gpc is on, stirip back slashes
        *
        * @param    string  $text
        *
        * @return   string
        */

        /**
         * @param string $text
         *
         * @return string
         */
        public function stripSlashesGPC($text): string
        {
            if (@\get_magic_quotes_gpc()) {
                $text = \stripslashes($text);
            }

            if (\function_exists('myalbum_callback_after_stripslashes_local')) {
                $text = \myalbum_callback_after_stripslashes_local($text);
            }

            return $text;
        }
        // The End of Class
    }
}
