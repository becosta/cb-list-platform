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

namespace tests\CBList\ModelBundle\Repository;

use Doctrine\ORM\EntityManager;

use CBList\Common\CBListKernelTestCase;
use CBList\ModelBundle\DataFixtures\ORM\Tests\LoadCategoryData;
use CBList\ModelBundle\DataFixtures\ORM\Tests\LoadHostData;
use CBList\ModelBundle\DataFixtures\ORM\Tests\LoadReportData;
use CBList\ModelBundle\Entity\Report;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class ReportRepositoryTest extends CBListKernelTestCase
{
    const REPORT_REPOSITORY = 'CBListModelBundle:Report';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addFixture(new LoadCategoryData());
        $this->addFixture(new LoadHostData());
        $this->addFixture(new LoadReportData());
        $this->executeFixtures();

        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager()
        ;
    }

    public function testExists()
    {
        $repository = $this->getReportRepository();

        $reports = $repository->findAll();
        foreach ($reports as $report) {
            $this->assertTrue($repository->exists($report));
            $this->assertTrue($repository->exists(
                    new Report( array('id' => $report->getId()) )
            ));
            $this->assertTrue($repository->exists(
                    new Report( array
                        (
                            'id'                => $report->getId(),
                            'dateSubmitted'     => $report->getDateSubmitted(),
                            'senderInetAddress' => $report->getSenderInetAddress(),
                            'category'          => $report->getCategory(),
                            'host'              => $report->getHost(),
                        )
                    )
            ));
            $this->assertFalse($repository->exists(
                    new Report( array
                        (
                            'id'                => -($report->getId()),
                            'dateSubmitted'     => $report->getDateSubmitted(),
                            'senderInetAddress' => $report->getSenderInetAddress(),
                            'category'          => $report->getCategory(),
                            'host'              => $report->getHost(),
                        )
                    )
            ));            
            $this->assertFalse($repository->exists(
                    new Report( array
                        (
                            'id'                => -1,
                            'dateSubmitted'     => $report->getDateSubmitted(),
                            'senderInetAddress' => $report->getSenderInetAddress(),
                            'category'          => $report->getCategory(),
                            'host'              => $report->getHost(),
                        )
                    )
            ));
            $this->assertFalse($repository->exists(
                    new Report( array
                        (
                            'dateSubmitted'     => $report->getDateSubmitted(),
                            'senderInetAddress' => $report->getSenderInetAddress(),
                            'category'          => $report->getCategory(),
                            'host'              => $report->getHost(),
                        )
                    )
            ));
        }
        $this->assertFalse($repository->exists(new Report()));
    }

    public function testNonNullValue()
    {
        $repository = $this->getReportRepository();
        $reports = $repository->findAll();

        $this->assertNotNull($reports);
        $this->assertEquals(4, count($reports));

        foreach ($reports as $report) {
            $this->assertNotNull($report->getDateSubmitted());
            $this->assertNotNull($report->getSenderInetAddress());
            $this->assertNotNull($report->getCategory());
            $this->assertNotNull($report->getHost());
        }
    }

    private function getReportRepository()
    {
        return $this->em->getRepository(self::REPORT_REPOSITORY);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
