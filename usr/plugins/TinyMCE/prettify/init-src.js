var $ = jQuery.noConflict();
jQuery(document).ready(function() {
	prettyPrint();
	$prettify = jQuery(".prettyprint");
	$prettify.hover(
	  function () {jQuery(this).css("overflow-x","visible");jQuery(this).attr("title","双击全选代码!");},
	  function () {jQuery(this).css("overflow-x","hidden");}
	).dblclick( function () {
		var code = "";
		$linecode = jQuery(this).find("li");
		$linecode.each(function(e,i){
			$li = jQuery($linecode[e]);
			code += $li.text()+"\n";
			//alert($li.text());
		});
		
		if(code==""){
			code = jQuery(this).text();
		}

		$t = jQuery("<textarea>", {
		  "class": "source",
		  "title":"单击恢复!",
		  val: code,
		  click: function(){jQuery(this).hide();}
		});
		//var textarea = document.createElement("textarea");
		//var $t = jQuery(textarea);
		//textarea.className = "source";
		//textarea.innerHTML = $code;
		//$t.attr("title","单击恢复!");
		jQuery(this).append($t);		
		jQuery(this).fadeOut("slow",function () {
		jQuery(this).after($t);
		//alert($t.width()+"  "+$t.height());
		$t.css({"width":jQuery(this).width(),"height":jQuery(this).height()}).fadeIn("slow");
		$t.focus();
		$t.select();		
		});
		$t.click(function () {$prettify.fadeIn("slow");$t.remove();code = "";})			
	});
});