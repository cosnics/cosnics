<i18n>
{
    "en": {
        "add-level": "Add Level",
        "cancel": "Cancel",
        "default": "Default",
        "default-info": "Optional choice field. The level assigned by default to a criterium.",
        "default-trunc": "Def.",
        "enter-level-description": "Enter level description",
        "level": "Level",
        "points": "Points",
        "remove": "Remove",
        "remove-level": "Remove level {item}"
    },
    "fr": {
        "add-level": "Ajouter un niveau",
        "cancel": "Annuler",
        "default": "Norme",
        "default-info": "Contrôle de choix optionnel. Le niveau attribué par défaut à un critère.",
        "default-trunc": "Nrm.",
        "enter-level-description": "Entrer une description de niveau",
        "level": "Niveau",
        "points": "Points",
        "remove": "Supprimer",
        "remove-level": "Supprimer le niveau {item}"
    },
    "nl": {
        "add-level": "Niveau toevoegen",
        "cancel": "Annuleer",
        "default": "Standaard",
        "default-info": "Optioneel keuzeveld. Het niveau dat standaard wordt toegekend aan een criterium.",
        "default-trunc": "Std.",
        "enter-level-description": "Voer een niveauomschrijving in",
        "level": "Niveau",
        "points": "Punten",
        "remove": "Verwijder",
        "remove-level": "Niveau {item} verwijderen"
    }
}</i18n>

