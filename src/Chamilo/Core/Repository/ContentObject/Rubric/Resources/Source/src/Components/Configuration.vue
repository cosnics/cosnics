<template>
    <b-form inline >
        <b-form-checkbox class="pull-left score-checkbox" v-model="store.rubric.useScores"
                         id="score-input"
                         name="score-input"
                         :value="true"
                         :unchecked-value="false">
              Gebruikt score
        </b-form-checkbox>
    </b-form>

</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../ScoreRubricStore";

    @Component({
        components: {}
    })
    export default class Configuration extends Vue {
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
</style>
