<?php
/**
 * Copyright 2010 Cyrille Mahieux
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations
 * under the License.
 *
 * ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°> ><)))°>
 *
 * Error container
 *
 * @author elijaa@free.fr
 *
 * @since 11/10/2010
 */

namespace App\Library\Data;

class Errors
{
    /** @var array */
    private static $_errors = [];

    /**
     * Add an error to the container
     * Return true if successful, false otherwise
     *
     * @param string $error Error message
     *
     * @return int
     */
    public static function add($error)
    {
        return array_push(self::$_errors, $error);
    }

    /**
     * Return last Error message
     *
     * @return mixed
     */
    public static function last()
    {
        return self::$_errors[count(self::$_errors) - 1] ?? null;
    }

    /**
     * Return errors count
     *
     * @return int
     */
    public static function count()
    {
        return count(self::$_errors);
    }
}
