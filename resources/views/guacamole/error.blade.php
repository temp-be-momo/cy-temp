@extends('layouts.app')

@section('title', 'Guacamole')

@section('content')
<div class="alert alert-warning">
    <p><i class="fas fa-exclamation-triangle"></i>
    <strong class="mx-2">Something went wrong!</strong></p>
    <p>At this URL you should normally see the interface of Guacamole.</p>

    <p>To fix this error you should either:</p>
    <ul>
        <li>
            Check the configuration of Apache reverse proxy:
            <a href="https://cylab.be/blog/198/configure-apache-reverse-proxy-in-front-of-cyrange">
                https://cylab.be/blog/198/configure-apache-reverse-proxy-in-front-of-cyrange
            </a> or
        </li>
        <li>
            In <b>cyrange.env</b>, set the correct address for Guacamole interface:
            <code>GUAC_URL=http://localhost:8081</code> and restart the cyrange server
        </li>
    </ul>
</div>
@endsection
