<template>
    <div>
        <div class="banner">
            <h1 class="banner-header">GradeBook</h1>
        </div>
        <div class="gradebook-toolbar">
            <input class="form-control" type="text" placeholder="Zoek student">
            <grades-dropdown id="dropdown-main" :graded-items="gradeBook.gradedItemsWithStatusAdded" @toggle="toggleGradeItem"></grades-dropdown>
        </div>
        <div class="gradebook-table-container">
            <div class="gradebook-create-actions">
                <button class="btn btn-default btn-sm" @click="createNewScore"><i aria-hidden="true" class="fa fa-plus"></i>Nieuwe score</button>
                <button class="btn btn-default btn-sm" @click="createNewCategory"><i aria-hidden="true" class="fa fa-plus"></i>Categorie</button>
            </div>
            <grades-table :grade-book="gradeBook" @item-settings="itemSettings = $event" @category-settings="categorySettings = $event"></grades-table>
        </div>
        <item-settings v-if="itemSettings !== null" :grade-book="gradeBook" :item-id="itemSettings" @close="itemSettings = null" @item-settings="itemSettings = $event"></item-settings>
        <category-settings v-if="selectedCategory" :category="selectedCategory" @close="closeSelectedCategory"></category-settings>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import GradesDropdown from './GradesDropdown.vue';
    import GradesTable from './GradesTable.vue';
    import GradeBook, {GradeItem} from '../domain/GradeBook';
    import ItemSettings from './ItemSettings.vue';
    import CategorySettings from './CategorySettings.vue';

    @Component({
        components: { GradesTable, GradesDropdown, ItemSettings, CategorySettings }
    })
    export default class Main extends Vue {
        private itemSettings: number|null = null;
        private categorySettings: number|null = null;

        @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;

        toggleGradeItem(item: GradeItem, isAdding: boolean) {
            this.gradeBook.toggleGradeItem(item, isAdding);
        }

        get selectedCategory() {
            return this.gradeBook.categories.find(cat => cat.id === this.categorySettings) || null;
        }

        createNewCategory() {
            const category = this.gradeBook.createNewCategory();
            this.categorySettings = category.id;
        }

        createNewScore() {
            this.gradeBook.createNewScore();
        }

        closeSelectedCategory() {
            this.categorySettings = null;
        }
    }
</script>

<style>
.u-flex {
    display: flex;
}

.u-gap-small {
    gap: 5px;
}

.u-flex-wrap {
    flex-flow: wrap;
}

.u-txt-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.banner {
    background-color: #2b6597;
    border-bottom: 1px solid #14578f;
    padding: 20px 15px;
}

.banner-header {
    color: #fff;
    font-size: 2.2rem;
    margin: 0;
}
</style>

<style lang="scss" scoped>
.gradebook-toolbar {
    display: flex;
    gap: 20px;
    margin: 25px 20px 20px;

    .form-control {
        flex: 1;
    }
}

.gradebook-table-container {
    margin: -10px 20px 20px;
}

.gradebook-create-actions {
    display:flex;
    gap: 5px;
    justify-content: flex-end;
    margin: 0 0 10px;

    .btn {
        padding: 3px 9px 3px 7px;
    }

    .fa {
        color: #406e8e;
        margin-right: 5px;
    }
}

#dropdown-main {
    flex: 1;
}
</style>

