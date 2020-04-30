<template>
    <div class="levels-container" :class="{'container-lc-list': !editMode}" @click="(editMode || newLevel) ? null : selectLevel(null)" @keydown.esc="hideRemoveLevelDialog">
        <div v-if="editMode" class="lc-edit" :class="{'new-level': newLevel !== null}">
            <form>
                <div class="level-detail ld-level">
                    <label for="level_title" class="lc-label label-level-title">Niveau</label>
                    <input id="level_title" tabindex="0" type="text" autocomplete="off" v-model="selectedLevel.title" placeholder="Vul hier een niveau in" class="input-detail level-title-input">
                </div>
                <div class="level-detail ld-description">
                    <label for="level_description" class="lc-label label-level-description">Beschrijving</label>
                    <textarea id="level_description" tabindex="0" v-model="selectedLevel.description" placeholder="Vul hier een beschrijving in" class="ta-description input-detail"></textarea>
                </div>
                <div class="level-detail ld-weight">
                    <label for="level_score" class="lc-label label-level-weight">Punten</label>
                    <input id="level_score" tabindex="0" type="number" name="Weight" maxlength="3" v-model="selectedLevel.score" class="input-detail level-weight-input">
                </div>
                <div class="level-detail ld-default">
                    <input id="level_default" tabindex="0" type="radio" :checked="selectedLevel.isDefault" @click.stop="setDefault(selectedLevel)" @keydown.space.prevent="setDefault(selectedLevel)" class="input-detail">
                    <label class="lc-label label-level-default" :class="selectedLevel.isDefault ? 'checked' : 'not-checked'" @click.stop="" for="level_default"><i class="fa fa-fw fa-check"></i>Standaard keuze</label>
                </div>
            </form>
            <div v-if="!newLevel" class="ld-delete">
                <button class="lc-btn btn-level-delete" @click.prevent="showRemoveLevelDialog(selectedLevel)"><!--    v-b-popover.hover.top="'Verwijder'">-->
                    <i class="fa fa-fw fa-minus-circle" aria-hidden="true"></i>Verwijder niveau
                </button>
            </div>
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
                        <div class="level-details">
                            <div class="level-detail ld-level" @click.stop="selectLevel(level)">
                                <label :for="`level_title_${levelIndex}`" class="lc-label label-maybe-hide">Niveau</label>
                                <input :id="`level_title_${levelIndex}`" tabindex="0" type="text" autocomplete="off" v-model="level.title" placeholder="Vul hier een niveau in" @focus="selectLevel(level)" @keydown.enter.prevent="" class="level-title-input input-detail">
                                <div class="ld-cover"></div>
                            </div>
                            <div class="level-detail ld-weight" @click.stop="selectLevel(level)">
                                <label :for="`level_score_${levelIndex}`" class="lc-label label-maybe-hide">Punten</label>
                                <input :id="`level_score_${levelIndex}`" tabindex="0" type="number" name="Weight" maxlength="3" v-model="level.score" @focus="selectLevel(level)" @keydown.enter.prevent="" class="level-weight-input input-detail">
                                <div class="ld-cover"></div>
                            </div>
                            <div class="level-detail ld-default">
                                <label :for="`level_default_${levelIndex}`" class="lc-label label-maybe-hide">Standaard</label>
                                <input :id="`level_default_${levelIndex}`" tabindex="0" type="radio" :checked="level.isDefault" @click.stop="setDefault(level)" @keydown.space.prevent="setDefault(level)" @keydown.enter.prevent="" class="input-detail level-default-input">
                                <label class="lc-label label-level-default" :class="`${level.isDefault ? 'checked' : 'not-checked'} ${newLevel && newLevel !== level && newLevel.isDefault && level.isDefault ? 'old-default' : ''}`" @click.stop="" :for="`level_default_${levelIndex}`"><i class="fa fa-fw fa-check"></i></label>
                            </div>
                            <div class="level-detail ld-delete" :class="{'ld-delete-hide': level === newLevel}">
                                <button class="lc-btn btn-level-delete" :disabled="newLevel !== null" @click.prevent="showRemoveLevelDialog(level)"><!--    v-b-popover.hover.top="'Verwijder'">-->
                                    <i class="fa fa-fw fa-minus-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="level-detail ld-edit">
                                <button class="lc-btn btn-level-edit" :disabled="newLevel !== null" @click.prevent="editLevel(level)">
                                    <i class="fa fa-fw fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ld-description" :class="{ empty: level.description.length === 0 }">
                            <label :for="`level_description_${levelIndex}`" class="lc-label label-maybe-hide">Beschrijving</label>
                            <textarea :id="`level_description_${levelIndex}`" tabindex="0" v-model="level.description" placeholder="Vul hier een beschrijving in" @focus="selectLevel(level)" class="ta-description input-detail"></textarea>
                        </div>
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
                        @click.stop="rubric.moveLevelUp(selectedLevel)"
                        :disabled="!selectedLevel || rubric.levels.indexOf(selectedLevel) <= 0"><i
                        class="fa fa-arrow-up" aria-hidden="true"></i></button>
                <button class="lc-btn btn-updown"
                        @click.stop="rubric.moveLevelDown(selectedLevel)"
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
    import Rubric from '../../Domain/Rubric';
    import Level from '../../Domain/Level';
    import DataConnector from '../../Connector/DataConnector';

    @Component({
        name: 'levels-view'
    })
    export default class LevelsView extends Vue {
        private newLevel: Level|null = null;
        private selectedLevel: Level|null = null;
        private removingLevel: Level|null = null;
        private editMode: boolean = false;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

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
    }
</script>

<style scoped>
    * {
        outline-width: thin;
    }
</style>