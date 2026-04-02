<div class="card-soft p-4">
  <h4 class="mb-3">💳 Thanh toán</h4>

  <?php if (empty($payments)): ?>
    <div class="alert alert-warning">
      Chưa có dữ liệu thanh toán (hoặc bạn chưa tạo bảng <code>payments</code>).
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Số tiền</th>
            <th>Nội dung</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($payments as $p): ?>
            <tr>
              <td><?= (int)$p['id'] ?></td>
              <td><?= (int)$p['user_id'] ?></td>
              <td><?= number_format((int)$p['amount']) ?>đ</td>
              <td><?= htmlspecialchars($p['description'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['status'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['created_at'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
