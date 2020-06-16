<template>
	<div class="app-container" :class="mainClass">
		<div class="rubric-panes-wrapper">
			<div class="rubric-panes" :class="{ 'criterium-selected': !!selectedCriterium }" @click="hideMenu" @keyup.esc="hideMenu">
				<rubric-pane id="view1"
							 :rubric="rubric"
							 :data-connector="dataConnector"
							 :selected-cluster="selectedClusterView1"
							 :other-selected-cluster="selectedClusterView2"
							 :selected-criterium="selectedCriterium"
							 :cluster-actions-enabled="newClusterDialogView === ''"
							 :category-actions-enabled="newCategoryDialogView === ''"
							 :menu-actions-id="menuActionsId"
							 :is-editing="isEditing"
							 :edit-category-color-id="editCategoryColorId"
							 :drag-item-type="dragItemType"
							 :draggable-disabled="draggableDisabled"
							 :banned-for-drop="bannedForDrop"
							 @cluster-selected="onClusterSelected($event, 'view1')"
							 @criterium-selected="selectCriterium"
							 @dialog-new-cluster="newClusterDialogView = $event"
							 @dialog-new-category="newCategoryDialogView = $event"
							 @item-actions="onItemActions($event)"
							 @start-edit="onStartEdit"
							 @finish-edit="onFinishEdit"
							 @change-color="onChangeColor"
							 @remove="showRemoveDialog"
							 @color-picker="onColorPicker"
							 @start-drag="startDrag"
							 @end-drag="endDrag"
							 @over-element="dragOverElement"
				></rubric-pane>
				<rubric-pane id="view2" v-if="uiState.showSplitView"
							 :rubric="rubric"
							 :data-connector="dataConnector"
							 :selected-cluster="selectedClusterView2"
							 :other-selected-cluster="selectedClusterView1"
							 :selected-criterium="selectedCriterium"
							 :cluster-actions-enabled="newClusterDialogView === ''"
							 :category-actions-enabled="newCategoryDialogView === ''"
							 :menu-actions-id="menuActionsId"
							 :is-editing="isEditing"
							 :edit-category-color-id="editCategoryColorId"
							 :drag-item-type="dragItemType"
							 :draggable-disabled="draggableDisabled"
							 :banned-for-drop="bannedForDrop"
							 @cluster-selected="onClusterSelected($event, 'view2')"
							 @criterium-selected="selectCriterium"
							 @dialog-new-cluster="newClusterDialogView = $event"
							 @dialog-new-category="newCategoryDialogView = $event"
							 @item-actions="onItemActions($event)"
							 @start-edit="onStartEdit"
							 @finish-edit="onFinishEdit"
							 @change-color="onChangeColor"
							 @remove="showRemoveDialog"
							 @color-picker="onColorPicker"
							 @start-drag="startDrag"
							 @end-drag="endDrag"
							 @over-element="dragOverElement"
				></rubric-pane>
			</div>
		</div>
		<transition name="selected-fade" mode="out-in">
	        <criterium-details-view :key="selectedCriterium ? selectedCriterium.id : 'none'" v-if="selectedCriterium" :rubric="rubric" :criterium="selectedCriterium" @close="selectCriterium(null)" @change-criterium="onChangeCriterium" @change-choice="onChangeChoice"></criterium-details-view>
		</transition>
		<remove-dialog :remove-item="removeItem" @remove="onRemoveItem" @cancel="hideRemoveDialog"></remove-dialog>
	</div>
</template>

