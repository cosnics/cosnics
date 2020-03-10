<template>
	<div class="container" @mouseover="onMouseOver">
		<nav class="clusters">
			<draggable :list="clustersFiltered2" group="clusters" tag="ul" swapThreshold="0.75" :animation="250" ghost-class="ghost" @start="e => startDragCluster(e, 'view1')" @end="endDragCluster" :class="{ clusterDragging }" @change="onChange($event, 'view1')">
				<li v-for="cluster in clustersFiltered2" :id="cluster.id" :key="cluster.id" :class="{selected: isSelected(cluster)}" @click="selectCluster(cluster)"><!--@dragstart="rewriteDragImg($event)">-->
					<div>
						<i v-if="cluster.title !== ''" class="fa fa-map-o" aria-hidden="true"/><i v-else class="fa fa-institution empty" aria-hidden="true"/>{{cluster.title}}
					</div>
				</li>
			</draggable>
			<div class="actions">
				<div v-if="clusterDialogShown">
					<name-input class="cluster-new item-new" @ok="addNewCluster" @cancel="cancelNewCluster" placeholder="Titel voor nieuwe cluster" v-model="newCluster.title" />
				</div>
				<button v-else @click="showClusterDialog"><i class="fa fa-plus-circle" aria-hidden="true"/>Nieuw</button>
			</div>
		</nav>
		<div class="cluster-content" ref="cluster-content">
				<draggable v-if="isCategoryDraggable" handle=".handle" :list="categoriesFiltered2" group="categories" tag="div" swapTreshold="0.75" :animation="250" ghost-class="ghost"  @start="e => startDragCategory(e, 'view1')" @end="endDragCategory" @change="onChangeCategory($event, 'view1')">
					<div v-for="category in categoriesFiltered2" :id="category.id" :key="category.id" class="category">
						<div class="handle handle-area-category">
							<a :style="{'background-color': category.color}" tabindex="0" @click="() => openColorPickerForCategory(category)" @keyup.enter.space="() => openColorPickerForCategory(category)"></a>
							<h2 class="handle-area-category">{{ category.title }}</h2>
							<swatches v-if="isColorPickerOpened(category)" v-model="category.color" background-color="transparent" show-border swatch-size="20" inline @input="closeColorPicker"></swatches>
						</div>
						<div>
							<div v-for="criterium in category.criteria" @click="selectCriterium(criterium)" class="criterium" :class="{selected: store.selectedCriterium === criterium}">{{ criterium.title }}</div>
						</div>
						<div v-if="!isAddingCriteriumFor(category)" class="criterium-add-new" :class="{criteriumDragging: store.criteriumDragging}">
							<button @click="() => addCriteriumForCategory(category)"><i class="fa fa-plus-circle" aria-hidden="true"/>Voeg een criterium toe</button>
						</div>
						<div v-else>
							<name-input class="criterium-new item-new" @ok="addNewCriterium" @cancel="cancelNewCriterium" placeholder="Titel voor nieuw criterium" v-model="newCriterium.title"/>
						</div>
					</div>
				</draggable>
				<div v-else>
					<div v-for="category in categoriesFiltered2" :id="category.id" :key="category.id" class="category">
						<div class="handle handle-area-category">
							<a :style="{'background-color': category.color}" tabindex="0" @click="() => openColorPickerForCategory(category)" @keyup.enter.space="() => openColorPickerForCategory(category)"></a>
							<h2 class="handle-area-category">{{ category.title }}</h2>
							<swatches v-if="isColorPickerOpened(category)" v-model="category.color" background-color="transparent" show-border swatch-size="20" inline @input="closeColorPicker"></swatches>
						</div>
						<draggable handle=".criterium" :list="category.criteria" group="criteria" tag="div" swapTreshold="0.75" :animation="250" ghost-class="ghost" @start="store.criteriumDragging = true" @end="store.criteriumDragging = false">
							<div v-for="criterium in category.criteria" @click="selectCriterium(criterium)" class="criterium" :class="{selected: store.selectedCriterium === criterium}">{{ criterium.title }}</div>
						</draggable>
						<div v-if="!isAddingCriteriumFor(category)" class="criterium-add-new" :class="{ criteriumDragging }">
							<button @click="() => addCriteriumForCategory(category)"><i class="fa fa-plus-circle" aria-hidden="true"/>Voeg een criterium toe</button>
						</div>
						<div v-else>
							<name-input class="criterium-new item-new" @ok="addNewCriterium" @cancel="cancelNewCriterium" placeholder="Titel voor nieuw criterium" v-model="newCriterium.title"/>
						</div>
					</div>
				</div>
				<div class="actions" v-if="!isAddingCategory">
					<button class="btn-category-add" @click="addCategory"><i class="fa fa-plus-circle" aria-hidden="true"/>Categorie</button>
					<button class="btn-criterium-add" @click="addCriterium"><i class="fa fa-plus-circle" aria-hidden="true"/>Criterium</button>
				</div>
				<div v-else-if="isAddingCategory" class="category newcategory">
					<name-input class="category-new item-new" @ok="addNewCategory" @cancel="cancelNewCategory" placeholder="Titel voor nieuwe categorie" v-model="newCategory.title"/>
				</div>
		</div>
		<nav class="clusters">
			<draggable :list="clustersFiltered1" group="clusters" tag="ul" swapThreshold="0.75" :animation="250" ghost-class="ghost" @start="e => startDragCluster(e, 'view2')" @end="endDragCluster" :class="{ clusterDragging }" @change="onChange($event, 'view2')">
				<li v-for="cluster in clustersFiltered1" :id="cluster.id" :key="cluster.id"  :class="{selected: isSelected2nd(cluster)}" @click="selectCluster2nd(cluster)"><div><i v-if="cluster.title !== ''" class="fa fa-map-o" aria-hidden="true"/><i v-else class="fa fa-institution empty" aria-hidden="true"/>{{cluster.title}}</div></li>
			</draggable>
		</nav>
		<div class="cluster-content">
			<draggable v-if="isCategoryDraggable" handle=".handle" :list="categoriesFiltered1" group="categories" tag="div" swapTreshold="0.75" :animation="250" ghost-class="ghost"   @start="e => startDragCategory(e, 'view2')" @end="endDragCategory" @change="onChangeCategory($event, 'view2')">
				<div v-for="category in categoriesFiltered1" class="category" :id="category.id" :key="category.id" >
					<div class="handle handle-area-category">
						<a :style="{'background-color': category.color}" tabindex="0"></a>
						<h2 class="handle-area-category">{{ category.title }}</h2>
					</div>
					<div>
						<div v-for="criterium in category.criteria" @click="selectCriterium(criterium)" class="criterium" :class="{selected: store.selectedCriterium === criterium}">{{ criterium.title }}</div>
					</div>
				</div>
			</draggable>
			<div v-else>
				<div v-for="category in categoriesFiltered1" class="category" :id="category.id" :key="category.id" >
					<div class="handle handle-area-category">
						<a :style="{'background-color': category.color}" tabindex="0"></a>
						<h2 class="handle-area-category">{{ category.title }}</h2>
					</div>
					<draggable handle=".criterium" :list="category.criteria" group="criteria" tag="div" swapTreshold="0.75" :animation="250" ghost-class="ghost" @start="criteriumDragging = true" @end="criteriumDragging = false">
						<div v-for="criterium in category.criteria" @click="selectCriterium(criterium)" class="criterium" :class="{selected: selectedCriterium === criterium}" :id="criterium.id" :key="criterium.id">{{ criterium.title }}</div>
					</draggable>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
	import {Component, Vue, Watch} from "vue-property-decorator";
	import 'jquery.fancytree/dist/modules/jquery.fancytree.edit';
	import 'jquery.fancytree/dist/modules/jquery.fancytree.dnd';
	import draggable from 'vuedraggable';
	import Cluster from "../../Domain/Cluster";
	import Criterium from "../../Domain/Criterium";
	import Category from "../../Domain/Category";
	import Swatches from 'vue-swatches';
	import NameInput from "./NameInput.vue";
	import TreeNode from "../../Domain/TreeNode";

	//const swatchColors = ['#FF0000', '#00FF00', '#F493A7', '#F891A6', '#FFCCD5', 'hsl(190, 100%, 50%)'];

	@Component({
		name: 'score-rubric-view',
		components: { Swatches, NameInput, draggable }
	})
	export default class ScoreRubricView extends Vue {

		private draggedClusterView1: Cluster|null = null;
		private draggedClusterView2: Cluster|null = null;
		private draggedCategoryView1: Category|null = null;
		private draggedCategoryView2: Category|null = null;
		private selectedCluster: Cluster|null = this.store.rubric.clusters.length && this.store.rubric.clusters[0] || null;
		private selectedCluster2nd: Cluster|null = this.store.rubric.clusters.length && this.store.rubric.clusters[0] || null;
		private selectedCategoryColorPicker: Category|null = null;
    	private selectedCategoryNewCriterium: Category|null = null;
		private selectedCriterium: Criterium|null = null;
		private newCluster: Cluster|null = null;
		private newCategory: Category|null = null;
		private newCriterium: Criterium|null = null;
		private initiatedDrag: string = '';
		private clusterDragging: boolean = false;
		private categoryDragging: boolean = false;
		private criteriumDragging: boolean = false;
		private clusterDialogShown: boolean = false;
		private isAddingCategory: boolean = false;
		private isAddingCriterium: boolean = false;
		private overCategoryHandleArea: boolean = false;

		onMouseOver(event: MouseEvent) {
			const target = event.target! as any;
			if (typeof target.className !== 'string') { return; }
			this.overCategoryHandleArea = target.className.indexOf('handle-area-category') !== -1 || target.className.indexOf('vue-swatches') !== -1 ;
		}

		startDragCluster(el: HTMLElement, view: string) {
			this.clusterDragging = true;
			this.initiatedDrag = view;
			const cluster = this.store.rubric.clusters.find((c: Cluster) => c.id === (el as any).item.id);
			if (view === 'view1') {
				this.draggedClusterView1 = cluster;
				this.draggedClusterView2 = null;
			} else if (view === 'view2') {
				this.draggedClusterView2 = cluster;
				this.draggedClusterView1 = null;
			}
		}
		endDragCluster() {
			this.clusterDragging = false;
			this.draggedClusterView1 = null;
			this.draggedClusterView2 = null;
			this.initiatedDrag = '';
		}
		startDragCategory(el: HTMLElement, view: string) {
			this.categoryDragging = true;
			this.initiatedDrag = view;
			const categories = this.store.rubric.getAllCategories();
			const category = categories.find((c: Category) => c.id === (el as any).item.id);
			if (view === 'view1') {
				this.draggedCategoryView1 = category;
				this.draggedCategoryView2 = null;
			} else if (view === 'view2') {
				this.draggedCategoryView2 = category;
				this.draggedCategoryView1 = null;
			}
		}
		endDragCategory() {
			this.categoryDragging = false;
			this.draggedCategoryView1 = null;
			this.draggedCategoryView2 = null;
			this.initiatedDrag = '';
		}
		get isCategoryDraggable() {
			if (this.criteriumDragging) { return false; }
			if (this.categoryDragging) { return true; }
			return this.overCategoryHandleArea;
		}
		onChange(evt: any, view: string) {
			if (evt.added && evt.added.element) {
				const oldIndex = this.store.rubric.clusters.indexOf(evt.added.element);
				this.store.rubric.moveChild(evt.added.element, evt.added.newIndex, oldIndex);
				this.store.moveChild(evt.added.element, this.store.rubric, evt.added.newIndex);
			} else if (evt.moved) {
				this.store.rubric.moveChild(evt.moved.element, evt.moved.newIndex, evt.moved.oldIndex);
				this.store.moveChild(evt.moved.element, this.store.rubric, evt.moved.newIndex);
			}
		}
		onChangeCategory(evt: any, view: string) {
			if (evt.added && evt.added.element) {
				if (this.selectedCluster === this.selectedCluster2nd) {
					const oldIndex = this.selectedCluster.categories.indexOf(evt.added.element);
					this.selectedCluster.moveChild(evt.added.element, evt.added.newIndex, oldIndex);
					this.store.moveChild(evt.added.element, this.selectedCluster, evt.added.newIndex);
				} else {
					if (view === 'view1') {
						this.selectedCluster2nd.removeChild(evt.added.element);
						this.selectedCluster.addChild(evt.added.element, evt.added.newIndex);
						this.store.moveChild(evt.added.element, this.selectedCluster, evt.added.newIndex);
					} else {
						this.selectedCluster.removeChild(evt.added.element);
						this.selectedCluster2nd.addChild(evt.added.element, evt.added.newIndex);
						this.store.moveChild(evt.added.element, this.selectedCluster2nd, evt.added.newIndex);
					}
				}
			} else if (evt.moved) {
				let category: Category = evt.moved.element;
				let cluster = category.parent as Cluster;
				cluster.moveChild(category, evt.moved.newIndex, evt.moved.oldIndex);
				this.store.moveChild(category, cluster, evt.moved.newIndex);
			}
		}
		get console() {
			return window.console;
		}
		addCategory() {
			this.newCategory = new Category();
			this.isAddingCategory = true;
		}

		addNewCategory() {
			this.selectedCluster!.addChild(this.newCategory!, this.selectedCluster!.categories.length);
			this.newCategory = null;
			this.isAddingCategory = false;
		}

		addNewCriterium(name: string) {
		rewriteDragImg(nth: any, event: any) {
			event.target.style.cursor = 'grabbing';
			this.selectedCategoryNewCriterium!.addCriterium(this.newCriterium!);
			this.newCriterium = null;
			this.selectedCategoryNewCriterium = null;
			this.isAddingCriterium = false;
			this.isAddingCategory = false;
		}
			let el = event.target.cloneNode(true);
			el.className="dragged-item";
			el.style.width = event.target.style.width;
			/*el.style.position = "absolute";
			el.style.left = "-1000px";
			el.style.top = "-1000px";
			el.style.cursor = "grabbing";
			el.style.transform = "rotate(45deg)";*/
			document.body.appendChild(el);
			event.dataTransfer.effectAllowed = "copyMove";
			event.dataTransfer.mozCursor = "grabbing";
			event.dataTransfer.setDragImage(el, 0,0);
		}
		cancelNewCriterium() {
			this.newCriterium = null;
			this.selectedCategoryNewCriterium = null;
			this.isAddingCriterium = false;
		}

		cancelNewCategory() {
			this.newCategory = null;
			this.isAddingCategory = false;
		}

		addCriteriumForCategory(category: Category) {
			const criterium = new Criterium();
			this.selectedCategoryNewCriterium = category;
			this.newCriterium = criterium;
			this.isAddingCriterium = true;
		}

		addCriterium() {
			this.isAddingCriterium = true;
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

		selectCluster(cluster: Cluster|null) {
			this.selectedCluster = cluster;
		}

		selectCluster2nd(cluster: Cluster|null) {
			this.selectedCluster2nd = cluster;
		}

		selectCriterium(criterium: Criterium|null) {
			this.selectedCriterium = criterium;
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

		isColorPickerOpened(category: Category) {
			if (this.selectedCategoryColorPicker === null) { return false; }
			return category === this.selectedCategoryColorPicker;
		}

		isSelected(cluster: Cluster) {
			return cluster === this.selectedCluster;
		}

		isSelected2nd(cluster: Cluster) {
			return cluster === this.selectedCluster2nd;
		}

		get clustersFiltered1() {
			return this.store.rubric.clusters.filter((c: Cluster) => c !== this.draggedClusterView1);
		}

		get clustersFiltered2() {
			return this.store.rubric.clusters.filter((c: Cluster) => c !== this.draggedClusterView2);
		}

		checkMove(evt: any) {
			console.log(evt);
			return true;
		}

		get categoriesFiltered2() {
			if (!this.selectedCluster) { return []; }
			return this.selectedCluster.categories.filter((c: Category) => c !== this.draggedCategoryView2);/*.map((c:Category) => {
				const newCat = Category.fromJSON(c.toJSON());
				newCat.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
				newCat.actualCategory = c;
				return newCat;
			});*/
/*			if (!this.store.draggedCategoryView2) {
				console.log('return new');
				return this.selectedCluster.categories.map((c:Category) => {
					const newCat = Category.fromJSON(c.toJSON());
					newCat.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
					return newCat;
				});
			} else {
				console.log('return old');
				return this.selectedCluster.categories.filter((c: Category) => c !== this.store.draggedCategoryView2);
			}*/
/*			console.log(this.store.draggedCategoryView2);
			const categories = this.selectedCluster.categories.filter((c: Category) => c !== this.store.draggedCategoryView2);
			return categories.map((c:Category) => {
				const newCat = Category.fromJSON(c.toJSON());
				newCat.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
				return newCat;
			}); */
		}

		get categoriesFiltered1() {
			if (!this.selectedCluster2nd) { return []; }
			return this.selectedCluster2nd.categories.filter((c: Category) => c !== this.draggedCategoryView1);/*.map((c:Category) => {
				const newCat = Category.fromJSON(c.toJSON());
				newCat.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
				newCat.actualCategory = c;
				return newCat;
			});*/
/*			if (!this.store.draggedCategoryView1) {
				return this.selectedCluster2nd.categories.map((c:Category) => {
					const newCat = Category.fromJSON(c.toJSON());
					newCat.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
					return newCat;
				});
			} else {
				return this.selectedCluster2nd.categories.filter((c: Category) => c !== this.store.draggedCategoryView1);
			} */
		}

		get store(){
			return this.$root.$data.store;
		}

		get rubric(){
			return this.store.rubric;
		}

		@Watch('store.rubric')
		onRubricChanged(){
			console.log("change");
		}

		@Watch('isAddingCategory')
		onCategoryAddingChanged() {
			console.log('isAddingCategory', this.isAddingCategory);
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
	#app > div > div {
		display: flex;
		height: 100vh;
	}
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
	.dragged-item {
		background: hsla(165, 5%, 90%, 1);
		opacity: 1;
		position: absolute;
		top: -1000px;
		left: -1000px;
		cursor: grab;
	}
	.sortable-fallback {
		background: red;
	}
	.container {
		width: 100%;
		padding-top: 20px;
		background-color: hsla(165, 5%, 90%, 1);
		display: flex;
		flex-direction: column;
		overflow-x: auto;
	}

	/* Navigation: Clusters */
	nav.clusters {
		--background: hsla(200, 10%, 80%, 1);
		width: 100%;
		height: 36px;
		display: flex;
	}
	ul {
		margin-left: 21px;
		list-style-type: none;
		display: flex;
	}
	li {
		display: block;
		margin: 0 10px 0 0;
		padding: 0;
		color: #444;
		cursor: grab;
	}
	li.selected {
		--background: hsla(190, 40%, 45%, 1);
		color: white;
	}
	li div {
		margin: 0;
		padding: 8px 12px;
		min-width: 70px;
		height: 36px;
		background: var(--background);
		border-radius: 3px;
		text-align:center;
	}
	li > div i {
		margin-right: 8px;
		font-size: 13px;
	}
	li > div i.empty {
		margin-right: 0;
	}
	ul:not(.clusterDragging) li:not(.selected):hover {
		--background: hsla(190, 20%, 75%, 1);
		color: #222;
	}
	li.selected:after {
		content: '';
		display: block;
		margin: 0 auto;
		padding: 0;
		width: 0;
		height: 0;
		border-width: 11px 13px 0;
		border-color: var(--background) transparent;
		border-style: solid solid none;
	}
	li.selected div {
		box-shadow: 0px 2px 4px #999;
	}
	li, li div {
		-moz-user-select: -moz-none;
		-khtml-user-select: none;
		-webkit-user-select: none;
		-o-user-select: none;
		user-select: none;
	}
	li div {
		pointer-events: none;
	}
	li.ghost {
		background: rgba(255, 255, 255, 0.45);
		border: 1px dotted rgba(28,110,164,0.65);
		border-radius: 4px;
	}
	li.ghost:after, li.ghost > * {
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
		padding-top: 40px;
		width: 100%;
		user-select: none;
		overflow-x: auto;
		overflow-y: hidden;
		overflow-wrap: break-word;
		display: flex;
	}
	.cluster-content > div {
		margin-left: 21px;
		white-space: nowrap;
		display: flex;
		flex-direction: row;
		align-items: flex-start;
	}
	.cluster-content > div > div {
		margin-right: 16px;
		width: 240px;
	}
	.cluster-content .actions {
		margin-left: 0;
	}
	.category {
		min-width: 240px;
		white-space: normal;
		border: 1px solid #dedede;
		border-radius: 4px;
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
		background: linear-gradient(to bottom, rgba(255, 255, 255, 0.4) 0px, rgba(255, 255, 255, 0) 10px, rgba(0,0,0,0) 19px,  rgba(0,0,0,0.08) 36px);
		font-weight: bold;
		border-radius: 4px 4px 0 0;
	}
	.category.newcategory {
		margin-left: 0;
		border: none;
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
		background: linear-gradient(to top, rgba(0, 0, 0, 0.06) 0px, rgba(0, 0, 0, 0) 20px);
	}
	.category > div:nth-child(2) > div:first-child {
		border-top: 1px solid #ccc;
	}
	.category > div:nth-child(2) > div:last-child {
		border-bottom: 1px solid #ccc;
	}
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
		background: hsla(220, 30%, 35%, 0.5);
		color: white;
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
	.icon-btn {
		font-family: '-webkit-pictograph';
		/*font-style: normal;*/
		font-size: 3em;
		speak: none;
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
