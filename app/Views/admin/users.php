<?php
$contentView = __FILE__;
?>

<div class="card-soft p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">👥 Quản lý người dùng</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
        + Thêm người dùng
    </button>
  </div>

  <div class="alert alert-info">
    <b>Debug:</b> Tổng số người dùng: <?= count($users ?? []) ?>
  </div>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Vai trò</th>
          <th>VIP</th>
          <th>Ngày tạo</th>
          <th class="text-end">Hành động</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach (($users ?? []) as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
          <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
          <td>
            <?php $isAdmin = (int)($u['is_admin'] ?? 0) === 1; ?>
            <span class="badge <?= $isAdmin ? 'text-bg-primary' : 'text-bg-secondary' ?>">
                <?= $isAdmin ? 'Admin' : 'User' ?>
            </span>

          </td>
          <td>
            <?php $vip = (int)($u['is_vip'] ?? 0); ?>
            <span class="badge <?= $vip ? 'text-bg-warning' : 'text-bg-light' ?>">
              <?= $vip ? 'VIP' : 'Free' ?>
            </span>
          </td>
          <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>

          <td class="text-end">
            <button
                type="button"
                class="btn btn-success btn-sm btn-edit"
                data-id="<?= (int)$u['id'] ?>"
                data-username="<?= htmlspecialchars($u['username'] ?? '', ENT_QUOTES) ?>"
                data-email="<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES) ?>"
                data-admin="<?= (int)($u['is_admin'] ?? 0) ?>"
                data-vip="<?= (int)($u['is_vip'] ?? 0) ?>"
            >✏️</button>
            <button class="btn btn-warning btn-sm"
                onclick="addVip(<?= (int)$u['id'] ?>)">
                👑
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteUser(<?= (int)$u['id'] ?>)">🗑️</button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="modalCreate" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCreate">
        <div class="modal-header">
          <h5 class="modal-title">Thêm người dùng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
          </div>

          <div class="d-flex gap-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_admin" value="1" id="c_is_admin">
              <label class="form-check-label" for="c_is_admin">Admin</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_vip" value="1" id="c_is_vip">
              <label class="form-check-label" for="c_is_vip">VIP</label>
            </div>
          </div>

          <div class="text-danger small mt-2" id="createErr" style="display:none"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Hủy</button>
          <button class="btn btn-primary" type="submit">Tạo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEdit">
        <div class="modal-header">
          <h5 class="modal-title">Sửa người dùng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="e_id">

          <div class="mb-2">
            <label class="form-label">Username</label>
            <input name="username" id="e_username" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input name="email" id="e_email" type="email" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Password mới (để trống nếu không đổi)</label>
            <input name="password" id="e_password" type="password" class="form-control">
          </div>

          <div class="d-flex gap-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_admin" value="1" id="e_is_admin">
              <label class="form-check-label" for="e_is_admin">Admin</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_vip" value="1" id="e_is_vip">
              <label class="form-check-label" for="e_is_vip">VIP</label>
            </div>
          </div>

          <div class="text-danger small mt-2" id="editErr" style="display:none"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Hủy</button>
          <button class="btn btn-primary" type="submit">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
async function post(url, data){
  const fd = new FormData();
  Object.entries(data).forEach(([k,v]) => fd.append(k, v));
  const res = await fetch(url, { method:'POST', body:fd });
  return res.json().catch(()=>({ok:false, message:'JSON lỗi'}));
}

document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('modalEdit');
  if (!modalEl) {
    console.error('Không tìm thấy #modalEdit');
    return;
  }

  // gán click cho tất cả nút edit
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const username = btn.dataset.username;
      const email = btn.dataset.email;
      const is_admin = Number(btn.dataset.admin || 0);
      const is_vip = Number(btn.dataset.vip || 0);

      // đổ data vào form
      document.getElementById('e_id').value = id;
      document.getElementById('e_username').value = username;
      document.getElementById('e_email').value = email;
      document.getElementById('e_password').value = '';
      document.getElementById('e_is_admin').checked = is_admin === 1;
      document.getElementById('e_is_vip').checked = is_vip === 1;

      // mở modal
      if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        alert('Bootstrap Modal chưa load. Kiểm tra bootstrap.bundle.');
        return;
      }
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
  });
});

async function toggleVip(id){
  const j = await post('/admin/users/toggle-vip', {id});
  if (j.ok) location.reload();
  else alert(j.message || 'Lỗi toggle VIP');
}

async function deleteUser(id){
  if (!confirm("Xóa user ID " + id + " ?")) return;
  const j = await post('/admin/users/delete', {id});
  if (j.ok) location.reload();
  else alert(j.message || 'Lỗi xóa');
}

// CREATE
document.getElementById('formCreate')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const err = document.getElementById('createErr');
  err.style.display = 'none';

  const fd = new FormData(e.target);
  // checkbox: nếu không tick, server sẽ coi là 0
  const data = Object.fromEntries(fd.entries());

  const j = await post('/admin/users/create', data);
  if (j.ok) {
    location.reload();
  } else {
    err.textContent = j.message || 'Tạo user thất bại';
    err.style.display = 'block';
  }
});

// EDIT
document.getElementById('formEdit')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const err = document.getElementById('editErr');
  err.style.display = 'none';

  const fd = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());

  const j = await post('/admin/users/edit', data);
  if (j.ok) {
    location.reload();
  } else {
    err.textContent = j.message || 'Lưu thất bại';
    err.style.display = 'block';
  }
});

async function addVip(id){
  const j = await post('/admin/vip/add', {id});
  if (j.ok) location.reload();
  else alert(j.message || 'Lỗi cấp VIP');
}

</script>

