LOCAL CHANGES
*************

Remove PRINT functionality from viewer.html

File: web/viewer.html

Remove print buttons by adding display:none to css

<button id="print" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print" style="display: none;">
  <span data-l10n-id="print_label">Print</span>
</button>

<button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print" style="display: none;">
  <span data-l10n-id="print_label">Print</span>
</button>

Make sure that if the press CTRL+P that they print a minimal amount of pages by defining a custom CSS

<style type="text/css" media="print">
  body { visibility: hidden; display: none }
</style>