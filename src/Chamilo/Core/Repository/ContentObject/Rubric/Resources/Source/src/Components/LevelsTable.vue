<template>
    <div class="table-responsive w-100">
        <table class="table table-striped table-condensed">
            <caption>
                <collapse :collapsed="collapsed" v-on:toggle-collapse="toggleConfigurationCollapsed">
                    <slot>
                        Pas niveau's aan
                    </slot>
                </collapse>
            </caption>
            <thead v-show="!collapsed" >
            <tr class="levels-header">
                <th scope="col" class="levels-title">Niveau</th>
                <th scope="col" class="levels-title">Beschrijving</th>
                <th scope="col" class="levels-title">Standaard keuze</th>
                <th scope="col" class="levels-title"></th>
            </tr>
            </thead>
            <tbody v-show="!collapsed" class="table-striped">
            <tr scope="row" v-for="(level, levelIndex) in rubric.levels">
                <td><input class="form-control text-area-level-title font-weight-bold"
                           v-model="level.title"
                           placeholder="Vul hier een niveau in"></input>
                    <b-input-group v-if="store.rubric.useScores" append="Punten" class="score-input-group">
                        <input type="number" name="Weight" class="form-control"
                               maxlength="3"
                               v-model="level.score">
                    </b-input-group>
                </td>

                <td><textarea class="form-control text-area-level-description"
                              v-model="level.description"
                              placeholder="Vul hier een beschrijving in"></textarea></td>
                <td>
                    <b-form-radio v-model="level.isDefault" name="isDefault" value=""></b-form-radio>
                </td>
                <td>
                    <MoveDeleteBar :index="levelIndex" :max-index="rubric.levels.length - 1"
                                   v-on:move-up="rubric.moveLevelUp(level)"
                                   v-on:move-down="rubric.moveLevelDown(level)"
                                   v-on:remove="removeLevel(level)">
                    </MoveDeleteBar>
                </td>
            </tr>
            <tr scope="row">
                <td :colspan="rubric.levels.length + 1" class="button-row">
                    <button class="btn btn-sm btn-primary ml-1 pull-left"
                            v-on:click="rubric.addLevel(getDefaultLevel())"><i
                            class="fa fa-plus" aria-hidden="true"></i> Voeg niveau toe
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import Level from "../Domain/Level";
    import Rubric from "../Domain/Rubric";
    import Collapse from "./Collapse.vue";
    import ScoreRubricStore from "../ScoreRubricStore";
    import MoveDeleteBar from "./MoveDeleteBar.vue";

    @Component({
        components: {MoveDeleteBar, Collapse}
    })
    export default class LevelsTable extends Vue {
        protected collapsed: boolean = true;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        get rubric() {
            return this.store.rubric;
        }

        toggleConfigurationCollapsed() {
            this.collapsed = !this.collapsed;
        }

        removeLevel(level: Level) {
            if (confirm("Niveau verwijderen?") === false) {
                return;
            }
            this.rubric.removeLevel(level);
        }

        getDefaultLevel() {
            return new Level('');
        }
    }
</script>
<style scoped>
    .level-title {
        align-self: center;
    }

    .button-row {
        padding-top: 3px;
        padding-bottom: 3px;
    }

    .text-area-level-description {
        font-size: 12px;
        width: 100%;
    }

    h3 {
        margin: 40px 0;
        padding-bottom: 10px;
        font-size: 24px
    }

    .score-input-group {
        width: 150px;
        margin-top: 3px;
    }

    .input-group-prepend,
    .input-group-append {
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #555;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .input-group-prepend, .input-group-btn,
    .input-group-append, .input-group-btn {
        width: 1%;
        white-space: nowrap;
        vertical-align: middle;
    }

    .input-group .form-control, .input-group-prepend, .input-group-btn,
    .input-group .form-control, .input-group-append, .input-group-btn {
        display: table-cell;
    }

    .input-group-prepend:first-child {
        border-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-append:last-child {
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

</style>
