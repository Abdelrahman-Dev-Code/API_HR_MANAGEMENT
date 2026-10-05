@extends('layouts.app')

@section('title', 'Settings - HR Management')

@section('content')
<div class="dashboard-page">
    <aside class="sidebar">
        <div class="brand-box">
            <div class="brand-mark">H</div>
            <div>
                <p class="eyebrow">Enterprise</p>
                <h2>HR Suite</h2>
            </div>
        </div>
        <nav class="nav-menu">
            <a class="nav-item" href="{{ route('dashboard') }}"><span class="icon">⌂</span><span>Dashboard</span></a>
            <a class="nav-item" href="{{ route('employees') }}"><span class="icon">👥</span><span>Employees</span></a>
            <a class="nav-item" href="{{ route('attendance') }}"><span class="icon">🗓</span><span>Attendance</span></a>
            <a class="nav-item" href="{{ route('payroll') }}"><span class="icon">💰</span><span>Payroll</span></a>
            <a class="nav-item active" href="{{ route('settings') }}"><span class="icon">⚙</span><span>Settings</span></a>
        </nav>
        <div class="sidebar-card">
            <p class="eyebrow">System</p>
            <h3>Healthy</h3>
            <span>All services online</span>
        </div>
    </aside>

    <main class="main-panel">
        <header class="topbar">
            <div>
                <p class="eyebrow">Configuration</p>
                <h1>System settings</h1>
            </div>
            <div class="topbar-actions">
                <button class="ghost-btn">Reset</button>
                <button class="primary-btn">Save changes</button>
            </div>
        </header>

        <section class="panel-card">
            <div class="login-card" style="margin: 0; max-width: 700px; width: 100%;">
                <div class="login-header">
                    <h1>Company profile</h1>
                    <p class="subtitle">Update core record settings for the HR system.</p>
                </div>
                <div class="login-form">
                    <div class="field">
                        <label>Company name</label>
                        <input value="Northstar Holdings" />
                    </div>
                    <div class="field">
                        <label>Timezone</label>
                        <input value="Asia/Riyadh" />
                    </div>
                    <div class="field">
                        <label>Work week</label>
                        <input value="Sun - Thu" />
                    </div>
                    <div class="field">
                        <label>Annual leave days</label>
                        <input value="22" />
                    </div>
                    <button class="submit-btn">Apply settings</button>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection
