<i18n>
{
    "en": {
        "add": "Add",
        "add-criterium": "Add new criterium",
        "title-new-criterium": "Title of new criterium"
    },
    "fr": {
        "add": "Ajouter",
        "add-criterium": "Ajouter un critère",
        "title-new-criterium": "Titre du nouveau critère"
    },
    "nl": {
        "add": "Voeg Toe",
        "add-criterium": "Criterium toevoegen",
        "title-new-criterium": "Titel voor nieuw criterium"
    }
}
</i18n>

<template>
    <div v-if="inputFormShown">
        <name-input ref="name-input" :ok-title="$t('add')" class="criterium-new item-new" @ok="addNewCriterium" @cancel="cancel" :placeholder="$t('title-new-criterium')" v-model="newCriterium.title"/>
    </div>
    <div v-else class="actions" :class="{criteriumDragging}">
        <button class="btn-new" :disabled="!actionsEnabled" @keydown.enter="blockEnterUp" @click="createNewCriterium">{{ $t('add-criterium') }}</button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Criterium from '../Domain/Criterium';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'new-criterium',
        components: { NameInput }
    })
    export default class NewCriterium extends Vue {
        private newCriterium: Criterium|null = null;
        private blockKeyUpEnter = false;

        @Prop({type: Boolean, default: false}) readonly criteriumDragging!: boolean;
        @Prop({type: Boolean, default: true}) readonly actionsEnabled!: boolean;

        get inputFormShown() {
            return this.newCriterium !== null;
        }

        createNewCriterium() {
            this.newCriterium = new Criterium();
            this.$emit('criterium-adding', true);
        }

        blockEnterUp() {
            this.blockKeyUpEnter = true;
        }

        checkAndReleaseBlockEnterUp() {
            if (this.blockKeyUpEnter) {
                this.blockKeyUpEnter = false;
                return true;
            }
            return false;
        }

        addNewCriterium() {
            if (this.checkAndReleaseBlockEnterUp()) { return; }
            this.$emit('criterium-added', this.newCriterium);
            this.$emit('criterium-adding', false);
            this.createNewCriterium();
        }

        cancel() {
            this.newCriterium = null;
            this.$emit('criterium-adding', false);
        }

        @Watch('newCriterium')
        newCriteriumChanged() {
            this.$nextTick(()=> {
                if (this.inputFormShown) {
                    const nameInput = (this.$refs['name-input'] as Vue).$el as any;
                    if (nameInput.scrollIntoViewIfNeeded) {
                        nameInput.scrollIntoViewIfNeeded();
                    } else {
                        nameInput.scrollIntoView();
                    }
                }
            });
        }
    }
</script>