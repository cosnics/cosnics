<i18n>
{
    "en": {
        "add": "Add",
        "add-category": "Add new category",
        "title-new-category": "Title of new category"
    },
    "fr": {
        "add": "Ajouter",
        "add-category": "Ajouter une catégorie",
        "title-new-category": "Titre de la nouvelle catégorie"
    },
    "nl": {
        "add": "Voeg Toe",
        "add-category": "Categorie toevoegen",
        "title-new-category": "Titel voor nieuwe categorie"
    }
}
</i18n>

<template>
    <div v-if="inputFormShown" class="category-name-input">
        <name-input :ok-title="$t('add')" class="category-new item-new" @ok="addNewCategory" @cancel="cancel" :placeholder="$t('title-new-category')" v-model="newCategory.title"/>
    </div>
    <div v-else class="actions">
        <button :disabled="!actionsEnabled" class="btn-new mod-category-add" @keydown.enter="blockEnterUp" @click="createNewCategory">{{ $t('add-category') }}</button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Category from '../../../Domain/Category';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'new-category',
        components: { NameInput }
    })
    export default class NewCategory extends Vue {
        private newCategory: Category|null = null;
        private blockKeyUpEnter = false;

        @Prop({type: String, default: null}) readonly viewId!: string|null;
        @Prop({type: Boolean, default: true}) readonly actionsEnabled!: boolean;

        get inputFormShown() {
            return this.newCategory !== null;
        }

        createNewCategory() {
            this.newCategory = new Category();
            this.newCategory.color = '';
            this.emit(this.viewId);
        }

        blockEnterUp() {
            this.blockKeyUpEnter = true;
        }

        checkAndReleaseBlockEnterUp() {
            if (this.blockKeyUpEnter) {
                this.blockKeyUpEnter = false;
                return true;
            }
            return false;
        }

        addNewCategory() {
            if (this.checkAndReleaseBlockEnterUp()) { return; }
            this.$emit('category-added', this.newCategory);
            this.cancel();
        }

        cancel() {
            this.newCategory = null;
            this.emit('');
        }

        emit(value: string|null) {
            if (this.viewId !== null) {
                this.$emit('dialog-view', value);
            }
        }
    }
</script>