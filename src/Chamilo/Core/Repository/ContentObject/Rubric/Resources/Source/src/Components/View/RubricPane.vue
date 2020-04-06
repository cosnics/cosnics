<template>
    <div>
        <div class="clusters-view" @mouseover="dragMouseOver(`${id}_clusters`)" @mouseout="dragMouseOut" :class="{ 'no-drop': clusterDragging && bannedForDrop === `${id}_clusters` }">
            <draggable :disabled="draggableDisabled" :id="`${id}_clusters`" tag="ul" group="clusters" class="clusters" ghost-class="ghost" :list="clusters" :class="{ 'cluster-dragging': clusterDragging }" :forceFallback="true" :animation="250"
                       :move="onMoveCluster" @start="startDrag($event, 'cluster')" @end="endDrag" @change="onChangeCluster">
                <cluster-view v-for="cluster in clusters"
                       :id="`${id}_${cluster.id}`" :key="`${id}_${cluster.id}`" :cluster="cluster" :menu-actions-id="menuActionsId" :selected="isSelected(cluster)"
                       @cluster-selected="selectCluster" @item-actions="$emit('item-actions', $event)" @remove="onRemove" @start-edit="onStartEdit" @finish-edit="onFinishEdit"></cluster-view>
            </draggable>
            <new-cluster :view-id="id" :actions-enabled="clusterActionsEnabled" @dialog-view="$emit('dialog-new-cluster', $event)" @cluster-selected="selectCluster"></new-cluster>
        </div>
        <div class="cluster-content" ref="cluster-content" @mouseover="categoryDragging && dragMouseOver(`${id}_categories`)" @mouseout="categoryDragging && dragMouseOut" :class="{ 'no-drop': categoryDragging && bannedForDrop === `${id}_categories` }">
            <draggable :disabled="draggableDisabled" :id="`${id}_categories`" tag="div" group="categories" handle=".handle" ghost-class="ghost" :list="categories" :forceFallback="true" :animation="250" :move="onMoveCategory"
                       @start="startDrag($event, 'category')" @end="endDrag" @change="onChangeCategory">
                <div v-for="category in categories" @mouseover="criteriumDragging && dragMouseOver(`${id}_${category.id}`)" @mouseout="criteriumDragging && dragMouseOut" :id="`${id}_${category.id}`" :key="`${id}_${category.id}`" class="category" :class="{ 'no-drop': criteriumDragging && bannedForDrop === `${id}_${category.id}`, 'null-category': category.color === '' }">
                    <category-view :id="`${id}_${category.id}`" :key="`${id}_${category.id}`" :category="category" :menu-actions-id="menuActionsId" :edit-category-color-id="editCategoryColorId"
                                   @color-picker="$emit('color-picker', $event)" @item-actions="$emit('item-actions', $event)" @remove="onRemove" @start-edit="onStartEdit" @finish-edit="onFinishEdit"></category-view>
                    <draggable :key="`${id}_${category.id}_draggable`" :disabled="draggableDisabled" tag="div" group="criteria" handle=".criterium" ghost-class="ghost" swapTreshold="0.75" :list="category.criteria" :forceFallback="true" :animation="250"
                               :move="onMoveCriterium" @start="startDrag($event,'criterium')"	@end="endDrag"	@change="onChangeCriterium($event, category)">
                        <criterium-view v-for="criterium in category.criteria" :id="`${id}_${criterium.id}`" :key="`${id}_${criterium.id}`"
                                        :criterium="criterium" :menu-actions-id="menuActionsId" :selected="isSelected(criterium)"
                                        @criterium-selected="selectCriterium" @item-actions="$emit('item-actions', $event)" @remove="onRemove" @start-edit="onStartEdit" @finish-edit="onFinishEdit"></criterium-view>
                    </draggable>
                    <new-criterium :category="category" :criterium-dragging="criteriumDragging"></new-criterium>
                </div>
                <div class="category null-category cluster" v-if="criteriumDragging">
                    <div><h2 class="handle-area-category">Nieuwe lijst met criteria...</h2></div>
                    <draggable tag="div" ghost-class="ghost" swapTreshold="0.75" :list="[]" group="criteria" @end="endDrag" @change="onChangeCriteriumInCluster"></draggable>
                </div>
                <div slot="footer" class="no-category"></div>
            </draggable>
            <!-- todo: 'rubric' cluster, v-if="selectedCluster" can then be removed -->
            <new-category v-if="selectedCluster" :cluster="selectedCluster" :view-id="id" :actions-enabled="categoryActionsEnabled" @dialog-view="$emit('dialog-new-category', $event)"></new-category>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import draggable from 'vuedraggable';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';
    import ClusterView from './ClusterView.vue';
    import CategoryView from './CategoryView.vue';
    import CriteriumView from './CriteriumView.vue';
    import NewCluster from './NewCluster.vue';
    import NewCategory from './NewCategory.vue';
    import NewCriterium from './NewCriterium.vue';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'rubric-pane',
        components: { ClusterView, CategoryView, CriteriumView, NewCluster, NewCategory, NewCriterium, NameInput, draggable }
    })
    export default class RubricPane extends Vue {

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: Cluster, default: null}) readonly selectedCluster!: Cluster|null;
        @Prop({type: Cluster, default: null}) readonly otherSelectedCluster!: Cluster|null;
        @Prop({type: String, default: ''}) readonly bannedForDrop!: string;
        @Prop({type: Boolean, required: true}) readonly clusterActionsEnabled!: boolean;
        @Prop({type: Boolean, required: true}) readonly categoryActionsEnabled!: boolean;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: String, required: true}) readonly editCategoryColorId!: string;
        @Prop({type: Boolean, required: true}) readonly draggableDisabled!: boolean;
        @Prop({type: String, default: ''}) readonly dragItemType!: string;
        @Prop(Criterium) readonly selectedCriterium!: Criterium | null;

        get store() {
            return this.$root.$data.store;
        }

        get clusters() : Cluster[] {
            return [...this.store.rubric.clusters];
        }

        get categories() : Category[] {
            if (!this.selectedCluster) { return []; }
            return [...this.selectedCluster.categories];
        }

        // Selection

        isSelected(item: Cluster|Criterium) : boolean {
            if (item instanceof Cluster) {
                return item === this.selectedCluster;
            } else if (item instanceof Criterium) {
                return item === this.selectedCriterium;
            }
            return false;
        }

        selectCluster(cluster: Cluster|null) : void {
            this.$emit('cluster-selected', cluster, this.id);
        }

        selectCriterium(criterium: Criterium|null) : void {
            this.$emit('criterium-selected', criterium);
        }

        @Watch('categoryActionsEnabled')
        onCategoryAddingChanged() {
            this.$nextTick(()=> {
                const clusterContent = this.$refs['cluster-content'] as HTMLElement;
                clusterContent.scrollTo(clusterContent.scrollWidth, 0);
            });
        }

        // Menu Actions

        onStartEdit() {
            this.$emit('start-edit');
        }

        onFinishEdit() {
            this.$emit('finish-edit');
        }

        onRemove(item: Cluster|Category|Criterium) {
            this.$emit('remove', item);
        }

        // Drag & Drop

        startDrag(event: any, type: string) {
            this.$emit('start-drag', event, type);
        }

        endDrag() {
            this.$emit('end-drag');
        }

        dragMouseOver(elementId: string) {
            this.$emit('over-element', elementId);
        }

        dragMouseOut() {
            this.$emit('over-element', '');
        }

        get clusterDragging() {
            return this.dragItemType === 'cluster';
        }

        get categoryDragging() {
            return this.dragItemType === 'category';
        }

        get criteriumDragging() {
            return this.dragItemType === 'criterium';
        }

        onMoveCluster(event: any) {
            return event.related.parentElement.id !== this.bannedForDrop;
        }

        onMoveCategory(event: any) {
            return event.related.parentElement.id !== this.bannedForDrop;
        }

        onMoveCriterium(event: any) {
            return event.related.parentElement?.parentElement.id !== this.bannedForDrop;
        }

        onChangeCluster(event: any) {
            if (event.added && event.added.element) {
                const oldIndex = this.store.rubric.clusters.indexOf(event.added.element);
                this.store.rubric.moveChild(event.added.element, event.added.newIndex, oldIndex);
                this.store.moveChild(event.added.element, this.store.rubric, event.added.newIndex);
            } else if (event.moved) {
                this.store.rubric.moveChild(event.moved.element, event.moved.newIndex, event.moved.oldIndex);
                this.store.moveChild(event.moved.element, this.store.rubric, event.moved.newIndex);
            }
        }

        onChangeCategory(event: any) {
            if (event.added && event.added.element) {
                if (this.otherSelectedCluster === null || this.selectedCluster === this.otherSelectedCluster) {
                    throw new Error(''); // Todo: meaningful message
                }
                const { element, newIndex } = event.added;
                this.otherSelectedCluster!.removeChild(element);
                this.selectedCluster!.addChild(element, newIndex);
                this.store.moveChild(element, this.selectedCluster, newIndex);
            } else if (event.moved) {
                const category: Category = event.moved.element;
                const cluster = category.parent as Cluster;
                cluster.moveChild(category, event.moved.newIndex, event.moved.oldIndex);
                this.store.moveChild(category, cluster, event.moved.newIndex);
            }
        }

        onChangeCriterium(event: any, category: Category) {
            if (event.added && event.added.element) {
                const criterium: Criterium = event.added.element;
                const oldCategory = criterium.parent as Category;
                oldCategory.removeChild(criterium);
                category.addChild(criterium, event.added.newIndex);
                this.store.moveChild(criterium, category, event.added.newIndex);
            } else if (event.moved) {
                const criterium: Criterium = event.moved.element;
                const category = criterium.parent as Category;
                category.moveChild(criterium, event.moved.newIndex, event.moved.oldIndex);
                this.store.moveChild(criterium, category, event.moved.newIndex);
            }
        }

        onChangeCriteriumInCluster(event: any) {
            if (event.added && event.added.element) {
                const criterium: Criterium = event.added.element;
                const oldCategory = criterium.parent as Category;
                oldCategory.removeChild(criterium);
                const category = new Category();
                category.color = '';
                this.selectedCluster!.addChild(category, this.selectedCluster!.categories.length);
                category.addChild(criterium, event.added.newIndex);
                this.store.moveChild(criterium, category, event.added.newIndex);
            }
        }
    }
</script>