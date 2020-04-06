<template>
    <div class="actions">
        <div v-if="inputFormShown">
            <name-input ok-title="Voeg Toe" class="cluster-new item-new" @ok="addNewCluster" @cancel="cancel" placeholder="Titel voor nieuwe cluster" v-model="newCluster.title" />
        </div>
        <button v-else :disabled="!actionsEnabled" @click="createNewCluster"><i class="fa fa-plus" aria-hidden="true"/>Nieuw</button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Cluster from '../../Domain/Cluster';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'new-cluster',
        components: { NameInput }
    })
    export default class NewCluster extends Vue {
        private newCluster: Cluster|null = null;

        @Prop({type: String, default: null}) readonly viewId!: string|null;
        @Prop({type: Boolean, default: true}) readonly actionsEnabled!: boolean;

        get store() {
            return this.$root.$data.store;
        }

        get inputFormShown() {
            return this.newCluster !== null;
        }

        createNewCluster() {
            this.newCluster = new Cluster();
            this.emit(this.viewId);
        }

        addNewCluster() {
            this.store.rubric.addChild(this.newCluster, this.store.rubric.clusters.length);
            this.$emit('cluster-selected', this.newCluster);
            this.cancel();
        }

        cancel() {
            this.newCluster = null;
            this.emit('');
        }

        emit(value: string|null) {
            if (this.viewId !== null) {
                this.$emit('dialog-view', value);
            }
        }
    }
</script>