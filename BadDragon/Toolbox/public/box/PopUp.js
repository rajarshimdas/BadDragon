/*
+-------------------------------------------------------+
| Rajarshi Das						|
+-------------------------------------------------------+
| Created On: 11-Oct-2012				|
| Updated On:           				|
+-------------------------------------------------------+
| CONCERT   http://concert.rajarshi.in                  |
|           raj@rajarshi.in                             |
+-------------------------------------------------------+
*/
function PopUp(URL, pageWidth, pageHeight) {
    // Variables
    var w       = pageWidth;
    var h       = pageHeight;
    var day     = new Date();
    var id      = day.getTime();

    // Find the center of the screen
    var leftpx  = (screen.width/2)-(w/2);
    var toppx   = (screen.height/2)-(h/2);

    // Trigger the POP-Up
    eval("page" + id + " = window.open(URL,'" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=" + w + ",height=" + h + ",left=" + leftpx +",top=" + toppx + "');");

}