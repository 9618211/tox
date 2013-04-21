<?php
/**
 * Defines the sync standard of models between runtime and data sources.
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

namespace Tox\Application;

/**
 * Announces the sync standard of models between runtime and data sources.
 *
 * @package tox.application
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 * @since   0.1.0-beta1
 */
interface ICommittable
{
    /**
     * Commits the changements.
     *
     * @return self
     */
    public function commit();

    /**
     * Checks whether changed.
     *
     * @return bool
     */
    public function isChanged();

    /**
     * Ignores the changements and resets to the initial state.
     *
     * @return self
     */
    public function reset();

    /**
     * Enables async mode.
     *
     * @return self
     */
    public function enableAsync();

    /**
     * Disables async mode.
     *
     * @return self
     */
    public function disableAsync();

    /**
     * Checks wether in async mode.
     *
     * @return bool
     */
    public function isAsync();
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
