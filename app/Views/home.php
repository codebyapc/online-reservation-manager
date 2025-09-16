<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="text-center mb-5">
            <h1 class="display-4">Online Reservation Manager</h1>
            <p class="lead">Manage reservations for your small business efficiently</p>
        </div>
    </div>
</div>

<?php if (!session()->get('is_logged_in')): ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Get Started</h5>
                <p class="card-text">Register for an account or login to manage your reservations.</p>
                <a href="/auth/register" class="btn btn-primary me-2">Register</a>
                <a href="/auth/login" class="btn btn-outline-primary">Login</a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <?php if (session()->get('user_role') === 'admin'): ?>
                    <a href="/admin/dashboard" class="btn btn-primary mb-2">Admin Dashboard</a><br>
                    <a href="/admin/reservations" class="btn btn-secondary mb-2">Manage Reservations</a><br>
                    <a href="/admin/businesses" class="btn btn-secondary">Manage Businesses</a>
                <?php else: ?>
                    <a href="/reservations" class="btn btn-primary mb-2">My Reservations</a><br>
                    <a href="/reservations/create" class="btn btn-secondary mb-2">New Reservation</a><br>
                    <a href="/reservations/calendar" class="btn btn-secondary">View Calendar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Welcome, <?= session()->get('user_name') ?>!</h5>
            </div>
            <div class="card-body">
                <p>You are logged in as a <?= session()->get('user_role') ?>.</p>
                <a href="/auth/logout" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>