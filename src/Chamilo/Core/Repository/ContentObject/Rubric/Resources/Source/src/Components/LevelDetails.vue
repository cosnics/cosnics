<i18n>
{
    "en": {
        "add": "Add",
        "cancel": "Cancel",
        "default": "Default",
        "default-info": "Optional choice field. The level assigned by default to a criterium.",
        "enter-level-here": "Enter a level here",
        "level": "Level",
        "move-level-down": "Move level down",
        "move-level-up": "Move level up",
        "points": "Points",
        "remove-level": "Remove level"
    },
    "fr": {
        "add": "Ajouter",
        "cancel": "Annuler",
        "default": "Norme",
        "default-info": "Contrôle de choix optionnel. Le niveau attribué par défaut à un critère.",
        "enter-level-here": "Entre un niveau ici",
        "level": "Niveau",
        "move-level-down": "Déplacer vers le bas",
        "move-level-up": "Déplacer vers le haut",
        "points": "Points",
        "remove-level": "Supprimer le niveau"
    },
    "nl": {
        "add": "Voeg Toe",
        "cancel": "Annuleer",
        "default": "Standaard",
        "default-info": "Optioneel keuzeveld. Het niveau dat standaard wordt toegekend aan een criterium.",
        "enter-level-here": "Vul hier een niveau in",
        "level": "Niveau",
        "move-level-down": "Verplaats niveau naar beneden",
        "move-level-up": "Verplaats niveau naar boven",
        "points": "Punten",
        "remove-level": "Niveau verwijderen"
    }
}
</i18n>

<template>
    <component :is="tag" class="level-details" :class="{ /*'show-description': showDescription,*/ 'is-selected': selectedLevel === level, 'new-level': isNew }" @keydown.esc="cancelNewLevel">
        <div class="level-details-text">
            <div class="level-details-text-1">
                <div class="ld-title" @click.stop="selectLevel">
                    <label :for="`level_title_${index}`" class="level-label label-hidden">{{ $t('level') }}</label>
                    <input :id="`level_title_${index}`" :tabindex="tabIndex" type="text" autocomplete="off" v-model="level.title" @keydown.enter="isNew ? addNewLevel() : null" @input="onChange" @focus="selectLevel" :placeholder="$t('enter-level-here')" class="input-detail" >
                </div>
                <!--<button class="btn-more" v-if="!showLevelDescriptions":tabindex="tabIndex" @click.stop="showDescription = !showDescription"><i tabindex="-1" class="check fa" aria-hidden="true" /></button>-->
            </div>
            <!--<div class="ld-description" @click.stop="selectLevel">
                <label :for="`level_description_${index}`" class="level-label label-hidden">Beschrijving</label>
                <textarea :id="`level_description_${index}`" :tabindex="tabIndex" v-model="level.description" placeholder="Vul hier een beschrijving in" @input="onChange" @focus="selectLevel" class="input-detail"></textarea>
            </div>-->
        </div>
        <div v-if="rubric.useScores" class="ld-score" @click.stop="selectLevel">
            <label :for="`level_score_${index}`" class="level-label label-hidden" :style="rubric.useRelativeWeights ? 'margin-left: 1.5rem' : ''">{{ rubric.useRelativeWeights ? '%' : $t('points') }}</label>
            <input :id="`level_score_${index}`" :tabindex="tabIndex" type="number" name="Weight" maxlength="3" v-model.number="level.score" required min="0" step="1" @keydown.enter="isNew ? addNewLevel() : null" @input="onChangeScore" @focus="selectLevel" class="input-detail">
        </div>
        <div class="ld-default" @click.stop="">
            <label :for="`level_default_${index}`" class="level-label label-hidden">{{ $t('default') }} <i class="fa fa-info-circle" :title="$t('default-info')" /></label>
            <input :id="`level_default_${index}`" :tabindex="tabIndex" type="radio" :checked="level.isDefault" @click.stop="setDefault" class="input-detail">
            <label :for="`level_default_${index}`" :class="`${level.isDefault ? 'checked' : 'not-checked'}`" class="fa" aria-hidden="true"></label>
        </div>
        <!--<span style="flex-basis: 100%; height: 0"></span>-->
        <div v-if="isNew" class="actions">
            <button class="btn-strong mod-confirm" @click.prevent="addNewLevel">{{ $t('add') }}</button>
            <button class="btn-strong" @click.prevent="cancelNewLevel">{{ $t('cancel') }}</button>
        </div>
        <div class="level-actions-wrapper" :class="{ 'is-active': !isNew && selectedLevel === level }" @click.stop="">
            <div class="level-actions">
                <button :id="`level_move_up_${level.id}`" class="btn-level-action" :disabled="isNew || index <= 0" :aria-label="$t('move-level-up')" :title="$t('move-level-up')" @click.stop="$emit('level-move-up')">
                    <i class="fa fa-arrow-up" aria-hidden="true" />
                </button>
                <button :id="`level_move_down_${level.id}`" class="btn-level-action" :disabled="isNew || index >= rubric.levels.length - 1" :aria-label="$t('move-level-down')" :title="$t('move-level-down')" @click.stop="$emit('level-move-down')">
                    <i class="fa fa-arrow-down" aria-hidden="true" />
                </button>
                <button class="btn-level-action btn-delete" :disabled="isNew" :aria-label="$t('remove-level')" :title="$t('remove-level')" @click.prevent="removeLevel">
                    <i class="fa fa-minus-circle" aria-hidden="true" />
                </button>
            </div>
        </div>
    </component>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import debounce from 'debounce';

    @Component({
        name: 'level-details',
        components: {
        },
    })
    export default class LevelDetails extends Vue {
        private showDescription = false;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Level, required: true}) readonly level!: Level;
        @Prop({type: Level, default: null}) readonly selectedLevel!: Level|null;
        @Prop({type: String, default: 'div'}) readonly tag!: String;
        @Prop({type: Boolean, default: false }) readonly isNew!: boolean;
        @Prop({type: Boolean, default: false }) readonly hasNew!: boolean;
        @Prop({type: Boolean, default: false }) readonly showLevelDescriptions!: boolean;

        constructor() {
            super();
            this.onChange = debounce(this.onChange, 750);
        }

        get focusable() : boolean {
            if (this.isNew) { return true; }
            return !this.hasNew;
        }

        get tabIndex() : number {
            return this.focusable ? 0 : -1;
        }

        get index() : number {
            if (this.isNew) { return this.rubric.levels.length; }
            return this.rubric.levels.indexOf(this.level);
        }

        selectLevel() {
            this.$emit('level-selected', this.level);
        }

        setDefault() {
            this.$emit('level-default', this.level);
            this.onChange();
        }

        onChange() {
            this.$emit('change', this.level);
        }

        onChangeScore(event: any) {
            const el = event.target as HTMLInputElement;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            this.$emit('change', this.level);
        }

        addNewLevel() {
            this.$emit('new-level-added');
        }

        cancelNewLevel() {
            this.$emit('new-level-canceled');
        }

        removeLevel() {
            this.$emit('level-remove', this.level);
        }
    }
</script>
<style lang="scss" scoped>
    .ld-score .input-detail {
        -moz-appearance: textfield; /* Firefox */
        &::-webkit-outer-spin-button,
        &::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    }
</style>