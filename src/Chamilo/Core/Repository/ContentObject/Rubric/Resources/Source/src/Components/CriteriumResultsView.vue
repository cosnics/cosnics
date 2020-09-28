<i18n>
{
    "en": {
        "chose": "chose",
        "close": "Close",
        "extra-feedback": "Extra feedback",
        "gave-score": "gave a score of",
        "level-descriptions": "Level descriptions"
    },
    "fr": {
        "chose": "a choisi",
        "close": "Fermer",
        "extra-feedback": "Feed-back supplémentaire",
        "gave-score": "a donné le score",
        "level-descriptions": "Descriptions de niveau"
    },
    "nl": {
        "chose": "koos",
        "close": "Sluiten",
        "extra-feedback": "Extra feedback",
        "gave-score": "gaf score",
        "level-descriptions": "Niveauomschrijvingen"
    }
}
</i18n>
<template>
    <div class="rr-selected-criterium-wrapper">
        <div class="rr-selected-criterium">
            <button class="btn-info-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i aria-hidden="true" class="fa fa-close"/></button>
            <div class="rr-selected-criterium-results">
                <div class="rr-selected-criterium-results-title">
                    <!--<span>{{ criterium.parent.parent.title }}<i class="fa fa-angle-right separator" /></span>
                    <span v-if="criterium.parent.title.trim().length !== 0">{{ criterium.parent.title }}<i class="fa fa-angle-right separator" /></span>
                    --><span>{{ criterium.title }}</span>
                </div>
                <div class="rr-selected-result" v-for="{evaluator, score, level, feedback} in evaluations">
                    <p v-if="rubric.useScores && level !== null">
                        <span>{{ evaluator.name|capitalize }}</span> {{ $t('gave-score') }} <span>{{ score || '0' }}</span>
                        (<span class="score-title">{{ level.title }}</span>)
                    </p>
                    <p v-else-if="level !== null">
                        <span>{{ evaluator.name|capitalize }}</span> {{ $t('chose') }}
                        '<span class="score-title">{{ level.title }}</span>'
                    </p>
                    <p v-if="feedback">
                        {{ $t('extra-feedback') }}: {{ feedback }}
                    </p>
                </div>
            </div>
            <div class="rr-selected-criterium-levels">
                <div class="title">{{ $t('level-descriptions') }}:</div>
                <ul class="levels-list">
                    <li v-for="level in rubric.levels" :key="level.id" class="levels-list-item">
                        <div class="levels-list-item-header">
                            <div class="title">{{ level.title }}</div>
                            <div class="choice-score" v-if="rubric.useScores">{{ rubric.getChoiceScore(criterium, level) }}</div>
                        </div>
                        <div class="choice-feedback" v-html="marked(rubric.getChoice(criterium, level).feedback)"></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import * as marked from 'marked';
    import DOMPurify from 'dompurify';

    @Component({
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            }
        }
    })
    export default class CriteriumResultsView extends Vue {
        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Criterium}) readonly criterium!: Criterium;
        @Prop({type: Array}) readonly evaluations!: any[];

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
        }
    }
</script>