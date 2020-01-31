<template>
    <div>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon0">Titel</span>
        <input class="form-control mb-2"
               v-model="criterium.title"
               placeholder="Vul hier het criterium in"/>
        </div>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">gewicht</span>
            <input type="number" name="Score" class="form-control "
                   placeholder="Gewicht %" min="0" max="100" maxlength="3"
                   v-model="criterium.weight">
            <span class="input-group-addon" id="basic-addon2">%</span>
        </div>

        <table class="rubric-table table table-condensed table-striped">
            <thead>
            <th scope="col" v-for="level in store.rubric.levels">
                <i v-if="level.description" class="fa fa-info-circle mr-2" aria-hidden="true"
                   v-b-popover.hover.top="level.description"></i>{{ level.title }}
            </th>
            </thead>
            <tbody>
            <td v-for="level in store.rubric.levels" class="score">
                    <textarea class="form-control text-area-level-description mb-2 feedback-text"
                              v-model="store.rubric.getChoice(criterium, level).feedback"
                              placeholder="Geef feedback"></textarea>
                <div v-if="store.rubric.useScores">
                    {{store.rubric.getChoiceScore(criterium, level)}} punten
                </div>
            </td>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import Criterium from "../../Domain/Criterium";

    @Component({
        components: {}
    })
    export default class CriteriumNodeBuilder extends Vue {
        @Prop()
        criterium!: Criterium;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }
    }
</script>

<style scoped>
    .options {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        text-align: left;
    }

    .score-checkbox {
        margin-right: 2px;
    }

    .feedback-text {
        height: 150px;
    }
</style>
