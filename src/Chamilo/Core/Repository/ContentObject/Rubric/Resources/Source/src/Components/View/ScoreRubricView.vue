<template>
	<div class="app-container" :class="mainClass">
		<div class="rubric-panes-wrapper">
			<div class="rubric-panes" :class="{ 'criterium-selected': !!selectedCriterium }" @click="hideMenu">
				<rubric-pane id="view1"
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
							 @remove="showRemoveDialog"
							 @color-picker="onColorPicker"
							 @start-drag="startDrag"
							 @end-drag="endDrag"
							 @over-element="dragOverElement"
				></rubric-pane>
				<rubric-pane id="view2" v-if="split"
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
							 @remove="showRemoveDialog"
							 @color-picker="onColorPicker"
							 @start-drag="startDrag"
							 @end-drag="endDrag"
							 @over-element="dragOverElement"
				></rubric-pane>
			</div>
		</div>
        <criterium-details-view v-if="selectedCriterium" :criterium="selectedCriterium" @close="selectCriterium(null)"></criterium-details-view>
		<remove-dialog :remove-item="removeItem" @remove="onRemoveItem" @cancel="hideRemoveDialog"></remove-dialog>
	</div>
</template>

<script lang="ts">
	import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
	import TreeNode from '../../Domain/TreeNode';
	import Cluster from '../../Domain/Cluster';
	import Category from '../../Domain/Category';
	import Criterium from '../../Domain/Criterium';
	import RubricPane from './RubricPane.vue';
    import CriteriumDetailsView from './CriteriumDetailsView.vue';
	import RemoveDialog from './RemoveDialog.vue';

	@Component({
		name: 'score-rubric-view',
		components: { RubricPane, RemoveDialog, CriteriumDetailsView }
	})
	export default class ScoreRubricView extends Vue {

		private selectedClusterView1: Cluster|null = this.store.rubric.clusters.length && this.store.rubric.clusters[0] || null;
		private selectedClusterView2: Cluster|null = this.store.rubric.clusters.length && this.store.rubric.clusters[0] || null;
		private menuActionsId: string = '';
		private newClusterDialogView: string = '';
		private newCategoryDialogView: string = '';
		private isEditing: boolean = false;
		private removeItem: Cluster|Category|Criterium|null = null;
		private editCategoryColorId: string = '';
		private initiatedDrag: string = '';
		private dragItemType: string = '';
		private overElementId: string = '';
		private bannedForDrop: string = '';
		private innerWidth: number = window.innerWidth;

		@Prop(Criterium) readonly selectedCriterium!: Criterium | null;
		@Prop(Boolean) readonly split!: boolean;

		get store() {
			return this.$root.$data.store;
		}

		get rubric() {
			return this.store.rubric;
		}

		get clusters() {
			return [...this.store.rubric.clusters];
		}

		get console() {
			return window.console;
		}

		// Selection

		onClusterSelected(cluster: Cluster, view: string) {
			if (view === 'view1') {
				this.selectedClusterView1 = cluster;
			} else if (view === 'view2') {
				this.selectedClusterView2 = cluster;
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

		onStartEdit() {
			this.isEditing = true;
			this.hideMenu();
		}

		onFinishEdit() {
			this.isEditing = false;
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
				}
				if (item === this.selectedClusterView2) {
					this.selectedClusterView2 = null;
				}
			}
			item!.parent!.removeChild(item);
			this.store.removeChild(item, item!.parent!);
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
				'split-view': this.split
			};
		}

		@Watch('store.rubric')
		onRubricChanged(){
			// console.log('change');
		}
	}
	//todo replace border with padding
</script>

