<?php
require 'vendor/autoload.php';

$server_name = $argv[1];
$uptime_now = $argv[2];
$uptime_threshold_old = $argv[3];
$uptime_threshold_new = $argv[4];

$ridi_id = <RIDI-PROJECT-ID>;
$project_name = "[인프라] 서비스";
$task_name = $server_name.' Uptime 초과 ('.$uptime_now.' > '.$uptime_threshold_old.')';
$task_note = "(스크립트에서 자동 생성된 Task입니다.)\n"
            .$server_name.'의 현재 uptime => '.$uptime_now."\n"
            .$uptime_threshold_old."을 초과했습니다.\n"
            ."\n"
            .'경고 기준은 '.$uptime_threshold_new.'로 재설정되었습니다.';

$client = Asana\Client::accessToken('<ASANA-ACCESS-TOKEN>');
$me = $client->users->me();

$projects = $client->projects->findByWorkspace($ridi_id, null, array('iterator_type' => false, 'page_size' => null))->data;
$projectArray = array_filter($projects, function($project) use ($project_name) { return $project->name == $project_name; });
$project = array_pop($projectArray);

$demoTask = $client->tasks->createInWorkspace($ridi_id, array(
    'name' => $task_name,
    'notes' => $task_note,
    'projects' => array($project->id)
));
