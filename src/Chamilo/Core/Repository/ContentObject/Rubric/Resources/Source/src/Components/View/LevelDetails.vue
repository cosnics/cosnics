<template>
    <div>
        <div class="level-details">
            <div class="level-detail ld-level" @click.stop="selectLevel">
                <label :for="`level_title_${levelIndex}`" class="lc-label" :class="`${editMode ? 'label-level-title' : 'label-maybe-hide'}`">Niveau</label>
                <input :id="`level_title_${levelIndex}`" tabindex="0" type="text" autocomplete="off" v-model="level.title" placeholder="Vul hier een niveau in" @focus="selectLevel" @keydown.enter.prevent="" class="input-detail level-title-input">
                <div class="ld-cover"></div>
            </div>
            <div class="level-detail ld-description" :class="{ empty: level.description.length === 0 }">
                <label :for="`level_description_${levelIndex}`" class="lc-label" :class="`${editMode ? 'label-level-description' : 'label-maybe-hide'}`">Beschrijving</label>
                <textarea :id="`level_description_${levelIndex}`" tabindex="0" v-model="level.description" placeholder="Vul hier een beschrijving in" @focus="selectLevel" class="input-detail ta-description"></textarea>
            </div>
            <div class="level-detail ld-weight" @click.stop="selectLevel">
                <label :for="`level_score_${levelIndex}`" class="lc-label" :class="`${editMode ? 'label-level-weight' : 'label-maybe-hide'}`">Punten</label>
                <input :id="`level_score_${levelIndex}`" tabindex="0" type="number" name="Weight" maxlength="3" v-model="level.score" @focus="selectLevel" @keydown.enter.prevent="" class="input-detail level-weight-input">
                <div class="ld-cover"></div>
            </div>
            <div class="level-detail ld-default">
                <label v-if="!editMode" :for="`level_default_${levelIndex}`" class="lc-label label-maybe-hide">Standaard</label>
                <input :id="`level_default_${levelIndex}`" tabindex="0" type="radio" :checked="level.isDefault" @click.stop="setDefault" @keydown.space.prevent="setDefault" @keydown.enter.prevent="" class="input-detail level-default-input">
                <label class="lc-label label-level-default" :class="`${level.isDefault ? 'checked' : 'not-checked'} ${newLevel && newLevel !== level && newLevel.isDefault && level.isDefault ? 'old-default' : ''}`" @click.stop="" :for="`level_default_${levelIndex}`"><i class="fa fa-fw fa-check"></i><span v-if="editMode">Standaard keuze</span></label>
            </div>
            <div v-if="!editMode || !newLevel" class="level-detail ld-delete" :class="{'ld-delete-hide': level === newLevel}">
                <button class="lc-btn btn-level-delete" :disabled="newLevel !== null" @click.prevent="removeLevel"><!--    v-b-popover.hover.top="'Verwijder'">-->
                    <i class="fa fa-fw fa-minus-circle" aria-hidden="true"></i><span v-if="editMode">Verwijder niveau</span>
                </button>
            </div>
            <div v-if="!editMode" class="level-detail ld-edit">
                <button class="lc-btn btn-level-edit" :disabled="newLevel !== null" @click.prevent="editLevel">
                    <i class="fa fa-fw fa-edit"></i>
                </button>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import Level from '../../Domain/Level';

    @Component({
        name: 'level-details',

    })
    export default class LevelDetails extends Vue {
        @Prop({type: Level, required: true}) readonly level!: Level;
        @Prop({type: Level}) readonly newLevel!: Level|null;
        @Prop({type: Number, required: true}) readonly levelIndex!: number;
        @Prop({type: Boolean, default: false}) readonly editMode!: boolean;

        selectLevel() {
            this.$emit('level-selected', this.level);
        }

        setDefault() {
            this.$emit('level-default', this.level);
        }

        removeLevel() {
            this.$emit('level-remove', this.level);
        }

        editLevel() {
            this.$emit('level-edit', this.level);
        }
    }
</script>