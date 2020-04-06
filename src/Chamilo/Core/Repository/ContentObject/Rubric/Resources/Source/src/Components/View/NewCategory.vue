<template>
    <div v-if="inputFormShown" class="category newcategory">
        <name-input v-if="isAddingCategory" ok-title="Voeg Toe" class="category-new item-new" @ok="addNewCategory" @cancel="cancel" placeholder="Titel voor nieuwe categorie" v-model="newCategory.title"/>
        <name-input v-else ok-title="Voeg Toe" class="category-new item-new" @ok="addNewCriterium" @cancel="cancel" placeholder="Titel voor nieuw criterium" v-model="newCriterium.title"/>
    </div>
    <div v-else class="actions">
        <button :disabled="!actionsEnabled" class="btn-category-add" @click="createNewCategory"><i class="fa fa-plus" aria-hidden="true"/>Categorie</button>
        <button :disabled="!actionsEnabled" class="btn-criterium-add" @click="createNewCriterium"><i class="fa fa-plus" aria-hidden="true"/>Criterium</button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'new-category',
        components: { NameInput }
    })
    export default class NewCategory extends Vue {

        private newCategory: Category|null = null;
        private newCriterium: Criterium|null = null;

        @Prop({type: Cluster, required: true}) readonly cluster!: Cluster;
        @Prop({type: String, default: null}) readonly viewId!: string|null;
        @Prop({type: Boolean, default: true}) readonly actionsEnabled!: boolean;

        get inputFormShown() {
            return this.isAddingCategory || this.isAddingCriterium;
        }

        get isAddingCategory() {
            return this.newCategory !== null;
        }

        get isAddingCriterium() {
            return this.newCriterium !== null;
        }

        createNewCategory() {
            this.newCategory = new Category();
            this.newCategory.color = 'transparent';
            this.emit(this.viewId);
        }

        createNewCriterium() {
            this.newCategory = new Category();
            this.newCategory.color = '';
            this.newCriterium = new Criterium();
            this.emit(this.viewId);
        }

        addNewCategory() {
            this.cluster.addChild(this.newCategory!, this.cluster.categories.length);
            this.cancel();
        }

        addNewCriterium() {
            this.newCategory!.addChild(this.newCriterium!, this.newCategory!.criteria.length);
            this.addNewCategory();
        }

        cancel() {
            this.newCriterium = null;
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