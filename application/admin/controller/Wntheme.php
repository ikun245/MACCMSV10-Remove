<?php

namespace app\admin\controller;
use think\Db;

defined('ENTRANCE') or exit('Access Denied');

/**
**************************************************
* 作者: @haixianmian8
* Telegram: https://t.me/haixianmian8
* 版本: 2.6.0
* 最後更新於: 2024-09-05
**************************************************
*/
class Wntheme extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function theme()
    {
        $tid = input('tid');
        $wid = input('wid') ?: $tid;
        $url = input('url');
        //$thene_file = input('theme_file') ?: 'theme.html';

        if(empty($wid)){
            echo "錯誤: 網站ID未設定<br>";
            echo "請確認後台左邊的自定義快捷菜單的鏈接中包括<span style='color:red;font-weight:bold'>wid</span>參數<br>";
            echo "格式: wntheme/theme?tid=模板ID&<span style='color:red;font-weight:bold'>wid=自定網站ID</span><br><br>";
            echo "例如:<br>";
            echo "主題設定,wntheme/theme?tid=wntheme99&<span style='color:red;font-weight:bold'>wid=mywebsite1</span>";
            die;
        }

        if(empty($tid)){
            echo "錯誤: 模板ID未設定<br>";
            echo "請確認後台左邊的自定義快捷菜單的鏈接中包括<span style='color:red;font-weight:bold'>tid</span>參數<br>";
            echo "格式: wntheme/theme?<span style='color:red;font-weight:bold'>tid=模板ID</span>&wid=自定網站ID<br><br>";
            echo "例如:<br>";
            echo "主題設定,wntheme/theme?<span style='color:red;font-weight:bold'>tid=wntheme99</span>&wid=mywebsite1";
            die;
        }

        if (Request()->isPost()) {

            $config = input();
            $validate = \think\Loader::validate('Token');
            if(!$validate->check($config)){
                return $this->error($validate->getError());
            }
            unset($config['__token__']);

            $config_new = $this->compatible($tid, $config);
            $config_old = config($wid) !== null ? config($wid) : [];
            $config_new = array_merge($config_old, $config_new);

            $res = mac_arr2file(APP_PATH . "extra/{$wid}.php", $config_new);
            if ($res === false) {
                return $this->error(lang('save_err'));
            }
            return $this->success(lang('save_ok'));
        }

        $config = config($wid);
        if(empty($config)){
            $config = require ROOT_PATH . "template/{$tid}/admin/config/{$tid}.php";
        }
        
        $info_file = ROOT_PATH . "template/{$tid}/info.ini";
        $version = parse_ini_file($info_file)['version'] ?? '';

        $option_path = ROOT_PATH . "template/{$tid}/admin/config/options.php";
        if(!file_exists($option_path)){
            echo "找不到模板選項文件";
            echo "<br>";
            echo "請確認 <span style='color:red;font-weight:bold'>" . $option_path . "</span> 是否存在";
            die;
        }else{
            require $option_path;
        }
        
        foreach($tabs as $tab_index => $tab_content){
            foreach($tab_content['options'] as $option_index => $option){
                if(strpos($option['name'], '.') !== false){
                    
                    $tabs[$tab_index]['options'][$option_index]['name'] = str_replace(".", "][", $option['name']);

                    $nested_options = explode(".", $option['name']);
                    $option['name'] = implode("][", $nested_options);

                    $value = null;
                    foreach($nested_options as $key){
                        if(isset($value)){
                            if(isset($value[$key])){
                                $config[$option['name']] = $value[$key];
                            }
                        }elseif(is_array($config[$key])){
                            $value = $config[$key];
                        }else{
                            $config[$option['name']] = $value;
                        }
                    }
                }
            }
            
        }

        $this->assign('wid', $wid);
        $this->assign('tid', $tid);
        $this->assign('url', $url);
        $this->assign('version', $version);

        $this->assign('tabs', $tabs);
        $this->assign('config', $config);

        return $this->fetch("../../../template/{$tid}/admin/config/theme");
        //return $this->fetch("admin@wntheme/theme");
    }
    
    public function compatible($tid, $config)
    {
        if($tid == 'wntheme16'){
            $nav = ['type','mid','aid','top','bottom'];
            foreach($nav as $k=>$v){
                $a = implode(",", $config['nav'][$v]['PC']);
                $b = implode(",", $config['nav'][$v]['mobile']);
                $config['nav'][$v]['PC'] = $a;
                $config['nav'][$v]['mobile'] = $b;
            }
            $config['search']['filter'] = implode(",", $config['search']['filter']);
            $config['actor']['show']['filter'] = implode(",", $config['actor']['show']['filter']);
            return $config;
        }else{
            return $config['param'] ?? [];
        }
    }
}