<?php

/*
 * The MIT License
 *
 * Copyright (c) 2017 Benjamin Costa <benjamin.costa.75@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace CBList\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract base class representing an entity.
 *
 * This class basically holds a unique id and some helper methods.
 * Any Entity that doesn't have special needs for their unique id
 * should extends this class for convenience.
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
abstract class Entity implements CBListEntity
{
    /**
     * The Category instance unique ID.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Constructs a new Entity instance.
     *
     * You can pass an array to this constructor where the keys corresponds
     * to an Entity attribute name and their values to the one you want your
     * instances to be initialized with.
     * While no verification are made for the id attribute,
     * the usual constraints apply for the other attributes.
     *
     * ie: array('id' => 0, 'label' => 'label')
     *
     * @param array $data the array of values to initialize this instance with
     */
    public function __construct(array $data = null)
    {
        if (null !== $data) {
            $this->hydrate($data);
        }
    }

    /**
     * Initializes this Entity instance attributes with the values found in $data.
     *
     * @param array $data the array of values to initialize this instance with
     *
     * @see \CBList\ModelBundle\Entity\Entity::__construct()
     */
    protected function hydrate(array $data)
    {
        if (array_key_exists('id', $data)) {
                $this->id = $data['id'];
        }
    }

    /**
     * Returns this entity's unique id.
     *
     * @return int this entity's id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Assert than an argument is a valid string.
     *
     * @param string $argument the argument to validate
     * @param string $message the optional message to pass to the exception if one is thrown
     *
     * @return void
     * @throws \InvalidArgumentException if the argument cannot be validated
     */
    public static function assertValidString(
            $argument, $message = 'Expected argument of type string, got '
    ) {
        if (!is_string($argument)) {
            throw new \InvalidArgumentException(
                    $message . gettype($argument)
            );
        }
    }

    /**
     * Asserts that an argument has a null value or is a valid string.
     *
     * @param string $argument the argument to validate
     *
     * @return void
     * @throws \InvalidArgumentException if the argument cannot be validated
     */
    public static function assertNullOrValidString($argument)
    {
        if (null !== $argument) {
            self::assertValidString(
                    $argument, 'Expected argument of type string or null value, got '
            );
        }
    }

    /**
     * Asserts that an argument has a valid, non empty string value.
     *
     * @param string $argument the argument to validate
     * @param string $message the optional message to pass to the exception if one is thrown
     *
     * @return void
     * @throws \InvalidArgumentException if the argument cannot be validated
     */
    public static function assertNonEmptyString(
            $argument, $message = 'Expected non empty string'
    ) {
        self::assertValidString($argument);
        if ($argument === '') {
            throw new \InvalidArgumentException($message);
        }
    }
}
