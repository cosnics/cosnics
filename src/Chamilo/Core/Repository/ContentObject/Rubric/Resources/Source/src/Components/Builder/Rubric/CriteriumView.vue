<i18n>
{
    "en": {
        "edit": "Edit",
        "edit-title": "Edit title",
        "details": "Details",
        "open-menu": "Open menu",
        "remove": "Remove",
        "title-criterium": "Title of criterium"
    },
    "fr": {
        "edit": "Modifier",
        "edit-title": "Modifier le titre",
        "details": "Détails",
        "open-menu": "Ouvrer le menu",
        "remove": "Supprimer",
        "title-criterium": "Titre du critère"
    },
    "nl": {
        "edit": "Wijzig",
        "edit-title": "Wijzig titel",
        "details": "Details",
        "open-menu": "Open menu",
        "remove": "Verwijder",
        "title-criterium": "Titel voor criterium"
    }
}
</i18n>

<template>
    <component :is="tag" :id="id" class="handle criterium-handle">
        <div class="b-criterium" :class="{ 'is-selected': selected }">
            <div class="item-header-bar mod-criterium">
                <div @click="$emit('criterium-selected', criterium)" @dblclick.stop="startEditing" @keyup.space.enter="$emit('criterium-selected', criterium)" class="b-criterium-title-wrapper" tabindex="0">
                    <h3 class="b-criterium-title u-markdown-criterium" tabindex="-1" v-html="criterium.toMarkdown()"></h3>
                </div>
                <button class="btn-toggle-menu mod-criterium" :class="{'is-menu-visible': showMenuActions}" :aria-label="!showMenuActions && $t('open-menu')" :title="!showMenuActions && $t('open-menu')" @keyup.space.stop="" @keydown.enter.stop="" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></button>
                <div class="action-menu mod-criterium" :class="{'is-menu-visible': showMenuActions}">
                    <ul class="action-menu-list">
                        <li @click="$emit('criterium-selected', criterium)" @keyup.space.enter="$emit('criterium-selected', criterium)" class="action-menu-list-item menu-list-item-details" tabindex="0"><i class="action-menu-icon fa fa-search"></i><span class="action-menu-text">{{ $t('details') }}</span></li>
                        <li @click.stop="startEditing" @keyup.space.enter="startEditing" class="action-menu-list-item" tabindex="0"><i class="action-menu-icon fa fa-pencil" /><span class="action-menu-text">{{ $t('edit-title') }}</span></li>
                        <li @click.stop="$emit('remove', criterium)" @keyup.space.enter="$emit('remove', criterium)" class="action-menu-list-item" tabindex="0"><i class="action-menu-icon fa fa-remove" /><span class="action-menu-text">{{ $t('remove') }}</span></li>
                    </ul>
                </div>
            </div>
            <div v-if="isEditing" class="edit-title">
                <div class="modal-bg"></div>
                <name-input class="item-new mod-edit" :ok-title="$t('edit')" @ok="finishEditing" @cancel="cancel" :placeholder="$t('title-criterium')" v-model="newTitle"/>
            </div>
        </div>
    </component>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Criterium from '../../../Domain/Criterium';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'criterium-view',
        components: { NameInput }
    })
    export default class CriteriumView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';
        private newTitle: string = '';

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, default: 'div'}) readonly tag!: String;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: Boolean, required: true}) readonly selected!: boolean;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;

        mounted() {
            this.resetTitle();
        }

        resetTitle() {
            this.newTitle = this.criterium.title;
        }

        get showMenuActions() {
            return this.menuActionsId === this.id;
        }

        startEditing() {
            // todo: dataConnector: how to deal with updates?
            this.isEditing = true;
            this.oldTitle = this.criterium.title;
            this.$emit('start-edit');
        }

        finishEditing(canceled=false) {
            this.isEditing = false;
            this.oldTitle = '';
            this.$emit('finish-edit', this.newTitle, canceled);
        }

        cancel() {
            this.criterium.title = this.oldTitle;
            this.finishEditing(true);
            this.resetTitle();
        }

        // Because mounted() only occurs once, and this component keeps its own state, we have to check if the title has changed through an external update.
        @Watch('criterium.title')
        onTitleChanged() {
            this.resetTitle();
        }
    }
</script>