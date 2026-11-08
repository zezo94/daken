
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Profile | DAKEN Chocolate</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="background:#f8f5f0;color:#302018;font-family:Arial,sans-serif;">

<main style="max-width:760px;margin:50px auto;padding:20px;">

    <header style="display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:32px;">
        <div>
            <h1 style="font-size:30px;margin:0;">My Profile</h1>
            <p>Manage your DAKEN account securely.</p>
        </div>

        <a href="{{ route('home') }}"
           style="color:#70462e;text-decoration:none;">
            Back to Store
        </a>
    </header>

    @if (session('status'))
        <div style="padding:14px;background:#e8f5e9;margin-bottom:20px;border-radius:8px;">
            {{ session('status') }}
        </div>
    @endif

    <section style="background:white;padding:26px;border-radius:14px;margin-bottom:24px;">

        <h2>Account Information</h2>

        <form method="POST"
              action="{{ route('user-profile-information.update') }}">

            @csrf
            @method('PUT')

            <div style="margin-bottom:18px;">
                <label for="name">Full Name</label>

                <input id="name"
                       type="text"
                       value="{{ auth()->user()->name }}"
                       disabled
                       style="display:block;width:100%;padding:12px;margin-top:6px;background:#eee;border:1px solid #ddd;border-radius:8px;">

                <small>Your name cannot be changed.</small>
            </div>

            <div style="margin-bottom:18px;">
                <label for="email">Email Address</label>

                <input id="email"
                       name="email"
                       type="email"
                       value="{{ old('email', auth()->user()->email) }}"
                       required
                       autocomplete="email"
                       style="display:block;width:100%;padding:12px;margin-top:6px;border:1px solid #ddd;border-radius:8px;">

                @error('email', 'updateProfileInformation')
                <p style="color:#b42318;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:18px;">
                <label for="email_current_password">Current Password</label>

                <input id="email_current_password"
                       name="current_password"
                       type="password"
                       required
                       autocomplete="current-password"
                       style="display:block;width:100%;padding:12px;margin-top:6px;border:1px solid #ddd;border-radius:8px;">

                @error('current_password', 'updateProfileInformation')
                <p style="color:#b42318;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    style="background:#543521;color:white;padding:12px 22px;border:0;border-radius:8px;cursor:pointer;">
                Save Email
            </button>

        </form>
    </section>

    <section style="background:white;padding:26px;border-radius:14px;margin-bottom:24px;">

        <h2>Change Password</h2>

        <form method="POST"
              action="{{ route('user-password.update') }}">

            @csrf
            @method('PUT')

            <div style="margin-bottom:18px;">
                <label for="password_current">Current Password</label>

                <input id="password_current"
                       name="current_password"
                       type="password"
                       required
                       autocomplete="current-password"
                       style="display:block;width:100%;padding:12px;margin-top:6px;border:1px solid #ddd;border-radius:8px;">

                @error('current_password', 'updatePassword')
                <p style="color:#b42318;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:18px;">
                <label for="password">New Password</label>

                <input id="password"
                       name="password"
                       type="password"
                       required
                       minlength="12"
                       autocomplete="new-password"
                       style="display:block;width:100%;padding:12px;margin-top:6px;border:1px solid #ddd;border-radius:8px;">

                @error('password', 'updatePassword')
                <p style="color:#b42318;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:18px;">
                <label for="password_confirmation">Confirm New Password</label>

                <input id="password_confirmation"
                       name="password_confirmation"
                       type="password"
                       required
                       minlength="12"
                       autocomplete="new-password"
                       style="display:block;width:100%;padding:12px;margin-top:6px;border:1px solid #ddd;border-radius:8px;">

            </div>

            <button type="submit"
                    style="background:#543521;color:white;padding:12px 22px;border:0;border-radius:8px;cursor:pointer;">
                Update Password
            </button>

        </form>
    </section>

    <section style="background:white;padding:26px;border-radius:14px;">
        <h2>Sign Out</h2>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                    style="background:#a72c28;color:white;padding:12px 22px;border:0;border-radius:8px;cursor:pointer;">
                Logout
            </button>
        </form>
    </section>

</main>

</body>
</html>
