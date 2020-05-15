<template>
    <li :id="id" class="rb-criterium-list-item handle criterium-handle">
        <div class="rb-criterium" :class="{ selected }">
            <div class="item-header-bar">
                <div @click="$emit('criterium-selected', criterium)" class="rb-criterium-title">
                    <h3 class="title">{{ criterium.title }}</h3>
                </div>
                <div class="item-actions" :class="{'show-menu': showMenuActions}" @click.stop="$emit('item-actions', id)"><i :class="showMenuActions ? 'fa fa-close' : 'fa fa-ellipsis-h'"/></div>
                <div class="action-menu" :class="{'show-menu': showMenuActions}">
                    <ul class="action-menu-list">
                        <li @click="$emit('criterium-selected', criterium)" class="action-menu-list-item menu-list-item-details"><i class="fa fa-search"></i><span>Details</span></li>
                        <li @click.stop="startEditing" class="action-menu-list-item"><i class="fa fa-pencil" /><span>Wijzig naam</span></li>
                        <li @click.stop="$emit('remove', criterium)" class="action-menu-list-item"><i class="fa fa-remove" /><span>Verwijder</span></li>
                    </ul>
                </div>
            </div>
            <div v-if="isEditing" class="edit-title">
                <div class="cover"></div>
                <name-input class="item-new" ok-title="Wijzig" @ok="finishEditing" @cancel="cancel" placeholder="Titel voor criterium" v-model="newTitle"/>
            </div>
        </div>
    </li>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Criterium from '../Domain/Criterium';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'criterium-view',
        components: { NameInput }
    })
    export default class CriteriumView extends Vue {
        private isEditing: boolean = false;
        private oldTitle: string = '';
        private newTitle: string = '';

        @Prop({type: String, required: true}) readonly id!: string;
        @Prop({type: String, required: true}) readonly menuActionsId!: string;
        @Prop({type: Boolean, required: true}) readonly selected!: boolean;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;

        mounted() {
            this.resetTitle();
        }

        resetTitle() {
            this.newTitle = this.criterium.title;
        }

        get showMenuActions() {
            return this.menuActionsId === this.id;
        }

        startEditing() {
            // todo: dataConnector: how to deal with updates?
            this.isEditing = true;
            this.oldTitle = this.criterium.title;
            this.$emit('start-edit');
        }

        finishEditing(canceled=false) {
            this.isEditing = false;
            this.oldTitle = '';
            this.$emit('finish-edit', this.newTitle, canceled);
        }

        cancel() {
            this.criterium.title = this.oldTitle;
            this.finishEditing(true);
            this.resetTitle();
        }

        // Because mounted() only occurs once, and this component keeps its own state, we have to check if the title has changed through an external update.
        @Watch('criterium.title')
        onTitleChanged() {
            this.resetTitle();
        }
    }
</script>