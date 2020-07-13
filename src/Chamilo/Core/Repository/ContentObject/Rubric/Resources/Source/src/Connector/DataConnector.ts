import axios from 'axios';
import APIConfiguration from './APIConfiguration';
import PQueue from 'p-queue';
import Level from '../Domain/Level';
import TreeNode from '../Domain/TreeNode';
import Choice from '../Domain/Choice';
(window as unknown as any).axios = axios;

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

    addLevel(level: Level, index: number) {
        this.addToQueue(async () => {
            const parameters = {
                'newSort': index + 1,
                'levelData': JSON.stringify({ 'is_default': level.isDefault, ...level })
            };

            const res = await this.executeAPIRequest(this.apiConfiguration.addLevelURL, parameters);
            level.id = String(res.level.id);
        });
    }

    addTreeNode(treeNode: TreeNode, parentTreeNode: TreeNode, index: number) {
        this.addToQueue(async () => {
            const parameters = {
                'treeNodeData': JSON.stringify({ type: treeNode.getType(), ...treeNode }),
                'newParentId': parseInt(parentTreeNode.id),
                'newSort': index + 1
            };

            const res = await this.executeAPIRequest(this.apiConfiguration.addTreeNodeURL, parameters);
            treeNode.id = String(res.tree_node.id);
        });
    }

    deleteLevel(level: Level) {
        this.addToQueue(async () => {
            const parameters = {
                'levelData': JSON.stringify(level)
            };
            await this.executeAPIRequest(this.apiConfiguration.deleteLevelURL, parameters);
        });
    }

    deleteTreeNode(treeNode: TreeNode) {
        this.addToQueue(async () => {
            const parameters = {
                'treeNodeData': JSON.stringify(treeNode)
            };
            await this.executeAPIRequest(this.apiConfiguration.deleteTreeNodeURL, parameters);
        });
    }

    moveLevel(level: Level, newIndex: number) {
        this.addToQueue(async () => {
            const parameters = {
                'levelData': JSON.stringify(level),
                'newSort': newIndex + 1
            };
            await this.executeAPIRequest(this.apiConfiguration.moveLevelURL, parameters);
        });
    }

    moveTreeNode(treeNode: TreeNode, newParentNode: TreeNode, newIndex: number) {
        this.addToQueue(async () => {
            const parameters = {
                'treeNodeData': JSON.stringify(treeNode),
                'newParentId': parseInt(newParentNode.id),
                'newSort': newIndex + 1
            };
            await this.executeAPIRequest(this.apiConfiguration.moveTreeNodeURL, parameters);
        });
    }

    async updateChoice(choice: Choice) {
        const parameters = {
            'choiceData': JSON.stringify(choice),
        }

        const data = await this.executeAPIRequest(this.apiConfiguration.updateChoiceURL, parameters);
        console.log(data);
    }

    updateLevel(level: Level) {
        this.addToQueue(async () => {
            const parameters = {
                'levelData': JSON.stringify(Object.assign({}, level, { 'is_default': level.isDefault}))
            };
            await this.executeAPIRequest(this.apiConfiguration.updateLevelURL, parameters);
        });
    }

    updateTreeNode(treeNode: TreeNode) {
        this.addToQueue(async () => {
            const parameters = {
                'treeNodeData': JSON.stringify({ type: treeNode.getType(), ...treeNode })
            };
            await this.executeAPIRequest(this.apiConfiguration.updateTreeNodeURL, parameters);
        });
    }

    protected addToQueue(callback: Function) {
        this.queue.add(async () => {
            await callback();
        });
        this.queue.onIdle().then(this.endSaving.bind(this));
    }

    protected async executeAPIRequest(apiURL: string, parameters: any) {
        /*function timeout(ms: number) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }*/

        this.beginSaving();
        parameters['rubricDataId'] = this.rubricDataId;
        parameters['version'] = this.currentVersion;
        const formData = new FormData();
        for (const [key, value] of Object.entries(parameters)) {
            formData.set(key, value as any);
        }

        try {
            const res = await axios.post(apiURL, formData);
            console.log('res', res);
            document.getElementById('innerhtml')!.innerHTML = res.data;
            this.rubricDataId = res.data.rubric.id;
            this.currentVersion = res.data.rubric.version;
            /*await timeout(300); // simulate a save
            resolve({});*/
            return res.data;
        } catch (err) {
            console.log('error', err);
            return err;
        }
    }
}
