<?php

/**
 * Formats a timeframe
 *
 * PHP Version 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 2007, Philippe Jausions / 11abacus
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   - Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   - Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *   - Neither the name of the 11abacus nor the names of its contributors may
 *     be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * Format: fractions of timeframe
 * <ul>
 *  <li>%s :seconds (0-59)</li>
 *  <li>%m :minutes (0-59)</li>
 *  <li>%h :hours (0-23)</li>
 *  <li>%d :week days (0-6)</li>
 *  <li>%j :year days (0-365)</li>
 *  <li>%w :weeks (0-51)</li>
 *  <li>%y :years</li>
 * </ul>
 *
 * Format: timeframe in units
 * <ul>
 *  <li>%S :seconds</li>
 *  <li>%M :minutes</li>
 *  <li>%H :hours</li>
 *  <li>%D :days</li>
 *  <li>%W :weeks</li>
 *  <li>%Y :years</li>
 * </ul>
 *
 * Example:
 * <smarty>
 * {timeframe_format to="tomorrow" precision=4 format="(%h @h|1:hour|hours|::)%m minutes"}
 * </smarty>
 *
 * For format uses the following syntax:
 * <ul>
 *  <li>() to mark conditional part of the format. The part will only be
 *         displayed if the first date value found inside is not zero</li>
 *  <li>[] to mark conditional and default part of the format. The part
 *         will be displayed if none of the other () conditional parts are
 *         displayed.</li>
 * </ul>
 *
 * Use backslash (\) to escape () [], %, @ and |
 *
 * Noun declination i.e. "0 years" vs. "1 year" vs. "2 years".
 * To decline nouns according to the value for the date part, start with
 * a @ followed by the name of the date part to work with, followed by |.
 * You then add the declinations using the format:
 * part value ":" declination "|"
 *
 * The last declination listed is used by default.
 *
 * For instance the 2 formats below are equivalent:
 * <ul>
 *  <li>%Y @Y|0:years|1:year|years|</li>
 *  <li>%Y @Y|1:year|years|</li>
 * </ul>
 *
 * For timeframes in units, you can specify a decimal precision using the
 * sprintf style. i.e. %.1H for total hours with 1 decimal. You can use
 * the decimal precision with the declination as well: @.1H
 *
 * Examples of format (first example is the default value):
 * <ul>
 *  <li>(%y @y|1:year|years| )(%w @w|1:week|weeks| )(%d @d|1:day|days| )(%h @h|1:hour|hours| )(%m @m|1:minute|minutes| )(%s @s|1:second|seconds|)[%s @s|1:second|seconds|]</li>
 *  <li>(%y year(s)) (%w week(s)) (%d day(s)) (%h hour(s)) (%m minute(s)) (%s second(s))[%s second(s)]</li>
 *  <li>(%d day\(s\))[Today in %h hour(s)]</li>
 *  <li>%.0D::%h::%m::%s</li>
 * </ul>
 *
 * @param       array     $params list of arguments for the function
 * <ul>
 *  <li>timeframe (integer)</li>
 *  <li>to (datetime) (if provided "timeframe" is ignored)
 *                     - default: current time</li>
 *  <li>from (datetime) (if provided "timeframe" is ignored)
 *                       - default: current time</li>
 *  <li>precision (integer) How many most important date parts to display
 *                          (this only applies to lowercase placeholders,
 *                           and when conditional () formatting is used.)</li>
 *  <li>format (string)</li>
 *  <li>assign (string) Smarty variable name</li>
 * </ul>
 * @param       Smarty    $smarty template engine instance
 *
 * @return      string|void If "assign" is set, the value will be set to the
 *                          Smarty instead of being returned.
 *
 * @copyright   (c) 2007 Philippe Jausions / 11abacus
 * @author      Philippe Jausions <jausions@php.net>
 * @license     New BSD License
 * @filesource
 * @package     Smarty
 * @subpackage  plugins
 * @uses        smarty_function_timeframe()
 * @see         smarty_function_timeframe()
 * @link        http://pear.11abacus.com/dev/Smarty/libs/plugins/function.timeframe_format.phps
 */
