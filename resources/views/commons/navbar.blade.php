<header class="mb-4">
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        {{-- トップページへのリンク --}}
        <a class="navbar-brand navbar-brand-center mx-auto" style="font-size: 18pt;" href="/">ChannelChart</a>

        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#nav-bar" style="z-index:1">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav-bar">
            <ul class="navbar-nav mr-auto"　style="z-index:1">
                @if (Auth::check())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"  data-bs-toggle="dropdown" aria-expanded="false" data-toggle="dropdown">
                        リスト一覧
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/">すべてのチャンネル</a></li>
                        @if (null !== $lists)
                        @foreach ($lists as $list)
                        <li>{!! link_to_route('channels.show', $list->name, ['id' => $list->id], ['class' => 'dropdown-item']) !!}</li>
                        @endforeach
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>{!! link_to_route('lists.create', '＋ リスト作成', [], ['class' => 'dropdown-item']) !!}</li>
                    </ul>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav" style="z-index:1">
                @if (Auth::check())
                    {{-- アカウント情報ページへのリンク --}}
                    <li class="nav-item">{!! link_to_route('users.show', Auth::user()->name, ['user' => Auth::id()], ['class' => 'nav-link']) !!}</li>
                @else
                    {{-- ユーザ登録ページへのリンク --}}・
                    <li class="nav-item">{!! link_to_route('signup.get', '登録', [], ['class' => 'nav-link']) !!}</li>
                    {{-- ログインページへのリンク --}}
                    <li class="nav-item">{!! link_to_route('login', 'ログイン', [], ['class' => 'nav-link']) !!}</li>
                @endif
            </ul>
        </div>
    </nav>
</header>