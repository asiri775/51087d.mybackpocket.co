@extends('admin.layouts.newMaster')
@section('title', 'Vendor')
@section('page-css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-default px-5">
            <div class="card-header">
                <div class="card-title" style="width: 100%">
                    <div class="row col-md-12">
                       
                        <div class="col-md-4">
                            <h5><strong>{{ $vendor->name }}</strong></h5>
                        </div>
                        <div class="col-md-4">
                            <h5>Vendor Details</h5>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ url('admin/vendors/edit-vendor/' . $vendor->id) }}"> <button class="btn btn-info" style="margin-top: 1rem; background: #880638 !important; text-transform: uppercase; color: #fff; width: 100%;">
                                    Edit Vendor Details
                                </button></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-default" style="min-height: 18rem">
                        <div class="card-body text-center">
                            <?php
                            $logo=(isset($vendor->logo_vendor))?$vendor->logo_vendor:$vendor->logo;
                            ?>
                            <img class="transaction-logo"
                                style="width: 200px; position: relative; left: 40%; transform: translateX(-50%)" alt="Logo"
                                data-src-retina="{{ asset('admin/assets/img/vendor-logos/' .$logo ) }}"
                                data-src="{{ asset('admin/assets/img/vendor-logos/' . $logo ) }}"
                                src="{{ asset('admin/assets/img/vendor-logos/' . $logo ) }}">
                            <?php $address = trim($vendor->address); ?>    
                             @if($vendor->name=='Uber Trip')<h4>{{$address}}</h4>@endif
                            <h4>{{ $vendor->street_name }}</h4>
                            <h4>{{ $vendor->city }}</h4>
                            <h4>{{ $vendor->state }}</h4>
                            <h4>{{ $vendor->zip_code }}</h4>
                            <p><a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a></p>
                            <h4> @if($vendor->HST) HST No : {{ $vendor->HST }} @endif</h4>
                            @if ($vendor->store_no)
                                <p>{{ $vendor->store_no }}</p>
                            @endif
                            @if ($vendor->QST)
                                <p>{{ $vendor->QST}}</p>
                            @endif
                            <button class="btn"
                                style="margin-top: 1rem; background: #6d5cae; text-transform: uppercase; color: #fff; width: 100%">
                                Add
                                to Favourites
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">

                    <div id="map" style="min-height: 18rem"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-default" style="text-align: center; min-height: 350px">
                        <script>
                            function quickfilter() {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                var filter = document.getElementById("quickfilter").value;
                                var vendor_id = '<?= $vendor->id ?>';
                                $.ajax({
                                    url: "<?= route('vendors.selectDate') ?>",
                                    type: 'POST',
                                    data: {
                                        vendor_id: vendor_id,
                                        filter: filter
                                    },
                                    dataType: "json",
                                    success: function(data) {
                                        $("#amount").html(data.total);
                                        $("#count").html(data.count);

                                    }

                                });
                            }
                        </script>
                        <h5 class="card-title" style="font-weight: bold; text-transform: uppercase">Quick Report</h5>
                        <hr style="margin: 0 25px;" />
                        <div class="card-body text-center">
                            <div style="display: flex; justify-content: center">
                                <select name="date" id="quickfilter" onchange="quickfilter();" class="form-control"
                                    style="width: 30%">
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week</option>
                                </select>
                            </div>
                            <hr />
                            <div style="width: 100%; display: flex; justify-content: center">
                                <div style="width: 50%;">
                                    <div>
                                        <strong style="text-transform: uppercase">Total
                                            Spent:</strong>&nbsp;&nbsp;&nbsp;&nbsp;$<span
                                            id="amount">{{ number_format($total, 2, '.', '') }}</span>
                                    </div>
                                    <hr />
                                    <div><strong
                                            style="text-transform: uppercase">Transactions:</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span
                                            id="count">{{ $count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <img src="https://via.placeholder.com/750x250.png?text=Ad+Image" style="width: 100%" alt="Image">
                </div>
            </div>
        </div>
    </div>
@endsection



@section('page-js')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API') }}&callback" async></script>
    <script>
        let geoLocation;
        fetch(
            'https://maps.googleapis.com/maps/api/geocode/json?address={{ $vendor->address }}&key={{ env('GOOGLE_MAP_API') }}'
        ).then(async (res) => {
            try {
                const payload = await res.json();
                geoLocation = payload.results[0].geometry.location;
                const map = new google.maps.Map(document.getElementById('map'), {
                    center: geoLocation,
                    zoom: 15,
                });
                new google.maps.Marker({
                    position: geoLocation,
                    map,
                    title: '{{ $vendor->name }}'
                });
            } catch (error) {
                console.log(error);
            }
        });
    </script>
@endsection
