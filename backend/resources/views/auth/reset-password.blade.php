<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
<h1>Reset Password</h1>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
    >

    <input
            type="hidden"
            name="email"
            value="{{ $request->email }}"
    >

    <div>
        <label for="password">New password</label>
        <input
                id="password"
                type="password"
                name="password"
                required
        >
    </div>

    <div>
        <label for="password_confirmation">Confirm password</label>
        <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
        >
    </div>

    <button type="submit">
        Reset Password
    </button>
</form>
</body>
</html>