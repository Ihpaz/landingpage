<nav class="navbar top-navbar navbar-expand-md navbar-light">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{route('backend.dashboard.index')}}">
            <b>
                <img src="{{asset('img/cms.png')}}" alt="homepage" class="dark-logo" />
                <img src="{{asset('img/cms.png')}}" class="light-logo" />
            </b>
        </a>
    </div>
    <div class="navbar-collapse">
        <ul class="navbar-nav mr-auto mt-md-0">
            <li class="nav-item">
                <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a>
            </li>
            <li class="nav-item m-l-10">
                <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav my-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" id="2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-bell-outline"></i>
                    <div id="messages_notify" class="notify"></div>
                </a>
                <div class="dropdown-menu dropdown-menu-right mailbox animated scale-up" aria-labelledby="2">
                    <ul>
                        <li>
                            <div id="messages_count" class="drop-title">Terdapat 0 pesan baru</div>
                        </li>
                        <li>
                            <div id="messages_list" class="message-center">
                            </div>
                        </li>
                        <li>
                            <a class="nav-link text-center" href="{{route('backend.notification.index')}}"> <strong>Lihat Semua Pesan</strong> <i class="fa fa-angle-right"></i> </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark @impersonating bg-warning @endImpersonating" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{auth()->user()->user_thumbnail}}" alt="user" class="profile-pic" /></a>
                <div class="dropdown-menu dropdown-menu-right scale-up">
                    <ul class="dropdown-user">
                        <li>
                            <div class="dw-user-box">
                                <div class="u-text">
                                    <h4>{{auth()->user()->fullname}}</h4>
                                    <p class="text-muted">{{auth()->user()->email}}</p>
                                    @foreach(auth()->user()->getRoleNames() as $role)
                                    <span class="label label-info mb-0">{{strtoupper($role)}}</span>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{{route('backend.profile.index')}}"><i class="mdi mdi-account-star-variant"></i> Profile</a></li>
                        <li><a href="{{route('backend.profile.edit')}}"><i class="mdi mdi-account-settings-variant"></i> Change Password</a></li>
                        <li><a href="{{route('backend.notification.index')}}"><i class="mdi mdi-email"></i> Notification</a></li>
                        <li role="separator" class="divider"></li>
                        @impersonating
                        <li><a href="{{route('cms.user.impersonate.leave')}}"><i class="mdi mdi-account-convert"></i> Leave impersonation</a></li>
                        @endImpersonating
                        @if(session()->has('sso_id_token'))
                        <li><a href="{{route('oauth.logout')}}"><i class="mdi mdi-logout"></i> Logout</a></li>
                        @else
                        <li><a href="{{route('auth.logout')}}"><i class="mdi mdi-logout"></i> Logout</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>