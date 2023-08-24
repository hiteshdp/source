<section class="notification-block">
    <div id="responseMessage"></div>
</section>
@if ($message = Session::get('success'))
<section class="notification-block">
    <div class="alert alert-success mb-0">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <span>{{ $message }}</span>
    </div>
</section>
@endif

@if ($alert = Session::get('error'))
<section class="notification-block">
    <div class="alert alert-danger mb-0">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <span>{{ $alert }}</span>
    </div>
</section>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-warning mb-0">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <span>{{ $message }}</span>
    </div>
@endif

@if ($message = Session::get('message'))
<section class="notification-block">
    <div class="alert alert-info mb-0">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <span>{{ $message }}</span>
    </div>
</section>
@endif

@if (count($errors) > 0)
<section class="notification-block">
    <div class="alert alert-danger mb-0">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Whoops!</strong> There were some problems occured.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</section>
@endif

<div class="alert alert-danger validation_message" style="display: none;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <p>{{ $alert }}</p>
</div>