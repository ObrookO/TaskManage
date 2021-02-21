<?php


namespace App\Http\Controllers;


use App\Models\Project;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends BaseController
{
    /**
     * 添加任务
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => 'required',
            'project_id' => 'required',
            'name' => 'required',
        ];
        $message = [
            'name.required' => '任务标题不能为空'
        ];

        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return response()->json(['code' => 10001, 'msg' => $validator->errors()->first()]);
        }

        $task = Task::where('project_id', $data['project_id'])->where('name', $data['name'])->first();
        if (!empty($task)) {
            return response()->json(['code' => 10002, 'msg' => '任务已存在']);
        }

        Task::create([
            'project_id' => $data['project_id'],
            'task_list_id' => $data['id'],
            'name' => $data['name'],
            'creator_id' => $this->userId,
            'user_id' => $data['user'] ?? 0
        ]);

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }

    /**
     * 获取任务信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Task::with(['project:id,name', 'taskList:id,name', 'parent:id,name', 'user:id,name'])->find($id);
        if (empty($task)) {
            return response()->json(['code' => 10001, 'msg' => '任务不存在']);
        }

        if (!empty($task->deadline)) {
            $task->deadline = date('Y-m-d\TH:i', strtotime($task->deadline));
        }

        $projects = Project::orderBy('id')->get(['id', 'name']);
        $taskList = TaskList::where('project_id', $task->project_id)->orderBy('sort')->get(['id', 'name']);
        $tasks = Task::where('id', '!=', $id)->where('project_id', $task->project_id)->get(['id', 'name']);
        $users = User::orderBy('id')->get(['id', 'name']);

        return response()->json(['code' => 200, 'msg' => 'OK', 'data' => [
            'task' => $task,
            'projects' => $projects,
            'taskList' => $taskList,
            'tasks' => $tasks,
            'users' => $users
        ]]);
    }

    /**
     * 更新任务
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->except('_token');
        $id = $data['id'];
        unset($data['id']);
        Task::where('id', $id)->update($data);
        return response()->json(['code' => 200, 'msg' => 'OK']);
    }
}
