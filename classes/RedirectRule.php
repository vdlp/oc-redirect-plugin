<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use JsonException;
use Vdlp\Redirect\Models\Redirect;

final class RedirectRule
{
    private int $id;
    private string $matchType;
    private string $targetType;
    private string $fromUrl;
    private string $fromScheme;
    private string $toUrl;
    private string $toScheme;
    private string $cmsPage;
    private string $staticPage;
    private int $statusCode;
    private array $requirements = [];
    private ?Carbon $fromDate = null;
    private ?Carbon $toDate = null;
    private array $placeholderMatches = [];
    private array $pregMatchMatches = [];
    private bool $ignoreQueryParameters;
    private bool $ignoreCase;
    private bool $ignoreTrailingSlash;
    private bool $keepQuerystring;

    public function __construct(array $attributes)
    {
        $this->id = (int) ($attributes['id'] ?? null);
        $this->matchType = (string) ($attributes['match_type'] ?? null);
        $this->targetType = (string) ($attributes['target_type'] ?? null);
        $this->fromUrl = (string) ($attributes['from_url'] ?? null);
        $this->fromScheme = (string) ($attributes['from_scheme'] ?? null);
        $this->toUrl = (string) ($attributes['to_url'] ?? null);
        $this->toScheme = (string) ($attributes['to_scheme'] ?? null);
        $this->cmsPage = (string) ($attributes['cms_page'] ?? null);
        $this->staticPage = (string) ($attributes['static_page'] ?? null);
        $this->statusCode = (int) ($attributes['status_code'] ?? null);

        try {
            $requirements = $attributes['requirements'] ?? null;

            if (!is_string($requirements)) {
                $requirements = '[]';
            }

            $this->requirements = json_decode($requirements, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            // @ignoreException
            $this->requirements = [];
        }

        if (
            isset($attributes['from_date'])
            && is_string($attributes['from_date'])
            && $attributes['from_date'] !== ''
        ) {
            try {
                $date = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    substr($attributes['from_date'], 0, 10) . ' 00:00:00'
                );

                $this->fromDate = $date === false ? null : $date;
            } catch (InvalidFormatException) {
                // @ignoreException
                $this->fromDate = null;
            }
        }

        if (
            isset($attributes['to_date'])
            && is_string($attributes['to_date'])
            && $attributes['to_date'] !== ''
        ) {
            try {
                $date = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    substr($attributes['to_date'], 0, 10) . ' 00:00:00'
                );

                $this->toDate = $date === false ? null : $date;
            } catch (InvalidFormatException) {
                // @ignoreException
                $this->toDate = null;
            }
        }

        if ($attributes['ignore_query_parameters'] === false) {
            $this->keepQuerystring = false;
        } else {
            $this->keepQuerystring = (bool) ($attributes['keep_querystring'] ?? false);
        }

        $this->ignoreQueryParameters = (bool) ($attributes['ignore_query_parameters'] ?? false);
        $this->ignoreCase = (bool) ($attributes['ignore_case'] ?? false);
        $this->ignoreTrailingSlash = (bool) ($attributes['ignore_trailing_slash'] ?? false);
    }

    public static function createWithModel(Redirect $model): RedirectRule
    {
        $attributes = $model->getAttributes();
        $requirements = $model->getAttribute('requirements');

        if ($requirements === '' || $requirements === null) {
            $requirements = [];
        }

        try {
            $attributes['requirements'] = json_encode($requirements, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            // @ignoreException
            $attributes['requirements'] = '[]';
        }

        return new self($attributes);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMatchType(): string
    {
        return $this->matchType;
    }

    public function getTargetType(): string
    {
        return $this->targetType;
    }

    public function getFromUrl(): string
    {
        return $this->fromUrl;
    }

    public function getFromScheme(): string
    {
        return $this->fromScheme;
    }

    public function getToUrl(): string
    {
        return $this->toUrl;
    }

    public function getToScheme(): string
    {
        return $this->toScheme;
    }

    public function getCmsPage(): string
    {
        return $this->cmsPage;
    }

    public function getStaticPage(): string
    {
        return $this->staticPage;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
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
        return $this->placeholderMatches;
    }

    public function setPlaceholderMatches(array $placeholderMatches = []): self
    {
        $this->placeholderMatches = $placeholderMatches;

        return $this;
    }

    public function getPregMatchMatches(): array
    {
        return $this->pregMatchMatches;
    }

    public function setPregMatchMatches(array $pregMatchMatches): self
    {
        $this->pregMatchMatches = $pregMatchMatches;

        return $this;
    }

    public function isIgnoreQueryParameters(): bool
    {
        return $this->ignoreQueryParameters;
    }

    public function isIgnoreCase(): bool
    {
        return $this->ignoreCase;
    }

    public function isIgnoreTrailingSlash(): bool
    {
        return $this->ignoreTrailingSlash;
    }

    public function isKeepQuerystring(): bool
    {
        return $this->keepQuerystring;
    }
}
