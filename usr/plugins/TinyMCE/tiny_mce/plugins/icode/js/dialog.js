tinyMCEPopup.requireLangPack();

var icodeDialog = {
    init: function() {
        var f = document.forms[0];

        // Get the selected contents as text and place it in the input
        //f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});		
    },

    insert: function() {
        // Insert the contents from the input into the document
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, GetFormatedCode());
        tinyMCEPopup.close();
    }
};

function GetFormatedCode() {
    var strCode = document.forms[0].txtCode.value;

    strCode = strCode.replace(/</gi,"&lt;");
    strCode = strCode.replace(/>/gi, "&gt;");
    //strCode = strCode.replace(/&gt;/gi, ">");
	var strCodeText = '<pre class="prettyprint linenums bush:' + document.forms[0].selctLanguage.value + '" lang="' + document.forms[0].selctLanguage.value + '">';
/*    var strCodeText = '<a class="button" href="#source-code" id="view-source">查看源代码</a><div id="source-code"><pre  class="prettyprint">';
    strCodeText += strCode;
    strCodeText += '</pre><a href="#" id="x"><img src="' + tinyMCEPopup.getWindowArg('plugin_url') + '/img/x.png" alt="close"></a></div>'  
*/
	//var strCodeText = '<pre  class="prettyprint ' + document.forms[0].selctLanguage.value + '">';
    strCodeText += strCode;
    strCodeText += '</pre>'  
    return strCodeText;
    //alert("done");
}

tinyMCEPopup.onInit.add(icodeDialog.init, icodeDialog);
