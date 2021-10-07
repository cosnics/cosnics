<i18n>
{
    "en": {
        "display": "Display"
    },
    "nl": {
        "display": "Weergave"
    }
}
</i18n>

<template>
    <b-tr>
        <b-th class="u-font-normal">{{ $t('display') }}</b-th>
        <b-th colspan="3">
            <div class="u-flex u-gap-small u-flex-wrap" style="padding: 4px 0">
                <div v-for="(status, index) in presenceStatuses" :key="`status-${index}`"
                     class="color-code" :class="[status.color]"><span>{{ status.code }}</span></div>
            </div>
        </b-th>
    </b-tr>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {PresenceStatus} from '../../types';

@Component({
    name: 'selection-preview'
})
export default class SelectionPreview extends Vue {
    @Prop({type: Array, default: () => []}) readonly presenceStatuses!: PresenceStatus[];
}
</script>
