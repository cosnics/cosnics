<i18n>
{
    "en": {
        "display": "Display",
        "move-up": "Move up",
        "move-down": "Move down",
        "remove": "Remove",
        "save": "Save",
        "cancel": "Cancel",
        "new-presence-status": "New presence status",
        "label": "Label",
        "title": "Title",
        "meaning": "Maps to",
        "color": "Color"
    },
    "nl": {
        "display": "Weergave",
        "move-up": "Verplaats naar boven",
        "move-down": "Verplaats naar beneden",
        "remove": "Verwijder",
        "save": "Opslaan",
        "cancel": "Annuleren",
        "new-presence-status": "Nieuwe aanwezigheidsstatus",
        "label": "Label",
        "title": "Titel",
        "meaning": "Mapt naar",
        "color": "Kleur"
    }
}
</i18n>

<template>
    <div @click.stop="selectedStatus = null">
        <b-table bordered :foot-clone="createNew" :items="presenceStatuses" :fields="fields"
                 class="mod-presence mod-builder" :class="{'is-changes-disabled': createNew}"
                 :tbody-tr-class="rowClass">
            <template #thead-top="">
                <b-tr>
                    <b-th style="background: white;font-weight: 400">{{ $t('display') }}</b-th>
                    <b-th colspan="3" style="background: white">
                        <div class="u-flex u-gap-small u-flex-wrap" style="padding: 4px 0">
                            <div v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code"
                                 :class="[status.color]"><span>{{ status.code }}</span></div>
                        </div>
                    </b-th>
                </b-tr>
            </template>
            <template #cell(code)="status">
                <div class="cell-pad" @click.stop="onSelectStatus(status.item)">
                    <b-input type="text" v-model="status.item.code" autocomplete="off" :disabled="createNew"
                             class="mod-input mod-pad mod-small" @focus="onSelectStatus(status.item)"/>
                </div>
            </template>
            <template #cell(title)="status">
                <div class="cell-pad" @click.stop="onSelectStatus(status.item)">
                    <template v-if="status.item.type === 'fixed' || status.item.type === 'semifixed'"><span
                        style="line-height: 26px">{{ getStatusDefault(status.item).title }}</span></template>
                    <b-input v-else type="text" v-model="status.item.title" autocomplete="off" :disabled="createNew"
                             class="mod-input mod-pad" @focus="onSelectStatus(status.item)"/>
                </div>
            </template>
            <template #head(code)>{{ $t('label') }}</template>
            <template #head(title)>{{ $t('title') }}</template>
            <template #head(meaning)>{{ $t('meaning') }}</template>
            <template #head(color)>{{ $t('color') }}</template>
            <template #cell(meaning)="status">
                <div class="cell-pad" style="line-height: 26px" @click.stop="onSelectStatus(status.item)">
                    <span v-if="status.item.type === 'fixed' || status.item.type === 'semifixed'">{{
                            getStatusDefault(status.item, true).title
                        }}</span>
                    <select v-else class="form-control mod-select" :disabled="createNew"
                            @focus="onSelectStatus(status.item)" v-model="status.item.aliasses">
                        <option v-for="(statusDefault, index) in fixedStatusDefaults" :key="`fs-${index}`"
                                :value="statusDefault.id">{{ statusDefault.title }}
                        </option>
                    </select>
                </div>
            </template>
            <template #cell(color)="status">
                <div class="u-flex cell-pad" style="align-items: center; height: 42px;"
                     @click.stop="onSelectStatus(status.item)">
                    <button :id="`color-${status.index}`" class="color"
                            :class="[{'is-selected': status.item === selectedStatus}, status.item.color]"
                            :disabled="createNew" @focus="onSelectStatus(status.item)"></button>
                    <color-picker :target="`color-${status.index}`" triggers="click blur" placement="right"
                                  :selected-color="status.item.color"
                                  @color-selected="setColorForItem(status.item, $event)"></color-picker>
                </div>
            </template>
            <template #cell(actions)="status">
                <div class="cell-pad-x">
                    <div class="u-flex u-gap-small presence-actions">
                        <button class="btn btn-default btn-sm mod-presence" :title="$t('move-up')"
                                :disabled="createNew || status.index === 0" @click.stop="onMoveUp(status)"
                                :id="`btn-up-${status.index}`" @focus="onSelectStatus(status.item)">
                            <i class="fa fa-arrow-up" aria-hidden="true"></i>
                            <span class="sr-only">{{ $t('move-up') }}</span>
                        </button>
                        <button class="btn btn-default btn-sm mod-presence" :title="$t('move-down')"
                                :disabled="createNew || status.index >= presenceStatuses.length - 1"
                                @click.stop="onMoveDown(status)" :id="`btn-down-${status.index}`"
                                @focus="onSelectStatus(status.item)">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                            <span class="sr-only">{{ $t('move-down') }}</span>
                        </button>
                        <button :title="$t('remove')" :disabled="createNew || status.item.type === 'fixed'"
                                class="btn btn-default btn-sm mod-presence" @click.stop="$emit('remove', status.item)"
                                @focus="onSelectStatus(status.item)">
                            <i class="fa fa-minus-circle" aria-hidden="true"></i>
                            <span class="sr-only">{{ $t('remove') }}</span>
                        </button>
                    </div>
                </div>
            </template>
            <template #foot(code)="">
                <input type="text" class="form-control mod-input mod-pad mod-small" id="new-presence-code"
                       v-model="codeNew"/>
            </template>
            <template #foot(title)="">
                <b-input type="text" class="mod-input mod-pad" v-model="titleNew"/>
            </template>
            <template #foot(meaning)="">
                <select class="form-control mod-select" v-model="aliasNew">
                    <option v-for="(statusDefault, index) in fixedStatusDefaults" :key="`fs-${index}`"
                            :value="statusDefault.id">{{ statusDefault.title }}
                    </option>
                </select>
            </template>
            <template #foot(color)="">
                <div class="u-flex">
                    <button class="color" :class="colorNew" id="color-new"></button>
                    <color-picker target="color-new" triggers="click blur" placement="right" :selected-color="colorNew"
                                  @color-selected="colorNew = $event"></color-picker>
                </div>
            </template>
            <template #foot(actions)="">
                <div class="u-flex u-gap-small presence-actions">
                    <button class="btn btn-default btn-sm mod-presence" :title="$t('save')" @click.stop="onSaveNew"
                            :disabled="!(codeNew && titleNew && aliasNew > 0)">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                        <span class="sr-only">{{ $t('save') }}</span>
                    </button>
                    <button class="btn btn-default btn-sm mod-presence mod-cancel" :title="$t('cancel')"
                            @click.stop="onCancelNew">
                        <i class="fa fa-minus-circle" aria-hidden="true"></i>
                        <span class="sr-only">{{ $t('cancel') }}</span>
                    </button>
                </div>
            </template>
        </b-table>
        <div style="margin: 8px 0 0 8px" v-if="!createNew">
            <a class="presence-new" @click="onCreateNew"><i class="fa fa-plus" aria-hidden="true"></i>
                {{ $t('new-presence-status') }}</a>
        </div>
        <div style="margin: 16px 0 0 8px" v-if="!createNew">
            <button class="btn btn-primary mod-presence-save" @click="$emit('save')">{{ $t('save') }}</button>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { PresenceStatusDefault, PresenceStatus } from '../types';
