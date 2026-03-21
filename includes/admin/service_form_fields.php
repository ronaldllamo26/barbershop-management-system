<?php $cats = $conn->query("SELECT * FROM service_categories ORDER BY sort_order"); ?>
<div class="row g-3">
  <div class="col-12">
    <label class="admin-label">Category *</label>
    <select name="category_id" class="admin-input" required>
      <?php while ($c = $cats->fetch_assoc()): ?>
      <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-12">
    <label class="admin-label">Service Name *</label>
    <input type="text" name="name" class="admin-input" placeholder="e.g. Regular Haircut" required>
  </div>
  <div class="col-12">
    <label class="admin-label">Description</label>
    <textarea name="description" class="admin-input" rows="2" placeholder="Short description..."></textarea>
  </div>
  <div class="col-md-6">
    <label class="admin-label">Price (₱) *</label>
    <input type="number" name="price" class="admin-input" step="0.01" min="0" placeholder="350.00" required>
  </div>
  <div class="col-md-6">
    <label class="admin-label">Duration (mins) *</label>
    <input type="number" name="duration_mins" class="admin-input" min="5" placeholder="30" required>
  </div>
  <div class="col-md-6">
    <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
      <input type="checkbox" name="is_featured" value="1" style="width:16px;height:16px;">
      Featured Service
    </label>
  </div>
  <div class="col-md-6">
    <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
      <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;">
      Active (visible on site)
    </label>
  </div>
</div>