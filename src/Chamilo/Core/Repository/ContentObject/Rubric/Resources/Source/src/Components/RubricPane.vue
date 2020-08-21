<i18n>
    {
    "en": {
        "new-criteria-list": "New list of criteria",
        "no-criteria-yet": "No criteria yet",
        "subsections": "Subsections"
    },
    "fr": {
        "new-criteria-list": "Nouvelle liste de critères",
        "no-criteria-yet": "Pas de critères ajoutés",
        "subsections": "Sous-sections"
    },
    "nl": {
        "new-criteria-list": "Nieuwe lijst met criteria",
        "no-criteria-yet": "Nog geen criteria toegevoegd",
        "subsections": "Onderverdelingen"
    }
}
</i18n>

<template>
    <div :id="`clusters-wrapper-${id}`" class="clusters-wrapper" :class="{ 'is-split': showSplitView, 'category-dragging': categoryDragging }">
        <button class="btn-collapse" :class="{ 'is-open': showClusters }" @click="toggleShowClusters"><i class="fa fa-institution"></i><span>{{ $t('subsections') }}</span></button>
        <div class="clusters-collapse">
            <transition name="clusters-slide">
                <div class="clusters-view" @mouseover="dragMouseOver(`${id}_clusters`)" @mouseout="dragMouseOut" :class="{ 'no-drop': clusterDragging && bannedForDrop === `${id}_clusters`, 'is-closed': !showClusters, 'mod-separator': clusterActionsEnabled }" :key="showClusters ? 'open' : 'closed'">
                    <split-view-button v-if="id === 'view1'" :show-split-view="showSplitView" @changed="$emit('split-view-changed', $event)"></split-view-button>
                    <draggable handle=".handle" :disabled="draggableDisabled" :id="`${id}_clusters`" tag="ul" group="clusters" class="b-clusters" ghost-class="ghost" :list="clusters" :class="{ 'cluster-dragging': clusterDragging }" :forceFallback="true" :animation="250"
                            :move="onMoveCluster" @start="startDrag($event, 'cluster')" @end="endDrag" @change="onChangeCluster">
                        <cluster-view v-for="(cluster, index) in clusters"
                            tag="li" class="b-cluster-list-item" :id="`${id}_${cluster.id}`" :key="`${id}_${cluster.id}`" :cluster="cluster" :menu-actions-id="menuActionsId" :selected="isSelected(cluster)"
                            @cluster-selected="selectCluster" @item-actions="$emit('item-actions', $event)" @remove="onRemove" @start-edit="onStartEdit(cluster)" @finish-edit="onFinishEdit(cluster, ...arguments)"></cluster-view>
                    </draggable>
                    <new-cluster class="rubric-actions" :view-id="id" :actions-enabled="clusterActionsEnabled" @dialog-view="$emit('dialog-new-cluster', $event)" @cluster-added="addCluster"></new-cluster>
                </div>
            </transition>
        </div>
        <h1 v-if="selectedCluster" class="b-cluster-selected-title">{{ selectedCluster.title }}</h1>
        <transition :name="categoriesTransitionName" mode="out-in">
            <div :key="selectedCluster ? selectedCluster.id : 'none'" class="cluster-content" ref="cluster-content" @mouseover="categoryDragging && dragMouseOver(`${id}_categories`)" @mouseout="categoryDragging && dragMouseOut" :class="{ 'no-drop': categoryDragging && bannedForDrop === `${id}_categories` }">
                <draggable :disabled="draggableDisabled" :id="`${id}_categories`" tag="ul" class="b-categories" group="categories" handle=".category-handle" ghost-class="ghost" :list="categories" :forceFallback="true" :animation="250" :move="onMoveCategory"
                           @start="startDrag($event, 'category')" @end="endDrag" @change="onChangeCategory">
                    <li v-for="category in categories" @mouseover="criteriumDragging && dragMouseOver(`${id}_${category.id}`)" @mouseout="criteriumDragging && dragMouseOut" :id="`${id}_${category.id}`" :key="`${id}_${category.id}`" class="b-category" :class="{ 'no-drop': criteriumDragging && bannedForDrop === `${id}_${category.id}` }" :style="{'--category-color': category.color}">
                        <category-view :id="`${id}_${category.id}`" :key="`${id}_${category.id}`" :category="category" :menu-actions-id="menuActionsId" :edit-category-color-id="editCategoryColorId"
                                       @color-picker="$emit('color-picker', $event)" @item-actions="$emit('item-actions', $event)" @remove="onRemove" @start-edit="onStartEdit(category)" @finish-edit="onFinishEdit(category, ...arguments)" @change-color="$emit('change-color', category)"></category-view>
                        <div v-if="category.criteria.length === 0 && !categoriesAddingCriterium[category.id] && !criteriumDragging" class="criteria-empty-list">{{ $t('no-criteria-yet') }}</div>
                        <draggable :key="`${id}_${category.id}_draggable`" :disabled="draggableDisabled" tag="ul" group="criteria" handle=".criterium-handle" ghost-class="ghost" swapTreshold="0.75" :list="category.criteria" :forceFallback="true" :animation="250"
                                   :move="onMoveCriterium" @start="startDrag($event,'criterium')" @end="endDrag" @change="onChangeCriterium($event, category)" class="b-criteria">
                            <criterium-view v-for="criterium in category.criteria" tag="li" class="b-criterium-list-item" :id="`${id}_${criterium.id}`" :key="`${id}_${criterium.id}`"
                                            :criterium="criterium" :menu-actions-id="menuActionsId" :selected="isSelected(criterium)"
                                            @criterium-selected="selectCriterium" @item-actions="$emit('item-actions', $event)" @remove="onRemove" @start-edit="onStartEdit(criterium)" @finish-edit="onFinishEdit(criterium, ...arguments)"></criterium-view>
                        </draggable>
                        <new-criterium class="category-actions" :criterium-dragging="criteriumDragging" @criterium-adding="addingCriteriumForCategory(category, $event)" @criterium-added="addCriterium(category, $event)"></new-criterium>
                    </li>
                    <li v-if="criteriumDragging" slot="footer" class="b-category">
                        <div class="b-category-list-item mod-null-category-drag">
                            <div class="item-header-bar mod-category">
                                <div class="b-category-title-wrapper">
                                    <h2 class="b-category-title mod-null-category-drag">{{ $t('new-criteria-list') }}...</h2>
                                </div>
                            </div>
                        </div>
                        <draggable tag="ul" group="criteria" handle=".criterium-handle" ghost-class="ghost" swapTreshold="0.75" :list="[]" :forceFallback="true" :animation="250"
                                   @end="endDrag" @change="onChangeCriteriumInCluster" class="b-criteria"></draggable>
                    </li>
                    <li v-else-if="categories.length === 0" slot="footer" class="b-category-drag-filler"></li>
                </draggable>
                <new-category class="cluster-actions" v-if="selectedCluster" :view-id="id" :actions-enabled="categoryActionsEnabled" @dialog-view="$emit('dialog-new-category', $event)" @category-added="addCategory"></new-category>
            </div>
        </transition>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import draggable from 'vuedraggable';
    import TreeNode from '../Domain/TreeNode';
    import Rubric from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import ClusterView from './ClusterView.vue';
    import CategoryView from './CategoryView.vue';
    import CriteriumView from './CriteriumView.vue';
    import NewCluster from './NewCluster.vue';
    import NewCategory from './NewCategory.vue';
    import NewCriterium from './NewCriterium.vue';
    import NameInput from './NameInput.vue';
    import SplitViewButton from './SplitViewButton.vue';
    import DataConnector from '../Connector/DataConnector';

    @Component({
        name: 'rubric-pane',
        components: { ClusterView, CategoryView, CriteriumView, NewCluster, NewCategory, NewCriterium, NameInput, SplitViewButton, draggable }
    })
    export default class RubricPane extends Vue {

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Cluster, default: null}) readonly selectedCluster!: Cluster|null;
        @Prop({type: Cluster, default: null}) readonly otherSelectedCluster!: Cluster|null;
        @Prop({type: String, default: ''}) readonly bannedForDrop!: string;
        @Prop({type: Boolean, required: true}) readonly clusterActionsEnabled!: boolean;
        @Prop({type: Boolean, required: true}) readonly categoryActionsEnabled!: boolean;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: String, required: true}) readonly editCategoryColorId!: string;
        @Prop({type: Boolean, required: true}) readonly draggableDisabled!: boolean;
        @Prop({type: Boolean, required: true}) readonly isEditing!: boolean;
        @Prop({type: String, default: ''}) readonly dragItemType!: string;
        @Prop({type: Boolean, default: false}) readonly showSplitView!: boolean;
        @Prop(Criterium) readonly selectedCriterium!: Criterium | null;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

        private categoriesTransitionName = '';
        private showClusters: boolean = false;
        private categoriesAddingCriterium: any = {};

        mounted() {
            window.setTimeout(() => {
                this.categoriesTransitionName = 'selected-fade';
            }, 100);

            this.initScrollHandler();
        }

        initScrollHandler() {
            /* // todo: Integrate this code, currently it's conflicting with the mouse handler in the template
            const slider = this.$refs['cluster-content'];
            if (!slider) { return; }
            let startX: number;
            let scrollLeft: number;
            let isDown: boolean;
            slider.addEventListener('mousedown', e => {
                isDown = true;
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener('mouseleave', () => {
                isDown = false;
            });
            slider.addEventListener('mouseup', () => {
                isDown = false;
            });
            slider.addEventListener('mousemove', e => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                slider.scrollLeft = scrollLeft - (x - startX);
            }); */
        }

        get clusters() : Cluster[] {
            return [...this.rubric.clusters];
        }

        get categories() : Category[] {
            if (!this.selectedCluster) { return []; }
            return [...this.selectedCluster.categories];
        }

        // Selection

        toggleShowClusters() {
            this.showClusters = !this.showClusters;
            this.$emit('dialog-new-cluster', '');
        }

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

        // Add TreeNodes

        addCluster(cluster: Cluster) {
            this.rubric.addChild(cluster, this.rubric.clusters.length);
            this.dataConnector?.addTreeNode(cluster, this.rubric, this.rubric.clusters.length);
            const category = new Category();
            category.color = '';
            cluster.addChild(category, 0);
            this.dataConnector?.addTreeNode(category, cluster, 0);
            this.selectCluster(cluster);
        }

        addCategory(category: Category) {
            this.selectedCluster!.addChild(category, this.selectedCluster!.categories.length);
            this.dataConnector?.addTreeNode(category, this.selectedCluster!, this.selectedCluster!.categories.length);
            this.updateFirstCategoryIfNeeded();
        }

        updateFirstCategoryIfNeeded() {
            if (this.selectedCluster!.categories.length === 2) {
                const firstCategory = this.selectedCluster!.categories[0];
                if (firstCategory.title === '') {
                    firstCategory.title = 'Categorie';
                    this.dataConnector?.updateTreeNode(firstCategory);
                }
            }
        }

        @Watch('categoryActionsEnabled')
        onCategoryAddingChanged() {
            this.$nextTick(()=> {
                const clusterContent = this.$refs['cluster-content'] as HTMLElement;
                if (clusterContent) {
                    clusterContent.scrollTo(clusterContent.scrollWidth, 0);
                }
                //(this.$refs['btn-category-add'] as HTMLElement).blur();
            });
        }

        @Watch('selectedCluster')
        onSelectedClusterChanged() {
            this.$emit('dialog-new-category', '');
        }

        addCriterium(category: Category, criterium: Criterium) {
            category.addChild(criterium, category.criteria.length);
            this.dataConnector?.addTreeNode(criterium, category, category.criteria.length);
        }

        addingCriteriumForCategory(category: Category, adding: boolean) {
            if (typeof this.categoriesAddingCriterium[category.id] === 'undefined') {
                Vue.set(this.categoriesAddingCriterium, category.id, adding);
            } else {
                this.categoriesAddingCriterium[category.id] = adding;
            }
        }

        /*@Watch('isEditing')
        // Not sure yet if I want to use this, since the result is a bit unsatisfactory. leaving this here for now.
        onEditStateChanged() {
            if (this.isEditing) {
                this.$nextTick(()=> {
                    const nameInput = document.querySelector('.name-input') as HTMLElement;
                    if (nameInput) {
                        if (HTMLElement.prototype.scrollIntoViewIfNeeded) {
                            nameInput.scrollIntoViewIfNeeded();
                        } else {
                            nameInput.scrollIntoView();
                        }
                    }
                });
            }
        }*/

        // Menu Actions

        onStartEdit(item: TreeNode) {
            this.$emit('start-edit', item);
        }

        onFinishEdit(item: TreeNode, newTitle: string, canceled=false) {
            this.$emit('finish-edit', item, newTitle, canceled);
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
                const oldIndex = this.rubric.clusters.indexOf(event.added.element);
                this.rubric.moveChild(event.added.element, event.added.newIndex, oldIndex);
                this.dataConnector?.moveTreeNode(event.added.element, this.rubric, event.added.newIndex);
            } else if (event.moved) {
                this.rubric.moveChild(event.moved.element, event.moved.newIndex, event.moved.oldIndex);
                this.dataConnector?.moveTreeNode(event.moved.element, this.rubric, event.moved.newIndex);
            }
        }

        onChangeCategory(event: any) {
            if (event.added && event.added.element) {
                if (this.otherSelectedCluster === null || this.selectedCluster === this.otherSelectedCluster) {
                    throw new Error(''); // Todo: meaningful message
                }
                const { element, newIndex } = event.added;
                // Notify false prevents the choices of the category's criteria to be removed at the root level.
                // Is there a better way?
                this.otherSelectedCluster!.removeChild(element, false);
                this.selectedCluster!.addChild(element, newIndex);
                this.dataConnector?.moveTreeNode(element, this.selectedCluster!, newIndex);
            } else if (event.moved) {
                const category: Category = event.moved.element;
                const cluster = category.parent as Cluster;
                cluster.moveChild(category, event.moved.newIndex, event.moved.oldIndex);
                this.dataConnector?.moveTreeNode(category, cluster, event.moved.newIndex);
            }
        }

        onChangeCriterium(event: any, category: Category) {
            if (event.added && event.added.element) {
                const criterium: Criterium = event.added.element;
                const oldCategory = criterium.parent as Category;
                oldCategory.removeChild(criterium);
                category.addChild(criterium, event.added.newIndex);
                this.dataConnector?.moveTreeNode(criterium, category, event.added.newIndex);
            } else if (event.moved) {
                const criterium: Criterium = event.moved.element;
                const category = criterium.parent as Category;
                category.moveChild(criterium, event.moved.newIndex, event.moved.oldIndex);
                this.dataConnector?.moveTreeNode(criterium, category, event.moved.newIndex);
            }
        }

        onChangeCriteriumInCluster(event: any) {
            if (event.added && event.added.element) {
                const criterium: Criterium = event.added.element;
                const oldCategory = criterium.parent as Category;
                oldCategory.removeChild(criterium);
                const category = new Category();
                category.color = '';
                this.addCategory(category);
                category.addChild(criterium, event.added.newIndex);
                this.dataConnector?.moveTreeNode(criterium, category, event.added.newIndex);
            }
        }
    }
</script>