<i18n>
{
    "en": {
        "change-into-category": "Change into categorie",
        "color-picker": "Color Picker",
        "edit": "Edit",
        "edit-title": "Edit title",
        "list-of-criteria": "List of criteria",
        "open-menu": "Open menu",
        "remove": "Remove",
        "title-category": "Title of category"
    },
    "fr": {
        "change-into-category": "Transformer en catégorie",
        "color-picker": "Pipette à Couleurs",
        "edit": "Modifier",
        "edit-title": "Modifier le titre",
        "list-of-criteria": "Liste des critères",
        "open-menu": "Ouvrer le menu",
        "remove": "Supprimer",
        "title-category": "Titre de la catégorie"
    },
    "nl": {
        "change-into-category": "Wijzig naar categorie",
        "color-picker": "Kleurenkiezer",
        "edit": "Wijzig",
        "edit-title": "Wijzig titel",
        "list-of-criteria": "Lijst met criteria",
        "open-menu": "Open menu",
        "remove": "Verwijder",
        "title-category": "Titel voor categorie"
    }
}
</i18n>

<template>
    <div :id="id" class="b-category-list-item handle category-handle" :class="{ 'mod-null-category': !category.title }">
        <div class="item-header-bar mod-category">
            <button v-if="category.title" :aria-label="$t('color-picker')" :title="$t('color-picker')" :aria-expanded="isColorPickerOpened ? 'true' : 'false'" :aria-controls="id + '--swatches'" class="btn-category-color" :class="{ 'xvue-swatches__diagonal': category.color === '' }" :style="{'background-color': category.color || null}" @click="openColorPickerForCategory(category)"></button>
            <div class="b-category-header-wrapper" :class="{ 'mod-null-category': !category.title }">
                <swatches :id="id + '--swatches'"
                          :swatches="swatchColors"
                          v-if="isColorPickerOpened"
                          v-model="category.color"
                          background-color="transparent"
                          show-border swatch-size="20"
                          inline
                          @input="closeColorPicker"
                ></swatches>
                <h2 class="b-category-title" :class="{ 'mod-null-category': !category.title }" @dblclick.stop="startEditing">{{ category.title || $t('list-of-criteria') }}</h2>
            </div>
            <button class="btn-toggle-menu mod-category" :class="{'is-menu-visible': showMenuActions}" :aria-label="!showMenuActions && $t('open-menu')" :title="!showMenuActions && $t('open-menu')" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></button>
            <div class="action-menu mod-category" :class="{'is-menu-visible': showMenuActions}">
                <ul class="action-menu-list">
                    <li @click.stop="startEditing" @keyup.space.enter="startEditing" class="action-menu-list-item" tabindex="0"><i class="action-menu-icon fa fa-pencil" /><span class="action-menu-text">{{ category.title ? $t('edit-title') : $t('change-into-category') }}</span></li>
                    <li @click.stop="$emit('remove', category)" @keyup.space.enter="$emit('remove', category)" class="action-menu-list-item" tabindex="0"><i class="action-menu-icon fa fa-remove" /><span class="action-menu-text">{{ $t('remove') }}</span></li>
                </ul>
            </div>
        </div>
        <div v-if="isEditing" class="edit-title">
            <div class="modal-bg"></div>
            <name-input class="item-new mod-edit" :ok-title="$t('edit')" @ok="finishEditing" @cancel="cancel" allow-empty="true" :placeholder="$t('title-category')" v-model="newTitle"/>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Category from '../../../Domain/Category';
    import NameInput from './NameInput.vue';
    import Swatches from 'vue-swatches';

    @Component({
        name: 'category-view',
        components: { NameInput, Swatches }
    })
    export default class CategoryView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';
        private newTitle: string = '';

        // Color palette generated with http://medialab.github.io/iwanthue/
        private readonly swatchColors = ['', '#000000', '#5e318e', '#bd002f', '#b10099', '#1c5ce2', '#00943e', '#0182ed', '#fddf00', '#ff2b84', '#e76f01', '#c58d00', '#ff9385', '#b7aaff', '#a4c592', '#56e9c2'];

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: String, required: true}) readonly editCategoryColorId!: string;
        @Prop({type: Category, required: true}) readonly category!: Category;

        mounted() {
            this.resetTitle();
        }

        resetTitle() {
            this.newTitle = this.category.title;
        }

        get showMenuActions() {
            return this.menuActionsId === this.id;
        }

        openColorPickerForCategory(category: Category|null) {
            const id = category ? this.id : '';
            this.$emit('color-picker', this.editCategoryColorId !== id ? id : '');
        }

        get isColorPickerOpened() : boolean {
            if (this.editCategoryColorId === '') { return false; }
            return this.id === this.editCategoryColorId;
        }

        closeColorPicker() {
            window.setTimeout(() => this.openColorPickerForCategory(null), 400);
        }

        startEditing() {
            this.isEditing = true;
            this.oldTitle = this.category.title;
            this.$emit('start-edit');
        }

        finishEditing(canceled=false) {
            this.isEditing = false;
            this.oldTitle = '';
            this.$emit('finish-edit', this.newTitle, canceled);
        }

        cancel() {
            this.category.title = this.oldTitle;
            this.finishEditing(true);
            this.resetTitle();
        }

        // Because mounted() only occurs once, and this component keeps its own state, we have to check if the title has changed through an external update.
        @Watch('category.title')
        onTitleChanged() {
            this.resetTitle();
        }

        @Watch('category.color')
        onColorChanged() {
            this.$emit('change-color');
        }
    }
</script>
