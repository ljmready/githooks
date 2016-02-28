<?php
header("Content-type: text/html; charset=utf-8");

if (! isset($_REQUEST['hook'])) mylog ('非法请求');
$config = require("./config/config.php");
if(!$config['projects']){
    mylog('配置不存在');
}
//hook内容详见http://git.oschina.net/oschina/git-osc/wikis/HOOK%E9%92%A9%E5%AD%90
file_put_contents('data.json',$_REQUEST['hook']."\n", FILE_APPEND);
$hook = json_decode($_REQUEST["hook"], true);
//$hook = json_decode(file_get_contents('request.json'), true);
$project = $hook['push_data']['repository']['name'];

//判断密码
if ($hook['password'] != $config['projects'][$project]['password']) mylog("密码错误");
//判断branch
if (strpos($hook['push_data']['ref'] , $config['projects'][$project]['branch']) === false)
    mylog ("非自动部署分支\n".$hook['push_data']['ref']);
$shell = <<<EOF
WEB_PATH='{$config['projects'][$hook['push_data']['repository']['name']]['web_path']}'
WEB_USER='{$config['web_user']}'
WEB_GROUP='{$config['web_group']}'
echo "Start deployment"
cd \$WEB_PATH
echo "pulling source code..."
git pull -f
echo "changing permissions..."
chown -R \$WEB_USER:\$WEB_GROUP \$WEB_PATH
echo "Finished."
EOF;

file_put_contents('deploy.sh', $shell);
$res = shell_exec("bash deploy.sh");

$log_file = "{$project}.log";
foreach ($hook['push_data']['commits'] as $commit) {
    file_put_contents($log_file, 
        "※" . date('Y-m-d H:i:s') . "\t" . 
        $hook['push_data']['repository']['name'] . "\t" . 
        $commit['message'] . "\t" . 
        $commit['author']['name'] . PHP_EOL, 
        FILE_APPEND
    );
}
file_put_contents($log_file, $res . PHP_EOL, FILE_APPEND);

function mylog($msg){
    file_put_contents('RuntimeError.log',$msg.date("Y-m-d H:i:s")."\n", FILE_APPEND);
    exit;
}
