@extends('layouts.app')

@section('title', 'Attendance - HR Management')

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
            <a class="nav-item active" href="{{ route('attendance') }}"><span class="icon">🗓</span><span>Attendance</span></a>
            <a class="nav-item" href="{{ route('payroll') }}"><span class="icon">💰</span><span>Payroll</span></a>
            <a class="nav-item" href="{{ route('settings') }}"><span class="icon">⚙</span><span>Settings</span></a>
        </nav>
        <div class="sidebar-card">
            <p class="eyebrow">Summary</p>
            <h3>96.2%</h3>
            <span>On time attendance</span>
        </div>
    </aside>

    <main class="main-panel">
        <header class="topbar">
            <div>
                <p class="eyebrow">Operations</p>
                <h1>Attendance</h1>
            </div>
            <div class="topbar-actions">
                <button class="ghost-btn">Export</button>
                <button class="primary-btn">+ Mark attendance</button>
            </div>
        </header>

        <section class="stats-grid">
            <article class="stat-card accent-blue">
                <div class="stat-head"><span class="stat-label">Today present</span><span class="trend up">+4.5%</span></div>
                <h3>1,120</h3>
                <div class="stat-foot"><span>of 1,230</span><strong>91%</strong></div>
            </article>
            <article class="stat-card accent-purple">
                <div class="stat-head"><span class="stat-label">Late arrivals</span><span class="trend down">-1.2%</span></div>
                <h3>36</h3>
                <div class="stat-foot"><span>Average delay</span><strong>11 min</strong></div>
            </article>
            <article class="stat-card accent-green">
                <div class="stat-head"><span class="stat-label">Absences</span><span class="trend up">+0.7%</span></div>
                <h3>18</h3>
                <div class="stat-foot"><span>Approved</span><strong>9</strong></div>
            </article>
            <article class="stat-card accent-orange">
                <div class="stat-head"><span class="stat-label">Overtime</span><span class="trend up">+7.1%</span></div>
                <h3>184h</h3>
                <div class="stat-foot"><span>This week</span><strong>86.4%</strong></div>
            </article>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div><p class="eyebrow">Live</p><h3>Daily attendance log</h3></div>
                <button class="chip-btn">Today</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Shift</th>
                        <th>Check in</th>
                        <th>Check out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-one">A</span><span>Amal Rami</span></div></td>
                        <td>Day</td>
                        <td>08:42</td>
                        <td>05:18</td>
                        <td><span class="badge success">Present</span></td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-two">K</span><span>Khalid Noor</span></div></td>
                        <td>Night</td>
                        <td>09:18</td>
                        <td>06:20</td>
                        <td><span class="badge warning">Late</span></td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-three">S</span><span>Salma Omar</span></div></td>
                        <td>Day</td>
                        <td>08:56</td>
                        <td>05:25</td>
                        <td><span class="badge success">Present</span></td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-four">Y</span><span>Yousef Ali</span></div></td>
                        <td>Day</td>
                        <td>--</td>
                        <td>--</td>
                        <td><span class="badge danger">Absent</span></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</div>
@endsection
