/**
 * Created by SiarkoWodór on 02.07.2017.
 */
window.addEventListener('load', function(){
    var console = $("#consoleContainer");
    console.draggable().css({top: 400, left: (window.innerWidth-console.width()-50)});
    $(".consoleEntry").click(function(elem){
        $(this).children(".consoleTrace").toggleClass("hidden");
    });
});
