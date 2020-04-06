<template>
    <div :id="id" @click="$emit('criterium-selected', criterium)" class="criterium" :class="{ selected }">
        {{ criterium.title }}
        <div class="item-actions" :class="{'show-menu': showMenuActions}" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></div>
        <div v-if="showMenuActions" class="action-menu">
            <ul>
                <li @click.stop="startEditing"><i class="fa fa-pencil" />Wijzig naam</li>
                <li @click.stop="$emit('remove', criterium)"><i class="fa fa-remove" />Verwijder</li>
            </ul>
        </div>
        <div v-if="isEditing" class="edit-title">
            <div class="cover"></div>
            <name-input class="item-new" ok-title="Wijzig" @ok="finishEditing" @cancel="cancel" placeholder="Titel voor criterium" v-model="criterium.title"/>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Criterium from '../../Domain/Criterium';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'criterium-view',
        components: { NameInput }
    })
    export default class CriteriumView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: Boolean, required: true}) readonly selected!: boolean;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;

        get showMenuActions() {
            return this.menuActionsId === this.id;
        }

        startEditing() {
            this.isEditing = true;
            this.oldTitle = this.criterium.title;
            this.$emit('start-edit');
        }

        finishEditing() {
            this.isEditing = false;
            this.oldTitle = '';
            this.$emit('finish-edit');
        }

        cancel() {
            this.criterium.title = this.oldTitle;
            this.finishEditing();
        }
    }
</script>