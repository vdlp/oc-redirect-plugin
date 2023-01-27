<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

use Vdlp\Redirect\Classes\Contracts\RedirectConditionInterface;
use Vdlp\Redirect\Classes\Contracts\RedirectManagerInterface;

final class RedirectConditionManager
{
    public function __construct(
        private RedirectManagerInterface $redirectManager
    ) {
    }

    public function getEnabledConditions(RedirectRule $rule): array
    {
        $enabledConditions = [];

        if (!class_exists(\Vdlp\RedirectConditions\Models\ConditionParameter::class)) {
            return $enabledConditions;
        }

        $conditions = $this->redirectManager->getConditions();

        if (count($conditions) === 0) {
            return $enabledConditions;
        }

        $conditionCodes = \Vdlp\RedirectConditions\Models\ConditionParameter::query()
            ->where('redirect_id', '=', $rule->getId())
            ->whereNotNull('is_enabled')
            ->get(['condition_code'])
            ->pluck('condition_code')
            ->toArray();

        if (count($conditionCodes) === 0) {
            return $enabledConditions;
        }

        foreach ($conditions as $condition) {
            /** @var RedirectConditionInterface $condition */
            $condition = resolve($condition);

            if (!in_array($condition->getCode(), $conditionCodes, true)) {
                continue;
            }

            $enabledConditions[] = $condition;
        }

        return $enabledConditions;
    }
}
