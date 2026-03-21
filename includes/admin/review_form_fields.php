<div class="row g-3">
  <div class="col-md-6">
    <label class="admin-label">Customer Name *</label>
    <input type="text" name="customer_name" class="admin-input" placeholder="Juan P." required>
  </div>
  <div class="col-md-6">
    <label class="admin-label">Location</label>
    <input type="text" name="location" class="admin-input" placeholder="Cubao, QC">
  </div>
  <div class="col-12">
    <label class="admin-label">Review *</label>
    <textarea name="review_text" class="admin-input" rows="3"
              placeholder="Write the customer's review here..." required></textarea>
  </div>
  <div class="col-12">
    <label class="admin-label">Rating *</label>
    <div class="star-rating-input">
      <?php for ($i = 5; $i >= 1; $i--): ?>
      <button type="button" class="star-btn" data-val="<?= $i ?>"
              onclick="setRating(this, <?= $i ?>)"
              style="background:none;border:none;cursor:pointer;font-size:1.4rem;color:#ccc;padding:2px;">
        ★
      </button>
      <?php endfor; ?>
      <input type="hidden" name="rating" value="5">
    </div>
  </div>
  <div class="col-md-6">
    <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
      <input type="checkbox" name="is_featured" value="1" checked style="width:16px;height:16px;">
      Featured (show on homepage)
    </label>
  </div>
  <div class="col-md-6">
    <label class="admin-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
      <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;">
      Active (visible on site)
    </label>
  </div>
</div>
<script>
function setRating(btn, val) {
  const container = btn.closest('.star-rating-input');
  container.querySelector('[name="rating"]').value = val;
  const stars = container.querySelectorAll('.star-btn');
  // Stars are rendered 5 to 1, so reverse logic
  stars.forEach(s => {
    s.style.color = parseInt(s.dataset.val) <= val ? '#C9A84C' : '#ccc';
  });
}
// Init stars on load - default 5
document.querySelectorAll('.star-rating-input').forEach(container => {
  const val = parseInt(container.querySelector('[name="rating"]').value) || 5;
  container.querySelectorAll('.star-btn').forEach(s => {
    s.style.color = parseInt(s.dataset.val) <= val ? '#C9A84C' : '#ccc';
  });
});
</script>