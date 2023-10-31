<?php

namespace modules\main\conditions;

use Craft;
use craft\base\ElementInterface;
use craft\base\conditions\BaseLightswitchConditionRule;
use craft\elements\conditions\ElementConditionRuleInterface;
use craft\elements\db\ElementQueryInterface;

/**
 * Include Unpublished Drafts element condition rule
 */
class IncludeUnpublishedDrafts extends BaseLightswitchConditionRule implements ElementConditionRuleInterface
{
    function getLabel(): string
    {
        return 'Include Unpublished Drafts';
    }

    function getExclusiveQueryParams(): array
    {
        return ['drafts'];
    }

    function modifyQuery(ElementQueryInterface $query): void
    {
        if ($this->value) {
            $query->drafts(null)->draftOf(false)->savedDraftsOnly(true);
        }
    }

    function matchElement(ElementInterface $element): bool
    {
        return true;
    }
}
