<?php


namespace App\Http\Controllers;


use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends BaseController
{
    private $title = 'Project';

    /**
     * 添加项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required|unique:projects,name',
            'users' => 'array|max:10'
        ];
        $messages = [
            'name.required' => '请输入项目名称',
            'name.unique' => '项目已存在',
            'users.array' => '请选择正确的项目成员',
            'users.max' => '项目成员最多可选10个'
        ];

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['code' => 10001, 'msg' => $validator->errors()->first()]);
        }

        $data['users'][] = $this->userId;
        $project = Project::create(['name' => $data['name'], 'creator_id' => $this->userId]);
        if (!empty($data['users'])) {
            $users = array_map(function ($item) use ($project) {
                return [
                    'project_id' => $project->id,
                    'user_id' => $item,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }, $data['users']);

            ProjectUser::insert($users);
        }

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }

    /**
     * 项目详情
     * @param int $id 项目id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $project = Project::find($id);
        if (empty($project)) {
            abort(404);
        }

        // 判断当前用户是否是此项目的参与者
        $projectUserIds = $project->users->pluck('id')->toArray();
        if (!in_array($this->userId, $projectUserIds)) {
            abort(404);
        }

        $taskList = TaskList::where('project_id', $id)
            ->with(['tasks'])
            ->orderBy('sort')
            ->get();

//        foreach ($taskList as $k => $item) {
//            $taskList[$k]['tasks'] = $this->formatTasks($item->tasks);
//        }

        return view('projects.show', [
            'title' => $this->title,
            'project' => $project,
            'taskList' => $taskList
        ]);
    }

    private function formatTasks($tasks)
    {
        $newTasks = [];
        $sTasks = [];

        foreach ($tasks as $t) {
            if ($t->pid != 0) {
                $sTasks[$t->pid][] = $t;
            }
        }
        foreach ($tasks as $t) {
            if ($t->pid == 0) {
                $newTasks[$t->id] = $t;
                $newTasks[$t->id]['children'] = $sTasks[$t->id];
            }
        }

        return $newTasks;
    }

    /**
     * 获取项目信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        $id = $request->input('id');
        $project = Project::find($id);
        if ($this->userId != $project->creator_id) {
            return response()->json(['code' => 10001, 'msg' => '获取项目信息失败']);
        }

        $project->uids = $project->users->filter(function ($item) {
            return $item->id != $this->userId;
        })->pluck('id');
        unset($project->users);
        return response()->json(['code' => 200, 'msg' => 'OK', 'data' => $project]);
    }

    /**
     * 删除项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        $project = Project::find($id);
        if ($this->userId != $project->creator_id) {
            return response()->json(['code' => 10001, 'msg' => '暂无删除此项目的权限']);
        }

        $project->delete();
        ProjectUser::where('project_id', $id)->delete();
        TaskList::where('project_id', $id)->delete();
        Task::where('project_id', $id)->delete();

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }

    /**
     * 更新项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => 'required',
            'name' => 'required',
            'users' => 'array|max:10'
        ];
        $messages = [
            'id.required' => '参数错误',
            'name.required' => '请输入项目名称',
            'users.array' => '请选择正确的项目成员',
            'users.max' => '项目成员最多可选10个'
        ];

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['code' => 10001, 'msg' => $validator->errors()->first()]);
        }

        $project = Project::find($data['id']);
        if (empty($project)) {
            return response()->json(['code' => 10002, 'msg' => '参数错误']);
        }

        $otherProjects = Project::where('name', $data['name'])->where('id', '!=', $data['id'])->count();
        if (!empty($otherProjects)) {
            return response()->json(['code' => 10003, 'msg' => '项目已存在']);
        }

        $data['users'][] = $this->userId;
        ProjectUser::where('project_id', $data['id'])->whereNotIn('user_id', $data['users'])->delete();

        foreach ($data['users'] as $item) {
            ProjectUser::updateOrCreate(['project_id' => $data['id'], 'user_id' => $item]);
        }

        return response()->json(['code' => 200, 'msg' => 'OK']);
    }

}
