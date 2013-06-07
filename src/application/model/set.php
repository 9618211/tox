<?php
/**
 * Defines the models sets.
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

namespace Tox\Application\Model;

use Tox\Core;
use Tox\Application;

/**
 * Represents as a models set.
 *
 * **THIS CLASS CANNOT BE INSTANTIATED.**
 *
 * @package tox.application.model
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 * @since   0.1.0-beta1
 */
abstract class Set extends Core\Assembly implements Application\IModelSet
{
    /**
     * Stores the data access object in use.
     *
     * @var Application\IDao
     */
    protected $dao;

    /**
     * Stores the applied filters.
     *
     * @var array[]
     */
    protected $filters;

    /**
     * Stores the applied sorting orders.
     *
     * @var const[]
     */
    protected $orders;

    /**
     * Stores the applied cropping offset.
     *
     * @var int
     */
    protected $offset;

    /**
     * Stores the applied cropping limit.
     *
     * @var int
     */
    protected $limit;

    /**
     * Stores the amount of included model entities.
     *
     * @var int
     */
    protected $length;

    /**
     * Stores the included model entities.
     *
     * @var Model[]
     */
    protected $items;

    /**
     * Stores the cursor of iteration.
     *
     * @var int
     */
    protected $cursor;

    /**
     * Stores the parent model entity.
     *
     * @var Application\IModel
     */
    protected $parent;

    /**
     * Stores the identifiers of included model entities.
     *
     * @var true[]
     */
    protected $index;

    /**
     * Stores the model entities to be appended.
     *
     * @var Application\IModel[]
     */
    protected $toxAppend;

    /**
     * Stores the model entities to be dropped.
     *
     * @var Application\IModel[]
     */
    protected $toxDrop;

    /**
     * Stores whether in async mode.
     *
     * @var bool
     */
    protected $toxAsync;

    /**
     * Retrieves the default data access object.
     *
     * @return Application\IDao
     */
    abstract protected function getDefaultDao();

    /**
     * CONSTRUCT FUNCTION
     *
     * @param Application\IModel $parent OPTIONAL. Parent model entity.
     * @param Application\IDao   $dao    OPTIONAL. Data access object to use.
     */
    public function __construct(Application\IModel $parent = null, Application\IDao $dao = null)
    {
        $this->parent = $parent;
        $this->dao = $dao;
        $this->filters =
        $this->orders = array();
        $this->offset =
        $this->limit = 0;
        $this->length =
        $this->cursor = -1;
        $this->toxAppend =
        $this->toxDrop = array();
        $this->toxAsync = true;
    }

    /**
     * Retrieves the data access object in use.
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return Application\IDao
     */
    final protected function getDao()
    {
        if (!$this->dao instanceof Application\IDao) {
            $this->dao = $this->getDefaultDao();
        }
        return $this->dao;
    }

    /**
     * Be invoked on filterings or excludings.
     *
     * @param  string $method    Invoking method name.
     * @param  array  $arguments Arguments.
     * @return self
     *
     * @throws IllegalFilterException If calling unexpected method.
     */
    public function __call($method, Array $arguments)
    {
        $regex = '@^(filter|exclude)(\w+)' .
            '(Equals|GreaterThan|GreaterOrEquals|LessThan|LessOrEquals|Between|In|Like)$@U';
        $a_parts = array();
        if (preg_match($regex, $method, $a_parts)) {
            if ('Between' == $a_parts[3] && 1 == count($arguments)) {
                $a_parts[3] = 'equals';
            }
            array_unshift($arguments, $a_parts[2]);
            return call_user_func_array(array($this, $a_parts[1] . $a_parts[3]), $arguments);
        }
        $regex = '@^(sort)(\w+)$@';
        if (preg_match($regex, $method, $a_parts)) {
            array_unshift($arguments, $a_parts[2]);
            return call_user_func_array(array($this, 'doSort'), $arguments);
        }
        throw new IllegalFilterException(array('name' => $method));
    }

