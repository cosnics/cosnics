<i18n>
{
    "en": {
        "back-to-rubric": "Back to rubric",
        "close": "Close",
        "criterium": "Criterium",
        "formatting": "Formatting",
        "weight": "Weight"
    },
    "fr": {
        "back-to-rubric": "Retour à la rubrique",
        "close": "Fermer",
        "criterium": "Critère",
        "formatting": "Mise en forme",
        "weight": "Poids"
    },
    "nl": {
        "back-to-rubric": "Terug naar rubric",
        "close": "Sluiten",
        "criterium": "Criterium",
        "formatting": "Opmaakhulp",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div class="criterium-details-wrapper">
        <transition name="border-flash" mode="out-in">
            <div :key="criterium ? criterium.id : 'none'" class="criterium-details" :class="{'is-show-formatting': showFormatting}" v-if="criterium !== null">
                <div v-if="criterium" style="flex: 1">
                    <div class="criterium-details-header">
                        <button class="btn-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i class="fa fa-close" aria-hidden="true" /></button>
                        <div class="criterium-details-title">
                            <label for="criterium-title">{{ $t('criterium') }}: </label>
                            <textarea id="criterium-title" name="title" v-model="criterium.title" ref="criteriumTitleField" class="input-detail" @input="onCriteriumChange"></textarea>
                        </div>
                    </div>
                    <div style="display: flex;justify-content: space-between;align-items:baseline">
                        <div v-if="rubric.useScores" class="criterium-weight"><label for="weight">{{ $t('weight') }}:</label> <input type="number" id="weight" v-model="criterium.weight" class="input-detail" @input="onWeightChange" min="0" max="100" required /> %</div>
                        <div v-if="!showFormatting"><a href="#" @click.prevent="showFormatting=true" style="text-decoration: none">{{ $t('formatting') }}</a></div>
                    </div>
                    <ul class="b-criterium-levels">
                        <li v-for="level in rubric.levels" :key="level.id" class="b-criterium-level">
                            <criterium-level-view :rubric="rubric" :criterium="criterium" :level="level" @input="updateHeight" @change="onChoiceChange($event, criterium, level)"></criterium-level-view>
                        </li>
                    </ul>
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

    function updateHeight(elem: HTMLElement, addedPixels: number = 0) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight + addedPixels}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: { CriteriumLevelView, FormattingHelp }
    })
    export default class ScoreRubricView extends Vue {
        private showFormatting = false;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(Criterium) readonly criterium!: Criterium | null;

        constructor() {
            super();
            this.onCriteriumChange = debounce(this.onCriteriumChange, 750);
            this.onWeightChange = debounce(this.onWeightChange, 750);
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