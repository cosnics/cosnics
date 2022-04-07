<i18n>
{
    "en": {
        "cancel": "Cancel",
        "Category": "category",
        "Cluster": "subsection",
        "Criterium": "criterium",
        "remove": "Remove",
        "remove-criteria": "Remove criteria",
        "remove-item": "Remove {item}"
    },
    "fr": {
        "cancel": "Annuler",
        "Category": "la catégorie",
        "Cluster": "la sous-section",
        "Criterium": "le critère",
        "remove": "Supprimer",
        "remove-criteria": "Supprimer la liste des critères",
        "remove-item": "Supprimer {item}"
    },
    "nl": {
        "cancel": "Annuleer",
        "Category": "Categorie",
        "Cluster": "Onderverdeling",
        "Criterium": "Criterium",
        "remove": "Verwijder",
        "remove-criteria": "Criteria verwijderen",
        "remove-item": "{item} verwijderen"
    }
}
</i18n>


<template>
    <div class="modal-bg" v-if="removeItem !== null" @click.stop="$emit('cancel')">
        <div class="modal-content" @click.stop="">
            <div class="modal-content-title" v-if="removeItem.constructor.name === 'Category' && removeItem.title === ''">{{ $t('remove-criteria') }}?</div>
            <div class="modal-content-title" v-else>{{ $t('remove-item', { item: `${ $t(removeItem.constructor.name) } '${ removeItem.title }'` }) }}?</div>
            <div>
                <button class="btn-strong mod-confirm" ref="btn-remove" @click.stop="$emit('remove')">{{ $t('remove') }}</button>
                <button class="btn-strong" @click.stop="$emit('cancel')">{{ $t('cancel') }}</button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';

    @Component({
        name: 'remove-dialog'
    })
    export default class RemoveDialog extends Vue {
        @Prop({type: [Cluster, Category, Criterium], default: null}) readonly removeItem!: Cluster|Category|Criterium|null;

        @Watch('removeItem')
        onRemoveItemChanged() {
            if (this.removeItem) {
                this.$nextTick(() => {
                    (this.$refs['btn-remove'] as HTMLElement).focus();
                });
            }
        }
    }
</script>