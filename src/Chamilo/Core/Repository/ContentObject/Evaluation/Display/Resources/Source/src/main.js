import axios from 'axios';
import PQueue from 'p-queue';

const TIMEOUT_SEC = 30;

class DataConnector {

    constructor(apiConfiguration, csrf_token) {
        this.apiConfiguration = apiConfiguration;
        this.csrf_token = csrf_token;
        this.queue = new PQueue({concurrency: 1});
        this.errorListeners = [];
        this._isSaving = false;
        this.hasError = false;
    }

    addErrorListener(errorListener) {
        this.errorListeners.push(errorListener);
    }

    removeErrorListener(errorListener) {
        const index = this.errorListeners.indexOf(errorListener);
        if (index >= 0) {
            this.errorListeners.splice(index, 1);
        }
    }

    processingSize() {
        return this.queue.pending + this.queue.size;
    }

    isSaving() {
        return this._isSaving;
    }

    beginSaving() {
        this._isSaving = true;
    }

    endSaving() {
        this._isSaving = false;
    }

    updateScore(entityId, score, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'entity_id': entityId,
                'score': score
            };
            const res = await this.executeAPIPost(this.apiConfiguration.updateScoreURL, parameters);
            if (!this.hasError && callback) { callback(res); }
        });
    }

    createFeedback(entityId, comment, isPrivate, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'entity_id': entityId,
                'comment': comment,
                'is_private': isPrivate
            };
            const res = await this.executeAPIPost(this.apiConfiguration.saveNewFeedbackURL, parameters);
            if (!this.hasError && callback) { callback(res); }
        });
    }

    savePresenceStatus(entityId, presenceStatus, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'entity_id': entityId,
                'presence_status': presenceStatus
            };
            const res = await this.executeAPIPost(this.apiConfiguration.savePresenceStatusURL, parameters);
            if (!this.hasError && callback) { callback(res); }
        });
    }

    toggleOpenForStudents(isOpen, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'open_for_students': isOpen
            };
            const res = await this.executeAPIPost(this.apiConfiguration.toggleOpenForStudentsURL, parameters);
            if (!this.hasError && callback) { callback(res); }
        });
    }

    toggleRubricSelfEvaluation(selfEvaluationAllowed, callback) {
        this.addToQueue(async () => {
            const parameters = {
                'self_evaluation_allowed': selfEvaluationAllowed
            };
            const res = await this.executeAPIPost(this.apiConfiguration.toggleRubricSelfEvaluationURL, parameters);
            if (!this.hasError && callback) { callback(res); }
        });
    }

    loadEntities(parameters, callback) {
        (async () => {
            const res = await this.executeAPIGet(this.apiConfiguration.loadEntitiesURL, parameters);
            if (callback) { callback(res); }
        })();
    }

    loadFeedback(entityId, callback) {
        (async () => {
            const res = await this.executeAPIGet(this.apiConfiguration.loadFeedbackURL, { entity_id: entityId });
            if (callback) { callback(res); }
        })();
    }

    addToQueue(callback) {
        if (this.hasError) { return; }
        this.queue.add(async () => {
            await callback();
        });
        this.queue.onIdle().then(this.endSaving.bind(this));
    }

    async executeAPIGet(apiURL, parameters) {
        return await this.executeAPIRequest(apiURL, 'get', parameters);
    }

    async executeAPIPost(apiURL, parameters) {
        if (this.hasError) { return; }
        this.beginSaving();
        const formData = new FormData();
        if (this.csrf_token) {
            formData.set('_csrf_token', this.csrf_token);
        }
        for (const [key, value] of Object.entries(parameters)) {
            formData.set(key, value);
        }
        return await this.executeAPIRequest(apiURL, 'post', null, formData);
    }

    async executeAPIRequest(apiURL, type, parameters, formData) {
        try {
            let res;
            if (type === 'post') {
                res = await axios.post(apiURL, formData, {timeout: TIMEOUT_SEC * 1000});
            } else {
                res = await axios.get(apiURL, { params: parameters, timeout: TIMEOUT_SEC * 1000 });
            }
            if (typeof res.data === 'object') {
                if (res.data.result_code !== 200 || typeof res.data.properties !== 'object') {
                    throw { 'code': 'unknown' };
                } else {
                    return res.data.properties;
                }
            } else if (typeof res.data === 'string' && res.data.indexOf('formLogin') !== -1) {
                throw { 'code': 'logged_out' };
            } else {
                throw { 'code': 'unknown' };
            }
        } catch (err) {
            let code;
            if (err.isAxiosError && err.message && err.message.toLowerCase().indexOf('timeout') !== -1) {
                code = 'timeout';
            } else {
                code = err.code || 'unknown';
            }
            this.hasError = true;
            this.errorListeners.forEach(errorListener => errorListener.setError(code, type));
            return err;
        }
    }
}

export {DataConnector};
