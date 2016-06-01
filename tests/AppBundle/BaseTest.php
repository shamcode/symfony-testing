<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Loader as FixturesLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

abstract class BaseTest extends WebTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @beforeClass
     */
    public static function setUpTestClass()
    {
        self::bootKernel();
        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $loader = new FixturesLoader();
        $loader->loadFromFile('src/AppBundle/DataFixtures/ORM/LoadItemFixturesData.php');
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->em = $container->get('doctrine')->getManager();

        $loader = new FixturesLoader();
        $loader->loadFromFile('src/AppBundle/DataFixtures/ORM/LoadItemFixturesData.php');
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }
}
