<template>
    <div :id="id" class="category-header handle category-handle">
        <div class="item-header-bar">
            <div class="category-title">
                <a :style="{'background-color': category.color}" tabindex="0" @click="openColorPickerForCategory(category)" @keyup.enter.space="openColorPickerForCategory(category)"></a>
                <h2 class="title">{{ category.title || 'Criteria' }}</h2>
            </div>
            <div class="item-actions" :class="{'show-menu': showMenuActions}" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></div>
            <div class="action-menu" :class="{'show-menu': showMenuActions}">
                <ul class="action-menu-list">
                    <li @click.stop="startEditing" class="action-menu-list-item"><i class="fa fa-pencil" /><span>Wijzig naam</span></li>
                    <li @click.stop="$emit('remove', category)" class="action-menu-list-item"><i class="fa fa-remove" /><span>Verwijder</span></li>
                </ul>
            </div>
        </div>
        <swatches :colors="swatchColors" v-if="isColorPickerOpened" v-model="category.color" background-color="transparent" show-border swatch-size="20" inline @input="closeColorPicker"></swatches>
        <div v-if="isEditing" class="edit-title">
            <div class="cover"></div>
            <name-input class="item-new" ok-title="Wijzig" @ok="finishEditing" @cancel="cancel" placeholder="Titel voor categorie" v-model="category.title"/>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Category from '../../Domain/Category';
    import NameInput from './NameInput.vue';
    import Swatches from 'vue-swatches';

    @Component({
        name: 'category-view',
        components: { NameInput, Swatches }
    })
    export default class CategoryView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';

        // Color palette generated with http://medialab.github.io/iwanthue/
        private readonly swatchColors = ['', '#5e318e', '#bd002f', '#b10099', '#1c5ce2', '#00943e', '#0182ed', '#ff2b84', '#e76f01', '#c58d00', '#ff9385', '#b7aaff', '#a4c592', '#56e9c2', '#56ee7a', '#e8d275'];

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: String, required: true}) readonly editCategoryColorId!: string;
        @Prop({type: Category, required: true}) readonly category!: Category;

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
            // todo: dataConnector: how to deal with updates? How to deal with the color swatch update?
            this.isEditing = true;
            this.oldTitle = this.category.title;
            this.$emit('start-edit');
        }

        finishEditing() {
            this.isEditing = false;
            this.oldTitle = '';
            this.$emit('finish-edit');
        }

        cancel() {
            this.category.title = this.oldTitle;
            this.finishEditing();
        }
    }
</script>
