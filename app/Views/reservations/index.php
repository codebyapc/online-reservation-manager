<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Reservations</h2>
            <a href="/reservations/create" class="btn btn-primary">New Reservation</a>
        </div>

        <?php if (empty($reservations)): ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="mb-0">You have no reservations yet.</p>
                    <a href="/reservations/create" class="btn btn-primary mt-3">Create Your First Reservation</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Business</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            // Get business name - in real app, you'd join or cache this
                                            $businessModel = new \App\Models\BusinessModel();
                                            $business = $businessModel->find($reservation['business_id']);
                                            echo $business ? $business['name'] : 'Unknown';
                                            ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></td>
                                        <td><?= date('H:i', strtotime($reservation['reservation_time'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $reservation['status'] === 'approved' ? 'success' : ($reservation['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($reservation['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/reservations/edit/<?= $reservation['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="/reservations/delete/<?= $reservation['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Cancel</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>