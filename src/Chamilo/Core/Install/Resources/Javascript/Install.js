$(function()
{
    
    function setPackageSelection(e, ui)
    {
        e.preventDefault();
        
        var packageElement = $(this);
        var packageElementCheckbox = $('input', packageElement);
        var packageElementSelection = $('input:checked', packageElement);
        
        if (packageElementSelection.length == 1)
        {
            packageElement.removeClass('btn-success');
            packageElement.addClass('btn-default');
            packageElementCheckbox.prop('checked', false);
        }
        else
        {
            packageElement.addClass('btn-success');
            packageElement.removeClass('btn-default');
            packageElementCheckbox.prop('checked', true);
        }
    }
    
    function getPackageTypeContainer(node)
    {
        var packageTypeContainer = node.closest('.package-list');
        
        if (packageTypeContainer.length == 0)
        {
            packageTypeContainer = node.closest('.package-selection');
        }
        
        return packageTypeContainer;
    }
    
    function deselectAllPackages(e, ui)
    {
        var packageTypeContainer = getPackageTypeContainer($(this));
        
        $('.btn:not([disabled=\"disabled\"])', packageTypeContainer).removeClass('btn-success');
        $('.btn:not([disabled=\"disabled\"])', packageTypeContainer).addClass('btn-default');
        
        $('.btn:not([disabled=\"disabled\"]) input', packageTypeContainer).prop('checked', false);
    }
    
    function selectAllPackages(e, ui)
    {
        var packageTypeContainer = getPackageTypeContainer($(this));
        
        $('.btn:not([disabled=\"disabled\"])', packageTypeContainer).addClass('btn-success');
        $('.btn:not([disabled=\"disabled\"])', packageTypeContainer).removeClass('btn-default');
        
        $('.btn:not([disabled=\"disabled\"]) input', packageTypeContainer).prop('checked', true);
    }
    
    $(document).ready(function()
    {
        $(document).on('click', ".package-selection .btn:not([disabled=\"disabled\"])", setPackageSelection);
        $(document).on('click', ".package-selection .package-list-select-none", deselectAllPackages);
        $(document).on('click', ".package-selection .package-list-select-all", selectAllPackages);
    });
    
});