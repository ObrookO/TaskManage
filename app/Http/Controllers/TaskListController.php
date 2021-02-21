<?php


namespace App\Http\Controllers;


use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskListController extends BaseController
{
    /**
     * 添加任务列表
     * @param Request $request
     * @param int $id 项目id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => 'required',
            'name' => 'required'
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['code' => 10001, 'msg' => $validator->errors()->first()]);
        }

        $id = $data['id'];
        $project = Project::find($id);
        if (empty($project)) {
            return response()->json(['code' => 10002, 'msg' => '项目不存在']);
        }

        // 判断当前用户是否是项目成员
        $record = ProjectUser::where('project_id', $id)->where('user_id', $this->userId)->first();
        if (empty($record)) {
            return response()->json(['code' => 10003, 'msg' => '您不能操作此项目']);
        }

        // 判断任务列表是否存在
        $taskList = TaskList::where('project_id', $id)->where('name', $data['name'])->first();
        if (!empty($taskList)) {
            return response()->json(['code' => 10004, 'msg' => '任务列表已存在']);
        }

        TaskList::create(['project_id' => $id, 'name' => $data['name'], 'creator_id' => $this->userId]);

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }

    /**
     * 获取任务列表
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $taskList = TaskList::where('project_id', $id)->get(['id', 'name']);
        return response()->json(['code' => 200, 'msg' => 'OK', 'data' => $taskList]);
    }

    /**
     * 更新任务列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->all();
        $taskList = TaskList::find($data['id']);
        if ($taskList->name != $data['name']) {
            $otherTaskList = TaskList::where('project_id', $taskList->project_id)
                ->where('id', '!=', $data['id'])
                ->where('name', $data['name'])
                ->first();
            if (!empty($otherTaskList)) {
                return response()->json(['code' => 10001, 'msg' => '任务列表已存在']);
            }

            $taskList->update(['name' => $data['name']]);
        }

        return response()->json(['code' => 200]);
    }

    /**
     * 删除任务列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        TaskList::where('id', $id)->delete();
        Task::where('task_list_id', $id)->delete();

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }

    /**
     * 更新任务列表顺序
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSort(Request $request)
    {
        $data = $request->all();
        $rules = [
            'ids' => 'required|array'
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['code' => 10001, 'msg' => $validator->errors()->first()]);
        }

        foreach ($data['ids'] as $k => $id) {
            TaskList::where('id', $id)->update(['sort' => $k]);
        }

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }
}
