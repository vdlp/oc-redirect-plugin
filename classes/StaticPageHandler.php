<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Models\Redirect;

final class StaticPageHandler extends PageHandler
{
    /**
     * {@inheritDoc}
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function hasUrlChanged(): bool
    {
        return $this->getNewUrl() !== $this->getOriginalUrl();
    }

    /**
     * {@inheritDoc}
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getOriginalUrl(): string
    {
        $viewBag = $this->page->getOriginal('viewBag');

        if (array_key_exists('url', $viewBag)) {
            return $viewBag['url'];
        }

        return '';
    }

    /**
     * {@inheritDoc}
     * @noinspection PhpMissingParentCallCommonInspection
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

    /**
     * {@inheritDoc}
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getTargetType(): string
    {
        return Redirect::TARGET_TYPE_STATIC_PAGE;
    }
}
