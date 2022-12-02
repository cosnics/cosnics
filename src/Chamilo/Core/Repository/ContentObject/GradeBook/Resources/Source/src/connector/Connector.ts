import axios from 'axios';
import APIConfig from './APIConfig';
import PQueue from 'p-queue';
import {Category, ColumnId, GradeColumn, GradeItem, GradeScore, ItemId} from '../domain/GradeBook';
import {logResponse} from '../domain/Log';

const HTTP_FORBIDDEN = 403;
const HTTP_NOT_FOUND = 404;
const HTTP_CONFLICT = 409;
const ERROR_UNKNOWN = 'UNKNOWN';

const TIMEOUT_SEC = 30;

function timeout(ms: number) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

export interface ConnectorErrorListener {
    setError(data: any) : void;
}

export default class Connector {
    private apiConfig: APIConfig;
    private queue = new PQueue({concurrency: 1});

    private gradebookDataId: number;
    private currentVersion: number|null;

    private _isSaving = false;
    private errorListeners: ConnectorErrorListener[] = [];

    constructor(apiConfig: APIConfig, gradebookDataId: number, currentVersion: number|null) {
        this.apiConfig = apiConfig;
        this.gradebookDataId = gradebookDataId;
        this.currentVersion = currentVersion;

        this.finishSaving = this.finishSaving.bind(this);
    }

    addErrorListener(errorListener: ConnectorErrorListener) {
        this.errorListeners.push(errorListener);
    }

    removeErrorListener(errorListener: ConnectorErrorListener) {
        const index = this.errorListeners.indexOf(errorListener);
        if (index >= 0) {
            this.errorListeners.splice(index, 1);
        }
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

    private finishSaving(): void {
        this._isSaving = false;
    }

    static async loadGradeBookData(loadAllURL: string, csrfToken: string|undefined) {
        const params = csrfToken ? {'_csrf_token': csrfToken } : {};
        const res = await axios.get(loadAllURL, {params});
        return res.data;
    }

    addCategory(category: Category, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category)
            };
            const data = await this.executeAPIRequest(this.apiConfig.addCategoryURL, parameters);
            callback(data.category);
        });
    }

    updateCategory(category: Category, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category)
            };
            await this.executeAPIRequest(this.apiConfig.updateCategoryURL, parameters);
            if (callback) { callback(); }
        });
    }

    moveCategory(category: Category, newIndex: number, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category),
                'newSort': newIndex + 1
            };
            await this.executeAPIRequest(this.apiConfig.moveCategoryURL, parameters);
            if (callback) { callback(); }
        });
    }

    removeCategory(category: Category, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'categoryData': JSON.stringify(category)
            };
            await this.executeAPIRequest(this.apiConfig.removeCategoryURL, parameters);
            if (callback) { callback(); }
        });
    }

    addGradeColumn(gradeColumn: GradeColumn, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnData': JSON.stringify(gradeColumn)
            };
            const data = await this.executeAPIRequest(this.apiConfig.addColumnURL, parameters);
            callback(data.column, data.scores);
        });
    }

    addColumnSubItem(gradeColumnId: ColumnId, gradeItemId: ItemId, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumnId,
                'gradeItemId': gradeItemId
            };
            const data = await this.executeAPIRequest(this.apiConfig.addColumnSubItemURL, parameters);
            callback(data.column, data.scores);
        });
    }

    removeColumnSubItem(gradeColumnId: ColumnId, gradeItemId: ItemId, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumnId,
                'gradeItemId': gradeItemId
            };
            const data = await this.executeAPIRequest(this.apiConfig.removeColumnSubItemURL, parameters);
            callback(data.column, data.scores);
        });
    }

    updateGradeColumn(gradeColumn: GradeColumn, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnData': JSON.stringify(gradeColumn)
            };
            await this.executeAPIRequest(this.apiConfig.updateColumnURL, parameters);
            if (callback) { callback(); }
        });
    }

    updateGradeColumnCategory(gradeColumn: GradeColumn, categoryId: number|null, callback: Function|undefined = undefined)
    {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumn.id,
                'categoryId': categoryId
            };
            await this.executeAPIRequest(this.apiConfig.updateColumnCategoryURL, parameters);
            if (callback) { callback(); }
        });
    }

    moveGradeColumn(gradeColumn: GradeColumn, newIndex: number, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumn.id,
                'newSort': newIndex + 1
            };
            await this.executeAPIRequest(this.apiConfig.moveColumnURL, parameters);
            if (callback) { callback(); }
        });
    }

    removeGradeColumn(gradeColumn: GradeColumn, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeColumnId': gradeColumn.id
            };
            await this.executeAPIRequest(this.apiConfig.removeColumnURL, parameters);
            if (callback) { callback(); }
        });
    }

    synchronizeGradeBook(callback: Function) {
        this.addToQueue(async () => {
            const data = await this.executeAPIRequest(this.apiConfig.synchronizeGradeBookURL);
            callback(data.scores);
        });
/*        return new Promise(resolve => {
            this.addToQueue(async () => {
                const data = await this.executeAPIRequest(this.apiConfig.synchronizeGradeBookURL);
                resolve(data);
            });
        })*/
    }

    overwriteGradeResult(result: GradeScore, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeScoreId': result.id,
                'newScore': result.newScore,
                'newScoreAuthAbsent': result.newScoreAuthAbsent
            };
            const data = await this.executeAPIRequest(this.apiConfig.overwriteScoreURL, parameters);
            callback(data.score);
        });
    }

    revertOverwrittenGradeResult(result: GradeScore, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeScoreId': result.id
            };
            const data = await this.executeAPIRequest(this.apiConfig.revertOverwrittenScoreURL, parameters);
            callback(data.score);
        });
    }

    updateGradeResultComment(result: GradeScore, callback: Function) {
        this.addToQueue(async () => {
            const parameters = {
                'gradeScoreId': result.id,
                'comment': result.comment
            };
            const data = await this.executeAPIRequest(this.apiConfig.updateScoreCommentURL, parameters);
            callback(data.score);
        });
    }

    calculateTotalScores(callback: Function) {
        this.addToQueue(async () => {
            const data = await this.executeAPIRequest(this.apiConfig.calculateTotalScoresURL);
            callback(data.totalScores);
        });
    }

    protected addToQueue(callback: Function) {
        this.queue.add(async () => {
            await callback();
        });
        this.queue.onIdle().then(this.finishSaving);
    }

    private async executeAPIRequest(apiURL: string, parameters: any = {}) {
        this.beginSaving();

        parameters['gradebookDataId'] = this.gradebookDataId;
        parameters['version'] = this.currentVersion;

        const formData = new FormData();
        if (this.apiConfig.csrfToken) {
            formData.set('_csrf_token', this.apiConfig.csrfToken);
        }
        for (const [key, value] of Object.entries(parameters)) {
            formData.set(key, value as any);
        }

        try {
            const res = await axios.post(apiURL, formData, {timeout: TIMEOUT_SEC * 1000});
            logResponse(res.data);
            if (typeof res.data === 'object') {
                this.gradebookDataId = res.data.gradebook.dataId;
                this.currentVersion = res.data.gradebook.version;
                return res.data;
            } else if (typeof (res.data as unknown) === 'string' && res.data.indexOf('login') !== -1) {
                throw { 'type': 'LoggedOut' };
            } else {
                throw { 'type': 'Unknown' };
            }
        } catch (err) {
            logResponse(err);
            let error: any;
            if (err?.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
                error = { 'type': 'Timeout' };
            } else if ([HTTP_FORBIDDEN, HTTP_NOT_FOUND, HTTP_CONFLICT, ERROR_UNKNOWN].includes(err?.response?.status)) {
                const status = err.response.status;
                if (status === HTTP_FORBIDDEN) {
                    error = { 'type': 'Forbidden' };
                } else if (status === HTTP_NOT_FOUND) {
                    error = { 'type': 'NotFound' };
                } else if (status === HTTP_CONFLICT) {
                    error = { 'type': 'Conflict' };
                } else {
                    error = { 'type': 'Unknown' };
                }
            } else if (err?.response?.data?.error) {
                error = err.response.data.error;
            } else if (err?.type) {
                error = err;
            } else {
                error = { 'type': 'Unknown' };
            }

            this.errorListeners.forEach(errorListener => errorListener.setError(error));
        }
    }
}