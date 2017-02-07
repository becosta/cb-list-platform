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

namespace CBList\ModelBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Exception\EntityMismatchException;
use CBList\ModelBundle\Exception\EntityNotModifiedException;
use CBList\ModelBundle\Repository\CategoryRepository;
use CBList\ModelBundle\Service\CBListEntityService;
use CBList\ModelBundle\Service\ReportService;

/**
 * CategoryService
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class CategoryService extends CBListEntityService
{

    const SERVICE_NAME = 'app.category-service';

    const LABEL_FIELD = 'label';

    public function __construct(
            EntityManager $entityManager, CategoryRepository $repository
    ) {
        parent::__construct($entityManager, $repository);
    }

    /**
     * Checks whether a Category instance exist in the repository or not.
     *
     * @param Category $category the Category instance to search for
     *
     * @return boolean true if the Category instance exists in the repository, false otherwise
     */
    public function exists(Category $category)
    {
        return $this->repository->exists($category);
    }

    /**
     * Creates a new Category instance constructed from the given arguments.
     *
     * @param string $label the mandatory label for the new Category
     * @param string $description the optional description for the new Category
     *
     * @return Category the new Category instance
     * @throws \InvalidArgumentException if $label isn't a valid string or is empty
     * @throws \InvalidArgumentException if $description isn't a valid string
     */
    public function create($label, $description = '')
    {
        $this->assertValidLabel($label);
        $this->assertStringArgument($description);
        return (new Category())->setLabel($label)->setDescription($description);
    }

    /**
     * Adds a Category instance to the database.
     *
     * The given Category instance must not exist
     * in database prior to calling this method
     * or an exception will be thrown.
     *
     * @param Category $category the Category instance to add to the database
     *
     * @throws Exception if $category is already present in database
     */
    public function add(Category $category)
    {
        // TODO: validate $category
        // TODO: verify that $category doesn't exists
        if (!$this->repository->existsLabel($category->getLabel())) {
            $this->entityManager->persist($category);
            $this->entityManager->flush($category);
        }
        // TODO: throw exception
    }

    /**
     * Updates a Category instance present in database with the given values.
     *
     * The instance passed to this method will be updated, flushed to the database
     * and then returned as is.
     * This method doesn't creates a copy of the passed in instance !
     *
     * @param Category $old the original Category instance
     * @param array $values the new values for the Category instance
     *
     * @return Category the modified Category instance
     * @throws EntityNotModifiedException if no update were needed
     */
    public function update(Category $old, array $values)
    {
        $hasUpdate = false;

        foreach ($values as $field => $value) {
            $getter = $this->getFieldGetterName($field);
            if ( is_callable(array($old, $getter)) && $old->{$getter}() === $value ) {
                continue;
            }

            $setter = $this->getFieldSetterName($field);
            if ( is_callable(array($old, $setter)) ) {
                $old->{$setter}($value);
                $hasUpdate = true;
            }
        }
        if (!$hasUpdate) {
            throw new EntityNotModifiedException();
        }

        $this->entityManager->persist($old);
        $this->entityManager->flush($old);
        return $old;
    }

    /**
     * Updates a Category instance in database matching $oldId with the given values.
     *
     * @param integer $oldId the id of the original Category instance
     * @param array $values the new values for the Category instance
     *
     * @return Category the modified Category instance
     * @throws EntityNotFoundException if no update were needed
     */
    public function updateById($oldId, array $values)
    {
        $oldInstance = $this->getCategory($oldId);

        if (null === $oldInstance) {
            throw new EntityNotFoundException();
        }
        return $this->update($oldInstance, $values);
    }

    /**
     * Replace the values of an existing Category instance with the values of a new one.
     *
     * Both the label and the description will be updated independently of their values.
     *
     * @param Category $old the old Category instance to replace
     * @param Category $new the new Category instance to put in place of the old one
     *
     * @return Category the modified Category instance
     * @throws EntityNotFoundException if $old doesn't exists in the repository
     * @throws EntityMismatchException if $new already exists in the repository and is different from $old
     */
    public function replace(Category $old, Category $new)
    {
        if (!$this->repository->exists($old)) {
            throw new EntityNotFoundException();
        }
        if (null !== $new->getId() && $old->getId() != $new->getId()) {
            throw new EntityMismatchException();
        }

        $old->setLabel($new->getLabel())->setDescription($new->getDescription());
        $this->entityManager->persist($old);
        $this->entityManager->flush($old);
        return $old;
    }

    /**
     * Replace the values of an existing Category instance with the values of a new one.
     *
     * Both the label and the description will be updated independently of their values.
     *
     * @param integer $oldId the id of the Category instance to replace
     * @param Category $new the new Category instance to put in place of the old one
     *
     * @return Category the modified Category instance
     * @throws EntityNotFoundException if $oldID isn't found in the repository
     * @throws EntityMismatchException if $new already exists in the repository and is different from $old
     */
    public function replaceById($oldId, Category $newInstance)
    {
        $oldInstance = $this->getCategory($oldId);

        if (null === $oldInstance) {
            throw new EntityNotFoundException();
        }
        return $this->replace($oldInstance, $newInstance);
    }

    /**
     * Deletes the given Category instance.
     *
     * Note that any report associated with this category
     * will be kept in database and linked to
     * a special category with label "deleted-category".
     *
     * @param Category $category the Category instance to delete
     * @param ReportService $reportService
     * @return Collection a collection of report that have been moved from $category to the special category
     */
    public function delete(Category $category, ReportService $reportService)
    {
        $deletedCategory = $this->getDeletedCategory();
        $modifiedReports = $category->getReports();
        $category->setReports(new ArrayCollection());

        foreach ($modifiedReports as $report) {
            $report->setCategory($deletedCategory);
        }
        $this->entityManager->remove($category);
        $this->saveAll();

        return $modifiedReports;
    }

    /**
     * Returns the whole collection of Category instances.
     *
     * @return array the Category instances
     */
    public function getCategories()
    {
        return $this->repository->findAll();
    }

    /**
     * Returns the Category instance with id matching $id.
     *
     * Can return null.
     *
     * @param integer $id the id of the Category instance to return
     *
     * @return Category the Category instance or null
     *
     * @throws \InvalidArgumentException if $id isn't a valid integer
     */
    public function getCategory($id)
    {
        $this->assertValidInteger($id);
        return $this->repository->find($id);
    }

    /**
     * Returns the Category instance with label matching $label.
     *
     * Can return null.
     *
     * @param string $label the id of the Category instance to return
     *
     * @return Category the Category instance or null
     *
     * @throws \InvalidArgumentException if $label isn't a valid string or is empty
     */
    public function getCategoryByLabel($label)
    {
        $this->assertValidLabel($label);
        return $this->repository->findOneBy(array(self::LABEL_FIELD => $label));
    }


    /**
     *
     * @return Category
     */
    private function getDeletedCategory()
    {
        $category = $this->getCategoryByLabel('deleted-category');
        if (null === $category) {
            $category = $this->create(
                    'deleted-category',
                    'The original category associated with these reports was deleted'
            );
            $this->add($category);
        }
        return $category;

    }

    private function getFieldSetterName($fieldName) {
        return 'set' . ucfirst(strtolower($fieldName));
    }

    private function getFieldGetterName($fieldName) {
        return 'get' . ucfirst(strtolower($fieldName));
    }

    private function assertValidLabel($label)
    {
        Category::assertValidLabel($label);
    }
}
