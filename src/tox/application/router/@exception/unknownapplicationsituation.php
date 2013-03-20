<?php
/**
 * Defines an exception for routing failed.
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
 * @copyright © 2012-2013 SZen.in
 * @license   GNU General Public License, version 3
 */

namespace Tox\Application\Router;

use Tox\Core;

/**
 * Be raised on routing failed.
 *
 * **THIS CLASS CANNOT BE INHERITED.**
 *
 * @package tox.application.router
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 */
final class UnknownApplicationSituationException extends Core\Exception
{
    /**
     * {@inheritdoc}
     *
     * > Defined as `0x80020402`.
     */
    const CODE = 0x80020402;

    /**
     * {@inheritdoc}
     *
     * > Defined as `unknown application situation`.
     */
    const MESSAGE = 'unknown application situation';
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
