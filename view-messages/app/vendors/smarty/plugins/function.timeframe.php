<?php

/**
 * Timeframe plugin to return information between 2 dates or for a given time
 * interval
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
 * Example in template:
 * <smarty>
 * {timeframe from="2007-01-01 00:00:00 GMT"
 *            to="2008-03-25 13:45:26 GMT"
 *            assign="info"}
 * {$info|@print_r}
 * </smarty>
 *
 * or same in PHP:
 *
 * <code>
 * $from = '2007-01-01 00:00:00 GMT';
 * $to = '2008-03-25 13:45:26 GMT';
 * $params = array('to' => $to, 'from' => $from);
 *
 * print_r(smarty_function_timeframe($params, $smarty));
 * </code>
 *
 * Result:
 * Array (
 *    [SECONDS] => 38843126
 *    [MINUTES] => 647385.433333
 *    [HOURS] => 10789.7572222
 *    [DAYS] => 449.573217593
 *    [WEEKS] => 64.2247453704
 *    [YEARS] => 1.23086438766
 *    [seconds] => 26
 *    [minutes] => 45
 *    [hours] => 13
 *    [days] => 1
 *    [weeks] => 12
 *    [ydays] => 85
 *    [years] => 1
 * )
 *
 * @param       array     $params list of arguments for the function
 * <ul>
 *  <li>timeframe (integer)</li>
 *  <li>to (datetime) (if provided "timeframe" is ignored)
 *                     - default: current time</li>
 *  <li>from (datetime) (if provided "timeframe" is ignored)
 *                       - default: current time</li>
 *  <li>assign (string) Smarty variable name</li>
 * </ul>
 * @param       Smarty    $smarty template engine instance
 *
 * @return      array|void     Array with the following indexes (if "assign"
 *                             is set, the Smarty variable will be set instead)
 * <ul>
 *  <li>SECONDS</li>
 *  <li>MINUTES</li>
 *  <li>HOURS</li>
 *  <li>DAYS</li>
 *  <li>WEEKS</li>
 *  <li>YEARS</li>
 *  <li>seconds (0-59)</li>
 *  <li>minutes (0-59)</li>
 *  <li>hours (0-23)</li>
 *  <li>days (0-6)</li>
 *  <li>weeks (0-51)</li>
 *  <li>ydays (0-365)</li>
 *  <li>years</li>
 * </ul>
 *
 * @copyright   (c) 2007 Philippe Jausions / 11abacus
 * @author      Philippe Jausions <jausions@php.net>
 * @license     New BSD License
 * @package     Smarty
 * @subpackage  plugins
 * @uses        smarty_make_timestamp()
 * @link        http://pear.11abacus.com/dev/Smarty/libs/plugins/function.timeframe_format.phps
 */
function smarty_function_timeframe($params = array(), &$smarty)
{
    unset($params['smarty']);

    // Default values
    $timeframe = 0;
    $from      = time();
    $to        = time();
    extract($params);

    if (array_key_exists('to', $params) || array_key_exists('from', $params)) {
        /**
         * Include the {@link shared.make_timestamp.php} plugin
         */
        require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');

        $from = smarty_make_timestamp($from);
        $to = smarty_make_timestamp($to);

        $timeframe = $to - $from;
    }

    $parts['SECONDS'] = $timeframe;
    $parts['MINUTES'] = $timeframe / 60;
    $parts['HOURS']   = $timeframe / 3600;
    $parts['DAYS']    = $timeframe / 86400;
    $parts['WEEKS']   = $timeframe / 604800;
    $parts['YEARS']   = $timeframe / 31557600;

    $parts['seconds'] = $timeframe % 60;
    $parts['minutes'] = floor(($timeframe % 3600) / 60);
    $parts['hours']   = floor(($timeframe % 86400) / 3600);
    $parts['days']    = floor(($timeframe % 604800) / 86400);
    $parts['weeks']   = floor(($timeframe % 31557600) / 604800);
    $parts['ydays']   = $parts['days'] + $parts['weeks'] * 7;
    $parts['years']   = floor($parts['YEARS']);

    if (empty($assign)) {
        return $parts;
    }

    $smarty->assign($assign, $parts);
}

?>