<?php

require 'vendor/autoload.php';

if (is_readable(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__, '/../.env');
    $dotenv->overload();
}

$server_name = $argv[1];
$uptime_now = $argv[2];
$uptime_threshold_old = $argv[3];
$uptime_threshold_new = $argv[4];

$ridi_id = $_ENV['ASANA_ORGANIZATION_ID'];
$project_name = $_ENV['ASANA_PROJECT_NAME'];
$asana_token = $_ENV['ASANA_ACCESS_TOKEN'];

$task_name = "${server_name} Uptime ${uptime_threshold_old} 초과";
$task_note = "(스크립트에서 자동 생성된 Task입니다.)\n"
            ."${server_name}의 현재 uptime = ${uptime_now}\n"
            ."이전 threshold인 ${uptime_threshold_old}을 초과했습니다.\n"
            ."\n"
            ."반복 통지를 막기 위해 warning threshold를 ${uptime_threshold_new}으로 재설정했습니다.\n"
            ."재부팅 이후 원래 값으로 변경 부탁드립니다.";

$client = Asana\Client::accessToken($asana_token);
$me = $client->users->me();

$projects = $client->projects->findByWorkspace($ridi_id, null, array('iterator_type' => false, 'page_size' => null))->data;
$projectArray = array_filter($projects, function($project) use ($project_name) { return $project->name == $project_name; });
$project = array_pop($projectArray);

$task = $client->tasks->createInWorkspace($ridi_id, array(
    'name' => $task_name,
    'notes' => $task_note,
    'projects' => array($project->id)
));

$client->tasks->addSubtask($task->id, [
    'name' => 'uptime warning값 복구',
]);

$client->tasks->addSubtask($task->id, [
    'name' => '재부팅',
]);
