<div class="card-soft p-4">
  <h4 class="mb-3">👑 Quản lý VIP</h4>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>VIP</th>
          <th>Cập nhật</th>
          <th class="text-end">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($vipUsers ?? []) as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="badge text-bg-warning">VIP</span></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td class="text-end">
              <button class="btn btn-danger btn-sm"
                onclick="removeVip(<?= (int)$u['id'] ?>)">
                ❌ Gỡ VIP
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
async function post(url, data){
  const fd = new FormData();
  Object.entries(data).forEach(([k,v]) => fd.append(k,v));
  const res = await fetch(url, { method:'POST', body:fd });
  return res.json().catch(()=>({ok:false}));
}

async function removeVip(id){
  if (!confirm("Gỡ VIP người dùng ID " + id + " ?")) return;

  const j = await post('/admin/vip/remove', {id});
  if (j.ok) location.reload();
  else alert(j.message || 'Lỗi gỡ VIP');
}
</script>
