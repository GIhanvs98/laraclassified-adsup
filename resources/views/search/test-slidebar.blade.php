<div class="slidebar">
@php
    $routeText = 'test-slidebar';
@endphp

<div><a href="{{ route($routeText) }}">@if(isset($districtId))All of Sri Lanka @else<strong>All of Sri Lanka</strong>@endif</a></div>
<ol style="margin: 0px;">

    @if(isset($districtId))
        
        {{-- Output for the http://127.0.0.1:8000/ads/{city}/{id} --}}

        @if(DB::table('subadmin2')->where('district_id_city', $districtId)->where('active', 1)->exists())
            
            {{-- Is a district --}}
            
            @php
                
            $district = DB::table('cities')->whereId($districtId)->where('active', 1)->first();

            $cities = DB::table('cities')->whereNotIn('id', [$districtId])->where('active', 1)->where('subadmin2_code', $district->subadmin2_code)->get();

            @endphp

            <li>
                
                <div class="district-container">
                    <strong><a href="{{ route($routeText, ['city' => str()->slug(json_decode($district->name)->en), 'id' => $district->id]) }}">
                        {{ json_decode($district->name)->en }}
                    </a></strong>
                </div>
                <ol>
                    @forelse($cities as $key => $city)
                         
                        {{-- Calculate the total posts --}}
                        @php

                        $postsCount = DB::table('posts')->whereNull('deleted_at')->where('city_id', $city->id)->count();
                        
                        @endphp

                        <li class="district-container">
                            <a href="{{ route($routeText, ['city' => str()->slug(json_decode($city->name)->en), 'id' => $city->id]) }}">
                                {{ json_decode($city->name)->en }} ({{ $postsCount }})
                            </a>
                        </li>
                    @empty
                        
                        <div class="city">No cities found</div>
                    @endforelse
                </ol>
            </li>

        @else
            
            {{-- Not a district --}}

            @php
                
            $districtCode = DB::table('cities')->whereId($districtId)->where('active', 1)->first()->subadmin2_code;

            $district = DB::table('subadmin2')->where('code', $districtCode)->where('active', 1)->first();

            $cities = DB::table('cities')->whereNotIn('id', [$district->district_id_city])->where('active', 1)->where('subadmin2_code', $districtCode)->get();

            @endphp
            
            <li>
                
                <div class="district-container">
                    <a href="{{ route($routeText, ['city' => str()->slug(str_replace(' District', '', json_decode($district->name)->en)), 'id' => $district->district_id_city]) }}">
                        {{ str_replace(' District', '', json_decode($district->name)->en) }}
                    </a>
                </div>
                <ol>
                    @forelse($cities as $key => $city)   

                        {{-- Calculate the total posts --}}
                        @php

                        $postsCount = DB::table('posts')->whereNull('deleted_at')->where('city_id', $city->id)->count();
                        
                        @endphp

                        <li class="district-container">
                            <a href="{{ route($routeText, ['city' => str()->slug(json_decode($city->name)->en), 'id' => $city->id]) }}">
                                @if( $city->id == $districtId )
                                    <strong>{{ json_decode($city->name)->en }}</strong>
                                @else
                                    {{ json_decode($city->name)->en }} ({{ $postsCount }})
                                @endif
                            </a>
                        </li>
                    @empty
                        
                        <div class="city">No cities found</div>
                    @endforelse
                </ol>
            </li>

        @endif
        
    @else
            
        @php

        $districts = DB::table('subadmin2')->where('active', 1)->whereNotNull('district_id_city')->orderBy('name', 'asc')->get();
            
        @endphp

        {{-- Output for the http://127.0.0.1:8000/ads/ --}}
        
        @forelse($districts as $key => $district)
            
            {{-- Calculate the total posts --}}
            @php

            $postsCount = 0;

            $cities = DB::table('cities')->where('active', 1)->where('subadmin2_code', $district->code)->get();
            
            foreach ($cities as $key => $city) {

                $postsCount += DB::table('posts')->whereNull('deleted_at')->where('city_id', $city->id)->count();
            }
            
            @endphp

            <li class="district-container">
                <a href="{{ route($routeText, ['city' => str()->slug(str_replace(' District', '', json_decode($district->name)->en)), 'id' => $district->district_id_city]) }}">
                    {{ str_replace(' District', '', json_decode($district->name)->en) }} ({{ $postsCount }})
                </a>
            </li>
        @empty
            
            <div class="district-container">No districts found</div>
        @endforelse

    @endif
</ol>
</div>