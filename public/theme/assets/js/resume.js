
$(document).on("click", '[id^="for-"]', function(){

    var project = jQuery(this).attr("id");
    var splitted = project.split('-'); //changeRow_Qt_1504
    var target = splitted[1] //1504

    $("#"+target+"-page").show("slow");
    $("#close-icon").show();
    $("#download-icon").show();

});


$( "#close-icon" ).click(function() {
    $("#resume-page").hide("slow");
    $("#portfolio-page").hide("slow");
    $("#blog-page").hide("slow");
    $("#contact-page").hide("slow");
    $("#close-icon").hide();
    $("#download-icon").hide();
});

