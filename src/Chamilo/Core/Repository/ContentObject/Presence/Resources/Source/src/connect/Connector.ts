import axios from 'axios';
import APIConfig from './APIConfig';
import PQueue from 'p-queue';
import {PresenceStatus} from '../types';

const TIMEOUT_SEC = 30;

export interface ConnectorErrorListener {
    setError(data: any) : void;
}

export default class Connector {
    private apiConfig: APIConfig;
    private queue = new PQueue({concurrency: 1});

    private _isSaving = false;
    private errorListeners: ConnectorErrorListener[] = [];

    constructor(apiConfig: APIConfig) {
        this.apiConfig = apiConfig;
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

    async updatePresence(id: number, statuses: PresenceStatus[], has_checkout: boolean, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { data: JSON.stringify({id, statuses, has_checkout}) };
            const data = await this.executeAPIRequest(this.apiConfig.updatePresenceURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    async loadRegisteredPresenceEntryStatuses() {
        const res = await axios.get(this.apiConfig.loadRegisteredPresenceEntryStatusesURL);
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
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    async bulkSavePresenceEntries(periodId: number, statusId: number, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'status_id': statusId };
            const data = await this.executeAPIRequest(this.apiConfig.bulkSavePresenceEntriesURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    async togglePresenceEntryCheckout(periodId: number, userId: number, callback: Function|undefined = undefined) {
        this.addToQueue(async () => {
            const parameters = { 'period_id': periodId, 'user_id': userId };
            const data = await this.executeAPIRequest(this.apiConfig.togglePresenceEntryCheckoutURL, parameters);
            if (callback) {
                callback(data);
            }
            return data;
        });
    }

    private addToQueue(callback: Function) {
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
            const res = await axios.post(apiURL, formData, {timeout: TIMEOUT_SEC * 1000});
            if (typeof res.data === 'object') {
                return res.data;
            } else if (typeof res.data === 'string' && res.data.indexOf('formLogin') !== -1) {
                throw { 'type': 'LoggedOut' };
            } else {
                throw { 'type': 'Unknown' };
            }
        } catch (err) {
            let error: any;
            if (err?.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
                error = { 'type': 'Timeout' };
            } else if (!!err?.response?.data?.error) {
                error = err.response.data.error;
            } else if (!!err?.type) {
                error = err;
            } else {
                error = { 'type': 'Unknown' };
            }
            this.errorListeners.forEach(errorListener => errorListener.setError(error));
        }
    }
}