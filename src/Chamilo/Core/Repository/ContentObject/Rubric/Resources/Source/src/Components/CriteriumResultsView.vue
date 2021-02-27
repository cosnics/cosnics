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
                <div class="rr-selected-criterium-results-title u-markdown-criterium" v-html="criterium.toMarkdown()"></div>
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
                        <div class="choice-feedback" v-html="rubric.getChoice(criterium, level).toMarkdown()"></div>
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
    }
</script>
<style lang="scss">
    .btn-info-close {
        align-items: center;
        background-color: $bg-criterium-details;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: #777;
        display: flex;
        float: right;
        height: 1.6em;
        justify-content: center;
        margin-left: .5em;
        margin-top: .3em;
        padding: 0;
        transition: background-color 200ms, color 200ms;
        width: 1.6em;

        &:hover {
            background-color: $btn-color;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #fff;
        }

        &:focus {
            border: 1px solid $input-color-focus;
        }
    }

    .rr-selected-criterium-wrapper{
        margin-top: 1em;
    }

    .rr-selected-criterium {
        max-width: 80ch;
    }

    .choice-feedback {
        line-height: 1.5em;

        ul {
            list-style: disc;
        }

        ul, ol {
            margin: 0 0 0 2rem;
            padding: 0;
        }
    }

    @media only screen and (min-width: 900px) {
        .btn-info-close {
            display: none;
        }

        .rr-selected-criterium-wrapper {
            border-left: 1px solid hsla(191, 21%, 80%, 1);
            margin-left: 1.5em;
            padding-left: 1.5em;
            width: 40%;
            pointer-events: none;
        }

        .rr-selected-criterium {
            position: -webkit-sticky;
            position: sticky;
            top: 10px;
        }
    }
    @media only screen and (max-width: 899px) {
        .rr-selected-criterium-wrapper {
            align-items: flex-start;
            background: hsla(0, 0, 0, .15);
            display: flex;
            height: 100%;
            justify-content: center;
            left: 0;
            margin-top: 0;
            overflow: auto;
            padding-top: 3em;
            pointer-events: none;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10000;
        }

        .rr-selected-criterium {
            background: #fff;
            border-radius: $border-radius;
            box-shadow: 1px 1px 5px #999;
            margin: 0 1em;
            padding: .5em;
            pointer-events: all;
        }
    }

    .rr-selected-criterium-results {
        /*background: #e4e3e3;*/
        border-radius: $border-radius;
        padding: .5em;
    }

    .rr-selected-criterium-results-title {
        color: hsla(191, 41%, 38%, 1);
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.3em;
        margin-bottom: .5em;
        max-width: 75ch;

        .separator {
            margin: 0 .3em;
        }
    }

    .rr-selected-result {
        border-radius: $border-radius;
        margin-bottom: 1em;

        p {
            margin: 0;
            white-space: pre-line;
        }

        span {
            font-weight: bold;

            &.score-title {
                color: hsla(191, 41%, 33%, 1);
            }
        }
    }

    .rr-selected-criterium-levels {
        /*background: #e4e3e3;*/
        margin-top: 1.5em;
        padding: .5em;

        .title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0;
            margin-top: 0;
        }

        > .title {
            color: hsla(191, 41%, 38%, 1);
        }

        .levels-list {
            list-style: none;
            margin-top: 0;
            padding: 0;
        }

        .levels-list-item {
            margin-bottom: .75em;
        }

        .levels-list-item-header {
            align-items: baseline;
            border-bottom: 1px solid #d8dddf;
            display: flex;
            width: 100%;

            .title {
                flex: 1;
                font-weight: 700;
            }

            .choice-score {
                font-size: 2rem;
                text-align: right;
            }

            .choice-feedback {
                margin: .25em 1.5em 1.25em 0;
            }
        }
    }
</style>