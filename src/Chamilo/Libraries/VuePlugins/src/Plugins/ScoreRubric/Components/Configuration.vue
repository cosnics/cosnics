<template>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                Configuratie Rubric
            </h5>
        </div>
        <div class="card-body options">
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
    import ScoreRubricStore from "@/Plugins/ScoreRubric/ScoreRubricStore";

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
    .card-header {
        padding: 0.5rem 1.25rem;
    }
    .card-title {
        margin-bottom: 0;
    }
.options{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    text-align: left;
}
</style>
