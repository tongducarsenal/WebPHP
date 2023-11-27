@extends('admin.layouts.master')
@section('title', 'Thêm mới người dùng')

@section('body')

<!-- Main -->
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-ticket icon-gradient bg-mean-fruit"></i>
                </div>
                <div>
                    User
                    <div class="page-title-subheading">
                        View, create, update, delete and manage.
                    </div>
                </div>
            </div>

            <div class="page-title-actions">
                <a href="/quantri/user/create" class="btn-shadow btn-hover-shine mr-3 btn btn-primary">
                    <span class="btn-icon-wrapper pr-2 opacity-7">
                        <i class="fa fa-plus fa-w-20"></i>
                    </span>
                    Create
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <form method="get">
                        <div class="input-group">
                            <input type="search" name="search" id="search" placeholder="Tìm kiếm ở đây nek ???" class="form-control">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i>&nbsp;
                                    Search
                                </button>
                            </span>
                        </div>
                    </form>

                    <div class="btn-actions-pane-right">
                        <div role="group" class="btn-group-sm btn-group">
                            <button class="btn btn-focus">This week</button>
                            <button class="active btn btn-focus">Anytime</button>
                        </div>
                    </div>
                </div>

                <!-- Thông báo thành công -->
                @if(session("alert"))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{session("alert")}}</strong>
                </div>
                @endif


                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Level</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="text-center text-muted">#{{$user->id}}</td>
                                <td>
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="widget-content-left">
                                                    <img width="40" class="rounded-circle" data-toggle="tooltip" title="Ảnh Avatar" data-placement="bottom" src="./assets/images/user/{{$user->avatar ?? 'default-avatar.png'}}" alt="DuogBachDev">
                                                </div>
                                            </div>
                                            <div class="widget-content-left flex2">
                                                <div class="widget-heading">{{$user->name}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{$user->email}}</td>
                                <td class="text-center">
                                    {{ \App\Utilities\Constant::$user_level[$user->level] }}
                                    <!-- @if($user->level == 0)
                                    Host
                                    @elseif($user->level == 1)
                                    Admin
                                    @else
                                    Client
                                    @endif -->
                                </td>
                                <td class="text-center">
                                    <a href="/quantri/user/detail/{{$user->id}}" class="btn btn-hover-shine btn-outline-primary border-0 btn-sm">
                                        Details
                                    </a>
                                    <a href="/quantri/user/edit/{{$user->id}}" data-toggle="tooltip" title="Edit" data-placement="bottom" class="btn btn-outline-warning border-0 btn-sm">
                                        <span class="btn-icon-wrapper opacity-8">
                                            <i class="fa fa-edit fa-w-20"></i>
                                        </span>
                                    </a>
                                    <!-- <a href="/quantri/user/destroy/{{$user->id}}" data-toggle="tooltip" title="Edit" data-placement="bottom" class="btn btn-outline-warning border-0 btn-sm">
                                        <span class="btn-icon-wrapper opacity-8">
                                            <i class="fa fa-trash fa-w-20"></i>
                                        </span>
                                    </a> -->
                                    <form class="d-inline" action="/quantri/user/destroy/{{$user->id}}" method="post">
                                        <button class="btn btn-hover-shine btn-outline-danger border-0 btn-sm" type="submit" data-toggle="tooltip" title="Delete" data-placement="bottom" onclick="return confirm('Bạn Có Muốn Xóa User Này Không?')">
                                            <span class="btn-icon-wrapper opacity-8">
                                                <i class="fa fa-trash fa-w-20"></i>
                                            </span>
                                        </button>
                                        @csrf
                                    </form>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-block card-footer">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 leading-5">
                                    Hiển thị
                                    <span class="font-medium">{{ $startResult }}</span>
                                    tới
                                    <span class="font-medium">{{ $endResult }}</span>
                                    trên
                                    <span class="font-medium">{{ $totalResults }}</span>
                                    kết quả
                                </p>
                            </div>

                            <div>
                                {{ $users->links("pagination::bootstrap-4") }}
                            </div>

                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main -->
@endsection