    /**
     * Filters that the attribute equals to the value.
     *
     * @param  string $attribute Attribute name.
     * @param  mixed  $value     Expected value.
     * @return self
     */
    protected function filterEquals($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Filters that the attribute is greater than the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function filterGreaterThan($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Filters that the attribute is greater than or equals to the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function filterGreaterOrEquals($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Filters that the attribute is less than the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function filterLessThan($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Filters that the attribute is less than or equals to the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function filterLessOrEquals($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Filters that the attribute is in the range.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $min       Expected minimize value.
     * @param  int|float $max       Expected maximize value.
     * @return self
     */
    protected function filterBetween($attribute, $min, $max)
    {
        if ($min == $max) {
            return $this->filterEquals($attribute, $min);
        }
        return $this->doFilter(__METHOD__, $attribute, array($min, $max));
    }

    /**
     * Filters that the attribute is one of the values.
     *
     * @param  string $attribute Attribute name.
     * @param  array  $value     Expected values.
     * @return self
     */
    protected function filterIn($attribute, array $values)
    {
        return $this->doFilter(__METHOD__, $attribute, $values);
    }

    /**
     * Filters that the attribute is alike the value.
     *
     * @param  string $attribute Attribute name.
     * @param  string $value     Expected value.
     * @return self
     */
    protected function filterLike($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Excludes that the attribute equals to the value.
     *
     * @param  string $attribute Attribute name.
     * @param  mixed  $value     Expected value.
     * @return self
     */
    protected function excludeEquals($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Excludes that the attribute is greater than the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function excludeGreaterThan($attribute, $value)
    {
        return $this->filterLessOrEquals($attribute, $value);
    }

    /**
     * Excludes that the attribute is greater than or equals to the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function excludeGreaterOrEquals($attribute, $value)
    {
        return $this->filterLessThan($attribute, $value);
    }

    /**
     * Excludes that the attribute is less than the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function excludeLessThan($attribute, $value)
    {
        return $this->filterGreaterOrEquals($attribute, $value);
    }

    /**
     * Excludes that the attribute is less than or equals to the value.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $value     Expected value.
     * @return self
     */
    protected function excludeLessOrEquals($attribute, $value)
    {
        return $this->filterGreaterThan($attribute, $value);
    }

    /**
     * Excludes that the attribute is in the range.
     *
     * @param  string    $attribute Attribute name.
     * @param  int|float $min       Expected minimize value.
     * @param  int|float $max       Expected maximize value.
     * @return self
     */
    protected function excludeBetween($attribute, $min, $max)
    {
        if ($min == $max) {
            return $this->excludeEquals($attribute, $min);
        }
        return $this->doFilter(__METHOD__, $attribute, array($min, $max));
    }

    /**
     * Excludes that the attribute is one of the values.
     *
     * @param  string $attribute Attribute name.
     * @param  array  $value     Expected values.
     * @return self
     */
    protected function excludeIn($attribute, array $values)
    {
        return $this->doFilter(__METHOD__, $attribute, $values);
    }

    /**
     * Excludes that the attribute is alike the value.
     *
     * @param  string $attribute Attribute name.
     * @param  string $value     Expected value.
     * @return self
     */
    protected function excludeLike($attribute, $value)
    {
        return $this->doFilter(__METHOD__, $attribute, $value);
    }

    /**
     * Filters the set by a condition.
     *
     * @param  string $type      Filter type.
     * @param  string $attribute Attribute name.
     * @param  mixed  $value     Expected value.
     * @return self
     *
     * @throws AttributeFilteredException If filtering an attribute multiple
     *                                    times.
     */
    protected function doFilter($type, $attribute, $value)
    {
        $a_parts = explode('::', $type);
        $type = $a_parts[1];
        if (isset($this->filters[$attribute])) {
            if (array($type, $value) != $this->filters[$attribute]) {
                throw new AttributeFilteredException(
                    array('name' => $attribute, 'type' => $this->filters[$attribute][0])
                );
            }
            return $this;
        }
        $o_this = clone $this;
        $o_this->filters[$attribute] = array($type, $value);
        return $o_this;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function __clone()
    {
    }

    /**
     * Sorts by the attribute.
     *
     * @param  string $attribute Attribute name.
     * @param  const  $order     Sorting order.
     * @return self
     *
     * @throws AttributeSortedException If Sorting an attribute multiple times.
     */
    protected function doSort($attribute, $order = self::SORT_DESC)
    {
        if (self::SORT_DESC != $order) {
            $order = self::SORT_ASC;
        }
        if (isset($this->orders[$attribute])) {
            if ($order != $this->orders[$attribute]) {
                throw new AttributeSortedException(array('name' => $attribute));
            }
            return $this;
        }
        $o_this = clone $this;
        $o_this->orders[$attribute] = $order;
        return $o_this;
    }

    /**
     * {@inheritdoc}
     *
     * @param  int  $offset The beginning offset to be kept.
     * @param  int  $length OPTIONAL. The length to be kept. In defaults, every
     *                      model sould be kept until the end.
     * @return self
     */
    public function crop($offset, $length = 0)
    {
        $offset = (int) $offset;
        if (0 > $offset) {
            $offset = 0;
        }
        $length = (int) $length;
        if (0 > $length) {
            $length = 0;
        }
        if (!$offset && !$length) {
            return $this;
        }
        $o_this = clone $this;
        $o_this->offset += $offset;
        $o_this->limit = $o_this->limit ? min($length, $o_this->limit) : $length;
        return $o_this;
    }

    /**
     * Counts the amount of included model entities.
     *
     * @return int
     */
    public function count()
    {
        if (-1 == $this->length) {
            $this->length = $this->getDao()->countBy($this->filters, $this->offset, $this->limit);
        }
        return $this->length;
    }

    /**
     * Retrieves the class name of corresponding model.
     *
     * @return string
     */
    abstract protected function getModelClass();

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     */
    public function rewind()
    {
        if (!is_array($this->items)) {
            if (!$this->isFiltered()) {
                throw new SetWithoutFiltersException;
            }
            $this->index = array();
            $this->items = $this->getDao()->listBy($this->filters, $this->orders, $this->offset, $this->limit);
            $this->length = count($this->items);
        }
        $this->cursor = 0;
        foreach ($this->items as $ii => $jj) {
            $this->items[$ii] = call_user_func(array($this->getModelClass(), 'import'), $this, $this->getDao());
            $this->index[$this->items[$ii]->getId()] = $ii;
            $this->cursor++;
        }
        $this->cursor = 0;
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        if (-1 == $this->cursor) {
            $this->rewind();
        }
        return -1 < $this->cursor && $this->cursor < $this->length;
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next()
    {
        if (-1 == $this->cursor) {
            $this->rewind();
        }
        $this->cursor++;
    }

    /**
     * Return the key of the current element.
     *
     * @return int
     */
    public function key()
    {
        if (-1 == $this->cursor) {
            $this->rewind();
        }
        return $this->cursor;
    }

    /**
     * Return the current element.
     *
     * @return Application\IModel
     */
    public function current()
    {
        if (-1 == $this->cursor) {
            $this->rewind();
        }
        return $this->items[$this->cursor];
    }

    /**
     * {@inheritdoc}
     *
     * @return IModel
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Be invoked on retrieving the parent model entity.
     *
     * @return Application\IModel
     */
    final protected function toxGetParent()
    {
        return $this->getParent();
    }

    /**
     * {@inheritdoc}
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return int
     */
    public function getLength()
    {
        return $this->count();
    }

    /**
     * Be invoked on retrieving the amount of included models entities.
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return int
     */
    final protected function toxGetLength()
    {
        return $this->getLength();
    }

    /**
     * {@inheritdoc}
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return bool
     */
    final public function hasParent()
    {
        return $this->parent instanceof Application\IModel;
    }

    /**
     * {@ineritdoc}
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return self
     */
    final public function commit()
    {
        if (-1 != $this->cursor) {
            foreach ($this->items as $ii) {
                $ii->commit();
            }
        }
        $this->valid();
        foreach ($this->toxAppend as $ii) {
            if (array_key_exists($ii->commit()->getId(), $this->index)) {
                throw new ModelIncludedInSetException;
            }
            $this->items[] = $ii;
            $this->index[$ii->getId()] = $this->length++;
            if ($this->hasParent()) {
                $this->getDao()->tie($this->getParent(), $ii);
            }
        }
        foreach ($this->toxDrop as $ii) {
            unset($this->items[$this->index[$ii->getId()]], $this->index[$ii->getId()]);
            $this->length--;
        }
        $this->items = array_values($this->items);
        $this->index = array();
        foreach ($this->items as $ii => $jj) {
            $this->index[$jj->getId()] = $ii;
        }
        return $this->reset();
    }

    /**
     * {@inheritdoc}
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return self
     */
    final public function reset()
    {
        $this->toxAppend =
        $this->toxDrop = array();
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * NOTICE: When trying to append a model entity to an empty set (without any
     * filters, no offset and no limit), A new
     *
     * @param  Application\IModel $entity The model to be appended.
     * @return self
     *
     * @throws IllegalEntityForSetException If appending an illegal model
     *                                      entity.
     * @throws ModelIncludedInSetException  If appending an already included
     *                                      model entity.
     */
    public function append(Application\IModel $entity)
    {
        if (!is_a($entity, $this->getModelClass())) {
            throw new IllegalEntityForSetException;
        }
        $this->valid();
        if ($this->has($entity)) {
            throw new ModelIncludedInSetException;
        }
        $this->toxAppend[] = $entity;
        return $this->toxAsync ? $this : $this->commit();
    }

    /**
     * Removes a model.
     *
     * @param  Application\IModel $entity The model to be removed.
     * @return self
     *
     * @throws IllegalEntityForSetException If dropping an illegal model entity.
     * @throws PreparedModeltoxDropException If dropping a prepared model entity.
     */
    public function drop(Application\IModel $entity)
    {
        if (!is_a($entity, $this->getModelClass())) {
            throw new IllegalEntityForSetException;
        }
        $this->valid();
        if (!$entity->isAlive()) {
            throw new PreparedModelToDropException;
        }
        if (array_key_exists($entity->getId(), $this->index)) {
            $this->toxDrop[] = $entity;
        }
        return $this->toxAsync ? $this : $this->commit();
    }

    /**
     * {@inheritdoc}
     *
     * @param  Application\IModel  $entity The model to be checked.
     * @return bool
     */
    public function has(Application\IModel $entity)
    {
        return $entity->isAlive() && array_key_exists($entity->getId(), $this->index);
    }

    /**
     * Removes every model inside.
     *
     * @return self
     */
    public function clear()
    {
        $this->valid();
        $this->toxAppend =
        $this->toxDrop = array();
        foreach ($this as $entity) {
            $this->toxDrop[] = $entity;
        }
        return $this->toxAsync ? $this : $this->commit();
    }

    /**
     * Checks whether the set has been filtered.
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return bool
     */
    final protected function isFiltered()
    {
        return !empty($this->filters) || $this->offset || $this->limit;
    }

    /**
     * {@inheritdoc}
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return bool
     */
    final public function isChanged()
    {
        return !empty($this->toxAppend) || !empty($this->toxDrop);
    }

    /**
     * {@inheritdoc}
     *
     * **THIS METHOD CANNOT BE OVERRIDDEN.**
     *
     * @return bool
     */
    final public function isAsync()
    {
        return $this->toxAsync;
    }

    /**
     * {@inhertdoc}
     *
     * @return self
     */
    public function enableAsync()
    {
        $this->toxAsync = true;
        return $this;
    }

    /**
     * {@inhertdoc}
     *
     * @return self
     */
    public function disableAsync()
    {
        $this->toxAsync = false;
        return $this;
    }
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
