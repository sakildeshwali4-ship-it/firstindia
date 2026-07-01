<header class="header">
    <div class="title-control">
  
        <button class="btn side-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Mobile Logo -->
        <a href="{{route('dashboard')}}" class=" primary-color side-logo">
            <h3>{{App_Name()}}</h3>
        </a>

        <h1 class="page-title">@yield('title')</h1>
    </div>

    <div class="head-control">
        <!-- Setting -->
        <a href="{{ route('setting') }}" class="btn" title="Setting">
            <i class="fa-solid fa-gear fa-2xl" style="color: #4e45b8;"></i>
        </a>

        <!-- Profile -->
        <div class="dropdown dropright" title="Profile">
            <a href="#" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-user fa-2xl" style="color: #4e45b8;" class="avatar-img"></i>
            </a>
            <div class="dropdown-menu p-2 mt-2" aria-labelledby="dropdownMenuLink">
                <div>
                    <?php $data = adminEmail(); if($data){echo $data['user_name'] ?: "";} ?>
                    <br><hr class="mt-2">
                    <?php $data = adminEmail(); if($data){echo $data['email'] ?: "";} ?>
                </div><hr class="mt-2">
                <a class="dropdown-item" href="{{ route('adminLogout') }}" style="color:#4E45B8;">
                    <span><i class="fa-solid fa-arrow-right-from-bracket fa-xl mr-2"></i></span>
                    {{__('Label.Logout')}}
                </a>
            </div>
        </div>
    </div>
</header>