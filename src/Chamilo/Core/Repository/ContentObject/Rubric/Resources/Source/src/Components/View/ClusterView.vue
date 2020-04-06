<template>
    <li :id="id" class="cluster" :class="{ selected }" @click="$emit('cluster-selected', cluster)">
        <div class="title"><div><i :class="cluster.title === '' ? 'fa fa-institution' : 'fa fa-map-o'" aria-hidden="true"/><span>{{cluster.title}}</span></div></div>
        <div class="item-actions" :class="{'show-menu': showMenuActions}" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></div>
        <div v-if="showMenuActions" class="action-menu">
            <ul>
                <li @click.stop="startEditing"><i class="fa fa-pencil" />Wijzig naam</li>
                <li @click.stop="$emit('remove', cluster)"><i class="fa fa-remove" />Verwijder</li>
            </ul>
        </div>
        <div v-if="isEditing" class="edit-title">
            <div class="cover" @click.stop=""></div>
            <name-input class="item-new" ok-title="Wijzig" @ok="finishEditing" @cancel="cancel" placeholder="Titel voor cluster" v-model="cluster.title" />
        </div>
    </li>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Cluster from '../../Domain/Cluster';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'cluster-view',
        components: { NameInput }
    })
    export default class ClusterView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: Boolean, required: true}) readonly selected!: boolean;
        @Prop({type: Cluster, required: true}) readonly cluster!: Cluster;

        get showMenuActions() {
            return this.menuActionsId === this.id;
        }

        startEditing() {
            this.isEditing = true;
            this.oldTitle = this.cluster.title;
            this.$emit('start-edit');
        }

        finishEditing() {
            this.isEditing = false;
            this.oldTitle = '';
            this.$emit('finish-edit');
        }

        cancel() {
            this.cluster.title = this.oldTitle;
            this.finishEditing();
        }
    }
</script>