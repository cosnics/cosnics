<template>
    <li :id="id" class="cluster-list-item">
        <div class="cluster handle cluster-handle" :class="{ selected, 'show-menu': showMenuActions }">
            <div class="item-header-bar ">
                <div class="cluster-title" @click.stop="$emit('cluster-selected', cluster)">
                    <!--<div class="title"><div><i class="fa fa-map-o" aria-hidden="true"/><span>{{cluster.title}}</span></div></div>-->
                    {{cluster.title}}
                </div>
                <div class="item-actions" :class="{'show-menu': showMenuActions}" @click.prevent.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></div>
                <div class="action-menu" :class="{'show-menu': showMenuActions}">
                    <ul class="action-menu-list">
                        <li @click.stop="startEditing" class="action-menu-list-item"><i class="fa fa-pencil" /><span>Wijzig naam</span></li>
                        <li @click.stop="$emit('remove', cluster)" class="action-menu-list-item"><i class="fa fa-remove" /><span>Verwijder</span></li>
                    </ul>
                </div>
            </div>
            <div v-if="isEditing" class="edit-title">
                <div class="cover" @click.stop=""></div>
                <name-input class="item-new" ok-title="Wijzig" @ok="finishEditing" @cancel="cancel" placeholder="Titel voor onderverdeling" v-model="newTitle" />
            </div>
        </div>
    </li>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Cluster from '../Domain/Cluster';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'cluster-view',
        components: { NameInput }
    })
    export default class ClusterView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';
        private newTitle: string = '';

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: Boolean, required: true}) readonly selected!: boolean;
        @Prop({type: Cluster, required: true}) readonly cluster!: Cluster;

        mounted() {
            this.resetTitle();
        }

        resetTitle() {
            this.newTitle = this.cluster.title;
        }

        get showMenuActions() {
            return this.menuActionsId === this.id;
        }

        startEditing() {
            // todo: dataConnector: how to deal with updates?
            this.isEditing = true;
            this.oldTitle = this.cluster.title;
            this.$emit('start-edit');
        }

        finishEditing(canceled=false) {
            this.$emit('finish-edit', this.newTitle, canceled);
            this.isEditing = false;
            this.oldTitle = '';
        }

        cancel() {
            this.cluster.title = this.oldTitle;
            this.finishEditing(true);
            this.resetTitle();
        }

        // Because mounted() only occurs once, and this component keeps its own state, we have to check if the title has changed through an external update.
        @Watch('cluster.title')
        onTitleChanged() {
            this.resetTitle();
        }
    }
</script>