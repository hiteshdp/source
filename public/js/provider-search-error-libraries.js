// Show error below the input of provider search list
function showError(){
    $("#search_provider-error").show();
    $("#search_provider-error").text('Please select provider name from the list.');
}
// Show error below the input of provider search list when no data found from the search
function noDataError(){
    $("#search_provider-error").show();
    $("#search_provider-error").text('No data found from the list.');
}
// Hide error below the input of provider search list
function hideError(){
    $("#search_provider-error").hide();
    $("#search_provider-error").text('');
}

// validate input box form - code start
$('#add-provider-name').on('click', function(e){
    // prevent any activity to submit the form
    e.preventDefault();
    
    // Check if name is selected from the dropdown
    if($("#add-provider").attr('action') == ''){
        showError();
    }else{
        hideError();
        $('#add-provider').submit();

    }
});
// validate input box form - code end

