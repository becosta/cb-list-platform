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

namespace CBList\ModelBundle\DataFixtures\ORM\Tests;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Entity\Report;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class LoadReportData extends AbstractFixture implements OrderedFixtureInterface
{
    const REPORT_1_DATE_SUBMITTED = '2017-01-10T10:00:24+0100';
    const REPORT_1_NAME = 'report-instance-1';
    const REPORT_1_SENDER_INET_ADDRESS = '127.0.0.1';

    const REPORT_2_DATE_SUBMITTED = '2017-01-12T08:25:00+0100';
    const REPORT_2_NAME = 'report-instance-2';
    const REPORT_2_SENDER_INET_ADDRESS = '192.168.0.1';

    const REPORT_3_DATE_SUBMITTED = '2017-01-12T12:03:17+0100';
    const REPORT_3_NAME = 'report-instance-3';
    const REPORT_3_SENDER_INET_ADDRESS = '192.168.10.20';

    const REPORT_4_DATE_SUBMITTED = '2017-02-02T23:58:55+0100';
    const REPORT_4_NAME = 'report-instance-4';
    const REPORT_4_SENDER_INET_ADDRESS = '10.1.1.1';

    public function load(ObjectManager $manager)
    {
        $this->addReportInstance(
                $manager,
                self::REPORT_1_NAME, new \DateTime(self::REPORT_1_DATE_SUBMITTED),
                new InetAddress(self::REPORT_1_SENDER_INET_ADDRESS),
                $this->getReference(LoadCategoryData::SSH_BRUTEFORCE_CATEGORY_NAME),
                $this->getReference(LoadHostData::HOST_1_NAME)
        );
        $this->addReportInstance(
                $manager,
                self::REPORT_2_NAME, new \DateTime(self::REPORT_2_DATE_SUBMITTED),
                new InetAddress(self::REPORT_2_SENDER_INET_ADDRESS),
                $this->getReference(LoadCategoryData::APACHE_BADBOTS_CATEGORY_NAME),
                $this->getReference(LoadHostData::HOST_2_NAME)
        );
        $this->addReportInstance(
                $manager,
                self::REPORT_3_NAME, new \DateTime(self::REPORT_3_DATE_SUBMITTED),
                new InetAddress(self::REPORT_3_SENDER_INET_ADDRESS),
                $this->getReference(LoadCategoryData::SSH_DDOS_CATEGORY_NAME),
                $this->getReference(LoadHostData::HOST_3_NAME)
        );
        $this->addReportInstance(
                $manager,
                self::REPORT_4_NAME, new \DateTime(self::REPORT_4_DATE_SUBMITTED),
                new InetAddress(self::REPORT_4_SENDER_INET_ADDRESS),
                $this->getReference(LoadCategoryData::SSH_BRUTEFORCE_CATEGORY_NAME),
                $this->getReference(LoadHostData::HOST_4_NAME)
        );
        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }

    private function addReportInstance(
            ObjectManager $manager, $name, \DateTime $dateSubmitted,
            InetAddress $senderInetAddress, Category $category, Host $host
    ) {
        $report = new Report(array
                (
                    'dateSubmitted'     => $dateSubmitted,
                    'senderInetAddress' => $senderInetAddress,
                    'category'          => $category,
                    'host'              => $host,
                )
        );
        $manager->persist($report);
        $this->addReference($name, $report);
    }
}
