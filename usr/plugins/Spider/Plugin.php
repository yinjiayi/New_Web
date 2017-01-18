<?php
/**
 * 采集插件
 *
 * @package Spider
 * @author Syan
 * @version 1.0.0
 * @link http://onoboy.com
 */
class Spider_Plugin implements Typecho_Plugin_Interface
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
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $tables = $db->fetchAll($db->query('show tables'));
        foreach ($tables as $key => $value) {
            foreach ($value as $k => $v) {
                    $table[] = $v;
            }   
        }
        if(! in_array($prefix.'spider',$table))
        {
            $db->query("CREATE TABLE IF NOT EXISTS `".$prefix."spider` (
              `sid` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) CHARACTER SET utf8 NOT NULL,
              `url` varchar(255) CHARACTER SET utf8 NULL DEFAULT NULL,
              `isutf8` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'utf-8',
              `metaid` int(11) NOT NULL DEFAULT '1',
              `content_test_url` varchar(255) CHARACTER SET utf8 NOT NULL,
              `list_url` varchar(255) CHARACTER SET utf8 NOT NULL,
              `start` int(11) NOT NULL,
              `end` int(11) NOT NULL,
              `content_urls` text CHARACTER SET utf8 NOT NULL,
              `title` text CHARACTER SET utf8 NOT NULL,
              `title_a` text CHARACTER SET utf8,
              `title_b` text CHARACTER SET utf8,
              `content` text CHARACTER SET utf8 NOT NULL,
              `content_a` text CHARACTER SET utf8,
              `content_b` text CHARACTER SET utf8,
              `content_tag` text CHARACTER SET utf8,
              PRIMARY KEY (`sid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ");
        }
        Helper::addAction('Spider', 'Spider_Action');
        Helper::addPanel(3, 'Spider/panel.php', _t('采集管理'), _t('采集管理'), 'administrator');
        if(in_array($prefix.'spider',$table))
        {
            return('数据表 '.$prefix.'spider 已经存在, 插件已经成功激活!');
        }
        else
        {
            return('数据表 '.$prefix.'spider 创建成功, 插件已经成功激活!');
        }
        
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
        Helper::removeAction('Spider');
        Helper::removePanel(3, 'Spider/panel.php');
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){}

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

}
