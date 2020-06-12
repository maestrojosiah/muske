$(document).ready(function(){

    $(".filter-button").click(function(){

        $(this).toggleClass('btn-info');
        
        var value = $(this).attr('data-filter');
        var inputVal = $("#tags-input").val();
        
        if(inputVal.indexOf("."+value) >= 0 ){
            
            $("#tags-input").val($("#tags-input").val().trim().replace('.'+value, ''));
            
        } else {
            
            $("#tags-input").val($("#tags-input").val() + '.' + value + '');
            
        }
        
        var tags = $("#tags-input").val().trim();
        console.log(tags);
        
        if(value == "all" || tags == "")
        {

            $('.filter').show('1000');
            $(':button').addClass('btn-primary');
            $("#tags-input").val("");
            $("#you-are-here").text("");
            
        }
        else
        {
            $(".filter").not("."+value).hide('3000');
            $('.filter').filter(tags).show('3000');
            $("#you-are-here").html(tags.replace(/\./g, "+"));
            
        }
    });

});