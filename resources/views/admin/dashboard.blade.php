@extends('admin.layouts.master')

@section('title', __('Label.Dashboard'))

@section('content')
    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">@yield('title')</h1>

        <!-- Counter -->
        <div class="row counter-row">
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card user-card">
                    <i class="fa-solid fa-users fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                           
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('user') }}" style="color: #A98471;">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_user) ?: 00}}">{{no_format($total_user) ?: 00}}</p>
                        <span>{{__('Label.Users')}}</span>
                    </h2>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card artist-card">
                    <i class="fa-solid fa-tower-broadcast fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('channel') }}" style="color: #6DB3C6">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_channel) ?: 00}}">{{no_format($total_channel) ?: 00}}</p>
                        <span>{{__('Label.Channel')}}</span>
                    </h2>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card">
                    <i class="fa-solid fa-video fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('video') }}" style="color: #6cb373;">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_video) ?: 00 }}">{{no_format($total_video) ?: 00 }}</p>
                        <span>{{__('Label.Videos')}}</span>
                    </h2>
                </div>
            </div>
           
          <!--  <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card cate-card">
                    <i class="fa-solid fa-tv fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('TVShow') }}" style="color: #736AA6">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_show) ?: 00}}">{{no_format($total_show) ?: 00}}</p>
                        <span>{{__('Label.TV Shows')}}</span>
                    </h2>
                </div>
            </div> -->
            
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card package-card">
                    <i class="fa-solid fa-user-tie fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('cast') }}" style="color: #C0698B">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_cast) ?: 00}}">{{no_format($total_cast) ?: 00}}</p>
                        <span>{{__('Label.Cast')}}</span>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Counter -->
        <div class="row counter-row">
           
           <!--  <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card category-card">
                    <i class="fa-solid fa-money-bill fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('RentTransaction') }}" style="color: #9D0BB1">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_rent_transaction) ?: 00}}">{{no_format($total_rent_transaction) ?: 00}}</p>
                        <span>Rent Earnings({{currency_code()}})</span>
                    </h2>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card rent_video-card">
                    <i class="fa-regular fa-money-bill-1 fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('RentTransaction') }}" style="color: #692705">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_month_rent_transaction) ?: 00 }}">{{no_format($total_month_rent_transaction) ?: 00 }}</p>
                        <span>Monthly Rent Earnings ({{currency_code()}})</span>
                    </h2>
                </div>
            </div>-->
            
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card plan-card">
                    <i class="fa-solid fa-box-archive fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('package') }}" style="color: #201f1e">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_package) ?: 00}}">{{no_format($total_package) ?: 00}}</p>
                        <span>Package</span>
                    </h2>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card green-card">
                    <i class="fa-solid fa-money-bill fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('transaction') }}" style="color: #245c1c">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_month_transaction) ?: 00 }}">{{no_format($total_month_transaction) ?: 00 }}</p>
                        <span>Monthly Package Earnings ({{currency_code()}})</span>
                    </h2>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                <div class="db-color-card subscribers-card">
                    <i class="fa-regular fa-money-bill-1 fa-2x card-icon"></i>
                    <div class="dropdown dropright">
                        <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical dot-icon"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('transaction') }}" style="color: #530899">{{__('Label.View All')}}</a>
                        </div>
                    </div>
                    <h2 class="counter mt-2">
                        <p class="p-0 m-0 counting" data-count="{{no_format($total_transaction) ?: 00 }}">{{no_format($total_transaction) ?: 00 }}</p>
                        <span>Package Earnings ({{currency_code()}})</span>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Join User Statistice && Most View Video -->
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between"><h2 class="title text-white" style="font-size:16px;">Join Users Statistice</h2> <a href="{{ route('user') }}" class="btn btn-link">{{__('Label.View All')}}</a></div>
                    <div class="card-header">  
                        <Button id="year" class="btn btn-default">This Year</Button>
                        <Button id="month" class="btn btn-default">This Month</Button>
                    </div>
                    <div class="card-body">  <canvas id="UserChart" width="100%" height="45px"></canvas></div>
                    </div>         
              
            </div>

            <div class="col-12 col-xl-4">
                <div class="video-box card">
                    <div class="box-title mt-0">
                        <h2 class="title text-white">Most Viewed Video</h2>
                    </div>
                    @if(isset($most_view_video) && $most_view_video != null)
                    <div class="p-3 mt-4">
                        <img src="{{ Get_Image('video', $most_view_video->landscape) }}" class="img-fluid d-block mx-auto img-thumbnail" style="height: 300px; width: 100%;" />
                        <div class="box-title box-border-0">
                            <h5 class="f600 text-white" style="display: inline-block; text-overflow:ellipsis; white-space:nowrap; overflow:hidden; width:75%;">{{ $most_view_video->name}}</h5>
                            <div class="d-flex justify-content-between text-white">
                                <i data-feather="eye" class="mr-3"></i> <h5> {{no_format($most_view_video->view) ?: 00}} </h5>
                            </div>
                        </div>
                            <h5 class="f600 text-white" style="display: inline-block; text-overflow:ellipsis; white-space:nowrap; overflow:hidden; width:75%;">{{ $most_view_video->name}}</h5>
                        <div class="details text-white">
                            <span>{{ string_cut($most_view_video->description,110) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <?php /*
            <div class="col-12 col-xl-4">
                <div class="video-box card mb-3">
                    <div class="box-title mt-0">
                        <h2 class="title text-white">Most Viewed TVShow</h2>
                    </div>
                    @if(isset($most_view_show) && $most_view_show != null)
                    <div class="p-3 mt-4">
                        <img src="{{ Get_Image('show', $most_view_show->landscape) }}" class="img-fluid d-block mx-auto img-thumbnail" style="height: 300px; width: 100%;" />
                        <div class="box-title box-border-0">
                            <h5 class="f600 text-white" style="display: inline-block; text-overflow:ellipsis; white-space:nowrap; overflow:hidden; width:75%;">{{ $most_view_show->name}}</h5>
                            <div class="d-flex justify-content-between">
                                <i data-feather="eye text-white" style="color:#4e45b8" class="mr-3"></i> <h5> {{no_format($most_view_show->view) ?: 00}}</h5>
                            </div>
                        </div>
                        <div class="details text-white">
                            <span>{{ string_cut($most_view_show->description,110) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div> */ ?>
            <div class="col-12 col-xl-8">
                <div class="card mb-3">
                    <div class="card-header box-title mt-0">
                    <h2 class="title text-white">Package Earning Statistice</h2>
                    <a href="{{ route('transaction') }}" class="btn btn-link">{{__('Label.View All')}}</a>
                    </div>
                    <div class="card-body">
                    <canvas id="MyChart" width="100%" height="98%"></canvas>
                    </div>
    </div>
                
           
            </div>
        </div>

        <!-- Rent Earning Statistice -->
        <div class="row">
         <!--    <div class="col-12 col-xl-8">
                <div class="card mb-3">
                    <div class="card-header box-title mt-0">
                    <h2 class="title text-white">Rent Earning Statistice</h2>
                    <a href="{{ route('RentTransaction') }}" class="btn btn-link">{{__('Label.View All')}}</a>
                    </div>
                    <div class="card-body">
                    <canvas id="RentChart" width="100%" height="48px"></canvas>
                    </div>

</div>
                
               
            </div>
            <div class="col-12 col-xl-4">
                <div class="video-box card mb-3">
                    <div class="box-title mt-0">
                        <h2 class="title text-white">Most Viewed TVShow</h2>
                    </div>
                    @if(isset($most_view_show) && $most_view_show != null)
                    <div class="p-3 mt-4">
                        <img src="{{ Get_Image('show', $most_view_show->landscape) }}" class="img-fluid d-block mx-auto img-thumbnail" style="height: 300px; width: 100%;" />
                        <div class="box-title box-border-0">
                            <h5 class="f600 text-white" style="display: inline-block; text-overflow:ellipsis; white-space:nowrap; overflow:hidden; width:75%;">{{ $most_view_show->name}}</h5>
                            <div class="d-flex justify-content-between">
                                <i data-feather="eye text-white" style="color:#4e45b8" class="mr-3"></i> <h5> {{no_format($most_view_show->view) ?: 00}}</h5>
                            </div>
                        </div>
                        <div class="details text-white">
                            <span>{{ string_cut($most_view_show->description,110) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>-->
        </div>

        <!-- Plan Earning Statistice
        <div class="row"> -->

        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Counter
        $('.counting').each(function() {
            var $this = $(this),
                countTo = $this.attr('data-count');

            countTo = getVal(countTo);

            $(this).prop('Counter', 0).animate({
                countNum: countTo
            }, {
                duration: 3500,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                },
                complete: function() {
                    $this.text($this.attr('data-count'));
                }
            });
        });
        function getVal(val) {
            multiplier = val.substr(-1).toLowerCase();

            if (multiplier == "k")
                return parseFloat(val) * 1000;
            else if (multiplier == "m")
                return parseFloat(val) * 1000000;
            else if (multiplier == "b")
                return parseFloat(val) * 1000000000;
            else if (multiplier == "t")
                return parseFloat(val) * 1000000000000;
            else
                return val;
        }

        // User Statistice
        var cData = JSON.parse(`<?php echo $user_year; ?>`);
        var ctx = $("#UserChart");
        var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        var data = {
            labels: month,
            datasets: [{
                label: 'Users',
                data: cData['sum'],
                backgroundColor: '#f97316',
            }],
        };
        var options = {
            responsive: true,
            title: {
                display: true,
                position: "top",
                text: "Join Users Statistice (Current Year)",
                fontSize: 18,
                fontColor: "#ffffff"
            },
            legend: {
                title: "text",
                display: true,
                position: 'top',
                labels: {
                    fontSize: 16,
                    fontColor: "#ffffff",
                }
            },
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Total Count',
                        fontSize: 16,
                        fontColor: "#ffffff",
                    },
                }],
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Month',
                        fontSize: 16,
                        fontColor: "#ffffff",
                    }
                }]
            }
        };
        var chart1 = new Chart(ctx, {
            type: "bar",
            data: data,
            options: options
        });

        $("#year").on("click", function() {
            chart1.destroy();

            chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options

            });
        });
        $("#month").on("click", function() {

            var date = new Date();
            var currentYear = date.getFullYear();
            var currentMonth = date.getMonth() + 1;
            const getDays = (year, month) => new Date(year, month, 0).getDate();
            const days = getDays(currentYear, currentMonth);

            var all1 = [];
            for (let i = 0; i < days; i++) {
                all1.push(i + 1);
            }

            chart1.destroy();
            var cData = JSON.parse(`<?php echo $user_month ?>`);

            var data = {
                labels: all1,
                datasets: [{
                    label: 'Users',
                    data: cData['sum'],
                    backgroundColor: '#f97316',
                }],
            };
            var options = {
                responsive: true,
                title: {
                    display: true,
                    position: "top",
                    text: "Join Users Statistice (Current Month)",
                    fontSize: 18,
                    fontColor: "#ffffff"
                },
                legend: {
                    title: "text",
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 16,
                        fontColor: "#ffffff",
                    }
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Total Count',
                            fontSize: 16,
                            fontColor: "#ffffff",
                        },
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Date',
                            fontSize: 16,
                            fontColor: "#ffffff",
                        }
                    }]
                }
            };
            chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options,
            });
        });

        // Package Earning Statistice
        $(function() {
            //get the pie chart canvas
            var cData = JSON.parse(`<?php echo $package; ?>`);
            var ctx = $("#MyChart");
            var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            var backcolor = ["#6D3A74", "#528BA6", "#2A445E", "#E99E75", "#00bfa0", "#9b19f5", "#ffa300", "#dc0ab4", "#7c1158", "#b30000"];

            const datasetValue = [];
            for (let i = 0; i < cData['label'].length; i++) {
                datasetValue[i] = {
                    label: cData['label'][i],
                    data: cData['sum'][i],
                    backgroundColor: '#f97316',
                }
            }

            //bar chart data
            var data = {
                labels: month,
                datasets: datasetValue
            };

            //options
            var options = {
                responsive: true,
                title: {
                    display: true,
                    position: "top",
                    text: "Package Earning Statistice (Current Year)",
                    fontSize: 18,
                    fontColor: "#fff"
                },
                legend: {
                    title: "text",
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 16,
                        fontColor: "#fff",
                    }
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Amount',
                            fontSize: 16,
                            fontColor: "#fff",
                        },
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Month',
                            fontSize: 16,
                            fontColor: "#fff",
                        }
                    }]
                }
            };

            //create bar Chart class object
            var chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options
            });
        });

        // Rent Earning Statistice
        $(function() {
            var cData = JSON.parse(`<?php echo $rent; ?>`);
            var ctx = $("#RentChart");
            var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            var data = {
                labels: month,
                datasets: [{
                    label: 'Earning',
                    data: cData['sum'],
                    backgroundColor: '#4e45b8',
                }],
            };
            var options = {
                responsive: true,
                title: {
                    display: true,
                    position: "top",
                    text: "Rent Earning Statistice (Current Year)",
                    fontSize: 18,
                    fontColor: "#000"
                },
                legend: {
                    title: "text",
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 16,
                        fontColor: "#000000",
                    }
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Amount',
                            fontSize: 16,
                            fontColor: "#000000",
                        },
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Month',
                            fontSize: 16,
                            fontColor: "#000000",
                        }
                    }]
                }
            };
            var chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options
            });
        });
    </script>
@endsection