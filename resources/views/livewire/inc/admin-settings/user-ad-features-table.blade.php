<div class="text-xs text-gray-400 mb-2">Default settings are overwritten in below users. You can either enabled/disabled.</div>

<button wire:click="$dispatch('show-modal', { type: 'new', setting: '{{ $setting }}' })" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-2 mr-2">Add new user</button>

@if($settings_overwritten[$setting] && count($settings_overwritten[$setting]) > 0)
        
    <table class="dataTable table table-bordered table-striped display dt-responsive nowrap mt-3" style="width:100%">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>{{ ucfirst(str_replace('_', ' ', $setting)) }}</th>
            <th>Actions</th>
        </tr>

        @foreach ($settings_overwritten[$setting] as $key => $image_compression_user)
            <tr wire:key="{{ $setting }}_{{ $key }}">
                <td>{{ $key + 1 }}</td>
                <td>
                    <div class="text-sm text-gray-900 mb-1">
                        {{ $image_compression_user->name }}
                    </div>
                    <div class="text-xs text-gray-600">
                        {{ $image_compression_user->email }}
                    </div>
                </td>
                <td style="text-transform: capitalize;">
                    @if($image_compression_user->{$setting})
                        <div class="inline text-xs px-2 py-1 rounded-full text-white bg-green-600 w-fit">
                            Enabled
                        </div>
                    @else
                        <div class="inline text-xs px-2 py-1 rounded-full text-white bg-red-600 w-fit">
                            Disabled
                        </div>
                    @endif
                </td>
                <td>
                    <div class="flex">
                        <button wire:click="$dispatch('show-modal', { type: 'edit', setting: '{{ $setting }}', userId: '{{ $image_compression_user->id }}' })" class="btn btn-xs btn-info flex items-center min-w-fit max-w-fit mb-1 mr-2">
                            Edit
                        </button>
        
                        <button wire:click="setDefault('{{ $setting }}', '{{ $image_compression_user->id }}')" class="btn btn-xs btn-danger flex items-center min-w-fit max-w-fit mb-1">
                            Set system default
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach

    </table>
    
@else
    <div class="mt-2">No users with overwritten image compression setting. <a class="text-blue-500 cursor-pointer hover:text-blue-600" wire:click="$dispatch('show-modal', { type: 'new', setting: '{{ $setting }}' })">Add new</a></div>
@endif