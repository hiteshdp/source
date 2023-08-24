$(document).ready(function(){
    //Radio Click Content
    $('.radio-container label').addClass('inactive');
    $('.desc').hide();
    $('.desc:first').show();
        
    $('.radio-container label').click(function(){
        var t = $(this).attr('id');
    if($(this).hasClass('inactive')){ //this is the start of our condition 
        $('.radio-container label').addClass('inactive');           
        $(this).removeClass('inactive');
        
        $('.desc').hide();
        $('#'+ t + 'C').fadeIn('slow');
        $(this).parent().addClass('active').siblings().removeClass('active');
    }
    });
});