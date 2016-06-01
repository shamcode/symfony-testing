<?php

namespace Tests\AppBundle\Repository;

use Tests\AppBundle\BaseTest;
use AppBundle\Entity\Repository\ItemRepository;

class ItemRepositoryTest extends BaseTest
{
    public function testItemRepository()
    {
        /** @var ItemRepository $repo */
        $repo = $this->em->getRepository('AppBundle:Item');
        $this->assertEquals(3, count($repo->findAllActive()), 'Three active');
        $this->assertEquals(1, count($repo->findAllCompleted()), 'One completed');
    }
}
