import axios from 'axios';
import APIConfiguration from './APIConfiguration';
import PQueue from 'p-queue';
import Level from '../Domain/Level';
import TreeNode from '../Domain/TreeNode';
import Choice from '../Domain/Choice';

export default class DataConnector {

    protected apiConfiguration: APIConfiguration;
    protected queue = new PQueue({concurrency: 1});

    protected rubricDataId: number;
    protected currentVersion: number;

    private _isSaving: boolean = false;

    public constructor(apiConfiguration: APIConfiguration, rubricDataId: number, currentVersion: number) {
        this.rubricDataId = rubricDataId;
        this.currentVersion = currentVersion;
        this.apiConfiguration = apiConfiguration;
    }

    get processingSize() {
        return this.queue.pending + this.queue.size;
    }

    get isSaving() {
        return this._isSaving;
    }

    private beginSaving() {
        this._isSaving = true;
    }

    private endSaving() {
        this._isSaving = false;
    }

    async addLevel(level: Level, index: number) {
        const parameters = {
            'newSort': index + 1,
            'levelData': JSON.stringify(level)
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.addLevelURL, parameters);
        // level.id = data.level.id;
    }

    async addTreeNode(treeNode: TreeNode, parentTreeNode: TreeNode, index: number) {
        const parameters = {
            'treeNodeData': JSON.stringify(treeNode),
            'newParentId': parentTreeNode.id,
            'newSort': index + 1
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.addTreeNodeURL, parameters);
    }

    async deleteLevel(level: Level) {
        const parameters = {
            'levelData': JSON.stringify(level)
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.deleteLevelURL, parameters);
    }

    async deleteTreeNode(treeNode: TreeNode) {
        const parameters = {
            'treeNodeData': JSON.stringify(treeNode)
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.deleteTreeNodeURL, parameters);
    }

    async moveLevel(level: Level, newIndex: number) {
        const parameters = {
            'levelData': JSON.stringify(level),
            'newSort': newIndex + 1
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.moveLevelURL, parameters);
    }

    async moveTreeNode(treeNode: TreeNode, newParentNode: TreeNode, newIndex: number) {
        const parameters = {
            'treeNodeData': JSON.stringify(treeNode),
            'newParentId': newParentNode.id,
            'newSort': newIndex + 1
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.moveTreeNodeURL, parameters);
    }

    async updateChoice(choice: Choice) {
        const parameters = {
            'choiceData': JSON.stringify(choice),
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.updateChoiceURL, parameters);
    }

    async updateLevel(level: Level) {
        const parameters = {
            'levelData': JSON.stringify(level),
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.updateLevelURL, parameters);
    }

    async updateTreeNode(treeNode: TreeNode) {
        const parameters = {
            'treeNodeData': JSON.stringify(treeNode),
        }
        const data = await this.executeAPIRequest(this.apiConfiguration.updateTreeNodeURL, parameters);
    }

    protected async executeAPIRequest(apiURL: string, parameters: any) {
        return new Promise((resolve, reject) => {

            function timeout(ms: number) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            this.queue.add(async () => {
                this.beginSaving();
                parameters['rubricDataId'] = this.rubricDataId;
                parameters['version'] = this.currentVersion;
                const formData = new FormData();
                for (const [key, value] of Object.entries(parameters)) {
                    formData.set(key, value as any);
                }

                try {
                    /*const res = await axios.post(apiURL, formData);
                    this.rubricDataId = res.data.rubric.id;
                    this.currentVersion = res.data.rubric.version;
                    resolve(res.data);*/
                    await timeout(300); // simulate a save
                    resolve({});
                } catch (err) {
                    reject(err);
                }
            });
            this.queue.onIdle().then(this.endSaving.bind(this));
        });
    }
}
