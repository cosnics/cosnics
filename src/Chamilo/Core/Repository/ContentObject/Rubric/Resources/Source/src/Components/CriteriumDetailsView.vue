<i18n>
{
    "en": {
        "back-to-rubric": "Back to rubric",
        "cancel-custom-levels": "Cancel custom levels",
        "close": "Close",
        "criterium": "Criterium",
        "formatting": "Formatting",
        "use-custom-levels": "Use custom levels",
        "weight": "Weight"
    },
    "fr": {
        "back-to-rubric": "Retour à la rubrique",
        "cancel-custom-levels": "Annuler niveaux personnalisés",
        "close": "Fermer",
        "criterium": "Critère",
        "formatting": "Mise en forme",
        "use-custom-levels": "Utiliser des niveaux personnalisés",
        "weight": "Poids"
    },
    "nl": {
        "back-to-rubric": "Terug naar rubric",
        "cancel-custom-levels": "Annuleer aangepaste niveaus",
        "close": "Sluiten",
        "criterium": "Criterium",
        "formatting": "Opmaakhulp",
        "use-custom-levels": "Gebruik aangepaste niveaus",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div class="criterium-details-wrapper">
        <transition name="border-flash" mode="out-in">
            <div :key="criterium ? criterium.id : 'none'" class="criterium-details" :class="{'mod-levels': !!criteriumLevels.length || initCustomLevels,'is-show-formatting': showFormatting}" v-if="criterium !== null">
                <div v-if="criterium" style="flex: 1">
                    <div class="criterium-details-header">
                        <button class="btn-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i class="fa fa-close" aria-hidden="true" /></button>
                        <div class="criterium-details-title">
                            <label for="criterium-title">{{ $t('criterium') }}: </label>
                            <textarea id="criterium-title" name="title" v-model="criterium.title" ref="criteriumTitleField" class="input-detail" @input="onCriteriumChange"></textarea>
                        </div>
                    </div>
                    <div style="display: flex;justify-content: space-between;align-items:baseline">
                        <div v-if="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights)" class="criterium-weight">
                            <template v-if="rubric.useRelativeWeights">
                                {{ $t('weight') }}: <span :style="rubric.eqRestWeight < 0 && 'color: red'">{{ criterium.rel_weight === null ? rubric.eqRestWeight.toLocaleString() : criterium.rel_weight }} %</span> <i v-if="rubric.eqRestWeight < 0" class="fa fa-exclamation-circle" style="color: red;" aria-hidden="true"></i>
                            </template>
                            <template v-else>
                                <label for="weight">{{ $t('weight') }}:</label>
                                <template v-if="!criteriumLevels.length && !initCustomLevels"><input type="number" id="weight" v-model.number="criterium.weight" class="input-detail" @input="onWeightChange" min="0" max="100" required /> %</template>
                                <span v-else>100 %</span>
                            </template>
                        </div>
                        <div v-if="!showFormatting"><a href="#" @click.prevent="showFormatting=true" style="text-decoration: none">{{ $t('formatting') }}</a></div>
                    </div>
                    <template v-if="!criteriumLevels.length && !initCustomLevels">
                        <a href="#" @click.prevent="initCustomLevels = true" style="display: block; text-align: end">{{ $t('use-custom-levels') }}</a>
                        <ul class="b-criterium-levels">
                            <li v-for="level in rubric.rubricLevels" :key="level.id" class="b-criterium-level">
                                <criterium-level-view :rubric="rubric" :criterium="criterium" :level="level" @input="updateHeight" @change="onChoiceChange($event, criterium, level)"></criterium-level-view>
                            </li>
                        </ul>
                    </template>
                    <div v-else>
                        <a v-if="!criteriumLevels.length" href="#" @click.prevent="initCustomLevels = false" style="display: block; text-align: end">{{ $t('cancel-custom-levels') }}</a>
                        <levels :rubric="rubric" :data-connector="dataConnector" :criterium="criterium" @level-added="initCustomLevels = false"></levels>
                    </div>
                    <a href="#" role="button" @click.prevent="$emit('close')" class="rubric-return"><i class="fa fa-arrow-left"/> {{ $t('back-to-rubric') }}</a>
                </div>
                <formatting-help v-if="showFormatting" @close="showFormatting = false"></formatting-help>
            </div>
        </transition>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import Criterium from '../Domain/Criterium';
    import Choice from '../Domain/Choice';
    import CriteriumLevelView from './CriteriumLevelView.vue';
    import FormattingHelp from './FormattingHelp.vue';
    import DataConnector from '../Connector/DataConnector';
    import Levels from './Levels.vue';

    function updateHeight(elem: HTMLElement, addedPixels: number = 0) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight + addedPixels}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: { CriteriumLevelView, FormattingHelp, Levels }
    })
    export default class CriteriumDetailsView extends Vue {
        private showFormatting = false;
        private initCustomLevels = false;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(Criterium) readonly criterium!: Criterium | null;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

        constructor() {
            super();
            this.onCriteriumChange = debounce(this.onCriteriumChange, 750);
            this.onWeightChange = debounce(this.onWeightChange, 750);
        }

        get criteriumLevels() {
            if (!this.criterium) { return []; }
            return this.rubric.filterLevelsByCriterium(this.criterium);
        }

        onLevelMove(level: Level) {
            const levels = this.rubric.getFilteredLevels(level);
            if (!levels) { return; }
            const index = levels.indexOf(level);
            this.dataConnector?.moveLevel(level, index);
        }

        moveLevelUp(level: Level) {
            this.rubric.moveLevelUp(level);
            this.onLevelMove(level);
        }

        moveLevelDown(level: Level) {
            this.rubric.moveLevelDown(level);
            this.onLevelMove(level);
        }

        updateHeight(e: InputEvent) {
            updateHeight(e.target as HTMLElement);
        }

        updateHeightAll() {
            updateHeight(this.$refs['criteriumTitleField'] as HTMLElement, 5);

            for (let elem of document.getElementsByClassName('criterium-level-feedback')) {
                updateHeight(elem as HTMLElement);
            }
        }

        updated() {
            window.setTimeout(() => {
                this.updateHeightAll();
            }, 250);
        }

        mounted() {
            this.updateHeightAll();
        }

        onCriteriumChange() {
            this.$emit('change-criterium', this.criterium);
        }

        onWeightChange(event: InputEvent) {
            const el = event.target as HTMLInputElement;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            if (this.rubric.useRelativeWeights && (typeof this.criterium?.rel_weight !== 'number')) {
                this.criterium!.rel_weight = null;
            }
            this.$emit('change-criterium', this.criterium);
        }

        onChoiceChange(choice: Choice, criterium: Criterium, level: Level) {
            this.$emit('change-choice', choice, criterium, level);
        }

        @Watch('rubric.useScores')
        onUsesScoresChange() {
            window.setTimeout(() => {
                this.updateHeightAll();
            }, 250);
        }

        @Watch('criterium')
        onDisplayedCriterium() {
            this.initCustomLevels = false;
        }
    }
</script>

<style scoped lang="scss">
     * {
        outline: none;
     }


     @media only screen and (min-width: 900px) {
         .criterium-details.is-show-formatting {
             width: 50em;
         }
     }
</style>