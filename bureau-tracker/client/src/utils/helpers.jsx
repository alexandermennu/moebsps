export const STATUS_LABELS = {
  not_started: 'Not Started',
  ongoing: 'Ongoing',
  completed: 'Completed',
  not_applicable: 'N/A',
};

export const STATUS_COLORS = {
  not_started: 'badge-gray',
  ongoing: 'badge-yellow',
  completed: 'badge-green',
  not_applicable: 'badge-blue',
};

export function StatusBadge({ status }) {
  return (
    <span className={STATUS_COLORS[status] || 'badge-gray'}>
      {STATUS_LABELS[status] || status}
    </span>
  );
}

export function formatDate(dateStr) {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

export function daysUntil(dateStr) {
  if (!dateStr) return null;
  const diff = new Date(dateStr) - new Date();
  return Math.ceil(diff / (1000 * 60 * 60 * 24));
}
