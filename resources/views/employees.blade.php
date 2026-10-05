@extends('layouts.app')

@section('title', 'Employees - HR Management')

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
            <a class="nav-item active" href="{{ route('employees') }}"><span class="icon">👥</span><span>Employees</span></a>
            <a class="nav-item" href="{{ route('attendance') }}"><span class="icon">🗓</span><span>Attendance</span></a>
            <a class="nav-item" href="{{ route('payroll') }}"><span class="icon">💰</span><span>Payroll</span></a>
            <a class="nav-item" href="{{ route('settings') }}"><span class="icon">⚙</span><span>Settings</span></a>
        </nav>

        <div class="sidebar-card">
            <p class="eyebrow">This month</p>
            <h3>93.1%</h3>
            <span>Employee satisfaction</span>
        </div>
    </aside>

    <main class="main-panel">
        <header class="topbar">
            <div>
                <p class="eyebrow">Directory</p>
                <h1>Employees</h1>
            </div>
            <div class="topbar-actions">
                <button class="ghost-btn">Filter</button>
                <button class="primary-btn">+ Invite employee</button>
            </div>
        </header>

        <section class="panel-card">
            <div class="table-toolbar">
                <strong>Employee directory</strong>
                <input class="search-box" value="Search employees" aria-label="Search employees" />
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-one">A</span><span>Amal Rami</span></div></td>
                        <td>Engineering</td>
                        <td>Senior Engineer</td>
                        <td><span class="badge success">Active</span></td>
                        <td>Riyadh</td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-two">K</span><span>Khalid Noor</span></div></td>
                        <td>Finance</td>
                        <td>Account Manager</td>
                        <td><span class="badge warning">On leave</span></td>
                        <td>Jeddah</td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-three">S</span><span>Salma Omar</span></div></td>
                        <td>HR</td>
                        <td>HR Specialist</td>
                        <td><span class="badge success">Active</span></td>
                        <td>Dubai</td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-four">Y</span><span>Yousef Ali</span></div></td>
                        <td>Operations</td>
                        <td>Operations Lead</td>
                        <td><span class="badge danger">Inactive</span></td>
                        <td>Doha</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</div>
@endsection
