import axios from 'axios';
import APIConfig from './APIConfig';
import PQueue from 'p-queue';
import {PresenceStatus} from '../types';

const TIMEOUT_SEC = 30;

export default class Connector {
    private apiConfig: APIConfig;
    private queue = new PQueue({concurrency: 1});

    private _isSaving = false;
    private hasError = false;

    constructor(apiConfig: APIConfig) {
        this.apiConfig = apiConfig;
        this.finishSaving = this.finishSaving.bind(this);
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

    async createResultPeriod(callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const data = await this.executeAPIRequest(this.apiConfig.createPresencePeriodURL);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    // eslint-disable-next-line
    async loadPresenceEntries(params: any) {
        const res = await axios.get(this.apiConfig.loadPresenceEntriesURL, {params});
        return res.data;
    }

    // eslint-disable-next-line
    async loadPresence() {
        const res = await axios.get(this.apiConfig.loadPresenceURL);
        return res.data;
    }

    async updatePresence(id: number, statuses: PresenceStatus[], callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { data: JSON.stringify({id, statuses}) };
            const data = await this.executeAPIRequest(this.apiConfig.updatePresenceURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    async loadSavedEntryStatuses() {
        const res = await axios.get(this.apiConfig.loadSavedEntryStatusesURL);
        return res.data;
    }

    async updatePresencePeriod(periodId: number, label: string, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'period_label': label };
            const data = await this.executeAPIRequest(this.apiConfig.updatePresencePeriodURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    async deletePresencePeriod(periodId: number, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId };
            const data = await this.executeAPIRequest(this.apiConfig.deletePresencePeriodURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    async savePresenceEntry(periodId: number, userId: number, statusId: number, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'user_id': userId, 'status_id': statusId };
            const data = await this.executeAPIRequest(this.apiConfig.savePresenceEntryURL, parameters);
            console.log(data);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    private addToQueue(callback: Function) {
        if (this.hasError) {
            return;
        }
        this.queue.add(async () => {
            await callback();
        });
        this.queue.onIdle().then(this.finishSaving);
    }

    private async executeAPIRequest(apiURL: string, parameters: any = {}) {
        this.beginSaving();

        const formData = new FormData();
        if (this.apiConfig.csrfToken) {
            formData.set('_csrf_token', this.apiConfig.csrfToken);
        }
        for (const [key, value] of Object.entries(parameters)) {
            formData.set(key, value as any);
        }

        try {
            let res;
            try {
                res = await axios.post(apiURL, formData, {timeout: TIMEOUT_SEC * 1000});
            } catch (err) {
                console.log(err);
//          this.logResponse(err);
                throw err;
            }
            console.log(res);
            if (typeof res.data === 'object') {
                //console.log('result', res.data);
                return res.data;
            }

        } catch (err) {
            this.hasError = true;
            console.log(err);
        }
    }
}