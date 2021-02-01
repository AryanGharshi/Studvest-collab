$(document).ready(function(){
    var multipleCancelButton_tags = new Choices('#select_tags', {
        removeItemButton: true,
        maxItemCount: 100,
        searchResultLimit: 100,
        renderChoiceLimit: 100
    });
    var multipleCancelButton_bars = new Choices('#select_bars', {
        removeItemButton: true,
        maxItemCount: 100,
        searchResultLimit: 100,
        renderChoiceLimit: 100
    });
})