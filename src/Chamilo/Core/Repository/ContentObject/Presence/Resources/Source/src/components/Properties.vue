<i18n>
{
    "en": {
        "error-Timeout": "The server took too long to respond. Your changes have possibly not been saved. You can try again later.",
        "error-LoggedOut": "It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.",
        "error-Unknown": "An unknown error occurred. Your changes have possibly not been saved. You can try again later.",
        "print-qr": "Display / Print QR code for general self registration",
        "self-registration-off": "General self registration OFF",
        "self-registration-on": "General self registration ON"
    },
    "nl": {
        "error-LoggedOut": "Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "print-qr": "Toon / Print QR code voor globale zelfregistratie",
        "self-registration-off": "Globale zelfregistratie UIT",
        "self-registration-on": "Globale zelfregistratie AAN"
    }
}
</i18n>

<template>
    <div v-if="presence">
        <on-off-switch id="disable-selfreg-global-check" switch-class="mod-self-disable" style="margin-bottom: 15px;"
                       :on-text="$t('self-registration-on')" :off-text="$t('self-registration-off')" :checked="!presence.global_self_registration_disabled"
                       @toggle="selfRegistrationChanged"/>
        <a v-if="!presence.global_self_registration_disabled" style="display: block;font-size: 15px;margin-bottom: 15px" :href="apiConfig.printQrCodeURL" target="_blank"><i class="fa fa-print" style="margin-right: 5px;" aria-hidden="true"></i>{{ $t('print-qr') }}</a>
        <error-display v-if="errorData" @close="errorData = null">
            <span v-if="errorData.code === 500">{{ errorData.message }}</span>
            <span v-else-if="!!errorData.type">{{ $t('error-' + errorData.type) }}</span>
        </error-display>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import APIConfig from '../connect/APIConfig';
import Connector, {ConnectorErrorListener} from '../connect/Connector';
import ErrorDisplay from './ErrorDisplay.vue';
import OnOffSwitch from './OnOffSwitch.vue'
import {Presence} from '../types';

@Component({
    name: 'properties',
    components: {OnOffSwitch, ErrorDisplay}
})
export default class Properties extends Vue
{
    presence: Presence | null = null;
    connector: Connector | null = null;
    errorData: string|null = null;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;

    async load(): Promise<void> {
        const presenceData : any = await this.connector?.loadPresence();
        this.presence = presenceData?.presence || null;
    }

    selfRegistrationChanged() {
        if (!this.presence) { return; }
        this.errorData = null;
        const finished = !this.presence.global_self_registration_disabled;
        this.presence.global_self_registration_disabled = finished;
        this.connector?.updatePresenceGlobalSelfRegistration(this.presence.id, finished);
    }

    setError(data: any): void {
        this.errorData = data;
    }

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this as ConnectorErrorListener);
        this.load();
    }
}
</script>