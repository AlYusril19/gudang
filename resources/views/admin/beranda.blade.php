@extends('layouts.app_sneat')

@section('content')
<div class="row">
        <div class="col-xxl-6 mb-3 order-0">
            <div class="card">
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            {{-- {{ __('You are logged in!') }} --}}
                            <h5 class="card-title text-primary mb-3">Welcome {{ auth()->user()->role }} {{ auth()->user()->name }} 🎉</h5>
                            <p class="mb-6">You have 0 item data with low stock.<br>Check in bottom.</p>
                            
                            {{-- @if ($barangMenipis)
                                <a href="{{ route('laporan-admin.create') }}" class="btn btn-sm btn-outline-primary">View Pendings</a>
                            @endif --}}
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-6">
                            <img src="{{ asset('sneat') }}/assets/img/illustrations/man-with-laptop-light.png" height="175" class="scaleX-n1-rtl" alt="View Badge User">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--/ Total Revenue -->
        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button
                                        class="btn p-0"
                                        type="button"
                                        id="cardOpt4"
                                        data-bs-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="d-block mb-1">Penjualan</span>
                            <h5 class="card-title mb-2">{{ formatRupiah($penjualanSekarang) }}</h4>
                            @if ($bandingPenjualan >=0)
                                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $bandingPenjualan }}%</small>
                            @else
                                <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> {{ $bandingPenjualan }}%</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button
                                        class="btn p-0"
                                        type="button"
                                        id="cardOpt1"
                                        data-bs-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="{{ route('pembelian.index') }}">View More</a>
                                        {{-- <a class="dropdown-item" href="javascript:void(0);">Delete</a> --}}
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Pembelian</span>
                            <h5 class="card-title mb-2">{{ formatRupiah($pembelianSekarang) }}</h4>
                            @if ($bandingPembelian >=0)
                                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> {{ $bandingPembelian }}%</small>
                            @else
                                <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> {{ $bandingPembelian }}%</small>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Profile Report</h5>
                                        <span class="badge bg-label-warning rounded-pill">Year 2021</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <small class="text-success text-nowrap fw-semibold">
                                            <i class="bx bx-chevron-up"></i> 68.2%
                                        </small>
                                        <h3 class="mb-0">$84,686k</h3>
                                    </div>
                                </div>
                                <div id="reportChart"></div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const profileReportChartEl = document.querySelector('#reportChart'),
        profileReportChartConfig = {
        chart: {
            height: 80,
            // width: 175,
            type: 'line',
            toolbar: {
            show: false
            },
            dropShadow: {
            enabled: true,
            top: 10,
            left: 5,
            blur: 3,
            color: config.colors.warning,
            opacity: 0.15
            },
            sparkline: {
            enabled: true
            }
        },
        grid: {
            show: false,
            padding: {
            right: 8
            }
        },
        colors: [config.colors.warning],
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 5,
            curve: 'smooth'
        },
        series: [
            {
            data: [110, 270, 145, 245]
            }
        ],
        xaxis: {
            show: false,
            lines: {
            show: false
            },
            labels: {
            show: false
            },
            axisBorder: {
            show: false
            }
        },
        yaxis: {
            show: false
        }
        };
    if (typeof profileReportChartEl !== undefined && profileReportChartEl !== null) {
        const reportChart = new ApexCharts(profileReportChartEl, profileReportChartConfig);
        reportChart.render();
    }
    </script>
@endsection
