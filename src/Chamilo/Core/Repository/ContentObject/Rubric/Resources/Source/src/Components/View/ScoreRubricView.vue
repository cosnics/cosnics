<template>
	<div class="container" :class="mainClass">
		<div @click="hideMenu">
			<rubric-pane id="view1"
						 :selected-cluster="selectedClusterView1"
						 :other-selected-cluster="selectedClusterView2"
						 :selected-criterium="selectedCriterium"
						 :cluster-actions-enabled="newClusterDialogView === ''"
						 :category-actions-enabled="newCategoryDialogView === ''"
						 :menu-actions-id="menuActionsId"
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
		<remove-dialog :remove-item="removeItem" @remove="onRemoveItem" @cancel="hideRemoveDialog"></remove-dialog>
		<!--<div class="modal-bg" v-if="removeItem !== null" @click.stop="hideRemoveDialog">
			<div class="modal-level" @click.stop="">
				<div class="title" v-if="removeItem.constructor.name === 'Category' && removeItem.color === ''">Criteria verwijderen?</div>
				<div class="title" v-else>{{ removeItem.constructor.name }} '{{ removeItem.title }}' verwijderen?</div>
				<div>
					<button ref="btn-remove" class="btn" @click.stop="onRemoveItem">Verwijder</button>
					<button class="btn" @click.stop="hideRemoveDialog">Annuleer</button>
				</div>
			</div>
		</div>-->
	</div>
</template>

