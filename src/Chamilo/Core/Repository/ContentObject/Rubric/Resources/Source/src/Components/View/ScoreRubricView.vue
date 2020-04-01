<template>
	<div class="container" :class="mainClass">
		<div>
			<div>
				<div class="clusters-view" @mouseover="dragMouseOver($event,'view1_clusters')" @mouseout="dragMouseOut" :class="{ 'no-drop': clusterDragging && bannedForDrop === 'view1_clusters' }">
					<draggable id="view1_clusters" tag="ul"	group="clusters" class="clusters" ghost-class="ghost" :list="clusters" :class="{ 'cluster-dragging': clusterDragging }" :forceFallback="true" :animation="250"
							   :move="onMoveCluster" @start="startDragCluster"	@end="endDrag" @change="onChangeCluster">
						<li v-for="cluster in clusters" :id="`view1_${cluster.id}`" :key="`view1_${cluster.id}`" class="cluster" :class="{selected: isSelected(cluster, 'view1')}" @click="selectCluster(cluster, 'view1')">
							<div class="title"><div><i :class="cluster.title === '' ? 'fa fa-institution' : 'fa fa-map-o'" aria-hidden="true"/><span>{{cluster.title}}</span></div></div>
						</li>
					</draggable>
					<div class="actions">
						<div v-if="clusterDialogShown">
							<name-input class="cluster-new item-new" @ok="addNewCluster" @cancel="cancelNewCluster" placeholder="Titel voor nieuwe cluster" v-model="newCluster.title" />
						</div>
						<button v-else @click="showClusterDialog"><i class="fa fa-plus" aria-hidden="true"/>Nieuw</button>
					</div>
				</div>
				<div class="cluster-content" ref="cluster-content" @mouseover="categoryDragging && dragMouseOver($event,'view1_categories')" @mouseout="categoryDragging && dragMouseOut" :class="{ 'no-drop': categoryDragging && bannedForDrop === 'view1_categories' }">
					<draggable id="view1_categories" tag="div" group="categories" handle=".handle" ghost-class="ghost" :list="categoriesView1" :forceFallback="true" :animation="250" :move="onMoveCategory"
							   @start="startDragCategory" @end="endDrag" @change="onChangeCategory($event, 'view1')">
						<div v-for="category in categoriesView1" @mouseover="criteriumDragging && dragMouseOver($event, `view1_${category.id}`)" @mouseout="criteriumDragging && dragMouseOut" :id="`view1_${category.id}`" :key="`view1_${category.id}`" class="category" :class="{ 'no-drop': criteriumDragging && bannedForDrop === `view1_${category.id}`, 'null-category': category.title === '' }">
							<div v-if="category.title !== ''" class="handle handle-area-category">
								<a :style="{'background-color': category.color}" tabindex="0" @click="() => openColorPickerForCategory(category)" @keyup.enter.space="() => openColorPickerForCategory(category)"></a>
								<h2 class="handle-area-category">{{ category.title }}</h2>
								<swatches v-if="isColorPickerOpened(category)" v-model="category.color" background-color="transparent" show-border swatch-size="20" inline @input="closeColorPicker"></swatches>
							</div>
							<div v-else class="handle handle-area-category">
								<h2 class="handle-area-category">Criteria</h2>
							</div>
							<draggable tag="div" group="criteria" handle=".criterium" ghost-class="ghost" swapTreshold="0.75" :list="category.criteria"	:forceFallback="true" :animation="250"
									   :move="onMoveCriterium"	@start="startDragCriterium"	@end="endDrag"	@change="onChangeCriterium($event, category)">
								<div v-for="criterium in category.criteria" :id="`view1_${criterium.id}`" :key="`view1_${criterium.id}`" @click="selectCriterium(criterium)" class="criterium" :class="{selected: selectedCriterium === criterium}">{{ criterium.title }}</div>
							</draggable>
							<div v-if="!isAddingCriteriumFor(category)" class="criterium-add-new" :class="{criteriumDragging: criteriumDragging}">
								<button @click="() => addCriteriumForCategory(category)"><i class="fa fa-plus" aria-hidden="true"/>Voeg een criterium toe</button>
							</div>
							<div v-else>
								<name-input class="criterium-new item-new" @ok="addNewCriterium" @cancel="cancelNewCriterium" placeholder="Titel voor nieuw criterium" v-model="newCriterium.title"/>
							</div>
						</div>
						<div slot="footer" class="no-category"></div>
					</draggable>
					<div class="actions" v-if="!isAddingCategory && !isAddingCriteriumWithoutCategory">
						<button class="btn-category-add" @click="addCategory"><i class="fa fa-plus" aria-hidden="true"/>Categorie</button>
						<button class="btn-criterium-add" @click="addCriterium"><i class="fa fa-plus" aria-hidden="true"/>Criterium</button>
					</div>
					<div v-else-if="isAddingCategory" class="category newcategory">
						<name-input class="category-new item-new" @ok="addNewCategory" @cancel="cancelNewCategory" placeholder="Titel voor nieuwe categorie" v-model="newCategory.title"/>
					</div>
					<div v-else-if="isAddingCriteriumWithoutCategory" class="category newcategory">
						<name-input class="category-new item-new" @ok="addNewCriterium(true)" @cancel="cancelNewCriterium" placeholder="Titel voor nieuw criterium" v-model="newCriterium.title"/>
					</div>
				</div>
			</div>
			<div v-if="split">
				<div class="clusters-view" @mouseover="dragMouseOver($event, 'view2_clusters')" @mouseout="dragMouseOut" :class="{ 'no-drop': clusterDragging && bannedForDrop === 'view2_clusters' }">
					<draggable id="view2_clusters" tag="ul"	group="clusters" class="clusters" ghost-class="ghost" :list="clusters" :class="{ 'cluster-dragging': clusterDragging }" :forceFallback="true"	:animation="250"
							   :move="onMoveCluster" @start="startDragCluster"	@end="endDrag" @change="onChangeCluster">
						<li v-for="cluster in clusters" :id="`view2_${cluster.id}`" :key="`view2_${cluster.id}`" class="cluster" :class="{selected: isSelected(cluster, 'view2')}" @click="selectCluster(cluster, 'view2')">
							<div class="title"><div><i :class="cluster.title === '' ? 'fa fa-institution' : 'fa fa-map-o'" aria-hidden="true"/><span>{{cluster.title}}</span></div></div>
						</li>
					</draggable>
				</div>
				<div class="cluster-content" @mouseover="categoryDragging && dragMouseOver($event,'view2_categories')" @mouseout="categoryDragging && dragMouseOut" :class="{ 'no-drop': categoryDragging && bannedForDrop === 'view2_categories' }">
					<draggable id="view2_categories" tag="div"	group="categories" handle=".handle"	ghost-class="ghost" :list="categoriesView2"	:forceFallback="true" :animation="250" :move="onMoveCategory"
							   @start="startDragCategory" @end="endDrag" @change="onChangeCategory($event, 'view2')">
						<div v-for="category in categoriesView2" class="category" @mouseover="criteriumDragging && dragMouseOver($event, `view2_${category.id}`)" @mouseout="criteriumDragging && dragMouseOut" :id="`view2_${category.id}`" :key="`view2_${category.id}`" :class="{ 'no-drop': criteriumDragging && bannedForDrop === `view2_${category.id}`, 'null-category': category.title === '' }">
							<div v-if="category.title !== ''" class="handle handle-area-category">
								<a :style="{'background-color': category.color}" tabindex="0"></a>
								<h2 class="handle-area-category">{{ category.title }}</h2>
							</div>
							<div v-else class="handle handle-area-category">
								<h2 class="handle-area-category">Criteria</h2>
							</div>
							<draggable tag="div" group="criteria" handle=".criterium" ghost-class="ghost" swapTreshold="0.75" :list="category.criteria" :forceFallback="true" :animation="250"
									   :move="onMoveCriterium" @start="startDragCriterium"	@end="endDrag"	@change="onChangeCriterium($event, category)">
								<div v-for="criterium in category.criteria" :id="`view2_${criterium.id}`" :key="`view2_${criterium.id}`" @click="selectCriterium(criterium)" class="criterium" :class="{selected: selectedCriterium === criterium}">{{ criterium.title }}</div>
							</draggable>
						</div>
						<div slot="footer" class="no-category"></div>
					</draggable>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
	import {Component, Prop, Watch, Vue} from "vue-property-decorator";
	import draggable from 'vuedraggable';
	import Cluster from "../../Domain/Cluster";
	import Criterium from "../../Domain/Criterium";
	import Category from "../../Domain/Category";
	import Swatches from 'vue-swatches';
	import NameInput from "./NameInput.vue";

	//const swatchColors = ['#FF0000', '#00FF00', '#F493A7', '#F891A6', '#FFCCD5', 'hsl(190, 100%, 50%)'];

	@Component({
		name: 'score-rubric-view',
		components: { Swatches, NameInput, draggable }
	})
	export default class ScoreRubricView extends Vue {

		private selectedClusterView1: Cluster|null = this.store.rubric.clusters.length && this.store.rubric.clusters[0] || null;
		private selectedClusterView2: Cluster|null = this.store.rubric.clusters.length && this.store.rubric.clusters[0] || null;
		private selectedCategoryColorPicker: Category|null = null;
    	private selectedCategoryNewCriterium: Category|null = null;
		private newCluster: Cluster|null = null;
		private newCategory: Category|null = null;
		private newCriterium: Criterium|null = null;
		private initiatedDrag: string = '';
		private bannedForDrop: string = '';
		private overElementId: string = '';
		private clusterDragging: boolean = false;
		private categoryDragging: boolean = false;
		private criteriumDragging: boolean = false;
		private clusterDialogShown: boolean = false;
		private isAddingCategory: boolean = false;
		private isAddingCriterium: boolean = false;
		private isAddingCriteriumWithoutCategory: boolean = false;

		@Prop(Criterium) readonly selectedCriterium!: Criterium | null;
		@Prop(Boolean) readonly split!: boolean;

		dragMouseOver(event: any, elementId: string) {
			if (!this.initiatedDrag) { return; }
			this.overElementId = elementId;
		}

		dragMouseOut() {
			if (!this.initiatedDrag) { return; }
			this.overElementId = '';
		}

		endDrag() {
			this.clusterDragging = false;
			this.categoryDragging = false;
			this.criteriumDragging = false;
			this.initiatedDrag = '';
			this.bannedForDrop = '';
			this.overElementId = '';
		}

		startDragCluster(event: any) {
			const view = event.item.id.split('_')[0];
			this.clusterDragging = true;
			this.initiatedDrag = view;
			if (view === 'view1') {
				this.bannedForDrop = 'view2_clusters';
			} else if (view === 'view2') {
				this.bannedForDrop = 'view1_clusters';
			}
		}

		onMoveCluster(event: any) {
			return event.related.parentElement.id !== this.bannedForDrop;
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

		startDragCategory(event: any) {
			const view = event.item.id.split('_')[0];
			this.categoryDragging = true;
			this.initiatedDrag = view;
			if (this.selectedClusterView1 !== null && this.selectedClusterView1 === this.selectedClusterView2) {
				if (view === 'view1') {
					this.bannedForDrop = 'view2_categories';
				} else if (view === 'view2') {
					this.bannedForDrop = 'view1_categories';
				}
			}
		}

		onMoveCategory(event: any) {
			return event.related.parentElement.id !== this.bannedForDrop;
		}

		onChangeCategory(event: any, view: string) {
			if (event.added && event.added.element) {
				if (this.selectedClusterView1 !== null && this.selectedClusterView1 === this.selectedClusterView2) {
					throw new Error(''); // Todo: meaningful message
				}
				const { element, newIndex } = event.added;
				if (view === 'view1') {
					this.selectedClusterView2!.removeChild(element);
					this.selectedClusterView1!.addChild(element, newIndex);
					this.store.moveChild(element, this.selectedClusterView1, newIndex);
				} else {
					this.selectedClusterView1!.removeChild(element);
					this.selectedClusterView2!.addChild(element, newIndex);
					this.store.moveChild(element, this.selectedClusterView2, newIndex);
				}
			} else if (event.moved) {
				const category: Category = event.moved.element;
				const cluster = category.parent as Cluster;
				cluster.moveChild(category, event.moved.newIndex, event.moved.oldIndex);
				this.store.moveChild(category, cluster, event.moved.newIndex);
			}
		}

		startDragCriterium(event: any) {
			const [view, categoryId] = event.item.parentElement?.parentElement.id.split('_');
			this.criteriumDragging = true;
			this.initiatedDrag = view;
			if (this.selectedClusterView1 !== null && this.selectedClusterView1 === this.selectedClusterView2) {
				if (view === 'view1') {
					this.bannedForDrop = `view2_${categoryId}`;
				} else if (view === 'view2') {
					this.bannedForDrop = `view1_${categoryId}`;
				}
			}
		}

		onMoveCriterium(event: any) {
			return event.related.parentElement?.parentElement.id !== this.bannedForDrop;
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

		get console() {
			return window.console;
		}

		addCategory() {
			this.newCategory = new Category();
			this.newCategory.color = 'transparent';
			this.isAddingCategory = true;
		}

		addNewCategory() {
			this.selectedClusterView1!.addChild(this.newCategory!, this.selectedClusterView1!.categories.length);
			this.newCategory = null;
			this.isAddingCategory = false;
		}

		cancelNewCategory() {
			this.newCategory = null;
			this.isAddingCategory = false;
		}

		addCriterium() {
			this.selectedCategoryNewCriterium = new Category();
			this.selectedCategoryNewCriterium.color = '';
			this.newCriterium = new Criterium();
			this.isAddingCriteriumWithoutCategory = true;
		}

		addCriteriumForCategory(category: Category) {
			this.selectedCategoryNewCriterium = category;
			this.newCriterium = new Criterium();
			this.isAddingCriterium = true;
		}

		addNewCriterium(addCategory : boolean = false) {
			this.selectedCategoryNewCriterium!.addChild(this.newCriterium!, this.selectedCategoryNewCriterium!.criteria.length);
			this.newCriterium = null;
			if (addCategory) {
				this.selectedClusterView1!.addChild(this.selectedCategoryNewCriterium!, this.selectedClusterView1!.categories.length);
			}
			this.selectedCategoryNewCriterium = null;
			this.isAddingCriterium = false;
			this.isAddingCriteriumWithoutCategory = false;
			this.isAddingCategory = false;
		}

		cancelNewCriterium() {
			this.newCriterium = null;
			this.selectedCategoryNewCriterium = null;
			this.isAddingCriteriumWithoutCategory = false;
			this.isAddingCriterium = false;
		}

		addNewCluster() {
			const cluster = this.newCluster;
			this.newCluster = null;
			this.store.rubric.addChild(cluster, this.store.rubric.clusters.length);
			this.clusterDialogShown = false;
			this.selectCluster(cluster);
		}

		cancelNewCluster() {
			this.clusterDialogShown = false;
			this.newCluster = null;
		}

		showClusterDialog() {
			this.clusterDialogShown = true;
			this.newCluster = new Cluster();
		}

		isAddingCriteriumFor(category: Category) {
			return this.isAddingCriterium && this.selectedCategoryNewCriterium === category;
		}

		selectCluster(cluster: Cluster|null, view: string = 'view1') {
			if (view === 'view1') {
				this.selectedClusterView1 = cluster;
			} else if (view === 'view2') {
				this.selectedClusterView2 = cluster;
			}
		}

		selectCriterium(criterium: Criterium|null) {
			this.$emit('criterium-selected', criterium);
		}

		getCategoriesForCluster(cluster: Cluster) {
			return cluster.categories;
		}

		selectColor(color: string, category: Category) {
			this.store.selectedColorButton = null;
		}

		closeColorPicker() {
			window.setTimeout(() => this.openColorPickerForCategory(null), 400);
		}

		openColorPickerForCategory(category: Category|null) {
			this.selectedCategoryColorPicker = this.selectedCategoryColorPicker !== category ? category : null;
		}

		isColorPickerOpened(category: Category) : boolean {
			if (this.selectedCategoryColorPicker === null) { return false; }
			return category === this.selectedCategoryColorPicker;
		}

		isSelected(cluster: Cluster, view: string = 'view1') : boolean {
			if (view === 'view1') {
				return cluster === this.selectedClusterView1;
			} else if (view === 'view2') {
				return cluster === this.selectedClusterView2;
			}
			return false;
		}

		get clusters() {
			return [...this.store.rubric.clusters];
		}

		get categoriesView1() {
			if (!this.selectedClusterView1) { return []; }
			return [...this.selectedClusterView1.categories];
		}

		get categoriesView2() {
			if (!this.selectedClusterView2) { return []; }
			return [...this.selectedClusterView2.categories];
		}

		get store(){
			return this.$root.$data.store;
		}

		get rubric(){
			return this.store.rubric;
		}

		get mainClass() {
			return {
				'dragging': this.initiatedDrag !== '',
				'not-allowed': this.bannedForDrop !== '' && this.overElementId === this.bannedForDrop
			};
		}

		@Watch('store.rubric')
		onRubricChanged(){
			console.log("change");
		}

		@Watch('isAddingCategory')
		onCategoryAddingChanged() {
//			if (!this.store.isAddingCategory) {
				this.$nextTick(()=> {
					const clusterContent = this.$refs['cluster-content'] as HTMLElement;
					clusterContent.scrollTo(clusterContent.scrollWidth, 0);
				});
//			}
		}
		mounted() {
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
	.item-new button { font-weight: normal; margin-bottom: 4px; }
	.item-new button:not(:hover):nth-child(1) {
		background: hsla(200, 100%, 57%, 1);
		color: #fff;
	}
	.item-new button:not(:hover):nth-child(2) {
		background: transparent;
		border: 1px solid #cdcdcd;
	}
</style>
<style scoped>
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
		height: 50%;
		overflow-x: hidden;
		display: flex;
		flex-direction: column;
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
	.clusters:not(.cluster-dragging) li:not(.selected):hover {
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
		pointer-events: none;
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
	/* TODO Wishlist: hud */
	/*.category.hud {
		position: relative;
	}
	.category.hud:nth-child(1):before {
		content: '';
		display: block;
		width: 100vw;
		height: 100vh;
		background: rgba(0, 0, 0, 0.5);
		position: fixed;
		top: 0;
		left: 0;
	}
	.category.hud > div:nth-child(1) {
		background: white;
		position: absolute;
	}*/
	.category > div {
		font-size: 13px;
	}
	.category > div:nth-child(1) {
		background: #dfdfdf;
		background: linear-gradient(to bottom, rgba(255, 255, 255, 0.4) 0px, rgba(255, 255, 255, 0) 10px, rgba(0,0,0,0) 19px, rgba(0,0,0,0.08) 36px);
		font-weight: bold;
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
	}
	.category.ghost {
		background: rgba(255, 255, 255, 0.45);
		border: 1px dotted rgba(28,110,164,0.65);
		border-radius: 4px;
		height: 72px;
	}
	.category.ghost > * {
		visibility: hidden;
	}
	.category.null-category > div:nth-child(1) {
	}
	.category.null-category > div:nth-child(1) h2 {
		color: hsla(204, 38%, 36%, 1);
		font-size: 1.35rem;
		font-style: oblique;
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
	@keyframes fade-in {
		from { opacity: 0 }
		to { opacity: 1 }
	}
</style>
