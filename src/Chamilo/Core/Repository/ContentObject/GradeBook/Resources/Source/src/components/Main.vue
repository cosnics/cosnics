<template>
    <div>
        <div class="banner">
            <h1 class="banner-header">GradeBook</h1>
        </div>
        <div style="margin:25px 20px 20px;display:flex;gap:20px">
            <div style="flex:1"><input class="form-control" type="text" placeholder="Zoek student"></div>
            <grades-dropdown id="dropdown-main" :graded-items="gradeBook.gradedItemsWithStatusAdded" @toggle="toggleGradeItem"></grades-dropdown>
        </div>
        <div>
            <div class="sticky-table-columns">
                <div style="display:flex;justify-content:right;margin: 0 0 7px;gap: 5px">
                    <button class="btn btn-default btn-sm" style="padding: 3px 9px 3px 7px;" @click="createNewScore"><i data-v-1b75ca2e="" aria-hidden="true" class="fa fa-plus" style="margin-right: 5px;color: #406e8e;"></i>Nieuwe score</button>
                    <button class="btn btn-default btn-sm" style="padding: 3px 9px 3px 7px;" @click="createNewCategory"><i data-v-1b75ca2e="" aria-hidden="true" class="fa fa-plus" style="margin-right: 5px;color: #406e8e;"></i>Categorie</button>
                </div>
                <grades-table :grade-book="gradeBook" @item-settings="itemSettings = $event" @category-settings="categorySettings = $event"></grades-table>
            </div>
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
            const id = Math.max.apply(null, this.gradeBook.categories.map(cat => cat.id)) + 1;
            const newCategory = { id, name: 'Categorie', color: '#92eded', itemIds: [] };
            this.gradeBook.categories.push(newCategory);
            this.categorySettings = id;
        }

        createNewScore() {
            const id = this.gradeBook.createNewStandaloneScoreId();
            this.gradeBook.gradeColumns.push({id, name: 'Score', type: 'standalone', weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2});
            this.gradeBook.nullCategory.itemIds.push(id);
            this.gradeBook.resultsData.forEach(d => {
                d.results[id] = {value: null, ref: id, overwritten: true};
            });
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

.sticky-table-columns {
  margin: -10px 20px 20px;
}
</style>

