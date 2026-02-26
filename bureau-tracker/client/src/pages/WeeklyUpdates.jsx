import { useState, useEffect } from 'react';
import api from '../api/client';
import { useAuth } from '../context/AuthContext';
import { StatusBadge, formatDate } from '../utils/helpers';
import { FileText, Plus, X, ChevronDown } from 'lucide-react';
import Select from 'react-select';

export default function WeeklyUpdates() {
  const { user } = useAuth();
  const [updates, setUpdates] = useState([]);
  const [activities, setActivities] = useState([]);
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  const canSubmit = user?.role === 'division_director' || user?.role === 'admin';

  const [form, setForm] = useState({
    week_start: '',
    week_end: '',
    activity_description: '',
    responsible_persons: [],
    status: 'not_started',
    deadline: '',
    challenges: '',
    comments: '',
    activity_id: '',
  });

  useEffect(() => {
    fetchUpdates();
    if (canSubmit) {
      fetchActivities();
      fetchUsers();
    }
  }, []);

  async function fetchUpdates() {
    try {
      const data = await api.get('/weekly-updates');
      setUpdates(data.weeklyUpdates || []);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }

  async function fetchActivities() {
    try {
      const data = await api.get('/activities');
      setActivities(data.activities || []);
    } catch {
      // silent
    }
  }

  async function fetchUsers() {
    try {
      const data = await api.get('/users');
      setUsers(data.users || []);
    } catch {
      // silent
    }
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError('');
    setSubmitting(true);

    try {
      await api.post('/weekly-updates', {
        ...form,
        responsible_persons: form.responsible_persons.map((p) => p.value),
        activity_id: form.activity_id || undefined,
      });
      setShowForm(false);
      setForm({
        week_start: '', week_end: '', activity_description: '',
        responsible_persons: [], status: 'not_started',
        deadline: '', challenges: '', comments: '', activity_id: '',
      });
      fetchUpdates();
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  }

  const personOptions = users.map((u) => ({ value: u.name, label: `${u.name} (${u.position || u.role})` }));
  const activityOptions = activities.map((a) => ({ value: a.id, label: a.title }));

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Weekly Updates</h1>
          <p className="text-gray-500 mt-1">Submit and view weekly activity updates</p>
        </div>
        {canSubmit && (
          <button
            onClick={() => setShowForm(!showForm)}
            className="btn-primary flex items-center gap-2"
          >
            {showForm ? <X size={18} /> : <Plus size={18} />}
            {showForm ? 'Cancel' : 'New Update'}
          </button>
        )}
      </div>

      {/* Form */}
      {showForm && (
        <div className="card p-6">
          <h2 className="text-lg font-semibold text-gray-800 mb-4">Submit Weekly Update</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">{error}</div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="label">Reporting Week Start *</label>
                <input
                  type="date"
                  value={form.week_start}
                  onChange={(e) => setForm({ ...form, week_start: e.target.value })}
                  className="input-field"
                  required
                />
              </div>
              <div>
                <label className="label">Reporting Week End *</label>
                <input
                  type="date"
                  value={form.week_end}
                  onChange={(e) => setForm({ ...form, week_end: e.target.value })}
                  className="input-field"
                  required
                />
              </div>
            </div>

            <div>
              <label className="label">Link to Existing Activity (Optional)</label>
              <select
                value={form.activity_id}
                onChange={(e) => setForm({ ...form, activity_id: e.target.value })}
                className="input-field"
              >
                <option value="">— None (standalone update) —</option>
                {activityOptions.map((a) => (
                  <option key={a.value} value={a.value}>{a.label}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="label">Activity Description *</label>
              <textarea
                value={form.activity_description}
                onChange={(e) => setForm({ ...form, activity_description: e.target.value })}
                className="input-field"
                rows={3}
                required
              />
            </div>

            <div>
              <label className="label">Responsible Persons *</label>
              <Select
                isMulti
                options={personOptions}
                value={form.responsible_persons}
                onChange={(val) => setForm({ ...form, responsible_persons: val || [] })}
                className="text-sm"
                placeholder="Select responsible persons..."
              />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="label">Status *</label>
                <select
                  value={form.status}
                  onChange={(e) => setForm({ ...form, status: e.target.value })}
                  className="input-field"
                  required
                >
                  <option value="not_started">Not Started</option>
                  <option value="ongoing">Ongoing</option>
                  <option value="completed">Completed</option>
                  <option value="not_applicable">Not Applicable</option>
                </select>
              </div>
              <div>
                <label className="label">Deadline</label>
                <input
                  type="date"
                  value={form.deadline}
                  onChange={(e) => setForm({ ...form, deadline: e.target.value })}
                  className="input-field"
                />
              </div>
            </div>

            <div>
              <label className="label">Challenges</label>
              <textarea
                value={form.challenges}
                onChange={(e) => setForm({ ...form, challenges: e.target.value })}
                className="input-field"
                rows={2}
              />
            </div>

            <div>
              <label className="label">Comments</label>
              <textarea
                value={form.comments}
                onChange={(e) => setForm({ ...form, comments: e.target.value })}
                className="input-field"
                rows={2}
              />
            </div>

            <div className="flex justify-end gap-3">
              <button type="button" onClick={() => setShowForm(false)} className="btn-secondary">
                Cancel
              </button>
              <button type="submit" disabled={submitting} className="btn-primary">
                {submitting ? 'Submitting...' : 'Submit Update'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Updates List */}
      <div className="card">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="font-semibold text-gray-800 flex items-center gap-2">
            <FileText size={18} />
            Submitted Updates ({updates.length})
          </h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Week</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Division</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Activity</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Submitted By</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Deadline</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {updates.map((wu) => (
                <tr key={wu.id} className="hover:bg-gray-50">
                  <td className="px-5 py-3 text-gray-600 whitespace-nowrap">
                    {formatDate(wu.week_start)} – {formatDate(wu.week_end)}
                  </td>
                  <td className="px-5 py-3 text-gray-600">{wu.division_name}</td>
                  <td className="px-5 py-3 font-medium text-gray-900 max-w-xs truncate">
                    {wu.activity_description}
                  </td>
                  <td className="px-5 py-3 text-gray-600">{wu.submitted_by_name}</td>
                  <td className="px-5 py-3"><StatusBadge status={wu.status} /></td>
                  <td className="px-5 py-3 text-gray-600">{formatDate(wu.deadline)}</td>
                </tr>
              ))}
              {updates.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center py-8 text-gray-400">
                    No weekly updates submitted yet
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
