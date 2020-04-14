<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Carbon\Carbon;
use Throwable;
use Vdlp\Redirect\Models\Redirect;

final class RedirectRule
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $matchType;

    /**
     * @var string
     */
    private $targetType;

    /**
     * @var string
     */
    private $fromUrl;

    /**
     * @var string
     */
    private $fromScheme;

    /**
     * @var string
     */
    private $toUrl;

    /**
     * @var string
     */
    private $toScheme;

    /**
     * @var string
     */
    private $cmsPage;

    /**
     * @var string
     */
    private $staticPage;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $requirements;

    /**
     * @var Carbon|null
     */
    private $fromDate;

    /**
     * @var Carbon|null
     */
    private $toDate;

    /**
     * @var array
     */
    private $placeholderMatches;

    /**
     * @var bool
     */
    private $ignoreQueryParameters;

    /**
     * @var bool
     */
    private $ignoreCase;

    /**
     * @var bool
     */
    private $ignoreTrailingSlash;

    public function __construct(array $attributes)
    {
        $this->ignoreQueryParameters = false;
        $this->ignoreCase = false;
        $this->ignoreTrailingSlash = false;

        foreach ($attributes as $key => $value) {
            $property = camel_case($key);

            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        try {
            $this->fromDate = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                substr($this->fromDate, 0, 10) . ' 00:00:00'
            );
        } catch (Throwable $e) {
            $this->fromDate = null;
        }

        try {
            $this->toDate = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                substr($this->toDate, 0, 10) . ' 00:00:00'
            );
        } catch (Throwable $e) {
            $this->toDate = null;
        }

        $this->requirements = json_decode((string) $this->requirements, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->requirements = [];
        }
    }

    public static function createWithModel(Redirect $model): RedirectRule
    {
        $attributes = $model->getAttributes();
        $attributes['requirements'] = json_encode($model->getAttribute('requirements'));

        return new self($attributes);
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getMatchType(): string
    {
        return (string) $this->matchType;
    }

    public function getTargetType(): string
    {
        return (string) $this->targetType;
    }

    public function getFromUrl(): string
    {
        return (string) $this->fromUrl;
    }

    public function getFromScheme(): string
    {
        return (string) $this->fromScheme;
    }

    public function getToUrl(): string
    {
        return (string) $this->toUrl;
    }

    public function getToScheme(): string
    {
        return (string) $this->toScheme;
    }

    public function getCmsPage(): string
    {
        return (string) $this->cmsPage;
    }

    public function getStaticPage(): string
    {
        return (string) $this->staticPage;
    }

    public function getStatusCode(): int
    {
        return (int) $this->statusCode;
    }

    public function getRequirements(): array
    {
        return (array) $this->requirements;
    }

    public function getFromDate(): ?Carbon
    {
        return $this->fromDate;
    }

    public function getToDate(): ?Carbon
    {
        return $this->toDate;
    }

    public function isExactMatchType(): bool
    {
        return $this->matchType === Redirect::TYPE_EXACT;
    }

    public function isPlaceholdersMatchType(): bool
    {
        return $this->matchType === Redirect::TYPE_PLACEHOLDERS;
    }

    public function isRegexMatchType(): bool
    {
        return $this->matchType === Redirect::TYPE_REGEX;
    }

    public function getPlaceholderMatches(): array
    {
        return (array) $this->placeholderMatches;
    }

    public function setPlaceholderMatches(array $placeholderMatches = []): RedirectRule
    {
        $this->placeholderMatches = $placeholderMatches;
        return $this;
    }

    public function isIgnoreQueryParameters(): bool
    {
        return (bool) $this->ignoreQueryParameters;
    }

    public function isIgnoreCase(): bool
    {
        return (bool) $this->ignoreCase;
    }

    public function isIgnoreTrailingSlash(): bool
    {
        return (bool) $this->ignoreTrailingSlash;
    }
}
