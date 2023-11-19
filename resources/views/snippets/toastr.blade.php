<script>
    window.addEventListener('load', function() {
        @if (Session::has('toastr-info'))
            @foreach (Session::pull('toastr-info') as $message)
              toastr.info('{{ $message }}');
            @endforeach
        @endif

        @if (Session::has('toastr-warning'))
            @foreach (Session::pull('toastr-warning') as $message)
              toastr.warning('{{ $message }}');
            @endforeach
        @endif

        @if (Session::has('toastr-success'))
            @foreach (Session::pull('toastr-success') as $message)
              toastr.success('{{ $message }}');
            @endforeach
        @endif

        @if (Session::has('toastr-error'))
            @foreach (Session::pull('toastr-error') as $message)
              toastr.error('{{ $message }}');
            @endforeach
        @endif
    });
</script>
