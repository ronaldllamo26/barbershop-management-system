</div><!-- end admin-content -->
</div><!-- end admin-main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('adminSidebar').classList.toggle('collapsed');
  document.getElementById('adminMain').classList.toggle('expanded');
}
// Auto-collapse on mobile
if (window.innerWidth < 992) {
  document.getElementById('adminSidebar').classList.add('collapsed');
  document.getElementById('adminMain').classList.add('expanded');
}
</script>
</body>
</html>