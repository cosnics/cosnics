<template>
  <b-table bordered :items="previewStudents" :fields="fields" class="mod-presence mod-entry">
    <template #cell(period)="data">
      <div class="u-flex u-gap-small u-flex-wrap">
        <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code" :class="[status.color, { 'is-selected': data.item.selected === status.id }]" @click="data.item.selected = status.id" :aria-pressed="data.item.selected === status.id ? 'true': 'false'"><span>{{ status.code }}</span></button>
      </div>
    </template>
    <template #head(period-result)="">&nbsp;
    </template>
    <template #cell(period-result)="data">
      <div class="result-wrap">
        <div class="color-code" :class="[getStatusColorForStudent(data.item) || 'mod-none']">
          <span>{{ getStatusCodeForStudent(data.item) }}</span>
        </div>
      </div>
    </template>
  </b-table>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { PresenceStatus } from '../types';

type PreviewStudent = { name: string; selected: number; };

@Component({
  name: 'preview-entry'
})
export default class PreviewEntry extends Vue {
  
  readonly fields = [
    { key: 'name', sortable: false, label: 'Student' },
    { key: 'period', sortable: false, label: 'Preview', variant: 'period' },
    { key: 'period-result', sortable: false, label: '', variant: 'result' }
  ];
  
  @Prop({type: Array, required: true}) readonly presenceStatuses!: PresenceStatus[];
  @Prop({type: Array, required: true}) readonly previewStudents!: PreviewStudent[];
  
  getStatusForStudent(statusId : number) : PresenceStatus|undefined {
    return this.presenceStatuses.find(s => s.id === statusId);
  }
  
  getStatusCodeForStudent(student: PreviewStudent) : string {
    return this.getStatusForStudent(student.selected)?.code || '';
  }

  getStatusColorForStudent(student: PreviewStudent) : string {
    return this.getStatusForStudent(student.selected)?.color || '';
  }
}
</script>