<script lang="ts">
	import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
	import TreeNode from '../Domain/TreeNode';
	import Rubric from '../Domain/Rubric';
	import Cluster from '../Domain/Cluster';
	import Category from '../Domain/Category';
	import Criterium from '../Domain/Criterium';
	import Choice from '../Domain/Choice';
	import RubricPane from './RubricPane.vue';
    import CriteriumDetailsView from './CriteriumDetailsView.vue';
	import RemoveDialog from './RemoveDialog.vue';
	import DataConnector from '../Connector/DataConnector';

	@Component({
		name: 'score-rubric-view',
		components: { RubricPane, RemoveDialog, CriteriumDetailsView }
	})
	export default class ScoreRubricView extends Vue {

		private selectedClusterView1: Cluster|null = null;
		private selectedClusterView2: Cluster|null = null;
		private menuActionsId: string = '';
		private newClusterDialogView: string = '';
		private newCategoryDialogView: string = '';
		private isEditing: boolean = false;
		private oldItemTitle: string = '';
		private removeItem: Cluster|Category|Criterium|null = null;
		private editCategoryColorId: string = '';
		private initiatedDrag: string = '';
		private dragItemType: string = '';
		private overElementId: string = '';
		private bannedForDrop: string = '';
		private innerWidth: number = window.innerWidth;

		@Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
		@Prop(Criterium) readonly selectedCriterium!: Criterium | null;
		@Prop(DataConnector) readonly dataConnector!: DataConnector|null;
		@Prop({type: Object}) readonly uiState!: any;

		get clusters() {
			return [...this.rubric.clusters];
		}

		get console() {
			return window.console;
		}

		// Selection

		onClusterSelected(cluster: Cluster, view: string) {
			if (view === 'view1') {
				this.selectedClusterView1 = cluster;
				this.uiState.selectedClusterView1 = cluster.id;
			} else if (view === 'view2') {
				this.selectedClusterView2 = cluster;
				this.uiState.selectedClusterView2 = cluster.id;
			}
		}

		isSelected(cluster: Cluster, view: string = 'view1') : boolean {
			if (view === 'view1') {
				return cluster === this.selectedClusterView1;
			} else if (view === 'view2') {
				return cluster === this.selectedClusterView2;
			}
			return false;
		}

		selectCriterium(criterium: Criterium|null) {
			this.$emit('criterium-selected', criterium);
		}

		// Menu Actions

		onItemActions(id: string) {
			this.menuActionsId = this.menuActionsId === id ? '' : id;
		}

		hideMenu() {
			this.menuActionsId = '';
		}

		onStartEdit(item: TreeNode) {
			this.isEditing = true;
			this.oldItemTitle = item.title;
			this.hideMenu();
		}

		onFinishEdit(item: TreeNode, newTitle: string, canceled=false) {
			if (!canceled && newTitle !== this.oldItemTitle) {
				item.title = newTitle;
				this.dataConnector?.updateTreeNode(item);
			}
			this.isEditing = false;
			this.oldItemTitle = '';
		}

		onChangeColor(category: Category) {
			this.dataConnector?.updateTreeNode(category);
		}

		onChangeCriterium(criterium: Criterium) {
			this.dataConnector?.updateTreeNode(criterium);
		}

		onChangeChoice(choice: Choice) {
			this.dataConnector?.updateChoice(choice);
		}

		showRemoveDialog(item: Cluster|Category|Criterium|null) {
			this.removeItem = item;
			this.hideMenu();
		}

		hideRemoveDialog() {
			this.showRemoveDialog(null);
		}

		onRemoveItem() {
			const item = this.removeItem as TreeNode;
			if (item instanceof Criterium && item === this.selectedCriterium) {
				this.selectCriterium(null);
			} else if (item instanceof Category) {
				if (this.selectedCriterium?.parent === item) {
					this.selectCriterium(null);
				}
				if (this.editCategoryColorId && item.id === this.editCategoryColorId.split('_')[1]) {
					this.editCategoryColorId = '';
				}
			} else if (item instanceof Cluster) {
				if (this.selectedCriterium?.parent?.parent === item) {
					this.selectCriterium(null);
				}
				if (item === this.selectedClusterView1) {
					this.selectedClusterView1 = null;
					this.uiState.selectedClusterView1 = '';
				}
				if (item === this.selectedClusterView2) {
					this.selectedClusterView2 = null;
					this.uiState.selectedClusterView2 = '';
				}
			}
			item!.parent!.removeChild(item);
			this.dataConnector?.deleteTreeNode(item);
			this.hideRemoveDialog();
		}

		onColorPicker(id: string) {
			this.editCategoryColorId = id;
		}

		// Drag & Drop

		get draggableDisabled() {
			return this.innerWidth < 900 || this.isEditing || this.menuActionsId !== '' || this.newClusterDialogView !== '' || this.newCategoryDialogView !== '' || this.removeItem !== null;
		}

		startDrag(event: any, type: string) {
			const view = event.item.id.split('_')[0];
			const otherView = view === 'view1' ? 'view2' : 'view1';
			this.dragItemType = type;
			this.initiatedDrag = view;
			let bannedFilter = '';
			if (type === 'cluster') {
				bannedFilter = 'clusters';
			} else if (type === 'category') {
				bannedFilter = 'categories';
			} else {
				bannedFilter = event.item.parentElement?.parentElement.id.split('_')[1];
			}
			if (type === 'cluster' || (this.selectedClusterView1 !== null && this.selectedClusterView1 === this.selectedClusterView2)) {
				this.bannedForDrop = `${otherView}_${bannedFilter}`;
			}
		}

		endDrag() {
			this.dragItemType = '';
			this.initiatedDrag = '';
			this.bannedForDrop = '';
			this.overElementId = '';
		}

		dragOverElement(elementId: string = '') {
			if (!this.initiatedDrag) { return; }
			this.overElementId = elementId;
		}

		mounted() {
			if (this.uiState.selectedClusterView1) {
				const cluster = this.rubric.clusters.find(cluster => cluster.id === this.uiState.selectedClusterView1);
				if (cluster) {
					this.selectedClusterView1 = cluster;
				} else {
					this.uiState.selectedClusterView1 = '';
				}
			} else {
				this.selectedClusterView1 = this.clusters.length && this.clusters[0] || null;
			}
			if (this.uiState.selectedClusterView2) {
				const cluster = this.rubric.clusters.find(cluster => cluster.id === this.uiState.selectedClusterView2);
				if (cluster) {
					this.selectedClusterView2 = cluster;
				} else {
					this.uiState.selectedClusterView2 = '';
				}
			} else {
				this.selectedClusterView2 = this.clusters.length && this.clusters[0] || null;
			}
			window.addEventListener('resize', this.handleResize);
		}

		beforeDestroy() {
			window.removeEventListener('resize', this.handleResize);
		}

		handleResize() {
			this.innerWidth = window.innerWidth;
		}

		get mainClass() {
			return {
				'dragging': this.initiatedDrag !== '',
				'not-allowed': this.bannedForDrop !== '' && this.overElementId === this.bannedForDrop,
				'split-view': this.uiState.showSplitView
			};
		}

		@Watch('rubric')
		onRubricChanged(){
			// console.log('change');
		}
	}
	//todo replace border with padding
</script>

