<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">
                Configuratie Rubric
            </h5>
        </div>
        <div class="panel-body options">
            <b-form-checkbox v-model="store.rubric.useScores"
                             name="score"
                             :value="true"
                             :unchecked-value="false">
                Gebruik score
            </b-form-checkbox>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../ScoreRubricStore";

    @Component({
        components: {}
    })
    export default class Configuration extends Vue{
        get store():ScoreRubricStore {
            return this.$root.$data.store;
        }
    }
</script>

<style scoped>
.options{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    text-align: left;
}
</style>
