<template>
    <div :id="id" class="handle handle-area-category">
        <a v-if="isCategory" :style="{'background-color': category.color}" tabindex="0" @click="openColorPickerForCategory(category)" @keyup.enter.space="openColorPickerForCategory(category)"></a>
        <h2 class="handle-area-category">{{ isCategory ? category.title : 'Criteria' }}</h2>
        <swatches v-if="isCategory && isColorPickerOpened" v-model="category.color" background-color="transparent" show-border swatch-size="20" inline @input="closeColorPicker"></swatches>
        <div class="item-actions" :class="{'show-menu': showMenuActions}" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></div>
        <div v-if="showMenuActions" class="action-menu">
            <ul>
                <li v-if="isCategory" @click.stop="startEditing"><i class="fa fa-pencil" />Wijzig naam</li>
                <li @click.stop="$emit('remove', category)"><i class="fa fa-remove" />Verwijder</li>
            </ul>
        </div>
        <div v-if="isCategory && isEditing" class="edit-title">
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

    //const swatchColors = ['#FF0000', '#00FF00', '#F493A7', '#F891A6', '#FFCCD5', 'hsl(190, 100%, 50%)'];

    @Component({
        name: 'category-view',
        components: { NameInput, Swatches }
    })
    export default class CategoryView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: String, required: true}) readonly editCategoryColorId!: string;
        @Prop({type: Category, required: true}) readonly category!: Category;

        get isCategory() {
            return this.category.color !== '';
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