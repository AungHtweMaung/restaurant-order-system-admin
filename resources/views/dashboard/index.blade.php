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
            $('#today-btn').on('click', function() {
                setItemActive($(this));
                loadOrderItems('today');
            });

            // Most Selling Items buttons
            $('#weekly-btn').on('click', function() {
                setItemActive($(this));
                loadOrderItems('weekly');
            });

            $('#monthly-btn').on('click', function() {
                setItemActive($(this));
                loadOrderItems('monthly');
            });

            function setItemActive(activeBtn) {
                $('#today-btn, #weekly-btn, #monthly-btn').removeClass('active');
                activeBtn.addClass('active');
            }

            function loadOrderItems(period) {
                $.ajax({
                    url: '{{ route('dashboard.data', ':period') }}'.replace(':period', period),
                    method: 'GET',
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

            // Order Summary buttons
            $('#summary-today-btn').on('click', function() {
                setSummaryActive($(this));
                loadSummaryData('today');
            });

            $('#summary-weekly-btn').on('click', function() {
                setSummaryActive($(this));
                loadSummaryData('weekly');
            });

            $('#summary-monthly-btn').on('click', function() {
                setSummaryActive($(this));
                loadSummaryData('monthly');
            });

            function setSummaryActive(activeBtn) {
                $('#summary-today-btn, #summary-weekly-btn, #summary-monthly-btn').removeClass('active');
                activeBtn.addClass('active');
            }

            function loadSummaryData(period) {
                $.ajax({
                    url: '{{ route('dashboard.summary.data', ':period') }}'.replace(':period', period),
                    method: 'GET',
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
        });
    </script>
@endpush