function smarty_function_timeframe_format($params = array(), &$smarty)
{
    unset($params['smarty']);

    // Default values
    // Display only 2 most significant date parts
    $precision = 2;

    // Default format
    $format = '(%y @y|1:year|years| )(%w @w|1:week|weeks| )(%d @d|1:day|days| )(%h @h|1:hour|hours| )(%m @m|1:minute|minutes| )(%s @s|1:second|seconds|)[%s @s|1:second|seconds|]';

    extract($params);
    unset($params['assign']);

    /**
     * Include the function.timeframe.php plugin
     */
    require_once $smarty->_get_plugin_filepath('function', 'timeframe');
    $parts = smarty_function_timeframe($params, $smarty);

    $p = array('%S' => 'SECONDS',
               '%M' => 'MINUTES',
               '%H' => 'HOURS',
               '%D' => 'DAYS',
               '%W' => 'WEEKS',
               '%Y' => 'YEARS',
               '%s' => 'seconds',
               '%m' => 'minutes',
               '%h' => 'hours',
               '%d' => 'days',
               '%w' => 'weeks',
               '%j' => 'ydays',
               '%y' => 'years',
               );

    // Only keep the most important date parts, per the "precision" parameter
    $partImportance = array('%y', '%w', '%d', '%h', '%m', '%s');

    // Find the first non-zero date part
    $i = 0;
    foreach ($partImportance as $i => $part) {
        if ($parts[$p[$part]] != 0) {
            break;
        }
    }
    // Keep "precision" date parts, and zero the remaining ones
    $i += $precision;
    for (; $i < 6; ++$i) {
        $parts[$p[$partImportance[$i]]] = 0;
    }

    // Parsing variables
    $output         = '';
    $var            = '';
    $inDeclination  = false; // Parsing noun declination options
    $inConditional  = false; // Parsing conditional format fragment
    $inIfNothing    = false; // Parsing the conditional fragment that would
                             // be displayed if all the other conditional
                             // fragments wouldn't otherwise.
    $inVar          = false; // Parsing a "variable"
    $inVarPrecision = false; // Parsing a variable's precision
    $length         = strlen($format);
    $specials       = '([\\%@';
    $vars           = 'sSmMhHdDjwWyY';
    $markers        = array();
    $marker         = null;

    $debug = '';

    // Parse the format
    for ($i = 0; $i < $length; ++$i) {
        $char = $format[$i];

        // Currently parsing variable?
        if ($inVar) {
            $var .= $char;

            // Getting decimal precision?
            if ($inVarPrecision && is_numeric($char)) {
                continue;
            }
            // Allow the dot (.) if it hasn't already been parsed
            $_vars = $vars.(($inVarPrecision) ? '' : '.');

            // Expected decimal point or value type?
            if (strpos($_vars, $char) === false) {
                if ($inConditional || $inIfNothing) {
                    $markers[$marker]['output'] .= $var;
                } else {
                    $output .= $var;
                }
                $var = '';
                $inVar = false;
                $inVarPrecision = false;
                $inDeclination = false;
                continue;
            }
            if ($char == '.') {
                $inVarPrecision = true;
                continue;
            }
            // Get the value
            $f = '%'.substr($var, 1, -1).(($inVarPrecision) ? 'f' : 'd');
            $varValue = sprintf($f, $parts[$p['%'.$char]]);

            // Look for the declination matching the value, and use it
            // in place
            if ($inDeclination) {

                $_found = false;
                $text = '';
                if ($i + 3 < $length && $format[$i + 1] == '|') {
                    $value = '';
                    $inText = false;
                    for ($k = $i + 2; $k < $length; ++$k) {
                        $char = $format[$k];

                        switch ($char) {
                            case '|':
                                if ($inText) {
                                    if ($value == abs($varValue)) {
                                        $_found = true;
                                        $i = $k;
                                    }
                                    $inText = false;
                                    $value = '';
                                } else {
                                    // We found the last default declination
                                    // Use that text, if we didn't get a better
                                    // match before.
                                    if (!$_found) {
                                        $text = $value;
                                    }
                                    $_found = true;
                                    $i = $k;
                                    break 2;
                                }
                                break;
                            case ':':
                                if ($value == '') {
                                    break 2;
                                }
                                $inText = true;
                                if (!$_found) {
                                    $text = '';
                                }
                                break;
                            case '\\':
                                $char = $format[++$k];
                                // No BREAK here. This is intentional!
                            default:
                                if (!$_found) {
                                    if ($inText) {
                                        $text .= $char;
                                    } else {
                                        $value .= $char;
                                    }
                                }
                        }
                    }
                }
                if (!$_found) {
                    $varValue = $var;
                } else {
                    $varValue = $text;
                }
            }

            // Place value in output
            if ($inConditional || $inIfNothing) {
                $markers[$marker]['output'] .= $varValue;
                // The first value found determines if the conditional
                // format will be output
                if ($markers[$marker]['condition'] == -1
                    && !$inDeclination) {
                    if ($varValue) {
                        $markers[$marker]['condition'] = 1;
                    } else {
                        $markers[$marker]['condition'] = 0;
                    }
                }
            } else {
                $output .= $varValue;
            }
            $inVar = false;
            $inVarPrecision = false;
            $inDeclination = false;
            $var = '';
            continue;
        }

        // Special character at end of format?
        if (strpos($specials, $char) !== false) {
            if ($i + 1 == $length) {
                $output .= $char;
                break;
            }
        }

        switch ($char) {
            case '(':
            case '[':
                if ($inConditional || $inIfNothing) {
                    break;
                }
                if ($char == '(') {
                    $inConditional = true;
                } else {
                    $inIfNothing = true;
                }
                $marker = count($markers);
                $markers[] = array('position' => strlen($output),
                                   'type' => $char,
                                   'condition' => -1,
                                   'output' => '');
                break;

            case ')':
            case ']':
                if ($inConditional) {
                    $inConditional = false;
                    $markers[$marker]['output'] .= $char;
                    $char = '';
                } elseif ($inIfNothing) {
                    $inIfNothing = false;
                    $markers[$marker]['output'] .= $char;
                    $char = '';
                }
                break;

            case '@':
                $inDeclination = true;
                // No BREAK here. This is intentional
            case '%':
                $inVar = true;
                $inVarPrecision = false;
                $var = $char;
                continue 2;

            case '\\':
                $char = $format[++$i];
                break;
        }
        if ($inConditional || $inIfNothing) {
            $markers[$marker]['output'] .= $char;
        } else {
            $output .= $char;
        }
    }

    // Insert the conditional strings back into the output
    $condition = false;
    $count = count($markers);
    for ($marker = 0; $marker < $count; ++$marker) {
        $data = $markers[$marker];

        // We'll take care of the "if nothing" later below
        if ($data['type'] == '[') {
            continue;
        }

        if ($data['condition'] == 0) {
            unset($markers[$marker]);
            continue;
        }
        if ($data['condition'] == 1) {
            // Trim brackets [] ()
            $data['output'] = substr($data['output'], 1, -1);
            // We have at least one conditional fragment met
            // so ifNothing are not needed
            $condition = true;
        }

        $output = substr_replace($output, $data['output'],
                                 $data['position'], 0);
        $offset = strlen($data['output']);
        unset($markers[$marker]);

        // We need to adjust position due to the "ifNothing" that
        // we will revisit later below
        for ($i = $marker + 1; $i < $count; ++$i) {
            $markers[$i]['position'] += $offset;
        }
    }

    // At last, if none of the conditional fragments were added,
    // insert the ifNothing blocks
    if (!$condition) {
        foreach ($markers as $marker => $data) {
            $data['output'] = substr($data['output'], 1, -1);
            $output = substr_replace($output, $data['output'],
                                     $data['position'], 0);
            $offset = strlen($data['output']);
            for ($i = $marker + 1; $i < $count; ++$i) {
                if (isset($markers[$i])) {
                    $markers[$i]['position'] += $offset;
                }
            }
        }
    }

    if (empty($assign)) {
        return $output;
    }

    $smarty->assign($assign, $output);
}

?> 