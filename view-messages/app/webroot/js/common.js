
function gotoMessage(messageNo) {
   var loc = location;
   var url = loc.protocol + "//" + loc.host + "/messages/view/" + messageNo;
   location.replace(url);
}

function updateQuotedTextLinks() {
	// wrap all blockquotes with <p>
    $("blockquote").wrap("<p class=\"quoted-link\"></p>");

    // add anchor inside <p>
    $("p[@class='quoted-link']").prepend("<a class=\"ql\" href=\"#\">Show Quoted Text</a>");

    // hide all blockquote
    $("blockquote").hide();

    // add toggle function to show or hide text
    $("p[@class='quoted-link']").toggle(
         function() {
           $("blockquote", $(this)).slideDown("normal");
           $(this).attr('class', 'hide-quoted-link');
           $("a[@class='ql']", $(this)).html('Hide Quoted Text');
         },
         function() {
           $("blockquote", $(this)).slideUp("normal");
           $(this).attr('class', 'quoted-link');
           $("a[@class='ql']", $(this)).html('Show Quoted Text');
         }
    );
}

jQuery(function($) {
  //console.log('inside onload');
  updateQuotedTextLinks();
});