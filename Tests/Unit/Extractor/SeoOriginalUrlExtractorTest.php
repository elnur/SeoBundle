<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Unit\Extractor;

use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoOriginalUrlExtractor;

class SeoOriginalUrlExtractorTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->extractor = new SeoOriginalUrlExtractor();
        $this->extractMethod = 'getSeoOriginalUrl';
        $this->metadataMethod = 'setOriginalUrl';
    }

    public function getSupportsData()
    {
        return array(
            array($this->getMock('Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoOriginalUrlReadInterface')),
            array($this->getMock('Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoDescriptionReadInterface'), false),
            array($this->getMock('Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoOriginalRouteReadInterface'), false),
            array($this->getMock('Symfony\Cmf\Bundle\SeoBundle\Model\SeoAwareInterface'), false),
        );
    }
}
