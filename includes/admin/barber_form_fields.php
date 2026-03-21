<div class="row g-3">
  <div class="col-md-6">
    <label class="admin-label">First Name *</label>
    <input type="text" name="first_name" class="admin-input" placeholder="Marco" required>
  </div>
  <div class="col-md-6">
    <label class="admin-label">Last Name *</label>
    <input type="text" name="last_name" class="admin-input" placeholder="Reyes" required>
  </div>
  <div class="col-12">
    <label class="admin-label">Role / Title *</label>
    <input type="text" name="role_title" class="admin-input" placeholder="Head Barber" required>
  </div>
  <div class="col-12">
    <label class="admin-label">Bio</label>
    <textarea name="bio" class="admin-input" rows="2" placeholder="Short bio..."></textarea>
  </div>
  <div class="col-12">
    <label class="admin-label">Photo URL</label>
    <input type="text" name="photo" class="admin-input" placeholder="https://... or /bg-barbershop/assets/images/barbers/barber-1.jpg"
           oninput="updatePreview(this)">
    <img id="photoPreview" src="" alt="" style="margin-top:8px;width:80px;height:80px;object-fit:cover;border-radius:50%;display:none;"
         onerror="this.style.display='none'" onload="this.style.display='block'">
  </div>
  <div class="col-md-6">
    <label class="admin-label">Instagram URL</label>
    <input type="text" name="instagram" class="admin-input" placeholder="https://instagram.com/...">
  </div>
  <div class="col-md-6">
    <label class="admin-label">Facebook URL</label>
    <input type="text" name="facebook" class="admin-input" placeholder="https://facebook.com/...">
  </div>
  <div class="col-md-6">
    <label class="admin-label">Sort Order</label>
    <input type="number" name="sort_order" class="admin-input" value="0" min="0">
  </div>
  <div class="col-md-6">
    <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:24px;">
      <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;">
      Active (visible on site)
    </label>
  </div>
</div>
<script>
function updatePreview(input) {
  const prev = input.closest('form').querySelector('#photoPreview');
  if (prev) { prev.src = input.value; }
}
</script>