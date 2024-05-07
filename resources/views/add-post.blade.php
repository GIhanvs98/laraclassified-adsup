<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>add-post</title>
</head>
<body>

    <livewire:add-post />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/he/1.2.0/he.min.js" integrity="sha512-PEsccDx9jqX6Dh4wZDCnWMaIO3gAaU0j46W//sSqQhUQxky6/eHZyeB3NrXD2xsyugAKd4KPiDANkcuoEa2JuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script src="{{ asset('assets/plugins/jquery/3.3.1/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/momentjs/2.18.1/moment.min.js') }}"></script>
    
    {{-- Tinymce --}}
    <script src="https://cdn.tiny.cloud/1/090tcphbqgz9ryhjyh84bx4c6kf9sum2yht4lmy7euln5dyx/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    {{-- Jquery UI --}}
    <script src="{{ asset('assets/plugins/jqueryui/1.13.2/jquery-ui.js') }}"></script>

</body>
</html>