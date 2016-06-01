<?php

namespace PlacetGroupManagerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadItemFixturesData extends AbstractFixture implements OrderedFixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $fixtures = [
      __DIR__.'/../fixtures/item.yml',
    ];
    Fixtures::load($fixtures, $manager, ['providers' => [$this]]);
  }

  /**
   * Order position for fixtures loading
   *
   * @return int
   */
  public function getOrder()
  {
    return 1;
  }
}
