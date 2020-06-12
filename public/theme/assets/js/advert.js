
$(document).on("click", '[id^="for-"]', function(){

    var project = jQuery(this).attr("id");
    var splitted = project.split('-'); 
    var target = splitted[1] 

    $("#"+target+"-page").show("slow");
    console.log("#"+target+"-page");
    $("#close-icon").show();

});


$( "#close-icon" ).click(function() {
    $("#newAd-page").hide("slow");
    $("#trackAd-page").hide("slow");
});

