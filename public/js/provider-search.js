
//when there is an AJAX request and the user is not authenticated then reload the page - code start
$(document).ajaxError(function (event, xhr, settings, error) {
    if(xhr.status == 401) {
        alert("Your session has timed out. Please login.")
        window.location.reload();
    }
});
//when there is an AJAX request and the user is not authenticated then reload the page - code end

// store the search route for providers listing
var search_providers_route = location.origin+'/get-providers-list';

// Auto complete ajax call for search_provider dropdown - code start
$('input#search_provider').typeahead({
    items:'all',
    source: function (query, process) {
        return $.ajax({
        url: search_providers_route,
        type: 'get',
        data: { query: query },
        dataType: 'json',
        success: function (result) {
            hideError();
            // If no results found from the search then delete the action value from the form
            if(result.length === 0) {
                $("#add-provider").attr('action','');
                noDataError();
            }

            var resultList = result.map(function (item) {
                var aItem = { id: item.id, name: item.name, url: item.url};
                return JSON.stringify(aItem);
            });
            return process(resultList);
        }
        });
    },
    matcher: function (obj) {
        var item = JSON.parse(obj);
        return ~item.name.toLowerCase().indexOf(this.query.toLowerCase())
    },
    sorter: function (items) {          
        var beginswith = [], caseSensitive = [], caseInsensitive = [], item;
        while (aItem = items.shift()) {
        var item = JSON.parse(aItem);
        if (!item.name.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(JSON.stringify(item));
        else if (~item.name.indexOf(this.query)) caseSensitive.push(JSON.stringify(item));
        else caseInsensitive.push(JSON.stringify(item));
        }
        return beginswith.concat(caseSensitive, caseInsensitive)
    },
    highlighter: function (obj) {
        var item = JSON.parse(obj);
        var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
        return item.name.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
            return '<strong>' + match + '</strong>'
        })
    },
    updater: function (obj) {
        var item = JSON.parse(obj);
        if(item.url!=""){
            // Add the redirection url of the selected provider name to the form action
            $("#add-provider").attr('action',item.url);
            hideError();
        }
        
        return item.name;
    }
});
// Auto complete ajax call for search_provider dropdown - code end