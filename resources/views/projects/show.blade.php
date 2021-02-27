@extends('layouts.layout')

@section('title')
    {{ $title }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/summernote/summernote-bs4.css') }}">

    <style>
        .create-task:hover {
            color: #1b9aee;
        }

        .task-item {
            cursor: pointer;
            border-radius: 0.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            background-color: #ffffff;
            border-left: 10px solid #ffffff;
            margin-bottom: 1rem;
            padding: 0.8rem 0.8rem 0.1rem 0.5rem;
        }

        .create-task-container {
            display: none;
        }

        .task-info {
            word-break: break-all;
        }

        .task-info .delete-task {
            display: none;
        }

        .active-task {
            border-left: 10px solid #1b9aee;
        }

        .finished-task {
            color: #8a8a8a;
            text-decoration: line-through;
        }

        #task-detail-container {
            width: 60%;
            border-radius: 0;
            position: fixed;
            top: 0;
            right: 0;
            z-index: 9999;
            display: none;
        }

        #task-detail-container .card-header {
            padding: 0.6rem 1.25rem;
        }

        #task-detail-container .card-body {
            height: 90%;
            overflow: auto;
        }

        #task-detail-container .custom-div {
            cursor: pointer;
            display: block;
            width: 100%;
            padding: .375rem .75rem .375rem 0;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border-radius: .25rem;
            box-shadow: inset 0 0 0 transparent;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        #task-detail-container #change-task-status,
        #task-detail-container #change-task-note,
        #task-detail-container #change-task-project,
        #task-detail-container #change-task-list,
        #task-detail-container #change-task-operator,
        #task-detail-container #add-children-task {
            display: none;
        }

        #task-detail-container #task-note {
            min-height: calc(2.25rem + 2px);
            border: 1px solid #ced4da;
        }

        #task-detail-container .detail-label {
            color: #a1a8ae;
        }

        .task-name-active {
            background: #f0f0f0;
            border-radius: 5px;
        }

        #task-detail-container .textarea-options,
        #task-detail-container .position-options {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="card" id="task-detail-container">
        <div class="card-header">
            <div class="card-title mb-1" id="task-name"
                 style="width: 500px;font-size: 22px;word-break: break-all;cursor: pointer;"
                 onclick="editTrue(this)" onblur="updateTaskName(this)">
            </div>
            <div class="card-tools">
                <button class="btn btn-sm" onclick="closeDetail(this)"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="card-body">
            <form class="form-horizontal">
                <input type="hidden" id="task-id">
                <input type="hidden" id="project-id">
                <input type="hidden" id="task-list-id">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label detail-label" for="task-status">
                        <i class="fa fa-check-square"></i>&nbsp;&nbsp;&nbsp;&nbsp;状态
                    </label>
                    <div class="col-lg-4">
                        <div class="custom-div" id="task-status" onclick="showStatusSelect(this)"></div>
                        <select id="change-task-status" class="form-control" onblur="hideStatusSelect(this)"
                                onchange="updateTaskStatus(this)">
                            <option value="0">未完成</option>
                            <option value="1">已完成</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label detail-label" for="task-project">
                        <i class="fa fa-parking"></i>&nbsp;&nbsp;&nbsp;&nbsp;位置
                    </label>
                    <div class="col-lg-4">
                        <div class="custom-div" id="task-position" onclick="showProjectSelect(this)"></div>
                    </div>
                    <div class="col-lg-4" id="change-task-project">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">项目</span>
                            </div>
                            <select class="form-control" onchange="getProjectTaskList(this)"></select>
                        </div>
                    </div>
                    <div class="col-lg-4" id="change-task-list">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">列表</span>
                            </div>
                            <select class="form-control"></select>
                        </div>
                    </div>
                </div>
                <div class="form-group row position-options">
                    <div class="col-lg-10">
                        <button type="button" class="btn btn-primary btn-sm float-right"
                                onclick="updateTaskProject(this)">
                            保存
                        </button>
                        <button type="button" class="btn btn-default btn-sm float-right mr-1"
                                onclick="hideProjectSelect(this)">
                            取消
                        </button>
                    </div>
                </div>
                <div class="form-group row clearfix">
                    <label class="col-lg-2 col-form-label detail-label" for="task-operator">
                        <i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;&nbsp;执行者
                    </label>
                    <div class="col-lg-4">
                        <div class="custom-div" id="task-operator" onclick="showOperatorSelect(this)"></div>
                        <select id="change-task-operator" class="form-control" onblur="hideOperatorSelect(this)"
                                onchange="updateTaskOperator(this)"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label detail-label" for="task-deadline">
                        <i class="fa fa-calendar-alt"></i>&nbsp;&nbsp;&nbsp;&nbsp;截止时间
                    </label>
                    <div class="col-lg-4">
                        <input type="datetime-local" id="task-deadline" class="form-control"
                               onblur="updateTaskDeadline(this)">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label detail-label" for="task-operator">
                        <i class="fa fa-list"></i>&nbsp;&nbsp;&nbsp;&nbsp;子任务
                    </label>
                    <div class="col-lg-8">
                        <div id="children-task-container"></div>
                        <div id="add-children-task" class="custom-div">
                            <input type="text" class="form-control" id="children-task-name"
                                   onblur="addChildrenTask(this)">
                        </div>
                        <div class="custom-div">
                            <button type="button" class="btn btn-outline-secondary btn-xs"
                                    onclick="showAddChildrenTask()">
                                <i class="fa fa-plus"></i> 添加
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label detail-label" for="task-note">
                        <i class="fa fa-file-alt"></i>&nbsp;&nbsp;&nbsp;&nbsp;备注
                    </label>
                    <div class="col-lg-9">
                        <div id="task-note" class="custom-div" onclick="showTextarea(this)"></div>
                        <textarea id="change-task-note" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group row textarea-options">
                    <div class="col-lg-11">
                        <button type="button" class="btn btn-primary btn-sm float-right" onclick="updateTaskNote(this)">
                            保存
                        </button>
                        <button type="button" class="btn btn-default btn-sm float-right mr-1" onclick="hideTextarea()">
                            取消
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1>{{ $project->name }}</h1>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-2">
                    <button class="btn btn-secondary" data-toggle="modal" data-target="#create-list-modal">
                        <i class="fa fa-plus"></i> 新建任务列表
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="task-list-container">
                {{-- 任务列表 --}}
                @foreach($taskList as $list)
                    <div class="col-lg-3">
                        <div class="card card-info list-info" id="task-list{{$list->id}}"
                             data-task-list-id="{{ $list->id }}"
                             data-project-id="{{ $project->id }}">
                            {{-- 操作任务列表 --}}
                            <div class="card-header d-flex">
                                <h3 class="card-title">{{ $list->name }}</h3>
                                <ul class="nav nav-pills ml-auto" style="cursor: pointer">
                                    <li class="nav-item dropdown-toggle" data-toggle="dropdown">
                                        <div class="dropdown-menu">
                                            <a href="javascript:;" class="dropdown-item text-dark"
                                               onclick="showContainer(this)">
                                                <i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;添加任务
                                            </a>
                                            <a href="javascript:;" class="dropdown-item text-dark"
                                               onclick="showUpdateModal('{{ $list->id }}', '{{ $list->name }}')">
                                                <i class="fa fa-pencil-alt"></i>&nbsp;&nbsp;&nbsp;修改任务列表
                                            </a>
                                            <a href="javascript:;" class="dropdown-item text-danger"
                                               onclick="showDeleteModal('{{ $list->id }}')">
                                                <i class="fa fa-trash-alt"></i>&nbsp;&nbsp;&nbsp;删除任务列表
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                {{-- 创建任务表单 --}}
                                <div class="task-item create-task-container">
                                    <div class="form-group">
                                        <textarea id="task-name" cols="30" rows="3" class="form-control"
                                                  placeholder="输入标题以新建任务"></textarea>
                                        <p class="text-danger error"></p>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                                            </div>
                                            <select id="task-user" class="form-control">
                                                <option value="0">待认领</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-default btn-sm" onclick="hideContainer(this)">取消</button>
                                        <button class="btn btn-primary btn-sm float-right" onclick="createTask(this)">
                                            确定
                                        </button>
                                    </div>
                                </div>

                                {{-- 任务详情 --}}
                                <div class="task-info-container" data-task-list-id="{{ $list->id }}">
                                    @foreach($list->tasks as $task)
                                        <div class="task-item task-info" id="task{{$task->id}}"
                                             data-task-id="{{ $task->id }}">
                                            <div class="form-group">
                                                <div class="icheck-info d-inline">
                                                    <input type="checkbox" id="checkbox{{ $task->id }}"
                                                           @if($task->status == 1) checked
                                                           @endif onclick="changeTaskStatus(this)">
                                                    <label for="checkbox{{ $task->id }}"></label>
                                                    <span id="text{{ $task->id }}"
                                                          class="text @if($task->status == 1) finished-task @endif">{{ $task->name }}</span>
                                                </div>
                                            </div>
                                            <ol class="children-tasks">
                                                @foreach($task->children as $c)
                                                    <li class="children-task-item @if($c->status == 1) finished-task @endif"
                                                        id="children-task{{ $c->id }}">{{ $c->name }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 创建任务列表的模态框 --}}
        <div class="modal fade" id="create-list-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">添加任务列表</h4>
                        <button type="button" class="close" onclick="closeModal(this)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" id="id" value="{{ $project->id }}">
                            <div class="form-group">
                                <input type="text" class="form-control" id="name" placeholder="任务列表名称">
                            </div>
                            <div class="form-group">
                                <p class="error text-center text-danger"></p>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-primary confirm" onclick="createTaskList(this)">确定
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 修改任务列表的模态框 --}}
        <div class="modal fade" id="update-list-modal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">修改任务列表</h4>
                        <button type="button" class="close" onclick="closeModal(this)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" class="task-list-id">
                            <div class="form-group">
                                <input type="text" class="form-control" id="task-list-name" placeholder="任务列表名称">
                            </div>
                            <div class="form-group">
                                <p class="error text-center text-danger"></p>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary confirm" onclick="updateTaskList(this)">确定
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 删除任务列表的模态框 --}}
        <div class="modal fade" id="delete-list-modal">
            <div class="modal-dialog" style="width: 27%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">删除任务列表</h4>
                        <button type="button" class="close" onclick="closeModal(this)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" class="task-list-id">
                            <div class="form-group">
                                <p>
                                    删除任务列表将彻底清空此任务列表上的所有任务，<br>
                                    确定要删除整个任务列表？
                                </p>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-danger confirm" onclick="deleteTaskList(this)">确定
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('script')
    <script src="{{ asset('/plugins/drag/js/Sortable.min.js') }}"></script>
    <script src="{{ asset('/plugins/summernote/summernote-bs4.min.js') }}"></script>

    <script>
        let detailContainer = $('#task-detail-container');
        $(function () {
            detailContainer.css({'height': $(window).height()});
            $('.navbar, .content-header, .content').on('click', function () {
                if (detailContainer.css('display') === 'block') {
                    detailContainer.animate({width: 'toggle', opacity: 'toggle'});
                    $('body').css({'overflow': 'auto'});
                }
            });

            // 控制任务列表中任务的样式
            $('.task-info').on('mouseenter', function () {
                $(this).addClass('active-task')
            }).on('mouseleave', function () {
                $(this).removeClass('active-task')
            }).on('click', function (event) {
                let tag = event.target.nodeName.toLowerCase(),
                    id = $(this).data('task-id');

                if (tag !== 'label' && tag !== 'input') {
                    showDetail(id)
                }
            });


            // 任务列表拖动
            let taskListContainer = $('#task-list-container');
            new Sortable($(taskListContainer)[0], {
                group: 'task-list',
                animation: 200,
                onUpdate: function (e) {
                    let ids = [];
                    taskListContainer.find('.list-info').each(function () {
                        ids.push($(this).data('task-list-id'))
                    });

                    $.post('{{ route('task_list.update_sort') }}', {
                        _token: GlobalToken,
                        ids: ids
                    }, function (data) {
                        if (data.code !== 200) {
                            alert(data.msg);
                        }
                    }, 'json');
                }
            });

            // 任务拖动
            $('.task-info-container').each(function () {
                new Sortable($(this)[0], {
                    group: 'tasks',
                    animation: 200,
                    direction: 'horizontal',
                    onRemove: function (e) {
                        let taskId = $(e.item).data('task-id'),
                            newTaskListId = $(e.to).data('task-list-id');

                        updateTask({id: taskId, task_list_id: newTaskListId}, function (data) {
                            if (data.code !== 200) {
                                alert(data.msg)
                            }
                        });
                    }
                });
            });
        });

        function closeModal(obj) {
            let modal = $(obj).parents('.modal');
            modal.find('input').val('');
            modal.find('.error').text('');
            modal.modal('toggle');
        }

        function createTaskList(obj) {
            let modal = $(obj).parents('.modal'),
                id = modal.find('#id').val(),
                name = modal.find('#name').val();

            $.post('{{ route('task_list.store') }}', {
                _token: GlobalToken,
                id: id,
                name: name
            }, function (data) {
                if (data.code === 200) {
                    window.location.reload();
                } else {
                    modal.find('.error').text(data.msg);
                }
            }, 'json');
        }

        function showUpdateModal(id, name) {
            let modal = $('#update-list-modal');
            modal.find('.task-list-id').val(id);
            modal.find('#task-list-name').val(name);
            modal.modal('toggle');
        }

        function updateTaskList(obj) {
            let modal = $(obj).parents('.modal'),
                id = modal.find('.task-list-id').val(),
                name = modal.find('#task-list-name').val();

            modal.find('.error').text('');
            $.post('{{ route('task_list.update') }}', {
                _token: GlobalToken,
                id: id,
                name: name
            }, function (data) {
                if (data.code === 200) {
                    window.location.reload();
                } else {
                    modal.find('.error').text(data.msg)
                }
            }, 'json');
        }

        function showDeleteModal(id) {
            let modal = $('#delete-list-modal');
            modal.find('.task-list-id').val(id);
            modal.modal('toggle');
        }

        function deleteTaskList(obj) {
            let modal = $(obj).parents('#delete-list-modal'),
                id = modal.find('.task-list-id').val();

            $.post('{{ route('task_list.delete') }}', {
                _token: GlobalToken,
                id: id
            }, function (data) {
                window.location.reload();
            }, 'json');
        }

        function showContainer(obj) {
            $(obj).parents('.card-info').find('.create-task-container').show();
        }

        function hideContainer(obj) {
            let p = $(obj).parents('.create-task-container');
            p.find('#task-name').val('');
            p.find('#task-user').val(0);
            p.find('.error').text('');
            p.hide();
        }

        function createTask(obj) {
            let p = $(obj).parents('.create-task-container'),
                name = p.find('#task-name').val(),
                user = p.find('#task-user').val();

            $.post('{{ route('tasks.store') }}', {
                _token: GlobalToken,
                id: $(obj).parents('.card-info').data('task-list-id'),
                project_id: $(obj).parents('.card-info').data('project-id'),
                name: name,
                user: user
            }, function (data) {
                if (data.code === 200) {
                    window.location.reload();
                } else {
                    p.find('#task-name').addClass('is-invalid');
                    p.find('.error').text(data.msg);
                }
            }, 'json');
        }

        function changeTaskStatus(obj) {
            let p = $(obj).parents('.task-info'),
                id = p.data('task-id'),
                status = $(obj).prop('checked') ? 1 : 0;

            updateTask({id: id, status: status}, function (data) {
                let d = $('#task' + id),
                    c = $('.children-tasks').find('#children-task' + id);

                if (status === 1) {
                    d.find('span').addClass('finished-task');
                    d.find('input').prop('checked', true);
                    c.addClass('finished-task');
                } else {
                    d.find('span').removeClass('finished-task');
                    d.find('input').prop('checked', false);
                    c.removeClass('finished-task');
                }
            })
        }

        function showDetail(id) {
            detailContainer.hide();

            $.get('/tasks/' + id, {}, function (data) {
                if (data.code === 200) {
                    let info = data.data,
                        detail = info.task,
                        projects = info.projects,
                        children = detail.children,
                        taskList = info.taskList,
                        users = info.users,
                        projectsOptions = '',
                        childrenHtml = '',
                        taskListOptions = '',
                        usersOptions = '<option value="0">待认领</option>';

                    for (let i = 0; i < projects.length; i++) {
                        let p = projects[i];
                        projectsOptions += '<option value="' + p.id + '"' + (p.id == detail.project_id ? ' selected' : '') + '>' + p.name + '</option>';
                    }

                    for (let i = 0; i < taskList.length; i++) {
                        let t = taskList[i];
                        taskListOptions += '<option value="' + t.id + '"' + (t.id == detail.task_list_id ? ' selected' : '') + '>' + t.name + '</option>';
                    }

                    for (let i = 0; i < users.length; i++) {
                        let u = users[i];
                        usersOptions += '<option value="' + u.id + '"' + (u.id == detail.user_id ? ' selected' : '') + '>' + u.name + '</option>';
                    }

                    for (let i = 0; i < children.length; i++) {
                        let name = 'checkbox' + i,
                            c1 = children[i].status === 1 ? 'checked' : '',
                            c2 = children[i].status === 1 ? 'finished-task' : '';
                        childrenHtml +=
                            '<div class="task-info" data-task-id="' + children[i].id + '" id="task' + children[i].id + '" onmouseenter="showDelete(this)" onmouseleave="hideDelete(this)">' +
                            '<div class="custom-div">' +
                            '<div class="icheck-info d-inline">' +
                            '<input type="checkbox" ' + c1 + ' id="' + name + '" onclick="changeTaskStatus(this)">' +
                            '<label for="' + name + '"></label>' +
                            '<span class="children-task-name ' + c2 + '">' + children[i].name + '</span>' +
                            '<span class="delete-task float-right" onclick="deleteTask(this)">' +
                            '<i class="fa fa-trash-alt"></i>' +
                            '</span>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    }

                    // 填充select框
                    detailContainer.find('#change-task-project select').html(projectsOptions);
                    detailContainer.find('#change-task-list select').html(taskListOptions);
                    detailContainer.find('#change-task-operator').html(usersOptions);
                    detailContainer.find('#children-task-container').html(childrenHtml);

                    detailContainer.find('#task-id').val(detail.id);
                    detailContainer.find('#project-id').val(detail.project_id);
                    detailContainer.find('#task-list-id').val(detail.task_list_id);
                    detailContainer.find('#task-name').text(detail.name);
                    detailContainer.find('#task-status').text(detail.status === 0 ? '未完成' : '已完成');
                    detailContainer.find('#task-position').text(detail.project.name + ' / ' + detail.task_list.name);
                    detailContainer.find('#task-operator').text(detail.user ? detail.user.name : '待认领');
                    detailContainer.find('#task-deadline').val(detail.deadline);
                    detailContainer.find('#task-note').html(detail.note);

                    $('body').css({'overflow': 'hidden'});
                    detailContainer.animate({width: 'toggle', opacity: 'toggle'});
                } else {
                    alert(data.msg);
                }
            });
        }

        function closeDetail(obj) {
            detailContainer.animate({width: 'toggle', opacity: 'toggle'});
            $('body').css({'overflow': 'auto'});
        }

        function showDelete(obj) {
            $(obj).find('.delete-task').show();
        }

        function hideDelete(obj) {
            $(obj).find('.delete-task').hide();
        }

        function deleteTask(obj) {
            let id = $(obj).parents('.task-info').data('task-id');
            $.post('{{ route('tasks.delete') }}', {
                _token: GlobalToken,
                id: id
            }, function (data) {
                $('#children-task-container').find('#task' + id).remove();
                $('.children-tasks').find('#children-task' + id).remove();
            }, 'json');
        }

        function showStatusSelect(obj) {
            $(obj).hide().next('#change-task-status').show().focus();
        }

        function hideStatusSelect(obj) {
            $(obj).hide().siblings('#task-status').show();
        }

        function showProjectSelect(obj) {
            let p = $(obj).parent();
            p.hide().siblings('div').show();
            $('.position-options').show();
        }

        function hideProjectSelect(obj) {
            detailContainer.find('#change-task-project').hide();
            detailContainer.find('#change-task-list').hide();
            $(obj).parents('.position-options').hide();
            detailContainer.find('#task-position').parent().show();
        }

        function getProjectTaskList(obj) {
            $.get('/task_list/' + $(obj).val(), {}, function (data) {
                let options = '';
                for (let i = 0; i < data.data.length; i++) {
                    let list = data.data[i];
                    options += '<option value="' + list.id + '">' + list.name + '</option>';
                }

                detailContainer.find('#change-task-list select').html(options).focus();
            }, 'json')
        }

        function showOperatorSelect(obj) {
            $(obj).hide().next('#change-task-operator').show().focus();
        }

        function hideOperatorSelect(obj) {
            $(obj).hide().siblings('#task-operator').show();
        }

        function showAddChildrenTask() {
            $('#add-children-task').show();
            $('#children-task-name').focus();
        }

        function addChildrenTask(obj) {
            let project_id = detailContainer.find('#project-id').val(),
                task_list_id = detailContainer.find('#task-list-id').val(),
                pid = detailContainer.find('#task-id').val(),
                name = $(obj).val();

            if (name.length === 0) {
                $(obj).parent().hide();
                return;
            }

            $.post('{{ route('tasks.store') }}', {
                _token: GlobalToken,
                project_id: project_id,
                id: task_list_id,
                name: name,
                pid: pid
            }, function (data) {
                $(obj).val('').parent().hide();
                if (data.code === 200) {
                    let h =
                        '<div class="task-info" data-task-id="' + data.data + '" id="task' + data.data + '" onmouseenter="showDelete(this)" onmouseleave="hideDelete(this)">' +
                        '<div class="custom-div">' +
                        '<div class="icheck-info d-inline">' +
                        '<input type="checkbox" id="checkbox' + data.data + '" onclick="changeTaskStatus(this)">' +
                        '<label for="checkbox' + data.data + '"></label>' +
                        '<span class="children-task-name">' + name + '</span>' +
                        '<span class="delete-task float-right" onclick="deleteTask(this)">' +
                        '<i class="fa fa-trash-alt"></i>' +
                        '</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>',
                        l =
                            '<li class="children-task-item" id="children-task' + data.data + '">' + name + '</li>';

                    $('#children-task-container').append(h);
                    $('#task' + pid).find('.children-tasks').append(l);
                } else {
                    alert(data.msg);
                }
            }, 'json');
        }

        function showTextarea(obj) {
            let textarea = $(obj).next('#change-task-note'),
                html = $(obj).html();

            $(obj).hide();
            textarea.show();
            $('.textarea-options').show();
            textarea.summernote('code', html);
        }

        function hideTextarea() {
            let textarea = $('#change-task-note');
            textarea.summernote('destroy');
            textarea.hide();
            $('.textarea-options').hide();
            $('#task-note').show();
        }

        function editTrue(obj) {
            $(obj).attr('contenteditable', true).focus();
        }

        /**
         * 更新名称
         */
        function updateTaskName(obj) {
            let name = $(obj).text(),
                id = detailContainer.find('#task-id').val();

            updateTask({id: id, name: name}, function (data) {
                if (data.code === 200) {
                    $(obj).attr('contenteditable', false);
                    // 修改任务列表中的任务名称
                    $('.task-info-container #text' + id).text(name);
                } else {
                    alert(data.msg)
                }
            })
        }

        /**
         * 更新状态
         */
        function updateTaskStatus(obj) {
            let id = detailContainer.find('#task-id').val(),
                status = $(obj).val();

            updateTask({id: id, status: status}, function (data) {
                if (data.code === 200) {
                    let infoContainer = $('.task-info-container');
                    $(obj).hide();
                    detailContainer.find('#task-status').text($(obj).find('option[value="' + status + '"]').text()).show();
                    // 修改任务列表中任务的选中与取消
                    if (status == 0) {
                        infoContainer.find('#checkbox' + id).attr('checked', false);
                        infoContainer.find('#text' + id).removeClass('finished-task');
                    } else {
                        infoContainer.find('#checkbox' + id).attr('checked', true);
                        infoContainer.find('#text' + id).addClass('finished-task');
                    }
                } else {
                    alert(data.msg)
                }
            })
        }

        /**
         * 更新项目和任务列表
         */
        function updateTaskProject(obj) {
            let id = detailContainer.find('#task-id').val(),
                project_id = detailContainer.find('#change-task-project select').val(),
                task_list_id = detailContainer.find('#change-task-list select').val();

            if (task_list_id == null) {
                alert('无可用任务列表');
                return;
            }

            updateTask({id: id, project_id: project_id, task_list_id: task_list_id}, function (data) {
                if (data.code === 200) {
                    let project_name = detailContainer.find('#change-task-project :selected').text(),
                        task_list_name = detailContainer.find('#change-task-list :selected').text(),
                        original_project_id = detailContainer.find('#project-id').val(),
                        task = $('#task' + id);

                    detailContainer.find('#change-task-project').hide();
                    detailContainer.find('#change-task-list').hide();
                    detailContainer.find('.position-options').hide();
                    detailContainer.find('#task-position').text(project_name + ' / ' + task_list_name).parent().show();

                    task.remove();
                    if (project_id === original_project_id) {
                        $('#task-list' + task_list_id).find('.task-info-container').append(task);
                    }
                } else {
                    alert(data.msg)
                }
            });
        }

        /**
         * 更新执行人
         */
        function updateTaskOperator(obj) {
            let id = detailContainer.find('#task-id').val(),
                user_id = $(obj).val();

            updateTask({id: id, user_id: user_id}, function (data) {
                if (data.code === 200) {
                    $(obj).hide();
                    detailContainer.find('#task-operator').text($(obj).find(':selected').text()).show();
                } else {
                    alert(data.msg)
                }
            })
        }

        /**
         * 更新截止日期
         */
        function updateTaskDeadline(obj) {
            let id = detailContainer.find('#task-id').val(),
                deadline = $(obj).val();

            updateTask({id: id, deadline: deadline}, function (data) {
                if (data.code !== 200) {
                    alert(data.msg);
                }
            })
        }

        /**
         * 更新备注
         */
        function updateTaskNote(obj) {
            let textarea = detailContainer.find('#change-task-note'),
                taskId = detailContainer.find('#task-id').val(),
                note = textarea.summernote('code');

            updateTask({id: taskId, note: note}, function (data) {
                if (data.code === 200) {
                    textarea.summernote('destroy');
                    textarea.hide();
                    detailContainer.find('.textarea-options').hide();
                    detailContainer.find('#task-note').html(note).show();
                } else {
                    alert(data.msg)
                }
            })
        }

        /**
         * 更新任务信息
         * @param taskData obj 任务信息
         * @param fn function 回调函数
         */
        function updateTask(taskData, fn) {
            taskData._token = GlobalToken;
            $.post('{{ route('tasks.update') }}', taskData, fn, 'json')
        }
    </script>
@endsection
