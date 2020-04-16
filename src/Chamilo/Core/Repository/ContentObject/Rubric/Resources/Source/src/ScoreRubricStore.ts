import Rubric from "./Domain/Rubric";
import TreeNode from "./Domain/TreeNode";

import axios from "axios";
import Vue from "vue";
import PQueue from "p-queue";

export default class ScoreRubricStore {
    public rubric!: Rubric;
    public useScore: boolean = true;
    public selectedTreeNode!: TreeNode;
    public isLoading: boolean = false;
    public isSaving: boolean = false;
    public queue = new PQueue({concurrency: 1});

    constructor() {
    }

    async fetchData() {
        this.isLoading = true;
        const result: any = await this.queue.add(() => axios.get('/api/rubrics'));
        const rubric = Rubric.fromJSON(result.data.data);
        Vue.set(this, 'rubric', rubric);
        Vue.set(this, 'selectedTreeNode', rubric);
        this.isLoading = false;
    }

    async save() {
        this.isSaving = true;

        await (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
    }

    async removeChild(child: TreeNode, parent: TreeNode) {
        this.isSaving = true;
        await (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }

    async addChild(child: TreeNode, parent: TreeNode, index: number = 0) {
        this.isSaving = true;
        await (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }

    async moveChild(child: TreeNode, parent: TreeNode, newIndex:number) {
        this.isSaving = true;
        await (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }

    async removeTreeNode(node:TreeNode){
        this.isSaving = true;
        await (async () => {
            await this.queue.add(() => axios.get('/api/save'));
        })();
        this.queue.onIdle().then(() => this.isSaving = false);
    }
}
