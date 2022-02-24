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
            <b-table ref="levels" :class="{'mod-rubric': !criterium}" :items="levels" :fields="fields" thead-class="table-head" :thead-tr-class="'table-head-row' + (!!criterium ? ' mod-criterium': ' mod-rubric')" :tbody-tr-class="rowClass"
                     :selectable="true" select-mode="single" selected-variant="" @row-selected="onRowSelected">
                <template #head(title)>{{ $t('level') }}</template>
                <template #cell(title)="{item, index}">
                    <div>
                        <span class="level-index">{{ index + 1 }}</span>
                        <b-input type="text" v-model="item.title" autocomplete="off" class="mod-title mod-input mod-pad input-detail" @input="onLevelChange(item)" @focus="onSelectLevel(item, index)" />
                    </div>
                </template>
                <template #head(score)>{{ rubric.useRelativeWeights ? '%' : $t('points') }}</template>
                <template #cell(score)="{item, index}">
                    <b-input type="number" v-model.number="item.score" autocomplete="off" class="mod-input mod-pad mod-num input-detail" @input="onLevelChange(item)" @focus="onSelectLevel(item, index)" required min="0" step="1" />
                </template>
                <template #head(is_default)><div style="display: flex; flex-wrap: nowrap; align-items: baseline; gap: .2em">{{ $t(criterium ? 'default-trunc' : 'default') }} <i class="fa fa-info-circle" :title="$t('default-info')"></i></div></template>
                <template #cell(is_default)="{item}">
                    <input type="radio" :checked="item.isDefault" @keyup.enter="setDefault(item)" @click="setDefault(item)" class="input-detail" />
                </template>
                <template #row-details="{item, index}">
                    <div class="criterium-level-input-area" style="margin: -1rem 9rem 0 2.2rem;">
                        <textarea v-model="item.description" ref="feedbackField" class="criterium-level-feedback input-detail"
                                  :class="{ 'is-input-active': activeDescriptionInput === item || !item.description }"
                                  :placeholder="$t('enter-level-description')" @focus="onDescriptionFocus(item, index)" @blur="activeDescriptionInput = null">
                        </textarea>
                        <div class="criterium-level-markup-preview" :class="{'is-input-active': activeDescriptionInput === item || !item.description}" v-html="marked(item.description)"></div>
                    </div>
                </template>
                <template #cell(actions)="{item, index}">
                    <selection-controls
                        :id="item.id"
                        :is-up-disabled="index === 0"
                        :is-down-disabled="index >= levels.length - 1"
                        :is-remove-disabled="false"
                        class="level-actions-2"
                        @move-down="moveLevelDown(item)" @move-up="moveLevelUp(item)"
                        @remove="showRemoveLevelDialog(item)" @select="onSelectLevel(item, index)"/>
                </template>
                <template #bottom-row v-if="newLevel">
                    <b-td class="table-title">
                        <div>
                            <span class="level-index">{{ levels.length + 1 }}</span>
                            <b-input type="text" autocomplete="off" class="mod-title mod-input mod-pad input-detail" v-model="newLevel.title" id="level-title-new" />
                        </div>
                    </b-td>
                    <b-td class="table-score" v-if="rubric.useScores">
                        <b-input type="number" v-model.number="newLevel.score" autocomplete="off" class="mod-input mod-pad mod-num input-detail" required min="0" step="1" />
                    </b-td>
                    <b-td class="table-default">
                        <input type="radio" :checked="newLevel.isDefault" @keyup.enter="setDefault(newLevel)" @click="setDefault(newLevel)" class="input-detail" />
                    </b-td>
                    <b-td class="table-actions">
                        <div class="level-actions-2">
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
                </template>
            </b-table>
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
            console.log('setDefault');
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
            return `table-body-row level-row${level === this.selectedLevel ? ' is-selected' : ''}`;
        }

        onSelectLevel(level: Level, index: number = 0) {
            this.selectedLevel = level;
            (this.$refs['levels'] as unknown as any).selectRow(index);
        }

        onDescriptionFocus(level: Level, index: number = 0) {
            this.onSelectLevel(level, index);
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
        border: none;
        margin-top: 1em;
        max-width: fit-content;

        &.mod-rubric {
            margin-left: .25em;
            margin-top: 1em;

            >>> th, >>> td {
                border: 1px solid #ebebeb;

                &.table-actions {
                    border-width: 0;
                }
            }
        }

        >>> th, >>> td {
            border: 1px solid transparent;
            padding: 10px 8px;
            vertical-align: middle;
        }

        >>> th, >>> td {
            &:not(.table-default) {
                border-right-color: transparent;
            }
        }

        >>> .table-head .table-head-row.mod-rubric th {
            background-color: #f8fbfb;

            &.table-actions {
                background: none;
            }
        }

        >>> .table-head .table-head-row.mod-criterium th {
            background-color: #edeef0;
            border-color: #e3eaed;

            &.table-actions {
                background: none;
            }
        }

        >>> th {
            /*background-color: #f8fbfb;*/
            border-bottom: 0;
            color: #5885a2;

            &.table-score {
                text-align: center;
            }

            &.table-actions {
                background: none;
            }
        }

        >>> .table-head .table-head-row th {
            border-top: 1px solid #ebebeb;

            &:not(.table-actions) {
                border-bottom: 1px solid transparent;
            }

            &.table-actions {
                border-top-color: transparent;
            }
        }

        &.mod-rubric >>> .level-row:first-child td {
            background: linear-gradient(180deg, #e3eaed 0, hsla(0, 0%, 100%, 0) 4px);
        }

        >>> .level-row:first-child td {
            border-top: 0;

            &.table-actions {
                background: transparent;
            }
        }

        >>> .level-index {
            color: #406e8d;
            font-size: 1.5rem;
            text-align: right;
        }

        >>> .table-title {
            width: 25em;

            > div {
                align-items: baseline;
                display: flex;
                gap: .7em;
            }
        }
        >>> .table-score {
            width: 5em;
        }
        >>> .table-default {
            text-align: center;
        }
        >>> .table-actions {
            border-width: 0;
        }
    }
    .form-control {
        &.mod-input.mod-pad {
            height: auto;
            padding: 2px 5px;
        }

        &.mod-num {
            text-align: right;

            -moz-appearance: textfield;
            &::-webkit-outer-spin-button,
            &::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        }

        &.mod-input.mod-pad.mod-num {
            font-size: 1.7rem;
            padding: 0 5px;
        }
    }

    @media (pointer: fine) {
        .table {
            >>> tr:hover td:not(.table-actions) {
                /*background: #f4fbfb;*/
                /*border-color: #e3e3e3;*/
                cursor: pointer;
            }

            >>> tr:hover td:not(.table-default) {
                /*border-right-color: transparent;*/
            }

            >>> tr:first-child:hover td:not(.table-actions) {
                /*background: linear-gradient(to bottom, #e3eaed 0, #f4fbfb 4px);*/
            }
        }
    }

    .level-actions-2 {
        display: flex;
        gap: 5px;
    }

    @media only screen and (max-width: 459px) {
        .table {
            position: relative;
        }

        .level-actions-2 {
            bottom: -35px;
            position: absolute;
            right: 17px;
        }
    }

</style>
