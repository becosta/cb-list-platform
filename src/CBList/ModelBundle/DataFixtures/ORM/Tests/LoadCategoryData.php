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

use CBList\ModelBundle\Entity\Category;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    const APACHE_BADBOTS_CATEGORY_DESCRIPTION   = 'apache badbots reports';

    const APACHE_BADBOTS_CATEGORY_LABEL         = 'apache-badbots';

    const APACHE_BADBOTS_CATEGORY_NAME          = 'apache-badbots-category';

    const SSH_BRUTEFORCE_CATEGORY_DESCRIPTION   = 'ssh password bruteforce reports';

    const SSH_BRUTEFORCE_CATEGORY_LABEL         = 'ssh-bruteforce';

    const SSH_BRUTEFORCE_CATEGORY_NAME          = 'ssh-bruteforce-category';

    const SSH_DDOS_CATEGORY_DESCRIPTION         = 'ssh ddos reports';

    const SSH_DDOS_CATEGORY_LABEL               = 'ssh-ddos';

    const SSH_DDOS_CATEGORY_NAME                = 'ssh-ddos-category';

    public function load(ObjectManager $manager)
    {
        $this->addCategoryInstance(
                $manager,
                self::APACHE_BADBOTS_CATEGORY_NAME,
                self::APACHE_BADBOTS_CATEGORY_LABEL,
                self::APACHE_BADBOTS_CATEGORY_DESCRIPTION
        );
        $this->addCategoryInstance(
                $manager,
                self::SSH_BRUTEFORCE_CATEGORY_NAME,
                self::SSH_BRUTEFORCE_CATEGORY_LABEL,
                self::SSH_BRUTEFORCE_CATEGORY_DESCRIPTION
        );
        $this->addCategoryInstance(
                $manager,
                self::SSH_DDOS_CATEGORY_NAME,
                self::SSH_DDOS_CATEGORY_LABEL,
                self::SSH_DDOS_CATEGORY_DESCRIPTION
        );
        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }

    private function addCategoryInstance(
            ObjectManager $manager, $name, $label, $description
    ) {
        $category = new Category(array('label' => $label, 'description' => $description));
        $manager->persist($category);
        $this->addReference($name, $category);
    }
}
