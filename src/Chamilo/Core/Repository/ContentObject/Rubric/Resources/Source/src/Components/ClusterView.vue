<i18n>
{
    "en": {
        "edit": "Edit",
        "edit-title": "Edit title",
        "open-menu": "Open menu",
        "remove": "Remove",
        "title-subsection": "Title of subsection"
    },
    "fr": {
        "edit": "Modifier",
        "edit-title": "Modifier le titre",
        "open-menu": "Ouvrer le menu",
        "remove": "Supprimer",
        "title-subsection": "Titre de la sous-section"
    },
    "nl": {
        "edit": "Wijzig",
        "edit-title": "Wijzig titel",
        "open-menu": "Open menu",
        "remove": "Verwijder",
        "title-subsection": "Titel voor onderverdeling"
    }
}
</i18n>

<template>
    <component :is="tag" :id="id" tabindex="0" @keydown.enter="$emit('cluster-selected', cluster)" @keyup.space="$emit('cluster-selected', cluster)" @click.stop="$emit('cluster-selected', cluster)">
        <div class="b-cluster handle cluster-handle" tabindex="-1" :class="{ 'is-selected': selected, 'is-menu-visible': showMenuActions }">
            <div class="item-header-bar">
                <div class="b-cluster-title" @dblclick="startEditing" :title="cluster.title">
                    <!--<div class="title"><div><i class="fa fa-map-o" aria-hidden="true"/><span>{{cluster.title}}</span></div></div>-->
                    {{cluster.title}}
                </div>
                <button class="btn-toggle-menu mod-cluster" :class="{'is-menu-visible': showMenuActions}" :aria-label="!showMenuActions && $t('open-menu')" :title="!showMenuActions && $t('open-menu')" @keyup.space.stop="" @keydown.enter.stop="" @click.prevent.stop="onItemActions"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></button>
                <div class="action-menu mod-cluster mod-menu-fixed" :class="{'is-menu-visible': showMenuActions }" :style="{ left: `${menuX}px`, top: `${menuY}px`}">
                    <ul class="action-menu-list">
                        <li @click.stop="startEditing" role="button" @keyup.space.enter="startEditing" class="action-menu-list-item" :class="{ 'is-cluster-selected': selected }" tabindex="0"><i class="action-menu-icon fa fa-pencil" aria-hidden="true" /><span class="action-menu-text">{{ $t('edit-title' )}}</span></li>
                        <li @click.stop="$emit('remove', cluster)" role="button" @keyup.space.enter="$emit('remove', cluster)" class="action-menu-list-item" :class="{ 'is-cluster-selected': selected }" tabindex="0"><i class="action-menu-icon fa fa-remove" aria-hidden="true" /><span class="action-menu-text">{{ $t('remove') }}</span></li>
                    </ul>
                </div>
            </div>
            <div v-if="isEditing" class="edit-title">
                <div class="modal-bg" @click.stop=""></div>
                <name-input class="item-new mod-edit" :ok-title="$t('edit')" @ok="finishEditing" @cancel="cancel" :placeholder="$t('title-subsection')" v-model="newTitle" />
            </div>
        </div>
    </component>
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
        private menuX = 0;
        private menuY = 0;

        @Prop({type: String, default: 'div'}) readonly tag!: String;
        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: Boolean, required: true}) readonly selected!: boolean;
        @Prop({type: Cluster, required: true}) readonly cluster!: Cluster;

        mounted() {
            this.resetTitle();
        }

        onItemActions(event: any) {
            this.$emit('item-actions', this.id);
            const rect = event.target.getBoundingClientRect();
            this.menuX = rect.x;
            this.menuY = rect.bottom + 36;
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