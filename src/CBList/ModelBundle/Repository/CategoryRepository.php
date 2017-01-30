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

namespace CBList\ModelBundle\Repository;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Repository\CBListRepository;

/**
 * CategoryRepository
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class CategoryRepository extends CBListRepository
{
    const EXPECTED_STRING_ARGUMENT_MESSAGE = 'Expected string argument, got ';

    const SERVICE_NAME = 'app.category-repository';

    const LABEL_FIELD = 'label';

    /**
     * Returns the Category instance with matching label, if any.
     *
     * @param string $label the label to search for in database
     *
     * @return Category the matching Category instance
     * @throws \InvalidArgumentException if $label isn't a valid string object
     */
    public function findOneByLabel($label)
    {
        $this->assertValidLabel($label);
        return $this->findOneBy(array(self::LABEL_FIELD => $label));
    }

    /**
     * Check that the given Category instance exists in this repository.
     *
     * @param Category $category the category instance to search for
     *
     * @return boolean true if the Category instance was found in database, false otherwise
     */
    public function exists(Category $category)
    {
        return
                $this->existsId($category->getId()) ||
                (
                        null !== $category->getLabel() &&
                        $this->existsLabel($category->getLabel())
                )
        ;
    }

    /**
     * Check that a Category instance with matching label exists in this repository.
     *
     * @param string $label the label to search for
     *
     * @return boolean true if a match was found, false otherwise
     */
    public function existsLabel($label)
    {
        $this->assertValidLabel($label);
        return null !== $this->findOneByLabel($label);
    }

    /**
     * Validates a Category label.
     *
     * @param string $label the label to validate
     *
     * @throws \InvalidArgumentException if $label isn't a valid, non null string
     */
    private function assertValidLabel($label)
    {
        if (!is_string($label)) {
            throw new \InvalidArgumentException(
                    self::EXPECTED_STRING_ARGUMENT_MESSAGE . gettype($label)
            );
        }
        if ($label === '') {
            throw new \InvalidArgumentException(
                    self::EXPECTED_STRING_ARGUMENT_MESSAGE . 'empty string'
            );
        }
    }
}