import ColorPicker from './ColorPicker.vue';

const DEFAULT_COLOR_NEW = 'yellow-100';

@Component({
  name: 'builder',
  components: { ColorPicker }
})
export default class Builder extends Vue {
  readonly fields = [
    { key: 'code', sortable: false },
    { key: 'title', sortable: false },
    { key: 'meaning', sortable: false },
    { key: 'color', sortable: false },
    { key: 'actions', sortable: false, label: '', variant: 'actions' }
  ];
  
  createNew = false;
  codeNew = '';
  titleNew = '';
  aliasNew = 3;
  colorNew = DEFAULT_COLOR_NEW;
  
  selectedStatus: PresenceStatus|null = null;
  
  @Prop({type: Array, required: true}) readonly presenceStatuses!: PresenceStatus[];
  @Prop({type: Array, required: true}) readonly statusDefaults!: PresenceStatusDefault[];
  
  get fixedStatusDefaults(): PresenceStatusDefault[] {
    return this.statusDefaults.filter((s : PresenceStatusDefault) => s.type === 'fixed');
  }
  
  getStatusDefault(status: PresenceStatus, fixed = false): PresenceStatusDefault {
    const statusDefault = this.statusDefaults.find(s => s.id === status.id)!;
    if (!fixed) { return statusDefault; }
    return statusDefault.type === 'fixed' ? statusDefault : this.statusDefaults.find(s => s.id === statusDefault.aliasses)!;
  }
  
  onSelectStatus(status: PresenceStatus) {
    if (!this.createNew) {
      this.selectedStatus = status;
    }
  }
  
  onCreateNew() {
    this.createNew = true;
    this.selectedStatus = null;
    this.$nextTick(() => {
      document.getElementById('new-presence-code')?.focus();
    });
  }
  
  onSaveNew() {
    this.$emit('create', { code: this.codeNew, title: this.titleNew, aliasses: this.aliasNew, color: this.colorNew });
    this.resetNew();
    this.$nextTick(() => {
      this.selectedStatus = this.presenceStatuses[this.presenceStatuses.length - 1];
    });
  }
  
  onCancelNew() {
    this.resetNew();
  }
  
  onMoveDown(status: any) {
    this.$emit('move-down', status.index);
    this.$nextTick(() => {
      let el : HTMLButtonElement|null = document.querySelector(`#btn-down-${status.index + 1}`);
      if (el?.disabled) {
        el = el?.previousSibling as HTMLButtonElement;
      }
      el?.focus();
    });
  }

  onMoveUp(status: any) {
    this.$emit('move-up', status.index);
    this.$nextTick(() => {
      let el : HTMLButtonElement|null = document.querySelector(`#btn-up-${status.index - 1}`);
      if (el?.disabled) {
        el = el?.nextSibling as HTMLButtonElement;
      }
      el?.focus();
    });
  }
  
  resetNew() {
    this.createNew = false;
    this.codeNew = '';
    this.titleNew = '';
    this.aliasNew = 3;
    this.colorNew = DEFAULT_COLOR_NEW;
  }
  
  setColorForItem(item: PresenceStatus, color: string) {
    if (item.color !== color) {
      item.color = color;
    }
  }
  
  rowClass(item: PresenceStatus) : string {
    if (item === this.selectedStatus) { return 'is-selected'; }
    return '';
  }
}
</script>
