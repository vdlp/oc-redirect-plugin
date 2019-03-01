<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Cms\Classes\CmsCompoundObject;
use Event;
use Exception;
use Vdlp\Redirect\Models\Redirect;

/**
 * Class PageHandler
 *
 * @package Vdlp\Redirect\Classes
 */
class PageHandler
{
    /**
     * @var CmsCompoundObject
     */
    protected $page;

    /**
     * @param CmsCompoundObject $page
     */
    public function __construct(CmsCompoundObject $page)
    {
        $this->page = $page;
    }

    /**
     * Triggered before the Page is stored to filesystem.
     *
     * @return void
     */
    public function onBeforeUpdate()//: void
    {
        if ($this->page->getAttribute('is_hidden')) {
            return;
        }

        // Url hasn't change
        if (!$this->hasUrlChanged()) {
            return;
        }

        // Parameters and regex are not supported
        if ($this->newUrlContainsParams()) {
            return;
        }

        // Don't create a redirect loop; that would be silly ;-)
        if ($this->getNewUrl() === $this->getOriginalUrl()) {
            return;
        }

        $this->createRedirect();

        Event::fire('vdlp.redirect.changed');
    }

    /**
     * Triggered after a Page has been deleted.
     *
     * @return void
     * @throws Exception
     */
    public function onAfterDelete()//: void
    {
        Redirect::where($this->getTargetType(), '=', $this->page->getBaseFileName())
            ->where('system', '=', 1)
            ->delete();

        Redirect::where($this->getTargetType(), '=', $this->page->getBaseFileName())
            ->where('system', '=', 0)
            ->update([
                $this->getTargetType() => null,
                'is_enabled' => false,
            ]);

        Event::fire('vdlp.redirect.changed');
    }

    /**
     * @return bool
     */
    protected function hasUrlChanged(): bool
    {
        return array_key_exists('url', $this->page->getDirty());
    }

    /**
     * @return bool
     */
    protected function newUrlContainsParams(): bool
    {
        return strpos($this->getNewUrl(), ':') !== false;
    }

    /**
     * @return string
     */
    protected function getOriginalUrl(): string
    {
        return (string) $this->page->getOriginal('url');
    }

    /**
     * @return string
     */
    protected function getNewUrl(): string
    {
        $dirty = $this->page->getDirty();

        if (array_key_exists('url', $dirty)) {
            return $dirty['url'];
        }

        return (string) $this->page->getOriginal('url');
    }

    /**
     * @return string
     */
    protected function getTargetType(): string
    {
        return Redirect::TARGET_TYPE_CMS_PAGE;
    }

    /**
     * Create CMS page type
     *
     * @return void
     */
    protected function createRedirect()//: void
    {
        Redirect::create([
            'match_type' => Redirect::TYPE_EXACT,
            'target_type' => $this->getTargetType(),
            'from_url' => $this->getOriginalUrl(),
            'to_url' => null,
            $this->getTargetType() => $this->page->getBaseFileName(),
            'status_code' => 301,
            'is_enabled' => true,
            'system' => true,
        ]);
    }
}
