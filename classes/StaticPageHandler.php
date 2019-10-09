<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Models\Redirect;

/**
 * Class StaticPageHandler
 *
 * @package Vdlp\Redirect\Classes
 */
class StaticPageHandler extends PageHandler
{
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * {@inheritDoc}
     */
    protected function hasUrlChanged(): bool
    {
        return $this->getNewUrl() !== $this->getOriginalUrl();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * {@inheritDoc}
     */
    protected function getOriginalUrl(): string
    {
        $viewBag = $this->page->getOriginal('viewBag');

        if (array_key_exists('url', $viewBag)) {
            return $viewBag['url'];
        }

        return '';
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * {@inheritDoc}
     */
    protected function getNewUrl(): string
    {
        $dirty = $this->page->getDirty();

        if (array_key_exists('viewBag', $dirty)
            && array_key_exists('url', $dirty['viewBag'])
        ) {
            return $dirty['viewBag']['url'];
        }

        return $this->getOriginalUrl();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * {@inheritDoc}
     */
    protected function getTargetType(): string
    {
        return Redirect::TARGET_TYPE_STATIC_PAGE;
    }
}
