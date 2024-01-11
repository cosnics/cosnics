<template>
    <b-input v-if="isEditable" type="text" required v-model="status.title" autocomplete="off" :disabled="disabled"
             class="mod-input mod-trans mod-pad" @focus="$emit('select')"/>
    <span v-else>{{ statusTitle }}</span>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {PresenceStatus} from '../../types';

@Component({
    name: 'title-control'
})
export default class TitleControl extends Vue {
    @Prop({type: Object, required: true}) readonly status!: PresenceStatus;
    @Prop({type: String, default: ''}) readonly statusTitle!: string;
    @Prop({type: Boolean, default: false}) readonly disabled!: boolean;
    @Prop({type: Boolean, default: true}) readonly isEditable!: boolean;
}
</script>
