<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuoteControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/quote');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("quote")')->count()
        );
    }

    public function testTopic()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/quote/life');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("life")')->count()
        );
    }
}