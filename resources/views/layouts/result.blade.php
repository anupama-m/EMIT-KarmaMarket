@auth
    <p>Hi {{ Auth::user()->email }}; You are logged in</p>
@else
    <p>You are not logged in.</p>
@endauth