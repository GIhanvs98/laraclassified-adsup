<form wire:submit="update" class="form-horizontal">

    {{-- name --}}
    <div class="row mb-3 required">
        <label class="col-md-3 col-form-label" for="name">Name <sup>*</sup></label>
        <div class="col-md-9 col-lg-8 col-xl-6">
            <input wire:model="name" name="name" type="text" class="form-control" placeholder="Name">
            @error('name')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- email --}}
    <div class="row mb-3 auth-field-item required">
        <label class="col-md-3 col-form-label" for="email">Email <sup>*</sup></label>
        <div class="col-md-9 col-lg-8 col-xl-6">
            <div class="input-group mb-1">
                <span class="input-group-text"><i class="far fa-envelope"></i></span>
                <input wire:model="email" name="email" type="email" class="form-control" placeholder="Email Address">
            </div>
            @error('email')
                <div style="color: red;background: none;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @foreach($contact_numbers as $key => $contact_number)
        @include('livewire.inc.user-update.phone', [
            'title' => $contact_number['attribute_name'],
            'field' => $contact_number,
            'styles' => [
                'parent' => 'margin-top: 20px;',
            ]
        ])
    @endforeach

    <div class="row">
        <div class="offset-md-3 col-md-9 mb-1">
            @include('livewire.post-ad.fields.custom.add_contact_number_button')
        </div>
    </div>

    <div class="row">
        <div class="offset-md-3 col-md-9 mt-1">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>

    {{--@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif--}}


    @script
    
        <script type="module">

            document.addEventListener('livewire:initialized', () => {

                const contact_numbers = $wire.$get('contact_numbers');

                let contact_numbers_counters = [];

                Object.entries(contact_numbers).forEach(function(currentCN, index) {

                    contact_numbers_counters[index] = new Interval({
                        counter: `#counter_${currentCN[1].id}`,
                        delay: 1000, // miliseconds - define the time taken to switch to next digit - default 1 second
                        duration: 2, // minutes - total duration
                        onStart: function () {
                            // console.log("When started the interval");
                        },
                        onEnd: function () {
                            // console.log("Ended interval:- ", `contact_numbers.${currentCN[1].id}.states.countdown`);
                            $wire.$set(`contact_numbers.${currentCN[1].id}.states.countdown`, false);
                        },
                        onRestart: function () {
                            // console.log("When restarted the interval");
                        },
                    });

                    $wire.on(`start_timer_${currentCN[1].id}` , () => {
                        contact_numbers_counters[index].start();
                    });

                    $wire.on(`restart_timer_${currentCN[1].id}` , () => {
                        contact_numbers_counters[index].restart();
                    });

                });

            });

        </script>

    @endscript

</form>