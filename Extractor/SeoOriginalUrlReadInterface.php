<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\SeoBundle\Extractor;

/**
 * This interface is one of the ExtractorInterfaces to
 * get content properties for updating the SeoMetadata.
 *
 * If you want to have a content that is able to update its original URL for
 * the SeoMetadata on its own, you should implement this interface.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@onit-gmbh.de>
 */
interface SeoOriginalUrlReadInterface
{
    /**
     * The method returns the absolute URL as a string to redirect to or set to
     * the canonical link.
     *
     * @return string An absolute URL.
     */
    public function getSeoOriginalUrl();
}
