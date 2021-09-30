<i18n>
{
    "en": {
        "save": "Save"
    },
    "nl": {
        "save": "Opslaan"
    }
}
</i18n>

<template>
    <div>
        <button :disabled="isSaving" class="btn btn-primary mod-presence-save" @click="$emit('save')">
            <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin" style="margin-right: 5px"></div>
            {{ $t('save') }}
        </button>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'save-control'
})
export default class SaveControl extends Vue {
    @Prop({type: Boolean, default: false}) readonly isSaving!: boolean;
}
</script>
