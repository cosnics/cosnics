<template>
    <div class="modal-bg" v-if="removeItem !== null" @click.stop="$emit('cancel')">
        <div class="modal-content" @click.stop="">
            <div class="modal-content-title" v-if="removeItem.constructor.name === 'Category' && removeItem.color === ''">Criteria verwijderen?</div>
            <div class="modal-content-title" v-else>{{ removeItem.constructor.name }} '{{ removeItem.title }}' verwijderen?</div>
            <div>
                <button class="btn-dialog-remove btn-ok" ref="btn-remove" @click.stop="$emit('remove')">Verwijder</button>
                <button class="btn-dialog-remove btn-cancel" @click.stop="$emit('cancel')">Annuleer</button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';

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