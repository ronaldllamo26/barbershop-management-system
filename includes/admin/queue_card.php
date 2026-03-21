<?php
$isDone       = $r['status'] === 'done';
$isServing    = $r['status'] === 'in_progress';
$isWaiting    = $r['status'] === 'waiting';
$waited       = round((time() - strtotime($r['created_at'])) / 60);
$serviceTime  = $isServing && $r['started_at'] ? round((time() - strtotime($r['started_at'])) / 60) : 0;
?>
<div class="qc-body">
  <div class="qc-left">
    <div class="qc-avatar <?= $r['status'] ?>">
      <?= strtoupper(substr($r['customer_name'], 0, 1)) ?>
    </div>
    <div class="qc-info">
      <strong class="qc-name"><?= htmlspecialchars($r['customer_name']) ?></strong>
      <?php if ($r['phone']): ?>
      <span class="qc-phone"><?= htmlspecialchars($r['phone']) ?></span>
      <?php endif; ?>
      <span class="qc-service"><?= htmlspecialchars($r['service_name']) ?></span>
      <div class="qc-meta">
        <span><i class="fas fa-user-tie me-1"></i><?= htmlspecialchars($r['bfname'] . ' ' . $r['blname']) ?></span>
        <?php if ($isServing && $serviceTime > 0): ?>
        <span style="color:var(--gold-d);"><i class="fas fa-clock me-1"></i><?= $serviceTime ?> mins in chair</span>
        <?php elseif ($isWaiting): ?>
        <span><i class="fas fa-hourglass me-1"></i>Waited <?= $waited ?> mins · Est. <?= date('h:i A', strtotime($r['estimated_start'])) ?></span>
        <?php elseif ($isDone && $r['completed_at']): ?>
        <span style="color:#15803d;"><i class="fas fa-check me-1"></i>Done at <?= date('h:i A', strtotime($r['completed_at'])) ?></span>
        <?php endif; ?>
        <?php if ($r['notes']): ?>
        <span style="color:var(--gray);font-style:italic;">"<?= htmlspecialchars($r['notes']) ?>"</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="qc-actions">
    <?php if ($isWaiting): ?>
    <form method="POST">
      <input type="hidden" name="action" value="update_status">
      <input type="hidden" name="id" value="<?= $r['id'] ?>">
      <input type="hidden" name="status" value="in_progress">
      <button type="submit" class="btn-sm-action confirm" title="Start Service">
        <i class="fas fa-cut me-1"></i> Start
      </button>
    </form>
    <?php endif; ?>
    <?php if ($isServing): ?>
    <form method="POST">
      <input type="hidden" name="action" value="update_status">
      <input type="hidden" name="id" value="<?= $r['id'] ?>">
      <input type="hidden" name="status" value="done">
      <button type="submit" class="btn-sm-action complete" title="Mark as Done">
        <i class="fas fa-check me-1"></i> Done
      </button>
    </form>
    <?php endif; ?>
    <?php if (!$isDone): ?>
    <form method="POST" onsubmit="return confirm('Remove from queue?')">
      <input type="hidden" name="action" value="remove">
      <input type="hidden" name="id" value="<?= $r['id'] ?>">
      <button type="submit" class="btn-sm-action cancel" title="Remove">
        <i class="fas fa-times"></i>
      </button>
    </form>
    <?php endif; ?>
  </div>
</div>