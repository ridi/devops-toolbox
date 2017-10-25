<?php

require 'vendor/autoload.php';

if (is_readable(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__, '/../.env');
    $dotenv->overload();
}



// Get variables from args
$server_name = $argv[1];
$id_and_group = explode('@', $server_name);
$server_id = isset($id_and_group[0]) ? $id_and_group[0] : null;
$server_group = isset($id_and_group[1]) ? $id_and_group[1] : null;

$team = null;
$role = null;
if ($server_group) {
    $team_and_role = explode('-', $server_group);
    $team = isset($team_and_role[0]) ? $team_and_role[0] : null;
    $role = isset($team_and_role[1]) ? $team_and_role[1] : null;
}

$uptime_now = $argv[2];
$uptime_threshold_old = $argv[3];
$uptime_threshold_new = $argv[4];



// Get varibles from envs
$ridi_id = $_ENV['ASANA_ORGANIZATION_ID'];
$project_name = $_ENV['ASANA_PROJECT_NAME'];
$asana_token = $_ENV['ASANA_ACCESS_TOKEN'];

$assignee_email = null;
$section_name = null;
if (isset($team)) {
    $assignee_env_index = 'ASANA_ASSIGNEE_' . strtoupper($team);
    $assignee_email = isset($_ENV[$assignee_env_index]) ? $_ENV[$assignee_env_index] : null;

    $section_env_index = 'ASANA_SECTION_' . strtoupper($team);
    $section_name = isset($_ENV[$section_env_index]) ? $_ENV[$section_env_index] : null;
}



// Search project
$client = Asana\Client::accessToken($asana_token);

$projects = $client->projects->findByWorkspace($ridi_id, null, ['iterator_type' => false, 'page_size' => null])->data;
$project_arr = array_filter($projects, function($project) use ($project_name) {
    return $project->name == $project_name;
});
$project = array_pop($project_arr);



// Create task
$task_name = "${server_name} Uptime ${uptime_threshold_old} 초과";
$task_note = "(스크립트에서 자동 생성된 Task입니다.)\n"
    ."${server_name}의 현재 uptime = ${uptime_now}\n"
    ."이전 threshold인 ${uptime_threshold_old}을 초과했습니다.\n"
    ."\n"
    ."반복 통지를 막기 위해 warning threshold를 ${uptime_threshold_new}으로 재설정했습니다.\n"
    ."재부팅 이후 원래 값으로 변경 부탁드립니다.";

$task_data = [
    'name' => $task_name,
    'notes' => $task_note,
    'projects' => [ $project->id ],
];

if (isset($assignee_email)) {
    $task_data['assignee'] = [ 'email' => $assignee_email ];
}

$task = $client->tasks->createInWorkspace($ridi_id, $task_data);



// Search section
if (isset($section_name)) {
    $sections = $client->projects->sections($project->id, null, ['iterator_type' => false, 'page_size' => null])->data;
    $section_arr = array_filter($sections, function($section) use ($section_name) {
        return $section->name == $section_name;
    });
    $section = array_pop($section_arr);

    if (isset($section)) {
        $client->tasks->addProject($task->id, [
            'project' => $project->id,
            'section' => $section->id,
        ]);
    }
}



// Add subtasks
$client->tasks->addSubtask($task->id, [ 'name' => 'uptime warning값 복구' ]);
$client->tasks->addSubtask($task->id, [ 'name' => '재부팅' ]);
