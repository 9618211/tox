<?php
/**
 * Defines the APIs to control applications runtime environments which based on
 * `Tox'.
 *
 * This file is part of Tox.
 *
 * Tox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Tox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tox.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright © 2012-2013 PHP-Tox.org
 * @license   GNU General Public License, version 3
 */

namespace Tox\Core;

use Exception;

/**
 * Provides the APIs to control applications runtime environments.
 *
 * **THIS CLASS CAN BE NEITHER INHERITED NOR INSTANTIATED.**
 *
 * __*ALIAS*__ as `Tox`.
 *
 * @package tox.core
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 * @since   0.1.0-beta1
 */
final class Runtime
{
    /**
     * Stores the instance of the classes manager of framework runtime.
     *
     * @internal
     *
     * @var ClassManager
     */
    private $cman;

    /**
     * Stores the instance of current APIs provider.
     *
     * @internal
     *
     * @var Runtime
     */
    private static $instance;

    /**
     * Stores the instance of the packages manager of framework runtime.
     *
     * @internal
     *
     * @var PackageManager
     */
    private $pman;

    /**
     * CONSTRUCT FUNCTION
     */
    private function __construct()
    {
        $this->cman = new ClassManager;
        $this->pman = new PackageManager;
    }

    /**
     * AUTOLOADs the required class or interface.
     *
     * @internal
     *
     * @param  string $class Name of specified class or interface.
     * @return void
     */
    public function load($class)
    {
        $s_class = $this->cman->transform($class);
        $p_class = $this->pman->locate($s_class);
        for ($ii = 0; $ii < 10; $ii++) {
            if (!is_string($p_class)) {
                return;
            }
            if (is_file($p_class)) {
                require_once $p_class;
                if (class_exists($s_class, false)) {
                    $this->cman->register($s_class, $p_class);
                }
                return;
            }
            $s_class = $this->cman->transform($class);
            $p_class = $this->pman->locate($s_class);
        }
    }

    /**
     * Imports a new namespace to the path of eithor a directory or a PHar file.
     *
     * @api
     *
     * @param  string $namespace Namespace to be imported.
     * @param  string $path      The corresponding path.
     * @return void
     */
    public static function import($namespace, $path)
    {
        static::setUp();
        try {
            static::$instance->pman->register(str_replace('\\', '.', strtolower($namespace)), $path);
        } catch (Exception $ex) {
            return self::halt($ex);
        }
    }

    /**
     * Alias of `import()'.
     *
     * @deprecated Remained for forward compatibility. Would be removed in some
     *             future version.
     *
     * @api
     *
     * @param  string $namespace Namespace to be imported.
     * @param  string $path      The corresponding path.
     * @return void
     *
     * @see Runtime::import()
     */
    public static function registerNamespace($namespace, $path)
    {
        return self::import($namespace, $path);
    }

    /**
     * Sets the framework runtime environment up.
     *
     * @api
     *
     * @return void
     */
    public static function setUp()
    {
        if (static::$instance instanceof static) {
            return;
        }
        static::$instance = new static;
        spl_autoload_register(array(static::$instance, 'load'), true, true);
        self::alias(__CLASS__, 'Tox');
    }

    /**
     * Aliases either a class or an interface to another.
     *
     * @api
     *
     * @param  string $class Name of either a class or an interface.
     * @param  string $alias New alias name.
     * @return void
     */
    public static function alias($class, $alias)
    {
        static::setUp();
        try {
            static::$instance->cman->alias($class, $alias);
        } catch (Exception $ex) {
            return self::halt($ex);
        }
    }

    /**
     * Alias of `alias()'.
     *
     * @deprecated Remained for forward compatibility. Would be removed in some
     *             future version.
     *
     * @api
     *
     * @param  string $class   Name of either a class or an interface.
     * @param  string $classAs New alias name.
     * @return void
     *
     * @see Runtime::alias()
     */
    public static function treatClassAs($class, $classAs)
    {
        return self::alias($class, $classAs);
    }

    /**
     * Terminates the runtime environment for errors.
     *
     * @internal
     *
     * @param  Exception $exception Error occured.
     * @return void
     */
    protected static function halt(Exception $exception)
    {
        trigger_error('Tox: ' . $exception->getMessage(), E_USER_ERROR);
    }

    /**
     * Revises the current working directory to the *root*.
     *
     * The script file of calling would be thought as the reference, and its
     * parent directory would be treated as the *root*.
     *
     * Unless the directory named as `bin`, `sbin`, `include` or `libexec`, one
     * more parent would be fetched and used.
     *
     * @api
     *
     * @return void
     */
    public static function reviseCwd()
    {
        $o_ex = new Exception;
        $a_ctx = $o_ex->getTrace();
        if (empty($a_ctx) || !isset($a_ctx[0]['file'])) {
            return;
        }
        $p_dir = dirname($a_ctx[0]['file']);
        switch (basename($p_dir)) {
            case 'bin':
            case 'include':
            case 'libexec':
            case 'sbin':
                $p_dir = dirname($p_dir);
                break;
        }
        chdir($p_dir);
    }
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
