<i18n>
{
    "en": {
        "add": "Add",
        "add-subsection": "Add new subsection",
        "title-new-subsection": "Title of new subsection"
    },
    "fr": {
        "add": "Ajouter",
        "add-subsection": "Ajouter une sous-section",
        "title-new-subsection": "Titre de la nouvelle sous-section"
    },
    "nl": {
        "add": "Voeg Toe",
        "add-subsection": "Onderdeel toevoegen",
        "title-new-subsection": "Titel voor nieuwe onderverdeling"
    }
}
</i18n>

<template>
    <div class="actions">
        <div v-if="inputFormShown">
            <name-input :ok-title="$t('add')" class="cluster-new item-new" @ok="addNewCluster" @cancel="cancel" :placeholder="$t('title-new-subsection')" v-model="newCluster.title" />
        </div>
        <button v-else class="btn-new" :disabled="!actionsEnabled" @keydown.enter="blockEnterUp" @click="createNewCluster">{{ $t('add-subsection') }}</button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Cluster from '../Domain/Cluster';
    import NameInput from './NameInput.vue';

    @Component({
        name: 'new-cluster',
        components: { NameInput }
    })
    export default class NewCluster extends Vue {
        private newCluster: Cluster|null = null;
        private blockKeyUpEnter = false;

        @Prop({type: String, default: null}) readonly viewId!: string|null;
        @Prop({type: Boolean, default: true}) readonly actionsEnabled!: boolean;

        get inputFormShown() {
            return this.newCluster !== null;
        }

        createNewCluster() {
            this.newCluster = new Cluster();
            this.emit(this.viewId);
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

        addNewCluster() {
            if (this.checkAndReleaseBlockEnterUp()) { return; }
            this.$emit('cluster-added', this.newCluster);
            this.cancel();
        }

        cancel() {
            this.newCluster = null;
            this.emit('');
        }

        emit(value: string|null) {
            if (this.viewId !== null) {
                this.$emit('dialog-view', value);
            }
        }
    }
</script>