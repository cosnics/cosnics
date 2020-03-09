import Rubric from "./Domain/Rubric";
import TreeNode from "./Domain/TreeNode";
import Cluster from "./Domain/Cluster";
import Category from "./Domain/Category";
import Criterium from "./Domain/Criterium";

import axios from "axios";
import Vue from "vue";
import PQueue from "p-queue";

export default class ScoreRubricStore {
    public rubric!: Rubric;
    public useScore: boolean = true;
    public selectedTreeNode!: TreeNode;
    public selectedCluster?: Cluster;
    public selectedClusterCategories: Category[] = [];
    public selectedCluster2nd?: Cluster;
    public selectedCluster2ndCategories: Category[] = [];
    public selectedCategoryColorPicker?: Category;
    public selectedCategoryNewCriterium?: Category;
    public draggedClusterView1?: Cluster;
    public draggedClusterView2?: Cluster;
    public draggedCategoryView1?: Category;
    public draggedCategoryView2?: Category;
    public initiatedDrag: string = '';
    public newCluster?: Cluster;
    public newCategory?: Category;
    public newCriterium?: Criterium;
    public selectedCriterium?: Criterium;
    public clusterDragging: boolean = false;
    public categoryDragging: boolean = false;
    public criteriumDragging: boolean = false;
    public overCategoryHandleArea: boolean = false;
    public isLoading: boolean = false;
    public isSaving: boolean = false;
    public showClusterDialog: boolean = false;
    public isAddingCategory = false;
    public isAddingCriterium = false;
    public queue = new PQueue({concurrency: 1});

    constructor() {
    }

    async fetchData() {
        this.isLoading = true;
        const result: any = await this.queue.add(() => axios.get('/api/rubrics'));
        const rubric = Rubric.fromJSON(result.data.data);
        Vue.set(this, 'rubric', rubric);
        Vue.set(this, 'selectedTreeNode', rubric);
        Vue.set(this, 'selectedCluster', rubric.clusters.length && rubric.clusters[0] || null);
        Vue.set(this, 'selectedCluster2nd', rubric.clusters.length && rubric.clusters[0] || null);
        Vue.set(this, 'selectedClusterCategories', []);
        Vue.set(this, 'selectedCluster2ndCategories', []);
        Vue.set(this, 'selectedCategoryColorPicker', null);
        Vue.set(this, 'selectedCategoryNewCriterium', null);
        Vue.set(this, 'newCluster', null);
        Vue.set(this, 'draggedClusterView1', null);
        Vue.set(this, 'draggedClusterView2', null);
        Vue.set(this, 'draggedCategoryView1', null);
        Vue.set(this, 'draggedCategoryView2', null);
        Vue.set(this, 'newCategory', null);
        Vue.set(this, 'newCriterium', null);
        Vue.set(this, 'selectedCriterium', null);
        Vue.set(this, 'initiatedDrag', '');
        this.isLoading = false;
    }

    async save() {
        this.isSaving = true;

        (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
    }

    async removeChild(child: TreeNode, parent: TreeNode) {
        this.isSaving = true;
        (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }

    async addChild(child: TreeNode, parent: TreeNode, index: number = 0) {
        this.isSaving = true;
        (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }

    async moveChild(child: TreeNode, parent: TreeNode, newIndex:number) {
        this.isSaving = true;
        (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }

    async removeTreeNode(node:TreeNode){
        this.isSaving = true;
        (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }
}
