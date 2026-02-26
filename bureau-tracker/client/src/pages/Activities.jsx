import { useState, useEffect } from 'react';
import api from '../api/client';
import { useAuth } from '../context/AuthContext';
import { StatusBadge, formatDate, daysUntil } from '../utils/helpers';
import { Activity, Filter, MessageSquare, Send } from 'lucide-react';

export default function Activities() {
  const { user } = useAuth();
  const [activities, setActivities] = useState([]);
  const [divisions, setDivisions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedActivity, setSelectedActivity] = useState(null);
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState('');
  const [commentLoading, setCommentLoading] = useState(false);
  const [statusUpdating, setStatusUpdating] = useState(false);

  // Filters
  const [filterDivision, setFilterDivision] = useState('');
  const [filterStatus, setFilterStatus] = useState('');
  const [filterOverdue, setFilterOverdue] = useState(false);
  const [filterRepeated, setFilterRepeated] = useState(false);

  const canComment = ['bureau_head', 'minister', 'admin', 'division_director'].includes(user?.role);
  const canUpdateStatus = user?.role === 'division_director' || user?.role === 'admin';

  useEffect(() => {
    fetchActivities();
    fetchDivisions();
  }, [filterDivision, filterStatus, filterOverdue, filterRepeated]);

  async function fetchActivities() {
    try {
      const params = new URLSearchParams();
      if (filterDivision) params.set('division_id', filterDivision);
      if (filterStatus) params.set('status', filterStatus);
      if (filterOverdue) params.set('is_overdue', 'true');
      if (filterRepeated) params.set('is_repeated', 'true');

      const data = await api.get(`/activities?${params.toString()}`);
      setActivities(data.activities || []);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }

  async function fetchDivisions() {
    try {
      const data = await api.get('/divisions');
      setDivisions(data.divisions || []);
    } catch {
      // silent
    }
  }

  async function openDetail(activity) {
    setSelectedActivity(activity);
    try {
      const data = await api.get(`/activities/${activity.id}/comments`);
      setComments(data.comments || []);
    } catch {
      setComments([]);
    }
  }

  async function submitComment() {
    if (!newComment.trim() || !selectedActivity) return;
    setCommentLoading(true);
    try {
      const data = await api.post(`/activities/${selectedActivity.id}/comments`, {
        comment: newComment,
      });
      setComments((prev) => [...prev, data.comment]);
      setNewComment('');
    } catch (err) {
      console.error(err);
    } finally {
      setCommentLoading(false);
    }
  }

  async function updateStatus(activityId, newStatus) {
    setStatusUpdating(true);
    try {
      const data = await api.put(`/activities/${activityId}/status`, { status: newStatus });
      setActivities((prev) =>
        prev.map((a) => (a.id === activityId ? { ...a, ...data.activity } : a))
      );
      if (selectedActivity?.id === activityId) {
        setSelectedActivity({ ...selectedActivity, ...data.activity });
      }
    } catch (err) {
      console.error(err);
    } finally {
      setStatusUpdating(false);
    }
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Activities</h1>
        <p className="text-gray-500 mt-1">Track and manage all activities</p>
      </div>

      {/* Filters */}
      <div className="card p-4">
        <div className="flex items-center gap-2 mb-3">
          <Filter size={16} className="text-gray-500" />
          <span className="text-sm font-medium text-gray-700">Filters</span>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          {user?.role !== 'division_director' && (
            <select
              value={filterDivision}
              onChange={(e) => setFilterDivision(e.target.value)}
              className="input-field"
            >
              <option value="">All Divisions</option>
              {divisions.map((d) => (
                <option key={d.id} value={d.id}>{d.name}</option>
              ))}
            </select>
          )}
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            className="input-field"
          >
            <option value="">All Statuses</option>
            <option value="not_started">Not Started</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
            <option value="not_applicable">Not Applicable</option>
          </select>
          <label className="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input
              type="checkbox"
              checked={filterOverdue}
              onChange={(e) => setFilterOverdue(e.target.checked)}
              className="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            Overdue only
          </label>
          <label className="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input
              type="checkbox"
              checked={filterRepeated}
              onChange={(e) => setFilterRepeated(e.target.checked)}
              className="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            />
            Repeated only
          </label>
        </div>
      </div>

      <div className="flex gap-6">
        {/* Activities List */}
        <div className={`flex-1 ${selectedActivity ? 'hidden lg:block' : ''}`}>
          <div className="card">
            <div className="px-5 py-4 border-b border-gray-100">
              <h2 className="font-semibold text-gray-800 flex items-center gap-2">
                <Activity size={18} />
                Activities ({activities.length})
              </h2>
            </div>
            <div className="divide-y divide-gray-100">
              {activities.map((act) => {
                const daysLeft = daysUntil(act.deadline);
                return (
                  <div
                    key={act.id}
                    className={`px-5 py-4 hover:bg-gray-50 cursor-pointer transition-colors
                                ${selectedActivity?.id === act.id ? 'bg-primary-50' : ''}`}
                    onClick={() => openDetail(act)}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1 min-w-0">
                        <h3 className="font-medium text-gray-900 truncate">{act.title}</h3>
                        <p className="text-xs text-gray-500 mt-1">
                          {act.division_name} • Deadline: {formatDate(act.deadline)}
                        </p>
                        <div className="flex items-center gap-2 mt-2">
                          <StatusBadge status={act.status} />
                          {act.is_overdue && <span className="badge-red">Overdue</span>}
                          {act.is_repeated && <span className="badge-purple">Repeated</span>}
                        </div>
                      </div>
                      {daysLeft !== null && act.status !== 'completed' && (
                        <span className={`text-xs font-medium ml-2 ${
                          daysLeft < 0 ? 'text-red-600' : daysLeft <= 3 ? 'text-yellow-600' : 'text-gray-500'
                        }`}>
                          {daysLeft < 0 ? `${Math.abs(daysLeft)}d overdue` : `${daysLeft}d left`}
                        </span>
                      )}
                    </div>
                  </div>
                );
              })}
              {activities.length === 0 && (
                <div className="text-center py-12 text-gray-400">
                  No activities found
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Activity Detail Panel */}
        {selectedActivity && (
          <div className="w-full lg:w-96 flex-shrink-0">
            <div className="card sticky top-20">
              <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 className="font-semibold text-gray-800">Activity Detail</h3>
                <button
                  onClick={() => setSelectedActivity(null)}
                  className="text-gray-400 hover:text-gray-600 lg:hidden"
                >
                  ✕
                </button>
              </div>

              <div className="p-5 space-y-4">
                <div>
                  <h4 className="font-semibold text-gray-900">{selectedActivity.title}</h4>
                  {selectedActivity.description && (
                    <p className="text-sm text-gray-600 mt-1">{selectedActivity.description}</p>
                  )}
                </div>

                <div className="grid grid-cols-2 gap-3 text-sm">
                  <div>
                    <span className="text-gray-500">Division</span>
                    <p className="font-medium">{selectedActivity.division_name}</p>
                  </div>
                  <div>
                    <span className="text-gray-500">Deadline</span>
                    <p className="font-medium">{formatDate(selectedActivity.deadline)}</p>
                  </div>
                  <div>
                    <span className="text-gray-500">Status</span>
                    <div className="mt-0.5">
                      <StatusBadge status={selectedActivity.status} />
                    </div>
                  </div>
                  <div>
                    <span className="text-gray-500">Source</span>
                    <p className="font-medium capitalize">{selectedActivity.created_from || '—'}</p>
                  </div>
                </div>

                <div className="text-sm">
                  <span className="text-gray-500">Assigned To</span>
                  <div className="flex flex-wrap gap-1 mt-1">
                    {(selectedActivity.assigned_users || []).map((u, i) => (
                      <span key={i} className="badge-blue">{u}</span>
                    ))}
                    {(!selectedActivity.assigned_users || selectedActivity.assigned_users.length === 0) && (
                      <span className="text-gray-400">No assignments</span>
                    )}
                  </div>
                </div>

                {/* Status Update */}
                {canUpdateStatus && (
                  <div>
                    <label className="label">Update Status</label>
                    <select
                      value={selectedActivity.status}
                      onChange={(e) => updateStatus(selectedActivity.id, e.target.value)}
                      disabled={statusUpdating}
                      className="input-field"
                    >
                      <option value="not_started">Not Started</option>
                      <option value="ongoing">Ongoing</option>
                      <option value="completed">Completed</option>
                      <option value="not_applicable">Not Applicable</option>
                    </select>
                  </div>
                )}

                {/* Comments */}
                {canComment && (
                  <div>
                    <h4 className="text-sm font-medium text-gray-700 flex items-center gap-1 mb-2">
                      <MessageSquare size={14} />
                      Comments ({comments.length})
                    </h4>

                    <div className="space-y-2 max-h-48 overflow-y-auto mb-3">
                      {comments.map((c) => (
                        <div key={c.id} className="bg-gray-50 rounded-lg px-3 py-2">
                          <div className="flex items-center gap-1 mb-1">
                            <span className="text-xs font-semibold text-gray-700">{c.user_name}</span>
                            <span className="text-xs text-gray-400">
                              {new Date(c.created_at).toLocaleDateString()}
                            </span>
                          </div>
                          <p className="text-sm text-gray-600">{c.comment}</p>
                        </div>
                      ))}
                      {comments.length === 0 && (
                        <p className="text-xs text-gray-400 text-center py-2">No comments yet</p>
                      )}
                    </div>

                    <div className="flex gap-2">
                      <input
                        type="text"
                        value={newComment}
                        onChange={(e) => setNewComment(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && submitComment()}
                        placeholder="Add a comment..."
                        className="input-field flex-1"
                      />
                      <button
                        onClick={submitComment}
                        disabled={commentLoading || !newComment.trim()}
                        className="btn-primary px-3"
                      >
                        <Send size={16} />
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
