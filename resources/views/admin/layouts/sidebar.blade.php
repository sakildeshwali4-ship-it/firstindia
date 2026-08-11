<div class="sidebar">
    <div class="side-head">

        <a href="{{route('dashboard')}}" style="color:#4e45b8;">
            <!-- <h3>{{App_Name()}}</h3> -->
            <img src="https://ishivaxservices.com/admin_panel/public/images/app/401709451979.png"/>
        </a>

    </div>
    <ul class="side-menu mt-4">
        <li class="side_line {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard')}}">
                <img class="menu-icon" src="{{asset('/assets/imgs/dashboard.png')}}" alt="" />
                <span>{{__('Label.Dashboard')}}</span>
            </a>
        </li>
        <li class="dropdown {{ request()->is('admin/type*') ? 'active' : '' }}{{ request()->is('admin/avatar*') ? 'active' : '' }}{{ request()->is('admin/language*') ? 'active' : '' }}{{ request()->is('admin/session*') ? 'active' : '' }}{{ request()->is('admin/page*') ? 'active' : '' }}{{ request()->is('admin/category*') ? 'active' : '' }}{{ request()->is('admin/coupon*') ? 'active' : '' }}">
            <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-gear fa-2xl menu-icon"></i>
                <span> Basic Settings </span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->is('admin/type*') ? 'show' : '' }}{{ request()->is('admin/avatar*') ? 'show' : '' }}{{ request()->is('admin/language*') ? 'show' : '' }}{{ request()->is('admin/session*') ? 'show' : '' }}{{ request()->is('admin/page*') ? 'show' : '' }}{{ request()->is('admin/category*') ? 'show' : ''}}{{request()->is('admin/coupon*') ? 'show' : '' }}" aria-labelledby="dropdownMenuClickable">
                <li class="side_line {{ request()->is('admin/type*') ? 'active' : '' }}">
                    <a href="{{ route('type') }}" class="dropdown-item">
                        <i class="fa-solid fa-t fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Types')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/category*') ? 'active' : '' }}">
                    <a href="{{ route('category') }}" class="dropdown-item">
                        <i class="fa-solid fa-list fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Category')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/avatar*') ? 'active' : '' }}">
                    <a href="{{ route('Avatar') }}" class="dropdown-item">
                        <i class="fa-solid fa-user-plus fa-2xl submenu-icon"></i>
                        <span>Avatar</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/language*') ? 'active' : '' }}">
                    <a href="{{ route('language') }}" class="dropdown-item">
                        <i class="fa-solid fa-globe fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Languages')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/session*') ? 'active' : '' }}">
                    <a href="{{ route('session') }}" class="dropdown-item">
                        <i class="fa-solid fa-list-ol fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Session')}}</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/page*') ? 'active' : '' }}">
                    <a href="{{ route('Page') }}" class="dropdown-item">
                        <i class="fa-solid fa-book-open-reader fa-2xl submenu-icon"></i>
                        <span>Page</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/coupon*') ? 'active' : '' }}">
                    <a href="{{ route('coupon') }}" class="dropdown-item">
                        <i class="fa-solid fa-percent fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Coupons')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="dropdown {{ request()->is('admin/banner*') ? 'active' : '' }}{{ request()->is('admin/VideoSection*') ? 'active' : '' }}">
            <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="fa-solid fa-house fa-2xl menu-icon"></i>
                <span> Home </span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->is('admin/banner*') ? 'show' : '' }}{{ request()->is('admin/VideoSection*') ? 'show' : '' }}"
                aria-labelledby="dropdownMenuClickable">
                <li class="side_line {{ request()->is('admin/banner*') ? 'active' : '' }}">
                    <a href="{{ route('Banner') }}" class="dropdown-item">
                        <i class="fa-solid fa-scroll fa-2xl submenu-icon"></i>
                        <span> Banner </span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/VideoSection*') ? 'active' : '' }}">
                    <a href="{{ route('VideoSection') }}" class="dropdown-item">
                        <i class="fa-solid fa-bars-staggered fa-2xl submenu-icon"></i>
                        <span> Section </span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="dropdown {{ request()->is('admin/channel*') ? 'active' : '' }}{{ request()->is('admin/Channel-Banner*') ? 'active' : '' }}{{ request()->is('admin/ChannelSection*') ? 'active' : '' }}">
            <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="fa-solid fa-satellite-dish fa-2xl menu-icon"></i>
                <span> Channel </span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->is('admin/channel*') ? 'show' : '' }}{{ request()->is('admin/Channel-Banner*') ? 'show' : '' }}{{ request()->is('admin/ChannelSection*') ? 'show' : '' }}"
                aria-labelledby="dropdownMenuClickable">
                <li class="side_line {{ request()->is('admin/channel*') ? 'active' : '' }}">
                    <a href="{{ route('channel') }}" class="dropdown-item">
                        <i class="fa-solid fa-tower-broadcast fa-2xl submenu-icon"></i>
                        <span> Channels </span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/Channel-Banner*') ? 'active' : '' }}">
                    <a href="{{ route('ChannelBanner')}}" class="dropdown-item">
                        <i class="fa-solid fa-scroll fa-2xl submenu-icon"></i>
                        <span> Channel Banner</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/ChannelSection*') ? 'active' : '' }}">
                    <a href="{{ route('ChannelSection')}}" class="dropdown-item">
                        <i class="fa-solid fa-bars-staggered fa-2xl submenu-icon"></i>
                        <span> Channel Section </span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="side_line {{ request()->is('admin/enews*') ? 'active' : '' }}">
            <a href="{{ route('enews.index') }}">
                <i class="fa-solid fa-satellite-dish fa-2xl menu-icon"></i>
                <span>E-Newspaper</span>
            </a>
        </li>
        <li class="side_line {{ request()->is('admin/user*') ? 'active' : '' }}">
            <a href="{{ route('user') }}">
                <i class="fa-solid fa-users fa-2xl menu-icon"></i>
                <span>{{__('Label.Users')}}</span>
            </a>
        </li>
        <li class="side_line {{ request()->is('admin/cast*') ? 'active' : '' }}">
            <a href="{{ route('cast') }}">
                <i class="fa-solid fa-user-tie fa-2xl menu-icon"></i>
                <span>{{__('Label.Cast')}}</span>
            </a>
        </li> 
        <li class="side_line {{ request()->is('admin/video*') ? 'active' : '' }}">
            <a href="{{ route('video') }}">
                <i class="fa-solid fa-video fa-2xl menu-icon"></i>
                <span>{{__('Label.Videos')}}</span>
            </a>
        </li> 
        <li class="side_line {{ request()->is('admin/export_video_views') ? 'active' : '' }}">
            <a href="{{ route('export_video_views') }}">
                <i class="fa-solid fa-eye fa-2xl menu-icon"></i>
                <span>Video Views</span>
            </a>
        </li>

        <li class="side_line {{ request()->is('admin/event*') ? 'active' : '' }}">
            <a href="{{ route('event') }}">
                <i class="fa-solid fa-video fa-2xl menu-icon"></i>
                <span>{{__('Event')}}</span>
            </a>
        </li>
