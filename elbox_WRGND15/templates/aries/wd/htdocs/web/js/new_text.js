/*
 Grayed out textboexes should be very obvious.
	1. When textboxes disabled, background color should be obvious.
	2. We set textboxes background color is white when it is not disabled.
*/
function New_Text(){}
New_Text.prototype = 
{
	refresh: function()
	{
		inputs = document.getElementsByTagName("input");
		
		for(var b = 0; b < inputs.length; b++) 
		{
			if(inputs[b].type=="password" || inputs[b].type=="text")
			{
				if(inputs[b].disabled == true) inputs[b].style.backgroundColor = "#CCC";	
				else inputs[b].style.backgroundColor = "#FFF";
			}
		}
	}
}