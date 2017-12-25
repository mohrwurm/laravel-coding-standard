<?php
/**
 * Common
 *
 * @package   GreynoiseLaravel
 * @author    Louis Linehan <louis@greynoise.co.uk>
 * @copyright 2017 Louis Linehan
 * @license   https://github.com/greynoise-design/laravel-coding-standard/blob/master/LICENSE MIT License
 */

namespace GreynoiseLaravel\Util;

use PHP_CodeSniffer\Util\Common as BaseCommon;

/**
 * Common
 *
 * Extrends common functions.
 *
 * @author Louis Linehan <louis@greynoise.co.uk>
 */
class Common extends BaseCommon
{

    /**
     * An array of variable types for param/var we will check.
     *
     * @var string[]
     */
    public static $allowedTypes = [
        'array',
        'bool',
        'float',
        'int',
        'mixed',
        'object',
        'string',
        'resource',
        'callable',
    ];

    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    public static $magicMethods = array(
                                   'construct'  => true,
                                   'destruct'   => true,
                                   'call'       => true,
                                   'callstatic' => true,
                                   'get'        => true,
                                   'set'        => true,
                                   'isset'      => true,
                                   'unset'      => true,
                                   'sleep'      => true,
                                   'wakeup'     => true,
                                   'tostring'   => true,
                                   'set_state'  => true,
                                   'clone'      => true,
                                   'invoke'     => true,
                                   'debuginfo'  => true,
                                  );

    /**
     * Allowed public methodNames
     *
     * @var array
     */
    public static $publicMethodNames = array('_remap' => true);


    /**
     * Is lower snake case
     *
     * @param string $string The string to verify.
     *
     * @return boolean
     */
    public static function isLowerSnakeCase($string)
    {
        if (strcmp($string, strtolower($string)) !== 0) {
            return false;
        }

        if (strpos($string, ' ') !== false) {
            return false;
        }

        return true;

    }//end isLowerSnakeCase()


    /**
     * Has an underscore prefix
     *
     * @param string $string The string to verify.
     *
     * @return boolean
     */
    public static function hasUnderscorePrefix($string)
    {
        if (strpos($string, '_') !== 0) {
            return false;
        }

        return true;

    }//end hasUnderscorePrefix()


    /**
     * Pluralize
     *
     * Basic pluralize intended for use in error messages
     * tab/s, space/s, error/s etc.
     *
     * @param string $string String.
     * @param float  $num    Number.
     *
     * @return string
     */
    public static function pluralize($string, $num)
    {
        if ($num > 1) {
            return $string.'s';
        } else {
            return $string;
        }

    }//end pluralize()

    /**
     * Returns a valid variable type for param/var tag.
     *
     * If type is not one of the standard type, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     *
     * @return string
     */
    public static function suggestType($varType)
    {
        if ($varType === '') {
            return '';
        }

        if (in_array($varType, self::$allowedTypes) === true) {
            return $varType;
        } else {
            $lowerVarType = strtolower($varType);
            switch ($lowerVarType) {
                case 'bool':
                case 'boolean':
                    return 'bool';
                case 'double':
                case 'real':
                case 'float':
                    return 'float';
                case 'int':
                case 'integer':
                    return 'int';
                case 'array()':
                case 'array':
                    return 'array';
            }//end switch

            if (strpos($lowerVarType, 'array(') !== false) {
                // Valid array declaration:
                // array, array(type), array(type1 => type2).
                $matches = [];
                $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
                if (preg_match($pattern, $varType, $matches) !== 0) {
                    $type1 = '';
                    if (isset($matches[1]) === true) {
                        $type1 = $matches[1];
                    }

                    $type2 = '';
                    if (isset($matches[3]) === true) {
                        $type2 = $matches[3];
                    }

                    $type1 = self::suggestType($type1);
                    $type2 = self::suggestType($type2);
                    if ($type2 !== '') {
                        $type2 = ' => ' . $type2;
                    }

                    return "array($type1$type2)";
                } else {
                    return 'array';
                }//end if
            } else {
                if (in_array($lowerVarType, self::$allowedTypes) === true) {
                    // A valid type, but not lower cased.
                    return $lowerVarType;
                } else {
                    // Must be a custom type name.
                    return $varType;
                }
            }//end if
        }//end if

    }//end suggestType()


}//end class
