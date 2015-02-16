
function New_help(){}
New_help.prototype = 
{
	init: function() 
	{
		var span = document.getElementsByTagName("span");
		var help_title="";
		var help_text="";
		for(var i = 0; i < span.length; i++) 
		{
			if(span[i].className=="help" && span[i+1].className=="help_msg" ) 
			{
				if(!NewHelp.overlap(span[i]))
				{
					span[i].onclick = this.help_display;
					span[i].innerHTML = '<img src="pic/help.png" onmouseover="this.src=\'pic/help_hover.png\'" onmouseout="this.src=\'pic/help.png\'" />';
				
					var j=i+1;
					span[j].style.display="none";
					
					if(span[j].firstChild.className=="help_title") // For IE
					{help_title=span[j].firstChild.innerHTML;}
					else if(span[j].firstChild.nextSibling.className=="help_title") // For FF & Chrome
					{help_title=span[j].firstChild.nextSibling.innerHTML;}
					
					if(span[j].childNodes[2].className=="help_text") // For IE
					{help_text=span[j].childNodes[2].innerHTML;}				
					else if(span[j].childNodes[3].className=="help_text") // For FF & Chrome
					{help_text=span[j].childNodes[3].innerHTML;}
					
					span[j].innerHTML = "";
					var str='<div class="help_box">' +
								'<div class="help_box_top" onclick="this.parentNode.parentNode.style.display=\'none\'"><div class="help_box_top_title">' + help_title + '</div></div>' +
								'<div class="help_box_middle"><div class="help_box_middle_text">' + help_text + '</div></div>' +
								'<div class="help_box_bottom"></div>' +
							'</div>';
					span[j].innerHTML = str;
				}
			}			
		}
	},
	overlap: function(span)
	{
		if(span.innerHTML=="" || span.innerHTML==null) return false;
		return true;
	},		
	help_display: function()
	{
		var element = this.nextSibling;//For IE
		if(element.nodeType!=1) element = element.nextSibling;// For FF & Chrome			
		element.style.display='';
	}		
}
