<?php
/*
 * The MIT License (MIT)
 * 
 * Copyright (c) 2014 Rouven Weßling
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * This file is part of the hash_equals library
 *
 * For the full copyright and license information, please read the LICENSE above
 *
 * @copyright Copyright (c) 2013-2014 Rouven Weßling <http://rouvenwessling.de>
 * @license http://opensource.org/licenses/MIT MIT
 */

if (!function_exists('hash_equals')) {
	function hash_equals($known_string, $user_string)
	{
		// We jump trough some hoops to match the internals errors as closely as possible
		$argc = func_num_args();
        $params = func_get_args();
        
        if ($argc < 2) {
            trigger_error("hash_equals() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        
        if (!is_string($known_string)) {
        	trigger_error("hash_equals(): Expected known_string to be a string, " . gettype($known_string) . " given", E_USER_WARNING);
            return false;
        }
        if (!is_string($user_string)) {
        	trigger_error("hash_equals(): Expected user_string to be a string, " . gettype($user_string) . " given", E_USER_WARNING);
            return false;
        }
        
        if (strlen($known_string) !== strlen($user_string)) {
        	return false;
        }
		$len = strlen($known_string);
		$result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= (ord($known_string[$i]) ^ ord($user_string[$i]));
        }
        // They are only identical strings if $result is exactly 0...
        return 0 === $result;
	}
}