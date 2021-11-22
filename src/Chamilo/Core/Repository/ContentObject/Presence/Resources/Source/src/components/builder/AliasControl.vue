<template>
    <select v-if="isEditable" class="form-control mod-trans mod-select" :disabled="isSelectDisabled"
            @focus="$emit('select')" v-model="status.aliasses">
        <option v-for="(statusDefault, index) in fixedStatusDefaults" :key="`fs-${index}`" :value="statusDefault.id">{{ statusDefault.title }}</option>
    </select>
    <template v-else>{{ aliasTitle }}</template>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {PresenceStatusDefault} from '../../types';

@Component({
    name: 'alias-control'
})
export default class AliasControl extends Vue {
    @Prop({type: Array, default: () => []}) readonly fixedStatusDefaults!: PresenceStatusDefault[];
    @Prop({type: Object, required: true}) readonly status!: any;
    @Prop({type: String, default: ''}) readonly aliasTitle!: string;
    @Prop({type: Boolean, default: false}) readonly isSelectDisabled!: boolean;
    @Prop({type: Boolean, default: true}) readonly isEditable!: boolean;
}
</script>
