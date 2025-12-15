markdown
# maccmsV10

## 使用方法

### 推荐使用
- **aapanel**

### 下载命令
```bash
URL=https://www.aapanel.com/script/install_7.0_en.sh && if [ -f /usr/bin/curl ];then curl -ksSO "$URL" ;else wget --no-check-certificate -O install_7.0_en.sh "$URL";fi;bash install_7.0_en.sh aapanel
```

### 替换原本的站点文件所有文件


### 修改配置
- 修改 `application/database.php` 中的数据库名字、用户名、密码，以及表前缀（如 `mac_`）。
- 修改完成后，重新放回模板即可正常使用原版的所有功能。
- 注意：直接使用 `install.php` 重新下载可能会被脚本检查，容易被远程覆盖（例如 `http://update.maccms.la`）。


祝您使用愉快！
