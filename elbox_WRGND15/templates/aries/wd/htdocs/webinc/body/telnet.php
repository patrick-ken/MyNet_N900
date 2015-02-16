<? include "/htdocs/webinc/body/draw_elements.php";?>
<form>
<div>
        <div class="textinput">
            <span class="name"><?echo i18n("Enable telnetd");?></span>
            <span class="value"><input id="enable_telnet" type="checkbox" class="styled" /></span>
        </div>
        <div class="bottom_cancel_save">
            <input type="button" class="button_blue" onclick="BODY.OnSubmit();" value="<?echo I18N('h', 'Save');?>" />
        </div>
</div>
</form>