<script lang="ts">
	import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
	import TreeNode from '../../Domain/TreeNode';
	import Cluster from '../../Domain/Cluster';
	import Category from '../../Domain/Category';
	import Criterium from '../../Domain/Criterium';
	import RubricPane from './RubricPane.vue';
	import RemoveDialog from './RemoveDialog.vue';

	@Component({
		name: 'score-rubric-view',
		components: { RubricPane, RemoveDialog }
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
			return this.isEditing || this.menuActionsId !== '' || this.newClusterDialogView !== '' || this.newCategoryDialogView !== '' || this.removeItem !== null;
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

<style>
	button {
		border-radius: 3px;
		transition: background-color 0.2s ease-in, color 0.1s ease-in;
		font-size: 12px;
		background: transparent;
		border: 1px solid transparent;
		color: #fff;
	}
	button:not(:hover) {
		color: #666;
	}
	.actions {
		transition: opacity 100ms;
	}
	.actions i {
		color: #888;
		margin-right: 5px;
		transition: color 0.1s ease-in;
	}
	button:hover {
		background: hsla(200, 100%, 48%, 1);
		color: #fff;
		border: 1px solid transparent;
	}
	button:hover .fa {
		color: white;
	}
	.name-input {
		z-index: 20;
	}
	.name-input button { font-weight: normal; margin-bottom: 4px; }
	.name-input button:not(:hover):nth-child(1) {
		background: hsla(200, 100%, 57%, 1);
		color: #fff;
	}
	.name-input button:not(:hover):nth-child(2) {
		background: transparent;
		border: 1px solid #cdcdcd;
	}
</style>
<style>
	/* Reset elements */
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
		outline-width: thin;
	}
	button {
		padding: 4px 8px 4px 6px;
	}
	.container {
		position: relative;
		width: 100%;
		background-color: hsla(165, 5%, 90%, 1);
		display: flex;
		flex-direction: column;
	}
	.container.dragging, .container.dragging * {
		cursor: move;
		cursor: grabbing;
	}
	.container.dragging.not-allowed, .container.dragging.not-allowed * {
		cursor: not-allowed;
	}
	.container > div {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		overflow: hidden;
	}
	.container > div > div {
		height: 100%;
		overflow-x: hidden;
		display: flex;
		flex-direction: column;
	}
	.container.split-view > div > div {
		height: 50%;
	}
	.container > div > div:nth-child(2) {
		border-top: 1px solid hsl(200, 25%, 80%);
	}

	/* Clusters */
	.clusters-view {
		--background: hsla(200, 10%, 80%, 0.5);
		width: 100%;
		padding: 14px 0;
		margin-bottom: 8px;
		display: flex;
		transition: opacity 100ms;
	}
	.clusters-view.no-drop {
		opacity: 0.3;
	}
	.clusters {
		margin-left: 1.5em;
		font-size: 1.4rem;
		list-style: none;
		display: flex;
	}
	.clusters li {
		display: block;
		border: 1px solid transparent;
		margin-right: 0.7em;
		padding: 0;
		color: #444;
		position: relative;
		cursor: default;
	}
	.clusters li.selected {
		--background: hsla(190, 40%, 45%, 1);
		/*--background: hsla(207, 38%, 45%, 1);*/
		color: white;
	}
	.clusters .title {
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 0.8em;
		padding-right: 2.1em;
		line-height: 1.3em;
		height: 100%;
		background: var(--background);
		border-radius: 3px;
		text-align: center;
	}
	.clusters i {
		font-size: 1.3rem;
	}
	.clusters span:not(:empty) {
		margin-left: 0.6em;
	}
	.container:not(.dragging) .clusters li:not(.selected):hover {
		--background: hsla(190, 20%, 75%, 1);
		color: #222;
	}
	/*.clusters li.selected:after {
		content: '';
		display: block;
		margin: 0 auto;
		padding: 0;
		width: 0;
		height: 0;
		border-width: 11px 13px 0;
		border-color: var(--background) transparent;
		border-style: solid solid none;
	}*/
	.clusters li.selected .title {
		box-shadow: 0px 2px 4px #999;
	}
	.clusters li, .clusters .title {
		-moz-user-select: -moz-none;
		-khtml-user-select: none;
		-webkit-user-select: none;
		-o-user-select: none;
		user-select: none;
	}
	.clusters div {
		/*pointer-events: none;*/
	}
	.clusters li.ghost {
		background: rgba(255, 255, 255, 0.45);
		border: 1px dotted rgba(28,110,164,0.65);
		border-radius: 4px;
	}
	.clusters li.ghost:after, .clusters li.ghost > * {
		visibility: hidden;
	}

	/* Navigation: actions */
	.actions {
		flex: 1;
		display: flex;
		align-items: center;
		background: transparent;
	}
	.actions > div {
		position: relative;
		width: 200px;
		height: 100%;
	}
	.actions >>> button {
		font-size: 13px;
	}
	/* Form: New cluster */
	.actions > button:before {
		/*		content: '+';
                display: inline-block;
                margin-right: 5px;
                color: black;
                font-size: 13px;
                text-align: center;
                border-radius: 8px;
                transition: color 0.1s ease-in; */
	}
	.actions button:disabled, .actions button:disabled:hover {
		background: transparent;
		color: #bbb;
		cursor: not-allowed;
	}
	.actions button:disabled i, .actions button:disabled:hover i {
		color: #bbb;
	}
	/* Cluster content: categories and criteria */
	.cluster-content {
		flex: 1;
		width: 100%;
		padding-bottom: 40px;
		user-select: none;
		overflow: auto;
		overflow-wrap: break-word;
		display: flex;
		transition: opacity 100ms;
	}
	.cluster-content.no-drop {
		opacity: 0.3;
	}
	.cluster-content > div {
		margin-left: 21px;
		white-space: nowrap;
		display: flex;
		flex-direction: row;
		align-items: flex-start;
	}
	.cluster-content > div > div {
		width: 240px;
	}

	.cluster-content > div > div.no-category {
		margin-right: 0;
		height: 200px;
		flex: 0;
		background: transparent;
	}
	.cluster-content .actions {
		transform: translate(-260px, 0);
	}
	.container.dragging .cluster-content .actions {
		pointer-events: none;
	}
	.category {
		min-width: 240px;
		margin-right: 16px;
		margin-bottom: 40px;
		white-space: normal;
		border: 1px solid #dedede;
		border-radius: 4px;
		transition: opacity 200ms;
	}
	.category.no-drop {
		opacity: 0.3;
	}
	.category > div {
		font-size: 13px;
	}
	.category > div:nth-child(1) {
		background: #dfdfdf;
		background: linear-gradient(to bottom, rgba(255, 255, 255, 0.4) 0px, rgba(255, 255, 255, 0) 10px, rgba(0,0,0,0) 19px, rgba(0,0,0,0.08) 36px);
		/*font-weight: bold;*/
		border-radius: 4px 4px 0 0;
	}
	.category.newcategory {
		margin-left: 0;
		border: none;
		transform: translate(-240px, 0);
	}
	.container.dragging .category.newcategory {
		pointer-events: none;
	}
	.category.newcategory > div:nth-child(1) {
		background: transparent;
		padding: 0;
	}
	.category > div:nth-child(2) button {
		padding: 0;
	}
	.category.newcategory >>> input, .category.newcategory >>> input::placeholder {
		font-weight: 500;
	}
	.category > div:nth-child(2) {
		/*background: #fff;*/
		border-top: none;
		border-radius: 0 0 4px 4px;
	}
	.category > div:nth-child(2) > div {
		background: white;
		background: linear-gradient(to top, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0) 14px);
	}
	.category > div:nth-child(2) > div:first-child {
		border-top: 1px solid #ccc;
	}
	.category > div:nth-child(2) > div {
		border-bottom: 1px solid #ddd;
	}
	/*.category > div:nth-child(2) > div:last-child {
		border-bottom: 1px solid #ccc;
	}*/
	.category h2 {
		display: initial;
		font-size: 13px;
		outline-width: thin;
	}
	.category > div:nth-child(1), .category > div:nth-child(2) > div, .category .criterium-add-new {
		padding: 8px;
		overflow-wrap: break-word;
	}
	/*.category > div:nth-child(1):before {
        content: '';
        display: inline-block;
        margin-right: 8px;
        width: 12px;
        height: 12px;
        margin-bottom: -1px;
    }*/
	.category > div:nth-child(2) > div {
		transition: background-color 0.1s ease-in, color 0.1s ease-in;
	}
	.category > div:nth-child(2) > div.selected {
	/*	background: hsla(190, 40%, 45%, 1); */
		background: hsla(220, 30%, 35%, 0.25);
		/*color: white;*/
	}
	.category > div:nth-child(1) a {
		display: inline-block;
		margin-right: 8px;
		margin-bottom: -2px;
		width: 14px;
		height: 14px;
		outline-width: 2px;
		box-shadow: 0px 0px 3px #999;
		cursor: pointer;
	}
	.category.ghost {
		background: rgba(255, 255, 255, 0.45);
		border: 1px dotted rgba(28,110,164,0.65);
		border-radius: 4px;
		height: 72px;
	}
	.handle-area-category {
		position: relative;
		cursor: default;
	}
	.category.ghost > * {
		visibility: hidden;
	}
	.category.null-category > div:nth-child(1) {
	}
	.category.null-category.cluster > div:nth-child(1) {
		background: transparent;
	}
	.category.null-category > div:nth-child(1) h2 {
		color: hsla(204, 38%, 36%, 1);
		font-size: 1.35rem;
		font-style: oblique;
	}
	.category.null-category.cluster > div:nth-child(1) h2 {
		color: #999;
	}
	.category.null-category > div:nth-child(2) > div:first-child {
		border-top: 1px solid #d6d6d6;
	}

	.action .add:after { content: "+"; }

	a:focus {
		outline: none;
	}

	.action div {
		position: relative;
		display: inline-block;
		margin-right: -4px; /* See: http://css-tricks.com/fighting-the-space-between-inline-block-elements/ */
	}

	.action a {
		display: block;
		background-color: transparent;
		color: #a7a7a7;
		width: 60px;
		height: 60px;
		position: relative;
		text-align: center;
		line-height: 64px;
		border-radius: 50%;
		/*border:1px solid #dfdfdf;*/
		box-shadow: 0px 2px 4px #aaa;/*, inset 0px 2px 3px #fff;*/
		transition: background, color 200ms;
	}
	/*	.actions button.btn-criterium-add {
            color: #999;
        }
        .actions button.btn-criterium-add:hover {
            color: white;
        }
        .actions button.btn-criterium-add:hover i {
            color: white;
        }
        .actions button.btn-criterium-add i {
            color: #999;
        }*/
	.criterium-add-new button {
		background: none;
		background: none;
		padding: 0;
		border: none;
		transition: color 0.3s;
	}
	.criterium-add-new button i {
		color: #888;
		margin-right: 5px;
		transition: color 0.3s;
	}
	.criterium-add-new button:hover {
		background-color: transparent;
	}
	.criterium-add-new button:hover, .criterium-add-new button:hover i {
		color: #4f8be8;
	}
	criterium-add-new.criteriumDragging button:hover {
		color: #888;
	}
	.action a:hover {
		color: #555;
		background: #ededed;
	}
	a:link, a:visited, a:active, a:hover {
		text-decoration: none;
	}
	.vue-swatches {
		animation-name: fade-in;
		animation-duration: 300ms;
		margin-bottom: -8px;
	}
	.cluster-new {
		position: absolute;
		top: 0;
		left: 0;
		width: 240px;
		height: 60px;
	}
	.criterium.ghost {
		background: rgba(255, 255, 255, 0.45)!important;
		border: 1px dotted rgba(28,110,164,0.65);
		color: transparent;
	}
	.criterium {
		position: relative;
		cursor: default;
	}
	.item-actions {
		position: absolute;
		top: 10px;
		right: 4px;
		width: 20px;
		height: 20px;
		opacity: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		background: transparent;
		/*text-align: center;*/
		border: 1px solid transparent;
		border-radius: 3px;
		color: #777;
		transition: all 200ms;
		cursor: pointer;
	}
	.item-actions.show-menu {
		opacity: 1;
	}
	.item-actions:not(.show-menu) i {
		padding-top: 2px;
	}
	.item-actions i {
		pointer-events: none;
	}
	.item-actions, .item-actions i, .clusters li .item-actions i {
		font-size: 11px;
	}
	.container:not(.dragging) .criterium:hover .item-actions, .container:not(.dragging) .handle-area-category:hover .item-actions, .container:not(.dragging) .clusters li:hover .item-actions {
		opacity: 1;
	}
	.clusters li:hover .item-actions {
		color: #666;
	}
	.clusters li.selected:hover .item-actions {
		color: white;
	}
	.item-actions.show-menu, .item-actions:hover {
		background: #bbb;
		color: white;
	}
	.clusters li:hover .item-actions.show-menu, .clusters li .item-actions:hover {
		background: hsla(190, 20%, 60%, 1);
		color: white;
	}
	.clusters li.selected .item-actions.show-menu, .clusters li.selected .item-actions:hover {
		background: hsla(190, 40%, 40%, 1);
	}
	.criterium.selected .item-actions.show-menu, .criterium.selected .item-actions:hover {
		background: #a3a3a3;
	}
	.action-menu {
		position: absolute;
		top: 34px;
		right: -76px;
		min-width: 100px;
		background: white;
		box-shadow: 0px 0px 3px #999;
		border-radius: 3px;
		z-index: 10;
	}
	.clusters-view .action-menu li {
		margin-right: 0;
		font-size: 13px;
	}
	.action-menu i {
		margin-right: 4px;
		color: #666;
	}
	.action-menu ul {
		list-style: none;
	}
	.action-menu li {
		padding: 2px 4px;
		cursor: pointer;
		pointer-events: all;
	}
	.action-menu li:hover {
		background: #ddd;
	}
	.edit-title {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		z-index: 10;
		background: green;
	}
	.edit-title .cover {
		position: fixed;
		top: 0;
		left: 0;
		background: rgba(0, 0, 0, 0.31);
		width: 100%;
		height: 100%;
	}
	.edit-title .name-input {
		position: absolute;
		background: hsla(165, 5%, 90%, 1);
		padding-bottom: 4px;
		width: 100%;
		border-radius: 3px;
		color: black;
	}

	.modal-bg {
		position: fixed!important;
		background: rgba(0, 0, 0, 0.31);
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 10;
	}
	.modal-level {
		background: hsl(165, 5%, 90%);
		width: 420px;
		height: 150px!important;
		margin: 120px auto;
		padding: 20px;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		border-radius: 4px;
		box-shadow: 0px 6px 12px #666;
	}
	.modal-level .title {
		padding-bottom: 16px;
		margin-bottom: 10px;
		border-bottom: 1px solid hsl(200, 30%, 80%);
		width: 100%;
		text-align: center;
	}
	.modal-level button {
		outline-width: thin;
	}
	.modal-level button:first-child {
		margin-right: 8px;
	}
	.modal-level button:hover {
		color: white;
	}
	.clusters-view .name-input {
		width: 240px;
	}
	.clusters-view .name-input >>> input {
		color: #333;
	}
	.clusters-view .name-input >>> button:nth-child(2):not(:hover) {
		background: hsla(165, 5%, 90%, 1);
	}
	.edit-title .name-input >>> button:first-child { margin-left: 4px; }
	@keyframes fade-in {
		from { opacity: 0 }
		to { opacity: 1 }
	}
</style>
