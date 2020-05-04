<template>
    <div v-if="inputFormShown">
        <name-input ref="name-input" ok-title="Voeg Toe" class="criterium-new item-new" @ok="addNewCriterium" @cancel="cancel" placeholder="Titel voor nieuw criterium" v-model="newCriterium.title"/>
    </div>
    <div v-else class="actions" :class="{criteriumDragging}">
        <button class="btn-criterium-add" :disabled="!actionsEnabled" @click="createNewCriterium"><i class="fa fa-plus" aria-hidden="true"/>Nieuw criterium</button>
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

        @Prop({type: Boolean, default: false}) readonly criteriumDragging!: boolean;
        @Prop({type: Boolean, default: true}) readonly actionsEnabled!: boolean;

        get inputFormShown() {
            return this.newCriterium !== null;
        }

        createNewCriterium() {
            this.newCriterium = new Criterium();
        }

        addNewCriterium() {
            this.$emit('criterium-added', this.newCriterium);
            this.cancel();
        }

        cancel() {
            this.newCriterium = null;
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