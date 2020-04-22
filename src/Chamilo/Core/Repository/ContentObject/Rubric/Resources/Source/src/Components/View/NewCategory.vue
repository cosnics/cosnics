<template>
    <div v-if="inputFormShown" class="category newcategory">
        <name-input ok-title="Voeg Toe" allow-empty="true" class="category-new item-new" @ok="addNewCategory" @cancel="cancel" placeholder="Titel voor nieuwe categorie" v-model="newCategory.title"/>
    </div>
    <div v-else class="actions">
        <button :disabled="!actionsEnabled" class="btn-category-add" @click="createNewCategory"><i class="fa fa-plus" aria-hidden="true"/>Nieuw</button>
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

        @Prop({type: Cluster, required: true}) readonly cluster!: Cluster;
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

        addNewCategory() {
            this.cluster.addChild(this.newCategory!, this.cluster.categories.length);
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