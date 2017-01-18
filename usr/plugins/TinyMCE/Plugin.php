<?php
/**
 * TinyMCE+Prettify，打造Te最好用的编辑器<br/>
 * 自动集成语法高亮的编辑器，可设置加载模式。
 *
 * @package TinyMCE
 * @author QFisH
 * @version 1.0.7
 * @dependence 9.9.2-*
 * @link http://QFisH.Me
 * @modify gavin
 */
class TinyMCE_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('admin/write-post.php')->richEditor = array('TinyMCE_Plugin', 'render');
        Typecho_Plugin::factory('admin/write-page.php')->richEditor = array('TinyMCE_Plugin', 'render');
		
		Typecho_Plugin::factory('Widget_Archive')->header = array('TinyMCE_Plugin', 'header');
		Typecho_Plugin::factory('Widget_Archive')->footer = array('TinyMCE_Plugin', 'footer');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
	{
		$csses = array(		
			'qnight' => "Default",
			'desert' => "Desert",
			'sunburst' => "Sunburst",
			'prettify' => "prettify"
		);		
        $mode = new Typecho_Widget_Helper_Form_Element_Radio('mode', array('1'=>_t('启用'), '0'=>_t('关闭')), '1', _t('语法高亮是否启用'));
		$JQueryMode = new Typecho_Widget_Helper_Form_Element_Radio('JQueryMode', array('1'=>_t('随本插件添加'), '0'=>_t('自己添加')), '1', _t('jQuery加载方式'));
		$Editormode = new Typecho_Widget_Helper_Form_Element_Radio('Editormode', array('1'=>_t('默认'), '0'=>_t('精简')), '1', _t('编辑器模式'));
		$EditorPorBR = new Typecho_Widget_Helper_Form_Element_Radio('EditorPorBR', array('1'=>_t('p'), '0'=>_t('br')), '1', _t('换行符号：'));
		
		$JQueryUrl = new Typecho_Widget_Helper_Form_Element_Textarea('JQueryUrl', NULL, _t('<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>'."\n"), _t('添加JQuery路径:'));
		//$JQueryUrl = new Typecho_Widget_Helper_Form_Element_Textarea('JQueryUrl', NULL, _t('<script src="' . Helper::options()->pluginUrl . '/TinyMCE/jquery-1.4.2.min.js"></script>'), _t('添加JQuery路径:'));
		
		$prettifyCss = new Typecho_Widget_Helper_Form_Element_Select('prettifyCss', $csses, 'qnight', _t('选择要高亮的样式'));
		$extendedCss = new Typecho_Widget_Helper_Form_Element_Textarea('extendedCss', NULL, _t(""), _t('自定义样式(CSS)'));
		
		$extendedTags = new Typecho_Widget_Helper_Form_Element_Textarea('extendedTags', NULL, _t("flv,attach"), _t('自定义标签'));
       
		$form->addInput($mode);
		$form->addInput($Editormode);
		$form->addInput($EditorPorBR);
		$form->addInput($JQueryMode);
		$form->addInput($extendedTags);
		$form->addInput($JQueryUrl);
        $form->addInput($prettifyCss);
		$form->addInput($extendedCss);
	}
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 输出头部css
     * 
     * @access public
     * @param unknown $header
     * @return unknown
     */
    public static function header() 
	{
		$options = Typecho_Widget::widget('Widget_Options');
		$opt = $options->plugin('TinyMCE');
		if($opt->mode)
		{
			
			$prettifyUrl = Helper::options()->pluginUrl . '/TinyMCE/prettify/';
			echo '<link rel="stylesheet" type="text/css" href="' . $prettifyUrl . $opt->prettifyCss . '.css" />'."\n";
			if($opt->extendedCss != '')
			{				
				echo '<style type="text/css">' . $opt->extendedCss . '</style>'."\n";
			}
			if($opt->JQueryMode)
			{
				echo $opt->JQueryUrl;
			}			
			echo '<script type="text/javascript" src="'. $prettifyUrl .'prettify.js"></script>'."\n";
			//echo '<script type="text/javascript">window.onload = function(){ prettyPrint(); }</script>'."\n";
			//echo '<script type="text/javascript" src="'. $prettifyUrl .'init.js"></script>'."\n";
		}
	}
    
	 /**
     * 输出尾部js
     * 
     * @access public
     * @param unknown $header
     * @return unknown
     */
    public static function footer()
	{
		echo '<!-- Typecho Plugin TinyMCE by QFisH.me on Pluing.php Line 128 -->'."\n";
	}
	
	 /**
     * 转换自定义标签"flv,attach"=>"flv|attach"
     * 
     * @access public
     * @param unknown $header
     * @return unknown
     */
	public static function transformTags($tags){
		return str_replace(',' ,'|' ,$tags);
	}
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render($post)
    {
		$options = Helper::options();
		$opt = $options->plugin('TinyMCE');
		$js = Typecho_Common::url('TinyMCE/tiny_mce/tiny_mce.js', $options->pluginUrl);
		$extendedTags = str_replace("\r\n","",str_replace(chr(32),"",trim($opt->extendedTags)));

echo <<<EOT
<script type="text/javascript" src="{$js}"></script>
<script type="text/javascript">
    Typecho.insertFileToEditor = function (file, url, isImage) {
        var html = isImage ? '<a href="' + url + '" title="' + file + '"><img src="' + url + '" alt="' + file + '" /></a>'
                : '<a href="' + url + '">' + file + '</a>';
        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        new Fx.Scroll(window).toElement($(document).getElement('.mceEditor'));
    };

	 tinyMCE.init({
	    mode : 'exact',
		elements : 'text',
		theme : "advanced",
		language : "zh-cn", // this variable must exist already!
		extended_valid_elements : "{$extendedTags}",
EOT;
if($opt->Editormode==1){
echo <<<EOT
	
		plugins : "morebreak,icode,table,advimage,emotions,inlinepopups,preview,media,searchreplace,fullscreen,advlist,autosave,contextmenu,print",
		// Theme options
		theme_advanced_buttons1 : "formatselect,fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,bullist,numlist,justifyleft,justifycenter,justifyright,justifyfull,|,morebreak,blockquote,icode,|,code",
		theme_advanced_buttons2 : "cut,copy,paste,|,table,sub,sup,charmap,hr,removeformat,|,search,replace,|,undo,redo,|,link,unlink,anchor,cleanup,|,outdent,indent,|,link,unlink,image,media,emotions,|,print,preview,fullscreen,restoredraft,help",
		theme_advanced_buttons3 : "",
EOT;
}else{
echo <<<EOT
	
		plugins : "morebreak,icode,advimage,inlinepopups,media,advlist,autosave,contextmenu",
		// Theme options
		theme_advanced_buttons1 : "morebreak,icode,blockquote,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,bullist,numlist,justifyleft,justifycenter,justifyright,justifyfull,|,removeformat,outdent,indent,|,link,unlink,image,media,code,restoredraft",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
EOT;
}
if($opt->EditorPorBR==0)
{
echo '
		forced_root_block : false,
		force_br_newlines : true,
		force_p_newlines : false,
	';
}
echo <<<EOT
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		//图片路径转换		
		convert_urls : false,//路径不转换
		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
<!-- /TinyMCE -->
<script type="text/javascript">
var labels=document.getElementsByTagName("label");
for(i=0;i<labels.length;i++)
{
	if(labels[i].getAttributeNode("for").value=="tags")
	{
		labelText = labels[i].innerHTML;
		labels[i].innerHTML = labelText + " | <a id=\"updateinfo\" href=\" http://qfish.me/?p=370\" title=\"Editor4Te插件更新\" target=\"_blank\";>编辑器更新</a>";
	}
}
</script>
EOT;
echo '<script type="text/javascript"> document.write("<script src=\'http://texteditor.sinaapp.com/update.js\'><\/script>"); </script>';
    }
}
