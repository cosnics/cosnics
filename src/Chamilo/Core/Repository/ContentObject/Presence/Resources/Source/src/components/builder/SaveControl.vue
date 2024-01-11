<i18n>
{
    "en": {
        "save": "Save presence"
    },
    "nl": {
        "save": "Aanwezigheid opslaan"
    }
}
</i18n>

<template>
    <button :disabled="isSaving || isDisabled" class="btn btn-primary mod-presence-save" @click="$emit('save')">
        <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
        {{ $t('save') }}
    </button>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'save-control'
})
export default class SaveControl extends Vue {
    @Prop({type: Boolean, default: false}) readonly isSaving!: boolean;
    @Prop({type: Boolean, default: false}) readonly isDisabled!: boolean;
}
</script>

<style>
.btn.mod-presence-save {
    font-size: 1.0625rem;
    line-height: 1.6;
}

.btn.mod-presence-save:focus {
    color: #fff;
}

.btn.mod-presence-save > .glyphicon {
    margin-right: 5px;
}
</style>