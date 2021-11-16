<i18n>
{
    "en": {
        "search": "Find"
    },
    "nl": {
        "search": "Zoeken"
    }
}
</i18n>

<template>
    <div class="action-bar input-group">
        <b-form-input class="form-group action-bar-search shadow-none" v-model="searchOptions.globalSearchQuery" @input="$emit('filter-changed')"
                      type="text" :placeholder="$t('search')" debounce="750" autocomplete="off"></b-form-input>
        <div class="input-group-btn">
            <button name="clear" class="btn btn-default" value="clear" @click="$emit('filter-cleared')">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'search-bar'
})
export default class SearchBar extends Vue {
    @Prop({type: Object}) readonly searchOptions!: any;
}
</script>

<style scoped>
.shadow-none {
    box-shadow: none;
}
</style>