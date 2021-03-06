<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\SeoBundle\Tests\WebTest;

use Symfony\Cmf\Bundle\SeoBundle\Model\SeoPresentation;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;

/**
 * This test will cover all current frontend stuff.
 *
 * - title has to be a combination of the content title and the default one
 * - the description is the document description
 * - keywords, contain the default ones set in the sonat_seo section and the ones from doc
 * - canonical link has to exist
 *
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class SeoFrontendTest extends BaseTestCase
{
    /** @var  Client */
    private $client;

    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\SeoBundle\Tests\Resources\DataFixtures\Phpcr\LoadContentData',
        ));
        $this->client = $this->createClient();
    }

    /**
     * This test is without any setting in sonata_seo just cmf data.
     */
    public function testDefaultUsage()
    {
        $crawler = $this->client->request('GET', '/content/content-1');
        $res = $this->client->getResponse();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("Content 1")'));

        //test the title
        $titleCrawler = $crawler->filter('head > title');
        $this->assertEquals('Default | Title content 1', $titleCrawler->text());

        //test the meta tag entries
        $metaCrawler = $crawler->filter('head > meta')->reduce(function (Crawler $node) {
                $nameValue = $node->attr('name');

                return 'title' === $nameValue || 'description' === $nameValue ||'keywords' === $nameValue;
        });

        $actualMeta = $metaCrawler->extract('content', 'content');
        $expectedMeta = array(
            'testkey, content1, content',
            'Default | Title content 1',
            'Default description. Description of content 1.',
        );
        $this->assertEquals($expectedMeta, $actualMeta);

        //test the setting of canonical link
        $linkCrawler = $crawler->filter('head > link')->reduce(function (Crawler $node) {
            return SeoPresentation::ORIGINAL_URL_CANONICAL === $node->attr('rel');
        });
        $this->assertEquals('/to/original', $linkCrawler->eq(0)->attr('href'));
    }

    public function testExtractors()
    {
        $crawler = $this->client->request('GET', '/content/strategy-content');
        $res = $this->client->getResponse();

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("content of strategy test.")'));

        //test the title
        $titleCrawler = $crawler->filter('head > title');
        $this->assertEquals('Default | Strategy title', $titleCrawler->text());

        //test the meta tag entries
        $metaCrawler = $crawler->filter('head > meta')->reduce(function (Crawler $node) {
            $nameValue = $node->attr('name');

            return 'title' === $nameValue || 'description' === $nameValue ||'keywords' === $nameValue;
        });

        $actualMeta = $metaCrawler->extract('content', 'content');
        $expectedMeta = array(
            'testkey, test, key',
            'Default | Strategy title',
            'Default description. content of strategy test. ...',
        );
        $this->assertEquals($expectedMeta, $actualMeta);

        //test the setting of canonical link
        $linkCrawler = $crawler->filter('head > link')->reduce(function (Crawler $node) {
            return SeoPresentation::ORIGINAL_URL_CANONICAL === $node->attr('rel');
        });
        $this->assertEquals('/home', $linkCrawler->eq(0)->attr('href'));
    }
}
