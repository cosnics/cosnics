<i18n>
{
    "en": {
        "bold": "bold",
        "bold-strong": "Bold / Strong emphasis",
        "close": "Close",
        "first-item": "A first item",
        "first-paragraph": "A first paragraph",
        "formatting-help": "Formatting help",
        "formatting-intro": "The Markdown techniques below can be used for formatting level descriptions",
        "is": "is",
        "italic": "italicized",
        "italic-emphasis": "Italic / Emphasis",
        "lists": "Lists",
        "numbered-lists": "Numbered lists",
        "paragraphs": "Paragraphs",
        "paragraphs-explain": "Enter two new lines to create a new paragraph.",
        "second-item": "A second item",
        "second-paragraph": "A second paragraph",
        "third-item": "A third item",
        "this-text": "This text"
    },
    "fr": {
        "bold": "gras",
        "bold-strong": "Gras / Grande emphase",
        "close": "Fermer",
        "first-item": "Un premier élément",
        "first-paragraph": "Un premier paragraphe",
        "formatting-help": "Aide de mise en forme",
        "formatting-intro": "On peut utiliser les techniques Markdown ci-dessous pour mettre en forme les descriptions de niveau",
        "is": "est",
        "italic": "italique",
        "italic-emphasis": "Italique / Emphase",
        "lists": "Listes",
        "numbered-lists": "Listes numérotées",
        "paragraphs": "Paragraphes",
        "paragraphs-explain": "Sauter deux lignes pour commencer un nouveau paragraphe.",
        "second-item": "Un deuxième élément",
        "second-paragraph": "Un deuxième paragraphe",
        "third-item": "Un troisième élément",
        "this-text": "Ce texte"
    },
    "nl": {
        "bold": "vetgedrukt",
        "bold-strong": "Vetgedrukt / Sterke nadruk",
        "close": "Sluiten",
        "first-item": "Een eerste item",
        "first-paragraph": "Een eerste paragraaf",
        "formatting-help": "Opmaakhulp",
        "formatting-intro": "De volgende Markdown-technieken kan je gebruiken om niveauomschrijvingen op te maken",
        "is": "is",
        "italic": "schuingedrukt",
        "italic-emphasis": "Schuin / Nadruk",
        "lists": "Lijsten",
        "numbered-lists": "Genummerde lijsten",
        "paragraphs": "Paragrafen",
        "paragraphs-explain": "Voeg twee regels in om een nieuwe paragraaf te maken.",
        "second-item": "Een tweede item",
        "second-paragraph": "Een tweede paragraaf",
        "third-item": "Een derde item",
        "this-text": "Deze tekst"
    }
}
</i18n>

<template>
    <div class="formatting-help">
        <button class="btn-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i class="fa fa-close" aria-hidden="true" /></button>
        <div class="formatting-title">{{ $t('formatting-help') }}</div>
        <p>{{ $t('formatting-intro') }}:</p>
        <hr>
        <p class="formatting-example">{{ $t('bold-strong') }}</p>
        <code>**{{ $t('this-text') }}** {{ $t('is') }} {{ $t('bold') }}.</code>
        <p><strong>{{ $t('this-text') }}</strong> {{ $t('is') }} {{ $t('bold') }}.</p>
        <hr>
        <p class="formatting-example">{{ $t('italic-emphasis') }}</p>
        <code>*{{ $t('this-text') }}* {{ $t('is') }} {{ $t('italic') }}.</code>
        <p><em>{{ $t('this-text') }}</em> {{ $t('is') }} {{ $t('italic') }}.</p>
        <hr>
        <p class="formatting-example">{{ $t('paragraphs') }}</p>
        <p class="formatting-explain">{{ $t('paragraphs-explain') }}</p>
        <code>{{ $t('first-paragraph') }}.<br><br>{{ $t('second-paragraph') }}.</code>
        <p>{{ $t('first-paragraph') }}.</p>
        <p>{{ $t('second-paragraph') }}.</p>
        <hr>
        <p class="formatting-example">{{ $t('lists') }}</p>
        <code>
            - {{ $t('first-item') }}<br>
            - {{ $t('second-item') }}<br>
            - {{ $t('third-item') }}
        </code>
        <ul>
            <li>{{ $t('first-item') }}</li>
            <li>{{ $t('second-item') }}</li>
            <li>{{ $t('third-item') }}</li>
        </ul>
        <hr>
        <p class="formatting-example">{{ $t('numbered-lists') }}</p>
        <code>
            1. {{ $t('first-item') }}<br>
            2. {{ $t('second-item') }}<br>
            3. {{ $t('third-item') }}
        </code>
        <ol>
            <li>{{ $t('first-item') }}</li>
            <li>{{ $t('second-item') }}</li>
            <li>{{ $t('third-item') }}</li>
        </ol>
    </div>

</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';

    @Component({})
    export default class FormattingHelp extends Vue {
    }
</script>

<style lang="scss">
    .formatting-title {
        color: darken($cluster-color-selected, 10%);
        font-weight: 700;
    }

    .formatting-example {
        color: darken($cluster-color-selected, 15%);
        margin-bottom: 0;
    }

    .formatting-explain {
        line-height: 1.5em;
        margin-bottom: 0;
    }

    .formatting-help {
        border-left: 1px solid #ddd;
        flex-basis: 40%;
        margin-left: .625rem;
        padding-left: .625rem;

        hr {
            margin-top: 0;
            margin-bottom: .3125rem;
            border-color: #ccc;
        }

        code {
            background: #efefea;
            color: #555;
            display: block;
        }

        ul, ol {
            margin-top: .3125rem;
            padding-left: 1.25rem;
        }
    }
</style>