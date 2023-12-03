@extends('admin.layouts.newMaster')
@section('title', 'Vendor')
@section('page-css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-default px-5">
            <div class="card-header">
                <div class="card-title" style="width: 100%">
                    <div class="d-flex justify-content-between">
                        <h5><strong>Add A Vendor</strong></h5>
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-12">
                <br>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <p><strong>Opps Something went wrong</strong></p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <form action="{{ route('store.vendor') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="name">Vendor Name</label></div>
                                    <div class="col-md-9"> <input type="text" class="form-control" name="name"
                                            id="name" value="{{ old('name') }}" placeholder="Add A Name">
                                    </div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="email">Vendor Email</label></div>
                                    <div class="col-md-9"> <input type="email" class="form-control" name="email"
                                            id="email"
                                            value="{{ old('email') != '' ? old('email') : 'noemail@backpocket.ca' }}"></div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="mapAddress">Address</label></div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="Address" name="mapAddress"
                                            id="pac-input"
                                            value="{{ old('address') != '' ? old('address') : 'Add An Address' }}">
                                        <input type="hidden" value="{{ old('lat') }}" name="lat" id="lat">
                                        <input type="hidden" value="{{ old('lng') }}" name="lng" id="lng">
                                        <input type="hidden" value="{{ old('address') }}" name="address" id="location">
                                        <input class="field" value="{{ old('route') }}" name="route" id="route"
                                            type="hidden" />
                                        <input class="field" value="{{ old('city') }}" name="city" id="locality"
                                            type="hidden" />
                                        <input class="field" value="{{ old('state') }}" name="state"
                                            id="administrative_area_level_1" type="hidden" />
                                        <input class="field" value="Canada" name="country" id="country"
                                            type="hidden" />
                                        <div id="map" style="height: 300px;width: 100%" hidden></div>
                                    </div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="store_no">Postal Code</label></div>
                                    <div class="col-md-9"> <input type="text" class="form-control"
                                            value="{{ old('postal_code') != '' ? old('postal_code') : 'Add A Postal Code' }}"
                                            name="postal_code" id="postal_code" type="hidden"></div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="store_no">Store Number</label></div>
                                    <div class="col-md-9"> <input type="text" class="form-control" name="store_no"
                                            id="store_no"
                                            value="{{ old('store_no') != '' ? old('store_no') : 'Add A Store Number' }}">
                                    </div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="phone">Phone</label></div>
                                    <div class="col-md-9"> <input type="text" class="form-control" name="phone"
                                            id="phone"
                                            value="{{ old('phone') != '' ? old('phone') : 'Add A Phone' }}"></div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="hst">HST</label></div>
                                    <div class="col-md-9"> <input type="text" class="form-control" name="hst"
                                            id="hst" value="{{ old('hst') != '' ? old('hst') : 'Add A HST' }}"></div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="qst">QST</label></div>
                                    <div class="col-md-9"> <input type="text" class="form-control" name="qst"
                                            id="qst" value="{{ old('qst') != '' ? old('qst') : 'Add A QST' }}"></div>
                                </div>
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-3"> <label for="logo">Logo</label></div>
                                    <div class="col-md-9"> <img style="max-width: 250px;" src="" id="logo"
                                            alt="No Featured Image Added">
                                        <input onchange="readURL(this)" id="uploadFile" name="logo" type="file">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row col-md-12">
                                <div class="col-md-3"></div>
                                <div class="col-md-6"> <button type="submit" class="btn btn-info form-control" style=" color: #fff !important;
                                            background-color: #00238C !important;
                                            border-color: #3b475 !important;">Add</button></div>
                                <div class="col-md-3"></div>
                            </div>
                            <br>
                    </div>
                    </form>
                </div>
            </div>

            <div class="col-md-2"></div>
        </div>
    </div>
    </div>
@endsection



@section('page-js')

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRu_qlT0HNjPcs45NXXiOSMd3btAUduSc&libraries=places&callback=initMap"
        async defer></script>


    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 55.585901,
                    lng: -105.750596
                },
                zoom: 5
            });
            var options = {
                types: ['geocode'], // or '(cities)' if that's what you want?
                componentRestrictions: {
                    country: ["us", "ca"]
                }
            };
            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };
            var input = (document.getElementById('pac-input'));
            var types = document.getElementById('type-selector');
            //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

            var autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();

                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17); // Why 17? Because it looks good.
                }
                marker.setIcon( /** @type  {google.maps.Icon} */ ({
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                var item_Lat = place.geometry.location.lat()
                var item_Lng = place.geometry.location.lng()
                var item_Location = place.formatted_address;
                var street_number = "";
                var route = "";
                var locality = "";
                var administrative_area_level_1 = "";
                var country = "";
                var postal_code = "";
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                        var val = place.address_components[i][componentForm[addressType]];
                        if (addressType == "street_number") {
                            street_number = val;
                        }
                        if (addressType == "route") {
                            route = val;
                        }
                        if (addressType == "locality") {
                            locality = val;
                        }
                        if (addressType == "administrative_area_level_1") {
                            administrative_area_level_1 = val;
                        }
                        if (addressType == "country") {
                            country = val;
                        }
                        if (addressType == "postal_code") {
                            postal_code = val;
                        }
                    }
                }


                //alert("Lat= "+item_Lat+"_____Lang="+item_Lng+"_____Location="+item_Location+"__item_postCode"+item_postCode);
                $("#lat").val(item_Lat);
                $("#lng").val(item_Lng);
                $("#street_number").val(street_number);
                $("#postal_code").val(postal_code);
                $("#location").val(item_Location);
                $("#locality").val(locality);
                $("#route").val(route);
                $("#administrative_area_level_1").val(administrative_area_level_1);


                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0]
                            .short_name || ''),
                        (place.address_components[1] && place.address_components[1]
                            .short_name || ''),
                        (place.address_components[2] && place.address_components[2]
                            .short_name || '')
                    ].join(' ');
                }
                for (var component in componentForm) {
                    document.getElementById(component).value = '';
                    document.getElementById(component).disabled = false;
                }
                // Get each component of the address from the place details,
                // and then fill-in the corresponding field on the form.
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                        var val = place.address_components[i][componentForm[addressType]];
                        document.getElementById(addressType).value = val;
                    }
                }
                console.log(val);
                infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                infowindow.open(map, marker);
            });

            // Sets a listener on a radio button to change the filter type on Places
            // Autocomplete.
            function setupClickListener(id, types) {
                var radioButton = document.getElementById(id);
                /*radioButton.addEventListener('click', function() {
                autocomplete.setTypes(types);
                });*/
            }

            setupClickListener('changetype-all', []);
            setupClickListener('changetype-address', ['address']);
            setupClickListener('changetype-establishment', ['establishment']);
            setupClickListener('changetype-geocode', ['geocode']);
        }
    </script>
    <script>
        jQuery(function($) {
            $("#phone").mask("(999) 999 - 9999");

        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#logo').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
