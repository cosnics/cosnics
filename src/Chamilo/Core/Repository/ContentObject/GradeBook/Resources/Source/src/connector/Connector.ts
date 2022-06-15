import axios from 'axios';
import APIConfig from './APIConfig';
import PQueue from 'p-queue';

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

    async loadAllData(params: any = undefined) {
        const res = await axios.get(this.apiConfig.loadAllURL, {params});
        return res.data;
    }
}