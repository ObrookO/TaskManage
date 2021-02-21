@extends('layouts.layout')

@section('title')
    {{ $title }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <style>
        .project-block {
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .project-block:hover {
            box-shadow: 2px 2px 5px #a1a8ae;
            position: relative;
            top: -5px;
            cursor: pointer;
        }

        .project-block .info-block {
            height: 135px;
            padding: 1rem;
            border-radius: 1rem;
            text-align: center;
        }

        .project-options {
            display: none;
        }

        .project-block .create {
            color: #8a8a8a;
            font-size: 1.1rem;
            padding-top: 15px;
        }

        .project-block .project-name {
            width: 100px;
            font-size: 1rem;
            font-weight: bold;
            position: absolute;
            top: 6rem;
            padding-left: 2px;
            outline-color: #ffffff;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>全部项目</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @foreach($projects as $project)
                    <div class="col-md-2 ml-4 mr-4" id="project-{{ $project->id }}">
                        <div class="project-block">
                            <a href="{{ route('projects.show', ['id' => $project->id]) }}">
                                <div class="info-block bg-info">
                                    <span class="project-options float-right">
                                        <i class="fa fa-trash-alt delete" data-project-id="{{ $project->id }}"></i>&nbsp;
                                        <i class="fa fa-edit edit" data-project-id="{{ $project->id }}"></i>
                                    </span>
                                    <h5 class="project-name text-left">{{ $project->name }}</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="col-md-2 ml-4 mr-4">
                    <div class="create-project project-block" data-toggle="modal" data-target="#create-project-modal">
                        <div class="info-block bg-white">
                            <img class="img-circle" src="{{ asset('/img/plus.png') }}" alt=""/>
                            <h4 class="create">创建项目</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 添加项目的模态框 --}}
        <div class="modal fade" id="create-project-modal">
            <div class="modal-dialog" style="width: 28%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">创建项目</h4>
                        <button type="button" class="close" onclick="closeModal(this)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="form-group">
                                <label for="name">项目名称</label>
                                <input type="text" class="form-control" id="name">
                            </div>
                            <div class="form-group">
                                <label for="users">项目成员</label>
                                <select id="users" class="form-control select2" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <p class="error text-center text-danger"></p>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary confirm" onclick="createProject(this)">确定
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 删除项目的模态框 --}}
        <div class="modal fade" id="delete-project-modal">
            <div class="modal-dialog" style="width: 28%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">删除项目</h4>
                        <button type="button" class="close" onclick="closeModal(this)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" class="project-id">
                            <div class="form-group">
                                <p>
                                    删除项目将彻底清空此项目下的所有任务列表和任务，<br>
                                    确定要删除整个项目？
                                </p>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-danger confirm" onclick="deleteProject(this)">确定
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 编辑项目的模态框 --}}
        <div class="modal fade" id="edit-project-modal">
            <div class="modal-dialog" style="width: 28%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">编辑项目</h4>
                        <button type="button" class="close" onclick="closeModal(this)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" id="project-id">
                            <div class="form-group">
                                <label for="name">项目名称</label>
                                <input type="text" class="form-control" id="name">
                            </div>
                            <div class="form-group">
                                <label for="users2">项目成员</label>
                                <select id="users2" class="form-control select2" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <p class="error text-center text-danger"></p>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary confirm" onclick="updateProject(this)">确定
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
    <script src="{{ asset('/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            $('.select2').select2();

            $('.project-block').on('mouseenter', function () {
                $(this).find('.project-options').show();
            }).on('mouseleave', function () {
                $(this).find('.project-options').hide();
            });

            $('.project-options .delete').on('click', function (e) {
                e.preventDefault();
                let modal = $('#delete-project-modal');
                modal.find('.project-id').val($(this).data('project-id'));
                modal.modal('toggle');
            });
            $('.project-options .edit').on('click', function (e) {
                e.preventDefault();
                let id = $(this).data('project-id');
                $.get('{{ route('projects.info') }}', {id: id}, function (data) {
                    if (data.code === 200) {
                        let modal = $('#edit-project-modal'),
                            info = data.data;

                        modal.find('#project-id').val(id);
                        modal.find('#name').val(info.name);
                        modal.find('#users2').val(info.uids).trigger('change');
                        modal.modal('toggle');
                    } else {
                        alert(data.msg)
                    }
                });
            });
        });

        function closeModal(obj) {
            let modal = $(obj).parents('.modal');
            modal.find('#name').val('');
            modal.find('#users').val(null).trigger('change');
            modal.find('.error').text('');
            modal.modal('toggle');
        }

        function createProject(obj) {
            let modal = $(obj).parents('.modal'),
                name = modal.find('#name').val(),
                users = modal.find('#users').select2('val');

            $.post('{{ route('projects.store') }}', {
                _token: GlobalToken,
                name: name,
                users: users
            }, function (data) {
                if (data.code === 200) {
                    window.location.reload();
                } else {
                    modal.find('.error').text(data.msg);
                }
            }, 'json');
        }

        function deleteProject(obj) {
            let modal = $(obj).parents('.modal'),
                id = modal.find('.project-id').val();

            $.post('{{ route('projects.delete') }}', {
                _token: GlobalToken,
                id: id
            }, function (data) {
                if (data.code !== 200) {
                    alert(data.msg);
                }

                window.location.reload()
            }, 'json')
        }

        function updateProject(obj) {
            let modal = $(obj).parents('.modal'),
                id = modal.find('#project-id').val(),
                name = modal.find('#name').val(),
                users = modal.find('#users2').select2('val');

            $.post('{{ route('projects.update') }}', {
                _token: GlobalToken,
                id: id,
                name: name,
                users: users
            }, function (data) {
                if (data.code === 200) {
                    window.location.reload();
                } else {
                    modal.find('.error').text(data.msg);
                }
            }, 'json');
        }
    </script>
@endsection
