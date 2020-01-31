<template>
    <div>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon0">Titel</span>
        <input class="form-control mb-2"
               v-model="cluster.title"
               placeholder="Vul hier het criterium in"/>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import Cluster from "../../Domain/Cluster";

    @Component({
        components: {}
    })
    export default class ClusterNodeBuilder extends Vue {
        @Prop()
        cluster!: Cluster;

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
