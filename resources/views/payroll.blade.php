@extends('layouts.app')

@section('title', 'Payroll - HR Management')

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
            <a class="nav-item active" href="{{ route('payroll') }}"><span class="icon">💰</span><span>Payroll</span></a>
            <a class="nav-item" href="{{ route('settings') }}"><span class="icon">⚙</span><span>Settings</span></a>
        </nav>
        <div class="sidebar-card">
            <p class="eyebrow">Cycle</p>
            <h3>$184.5K</h3>
            <span>Monthly payroll</span>
        </div>
    </aside>

    <main class="main-panel">
        <header class="topbar">
            <div>
                <p class="eyebrow">Finance</p>
                <h1>Payroll</h1>
            </div>
            <div class="topbar-actions">
                <button class="ghost-btn">Export</button>
                <button class="primary-btn">+ Run payroll</button>
            </div>
        </header>

        <section class="stats-grid">
            <article class="stat-card accent-blue">
                <div class="stat-head"><span class="stat-label">Gross pay</span><span class="trend up">+6.8%</span></div>
                <h3>$162.4K</h3>
                <div class="stat-foot"><span>Net pay</span><strong>$147.6K</strong></div>
            </article>
            <article class="stat-card accent-purple">
                <div class="stat-head"><span class="stat-label">Deductions</span><span class="trend down">-2.3%</span></div>
                <h3>$14.8K</h3>
                <div class="stat-foot"><span>Taxes</span><strong>$8.1K</strong></div>
            </article>
            <article class="stat-card accent-green">
                <div class="stat-head"><span class="stat-label">Bonuses</span><span class="trend up">+9.4%</span></div>
                <h3>$11.2K</h3>
                <div class="stat-foot"><span>Approved</span><strong>96%</strong></div>
            </article>
            <article class="stat-card accent-orange">
                <div class="stat-head"><span class="stat-label">Pending</span><span class="trend down">-3.1%</span></div>
                <h3>14</h3>
                <div class="stat-foot"><span>Awaiting review</span><strong>4</strong></div>
            </article>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div><p class="eyebrow">Finance</p><h3>Payroll overview</h3></div>
                <button class="chip-btn">August</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Salary</th>
                        <th>Bonus</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-one">A</span><span>Amal Rami</span></div></td>
                        <td>$8,500</td>
                        <td>$480</td>
                        <td><span class="badge success">Paid</span></td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-two">K</span><span>Khalid Noor</span></div></td>
                        <td>$7,250</td>
                        <td>$320</td>
                        <td><span class="badge warning">Pending</span></td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-three">S</span><span>Salma Omar</span></div></td>
                        <td>$6,900</td>
                        <td>$410</td>
                        <td><span class="badge success">Paid</span></td>
                    </tr>
                    <tr>
                        <td><div class="user-cell"><span class="avatar avatar-four">Y</span><span>Yousef Ali</span></div></td>
                        <td>$9,200</td>
                        <td>$560</td>
                        <td><span class="badge danger">Review</span></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</div>
@endsection
