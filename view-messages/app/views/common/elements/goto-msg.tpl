     <div id="goto" class="side-bar-widget">
       <h2>Show Message</h2>
       <form name="goMsg" action="/messages/goto">
        <label for="msgNo">#</label><input id="msgNo" name="msgNo" size="6" maxlength="6" type="text" />
        <input name="sa" value="Go" type="button" onclick="gotoMessage(document.goMsg.msgNo.value)"/>
       </form>
     </div> <!-- goto -->
