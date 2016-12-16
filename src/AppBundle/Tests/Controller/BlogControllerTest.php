<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

    public function testPost()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/post');
    }

    public function testCategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/category');
    }

}
