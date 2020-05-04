<template>
    <div class="levels-container" :class="{'container-lc-list': !editMode}" @click="(editMode || newLevel) ? null : selectLevel(null)" @keydown.esc="hideRemoveLevelDialog">
        <div v-if="editMode" class="lc-edit" :class="{'new-level': newLevel !== null}">
            <form>
                <level-details :edit-mode="true" :level="selectedLevel" :level-index="0" :new-level="newLevel" @change="onLevelChange" @level-selected="selectLevel" @level-default="setDefault" @level-remove="showRemoveLevelDialog" @level-edit="editLevel"></level-details>
            </form>
            <div v-if="!newLevel" class="lc-return">
                <a role="button" @click.stop="editMode=false">Terug naar overzicht niveaus.</a>
            </div>
            <div v-else class="actions">
                <button class="btn-name-input btn-ok" @click.prevent="addLevel">Voeg toe</button>
                <button class="btn-name-input btn-cancel" @click.prevent="cancelLevel">Annuleer</button>
            </div>
        </div>
        <div v-else class="lc-list" :class="{'add-mode': newLevel !== null}">
            <div>
                <h1>Niveaus</h1>
                <form>
                <ul class="lc-levels" @click.stop="">
                    <li v-for="(level, levelIndex) in levels" :key="`level_${levelIndex}`" class="levels-list-item" :class="`${isSelected(level) ? 'selected' : 'not-selected'} ${isLabelHidden(level) ? 'labels-hide' : ''} ${newLevel === level ? 'new-level' : ''}`" >
                        <level-details :level="level" :level-index="levelIndex" :new-level="newLevel" @change="onLevelChange" @level-selected="selectLevel" @level-default="setDefault" @level-remove="showRemoveLevelDialog" @level-edit="editLevel"></level-details>
                        <div v-if="level === newLevel" class="actions">
                            <button class="btn-name-input btn-ok" @click.prevent="addLevel">Voeg toe</button>
                            <button class="btn-name-input btn-cancel" @click.prevent="cancelLevel">Annuleer</button>
                        </div>
                    </li>
                </ul>
                </form>
                <button v-if="!newLevel" style="position: relative" class="lc-btn btn-level-add"
                        @click.stop="createNewLevel(false)"><i
                        class="fa fa-plus" aria-hidden="true"></i> Voeg niveau toe
                    <div class="btn-level-add-cover" @click.stop="createNewLevel(true)"></div>
                </button>
            </div>
            <div v-if="newLevel === null && rubric.levels.length > 1 && selectedLevel !== null" class="level-updown" @click.stop="">
                <button class="lc-btn btn-updown"
                        @click.stop="moveLevelUp(selectedLevel)"
                        :disabled="!selectedLevel || rubric.levels.indexOf(selectedLevel) <= 0"><i
                        class="fa fa-arrow-up" aria-hidden="true"></i></button>
                <button class="lc-btn btn-updown"
                        @click.stop="moveLevelDown(selectedLevel)"
                        :disabled="!selectedLevel || rubric.levels.indexOf(selectedLevel) >= rubric.levels.length - 1"><i
                        class="fa fa-arrow-down" aria-hidden="true"></i></button>
            </div>
        </div>
        <div class="modal-bg" v-if="removingLevel !== null" @click.stop="hideRemoveLevelDialog">
            <div class="modal-content" @click.stop="">
                <div class="modal-content-title">Niveau '{{ removingLevel.title }}' verwijderen?</div>
                <div>
                    <button class="btn-dialog-remove btn-ok" ref="btn-remove-level" @click.stop="removeLevel(removingLevel)">Verwijder</button>
                    <button class="btn-dialog-remove btn-cancel" @click.stop="hideRemoveLevelDialog">Annuleer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import LevelDetails from './LevelDetails.vue';
    import DataConnector from '../Connector/DataConnector';

    @Component({
        name: 'levels-view',
        components: {
            LevelDetails
        },
    })
    export default class LevelsView extends Vue {
        private newLevel: Level|null = null;
        private selectedLevel: Level|null = null;
        private removingLevel: Level|null = null;
        private editMode: boolean = false;
        private isCompact: boolean = false;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

        constructor() {
            super();
            this.onLevelMove = debounce(this.onLevelMove, 750);
        }

        onLevelChange(level: Level) {
            this.dataConnector?.updateLevel(level);
        }

        onLevelMove(level: Level) {
            const index = this.rubric.levels.indexOf(level);
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

        addLevel() {
            if (this.newLevel!.isDefault) {
                this.rubric.levels.forEach(level => {
                    level.isDefault = false;
                });
            }
            this.rubric.addLevel(this.newLevel!);
            this.dataConnector?.addLevel(this.newLevel!, this.rubric.levels.length);
            this.newLevel = null;
            this.editMode = false;
        }

        cancelLevel() {
            this.newLevel = null;
            this.editMode = false;
            this.selectLevel(null);
        }

        get levels() {
            if (this.newLevel) {
                return [...this.rubric.levels, this.newLevel];
            } else {
                return this.rubric.levels;
            }
        }

        isLabelHidden(level: Level) : boolean {
            const index = this.rubric.levels.indexOf(level);
            return !this.isSelected(level) && index > 0;
        }

        isSelected(level: Level) : boolean {
            return this.selectedLevel === level;
        }

        selectLevel(level: Level|null) {
            this.selectedLevel = level;
            return false;
        }

        editLevel(level: Level) {
            this.selectLevel(level);
            this.editMode = true;
        }

        createNewLevel(editMode: boolean = false) {
            this.newLevel = this.getDefaultLevel();
            this.selectLevel(this.newLevel);
            if (editMode) {
                this.editMode = true;
            }
        }

        showRemoveLevelDialog(level: Level|null) {
            this.removingLevel = level;
        }

        hideRemoveLevelDialog() {
            this.showRemoveLevelDialog(null);
        }

        removeLevel(level: Level) {
            this.editMode = false;
            this.removingLevel = null;
            this.rubric.removeLevel(level);
            this.dataConnector?.deleteLevel(level);
            this.selectLevel(null);
        }

        setDefault(defaultLevel: Level) {
            if (this.newLevel === defaultLevel) {
                this.newLevel.isDefault = !this.newLevel.isDefault;
            } else {
                this.rubric.levels.forEach(level => {
                    level.isDefault = (defaultLevel === level) ? !level.isDefault : false;
                });
            }
        }

        getDefaultLevel() {
            return new Level('');
        }

        mounted() {
            window.addEventListener('resize', this.handleResize);
            this.handleResize();
        }

        beforeDestroy() {
            window.removeEventListener('resize', this.handleResize);
        }

        handleResize() {
            this.isCompact = window.innerWidth < 900;
        }

        @Watch('rubric.levels.length')
        onLevelsChanged(newLength: Number, oldLength: Number) {
            window.setTimeout(() => {
                if (newLength > oldLength) {
                    this.selectLevel(this.rubric.levels[this.rubric.levels.length - 1]);
                } else if (newLength < oldLength) {
                    (document.activeElement as HTMLElement).blur();
                }
            }, 50);
        }

        @Watch('newLevel')
        onNewLevel() {
            if (this.newLevel) {
                window.setTimeout(() => {
                    const elem = document.querySelector('.new-level .level-title-input');
                    (elem as HTMLElement).focus();
                }, 50);
            }
        }

        @Watch('removingLevel')
        onRemovingLevelChanged(level: Level|null) {
            if (level) {
                this.$nextTick(() => {
                    (this.$refs['btn-remove-level'] as HTMLElement).focus();
                });
            }
        }

        @Watch('isCompact')
        onDetectEditMode(newisCompact: boolean, oldIsCompact: boolean) {
            if (document.activeElement && !this.editMode && !oldIsCompact && newisCompact) {
                if (document.activeElement.classList.contains('input-detail')) {
                    const parentClass = document.activeElement.parentElement?.classList[1];
                    this.editMode = true;
                    window.setTimeout(() => {
                        const elem = document.querySelector(`.${parentClass} .input-detail`);
                        (elem as HTMLElement).focus();
                    }, 50);
                }
            }
        }
    }
</script>

<style scoped>
    * {
        outline-width: thin;
    }
</style>