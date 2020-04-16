import APIConfiguration from "./APIConfiguration";
import PQueue from "p-queue";
import Level from "../Domain/Level";
import TreeNode from "../Domain/TreeNode";
import Choice from "../Domain/Choice";
import axios from "axios";

export default class DataConnector {

    protected apiConfiguration: APIConfiguration;
    protected queue = new PQueue({concurrency: 1});

    protected rubricDataId: number;
    protected currentVersion: number;

    public isSaving: boolean = false;

    public constructor(apiConfiguration: APIConfiguration, rubricDataId: number, currentVersion: number) {
        this.rubricDataId = rubricDataId;
        this.currentVersion = currentVersion;
        this.apiConfiguration = apiConfiguration;
    }

    async addLevel(level: Level, index: number) {
        let parameters = {
            'newSort': index + 1,
            'levelData': JSON.stringify(level)
        }

        await this.executeAPIRequest(this.apiConfiguration.addLevelURL, parameters);
    }

    async addTreeNode(treeNode: TreeNode, parentTreeNode: TreeNode, index: number) {
        let parameters = {
            'treeNodeData': JSON.stringify(treeNode),
            'newParentId': parentTreeNode.id,
            'newSort': index + 1
        }

        await this.executeAPIRequest(this.apiConfiguration.addTreeNodeURL, parameters);
    }

    async deleteLevel(level: Level) {
        let parameters = {
            'levelData': JSON.stringify(level)
        }

        await this.executeAPIRequest(this.apiConfiguration.deleteLevelURL, parameters);
    }

    async deleteTreeNode(treeNode: TreeNode) {
        let parameters = {
            'treeNodeData': JSON.stringify(treeNode)
        }

        await this.executeAPIRequest(this.apiConfiguration.deleteTreeNodeURL, parameters);
    }

    async moveLevel(level: Level, newIndex: number) {
        let parameters = {
            'levelData': JSON.stringify(level),
            'newSort': newIndex + 1
        }

        await this.executeAPIRequest(this.apiConfiguration.moveLevelURL, parameters);
    }

    async moveTreeNode(treeNode: TreeNode, newParentNode: TreeNode, newIndex: number) {
        let parameters = {
            'treeNodeData': JSON.stringify(treeNode),
            'newParentId': newParentNode.id,
            'newSort': newIndex + 1
        }

        await this.executeAPIRequest(this.apiConfiguration.moveTreeNodeURL, parameters);
    }

    async updateChoice(choice: Choice) {
        let parameters = {
            'choiceData': JSON.stringify(choice),
        }

        await this.executeAPIRequest(this.apiConfiguration.updateChoiceURL, parameters);
    }

    async updateLevel(level: Level) {
        let parameters = {
            'levelData': JSON.stringify(level),
        }

        await this.executeAPIRequest(this.apiConfiguration.updateLevelURL, parameters);
    }

    async updateTreeNode(treeNode: TreeNode) {
        let parameters = {
            'treeNodeData': JSON.stringify(treeNode),
        }

        await this.executeAPIRequest(this.apiConfiguration.updateTreeNodeURL, parameters);
    }

    protected async executeAPIRequest(apiURL: string, parameters: any) {
        this.isSaving = true;

        await (async () => {
            await this.queue.add(() => {
                parameters['rubricDataId'] = this.rubricDataId;
                parameters['version'] = this.currentVersion;

                let formData = new FormData();
                parameters.forEach((value: any, key: string) => {
                    formData.set(key, value);
                })

                axios.post(apiURL, formData, ).then(
                    (value) => {
                        this.rubricDataId = value.data.rubric.id;
                        this.currentVersion = value.data.rubric.version;
                    },
                    (reason: any) => {

                    }
                )
            });
        })();

        this.queue.onIdle().then(() => this.isSaving = false);
    }
}
