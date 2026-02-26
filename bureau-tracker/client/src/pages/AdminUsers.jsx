import { useState, useEffect } from 'react';
import api from '../api/client';
import { Users, Plus, X, Edit2, Trash2 } from 'lucide-react';

export default function AdminUsers() {
  const [users, setUsers] = useState([]);
  const [divisions, setDivisions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState(null);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  const emptyForm = {
    name: '', email: '', password: '', role: 'division_director',
    division_id: '', position: '', is_active: true,
  };
  const [form, setForm] = useState(emptyForm);

  useEffect(() => {
    fetchUsers();
    fetchDivisions();
  }, []);

  async function fetchUsers() {
    try {
      const data = await api.get('/users');
      setUsers(data.users || []);
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

  function startEdit(user) {
    setEditing(user.id);
    setForm({
      name: user.name,
      email: user.email,
      password: '',
      role: user.role,
      division_id: user.division_id || '',
      position: user.position || '',
      is_active: user.is_active,
    });
    setShowForm(true);
    setError('');
  }

  function cancelForm() {
    setShowForm(false);
    setEditing(null);
    setForm(emptyForm);
    setError('');
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setError('');
    setSubmitting(true);

    try {
      const payload = { ...form };
      if (!payload.password) delete payload.password;
      if (!payload.division_id) payload.division_id = null;

      if (editing) {
        await api.put(`/users/${editing}`, payload);
      } else {
        if (!payload.password) {
          setError('Password is required for new users');
          setSubmitting(false);
          return;
        }
        await api.post('/users', payload);
      }

      cancelForm();
      fetchUsers();
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  }

  async function deactivateUser(id) {
    if (!confirm('Are you sure you want to deactivate this user?')) return;
    try {
      await api.delete(`/users/${id}`);
      fetchUsers();
    } catch (err) {
      alert(err.message);
    }
  }

  const roleLabels = {
    division_director: 'Division Director',
    bureau_head: 'Bureau Head',
    minister: 'Minister',
    admin: 'Admin',
  };

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
          <h1 className="text-2xl font-bold text-gray-900">User Management</h1>
          <p className="text-gray-500 mt-1">Create, edit, and manage system users</p>
        </div>
        <button
          onClick={() => { setShowForm(true); setEditing(null); setForm(emptyForm); }}
          className="btn-primary flex items-center gap-2"
        >
          <Plus size={18} />
          Add User
        </button>
      </div>

      {/* Form */}
      {showForm && (
        <div className="card p-6">
          <h2 className="text-lg font-semibold text-gray-800 mb-4">
            {editing ? 'Edit User' : 'Create User'}
          </h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">{error}</div>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="label">Full Name *</label>
                <input
                  type="text"
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                  className="input-field"
                  required
                />
              </div>
              <div>
                <label className="label">Email *</label>
                <input
                  type="email"
                  value={form.email}
                  onChange={(e) => setForm({ ...form, email: e.target.value })}
                  className="input-field"
                  required
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="label">
                  Password {editing ? '(leave blank to keep)' : '*'}
                </label>
                <input
                  type="password"
                  value={form.password}
                  onChange={(e) => setForm({ ...form, password: e.target.value })}
                  className="input-field"
                  minLength={8}
                  {...(!editing && { required: true })}
                />
              </div>
              <div>
                <label className="label">Role *</label>
                <select
                  value={form.role}
                  onChange={(e) => setForm({ ...form, role: e.target.value })}
                  className="input-field"
                  required
                >
                  <option value="division_director">Division Director</option>
                  <option value="bureau_head">Bureau Head</option>
                  <option value="minister">Minister</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="label">Division</label>
                <select
                  value={form.division_id}
                  onChange={(e) => setForm({ ...form, division_id: e.target.value })}
                  className="input-field"
                >
                  <option value="">— No Division —</option>
                  {divisions.map((d) => (
                    <option key={d.id} value={d.id}>{d.name}</option>
                  ))}
                </select>
              </div>
              <div>
                <label className="label">Position</label>
                <input
                  type="text"
                  value={form.position}
                  onChange={(e) => setForm({ ...form, position: e.target.value })}
                  className="input-field"
                />
              </div>
            </div>

            {editing && (
              <label className="flex items-center gap-2 text-sm">
                <input
                  type="checkbox"
                  checked={form.is_active}
                  onChange={(e) => setForm({ ...form, is_active: e.target.checked })}
                  className="rounded border-gray-300 text-primary-600"
                />
                Active
              </label>
            )}

            <div className="flex justify-end gap-3">
              <button type="button" onClick={cancelForm} className="btn-secondary">Cancel</button>
              <button type="submit" disabled={submitting} className="btn-primary">
                {submitting ? 'Saving...' : editing ? 'Update User' : 'Create User'}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Users Table */}
      <div className="card">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="font-semibold text-gray-800 flex items-center gap-2">
            <Users size={18} />
            Users ({users.length})
          </h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Name</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Email</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Role</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Division</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                <th className="text-left px-5 py-3 font-medium text-gray-600">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {users.map((u) => (
                <tr key={u.id} className="hover:bg-gray-50">
                  <td className="px-5 py-3 font-medium text-gray-900">{u.name}</td>
                  <td className="px-5 py-3 text-gray-600">{u.email}</td>
                  <td className="px-5 py-3">
                    <span className="badge-blue">{roleLabels[u.role]}</span>
                  </td>
                  <td className="px-5 py-3 text-gray-600">{u.division_name || '—'}</td>
                  <td className="px-5 py-3">
                    {u.is_active
                      ? <span className="badge-green">Active</span>
                      : <span className="badge-red">Inactive</span>
                    }
                  </td>
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => startEdit(u)}
                        className="p-1.5 rounded hover:bg-gray-100 text-gray-500"
                        title="Edit"
                      >
                        <Edit2 size={15} />
                      </button>
                      {u.is_active && (
                        <button
                          onClick={() => deactivateUser(u.id)}
                          className="p-1.5 rounded hover:bg-red-50 text-gray-500 hover:text-red-600"
                          title="Deactivate"
                        >
                          <Trash2 size={15} />
                        </button>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
