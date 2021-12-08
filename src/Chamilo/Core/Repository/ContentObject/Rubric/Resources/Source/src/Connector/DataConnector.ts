import axios from 'axios';
import APIConfiguration from './APIConfiguration';
import PQueue from 'p-queue';
import Rubric from '../Domain/Rubric';
import Level from '../Domain/Level';
import TreeNode from '../Domain/TreeNode';
import Criterium from '../Domain/Criterium';
import Choice from '../Domain/Choice';

const HTTP_FORBIDDEN = 403;
const HTTP_NOT_FOUND = 404;
const HTTP_CONFLICT = 409;
const ERROR_UNKNOWN = 'UNKNOWN';

const TIMEOUT_SEC = 30;

function timeout(ms: number) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

export interface DataConnectorErrorListener {
    setError(code: string) : void;
}

export default class DataConnector {

    protected apiConfiguration: APIConfiguration;
    protected queue = new PQueue({concurrency: 1});

    protected rubricDataId: number;
    protected currentVersion: number|null;

    private rubric: Rubric;
    private _isSaving: boolean = false;
    private hasError: boolean = false;

    private errorListeners: DataConnectorErrorListener[] = [];

    public constructor(rubric: Rubric, apiConfiguration: APIConfiguration, rubricDataId: number, currentVersion: number|null) {
        this.rubric = rubric;
        this.rubricDataId = rubricDataId;
        this.currentVersion = currentVersion;
        this.apiConfiguration = apiConfiguration;
    }

    addErrorListener(errorListener: DataConnectorErrorListener) {
        this.errorListeners.push(errorListener);
    }

    removeErrorListener(errorListener: DataConnectorErrorListener) {
        const index = this.errorListeners.indexOf(errorListener);
        if (index >= 0) {
            this.errorListeners.splice(index, 1);
        }
    }

    get isDummyRequest() {
        return this.currentVersion === null;
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

    updateRubric(rubric: Rubric) {
        this.addToQueue(async () => {
            const parameters = {
                'rubricData': JSON.stringify({ 'use_scores': rubric.useScores, 'use_relative_weights': rubric.useRelativeWeights })
            };
            await this.executeAPIRequest(this.apiConfiguration.updateRubricURL, parameters);
        });
    }

    addLevel(level: Level, index: number) {
        this.addToQueue(async () => {
            const parameters = {
                'newSort': index + 1,
                'levelData': JSON.stringify(level)
            };

            const res = await this.executeAPIRequest(this.apiConfiguration.addLevelURL, parameters);

            if (this.isDummyRequest || this.hasError) { return; }

            const oldId = level.id;
            level.id = String(res.level.id);
            this.rubric.setChoicesLevelId(oldId, level.id);
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

            if (this.isDummyRequest || this.hasError) { return; }

            const oldId = treeNode.id;
            treeNode.id = String(res.tree_node.id);
            if (res.tree_node.type === 'criterium') {
                this.rubric.setChoicesCriteriumId(oldId, treeNode.id);
            }
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

    updateChoice(choice: Choice, criterium: Criterium, level: Level) {
        this.addToQueue(async () => {
            const parameters = {
                'choiceData': JSON.stringify(choice.toJSON(criterium.id, level.id)),
            };
            await this.executeAPIRequest(this.apiConfiguration.updateChoiceURL, parameters);
        });
    }

    updateLevel(level: Level) {
        this.addToQueue(async () => {
            const parameters = {
                'levelData': JSON.stringify(level)
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
        if (this.hasError) { return; }
        this.queue.add(async () => {
            await callback();
        });
        this.queue.onIdle().then(this.endSaving.bind(this));
    }

    logResponse(data: any) {
        const responseEl = document.getElementById('server-response');
        if (responseEl) {
            if (typeof data === 'object') {
                responseEl.innerHTML = JSON.stringify(data, null, 4);
            } else {
                responseEl.innerHTML = `<div>An error occurred:</div>${data}`;
            }
        }
    }

    protected async executeAPIRequest(apiURL: string, parameters: any) {

        this.beginSaving();
        if (this.isDummyRequest) {
            await timeout(300); // Simulate a save
            return {};
        } else {
            parameters['rubricDataId'] = this.rubricDataId;
            parameters['version'] = this.currentVersion;

            const formData = new FormData();
            for (const [key, value] of Object.entries(parameters)) {
                formData.set(key, value as any);
            }

            try {
                let res;
                try {
                    res = await axios.post(apiURL, formData, { timeout: TIMEOUT_SEC * 1000 });
                } catch (err) {
                    this.logResponse(err);
                    throw err;
                }
                this.logResponse(res.data);
                if (typeof res.data === 'object') {
                    if (res.data.error) {
                        throw res.data.error;
                    } else {
                        this.rubricDataId = res.data.rubric.id;
                        this.currentVersion = res.data.rubric.version;
                        return res.data;
                    }
                } else {
                    throw { 'code': ERROR_UNKNOWN };
                }
            } catch (err) {
                let code: string;
                if (err.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
                    code = 'timeout';
                } else if ([HTTP_FORBIDDEN, HTTP_NOT_FOUND, HTTP_CONFLICT, ERROR_UNKNOWN].includes(err.code)) {
                    if (err.code === HTTP_FORBIDDEN) {
                        code = 'forbidden';
                    } else if (err.code === HTTP_NOT_FOUND) {
                        code = 'notfound';
                    } else if (err.code === HTTP_CONFLICT) {
                        code = 'conflict';
                    } else if (err.code === ERROR_UNKNOWN) {
                        code = 'unknown';
                    }
                } else {
                    code = 'unknown';
                }
                this.hasError = true;
                this.errorListeners.forEach(errorListener => errorListener.setError(code));
                return err;
            }
        }
    }
}
