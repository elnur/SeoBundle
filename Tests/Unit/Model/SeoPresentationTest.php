<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Unit\Model;

use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Cmf\Bundle\SeoBundle\DependencyInjection\SeoConfigValues;
use Symfony\Cmf\Bundle\SeoBundle\Tests\Resources\Document\AllStrategiesDocument;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoDescriptionExtractor;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoOriginalUrlExtractor;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoTitleExtractor;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoPresentation;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This test will cover the behavior of the SeoPresentation Model
 * This model is responsible for putting the SeoMetadata into
 * sonatas PageService.
 *
 */
class SeoPresentationTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @var SeoPresentation
     */
    private $seoPresentation;

    /**
     * @var SeoPage
     */
    private $pageService;

    /**
     * @var SeoMetadata
     */
    private $seoMetadata;

    /**
     * @var TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $translator;

    private $document;
    /**
     * @var SeoConfigValues
     */
    private $configValues;

    public function setUp()
    {
        $this->pageService = new SeoPage();
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->configValues = new SeoConfigValues();
        $this->configValues->setDescription('default_description');
        $this->configValues->setTitle('default_title');
        $this->configValues->setOriginalUrlBehaviour(SeoPresentation::ORIGINAL_URL_CANONICAL);
        $this->configValues->setTranslationDomain(null);

        $this->seoPresentation = new SeoPresentation(
            $this->pageService,
            $this->translator,
            $this->configValues
        );

        $this->seoMetadata = new SeoMetadata();

        // create the mock for the document
        $this->document = $this->getMock('Symfony\Cmf\Bundle\SeoBundle\Tests\Resources\Document\SeoAwareContent');
        $this->document
            ->expects($this->any())
           ->method('getSeoMetadata')
           ->will($this->returnValue($this->seoMetadata))
        ;
    }

    public function tearDown()
    {
        unset($this->seoMetadata, $this->configValues);
    }

    public function testDefaultTitle()
    {
        $this->seoMetadata->setTitle('Title test');
        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('default_title')
            ->will($this->returnValue('Title test | Default Title'))
        ;
        $this->seoPresentation->updateSeoPage($this->document);

        $actualTitle = $this->pageService->getTitle();
        $this->assertEquals('Title test | Default Title', $actualTitle);
    }

    public function testContentTitle()
    {
        $this->seoMetadata->setTitle('Content title');
        $this->configValues->setTitle(null);
        $this->seoPresentation->updateSeoPage($this->document);

        $actualTitle = $this->pageService->getTitle();
        $this->assertEquals('Content title', $actualTitle);
    }

    public function testDefaultDescription()
    {
        $this->seoMetadata->setMetaDescription('Test description.');
        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('default_description')
            ->will($this->returnValue('Default Description. Test description.'))
        ;
        $this->seoPresentation->updateSeoPage($this->document);

        $metas = $this->pageService->getMetas();
        $actualDescription = $metas['name']['description'][0];
        $this->assertEquals('Default Description. Test description.', $actualDescription);
    }

    public function testContentDescription()
    {
        $this->seoMetadata->setMetaDescription('Content description.');
        $this->configValues->setDescription(null);
        $this->seoPresentation->updateSeoPage($this->document);

        $metas = $this->pageService->getMetas();
        $actualDescription = $metas['name']['description'][0];
        $this->assertEquals('Content description.', $actualDescription);
    }

    public function testSettingKeywordsToSeoPage()
    {
        $this->seoMetadata->setMetaKeywords('key1, key2');
        //to set it here is the same as it was set in the sonata_seo settings
        $this->pageService->addMeta('name', 'keywords', 'default, other');
        $this->seoPresentation->updateSeoPage($this->document);
        $keywords = $this->pageService->getMetas();
        $this->assertEquals(
            'default, other, key1, key2',
            $keywords['name']['keywords'][0]
        );
    }

    public function testStrategies()
    {
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->will($this->returnValue('translation strategy test'))
        ;

        $seoPresentation = new SeoPresentation($this->pageService, $this->translator, $this->configValues);
        $seoPresentation->addExtractor(new SeoOriginalUrlExtractor());
        $seoPresentation->addExtractor(new SeoTitleExtractor());
        $seoPresentation->addExtractor(new SeoDescriptionExtractor());
        $seoPresentation->updateSeoPage(new AllStrategiesDocument());

        $metas = $this->pageService->getMetas();
        $actualDescription = $metas['name']['description'][0];
        $this->assertEquals('translation strategy test', $actualDescription);

        $this->assertEquals('translation strategy test', $this->pageService->getTitle());
        $this->assertEquals('/test-route', $this->pageService->getLinkCanonical());

        $this->assertFalse($seoPresentation->getRedirectResponse());
    }

    public function testRedirect()
    {
        $this->configValues->setOriginalUrlBehaviour(SeoPresentation::ORIGINAL_URL_REDIRECT);
        $seoPresentation = new SeoPresentation($this->pageService, $this->translator, $this->configValues);
        $this->seoMetadata->setOriginalUrl('/redirect/target');

        $seoPresentation->updateSeoPage($this->document);
        $redirect = $seoPresentation->getRedirectResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redirect);
        $this->assertEquals('/redirect/target', $redirect->getTargetUrl());
    }
}
