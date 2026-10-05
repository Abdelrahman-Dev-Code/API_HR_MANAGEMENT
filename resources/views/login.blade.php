@extends('layouts.app')

@section('title', 'Login - HR Management')

@section('content')
<div class="page-shell">
    <div class="login-card">
        <div class="login-header">
            <div class="brand-mark" style="margin: 0 auto 16px;">H</div>
            <p class="eyebrow" style="color: var(--muted); text-align: center;">Enterprise portal</p>
            <h1>Sign in</h1>
        </div>

        <div class="login-form">
            <div class="field">
                <label>Email</label>
                <input type="email" value="admin@northstar.com" />
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" value="password123" />
            </div>
            <button class="submit-btn">Login</button>
            <p class="subtitle">Access your HR dashboard</p>
        </div>
    </div>
</div>
@endsection
