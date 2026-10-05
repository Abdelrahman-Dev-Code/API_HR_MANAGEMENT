@extends('layouts.app')

@section('title', 'Dashboard - HR Management')

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
            <a class="nav-item active" href="#">
                <span class="icon">⌂</span>
                <span>Dashboard</span>
            </a>
            <a class="nav-item" href="#">
                <span class="icon">👥</span>
                <span>Employees</span>
            </a>
            <a class="nav-item" href="#">
                <span class="icon">🗓</span>
                <span>Attendance</span>
            </a>
            <a class="nav-item" href="#">
                <span class="icon">🏖</span>
                <span>Leaves</span>
            </a>
            <a class="nav-item" href="#">
                <span class="icon">💰</span>
                <span>Payroll</span>
            </a>
            <a class="nav-item" href="#">
                <span class="icon">📚</span>
                <span>Documents</span>
            </a>
            <a class="nav-item" href="#">
                <span class="icon">⚙</span>
                <span>Settings</span>
            </a>
        </nav>

        <div class="sidebar-card">
            <p class="eyebrow">This month</p>
            <h3>84.7%</h3>
            <span>Workforce efficiency</span>
        </div>
    </aside>

    <main class="main-panel">
        <header class="topbar">
            <div>
                <p class="eyebrow">Overview</p>
                <h1>Good morning, Sarah</h1>
            </div>
            <div class="topbar-actions">
                <button class="ghost-btn">Export</button>
                <button class="primary-btn">+ New employee</button>
            </div>
        </header>

        <section class="stats-grid">
            <article class="stat-card accent-blue">
                <div class="stat-head">
                    <span class="stat-label">Total employees</span>
                    <span class="trend up">+12.4%</span>
                </div>
                <h3>1,284</h3>
                <div class="stat-foot">
                    <span>Active</span>
                    <strong>1,078</strong>
                </div>
            </article>

            <article class="stat-card accent-purple">
                <div class="stat-head">
                    <span class="stat-label">Pending approvals</span>
                    <span class="trend down">-3.1%</span>
                </div>
                <h3>42</h3>
                <div class="stat-foot">
                    <span>Need review</span>
                    <strong>18</strong>
                </div>
            </article>

            <article class="stat-card accent-green">
                <div class="stat-head">
                    <span class="stat-label">Attendance rate</span>
                    <span class="trend up">+8.7%</span>
                </div>
                <h3>96.2%</h3>
                <div class="stat-foot">
                    <span>On time</span>
                    <strong>89.4%</strong>
                </div>
            </article>

            <article class="stat-card accent-orange">
                <div class="stat-head">
                    <span class="stat-label">Payroll cycle</span>
                    <span class="trend up">+2.4%</span>
                </div>
                <h3>$184.5K</h3>
                <div class="stat-foot">
                    <span>Processed</span>
                    <strong>102%</strong>
                </div>
            </article>
        </section>

        <section class="content-grid">
            <article class="panel-card large-panel">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Performance</p>
                        <h3>Team productivity</h3>
                    </div>
                    <button class="chip-btn">This year</button>
                </div>

                <div class="chart-area">
                    <div class="chart-bars">
                        <span style="--h:42%"></span>
                        <span style="--h:58%"></span>
                        <span style="--h:49%"></span>
                        <span style="--h:68%"></span>
                        <span style="--h:74%"></span>
                        <span style="--h:88%"></span>
                        <span style="--h:81%"></span>
                        <span style="--h:95%"></span>
                        <span style="--h:78%"></span>
                        <span style="--h:91%"></span>
                        <span style="--h:84%"></span>
                        <span style="--h:97%"></span>
                    </div>
                    <div class="chart-labels">
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                        <span>May</span>
                        <span>Jun</span>
                        <span>Jul</span>
                        <span>Aug</span>
                        <span>Sep</span>
                        <span>Oct</span>
                        <span>Nov</span>
                        <span>Dec</span>
                    </div>
                </div>
            </article>

            <article class="panel-card mini-panel">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Hiring</p>
                        <h3>Recruitment pipeline</h3>
                    </div>
                </div>

                <div class="ring-wrap">
                    <div class="progress-ring">
                        <div class="ring-center">
                            <strong>73%</strong>
                            <span>Filled</span>
                        </div>
                    </div>
                </div>

                <ul class="list-stats">
                    <li><span>Applicants</span><strong>932</strong></li>
                    <li><span>Shortlisted</span><strong>241</strong></li>
                    <li><span>Interviews</span><strong>70</strong></li>
                </ul>
            </article>
        </section>

        <section class="bottom-grid">
            <article class="panel-card table-panel">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Today</p>
                        <h3>Recent attendance</h3>
                    </div>
                    <button class="chip-btn">View all</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Check-in</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar-one">A</span>
                                    <span>Amal Rami</span>
                                </div>
                            </td>
                            <td>Engineering</td>
                            <td>08:42 AM</td>
                            <td><span class="badge success">Present</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar-two">K</span>
                                    <span>Khalid Noor</span>
                                </div>
                            </td>
                            <td>Finance</td>
                            <td>09:18 AM</td>
                            <td><span class="badge warning">Late</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar-three">S</span>
                                    <span>Salma Omar</span>
                                </div>
                            </td>
                            <td>HR</td>
                            <td>08:56 AM</td>
                            <td><span class="badge success">Present</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar-four">Y</span>
                                    <span>Yousef Ali</span>
                                </div>
                            </td>
                            <td>Operations</td>
                            <td>--</td>
                            <td><span class="badge danger">Absent</span></td>
                        </tr>
                    </tbody>
                </table>
            </article>

            <article class="panel-card task-panel">
                <div class="panel-head">
                    <div>
                        <p class="eyebrow">Reminders</p>
                        <h3>Upcoming tasks</h3>
                    </div>
                </div>

                <div class="task-list">
                    <div class="task-item">
                        <span class="task-dot blue"></span>
                        <div>
                            <h4>Payroll review</h4>
                            <p>Due today • 4:00 PM</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <span class="task-dot purple"></span>
                        <div>
                            <h4>New hire onboarding</h4>
                            <p>2 documents pending</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <span class="task-dot green"></span>
                        <div>
                            <h4>Training session</h4>
                            <p>Tomorrow • 10:30 AM</p>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </main>
</div>
@endsection
