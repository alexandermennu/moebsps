import { useState, useEffect } from 'react';
import api from '../api/client';
import { useAuth } from '../context/AuthContext';
import { formatDate } from '../utils/helpers';
import { CalendarPlus, Plus, X } from 'lucide-react';
import Select from 'react-select';

export default function WeeklyPlans() {
  const { user } = useAuth();
  const [plans, setPlans] = useState([]);
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  const canSubmit = user?.role === 'division_director' || user?.role === 'admin';

  const [form, setForm] = useState({
    week_start: '',
    week_end: '',
    planned_activity: '',
    responsible_persons: [],
    start_date: '',
    expected_completion_date: '',
    notes: '',
  });

  useEffect(() => {
    fetchPlans();
    if (canSubmit) fetchUsers();
  }, []);

  async function fetchPlans() {
    try {
      const data = await api.get('/weekly-plans');
      setPlans(data.weeklyPlans || []);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
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
      await api.post('/weekly-plans', {
        ...form,
        responsible_persons: form.responsible_persons.map((p) => p.value),
      });
      setShowForm(false);
      setForm({
        week_start: '', week_end: '', planned_activity: '',
        responsible_persons: [], start_date: '', expected_completion_date: '', notes: '',
      });
      fetchPlans();
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  }

  const personOptions = users.map((u) => ({
    value: u.name,
    label: `${u.name} (${u.position || u.role})`,
  }));

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
          <h1 className="text-2xl font-bold text-gray-900">Weekly Plans</h1>
          <p className="text-gray-500 mt-1">
            Plan weekly activities. Each plan auto-creates a trackable activity.
          </p>
        </div>
        {canSubmit && (
          <button
            onClick={() => setShowForm(!showForm)}
            className="btn-primary flex items-center gap-2"
          >
            {showForm ? <X size={18} /> : <Plus size={18} />}
            {showForm ? 'Cancel' : 'New Plan'}
          </button>
        )}
      </div>

      {/* Form */}
      {showForm && (
        <div className="card p-6">
          <h2 className="text-lg font-semibold text-gray-800 mb-4">Create Weekly Plan</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">{error}</div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="label">Week Start *</label>
                <input
                  type="date"
                  value={form.week_start}
                  onChange={(e) => setForm({ ...form, week_start: e.target.value })}
                  className="input-field"
                  required
                />
              </div>
              <div>
                <label className="label">Week End *</label>
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
              <label className="label">Planned Activity *</label>
              <textarea
                value={form.planned_activity}
                onChange={(e) => setForm({ ...form, planned_activity: e.target.value })}
                className="input-field"
                rows={3}
                placeholder="Describe the planned activity..."
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
                <label className="label">Start Date</label>
                <input
                  type="date"
                  value={form.start_date}
                  onChange={(e) => setForm({ ...form, start_date: e.target.value })}
                  className="input-field"
                />
              </div>
              <div>
                <label className="label">Expected Completion Date</label>
                <input
                  type="date"
                  value={form.expected_completion_date}
                  onChange={(e) => setForm({ ...form, expected_completion_date: e.target.value })}
                  className="input-field"
                />
              </div>
            </div>

            <div>
              <label className="label">Notes</label>
              <textarea
                value={form.notes}
                onChange={(e) => setForm({ ...form, notes: e.target.value })}
                className="input-field"
                rows={2}
              />
            </div>

            <div className="flex justify-end gap-3">
              <button type="button" onClick={() => setShowForm(false)} className="btn-secondary">
                Cancel
              </button>
              <button type="submit" disabled={submitting} className="btn-primary">
                {submitting ? 'Creating...' : 'Create Plan & Activity'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Plans List */}
      <div className="card">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="font-semibold text-gray-800 flex items-center gap-2">
            <CalendarPlus size={18} />
            Weekly Plans ({plans.length})
          </h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Week</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Division</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Planned Activity</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Responsible</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Submitted By</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Expected Completion</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {plans.map((wp) => (
                <tr key={wp.id} className="hover:bg-gray-50">
                  <td className="px-5 py-3 text-gray-600 whitespace-nowrap">
                    {formatDate(wp.week_start)} – {formatDate(wp.week_end)}
                  </td>
                  <td className="px-5 py-3 text-gray-600">{wp.division_name}</td>
                  <td className="px-5 py-3 font-medium text-gray-900 max-w-xs truncate">
                    {wp.planned_activity}
                  </td>
                  <td className="px-5 py-3 text-gray-600">
                    {(wp.responsible_persons || []).join(', ') || '—'}
                  </td>
                  <td className="px-5 py-3 text-gray-600">{wp.submitted_by_name}</td>
                  <td className="px-5 py-3 text-gray-600">
                    {formatDate(wp.expected_completion_date)}
                  </td>
                </tr>
              ))}
              {plans.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center py-8 text-gray-400">
                    No weekly plans created yet
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
