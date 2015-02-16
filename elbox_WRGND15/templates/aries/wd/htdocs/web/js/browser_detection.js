/*  
Browser detection can help programmer to know what browser user is currently using.
Browser detection can detect: IE, FireFox, Safari, Chrome, Opera, and Android.
*/
function client_browser()
{
	if(navigator.appName=="Microsoft Internet Explorer")
	{
		return "IE";
	}
	else if(navigator.appName=="Opera")
	{
		return "Opera";
	}
	else if(navigator.userAgent.search("Firefox")!=-1)
	{
		return "FireFox";
	}
	else if(navigator.userAgent.search("Chrome")!=-1 && navigator.vendor.search("Google")!=-1)
	{
		return "Chrome";
	}
	else if(navigator.userAgent.search("Safari")!=-1 && navigator.vendor.search("Apple")!=-1)
	{
		return "Safari";
	}
	else if(navigator.userAgent.search("Android")!=-1)
	{
		return "Android";
	}
}

function IE_browser_version()
{
	var v = 4;
	var div = document.createElement("div");
	var i = div.getElementsByTagName("i");

	do
	{
		div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->';
    }while (i[0]);
    
    return v > 5 ? v : false;
}