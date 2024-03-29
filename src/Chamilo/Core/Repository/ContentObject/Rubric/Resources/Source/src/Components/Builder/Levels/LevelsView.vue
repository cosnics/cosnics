<i18n>
{
    "en": {
        "levels": "Levels",
        "weights-per-total": "Weights relative to total score",
        "with-scores": "With scores",
        "without-scores": "Without scores"
    },
    "fr": {
        "levels": "Niveaux",
        "with-scores": "Avec scores",
        "without-scores": "Sans scores"
    },
    "nl": {
        "levels": "Niveaus",
        "weights-per-total": "Gewichten relatief t.o.v. totaalscore",
        "with-scores": "Met scores",
        "without-scores": "Zonder scores"
    }
}
</i18n>

<template>
    <div class="levels-view">
        <h1 class="title">{{ $t('levels') }}</h1>
        <div class="controls">
            <div><on-off-switch id="use-scores-check" class="switch" :value="rubric.useScores" @input="onUseScoresChanged" :on-value="$t('with-scores')" :off-value="$t('without-scores')"></on-off-switch></div>
            <div v-if="rubric.useScores && !rubric.hasAbsoluteWeights" class="m-mx">
                <button :aria-pressed="rubric.useRelativeWeights ? 'true' : 'false'" class="btn-check" :class="{ 'checked': rubric.useRelativeWeights }" @click="onUseRelativeWeightsChanged">
                    <span tabindex="-1" class="lbl-check"><i aria-hidden="true" class="btn-icon-check fa"></i>{{ $t('weights-per-total') }}</span>
                </button>
            </div>
        </div>
        <levels :rubric="rubric" :data-connector="dataConnector"></levels>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../../../Domain/Rubric';
    import DataConnector from '../../../Connector/DataConnector';
    import OnOffSwitch from '../../OnOffSwitch.vue';
    import Levels from './Levels.vue';

    @Component({
        name: 'levels-view',
        components: {
            OnOffSwitch, Levels
        },
    })
    export default class LevelsView extends Vue {

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

        onUseScoresChanged(useScores: boolean) {
            this.rubric.useScores = useScores;
            this.dataConnector?.updateRubric(this.rubric);
            if (useScores && !this.rubric.useRelativeWeights) {
                this.rubric.hasAbsoluteWeights = Rubric.usesAbsoluteWeights(this.rubric);
            } else if (!useScores) {
                this.rubric.hasAbsoluteWeights = false;
            }
        }

        onUseRelativeWeightsChanged() {
            this.rubric.useRelativeWeights = !this.rubric.useRelativeWeights;
            this.dataConnector?.updateRubric(this.rubric);
        }
    }
</script>

<style lang="scss" scoped>
    .title {
        color: #666;
        font-size: 2.2rem;
        margin-left: .25em;
        margin-top: .3em;
    }
    .controls {
        display: flex;
        gap: 1em;
        margin-left: .25em;
    }
    .switch {
        width: 124px;
    }
    .m-mx {
        margin-left: 1.6em;
    }
</style>

<style lang="scss">
    .rubrics-wrapper-levels {
        margin-left: -1.5em;
        margin-right: -1.5em;
        max-width: 52em;
        width: 100%;
    }

    .levels-view {
        margin-left: .75em;
        position: relative;
    }

    @media only screen and (min-width: 900px) {
        .rubrics-wrapper-levels {
            margin-left: 0;
        }

        .levels-view {
            width: 50em;
        }
    }

    @media only screen and (max-width: 659px) {
        .rubrics-wrapper-levels {
            max-width: initial;
            width: initial;
        }
    }
</style>