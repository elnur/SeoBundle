<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\SeoBundle\Tests\Resources\Document;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoDescriptionReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoOriginalUrlReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Extractor\SeoTitleReadInterface;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoAwareInterface;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata;

class AllStrategiesDocument implements
    SeoAwareInterface,
    SeoDescriptionReadInterface,
    SeoTitleReadInterface,
    SeoOriginalUrlReadInterface
{

    protected $seoMetadata;

    public function __construct()
    {
        $this->seoMetadata = new SeoMetadata();
    }

    /**
     * Provide a description of this page to be used in SEO context.
     *
     * @return string
     */
    public function getSeoDescription()
    {
        return 'Test Description.';
    }

    /**
     * Provide the original url of this page to be used in SEO context.
     *
     * @return Route the original route.
     */
    public function getSeoOriginalUrl()
    {
        return '/test-route';
    }

    /**
     * Provide a title of this page to be used in SEO context.
     *
     * @return string
     */
    public function getSeoTitle()
    {
        return 'Test title';
    }

    /**
     * To let a content be seo aware means in the SeoBundle to serve the SeoMetadata.
     * This SeoMetadata contains the information to fill some meta tags or has
     * the information of the original url of the content.
     *
     * @return SeoMetadata
     */
    public function getSeoMetadata()
    {
        return $this->seoMetadata;
    }

    public function setSeoMetadata($metadata)
    {
    }
}
