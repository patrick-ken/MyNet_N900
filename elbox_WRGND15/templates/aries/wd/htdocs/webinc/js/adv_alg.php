<style>
/* The CSS is only for this page.
 * Notice:
 * If the items are few, we put them here,
 * If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "ACL",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function ()	{},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var acl = PXML.FindModule("ACL");
		if (acl===""){ alert("InitValue ERROR!"); return false; }
		var fw = acl+"/acl";
		var pptp=XG(fw+"/alg/pptp");
		var ipsec=XG(fw+"/alg/ipsec");
		var sip=XG(fw+"/alg/sip");
		if(pptp=="1") OBJ("pptp").checked=true;
		else OBJ("pptp").checked=false;
		if(ipsec=="1") OBJ("ipsec").checked=true;
		else OBJ("ipsec").checked=false;
		if(sip=="1") OBJ("sip").checked=true;
		else OBJ("sip").checked=false;

		return true;
	},	
	PreSubmit: function()
	{
		var acl = PXML.FindModule("ACL");
		var fw = acl+"/acl";
		if (OBJ("pptp").checked ) XS(fw+"/alg/pptp", "1");
		else XS(fw+"/alg/pptp", "0");
		if (OBJ("ipsec").checked ) XS(fw+"/alg/ipsec", "1");
		else XS(fw+"/alg/ipsec", "0");
		if (OBJ("sip").checked ) XS(fw+"/alg/sip", "1");
		else XS(fw+"/alg/sip", "0");				
		
		return PXML.doc;
	},

	IsDirty: null,
	Synchronize: function() {}

	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
}
</script>
