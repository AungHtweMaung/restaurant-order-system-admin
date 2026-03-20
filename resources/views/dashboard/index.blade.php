@extends('layouts.app', [
    'elementActive' => 'dashboard',
])

@section('content')
    <div class="row gap-3">
        <div class="col-12 mt-2">
            <h2 class="text-center fw-bold">Dashboard</h2>
        </div>
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line me-2"></i>
                        <h5 class="mb-0">Most Selling Items</h5>
                    </div>
                    <div class="btn-group" role="group">
                       <button type="button" class="btn btn-sm btn-outline-light active" id="today-btn">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="weekly-btn">Weekly</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="monthly-btn">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="most-selling-date-filter" class="mb-3" style="display: none;">
                        <div class="row align-items-end g-3">
                            <div class="col-md-4">
                                <label for="start-date" class="form-label fw-semibold">Start Date</label>
                                <input type="date" id="start-date" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="end-date" class="form-label fw-semibold">End Date</label>
                                <input type="date" id="end-date" class="form-control">
                            </div>

                            <div class="col-md-4 d-flex flex-column flex-sm-row gap-2 align-items-stretch">
                                <button type="button" class="btn btn-danger w-100 w-sm-auto" id="reset-date-filter">Reset</button>
                                <button type="button" class="btn btn-primary w-100 w-sm-auto" id="search-date-filter">Search</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody id="order-items-tbody">
                                @php
                                    $index = 1;
                                @endphp
                                @foreach ($orderItems as $item)
                                    <tr>
                                        <td>{{ $index }}.</td>
                                        <td>{{ $item->mm_name }}</td>
                                        <td><span class="badge bg-warning">{{ $item->total_sold_quantity }}</span></td>
                                    </tr>
                                    @php
                                        $index++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 ">

            <!-- Order Summary Card -->
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-receipt me-2"></i>
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-light active" id="summary-today-btn">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="summary-weekly-btn">Weekly</button>
                        <button type="button" class="btn btn-sm btn-outline-light" id="summary-monthly-btn">Monthly</button>
                    </div>
                </div>

                <div class="card-body">
                    <div id="summary-date-filter" class="mb-3" style="display: none;">
                        <div class="row align-items-end g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date" id="summary-start-date" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date" id="summary-end-date" class="form-control">
                            </div>

                            <div class="col-md-4 d-flex flex-column flex-sm-row gap-2 align-items-stretch">
                                <button type="button" class="btn btn-danger w-100" id="summary-reset">Reset</button>
                                <button type="button" class="btn btn-primary w-100" id="summary-search">Search</button>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center text-sm-start">
                        <div class="col-12 col-sm-4">
                            <h5>OrderItem Count</h5>
                            <p class="display-5" id="summary-order-count">{{ $todayOrderItemCount ?? 0 }}</p>
                        </div>

                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                            <h5>Total Revenue</h5>
                            <p class="display-5">
                                THB
                                <span class="fw-bolder" id="summary-total-revenue">
                                    {{ $todayTotalRevenue ? number_format($todayTotalRevenue, 0, '.', ',') : 0 }}
                                </span>
                            </p>
                        </div>

                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                            <h5>Order Type</h5>
                            <div id="order-types" class="mt-2">
                                <div>• DineIn <span id="dinein-count">{{ $todayDineInCount ?? 0 }}</span></div>
                                <div>• Takeaway <span id="takeaway-count">{{ $todayTakeawayCount ?? 0 }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Weekly Order Count Card -->
                <div class="card shadow border-0 mt-4">
                    <div style="background-color: rgba(255, 193, 0, 1)" class="card-header text-white">
                        <h5 class="mb-0">Weekly Order Counts</h5>
                    </div>
                    <div class="card-body" style="height: 400px;">
                        {!! $chart->render() !!}
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let currentItemPeriod = 'today';

            $('#today-btn').on('click', function() {
                setItemActive($(this));
                currentItemPeriod = 'today';
                $('#most-selling-date-filter').hide();
                clearDateFilter();
                loadOrderItems('today');
            });

            $('#weekly-btn').on('click', function() {
                setItemActive($(this));
                currentItemPeriod = 'weekly';
                setDefaultDates('weekly');
                $('#most-selling-date-filter').show();
                loadOrderItems('weekly', $('#start-date').val(), $('#end-date').val());
            });

            $('#monthly-btn').on('click', function() {
                setItemActive($(this));
                currentItemPeriod = 'monthly';
                setDefaultDates('monthly');
                $('#most-selling-date-filter').show();
                loadOrderItems('monthly', $('#start-date').val(), $('#end-date').val());
            });

            function setItemActive(activeBtn) {
                $('#today-btn, #weekly-btn, #monthly-btn').removeClass('active');
                activeBtn.addClass('active');
            }

            function loadOrderItems(period, startDate = '', endDate = '') {
                $.ajax({
                    url: '{{ route('dashboard.data', ':period') }}'.replace(':period', period),
                    method: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        updateTable(response.orderItems);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading data:', error);
                        toastr.error('Failed to load data');
                    }
                });
            }

            function updateTable(orderItems) {
                let tbody = $('#order-items-tbody');
                tbody.empty();

                if (orderItems.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="3" class="text-center">No data found</td>
                        </tr>
                    `);
                    return;
                }

                orderItems.forEach(function(item, index) {
                    let row = `
                        <tr>
                            <td>${index + 1}.</td>
                            <td>${item.mm_name}</td>
                            <td><span class="badge bg-warning">${item.total_sold_quantity}</span></td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function clearDateFilter() {
                $('#start-date').val('');
                $('#end-date').val('');
            }

            function setDefaultDates(period) {
                const today = new Date();
                let startDate = new Date();
                let endDate = new Date();

                if (period === 'weekly') {
                    const day = today.getDay();
                    const diffToMonday = day === 0 ? -6 : 1 - day;
                    startDate.setDate(today.getDate() + diffToMonday);
                    endDate.setDate(startDate.getDate() + 6);
                } else if (period === 'monthly') {
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                }

                $('#start-date').val(formatDate(startDate));
                $('#end-date').val(formatDate(endDate));
            }

            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            $('#search-date-filter').on('click', function() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                if (!startDate || !endDate) {
                    toastr.error('Please select both start date and end date');
                    return;
                }

                if (startDate > endDate) {
                    toastr.error('Start Date cannot be later than End Date');
                    return;
                }

                loadOrderItems(currentItemPeriod, startDate, endDate);
            });

            $('#reset-date-filter').on('click', function() {
                if (currentItemPeriod === 'weekly' || currentItemPeriod === 'monthly') {
                    setDefaultDates(currentItemPeriod);
                    loadOrderItems(currentItemPeriod, $('#start-date').val(), $('#end-date').val());
                } else {
                    clearDateFilter();
                    $('#most-selling-date-filter').hide();
                    loadOrderItems('today');
                }
            });

            // Order Summary buttons
             $('#summary-today-btn').on('click', function() {
                setSummaryActive($(this));
                currentSummaryPeriod = 'today';
                $('#summary-date-filter').hide();
                clearSummaryDates();
                loadSummaryData('today');
            });

            $('#summary-weekly-btn').on('click', function() {
                setSummaryActive($(this));
                currentSummaryPeriod = 'weekly';
                setSummaryDefaultDates('weekly');
                $('#summary-date-filter').show();
                loadSummaryData('weekly', $('#summary-start-date').val(), $('#summary-end-date').val());
            });

            $('#summary-monthly-btn').on('click', function() {
                setSummaryActive($(this));
                currentSummaryPeriod = 'monthly';
                setSummaryDefaultDates('monthly');
                $('#summary-date-filter').show();
                loadSummaryData('monthly', $('#summary-start-date').val(), $('#summary-end-date').val());
            });

            function setSummaryActive(activeBtn) {
                $('#summary-today-btn, #summary-weekly-btn, #summary-monthly-btn').removeClass('active');
                activeBtn.addClass('active');
            }

            function loadSummaryData(period, startDate = '', endDate = '') {
                $.ajax({
                    url: '{{ route('dashboard.summary.data', ':period') }}'.replace(':period', period),
                    method: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        $('#summary-order-count').text(response.orderItemCount ?? 0);
                        $('#summary-total-revenue').text(
                            Number(response.totalRevenue ?? 0).toLocaleString()
                        );
                        $('#dinein-count').text(response.orderTypes?.DineIn ?? 0);
                        $('#takeaway-count').text(response.orderTypes?.Takeaway ?? 0);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading summary data:', error);
                        toastr.error('Failed to load summary data');
                    }
                });
            }

            function clearSummaryDates() {
                $('#summary-start-date').val('');
                $('#summary-end-date').val('');
            }

            function setSummaryDefaultDates(period) {
                const today = new Date();
                let startDate = new Date();
                let endDate = new Date();

                if (period === 'weekly') {
                    const day = today.getDay();
                    const diffToMonday = day === 0 ? -6 : 1 - day;
                    startDate.setDate(today.getDate() + diffToMonday);
                    endDate.setDate(startDate.getDate() + 6);
                } else if (period === 'monthly') {
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                }

                $('#summary-start-date').val(formatDate(startDate));
                $('#summary-end-date').val(formatDate(endDate));
            }

            $('#summary-search').on('click', function() {
                const startDate = $('#summary-start-date').val();
                const endDate = $('#summary-end-date').val();

                if (!startDate || !endDate) {
                    toastr.error('Please select both dates');
                    return;
                }

                if (startDate > endDate) {
                    toastr.error('Start Date cannot be greater than End Date');
                    return;
                }

                loadSummaryData(currentSummaryPeriod, startDate, endDate);
            });

            $('#summary-reset').on('click', function() {
                if (currentSummaryPeriod === 'weekly' || currentSummaryPeriod === 'monthly') {
                    setSummaryDefaultDates(currentSummaryPeriod);
                    loadSummaryData(
                        currentSummaryPeriod,
                        $('#summary-start-date').val(),
                        $('#summary-end-date').val()
                    );
                } else {
                    clearSummaryDates();
                    $('#summary-date-filter').hide();
                    loadSummaryData('today');
                }
            });

            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
        });
    </script>
@endpush