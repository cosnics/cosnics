<i18n>
{
    "en": {
        "first-name": "First name",
        "last-name": "Last name",
        "official-code": "Official code"
    },
    "nl": {
        "first-name": "Voornaam",
        "last-name": "Achternaam",
        "official-code": "Officiële code"
    }
}
</i18n>

<template>
    <table class="users-table">
        <thead>
            <tr class="table-row table-head-row">
                <th class="table-cell">{{ $t('last-name') }}</th>
                <th class="table-cell">{{ $t('first-name') }}</th>
                <th class="table-cell">{{ $t('official-code') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(user, row_index) in missingUsers" class="table-row table-body-row" :key="`result-row-${row_index}`">
                <td class="table-cell">{{ user.lastname }}</td>
                <td class="table-cell">{{ user.firstname }}</td>
                <td class="table-cell">{{ user.official_code }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

interface User { lastname: string; firstname: string; official_code: string; }

@Component({})
export default class MissingUsersTable extends Vue {
    @Prop({type: Array, default: () => []}) readonly missingUsers!: User[];
}
</script>

<style scoped>
.users-table {
    font-size: 13px;
    margin-bottom: 20px;
}

thead {
    background-color: #f8fbfb;
    border-bottom: 1px solid #ebebeb;
    border-top: 1px solid #ebebeb;
}

.table-row {
    border-bottom: 1px solid #ebebeb;
    border-left: 1px solid #ebebeb;
}

.table-body-row:first-child {
    background: linear-gradient(to bottom, #dde5e9 0, rgba(255, 255, 255, 0) 3px);
}

.table-cell {
    border-right: 1px solid #ebebeb;
    padding: 5px;
}
.table-head-row .table-cell {
    color: #727879;
}
</style>