<li class="dropdown {{ request()->is('admin/web-series*') ? 'active' : '' }}{{ request()->is('admin/seasons*') ? 'active' : '' }}{{ request()->is('admin/episodes*') ? 'active' : '' }}">
            <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="fa-solid fa-satellite-dish fa-2xl menu-icon"></i>
                <span> Web Series </span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->is('admin/web-series*') ? 'show' : '' }}{{ request()->is('admin/seasons*') ? 'show' : '' }}{{ request()->is('admin/episodes*') ? 'show' : '' }}"
                aria-labelledby="dropdownMenuClickable">
                <li class="side_line {{ request()->is('admin/web-series*') ? 'active' : '' }}">
                    <a href="{{ route('web-series.index') }}" class="dropdown-item">
                        <i class="fa-solid fa-tower-broadcast fa-2xl submenu-icon"></i>
                        <span> Web Series </span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/seasons*') ? 'active' : '' }}">
                    <a href="{{ route('seasons.index')}}" class="dropdown-item">
                        <i class="fa-solid fa-scroll fa-2xl submenu-icon"></i>
                        <span> Seasons</span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/episodes*') ? 'active' : '' }}">
                    <a href="{{ route('episodes.video')}}" class="dropdown-item">
                        <i class="fa-solid fa-bars-staggered fa-2xl submenu-icon"></i>
                        <span> Episodes </span>
                    </a>
                </li>
            </ul>
        </li>

        <?php
        if(Auth::user()->id == 3) { ?>

            @php
                $seriesMenu = request()->is('admin/series*') || request()->is('admin/wallet*');
                $addSeriesActive = request()->is('admin/series/create');
            @endphp

            <li class="dropdown {{ $seriesMenu ? 'active' : '' }}">
                <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-satellite-dish fa-2xl menu-icon"></i>
                    <span>Reel Drama</span>
                </a>

                <ul class="dropdown-menu side-submenu {{ $seriesMenu ? 'show' : '' }}"
                    aria-labelledby="dropdownMenuClickable">

                    <li class="side_line {{ request()->is('admin/series') || request()->is('admin/series/*') && !request()->is('admin/series/create') ? 'active' : '' }}">
                        <a href="{{ route('series.index') }}" class="dropdown-item">
                            <i class="fa-solid fa-tower-broadcast fa-2xl submenu-icon"></i>
                            <span>Webseries</span>
                        </a>
                    </li>

                    <li class="side_line {{ request()->is('admin/wallet*') ? 'active' : '' }}">
                        <a href="{{ route('wallet.edit') }}" class="dropdown-item">
                            <i class="fa-solid fa-tower-broadcast fa-2xl submenu-icon"></i>
                            <span>Wallet</span>
                        </a>
                    </li>

                    <li class="side_line {{ $addSeriesActive ? 'active' : '' }}">
                        <a href="{{ route('series.create') }}" class="dropdown-item">
                            <i class="fa-solid fa-tower-broadcast fa-2xl submenu-icon"></i>
                            <span>Add Webseries</span>
                        </a>
                    </li>

                </ul>
            </li>


             <li class="side_line {{ request()->is('admin/ads*') ? 'active' : '' }}">
            <a href="{{ route('ads') }}">
                <i class="fa-solid fa-rectangle-ad fa-2xl menu-icon"></i>
                <span>Ads</span>
            </a>
        </li>
<li class="side_line {{ request()->is('admin/option*') ? 'active' : '' }}">
            <a href="{{ route('option.index') }}">
                <i class="fa-solid fa-video fa-2xl menu-icon"></i>
                <span>Option Data</span>
            </a>
        </li>
            <?php
        } ?>
       <!-- <li class="side_line {{ request()->is('admin/TVShow*') ? 'active' : '' }}">
            <a href="{{ route('TVShow') }}">
                <i class="fa-solid fa-tv fa-2xl menu-icon"></i>
                <span>{{__('Label.TV Shows')}}</span>
            </a>
        </li>
        <li class="dropdown {{ request()->is('admin/upcomingvideo*') ? 'active' : '' }}{{ request()->is('admin/upcomingtvshow*') ? 'active' : '' }}">
            <a class="dropdown-toggle" id="dropdownMenuClickable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-clapperboard fa-2xl menu-icon"></i>
                <span> Upcoming </span>
            </a>
            <ul class="dropdown-menu side-submenu {{ request()->is('admin/upcomingvideo*') ? 'show' : '' }}{{ request()->is('admin/upcomingtvshow*') ? 'show' : '' }}" aria-labelledby="dropdownMenuClickable">
                <li class="side_line {{ request()->is('admin/upcomingvideo*') ? 'active' : '' }}">
                    <a href="{{ route('upcomingvideo') }}" class="dropdown-item">
                        <i class="fa-solid fa-video fa-2xl submenu-icon"></i>
                        <span> Videos </span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/upcomingtvshow*') ? 'active' : '' }}">
                    <a href="{{ route('upcomingTVShow')}}" class="dropdown-item">
                        <i class="fa-solid fa-tv fa-2xl submenu-icon"></i>
                        <span> TV Shows</span>
                    </a>
                </li>
            </ul>
        </li>-->
        <!-- <li class="dropdown {{request()->is('admin/renttransaction*') ? 'active' : '' }}{{ request()->is('admin/rent') ? 'active' : '' }}{{ request()->is('admin/rent/*') ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-arrow-trend-up fa-2xl menu-icon"></i>
                <span> Rent </span>
            </a>
            <ul class="dropdown-menu side-submenu {{request()->is('admin/renttransaction*') ? 'show' : '' }}{{ request()->is('admin/rent') ? 'show' : '' }}{{ request()->is('admin/rent/*') ? 'show' : '' }}">
                <li class="side_line {{ request()->is('admin/rent') ? 'active' : '' }}{{ request()->is('admin/rent/*') ? 'active' : '' }}">
                    <a href="{{ route('RentVideo') }}" class="dropdown-item">
                        <i class="fa-solid fa-video fa-2xl submenu-icon"></i>
                        <span>Rent Videos</span>
                    </a>
                </li>
                <li class="side_line {{request()->is('admin/renttransaction*') ? 'active' : '' }}">
                    <a href="{{ route('RentTransaction') }}" class="dropdown-item">
                        <i class="fa-solid fa-wallet fa-2xl submenu-icon"></i>
                        <span> Rent Transactions </span>
                    </a>
                </li>
            </ul>
        </li> -->
        <li class="dropdown {{ (request()->is('admin/packages*')) ? 'active' : '' }}{{ request()->is('admin/payment*') ? 'active' : '' }}{{ (request()->routeIs('transaction*')) ? 'active' : '' }}">
            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-box-archive fa-2xl menu-icon"></i>
                <span> {{__('Label.Subscription')}} </span>
            </a>
            <ul class="dropdown-menu side-submenu {{ (request()->is('admin/packages*')) ? 'show' : '' }}{{ request()->is('admin/payment*') ? 'show' : '' }}{{ (request()->routeIs('transaction*')) ? 'show' : '' }}">
                <li class="side_line {{ request()->is('admin/packages*') ? 'active' : '' }}">
                    <a href="{{ route('package') }}" class="dropdown-item">
                        <i class="fa-solid fa-box-archive fa-2xl submenu-icon"></i>
                        <span> Package </span>
                    </a>
                </li>
                <li class="side_line {{ (request()->routeIs('transaction*')) ? 'active' : '' }}">
                    <a href="{{ route('transaction') }}" class="dropdown-item">
                        <i class="fa-solid fa-wallet fa-2xl submenu-icon"></i>
                        <span> {{__('Label.Transactions')}} </span>
                    </a>
                </li>
                <li class="side_line {{ request()->is('admin/payment*') ? 'active' : '' }}">
                    <a href="{{ route('Payment')}}" class="dropdown-item">
                        <i class="fa-solid fa-money-bill-wave fa-2xl submenu-icon"></i>
                        <span>{{__('Label.Payment')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="side_line {{ request()->is('admin/setting*') ? 'active' : '' }}">
            <a href="{{ route('setting') }}">
                <i class="fa-solid fa-gears fa-2xl menu-icon"></i>
                <span>{{__('Label.Settings')}}</span>
            </a>
        </li><?php
        if(Auth::user()->id == 3) { ?>

        <li class="side_line {{ request()->is('admin/livetv*') ? 'active' : '' }}">
            <a href="{{ route('livetv.index') }}">
                <i class="fa-solid fa-gears fa-2xl menu-icon"></i>
                <span>{{__('Live Tv Urls')}}</span>
            </a>
        </li>

        <li class="side_line {{ request()->is('admin/livetv*') ? 'active' : '' }}">
            <a href="{{ route('livetv.reports') }}">
                <i class="fa-solid fa-gears fa-2xl menu-icon"></i>
                <span>{{__('Tv Views Reports')}}</span>
            </a>
        </li>
            
        <li class="side_line {{ request()->is('admin/developer_setting*') ? 'active' : '' }}">
            <a href="{{ route('developer_setting') }}">
                <i class="fa-solid fa-gears fa-2xl menu-icon"></i>
                <span>{{__('Label.Developer Settings')}}</span>
            </a>
        </li><?php
		} ?>

        <li class="side_line {{ request()->is('admin/notification') ? 'active' : '' }}{{ request()->is('admin/notification/add') ? 'active' : '' }}{{ request()->is('admin/notification/setting') ? 'active' : '' }} ">
            <a href="{{ route('notification') }}">
                <i class="fa-solid fa-bell fa-2xl menu-icon"></i>
                <span>{{__('Label.Notification')}}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('adminLogout') }}">
                <i class="fa-solid fa-arrow-right-from-bracket fa-2xl menu-icon"></i>
                <span>{{__('Label.Logout')}}</span>
            </a>
        </li>
    </ul>
</div>
