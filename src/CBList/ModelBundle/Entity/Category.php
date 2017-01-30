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
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use CBList\ModelBundle\Entity\Report;

/**
 * Represents a report category.
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="CBList\ModelBundle\Repository\CategoryRepository")
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class Category extends \CBList\ModelBundle\Entity\Entity implements CBListEntity
{
    const MAX_LABEL_LENGTH = 255;

    /**
     * A collection of Report instances linked to this Category instance.
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Report", mappedBy="category")
     */
    private $reports;

    /**
     * The Category instance (unique, non null, non empty) label.
     *
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=Category::MAX_LABEL_LENGTH, unique=true)
     */
    private $label;

    /**
     * The Category instance description.
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * {@inheritDoc}
     */
    protected function hydrate(array $data)
    {
        parent::hydrate($data);

        if (array_key_exists('reports', $data)) {
            $this->setReports($data['reports']);
        }
        if (array_key_exists('label', $data)) {
            $this->setLabel($data['label']);
        }
        if (array_key_exists('description', $data)) {
            $this->setDescription($data['description']);
        }
    }

    /**
     * Link a Report instance to this Category.
     *
     * If the given report object is already linked
     * to this category the operation is skipped silently.
     *
     * @param Report $report the report to add to this category
     */
    public function addReport(Report $report)
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
        }
    }

    /**
     * Returns the Report instances related to this Category instance.
     *
     * @return Collection the collection of Report instances
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Sets the Report collection related to this Category instance.
     *
     * @param Collection $reports the collection of Report instances
     *
     * @return Category this Category instance
     */
    public function setReports(Collection $reports)
    {
        $this->reports = $reports;
        return $this;
    }

    /**
     * Returns this instance's unique label.
     *
     * @return string this instance's label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets this instance's unique label.
     *
     * @param string $label the new label for this instance
     *
     * @return Category this Category instance
     * @throws \InvalidArgumentException if $label isn't a valid label
     */
    public function setLabel($label)
    {
        $this->assertValidLabel($label);
        $this->label = $label;
        return $this;
    }

    /**
     * Returns this instance's description.
     *
     * @return string this instance's description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets this instance's description.
     *
     * @param string $description the new description for this instance
     *
     * @return Category this Category instance
     * @throws \InvalidArgumentException if $description isn't a null or a valid string
     */
    public function setDescription($description)
    {
        $this->assertNullOrValidString($description);
        $this->description = $description;
        return $this;
    }

    /**
     * Asserts that a given string is a valid label.
     *
     * A label is considered valid if it is a non null, non empty, valid string.
     *
     * @param string $label the label to validate
     * @throws \InvalidArgumentException if $label isn't a valid label
     */
    public static function assertValidLabel($label)
    {
        parent::assertNonEmptyString($label, 'Expected category label, got empty string');

        $length = strlen($label);
        if ($length > self::MAX_LABEL_LENGTH) {
            throw new \InvalidArgumentException(
                    'Invalid Category label length, max allowed length is: ' .
                    self::MAX_LABEL_LENGTH . "; argument's length was: " . $length
            );
        }
    }
}