<template>
    <div @click.stop="selectedLevel = null">
        <div @click.stop="">
            <b-table-simple :class="{'mod-rubric': !criterium, 'mod-criterium': !!criterium}">
                <b-thead class="table-head">
                    <b-tr :class="'table-head-row' + (!!criterium ? ' mod-criterium': ' mod-rubric')">
                        <b-th class="table-title">{{ $t('level') }}</b-th>
                        <b-th v-if="rubric.useScores" class="table-score">{{ rubric.useRelativeWeights ? '%' : $t('points') }}</b-th>
                        <b-th class="table-default"><div style="display: flex; flex-wrap: nowrap; align-items: baseline; gap: .2em">{{ $t(criterium ? 'default-trunc' : 'default') }} <i class="fa fa-info-circle" :title="$t('default-info')"></i></div></b-th>
                        <b-th class="table-actions"></b-th>
                    </b-tr>
                </b-thead>
                <b-tbody>
                    <template v-for="(item, index) in levels">
                        <b-tr :class="rowClass(item)" @click.stop="onSelectLevel(item)" @mouseover="hoveredLevel = item" @mouseout="hoveredLevel = null">
                            <b-td class="table-title">
                                <div class="table-title-wrap">
                                    <span class="level-index">{{ index + 1 }}</span>
                                    <b-input type="text" v-model="item.title" autocomplete="off" class="mod-title mod-input mod-pad" :class="{'input-detail': !isEditDisabled}" :disabled="isEditDisabled" @input="onLevelChange(item)" @focus="onSelectLevel(item)" />
                                </div>
                            </b-td>
                            <b-td v-if="rubric.useScores" class="table-score">
                                <b-input type="number" v-model.number="item.score" autocomplete="off" class="mod-input mod-pad mod-num" :class="{'input-detail': !isEditDisabled}" :disabled="isEditDisabled" @input="onLevelChange(item)" @focus="onSelectLevel(item)" required min="0" step="1" />
                            </b-td>
                            <b-td class="table-default">
                                <input type="radio" :checked="item.isDefault" @keyup.enter="setDefault(item)" @click="setDefault(item)" :class="{'input-detail': !isEditDisabled}" :disabled="isEditDisabled" />
                            </b-td>
                            <b-td class="table-actions">
                                <selection-controls
                                    :id="item.id"
                                    :is-up-disabled="isEditDisabled || index === 0"
                                    :is-down-disabled="isEditDisabled || index >= levels.length - 1"
                                    :is-remove-disabled="isEditDisabled"
                                    class="level-actions"
                                    @move-down="moveLevelDown(item)" @move-up="moveLevelUp(item)"
                                    @remove="showRemoveLevelDialog(item)" @select="onSelectLevel(item)"/>
                            </b-td>
                        </b-tr>
                        <b-tr v-if="criterium" class="table-body-row details-row" @mouseover="hoveredLevel = item" @mouseout="hoveredLevel = null">
                            <b-td :colspan="rubric.useScores ? 3 : 2">
                                <div class="criterium-level-input-area" style="margin: -1rem 1rem 0 2.2rem;">
                                    <textarea v-model="item.description" ref="feedbackField" class="criterium-level-feedback"
                                              :class="{ 'input-detail': !isEditDisabled, 'is-input-active': activeDescriptionInput === item || !item.description }"
                                              :placeholder="$t('enter-level-description')" :disabled="isEditDisabled"
                                              @input="onLevelChange(item)" @focus="onDescriptionFocus(item)" @blur="activeDescriptionInput = null">
                                    </textarea>
                                    <div class="criterium-level-markup-preview" :class="{'is-input-active': activeDescriptionInput === item || !item.description}" v-html="marked(item.description)"></div>
                                </div>
                            </b-td>
                        </b-tr>
                    </template>
                    <b-tr v-if="newLevel" class="table-body-row new-level-row" :class="{'mod-criterium': !!criterium}">
                        <b-td class="table-title">
                            <div class="table-title-wrap">
                                <span class="level-index">{{ levels.length + 1 }}</span>
                                <b-input type="text" autocomplete="off" class="mod-title mod-input mod-pad input-detail" v-model="newLevel.title" id="level-title-new" @keydown.enter="addLevel" @keyup.esc="cancelLevel" />
                            </div>
                        </b-td>
                        <b-td class="table-score" v-if="rubric.useScores">
                            <b-input type="number" v-model.number="newLevel.score" autocomplete="off" class="mod-input mod-pad mod-num input-detail" required min="0" step="1" />
                        </b-td>
                        <b-td class="table-default">
                            <input type="radio" :checked="newLevel.isDefault" @keyup.enter="setDefault(newLevel)" @click="setDefault(newLevel)" class="input-detail" />
                        </b-td>
                        <b-td class="table-actions">
                            <div class="level-actions">
                                <button class="btn btn-default btn-sm mod-level-action" :title="$t('add')" @click.stop="addLevel">
                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                    <span class="sr-only">{{ $t('add') }}</span>
                                </button>
                                <button class="btn btn-default btn-sm mod-level-action mod-cancel" :title="$t('cancel')" @click.stop="cancelLevel">
                                    <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                    <span class="sr-only">{{ $t('cancel') }}</span>
                                </button>
                            </div>
                        </b-td>
                    </b-tr>
                    <b-tr v-if="newLevel && !!criterium" class="table-body-row details-row">
                        <b-td :colspan="rubric.useScores ? 3 : 2">
                            <div class="criterium-level-input-area" style="margin: -1rem 1rem 0 2.2rem;">
                                <textarea v-model="newLevel.description" ref="feedbackField" class="criterium-level-feedback input-detail"
                                          :class="{ 'is-input-active': activeDescriptionInput === newLevel || !newLevel.description }"
                                          :placeholder="$t('enter-level-description')"
                                          @focus="onDescriptionFocus(newLevel)" @blur="activeDescriptionInput = null">
                                </textarea>
                                <div class="criterium-level-markup-preview" :class="{'is-input-active': activeDescriptionInput === newLevel || !newLevel.description}" v-html="marked(newLevel.description)"></div>
                            </div>
                        </b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </div>
        <button v-if="!newLevel" class="btn-new" @click.stop="createNewLevel" style="margin-left: .2em">{{ $t('add-level') }}</button>
        <div class="modal-bg" v-if="removingLevel !== null" @click.stop="hideRemoveLevelDialog">
            <div class="modal-content" @click.stop="">
                <div class="modal-content-title">{{ $t('remove-level', {item: `'${removingLevel.title}'`}) }}?</div>
                <div>
                    <button class="btn-strong mod-confirm" ref="btn-remove-level" @click.stop="removeLevel(removingLevel)">{{ $t('remove') }}</button>
                    <button class="btn-strong" @click.stop="hideRemoveLevelDialog">{{ $t('cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import SelectionControls from './SelectionControls.vue';
    import Rubric from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';
    import debounce from 'debounce';
    import DataConnector from '../Connector/DataConnector';
    import DOMPurify from 'dompurify';
    import * as marked from 'marked';

    @Component({
        name: 'levels',
        components: {
            SelectionControls
        },
    })
    export default class Levels extends Vue {
        private newLevel: Level|null = null;
        private hoveredLevel: Level|null = null;
        private selectedLevel: Level|null = null;
        private removingLevel: Level|null = null;
        private activeDescriptionInput: Level|null = null;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Criterium, default: null}) readonly criterium!: Criterium;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

        constructor() {
            super();
            this.onLevelMove = debounce(this.onLevelMove, 750);
            this.onLevelChange = debounce(this.onLevelChange, 750);
        }

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
        }

        get isEditDisabled(): boolean {
            return !!this.newLevel;
        }

        get levels() {
            if (this.criterium) {
                return this.rubric.filterLevelsByCriterium(this.criterium).map(l => { (l as any)._showDetails = true; return l; });
            }
            return this.rubric.rubricLevels;
        }

        onLevelChange(level: Level) {
            this.dataConnector?.updateLevel(level);
        }

        createNewLevel() {
            this.selectLevel(null);
            this.newLevel = this.getDefaultLevel();
            this.$nextTick(() => {
                (document.querySelector(`#level-title-new`)! as HTMLElement).focus();
            });
        }

        addLevel() {
            if (this.newLevel!.isDefault) {
                this.levels.forEach(level => {
                    level.isDefault = false;
                });
            }
            this.rubric.addLevel(this.newLevel!);
            this.dataConnector?.addLevel(this.newLevel!, this.levels.length);
            this.newLevel = null;
            this.$emit('level-added');
            this.createNewLevel();
        }

        cancelLevel() {
            this.newLevel = null;
            this.selectLevel(null);
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
            this.$nextTick(() => {
                let el : HTMLButtonElement|null = document.querySelector(`#btn-up-${level.id}`);
                if (el?.disabled) {
                    el = el?.nextSibling as HTMLButtonElement;
                }
                el?.focus();
            });
        }

        moveLevelDown(level: Level) {
            this.rubric.moveLevelDown(level);
            this.onLevelMove(level);
            this.$nextTick(() => {
                let el : HTMLButtonElement|null = document.querySelector(`#btn-down-${level.id}`);
                if (el?.disabled) {
                    el = el?.previousSibling as HTMLButtonElement;
                }
                el?.focus();
            });
        }

        setDefault(defaultLevel: Level) {
            if (this.newLevel === defaultLevel) {
                this.newLevel.isDefault = !this.newLevel.isDefault;
            } else {
                this.levels.forEach(level => {
                    level.isDefault = (defaultLevel === level) ? !level.isDefault : false;
                });
                this.onLevelChange(defaultLevel);
            }
        }

        getDefaultLevel() {
            const level = new Level('');

            if (this.criterium) {
                level.criteriumId = this.criterium.id;
            }

            return level;
        }

        showRemoveLevelDialog(level: Level|null) {
            this.removingLevel = level;
        }

        hideRemoveLevelDialog() {
            this.showRemoveLevelDialog(null);
        }

        removeLevel(level: Level) {
            this.removingLevel = null;
            this.rubric.removeLevel(level);
            this.dataConnector?.deleteLevel(level);
            this.selectLevel(null);
        }

        get fields() {
            return [
                { key: 'title', sortable: false, variant: 'title' },
                this.rubric.useScores ? { key: 'score', sortable: false, variant: 'score' } : null,
                { key: 'is_default', sortable: false, variant: 'default' },
                { key: 'actions', sortable: false, label: '', variant: 'actions' }
            ];
        }

        rowClass(level: Level) : string {
            return `table-body-row level-row${this.criterium ? ' mod-criterium' : ''}${level === this.selectedLevel ? ' is-selected' : ''}${ this.newLevel ? '' : ' is-enabled'}${level === this.hoveredLevel ? ' is-hovered' : ''}`;
        }

        onSelectLevel(level: Level) {
            if (this.isEditDisabled) { return; }
            this.selectedLevel = level;
        }

        onDescriptionFocus(level: Level) {
            this.onSelectLevel(level);
            this.activeDescriptionInput = level;
        }

        onRowSelected(levels: Level[]) {
            this.selectLevel(levels[0] || null);
        }

        selectLevel(level: Level|null) {
            if (this.newLevel) { return false; }
            this.selectedLevel = level;
            return false;
        }

        @Watch('removingLevel')
        onRemoveItemChanged() {
            if (this.removingLevel) {
                this.$nextTick(() => {
                    (this.$refs['btn-remove-level'] as HTMLElement).focus();
                });
            }
        }

        mounted() {
            if (!this.levels.length) {
                this.createNewLevel();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .table {
        --border-color: #ebebeb;
        /*--hover-bg: #f4fbfb;*/
        border: none;
        margin-top: 1em;
        max-width: fit-content;

        &.mod-rubric {
            margin-left: .25em;
            margin-top: 1em;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 10px 8px;
            vertical-align: middle;

            &.table-actions {
                border-width: 0;
            }

            &:not(.table-default) {
                border-right-color: transparent;
            }
        }

        th {
            background-color: #f8fbfb;
            color: #5885a2;

            &.table-score {
                text-align: center;
            }

            &.table-actions {
                background: none;
                border-bottom: none;
            }

            &:not(.table-actions) {
                border-bottom: 1px solid transparent;
            }
        }

        .table-head .table-head-row th:not(.table-actions) {
            border-top: 1px solid var(--border-color);
        }

        .level-row {
            &:not(.is-selected) .table-actions {
                pointer-events: none;
            }

            &.mod-criterium td {
                border-bottom: none;
            }

            &:first-child td:not(.table-actions) {
                background: linear-gradient(to bottom, #e3eaed 0, hsla(0, 0%, 100%, 0) 4px);
            }
        }

        .new-level-row.mod-criterium td {
            border-bottom: none;
        }

        .details-row td {
            border-top: none;
            border-right: 1px solid var(--border-color);
        }

        .level-index {
            color: #406e8d;
            font-size: 1.5rem;
            text-align: right;
        }

        .table-title {
            width: 25em;

            &-wrap {
                align-items: baseline;
                display: flex;
                gap: .7em;
            }
        }

        .table-score {
            width: 5em;
        }

        .table-default {
            text-align: center;
        }

        .table-actions {
            border-width: 0;
        }

        @media only screen and (min-width: 900px) {
            &.mod-criterium {
                --border-color: #d3d8da;
                /*--hover-bg: #dfe2e3;*/
            }

            .table-head-row.mod-criterium th:not(.table-actions) {
                background-color: #e5e8ea;
            }

            .level-row.mod-criterium:first-child td:not(.table-actions) {
                background: linear-gradient(to bottom, #d5dadd 0, hsla(0, 0%, 100%, 0) 4px);
                background-origin: border-box;
            }
        }

        @media only screen and (max-width: 459px) {
            & {
                position: relative;
            }
        }

        @media (pointer: fine) {
            .level-row {
                &.is-enabled:hover td:not(.table-actions) {
                    cursor: pointer;
                }
                &:not(.is-enabled) {
                    &, & + .details-row {
                        opacity: .8;
                    }
                }
            }
        }

        .level-row.is-selected, .new-level-row {
            &, & + .details-row {
                td:not(.table-actions) {
                    background: #ecf4f4;
                }
            }

            &:first-child td:not(.table-actions) {
                background: linear-gradient(to bottom, #e3eaed 0, #ecf4f4 4px);
            }
        }

        @media only screen and (min-width: 900px) {
            .level-row.mod-criterium.is-selected, .new-level-row.mod-criterium {
                &, & + .details-row {
                    td:not(.table-actions) {
                        background: #d7dbdf;
                    }
                }
            }
        }
    }

    .form-control {
        &.mod-num {
            text-align: right;

            -moz-appearance: textfield;
            &::-webkit-outer-spin-button,
            &::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        }

        &.mod-input.mod-pad {
            height: auto;
            padding: 2px 5px;

            &.mod-num {
                font-size: 1.7rem;
                padding: 0 5px;
            }
        }
    }

    .criterium-level-feedback[disabled] {
        background: #eee;
        border-radius: 3px;
        cursor: not-allowed;

        + .criterium-level-markup-preview {
            background: #eee;
            border-color: #d2d3d3;
        }
    }

    .level-actions {
        display: flex;
        gap: 5px;
    }

    @media only screen and (max-width: 459px) {
        .level-actions {
            bottom: -35px;
            position: absolute;
            right: 17px;
        }
    }
</style>
