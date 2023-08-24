<!doctype html>
<html lang="en">
<!-- HEAD to include meta infor and head part -->
@include('partials.head')

<body>
    <!-- HEADER -->
    @include('partials.header')
    
    <!-- Dynamic Content will be Loaded here -->
    @yield('content')
    
    <!-- FOOTER -->
    @include('partials.footer')
    
    <!-- Load javascript code at last -->
    @include('partials.scripts')
</body>
</html>