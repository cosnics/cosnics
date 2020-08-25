<i18n>
{
    "en": {
        "back-to-rubric": "Back to rubric",
        "close": "Close",
        "criterium": "Criterium",
        "weight": "Weight"
    },
    "fr": {
        "back-to-rubric": "Retour à la rubrique",
        "close": "Fermer",
        "criterium": "Critère",
        "weight": "Poids"
    },
    "nl": {
        "back-to-rubric": "Terug naar rubric",
        "close": "Sluiten",
        "criterium": "Criterium",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div class="criterium-details-wrapper">
        <transition name="border-flash" mode="out-in">
            <div :key="criterium ? criterium.id : 'none'" class="criterium-details" v-if="criterium !== null">
                <div v-if="criterium">
                    <div class="criterium-details-header">
                        <button class="btn-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i class="fa fa-close" aria-hidden="true" /></button>
                        <div class="criterium-details-title">
                            <label for="criterium-title">{{ $t('criterium') }}: </label>
                            <textarea id="criterium-title" name="title" v-model="criterium.title" ref="criteriumTitleField" class="input-detail" @input="onCriteriumChange"></textarea>
                        </div>
                    </div>
                    <div class="criterium-path">{{ criterium.parent.parent.parent.title}} > {{ criterium.parent.parent.title}} <span v-if="criterium.parent.color !== ''"> > {{ criterium.parent.title }}</span></div>
                    <div class="criterium-weight"><label for="weight">{{ $t('weight') }}:</label> <input type="number" id="weight" v-model="criterium.weight" class="input-detail" @input="onCriteriumChange"/> %</div>
                    <ul class="criterium-levels">
                        <li v-for="level in rubric.levels" :key="level.id" class="b-criterium-level">
                            <criterium-level-view :rubric="rubric" :criterium="criterium" :level="level" @input="updateHeight" @change="onChoiceChange($event, criterium, level)"></criterium-level-view>
                        </li>
                    </ul>
                    <a href="#" role="button" @click.prevent="$emit('close')" class="rubric-return"><i class="fa fa-arrow-left"/> {{ $t('back-to-rubric') }}</a>
                </div>
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

    function updateHeight(elem: HTMLElement, addedPixels: number = 0) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight + addedPixels}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: { CriteriumLevelView }
    })
    export default class ScoreRubricView extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(Criterium) readonly criterium!: Criterium | null;

        constructor() {
            super();
            this.onCriteriumChange = debounce(this.onCriteriumChange, 750);
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

<style scoped>
     * {
        outline: none;
    }
</style>