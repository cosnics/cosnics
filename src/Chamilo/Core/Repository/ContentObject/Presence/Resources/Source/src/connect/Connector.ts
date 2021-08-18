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

    // eslint-disable-next-line
    async loadStudents() {
        const res = await axios.get(this.apiConfig.loadStudentsURL);
        console.log('loadStudents', res.data);
        return res.data;
    }

    // eslint-disable-next-line
    async loadPresence() {
        const res = await axios.get(this.apiConfig.loadPresenceURL);
        return res.data;
    }

    async updatePresence(id: number, statuses: PresenceStatus[]) {
        this.addToQueue(async () => {
            const parameters = {data: JSON.stringify({id, statuses})};
            const res = await this.executeAPIRequest(this.apiConfig.updatePresenceURL, parameters);
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

    private async executeAPIRequest(apiURL: string, parameters: any) {
        this.beginSaving();

        const formData = new FormData();
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