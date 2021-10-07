<i18n>
{
    "en": {
        "error-NoTitleGiven": "You haven't given a title for the following status:",
        "error-TitleUpdateForbidden": "The title of the status '{title}' can no longer be changed.",
        "error-InvalidType": "The status '{title}' has a wrong type set.",
        "error-PresenceStatusMissing": "The status '{title}' can no longer be removed.",
        "error-InvalidAlias": "The status with title '{title}' has a wrong mapping set.",
        "error-AliasUpdateForbiddene": "The mapping of the status '{title}' can no longer be changed.",
        "error-NoCodeGiven": "You haven't given a label for the status '{title}'.",
        "error-NoColorGiven": "You haven't given a color for the status '{title}'.",
        "error-InvalidColor": "You have given the status '{title}' an invalid color.",
        "error-Timeout": "The server took too long to respond. Your changes have possibly not been saved. You can try again later.",
        "error-LoggedOut": "It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.",
        "error-Unknown": "An unknown error occurred. Your changes have possibly not been saved. You can try again later.",
        "changes-not-saved": "Your changes have not been saved."
    },
    "nl": {
        "error-NoTitleGiven": "Je hebt geen titel opgegeven voor de volgende status:",
        "error-TitleUpdateForbidden": "De titel van de status '{title}' kan niet meer gewijzigd worden.",
        "error-InvalidType": "De status '{title}' status heeft een verkeerd type.",
        "error-PresenceStatusMissing": "De status '{title}' kan niet meer gewist worden.",
        "error-InvalidAlias": "De status '{title}' heeft een verkeerde mapping.",
        "error-AliasUpdateForbidden": "De mapping van de status '{title}' kan niet meer gewijzigd worden.",
        "error-NoCodeGiven": "Je hebt geen label opgegeven voor de status '{title}'.",
        "error-NoColorGiven": "Je hebt geen kleur opgegeven voor de status '{title}'.",
        "error-InvalidColor": "Je hebt de status '{title}' een verkeerde kleur toegewezen.",
        "error-LoggedOut": "Het lijkt erop dat je uitgelogt bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "changes-not-saved": "Je wijzigingen werden niet opgeslagen."
    }
}
</i18n>

<template>
    <div class="alert alert-danger">
        <template v-if="errorData.type === 'NoTitleGiven'">
            {{ $t('error-NoTitleGiven') }} <span class="u-block">{{ errorData.status }}</span> {{ $t('changes-not-saved') }}
        </template>
        <span v-else-if="!!errorData.status">{{ $t(`error-${errorData.type}`, {title: errorData.status.title}) }} {{ $t('changes-not-saved') }}</span>
        <span v-else>{{ $t(`error-${errorData.type}`) }}</span>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'error-display'
})
export default class ErrorDisplay extends Vue {
    @Prop({type: Object, required: true}) readonly errorData!: any;
}
</script>
