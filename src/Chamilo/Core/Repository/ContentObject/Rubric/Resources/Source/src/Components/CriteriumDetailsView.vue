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
                        <div v-if="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights)" class="criterium-weight">
                            <template v-if="rubric.useRelativeWeights">
                                {{ $t('weight') }}: <span :style="rubric.eqRestWeight < 0 && 'color: red'">{{ criterium.rel_weight === null ? rubric.eqRestWeight.toLocaleString() : criterium.rel_weight }} %</span> <i v-if="rubric.eqRestWeight < 0" class="fa fa-exclamation-circle" style="color: red;" aria-hidden="true"></i>
                            </template>
                            <template v-else>
                                <label for="weight">{{ $t('weight') }}:</label>
                                <input type="number" id="weight" v-model.number="criterium.weight" class="input-detail" @input="onWeightChange" min="0" max="100" required /> %
                            </template>
                        </div>
                        <div v-if="!showFormatting"><a href="#" @click.prevent="showFormatting=true" style="text-decoration: none">{{ $t('formatting') }}</a></div>
                    </div>
                    <template v-if="!criteriumLevels.length">
                        <a v-if="!addingLevel" href="#" @click.prevent="createLevel()">Gebruik aangepaste niveaus</a>
                        <ul class="b-criterium-levels" v-if="!addingLevel">
                            <li v-for="level in rubric.rubricLevels" :key="level.id" class="b-criterium-level">
                                <criterium-level-view :rubric="rubric" :criterium="criterium" :level="level" @input="updateHeight" @change="onChoiceChange($event, criterium, level)"></criterium-level-view>
                            </li>
                        </ul>
                        <div v-else style="display: flex; flex-direction: column; gap: 3px; align-items: flex-start;">
                            Maak een aangepast niveau:
                            <div>
                                <label for="lvl-title">Titel</label>
                                <input id="lvl-title" type="text" v-model="addingLevel.title" class="input-detail" />
                            </div>
                            <div>
                                <label for="lvl-score">Score</label>
                                <input id="lvl-score" type="number" v-model="addingLevel.score" class="input-detail" />
                            </div>
                            <div>
                                <label for="lvl-default">Standaard</label>
                                <input id="lvl-default" type="radio" v-model="addingLevel.isDefault" />
                            </div>
                            <div>
                                <label for="lvl-descr">Omschrijving</label>
                                <textarea id="lvl-descr" v-model="addingLevel.description" class="input-detail"></textarea>
                            </div>
                            <div>
                                <button @click.prevent="addLevel">Voeg toe</button><a href="#" @click.prevent="addingLevel = null">Cancel</a>
                            </div>
                        </div>
                    </template>
                    <div v-else style="display: flex; flex-direction: column; gap: 15px">
                        <div v-for="(level, index) in criteriumLevels">
                            <div>{{level.title}}</div>
                            <div>{{level.score}}</div>
                            <div>{{level.isDefault ? 'default' : 'not default'}}</div>
                            <div>{{level.description}}</div>
                            <div><button @click="moveLevelUp(level)">Up</button><button @click="moveLevelDown(level)">Down</button></div>
                        </div>
                        <div v-if="!addingLevel">
                            <a href="#" @click.prevent="createLevel">Maak een aangepast niveau:</a>
                        </div>
                        <div v-else style="display: flex; flex-direction: column; gap: 3px; align-items: flex-start;">
                            Maak een aangepast niveau:
                            <div>
                                <label for="lvl-title">Titel</label>
                                <input id="lvl-title" type="text" v-model="addingLevel.title" class="input-detail" />
                            </div>
                            <div>
                                <label for="lvl-score">Score</label>
                                <input id="lvl-score" type="number" v-model="addingLevel.score" class="input-detail" />
                            </div>
                            <div>
                                <label for="lvl-default">Standaard</label>
                                <input id="lvl-default" type="radio" v-model="addingLevel.isDefault" />
                            </div>
                            <div>
                                <label for="lvl-descr">Omschrijving</label>
                                <textarea id="lvl-descr" v-model="addingLevel.description" class="input-detail"></textarea>
                            </div>
                            <div>
                                <button @click.prevent="addLevel">Voeg toe</button><a href="#" @click.prevent="addingLevel = null">Cancel</a>
                            </div>
                        </div>
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

    function updateHeight(elem: HTMLElement, addedPixels: number = 0) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight + addedPixels}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: { CriteriumLevelView, FormattingHelp }
    })
    export default class CriteriumDetailsView extends Vue {
        private showFormatting = false;
        private addingLevel: Level|null = null;

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

        createLevel() {
            this.addingLevel = new Level('');
        }

        addLevel() {
            if (this.addingLevel && this.criterium) {
                this.addingLevel.criteriumId = this.criterium.id;
                this.rubric.addLevel(this.addingLevel);
                this.dataConnector?.addLevel(this.addingLevel, this.rubric.filterLevelsByCriterium(this.criterium).length);
                this.addingLevel = null;
            }
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
            this.addingLevel = null;
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