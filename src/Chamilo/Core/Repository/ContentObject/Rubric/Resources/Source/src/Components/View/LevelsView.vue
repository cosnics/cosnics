<template>
    <div class="levels-container" @click="editMode ? null : selectLevel(null)" @keydown.esc="hideRemoveLevelDialog">
        <div v-if="editMode" class="editMode">
            <form>
                <div class="level">
                    <label for="level_title">Niveau</label>
                    <input id="level_title" tabindex="0" type="text" autocomplete="off" v-model="selectedLevel.title" placeholder="Vul hier een niveau in">
                </div>
                <div class="description">
                    <label for="level_description">Beschrijving</label>
                    <textarea id="level_description" tabindex="0" v-model="selectedLevel.description" placeholder="Vul hier een beschrijving in"></textarea>
                </div>
                <div class="weight">
                    <label for="level_score">Punten</label>
                    <input id="level_score" tabindex="0" type="number" name="Weight" maxlength="3" v-model="selectedLevel.score">
                </div>
                <div class="default-choice">
                    <input id="level_default" tabindex="0" type="radio" :checked="selectedLevel.isDefault" @click.stop="setDefault(selectedLevel)" @keydown.space.prevent="setDefault(selectedLevel)">
                    <label class="check" @click.stop="" for="level_default"><i class="fa fa-fw fa-check"></i>Standaard keuze</label>
                </div>
            </form>
            <div class="delete">
                <button class="btn" @click.prevent="showRemoveLevelDialog(selectedLevel)"><!--    v-b-popover.hover.top="'Verwijder'">-->
                    <i class="fa fa-fw fa-minus-circle" aria-hidden="true"></i>Verwijder niveau
                </button>
            </div>
            <div class="back">
                <a @click.stop="editMode=false">Terug naar overzicht niveaus.</a>
            </div>
        </div>
        <div v-else class="levels">
            <div>
                <h1>Niveaus</h1>
                <form>
                <ul>
                    <li v-for="(level, levelIndex) in rubric.levels" :key="`level_${levelIndex}`" @click.stop="selectLevel(level)" :class="{ selected: isSelected(level) }" >
                        <div class="details">
                            <div class="level">
                                <label :for="`level_title_${levelIndex}`">Niveau</label>
                                <input :id="`level_title_${levelIndex}`" tabindex="0" type="text" autocomplete="off" v-model="level.title" placeholder="Vul hier een niveau in" @focus="selectLevel(level)">
                                <div class="overlay"></div>
                            </div>
                            <div class="weight">
                                <label :for="`level_score_${levelIndex}`">Punten</label>
                                <input :id="`level_score_${levelIndex}`" tabindex="0" type="number" name="Weight" maxlength="3" v-model="level.score" @focus="selectLevel(level)">
                                <div class="overlay"></div>
                            </div>
                            <div class="default-choice">
                                <label :for="`level_default_${levelIndex}`">Standaard</label>
                                <input :id="`level_default_${levelIndex}`" tabindex="0" type="radio" :checked="level.isDefault" @click.stop="setDefault(level)" @keydown.space.prevent="setDefault(level)">
                                <label class="check" @click.stop="" :for="`level_default_${levelIndex}`"><i class="fa fa-fw fa-check"></i></label>
                            </div>
                            <div class="delete">
                                <button class="btn" @click.prevent.stop="showRemoveLevelDialog(level)"><!--    v-b-popover.hover.top="'Verwijder'">-->
                                    <i class="fa fa-fw fa-minus-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="edit">
                                <button class="btn" @click.prevent="editLevel(level)">
                                    <i class="fa fa-fw fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        <div class="description" :class="{ empty: level.description.length === 0 }">
                            <label :for="`level_description_${levelIndex}`">Beschrijving</label>
                            <textarea :id="`level_description_${levelIndex}`" tabindex="0" v-model="level.description" placeholder="Vul hier een beschrijving in" @focus="selectLevel(level)"></textarea>
                        </div>
                    </li>
                </ul>
                </form>
                <button class="btn btn-sm"
                        @click.stop="addLevel"><i
                        class="fa fa-plus" aria-hidden="true"></i> Voeg niveau toe
                </button>
            </div>
            <div v-if="rubric.levels.length > 1 && selectedLevel !== null" class="actions" @click.stop="">
                <button class="btn btn-secondary"
                        @click.stop="rubric.moveLevelUp(selectedLevel)"
                        :disabled="!selectedLevel || rubric.levels.indexOf(selectedLevel) <= 0"><i
                        class="fa fa-arrow-up" aria-hidden="true"></i></button>
                <button class="btn btn-secondary"
                        @click.stop="rubric.moveLevelDown(selectedLevel)"
                        :disabled="!selectedLevel || rubric.levels.indexOf(selectedLevel) >= rubric.levels.length - 1"><i
                        class="fa fa-arrow-down" aria-hidden="true"></i></button>
            </div>
        </div>
        <div class="modal-bg" v-if="removingLevel !== null" @click.stop="hideRemoveLevelDialog">
            <div class="modal-level" @click.stop="">
                <div class="title">Niveau '{{ removingLevel.title }}' verwijderen?</div>
                <div>
                    <button ref="btn-remove-level" class="btn" @click.stop="removeLevel(removingLevel)">Verwijder</button>
                    <button class="btn" @click.stop="hideRemoveLevelDialog">Annuleer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from "vue-property-decorator";
    import Level from "../../Domain/Level";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import MoveDeleteBar from "../MoveDeleteBar.vue";

    @Component({
        name: 'levels-view',
        components: {MoveDeleteBar}
    })
    export default class LevelsView extends Vue {
        private selectedLevel: Level|null = null;
        private removingLevel: Level|null = null;
        private editMode: boolean = false;

        isSelected(level: Level) : boolean {
            return this.selectedLevel === level;
        }

        selectLevel(level: Level|null) {
            this.selectedLevel = level;
            return false;
        }

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        get rubric() {
            return this.store.rubric;
        }

        editLevel(level: Level) {
            this.selectLevel(level);
            this.editMode = true;
        }

        addLevel() {
            const level = this.getDefaultLevel();
            this.rubric.addLevel(level);
            this.selectLevel(level);
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
            this.selectLevel(null);
        }

        setDefault(defaultLevel: Level) {
            this.store.rubric.levels.forEach(level => {
                level.isDefault = (defaultLevel === level) ? !level.isDefault : false;
            });
        }

        getDefaultLevel() {
            return new Level('');
        }

        @Watch('rubric.levels.length')
        onLevelsChanged(newLength: Number, oldLength: Number) {
            window.setTimeout(() => {
                if (newLength > oldLength) {
                    const elems = document.querySelectorAll('.level input');
                    (elems[elems.length - 1] as HTMLElement).focus();
                } else if (newLength < oldLength) {
                    (document.activeElement as HTMLElement).blur();
                }
            }, 50);
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
    .levels-container {
        flex: 1;
        display: flex;
        justify-content: flex-start;
        background-color: hsl(165, 5%, 90%);
    }
    .levels {
        display: flex;
        height: fit-content;
        margin-top: 5px;
        margin-left: 18px;
        margin-bottom: 30px;
    }
    h1 {
        font-size: 22px;
        margin-left: 10px;
        color: #666;
    }
    ul + button.btn {
        /*margin-left: 22px;*/
    }
    button.btn {
        margin-left: 6px;
    }
    .levels > div:first-child button.btn i {
        margin-right: 4px;
    }
    button[disabled]:hover {
        background: initial;
        color: initial;
    }
    button[disabled]:hover i {
        color: grey;
    }
    .btn:hover {
        color: white;
    }
    input, textarea {
        border: 1px solid transparent;
    }
    ul {
        list-style: none;
        /*margin: 14px;
        margin-left: 0;*/
        padding: 0;
    }
    .actions {
        margin-left: 10px;
        align-self: center;
    }
    .actions button {
        display: block;
        font-size: 20px;
        outline-width: thin;
        background: transparent;
    }
    .actions button:not(:disabled) i {
        color: #999;
    }
    .actions button:disabled i {
        color: #bbb;
    }
    .actions button:not(:disabled):hover i {
        color: hsla(200, 100%, 48%, 1);
    }
    li {
        display: flex;
        flex-direction: column;
        padding: 8px;
        padding-bottom: 0;
        width: 600px;
        border-bottom: 1px solid #ddd;
    }
    div.details {
        flex: 1;
        display: flex;
    }
    .details .weight {
        margin: 0 10px;
    }

    div.description {
        margin-top: 12px;
    }
    li:not(.selected) div.description {
        margin-top: 0;
    }
    .default-choice input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    /*li:not(.selected) .default-choice input:not(:checked) { opacity: 0; }
    li:not(.selected):hover .default-choice input:not(:checked) { opacity: 1; }*/

    li .default-choice input:checked + label.check { color: #406e8d;  }
    li:not(.selected) .default-choice input:not(:checked) + label.check { opacity: 0; color: #aaa; }
    li.selected .default-choice input:not(:checked) + label.check { color: #bbb; }
    li:not(.selected):hover .default-choice input:not(:checked) + label.check { opacity: 1; }
    li:not(.selected) .default-choice input:focus + label.check { opacity: 1; }
    li:not(.selected) textarea { color: #999; margin-top: -6px; padding-left: 6px; margin-bottom: -10px;}

    .level input {
        width: 100%;
        height: 40px;
        padding: 4px ;
    }

    label {
        display: block;
        color: #406e8d;
    }
    input, textarea, .delete .btn {
        border-radius: 3px;
    }
    textarea {
        width: 100%;
        resize: none;
        padding: 4px;
    }
    li label {
        transition: all 150ms;
    }
    li:not(:first-child):not(.selected) label:not(.check), li:not(.selected) .description label {
        opacity: 0;
        margin: 0;
        padding: 0;
        height: 0;
    }
    li:not(.selected) input, li:not(.selected) textarea {
        border-color: transparent;
        background: transparent;
    }
    li.selected {
        background: hsl(215, 20%, 85%);
        padding-bottom: 8px;
    }
    li.selected input:not(:focus), li.selected textarea:not(:focus) {
        background: rgba(255, 255, 255, 0.2);
    }
    .level {
        flex: 5;
        display: flex;
        flex-direction: column;
    }
    .weight {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .level label, .description label {
        padding-left: 4px;
    }
    .weight label {
        text-align: center;
    }
    .weight input {
        display: block;
        width: 70px;
        font-size: 24px;
        text-align: right;
        padding-right: 5px;
    }
    .default-choice {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .default-choice input {
        margin-top: 8px;
    }
    .default-choice * {
        cursor: pointer;
    }
    li:not(.selected) .description.empty textarea {
        display: none;
    }
    button.btn {
        outline-width: thin;
    }
    li input:hover, li .default-choice input:focus + label i, li .delete .btn:focus {
        border: 1px solid hsla(200, 50%, 50%, 0.5);
    }
    .delete .btn {
        width: 26px;
        height: 26px;
        padding: 0;
        color: transparent;
    }
    .delete .btn i {
        margin-left: 3px;
        padding: 0;
        transition: color 120ms;
    }
    li.selected .delete .btn {
        color: #999;
    }
    li:hover .delete .btn {
        background: transparent;
        color: #bbb;
    }
    .delete .btn:focus {
        outline-width: thin;
    }
    .delete:hover .btn i, .delete .btn:focus i {
        color: #d9534f;
    }
    li:first-child .delete .btn, li.selected .delete .btn {
        margin-top: 30px;
    }
    li:not(.selected):not(:first-child) .delete .btn {
        margin-top: 4px;
    }
    .modal-bg {
        position: fixed;
        background: rgba(0, 0, 0, 0.31);
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }
    .modal-level {
        background: hsl(165, 5%, 90%);
        width: 420px;
        height: 150px;
        margin: 120px auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        box-shadow: 0px 6px 12px #666;
    }
    .modal-level .title {
        padding-bottom: 16px;
        margin-bottom: 10px;
        border-bottom: 1px solid hsl(200, 30%, 80%);
        width: 100%;
        text-align: center;
    }
    .overlay {
        width: 0; height: 0;
    }
    .edit {
        display: none;
    }
    .edit button {
        background: transparent;
    }
    .edit button i {
        color: #999;
    }
    .editMode {
        margin-top: 18px;
        margin-left: 24px;
        margin-right: 24px;
        width: 100%;
    }
    .editMode > div, .editMode > form > div {
        margin-bottom: 14px;
    }
    .editMode input, .editMode textarea {
        background: rgba(255, 255, 255, 0.3);
    }
    .editMode input:focus {
        background: white;
    }
    .editMode input[type="text"] { width: 100%; }
    .editMode .weight, .editMode .default-choice {
        margin-left: 4px;
        align-items: start;
    }
    .editMode .weight input {
        text-align: left;
        padding-left: 4px;
    }
    .editMode .default-choice input + label {
        border: 1px solid transparent;
    }
    .editMode .default-choice input + label.check i { margin-right: 6px; }
    .editMode .default-choice input:checked + label.check { color: #406e8d; }
    .editMode .default-choice input:not(:checked) + label.check { color: #aaa; }
    .editMode input:hover, .editMode .default-choice input:focus + label {
        border: 1px solid hsla(200, 50%, 50%, 0.5);
    }
    .editMode textarea {
        resize: vertical;
    }
    .editMode .back {
        cursor: pointer;
    }
    .editMode .delete {
        display: block;
    }
    .editMode .delete button {
        margin: 10px 0;
        width: unset;
        color: #999;
    }
    .editMode .delete button:hover {
        background: transparent;
        color: #337ab7
    }
    .editMode .delete button i {
        margin-right: 2px;
    }
    @media only screen and (max-width: 900px) {
        .levels ul {
            pointer-events: none;
        }
        .level input {
            text-overflow: ellipsis;
        }
        .delete {
            display: none;
        }
        li:not(.selected) .default-choice input:not(:checked) + label.check { opacity: 1; color: #c9c9c9; }
        li.selected .description { display: none; }
        li.selected input[type="text"], li.selected input[type="number"] { background: transparent; }
        li:not(:first-child).selected label:not(.check) {
            opacity: 0;
            margin: 0;
            padding: 0;
            height: 0;
        }
        .levels { flex: 1 }
        .actions {
            margin-right: 10px;
        }
        .details > div {
            position: relative;
        }
        .default-choice, .edit {
            pointer-events: visible;
        }
        .overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: transparent;
            pointer-events: visible;
        }
        .edit {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        li:first-child .edit button {
            margin-top: 26px;
        }
        .edit button {
            margin-top: 2px;
        }
    }

    @media only screen and (max-width: 700px) {
        .levels {
            flex-direction: column;
            align-items: flex-start;
        }
        .details {
            width: 90vw;
        }
        .levels li {
            width: 100%;
        }
        .actions button {
            display: inline-block;
        }
    }
</style>
