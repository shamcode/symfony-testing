<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\BaseTest;

class DefaultControllerTest extends BaseTest
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        // smoke test
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $trItems = $crawler->filter('#wrapper table tbody tr');

        $this->assertEquals(4, $trItems->count(), 'Total 4 items (row)');
        $tdItems = [];
        foreach ($trItems as $tr) {
            $tdItems[] = $tr->firstChild->nodeValue;
        }

        $occurrences = array_count_values($tdItems);
        $this->assertEquals(3, $occurrences['open'], 'Three item has status \'open\'');
        $this->assertEquals(1, $occurrences['close'], 'One item has status \'close\'');

        $this->assertEquals(4, (int)$crawler->filter('#wrapper table tfoot td span')->text(), 'Total 4 items');

        static::checkNavBar('/', ['Active', 'Completed']);
    }

    public function testActiveList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/active');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $trItems = $crawler->filter('#wrapper table tbody tr');
        $this->assertEquals(3, $trItems->count(), 'Total 3 items (row)');
        $tdItems = [];
        foreach ($trItems as $tr) {
            $tdItems[] = $tr->firstChild->nodeValue;
        }

        $occurrences = array_count_values($tdItems);
        $this->assertEquals(3, $occurrences['open'], 'Three item has status \'open\'');

        $this->assertEquals(3, (int)$crawler->filter('#wrapper table tfoot td span')->text(), 'Total 3 items');

        static::checkNavBar('/active', ['All', 'Completed']);
    }

    public function testCompletedList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/completed');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $trItems = $crawler->filter('#wrapper table tbody tr');
        $this->assertEquals(1, $trItems->count(), 'Total 1 items (row)');
        $tdItems = [];
        foreach ($trItems as $tr) {
            $tdItems[] = $tr->firstChild->nodeValue;
        }

        $occurrences = array_count_values($tdItems);
        $this->assertEquals(1, $occurrences['close'], 'Three item has status \'close\'');

        $this->assertEquals(1, (int)$crawler->filter('#wrapper table tfoot td span')->text(), 'Total 3 items');

        static::checkNavBar('/completed', ['All', 'Active']);
    }

    public function testCreate()
    {
        $client = static::createClient();

        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('#wrapper table ~ form button[type="submit"]')->form([
            'item[status]' => 'open',
            'item[text]' => 'new item'
        ]);
        $crawler = $client->submit($form);

        // check in db
        $item = $this->em->getRepository('AppBundle:Item')->findOneBy([
            'status' => 'open',
            'text' => 'new item'
        ]);
        $this->assertNotNull($item, 'Item saved');

        // check in page
        $this->assertEquals(1, $crawler->filter('table tbody td:contains("new item")')->count(), 'Item created');
    }

    public function testClose()
    {
        $client = static::createClient();

        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $link = $crawler
            ->filter('#wrapper table tbody tr')
            ->selectLink('Close')
            ->first();
        $itemText = $link
            ->parents()
            ->filter('td')
            ->eq(2)
            ->text();
        $crawler = $client->click($link->link());

        // check in db
        $item = $this->em->getRepository('AppBundle:Item')->findOneBy([
            'status' => 'close',
            'text' => $itemText
        ]);
        $this->assertNotNull($item, 'Item close (db)');

        // check in page
        $this->assertEquals(
            1,
            $crawler
                ->filter(sprintf('#wrapper table tbody tr td', $itemText))
                ->reduce(function ($node) use ($itemText) {
                    return $itemText == $node->text();
                })
                ->parents()
                ->first()
                ->filter('td:contains("close")')
                ->count(),
            'Item close'
        );
    }

    /**
     * Test nav-bar links
     * @param string $start
     * @param array $linksText
     */
    protected static function checkNavBar($start, $linksText)
    {
        $client = static::createClient();
        foreach ($linksText as $linkText) {
            $crawler = $client->request('GET', $start);
            $link = $crawler->filter('nav')->selectLink($linkText)->link();
            $client->click($link);
            self::assertEquals(
                200,
                $client->getResponse()->getStatusCode(),
                sprintf('Navigation bar item \'%s\' on page \'%s\'', $linkText, $start)
            );
        }
    }
}
