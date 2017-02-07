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

use Doctrine\ORM\EntityManager;
use DateTime;
use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Report;
use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Exception\EntityNotFoundException;
use CBList\ModelBundle\Repository\ReportRepository;
use CBList\ModelBundle\Service\CategoryService;
use CBList\ModelBundle\Service\CBListEntityService;
use CBList\ModelBundle\Service\HostService;

/**
 * Description of ReportService
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class ReportService extends CBListEntityService
{

    const SERVICE_NAME = 'app.report-service';

    private $hostService;

    private $categoryService;

    public function __construct(
            EntityManager $entityManager, ReportRepository $repository,
            HostService $hostService, CategoryService $categoryService
    ) {
        parent::__construct($entityManager, $repository);
        $this->hostService = $hostService;
        $this->categoryService = $categoryService;
    }

    /**
     * Creates a new Report instance constructed from the given arguments.
     *
     * @param Host $host the host concerned by this report
     * @param Category $category the category this report belongs to
     * @param InetAddress $senderInetAddress the report's sender network address
     *
     * @return Report the new Report instance
     */
    public function create(
            Host $host, Category $category,
            InetAddress $senderInetAddress,
            \DateTime $dateSubmitted = null
    ) {
        if (null === $dateSubmitted) {
            $dateSubmitted = new \DateTime('NOW');
        }
        $report = new Report(array
                (
                    'host'              => $host,
                    'category'          => $category,
                    'senderInetAddress' => $senderInetAddress,
                    'dateSubmitted'     => $dateSubmitted,
                )
        );
        return $report;
    }

    /**
     * Creates a new Report instance constructed from the given arguments.
     *
     * @param Category $category the category this report belongs to
     * @param InetAddress $hostInetAddress the network address of the host concerned by this report
     * @param InetAddress $senderInetAddress the report's sender network address
     *
     * @return Report the new Report instance
     */
    public function createWithCategoryInstance(
            Category $category, InetAddress $hostInetAddress,
            InetAddress $senderInetAddress, \DateTime $dateSubmitted = null
    ) {
        $host = $this->hostService->getHostByInetAddress($hostInetAddress);
        if (null === $host) {
            // TODO: persist host in cascade
            $host = $this->hostService->create($hostInetAddress);
            $this->hostService->add($host);
        }
        return $this->create($host, $category, $senderInetAddress, $dateSubmitted);
    }

    /**
     * Creates a new Report instance constructed from the given arguments.
     *
     * @param integer $categoryId the id of the category this report belongs to
     * @param InetAddress $hostInetAddress the network address of the host concerned by this report
     * @param InetAddress $senderInetAddress the report's sender network address
     *
     * @return Report the new Report instance
     */
    public function createWithCategoryId(
            $categoryId, InetAddress $hostInetAddress,
            InetAddress $senderInetAddress, \DateTime $dateSubmitted = null
    ) {
        $category = $this->categoryService->getCategory($categoryId);
        if (null === $category) {
            throw new EntityNotFoundException(
                    'Category id: ' . $categoryId . ' not found.' .
                    "can't continue without a valid report category"
            );
        }
        return $this->createWithCategoryInstance(
                $category, $hostInetAddress, $senderInetAddress, $dateSubmitted
        );
    }

    /**
     * Creates a new Report instance constructed from the given arguments.
     *
     * @param string $categoryLabel the unique label of the category this report belongs to
     * @param InetAddress $hostInetAddress the network address of the host concerned by this report
     * @param InetAddress $senderInetAddress the report's sender network address
     *
     * @return Report the new Report instance
     */
    public function createWithCategoryLabel(
            $categoryLabel, InetAddress $hostInetAddress,
            InetAddress $senderInetAddress, \DateTime $dateSubmitted = null
    ) {

        $category = $this->categoryService->getCategoryByLabel($categoryLabel);
        if (null === $category) {
            // TODO: persist category in cascade
            $category = $this->categoryService->create($categoryLabel);
            $this->categoryService->add($category);
        }
        return $this->createWithCategoryInstance(
                $category, $hostInetAddress, $senderInetAddress, $dateSubmitted
        );
    }

    /**
     * Adds a Report instance to the database.
     *
     * The given Report instance must not exist
     * in database prior to calling this method
     * or an exception will be thrown.
     *
     * @param Report $report the Report instance to add to the database
     *
     * @throws Exception if $report is already present in database
     */
    public function add(Report $report)
    {
        if (!$this->repository->exists($report)) {
            $this->entityManager->persist($report);
            $this->entityManager->flush($report);
        }
    }

    /**
     * Returns the whole collection of Report instances.
     *
     * @return array the Report instances
     */
    public function getReports()
    {
        return $this->repository->findAll();
    }
}
